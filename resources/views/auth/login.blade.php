<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favpvt1.png') }}?v=1" />
    <title>Admin Login | PV Travels</title>
    
    <!-- Google Fonts: Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --brand-orange: #eb9950;
            --brand-dark: #cc7a33;
            --brand-blue: #337ab7;
            --bg-light: #f4f7f6;
            --text-dark: #2d3436;
            --text-gray: #636e72;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-light);
            position: relative;
            overflow: hidden;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            padding: 50px 45px;
            text-align: center;
            border: 1px solid #edf2f7;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .logo-container {
            margin-bottom: 25px;
        }

        .logo-container img {
            height: 60px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));
        }

        .login-header {
            margin-bottom: 35px;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .login-header p {
            color: var(--text-gray);
            font-size: 14px;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px;
            position: relative;
            text-align: left;
        }

        .input-icon {
            position: absolute;
            left: 22px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--brand-orange);
            font-size: 18px;
            z-index: 5;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            height: 50px;
            padding: 0 20px 0 60px !important; /* Force padding to prevent icon overlap */
            background: #f8fafc;
            border: 2px solid #edf2f7;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background: #fff;
            border-color: var(--brand-orange);
            box-shadow: 0 0 0 4px rgba(235, 153, 80, 0.1);
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        /* Captcha Styling */
        .captcha-container {
            display: flex;
            gap: 12px;
            margin-bottom: 25px;
        }

        .captcha-box {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--brand-blue);
            color: white;
            padding: 0 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 20px;
            letter-spacing: 3px;
            font-style: italic;
            min-width: 120px;
            user-select: none;
        }

        .captcha-input {
            flex: 1;
            padding-left: 20px !important;
            height: 50px;
        }

        /* Login Button - Refined professional style */
        .btn-login {
            width: 100%;
            height: 52px;
            background-color: var(--brand-orange);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(235, 153, 80, 0.3);
            margin-bottom: 25px;
        }

        .btn-login:hover {
            background-color: var(--brand-dark);
            box-shadow: 0 12px 25px rgba(235, 153, 80, 0.4);
            transform: translateY(-2px);
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        /* Footer Links - Split Left and Right */
        .login-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #edf2f7;
        }

        .login-footer a {
            color: var(--brand-blue);
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .login-footer a:hover {
            color: var(--brand-orange);
        }

        /* Error States */
        .alert-error {
            background: #fff5f5;
            color: #e53e3e;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 5px solid #e53e3e;
            text-align: left;
        }

        @media (max-width: 500px) {
            .login-card {
                padding: 40px 25px;
            }
            .login-footer {
                flex-direction: row; /* Keep horizontal even on mobile as requested */
                font-size: 11px;
            }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">
            
            <div class="logo-container">
                <img src="{{ asset('Pvtnew1.png') }}" alt="PV Travels">
            </div>

            <div class="login-header">
                <h1>Admin Portal</h1>
                <p>Welcome back! Please enter your details.</p>
            </div>

            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $error }}
                    </div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <input type="hidden" name="ret" value="{{ $returnUrl ?? '' }}">

                <div class="form-group">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="form-control" placeholder="Email Address" required autofocus>
                </div>

                <div class="form-group">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" name="password" id="password"
                           class="form-control" placeholder="Password" required>
                </div>

                <div class="captcha-container">
                    <div class="captcha-box">
                        <span>{{ $captchaCode ?? 'ae8e' }}</span>
                    </div>
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <input type="text" name="captcha" class="form-control captcha-input"
                               placeholder="Enter Code" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <span>Login</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>

                <div class="login-footer">
                    <a href="{{ route('password.request') }}"><i class="fa-solid fa-key"></i> Forgot Password?</a>
                    <a href="{{ route('register') }}"><i class="fa-solid fa-user-plus"></i> Create Account</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
