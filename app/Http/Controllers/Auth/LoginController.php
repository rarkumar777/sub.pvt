<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Show login form with captcha.
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Generate random 4-char hex captcha code
        $captchaCode = substr(md5(mt_rand()), 0, 4);
        session(['captcha_code' => $captchaCode]);

        return view('auth.login', [
            'returnUrl' => $request->get('ret', ''),
            'captchaCode' => $captchaCode,
        ]);
    }

    /**
     * Handle login attempt.
     * Uses legacy SHA1 + salt password hashing to match existing users.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required',
        ]);

        // Validate captcha
        $sessionCaptcha = session('captcha_code');
        if (strtolower($request->captcha) !== strtolower($sessionCaptcha)) {
            return back()->withErrors([
                'captcha' => 'Invalid captcha code. Please try again.',
            ])->withInput($request->only('email'));
        }

        // Legacy password: SHA1(salt + SHA1(password))
        $salt = 'asdajs'; // From legacy config
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

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput($request->only('email'));
    }

    /**
     * Logout the user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Show Forgot Password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send Reset Link Email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find a user with that email address.']);
        }

        // Create token
        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Send Email (Simplified for Pro version)
        $resetLink = route('password.reset', ['token' => $token]) . '?email=' . urlencode($request->email);
        
        try {
            Mail::send([], [], function ($message) use ($request, $resetLink) {
                $message->to($request->email)
                    ->subject('Reset Password')
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                            <h2 style='color: #eb9950;'>Password Reset Request</h2>
                            <p>You are receiving this email because we received a password reset request for your account.</p>
                            <p style='text-align: center; margin: 30px 0;'>
                                <a href='{$resetLink}' style='background-color: #eb9950; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Reset Password</a>
                            </p>
                            <p>This password reset link will expire in 60 minutes.</p>
                            <p>If you did not request a password reset, no further action is required.</p>
                            <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                            <p style='font-size: 12px; color: #888;'>If you're having trouble clicking the \"Reset Password\" button, copy and paste the URL below into your web browser: <br> {$resetLink}</p>
                        </div>
                    ");
            });
        } catch (\Exception $e) {
            // Log error if needed or handle gracefully
        }

        return back()->with('status', 'We have e-mailed your password reset link!');
    }

    /**
     * Show Reset Form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle Password Reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:4|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Invalid token or email address.']);
        }

        // Check if token is expired (1 hour)
        if (Carbon::parse($record->created_at)->addHours(1)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'This password reset link has expired.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Legacy hashing: sha1(salt + sha1(password))
        $salt = 'asdajs';
        $user->pass = sha1($salt . sha1($request->password));
        $user->save();

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset!');
    }

    /**
     * Show Registration Form.
     */
    public function showRegistrationForm()
    {
        $countries = Country::orderBy('name')->get();
        $captchaCode = substr(md5(mt_rand()), 0, 4);
        session(['admin_captcha_code' => $captchaCode]);

        return view('auth.register', compact('countries', 'captchaCode'));
    }

    /**
     * Handle Admin Registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:150',
            'last_name' => 'required|string|max:150',
            'email' => 'required|email|unique:en33_users,email',
            'password' => 'required|min:4|confirmed',
            'captcha' => 'required',
        ]);

        // Validate Captcha
        if (strtolower($request->captcha) !== strtolower(session('admin_captcha_code'))) {
            return back()->withErrors(['captcha' => 'Invalid captcha code.'])->withInput();
        }

        // Legacy Hashing
        $salt = 'asdajs';
        $hashedPassword = sha1($salt . sha1($request->password));

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name ?? '';
        $user->email = $request->email;
        $user->pass = $hashedPassword;
        $user->url = $request->input('url') ?? '';
        
        // Country must be an integer (lang_id)
        $countryName = $request->input('country');
        $country = Country::where('name', $countryName)->first();
        $user->country = $country ? $country->lang_id : 0;
        
        $user->city = $request->input('city') ?? '';
        $user->company = $request->input('company') ?? '';
        $user->mobile = $request->input('mobile') ?? '';
        $user->phone = $request->input('telephone') ?? '';
        $user->fax = $request->input('fax') ?? '';
        $user->address = $request->input('address') ?? '';
        $user->birth_day = $request->input('birth_day') ?: null;
        
        // Gender must be integer (Male: 1, Female: 0 or as per DB convention)
        $user->gender = $request->input('gender') == 'male' ? 1 : 0;
        
        $user->user_group = 'clients'; 
        $user->user_regdate = time(); // Must be Unix Timestamp (Integer)
        $user->status = 1;
        
        // Uninitialized legacy fields (Must be integers where required)
        $user->last_login = 0; 
        $user->user_sig = 0;
        $user->posts = 0;
        $user->attachsig = 0;
        $user->rank = 0;
        $user->timezone_offset = 0;
        $user->fb_id = 0;
        $user->auth_secret = '';
        $user->permission = '';

        // Handle Avatar
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'admin_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $user->avatar = 'uploads/avatars/' . $filename;
        } else {
            $user->avatar = '';
        }

        $user->save();

        // Log the user in
        Auth::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Account created successfully!');
    }
}
