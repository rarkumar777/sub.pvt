<?php

namespace App\Http\Controllers;

use App\Models\TripRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class TripWizardController extends Controller
{
    public function show()
    {
        $accommodationCategories = \App\Models\ServiceCategory::where('parent_id', 403)->get();
        return view('trip-wizard', compact('accommodationCategories'));
    }

    public function store(Request $request)
    {
        $isLoggedIn = Auth::check();

        // Different validation rules for logged-in vs guest users
        $rules = [
            // Step 1: Planning
            'project_stage' => 'nullable|string',
            // Step 2: Participants & dates
            'participant_type' => 'nullable|string',
            'adults' => 'nullable|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'children_ages' => 'nullable|array',
            'is_honeymoon' => 'nullable|boolean',
            'has_exact_dates' => 'nullable|boolean',
            'departure_date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'departure_period' => 'nullable|string|max:100',
            'approx_duration' => 'nullable|string|max:100',
            // Step 3: Travel plan
            'travel_styles' => 'nullable|array',
            'accommodation_prefs' => 'nullable|array',
            'travel_plan' => 'nullable|string',
            'guide_type' => 'nullable|string',
            'guide_languages' => 'nullable|array',
            // Step 4: Budget
            'ideal_budget' => 'nullable|numeric',
            'max_budget' => 'nullable|numeric',
            'currency' => 'nullable|string|max:10',
            // Extra flags
            'is_logged_in' => 'nullable|boolean',
        ];

        if (!$isLoggedIn) {
            // Guest user: require registration fields
            $rules['civility'] = 'nullable|string|max:10';
            $rules['first_name'] = 'required|string|max:100';
            $rules['last_name'] = 'required|string|max:100';
            $rules['email'] = 'required|email|max:255';
            $rules['phone'] = 'nullable|string|max:50';
            $rules['password'] = 'required|string|min:1';
            $rules['dob'] = 'nullable|string|max:20';
            $rules['country'] = 'nullable|string|max:10';
            $rules['marketing_consent'] = 'nullable|boolean';
            $rules['terms_consent'] = 'accepted';
        }

        $data = $request->validate($rules);

        // Encode JSON fields
        $data['children_ages'] = json_encode($request->input('children_ages', []));
        $data['travel_styles'] = json_encode($request->input('travel_styles', []));
        $data['accommodation_prefs'] = json_encode($request->input('accommodation_prefs', []));
        $data['guide_languages'] = json_encode($request->input('guide_languages', []));

        // Set defaults
        $data['pipeline_stage'] = 'new_request';
        $data['marketing_consent'] = $request->boolean('marketing_consent');
        $data['terms_consent'] = true;

        $alreadyRegistered = false;

        if ($isLoggedIn) {
            // Logged-in user: use their existing data
            $user = Auth::user();
            $data['first_name'] = $user->first_name;
            $data['last_name'] = $user->last_name;
            $data['email'] = $user->email;
            $data['phone'] = $user->mobile ?? '';
            $data['password'] = $user->pass ?? '';
            $data['civility'] = $user->gender == 2 ? 'Mrs' : 'Mr';
            $data['country'] = $user->country ?? '';
            $data['dob'] = $user->birth_day ?? '';
        } else {
            // Guest user: create or find user
            $plainPassword = $data['password'];

            // Hash password for trip_requests table
            $data['password'] = Hash::make($plainPassword);

            // Convert DOB from DD/MM/YYYY to YYYY-MM-DD for MySQL
            $birthDay = null;
            if (!empty($data['dob'])) {
                $dobParts = explode('/', $data['dob']);
                if (count($dobParts) === 3) {
                    $birthDay = $dobParts[2] . '-' . $dobParts[1] . '-' . $dobParts[0]; // YYYY-MM-DD
                }
            }

            $user = User::where('email', $data['email'])->first();
            if ($user) {
                $alreadyRegistered = true;
            } else {
                // Legacy password hash (same as frontend register)
                $salt = 'asdajs';
                $hashedPassword = sha1($salt . sha1($plainPassword));

                $user = new User();
                $user->first_name = $data['first_name'];
                $user->last_name = $data['last_name'];
                $user->email = $data['email'];
                $user->pass = $hashedPassword;
                $user->url = '';
                $user->country = 0;
                $user->city = '';
                $user->company = '';
                $user->mobile = $data['phone'] ?? '';
                $user->phone = '';
                $user->fax = '';
                $user->address = '';
                $user->birth_day = $birthDay;
                $user->gender = ($data['civility'] ?? 'Mr.') === 'Mrs.' ? 2 : 1;
                $user->user_group = 'clients';
                $user->user_regdate = time();
                $user->status = 1;
                $user->last_login = 0;
                $user->user_sig = 0;
                $user->posts = 0;
                $user->attachsig = 0;
                $user->rank = 0;
                $user->timezone_offset = 0;
                $user->fb_id = null;
                $user->auth_secret = '';
                $user->permission = '';
                $user->avatar = '';
                $user->save();
            }

            // Auto-login only if new user
            if (!$alreadyRegistered) {
                Auth::login($user);
            }
        }

        // Remove the is_logged_in flag before saving
        unset($data['is_logged_in']);

        // Trip request always gets created
        $tripRequest = TripRequest::create($data);

        // -------------------------------------------------------
        // Send emails
        // -------------------------------------------------------
        $recipientEmail = $data['email'];
        $recipientName  = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));

        // 1) Welcome email — only for brand-new (guest) users
        if (!$isLoggedIn && !$alreadyRegistered) {
            try {
                Mail::send('emails.welcome-registration', [
                    'user'          => $user,
                    'plainPassword' => $plainPassword,
                    'loginUrl'      => url('/en/users/login/'),
                ], function ($message) use ($recipientEmail, $recipientName) {
                    $message->to($recipientEmail, $recipientName)
                            ->subject('Welcome! Your Account Has Been Created');
                });
            } catch (\Exception $e) {
                \Log::error('TripWizard welcome email failed: ' . $e->getMessage());
            }
        }

        // 2) Trip confirmation email — sent to all users (new, existing, logged-in)
        try {
            Mail::send('emails.trip-request-confirmation', [
                'firstName' => $data['first_name'] ?? '',
                'lastName'  => $data['last_name']  ?? '',
                'tripData'  => $data,
            ], function ($message) use ($recipientEmail, $recipientName) {
                $message->to($recipientEmail, $recipientName)
                        ->subject('✈️ Your Trip Request Has Been Received!');
            });
        } catch (\Exception $e) {
            \Log::error('TripWizard confirmation email failed: ' . $e->getMessage());
        }

        // 3) Admin ko notify karo
        try {
            $adminEmail = 'info@pvt.jo';
            Mail::send([], [], function ($msg) use ($data, $recipientName, $recipientEmail, $adminEmail) {
                $body  = "<h2>New Trip Wizard Request</h2>";
                $body .= "<p><strong>Name:</strong> {$recipientName}</p>";
                $body .= "<p><strong>Email:</strong> {$recipientEmail}</p>";
                $body .= "<p><strong>Phone:</strong> " . ($data['phone'] ?? '—') . "</p>";
                $body .= "<p><strong>Departure Date:</strong> " . ($data['departure_date'] ?? '—') . "</p>";
                $body .= "<p><strong>Adults:</strong> " . ($data['adults'] ?? 1) . " | <strong>Children:</strong> " . ($data['children'] ?? 0) . "</p>";
                $body .= "<p><strong>Travel Plan:</strong><br>" . nl2br(e($data['travel_plan'] ?? '')) . "</p>";
                $msg->to($adminEmail, 'PVT Reservations')
                    ->subject("New Trip Wizard Request — {$recipientName}")
                    ->html($body);
            });
        } catch (\Exception $e) {
            \Log::error('TripWizard admin email failed: ' . $e->getMessage());
        }

        // Quotations dashboard me dikhane ke liye record banao
        try {
            $quotation = new \App\Models\TourQuotation();
            $quotation->customer_name = substr($recipientName, 0, 150);
            $quotation->email = substr($data['email'] ?? '', 0, 150);
            $quotation->phone = substr($data['phone'] ?? '', 0, 17);
            $quotation->ref_number = 'WIZ-' . date('Ymd') . '-' . rand(1000, 9999);
            $quotation->travel_date = $data['departure_date'] ?? date('Y-m-d', strtotime('+30 days'));
            $durStr = $data['approx_duration'] ?? '';
            preg_match('/\d+/', $durStr, $matches);
            $quotation->days = isset($matches[0]) ? intval($matches[0]) : 1;
            $quotation->nights = max(0, $quotation->days - 1);
            $quotation->pricing_base = 0;
            $quotation->description = substr("Trip Wizard Request:\n" . ($data['travel_plan'] ?? ''), 0, 500);
            $quotation->travelers_number = intval($data['adults'] ?? 1) + intval($data['children'] ?? 0);
            $quotation->lang = 'en';
            $quotation->added_by = Auth::id() ?: 0;
            $quotation->last_edited = time();
            $quotation->views = 0;
            $quotation->total_cost = 0;
            $quotation->total = 0;
            $quotation->status = 'draft';
            $quotation->save();
        } catch (\Exception $e) {
            \Log::error('TripWizard quotation creation failed: ' . $e->getMessage());
        }
        // -------------------------------------------------------

        if ($request->ajax() || $request->wantsJson()) {
            $message = 'Your trip request has been submitted!';
            if ($alreadyRegistered) {
                $message = 'This email is already registered. Your trip request has been submitted. Please login with your existing password.';
            }
            return response()->json(['success' => true, 'already_registered' => $alreadyRegistered, 'message' => $message]);
        }

        return redirect('/create-trip')->with('success', 'Your trip request has been submitted successfully!');
    }
}
