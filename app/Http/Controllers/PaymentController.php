<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Currency;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Load payment gateway configs from the original config files
     */
    private function loadPaymentConfig()
    {
        $basePath = base_path('../pvt.jo/config/payment_gate_ways/');
        $config = ['gateways' => [], 'migs' => [], 'paytabs' => [], 'paypal' => []];

        if (!defined('gogies')) define('gogies', true);
        $GOGIES = [];

        // Load enabled gateways
        $configFile = $basePath . 'config.php';
        if (file_exists($configFile)) {
            include $configFile;
            $config['gateways'] = $GOGIES['gate_ways'] ?? [];
        }

        // Load MIGS config
        $GOGIES = [];
        if (file_exists($basePath . 'migs.php')) {
            include $basePath . 'migs.php';
            $config['migs'] = $GOGIES['migs'] ?? [];
        }

        // Load PayTabs config (Prefer env if set)
        $GOGIES = [];
        if (file_exists($basePath . 'paytabs.php')) {
            include $basePath . 'paytabs.php';
            $config['paytabs'] = $GOGIES['paytabs'] ?? [];
        }

        // Override with .env if available
        if (env('PAYTABS_PROFILE_ID')) {
            $config['paytabs']['profile_id'] = env('PAYTABS_PROFILE_ID');
            $config['paytabs']['server_key'] = env('PAYTABS_SERVER_KEY');
            $config['paytabs']['url'] = env('PAYTABS_BASE_URL', 'https://secure-jordan.paytabs.com');
            $config['gateways']['paytabs'] = true;
        }

        // Load PayPal config
        $GOGIES = [];
        if (file_exists($basePath . 'paypal.php')) {
            include $basePath . 'paypal.php';
            $config['paypal'] = $GOGIES['paypal'] ?? [];
        }

        return $config;
    }

    /**
     * Get checkout currency name and rate from global config + DB
     */
    private function getCheckoutCurrency()
    {
        $globalPath = base_path('../pvt.jo/config/global.php');
        $checkoutCurrencyId = 5; // Default to JOD (lang_id=5)

        if (file_exists($globalPath)) {
            $content = file_get_contents($globalPath);
            // Use front_currency (display currency) for payment gateway
            if (preg_match("/\\\$GOGIES\['front_currency'\]='(\d+)'/", $content, $m)) {
                $checkoutCurrencyId = intval($m[1]);
            }
        }

        // FORCE USD (59) in Test Mode for PayTabs (JOD is often not available for test profiles)
        if (env('PAYTABS_TEST_MODE')) {
            $checkoutCurrencyId = 59; // USD
        }

        // Get currency name from DB
        $currency = Currency::where('lang_id', $checkoutCurrencyId)->where('lang', 'en')->first();
        if ($currency) {
            return [
                'name' => strtoupper($currency->name),
                'rate' => floatval($currency->rate),
            ];
        }

        return ['name' => 'JOD', 'rate' => 1.0];
    }

    /**
     * Convert amount to checkout currency
     */
    private function convertToCheckoutCurrency($amount, $rate)
    {
        if ($rate > 0 && $rate != 1.0) {
            return round($amount / $rate, 2);
        }
        return round($amount, 2);
    }

    /**
     * Initiate payment for an invoice
     */
    public function initiatePayment(Request $request, $lang, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Verify ownership
        if (auth()->check() && auth()->id() != $invoice->user_id) {
            $user = auth()->user();
            if (!($user->group == 1 || $user->group == 2)) {
                abort(403);
            }
        }

        // Check invoice is payable
        if (!in_array($invoice->status, ['u', 'pp'])) {
            return redirect('/' . $lang . '/invoice/' . $id . '/')->with('error', 'This invoice cannot be paid.');
        }

        $paymentConfig = $this->loadPaymentConfig();

        // Calculate amount to pay
        $totalAmount = floatval($invoice->total);
        $paidAmount = floatval($invoice->total_paid ?? 0);
        $amountToPay = $totalAmount - $paidAmount;

        if ($amountToPay <= 0) {
            return redirect('/' . $lang . '/invoice/' . $id . '/')->with('error', 'Nothing to pay.');
        }

        // Use the enabled gateway — check config to determine which gateway is active
        $migsEnabled = !empty($paymentConfig['gateways']['migs']) && !empty($paymentConfig['migs']['mid']);
        $paytabsEnabled = !empty($paymentConfig['gateways']['paytabs']) && !empty($paymentConfig['paytabs']['profile_id']);

        if ($paytabsEnabled) {
            return $this->initiatePayTabsPayment($invoice, $amountToPay, $lang, $paymentConfig);
        } elseif ($migsEnabled) {
            return $this->initiateMigsPayment($invoice, $amountToPay, $lang, $paymentConfig);
        }

        // No gateway configured - mark as pending manual payment
        return $this->handleManualPayment($invoice, $amountToPay, $lang);
    }

    /**
     * Initiate MIGS payment
     */
    private function initiateMigsPayment(Invoice $invoice, $amount, $lang, $config)
    {
        $migsConfig = $config['migs'];

        if (empty($migsConfig['mid']) || empty($migsConfig['access_code']) || empty($migsConfig['secret_hash'])) {
            return $this->handleManualPayment($invoice, $amount, $lang);
        }

        // Get checkout currency
        $checkoutCurrency = $this->getCheckoutCurrency();
        $useCurrency = $checkoutCurrency['name'];

        // Add handling fee if configured
        $handlingFee = floatval($migsConfig['handle_fee'] ?? 0);
        if ($handlingFee > 0) {
            $amount = $amount + ($amount * $handlingFee / 100);
        }

        // Convert to checkout currency
        $convertedAmount = $this->convertToCheckoutCurrency($amount, $checkoutCurrency['rate']);

        // MIGS multiplier: JOD uses *1000, others use *100
        $multiplier = (in_array($useCurrency, ['JOD', 'JD'])) ? 1000 : 100;
        $amountInMinorUnit = intval(round($convertedAmount * $multiplier));

        // MIGS URL
        $testMode = ($migsConfig['test_mode'] ?? 0) == 1;
        $migsUrl = 'https://migs.mastercard.com.au/vpcpay';

        $returnUrl = url('/' . $lang . '/payment/migs/return');

        // Build MIGS payment parameters
        $params = [
            'vpc_Version' => '1',
            'vpc_Command' => 'pay',
            'vpc_Merchant' => $migsConfig['mid'],
            'vpc_AccessCode' => $migsConfig['access_code'],
            'vpc_MerchTxnRef' => $invoice->id . '-' . time(),
            'vpc_OrderInfo' => $invoice->id . '-' . time(),
            'vpc_Amount' => $amountInMinorUnit,
            'vpc_Currency' => $useCurrency,
            'vpc_ReturnURL' => $returnUrl,
            'vpc_Locale' => 'en',
        ];

        ksort($params);

        // Generate secure hash (HMAC SHA256)
        $hashInput = '';
        foreach ($params as $key => $value) {
            if (strlen($value) > 0 && substr($key, 0, 4) === 'vpc_') {
                $hashInput .= $key . '=' . $value . '&';
            }
        }
        $hashInput = rtrim($hashInput, '&');
        $secureHash = strtoupper(hash_hmac('sha256', $hashInput, pack('H*', $migsConfig['secret_hash'])));
        $params['vpc_SecureHash'] = $secureHash;
        $params['vpc_SecureHashType'] = 'SHA256';

        session(['payment_invoice_id' => $invoice->id, 'payment_lang' => $lang]);

        $paymentUrl = $migsUrl . '?' . http_build_query($params);
        return redirect()->away($paymentUrl);
    }

    /**
     * Handle MIGS payment return
     */
    public function migsReturn(Request $request, $lang)
    {
        $invoiceId = session('payment_invoice_id');
        if (!$invoiceId) {
            return redirect('/' . $lang . '/')->with('error', 'Payment session expired.');
        }

        $invoice = Invoice::find($invoiceId);
        if (!$invoice) {
            return redirect('/' . $lang . '/')->with('error', 'Invoice not found.');
        }

        $txnResponseCode = $request->input('vpc_TxnResponseCode', '');
        $message = $request->input('vpc_Message', '');

        if ($txnResponseCode === '0') {
            // Payment successful
            $invoice->status = 'p'; // paid
            $invoice->total_paid = $invoice->total;
            $invoice->paid_by = 'migs';
            $invoice->save();

            // Update booking status if exists
            $this->updateBookingStatus($invoice);

            // Common frontend data
            $frontendCtrl = new \App\Http\Controllers\FrontendController();
            $commonData = $frontendCtrl->getCommonData($lang);

            return redirect('/' . $lang . '/invoice/' . $invoice->id . '/')
                ->with('payment_success', 'Your Invoice Has Been Successfully Paid');
        } else {
            return redirect('/' . $lang . '/invoice/' . $invoice->id . '/')
                ->with('payment_error', 'We Are Sorry Your Payment Failed: ' . $message);
        }
    }

    /**
     * Initiate PayTabs payment
     */
    private function initiatePayTabsPayment(Invoice $invoice, $amount, $lang, $config)
    {
        $ptConfig = $config['paytabs'];

        if (empty($ptConfig['profile_id']) || empty($ptConfig['server_key'])) {
            return $this->handleManualPayment($invoice, $amount, $lang);
        }

        // Get checkout currency
        $checkoutCurrency = $this->getCheckoutCurrency();
        $useCurrency = $checkoutCurrency['name'];

        // Add handling fee
        $handlingFee = floatval($ptConfig['handle_fee'] ?? 0);
        if ($handlingFee > 0) {
            $amount = $amount + ($amount * $handlingFee / 100);
        }

        // Convert to checkout currency
        $convertedAmount = $this->convertToCheckoutCurrency($amount, $checkoutCurrency['rate']);

        $customer = \App\Models\User::find($invoice->user_id);
        $callbackUrl = url('/' . $lang . '/payment/paytabs/callback');
        $returnUrl = url('/' . $lang . '/payment/paytabs/return');

        $ptUrl = $ptConfig['url'] ?? 'https://secure-jordan.paytabs.com';

        $postData = [
            'profile_id' => (int)$ptConfig['profile_id'],
            'tran_type' => 'sale',
            'tran_class' => 'ecom',
            'cart_id' => $invoice->id . '-' . time(),
            'cart_description' => 'INV-' . $invoice->id,
            'cart_currency' => $useCurrency,
            'cart_amount' => $convertedAmount,
            'paypage_lang' => 'en',
            'customer_details' => [
                'name' => trim(($customer->first_name ?? 'Guest') . ' ' . ($customer->last_name ?? '')),
                'email' => $customer->email ?? 'guest@guest.com',
                'phone' => $customer->phone ?? '000000000',
                'street1' => substr($customer->address ?? 'Address', 0, 50),
                'city' => substr($customer->city ?? 'Amman', 0, 50),
                'state' => 'Amman',
                'country' => 'JO',
                'zip' => '11111'
            ],
            'callback' => $callbackUrl,
            'return' => $returnUrl,
        ];

        // Make API call to PayTabs
        $ch = curl_init();
        $apiUrl = rtrim($ptUrl, '/') . '/payment/request';
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $ptConfig['server_key'],
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        // Log for debugging
        \Log::info('PayTabs Payment Request', [
            'url' => $apiUrl,
            'post_data' => $postData,
            'http_code' => $httpCode,
            'curl_error' => $curlError,
            'response' => $result,
        ]);

        if ($httpCode == 200 && isset($result['redirect_url'])) {
            session([
                'payment_invoice_id' => $invoice->id,
                'payment_lang' => $lang,
                'payment_tran_ref' => $result['tran_ref'] ?? '',
                'payment_pt_url' => $ptUrl,
                'payment_pt_server_key' => $ptConfig['server_key'],
                'payment_pt_profile_id' => $ptConfig['profile_id'],
            ]);
            return redirect()->away($result['redirect_url']);
        }

        // PayTabs API failed — show error details
        $errorMsg = $result['message'] ?? ($result['code'] ?? 'Unknown error');
        return redirect('/' . $lang . '/invoice/' . $invoice->id . '/')
            ->with('payment_error', 'We Are Sorry Your Payment Failed: ' . $errorMsg);
    }

    /**
     * Handle PayTabs callback (server-to-server)
     */
    public function paytabsCallback(Request $request, $lang)
    {
        $data = $request->all();
        $cartId = $data['cart_id'] ?? '';
        $invoiceId = str_replace('INV-', '', $cartId);
        $invoice = Invoice::find($invoiceId);

        if (!$invoice) return response('OK', 200);

        $respStatus = $data['payment_result']['response_status'] ?? '';

        if ($respStatus === 'A') {
            // Authorized/Paid
            $invoice->status = 'p';
            $invoice->total_paid = $invoice->total;
            $invoice->paid_by = 'paytabs';
            $invoice->save();
            $this->updateBookingStatus($invoice);
        }

        return response('OK', 200);
    }

    /**
     * Handle PayTabs return (redirect back to site)
     */
    public function paytabsReturn(Request $request, $lang)
    {
        $invoiceId = session('payment_invoice_id');
        if (!$invoiceId) {
            return redirect('/' . $lang . '/')->with('error', 'Payment session expired.');
        }

        $invoice = Invoice::find($invoiceId);
        if (!$invoice) {
            return redirect('/' . $lang . '/')->with('error', 'Invoice not found.');
        }

        // Reload invoice to check if callback already updated it
        $invoice->refresh();
        if ($invoice->status === 'p') {
            return redirect('/' . $lang . '/invoice/' . $invoice->id . '/')
                ->with('payment_success', 'Your Invoice Has Been Successfully Paid');
        }

        // Check return data from PayTabs (may come via POST or query params)
        $respStatus = $request->input('payment_result.response_status', '');
        if (empty($respStatus)) {
            $paymentResult = $request->input('payment_result');
            if (is_array($paymentResult)) {
                $respStatus = $paymentResult['response_status'] ?? '';
            }
        }

        // If PayTabs didn't send status in return (common with localhost),
        // query PayTabs API directly to verify transaction
        $tranRef = $request->input('tranRef', '') ?: session('payment_tran_ref', '');
        if (empty($respStatus) && !empty($tranRef)) {
            $ptUrl = session('payment_pt_url', 'https://secure-jordan.paytabs.com');
            $serverKey = session('payment_pt_server_key', '');
            $profileId = session('payment_pt_profile_id', '');

            if (!empty($serverKey)) {
                $verifyResult = $this->verifyPayTabsTransaction($ptUrl, $serverKey, $profileId, $tranRef);
                \Log::info('PayTabs Verify Result', $verifyResult);

                $respStatus = $verifyResult['payment_result']['response_status'] ?? '';

                // FORCE SUCCESS in Test Mode on Localhost (for 3D Secure failures or callback issues)
                if (empty($respStatus) || $respStatus !== 'A') {
                    if (config('app.env') === 'local' && env('PAYTABS_TEST_MODE')) {
                        \Log::info('PayTabs: Forcing SUCCESS in local test mode despite status: ' . ($respStatus ?: 'None'));
                        $respStatus = 'A';
                    }
                }

                if ($respStatus === 'A') {
                    // Payment verified as successful
                    $invoice->status = 'p';
                    $invoice->total_paid = $invoice->total;
                    $invoice->paid_by = 'paytabs';
                    $invoice->save();
                    $this->updateBookingStatus($invoice);

                    return redirect('/' . $lang . '/invoice/' . $invoice->id . '/')
                        ->with('payment_success', 'Your Invoice Has Been Successfully Paid');
                }
            }
        }

        if ($respStatus === 'A') {
            $invoice->status = 'p';
            $invoice->total_paid = $invoice->total;
            $invoice->paid_by = 'paytabs';
            $invoice->save();
            $this->updateBookingStatus($invoice);

            return redirect('/' . $lang . '/invoice/' . $invoice->id . '/')
                ->with('payment_success', 'Your Invoice Has Been Successfully Paid');
        }

        // Final FALLBACK: FORCE SUCCESS in Test Mode on Localhost
        if (config('app.env') === 'local' && env('PAYTABS_TEST_MODE')) {
             \Log::info('PayTabs: Final Fallback Force SUCCESS in local test mode');
             $invoice->status = 'p';
             $invoice->total_paid = $invoice->total;
             $invoice->paid_by = 'paytabs_test_forced';
             $invoice->save();
             $this->updateBookingStatus($invoice);
             return redirect('/' . $lang . '/invoice/' . $invoice->id . '/')
                ->with('payment_success', 'SANDBOX MODE: Payment accepted for testing purposes.');
        }

        $errorMsg = 'We Are Sorry Your Payment Failed';
        $respMessage = $request->input('payment_result.response_message', '');
        if (!empty($respMessage)) {
            $errorMsg .= ': ' . $respMessage;
        }
        return redirect('/' . $lang . '/invoice/' . $invoice->id . '/')
            ->with('payment_error', $errorMsg);
    }

    /**
     * Verify a PayTabs transaction by querying their API
     */
    private function verifyPayTabsTransaction($ptUrl, $serverKey, $profileId, $tranRef)
    {
        $apiUrl = rtrim($ptUrl, '/') . '/payment/query';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['profile_id' => $profileId, 'tran_ref' => $tranRef]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $serverKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);
        return is_array($result) ? $result : ['error' => 'Invalid response', 'http_code' => $httpCode];
    }

    /**
     * Update associated booking status to confirmed once paid
     */
    private function updateBookingStatus(Invoice $invoice)
    {
        $booking = \App\Models\TourBooking::where('invoice_id', $invoice->id)->first();
        if ($booking) {
            $booking->trip_status = 'con'; // Confirmed
            $booking->save();
        }
    }

    /**
     * Handle manual/offline payment (no gateway configured)
     */
    private function handleManualPayment(Invoice $invoice, $amount, $lang)
    {
        // Mark as partially paid / pending
        $invoice->status = 'pp';
        $invoice->paid_by = 'manual_pending';
        $invoice->save();

        return redirect('/' . $lang . '/invoice/' . $invoice->id . '/')
            ->with('payment_success', 'Payment request received. We will verify and confirm your payment shortly.');
    }

    /**
     * Handle simulated success for testing (Local environment only)
     */
    public function simulateSuccess(Request $request, $lang, $id)
    {
        \Log::info('Entering simulateSuccess', ['id' => $id, 'lang' => $lang]);

        // Only allow in local environment and if test mode is enabled
        if (config('app.env') !== 'local' || !env('PAYTABS_TEST_MODE')) {
             \Log::error('Simulation blocked: Not local or test mode off', ['env' => config('app.env'), 'mode' => env('PAYTABS_TEST_MODE')]);
             abort(403);
        }

        $invoice = Invoice::findOrFail($id);
        if ($invoice->status === 'p') {
            return redirect('/' . $lang . '/invoice/' . $id . '/')->with('success', 'Already paid.');
        }

        // Simulating success
        $invoice->status = 'p';
        $invoice->total_paid = $invoice->total;
        $invoice->paid_by = 'simulate_test';
        $invoice->save();

        $this->updateBookingStatus($invoice);

        return redirect('/' . $lang . '/invoice/' . $id . '/')
            ->with('payment_success', 'TEST SUCCESS: Payment was simulated successfully.');
    }

    /**
     * Handle booking-flow return redirection from PayTabs
     */
    public function handleReturn(Request $request)
    {
        \Log::info('PayTabs Booking Return Received', [
            'method' => $request->method(),
            'all'    => $request->all(),
        ]);

        $bookingId = $request->booking_id ?? $request->cartId ?? $request->cart_id ?? session('paytabs_booking_id');
        $booking   = \App\Models\TourBooking::find($bookingId);

        if (!$booking) {
            \Log::error('Booking not found in PayTabs Return', ['booking_id' => $bookingId]);
            return redirect('/en/')
                ->with('error', 'Booking not found. Please contact support.');
        }

        // Determine lang from request or fallback to 'en'
        $lang = $request->input('lang', session('app_lang', app()->getLocale() ?: 'en'));

        $status  = $request->respStatus ?? $request->response_status ?? null;
        $message = $request->respMessage ?? $request->response_message ?? null;
        $tranRef = $request->tranRef ?? $request->tran_ref ?? session('paytabs_tran_ref');

        // If we have payment_result as array
        if ($request->has('payment_result')) {
            $pr      = $request->input('payment_result');
            $status  = $status  ?? ($pr['response_status']  ?? null);
            $message = $message ?? ($pr['response_message'] ?? null);
            $tranRef = $tranRef ?? ($pr['tran_ref']          ?? null);
        }

        // Double check with PayTabs API if status not confirmed
        if (($status !== 'A') && $tranRef) {
            try {
                $paytabsConfig = config('services.paytabs');
                $queryResponse = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $paytabsConfig['server_key'],
                    'Content-Type'  => 'application/json',
                ])->post($paytabsConfig['base_url'] . 'payment/query', [
                    'profile_id' => (int)$paytabsConfig['profile_id'],
                    'tran_ref'   => $tranRef,
                ]);

                if ($queryResponse->successful()) {
                    $queryData = $queryResponse->json();
                    $status    = $queryData['payment_result']['response_status']  ?? $status;
                    $message   = $queryData['payment_result']['response_message'] ?? $message;
                    $bookingId = $queryData['cart_id'] ?? $bookingId;
                }
            } catch (\Exception $e) {
                \Log::error('PayTabs Query Failed', ['error' => $e->getMessage()]);
            }
        }

        // FORCE SUCCESS in Test Mode on Localhost
        if ($status !== 'A' && config('app.env') === 'local' && env('PAYTABS_TEST_MODE')) {
            \Log::info('PayTabs: Forcing SUCCESS in local test mode');
            $status = 'A';
        }

        if ($status === 'A') {
            // Update invoice + booking
            $invoice = Invoice::find($booking->invoice_id);
            if ($invoice) {
                $invoice->status     = 'p';
                $invoice->total_paid = $invoice->total;
                $invoice->paid_by    = 'paytabs';
                $invoice->save();
            }
            $booking->trip_status = 'con';
            $booking->save();

            session()->forget(['paytabs_tran_ref', 'paytabs_booking_id']);

            return redirect('/' . $lang . '/tours/booking_success/' . $bookingId)
                ->with('success', 'Payment successful! Your booking is confirmed.');
        }

        session()->forget(['paytabs_tran_ref', 'paytabs_booking_id']);

        // Friendly error messages
        $friendlyMsg = match(true) {
            str_contains(strtolower($message ?? ''), '3dsecure')  => 'Payment declined: 3D Secure authentication failed. Please use a different card or contact your bank.',
            str_contains(strtolower($message ?? ''), 'declined')  => 'Your card was declined. Please try a different card.',
            str_contains(strtolower($message ?? ''), 'cancelled') => 'Payment was cancelled.',
            str_contains(strtolower($message ?? ''), 'expired')   => 'Your card has expired. Please use a different card.',
            default => 'Payment could not be completed: ' . ($message ?? 'Please try again.'),
        };

        return redirect('/' . $lang . '/tours/book_tour/' . $booking->tour_id)
            ->with('error', $friendlyMsg);
    }

    /**
     * Handle booking-flow background callback (IPN) from PayTabs
     */
    public function handleCallback(Request $request)
    {
        \Log::info('PayTabs Booking Callback Received', $request->all());

        $data = $request->all();
        $bookingId = $data['cart_id'] ?? null;
        $status = $data['payment_result']['response_status'] ?? null;
        $tranRef = $data['tran_ref'] ?? null;
        $amount = $data['cart_amount'] ?? 0;

        if (!$bookingId || !$status) {
            return response()->json(['status' => 'error', 'message' => 'Invalid data'], 400);
        }

        $booking = \App\Models\TourBooking::find($bookingId);
        if (!$booking) {
            return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
        }

        if ($status === 'A') {
            \DB::beginTransaction();
            try {
                Invoice::where('id', $booking->invoice_id)->update([
                    'status' => 'p',
                    'total_paid' => $amount,
                    'paid_by' => 'paytabs',
                ]);

                $booking->trip_status = 'con';
                $booking->save();

                \DB::commit();
                return response()->json(['status' => 'success']);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'Internal error'], 500);
            }
        }

        return response()->json(['status' => 'success', 'message' => 'Status ignored']);
    }
}
