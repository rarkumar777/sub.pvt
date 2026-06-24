@extends('frontend.layout')
@section('title', 'Login')

@section('content')
<!-- Montserrat Font -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --brand-orange: #eb9950;
        --brand-dark-orange: #d35400;
        --brand-blue: #337ab7;
        --card-bg: rgba(255, 255, 255, 0.95);
        --text-dark: #2d3436;
        --text-gray: #636e72;
    }

    #main-nav {
        background-color: #0f1729 !important;
    }

    footer {
        margin-top: 0 !important;
    }

    .login-ui-wrapper {
        font-family: 'Montserrat', sans-serif;
        background: #fdfdfd;
        padding: 20px 0; /* Minimized padding */
        min-height: auto; /* Remove fixed min-height to reduce scroll */
        display: flex;
        align-items: center;
        justify-content: center;
        background-image: radial-gradient(circle at 5% 5%, rgba(235, 153, 80, 0.05) 0%, transparent 35%),
                          radial-gradient(circle at 95% 95%, rgba(51, 122, 183, 0.05) 0%, transparent 35%);
    }

    .login-container-modern {
        background: var(--card-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 30px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 650px; /* Increased Width */
        padding: 40px 55px; /* Refined Height/Padding */
        position: relative;
        text-align: center;
    }

    .login-logo-area {
        margin-bottom: 15px; /* Reduced margin */
    }

    .login-logo-area img {
        height: 50px; /* Slightly smaller logo */
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));
    }

    .login-title-area {
        margin-bottom: 20px; /* Reduced margin */
    }

    .login-title-area h2 {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 6px;
    }

    .login-title-area p {
        color: var(--text-gray);
        font-size: 14px;
    }

    .form-group-modern {
        margin-bottom: 15px; /* Reduced gap */
        position: relative;
    }

    .input-wrapper-modern {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrapper-modern i.input-icon {
        position: absolute;
        left: 22px; /* Fixed Icon Position */
        color: var(--brand-orange);
        font-size: 18px;
        z-index: 5;
        pointer-events: none;
    }

    .form-input-modern {
        width: 100%;
        height: 44px; /* Reduced Height to 44px */
        padding: 0 20px 0 60px !important; /* FORCED PADDING TO FIX OVERLAP */
        background: #fff;
        border: 2px solid #edf2f7;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 500;
        color: var(--text-dark);
        transition: all 0.3s ease;
    }

    .form-input-modern:focus {
        outline: none;
        border-color: var(--brand-orange);
        box-shadow: 0 0 0 4px rgba(235, 153, 80, 0.1);
    }

    /* Cohesive Captcha UI */
    .captcha-row-modern {
        display: flex;
        gap: 0;
        margin-bottom: 20px; /* Reduced margin */
        border: 2px solid #edf2f7;
        border-radius: 12px;
        overflow: hidden;
    }

    .captcha-row-modern:focus-within {
        border-color: var(--brand-orange);
    }

    .captcha-code-modern {
        background: var(--brand-blue);
        color: white;
        padding: 0 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        letter-spacing: 2px;
        font-style: italic;
        min-width: 130px;
        user-select: none;
    }

    .captcha-input-modern {
        flex: 1;
        height: 40px; /* Matching the 44px height with border */
        border: none !important;
        border-radius: 0 !important;
        padding-left: 20px !important;
        background: #fff;
        box-shadow: none !important;
    }

    /* Premium Button */
    .btn-login-modern {
        width: 100%;
        height: 46px; /* Reduced Height to 46px */
        background: linear-gradient(135deg, var(--brand-orange), var(--brand-dark-orange));
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 15px rgba(235, 153, 80, 0.3);
        margin-bottom: 20px;
    }

    .btn-login-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px rgba(235, 153, 80, 0.4);
        filter: brightness(1.05);
    }

    /* Footer Links */
    .login-footer-links {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px; /* Reduced padding */
        border-top: 1px solid #edf2f7;
    }

    .login-footer-links a {
        color: var(--brand-blue);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .login-footer-links a:hover {
        color: var(--brand-orange);
    }

    /* Alert */
    .error-modern {
        background: #fff5f5;
        color: #c53030;
        padding: 12px 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        border-left: 4px solid #f56565;
        text-align: left;
    }

    @media (max-width: 600px) {
        .login-container-modern {
            padding: 30px 20px;
            margin: 0 10px;
        }
        .captcha-code-modern {
            min-width: 100px;
        }
    }
</style>

<div class="login-ui-wrapper">
    <div class="login-container-modern">
        
        <div class="login-logo-area">
            <img src="{{ asset('Pvtnew1.png') }}" alt="PV Travels">
        </div>

        <div class="login-title-area">
            <h2>Welcome Back</h2>
            <p>Access your travel itinerary and bookings.</p>
        </div>

        @if(session('status'))
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:10px; text-align:left;">
                <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="error-modern">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $error }}
                </div>
            @endforeach
        @endif

        <form method="POST" action="{{ route('frontend.login.post', ['lang' => $lang]) }}">
            @csrf
            <input type="hidden" name="ret" value="{{ $returnUrl ?? '' }}">

            <div class="form-group-modern">
                <div class="input-wrapper-modern">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-input-modern" placeholder="Email Address" required autofocus>
                </div>
            </div>

            <div class="form-group-modern">
                <div class="input-wrapper-modern">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" name="password"
                           class="form-input-modern" placeholder="Password" required>
                </div>
            </div>

            <div class="captcha-row-modern">
                <div class="captcha-code-modern">
                    <span>{{ $captchaCode }}</span>
                </div>
                <input type="text" name="captcha" class="form-input-modern captcha-input-modern"
                       placeholder="Enter Security Code" required>
            </div>

            <button type="submit" class="btn-login-modern">
                <span>Login Securely</span>
                <i class="fa-solid fa-arrow-right-long"></i>
            </button>

            <div class="login-footer-links">
                <a href="/{{ $lang }}/users/forgot-password/"><i class="fa-solid fa-shield-halved"></i> Forgot?</a>
                <a href="/{{ $lang }}/users/register/"><i class="fa-solid fa-user-plus"></i> Join Us</a>
            </div>
        </form>
    </div>
</div>

@endsection
