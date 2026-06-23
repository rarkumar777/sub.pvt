<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FrontendAuthController extends Controller
{
    /**
     * Show frontend login page
     */
    public function showLogin(Request $request, $lang)
    {
        // If already logged in, redirect to home
        if (auth()->check()) {
            return redirect('/' . $lang . '/');
        }

        $captchaCode = substr(md5(mt_rand()), 0, 4);
        session(['frontend_captcha_code' => $captchaCode]);

        $frontendCtrl = new FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        $returnUrl = $request->get('ret', '');

        return view('frontend.login', array_merge($commonData, compact('lang', 'captchaCode', 'returnUrl')));
    }

    /**
     * Handle frontend login
     */
    public function login(Request $request, $lang)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required',
        ]);

        // Validate captcha
        $sessionCaptcha = session('frontend_captcha_code');
        if (strtolower($request->captcha) !== strtolower($sessionCaptcha)) {
            return back()->withErrors([
                'captcha' => 'Invalid captcha code. Please try again.',
            ])->withInput($request->only('email'));
        }

        // Legacy password: SHA1(salt + SHA1(password))
        $salt = 'asdajs';
        $hashedPassword = sha1($salt . sha1($request->password));

        $user = User::where('email', $request->email)
            ->where('pass', $hashedPassword)
            ->first();

        if ($user) {
            Auth::login($user);

            $returnUrl = $request->input('ret', '');
            if (!empty($returnUrl)) {
                return redirect(urldecode($returnUrl));
            }

            return redirect('/' . $lang . '/');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput($request->only('email'));
    }

    /**
     * Show frontend register page
     */
    public function showRegister(Request $request, $lang)
    {
        if (auth()->check()) {
            return redirect('/' . $lang . '/');
        }

        $captchaCode = substr(md5(mt_rand()), 0, 4);
        session(['frontend_captcha_code' => $captchaCode]);

        $frontendCtrl = new FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        // Countries for dropdown
        $countries = Country::where('lang', $lang)->orderBy('name')->get();

        // Load group field config for "clients" (default registration group)
        $groupFields = $this->getGroupFieldConfig('clients');

        return view('frontend.register', array_merge($commonData, compact('lang', 'captchaCode', 'countries', 'groupFields')));
    }

    /**
     * Get group field configuration from pvt.jo config.
     * Returns associative array: field_name => 'a' (ask) | 'r' (required) | 'd' (disabled)
     * If no config exists, all configurable fields default to 'a' (ask/show).
     */
    private function getGroupFieldConfig($groupId)
    {
        $configPath = base_path('../pvt.jo/config/users/groups/' . $groupId . '.php');

        $GOGIES = [];
        if (file_exists($configPath)) {
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($configPath, true);
            }
            if (!defined('gogies')) {
                define('gogies', true);
            }
            include $configPath;
        }

        $fields = $GOGIES['uf'][$groupId] ?? [];

        // All configurable field keys with default 'a' (ask/show)
        $allFields = [
            'first_name', 'last_name', 'email', 'url', 'country', 'city',
            'company', 'mobile', 'phone', 'fax', 'address', 'birth_day',
            'gender', 'avatar'
        ];

        $result = [];
        foreach ($allFields as $key) {
            $result[$key] = $fields[$key] ?? 'a';
        }

        return $result;
    }

    /**
     * Handle frontend registration
     */
    public function register(Request $request, $lang)
    {
        // Load group field config for dynamic validation
        $groupFields = $this->getGroupFieldConfig('clients');

        // Core fields — always required
        $rules = [
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'password' => 'required|min:4',
            'retype_password' => 'required|same:password',
            'captcha' => 'required',
        ];

        // Dynamic fields — add validation only if visible ('a' or 'r')
        $optionalFields = [
            'url' => 'string|max:250',
            'country' => 'integer',
            'city' => 'string|max:150',
            'company' => 'string|max:150',
            'mobile' => 'string|max:50',
            'phone' => 'string|max:50',
            'fax' => 'string|max:50',
            'address' => 'string|max:500',
            'birth_day' => 'date',
            'gender' => 'integer|in:0,1',
        ];

        foreach ($optionalFields as $field => $fieldRules) {
            $status = $groupFields[$field] ?? 'a';
            if ($status === 'r') {
                $rules[$field] = 'required|' . $fieldRules;
            } elseif ($status === 'a') {
                $rules[$field] = 'nullable|' . $fieldRules;
            }
            // 'd' = disabled — no validation rule added, field is ignored
        }

        $request->validate($rules);

        // Validate captcha
        $sessionCaptcha = session('frontend_captcha_code');
        if (strtolower($request->captcha) !== strtolower($sessionCaptcha)) {
            return back()->withErrors([
                'captcha' => 'Invalid captcha code. Please try again.',
            ])->withInput($request->except('password', 'retype_password'));
        }

        // Check if email already exists
        $exists = User::where('email', $request->email)->first();
        if ($exists) {
            return back()->withErrors([
                'email' => 'This email is already registered.',
            ])->withInput($request->except('password', 'retype_password'));
        }

        // Legacy password hash
        $salt = 'asdajs';
        $plainPassword = $request->password;
        $hashedPassword = sha1($salt . sha1($plainPassword));

        try {
            $user = new User();
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name') ?? '';
            $user->email = $request->input('email');
            $user->pass = $hashedPassword;
            $user->url = $request->input('url') ?? '';
            $user->country = (int)($request->input('country') ?? 0);
            $user->city = $request->input('city') ?? '';
            $user->company = $request->input('company') ?? '';
            $user->mobile = $request->input('mobile') ?? '';
            $user->phone = $request->input('telephone') ?? '';
            $user->fax = $request->input('fax') ?? '';
            $user->address = $request->input('address') ?? '';
            $user->birth_day = $request->input('birth_day') ?: null;
            $user->gender = (int)($request->input('gender') ?? 1);
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

            // Handle avatar
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $filename = 'avatar_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/avatars'), $filename);
                $user->avatar = 'uploads/avatars/' . $filename;
            } else {
                $user->avatar = '';
            }

            $user->save();
        } catch (\Exception $e) {
            \Log::error('Registration failed: ' . $e->getMessage());
            return back()->withErrors([
                'error' => 'Registration failed. Please check your details and try again.',
            ])->withInput($request->except('password', 'retype_password'));
        }

        // Send welcome email with login details
        try {
            \Illuminate\Support\Facades\Mail::send('emails.welcome-registration', [
                'user' => $user,
                'plainPassword' => $plainPassword,
                'loginUrl' => url('/' . $lang . '/users/login/'),
            ], function ($message) use ($user) {
                $message->to($user->email, $user->first_name . ' ' . $user->last_name)
                        ->subject('Welcome! Your Account Has Been Created');
            });
        } catch (\Exception $e) {
            // Email sending failed but registration succeeded, continue silently
        }

        // Auto-login after registration
        Auth::login($user);

        return redirect('/' . $lang . '/')->with('success', 'Account created successfully! A welcome email has been sent to your email address.');
    }

    /**
     * Show edit account page
     */
    public function showEditAccount(Request $request, $lang)
    {
        if (!auth()->check()) {
            return redirect('/' . $lang . '/users/login/?ret=' . urlencode('/' . $lang . '/users/account/edit-account/'));
        }

        $frontendCtrl = new FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        $user = auth()->user();
        $countries = Country::where('lang', $lang)->orderBy('name')->get();

        return view('frontend.edit_account', array_merge($commonData, compact('lang', 'user', 'countries')));
    }

    /**
     * Handle account update
     */
    public function updateAccount(Request $request, $lang)
    {
        if (!auth()->check()) {
            return redirect('/' . $lang . '/users/login/');
        }

        $request->validate([
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
        ]);

        $user = auth()->user();

        // Check if email changed and already in use
        if ($request->email !== $user->email) {
            $exists = User::where('email', $request->email)->where('id', '!=', $user->id)->first();
            if ($exists) {
                return back()->withErrors(['email' => 'This email is already in use.'])->withInput();
            }
        }

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->url = $request->input('url', '');
        $user->country = $request->input('country', '');
        $user->city = $request->input('city', '');
        $user->company = $request->input('company', '');
        $user->mobile = $request->input('mobile', '');
        $user->phone = $request->input('telephone', '');
        $user->fax = $request->input('fax', '');
        $user->address = $request->input('address', '');
        $user->birth_day = $request->input('birth_day', '');
        $user->gender = $request->input('gender', 'male');

        // Update password if provided
        if ($request->filled('password') && $request->filled('retype_password')) {
            if ($request->password !== $request->retype_password) {
                return back()->withErrors(['password' => 'Password and Retype Password do not match.'])->withInput();
            }
            $salt = 'asdajs';
            $user->pass = sha1($salt . sha1($request->password));
        }

        // Handle avatar
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $user->avatar = 'uploads/avatars/' . $filename;
        }

        $user->save();

        return back()->with('success', 'Account updated successfully!');
    }

    /**
     * Show user bookings page
     */
    public function showMyBookings(Request $request, $lang)
    {
        if (!auth()->check()) {
            return redirect('/' . $lang . '/users/login/?ret=' . urlencode('/' . $lang . '/users/account/my-bookings/'));
        }

        $frontendCtrl = new FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        $user = auth()->user();
        $bookings = \App\Models\TourBooking::where('user_id', $user->id)
            ->with(['invoice', 'tour.contents' => function($q) use ($lang) {
                $q->where('lang', $lang);
            }])
            ->orderBy('id', 'desc')
            ->get();

        return view('frontend.my_bookings', array_merge($commonData, compact('lang', 'user', 'bookings')));
    }

    /**
     * Show user messages page
     */
    public function showMyMessages(Request $request, $lang)
    {
        if (!auth()->check()) {
            return redirect('/' . $lang . '/users/login/?ret=' . urlencode('/' . $lang . '/users/account/my-messages/'));
        }

        $frontendCtrl = new FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);

        $user = auth()->user();
        $tripRequests = \App\Models\TripRequest::where('email', $user->email)
            ->with(['messages' => function($q) {
                $q->orderBy('created_at', 'asc');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('frontend.my_messages', array_merge($commonData, compact('lang', 'user', 'tripRequests')));
    }

    /**
     * Send message from frontend user
     */
    public function sendMessage(Request $request, $lang)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $user = auth()->user();
        $tripRequest = \App\Models\TripRequest::where('id', $request->input('trip_request_id'))
            ->where('email', $user->email)
            ->first();

        if (!$tripRequest) {
            return response()->json(['success' => false, 'message' => 'Trip request not found'], 404);
        }

        $message = trim($request->input('message', ''));
        if (empty($message)) {
            return response()->json(['success' => false, 'message' => 'Message is required'], 422);
        }

        \App\Models\TripRequestMessage::create([
            'trip_request_id' => $tripRequest->id,
            'user_id' => $user->id,
            'sender_type' => 'client',
            'sender_name' => $user->first_name . ' ' . $user->last_name,
            'message' => $message,
        ]);

        $tripRequest->update([
            'is_read' => 0,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Show Forgot Password form
     */
    public function showForgotPassword(Request $request, $lang)
    {
        $frontendCtrl = new FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);
        return view('frontend.forgot_password', array_merge($commonData, compact('lang')));
    }

    /**
     * Send password reset link email
     */
    public function sendForgotPassword(Request $request, $lang)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Always show success to prevent email enumeration
        if (!$user) {
            return back()->with('status', 'If this email is registered, you will receive a reset link shortly.');
        }

        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
        );

        $resetLink = url('/' . $lang . '/users/reset-password/' . $token) . '?email=' . urlencode($request->email);

        try {
            Mail::send([], [], function ($message) use ($request, $resetLink) {
                $message->to($request->email)
                    ->subject('Reset Your Password')
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 30px; border: 1px solid #eee; border-radius: 10px;'>
                            <h2 style='color: #eb9950;'>Password Reset Request</h2>
                            <p>You are receiving this email because we received a password reset request for your account.</p>
                            <p style='text-align: center; margin: 30px 0;'>
                                <a href='{$resetLink}' style='background-color: #eb9950; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Reset Password</a>
                            </p>
                            <p style='color:#888; font-size:13px;'>This link will expire in 60 minutes.</p>
                            <p style='color:#888; font-size:13px;'>If you did not request a password reset, no further action is required.</p>
                            <hr style='border:0; border-top:1px solid #eee; margin:20px 0;'>
                            <p style='font-size:12px; color:#aaa;'>Or copy this link: {$resetLink}</p>
                        </div>
                    ");
            });
        } catch (\Exception $e) {
            // Email failed silently — still show success
        }

        return back()->with('status', 'If this email is registered, you will receive a reset link shortly.');
    }

    /**
     * Show Reset Password form
     */
    public function showResetPassword(Request $request, $lang, $token)
    {
        $frontendCtrl = new FrontendController();
        $commonData = $frontendCtrl->getCommonData($lang);
        $email = $request->email;
        return view('frontend.reset_password', array_merge($commonData, compact('lang', 'token', 'email')));
    }

    /**
     * Handle Reset Password
     */
    public function resetPassword(Request $request, $lang)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:4|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Invalid or expired reset link.']);
        }

        if (Carbon::parse($record->created_at)->addHours(1)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $salt = 'asdajs';
        $user->pass = sha1($salt . sha1($request->password));
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/' . $lang . '/users/login/')
            ->with('status', 'Your password has been reset successfully. Please login.');
    }

    /**
     * Frontend logout
     */
    public function logout(Request $request, $lang)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/' . $lang . '/');
    }
}
