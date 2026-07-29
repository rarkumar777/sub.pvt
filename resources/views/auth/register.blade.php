<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favpvt1.png') }}?v=1" />
    <title>Create Account | PV Travels</title>
    
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
            --border-color: #edf2f7;
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
            padding: 15px; /* Reduced body padding */
        }

        .register-wrapper {
            width: 100%;
            max-width: 1150px; 
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 12px 35px; /* Ultra-minimized padding */
            border: 1px solid var(--border-color);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2px; /* Nano margin */
        }

        .logo-container img {
            height: 28px; /* Extremely tiny logo */
        }

        .login-header {
            text-align: center;
            margin-bottom: 5px; /* Nano margin */
        }

        .login-header h1 {
            font-size: 18px; /* Nano font */
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 2px;
        }

        .login-header p {
            display: none; /* Removed sub-header to save room */
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 6px 15px; /* Nano gaps */
        }

        @media (max-width: 1100px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
            .captcha-container, .btn-pro {
                grid-column: span 2 !important;
            }
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .captcha-container, .btn-pro {
                grid-column: span 1 !important;
            }
            .login-card {
                padding: 40px 25px;
            }
        }

        .form-group {
            margin-bottom: 5px;
            position: relative;
        }

        .form-group label {
            display: none; /* Removed labels to save height */
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--brand-orange);
            font-size: 16px;
            z-index: 5;
        }

        .form-control {
            width: 100%;
            height: 32px; /* Nano height */
            padding: 0 12px 0 38px;
            background: #f8fafc;
            border: 1px solid var(--border-color); /* Thinner border */
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background: #fff;
            border-color: var(--brand-orange);
            box-shadow: 0 0 0 3px rgba(235, 153, 80, 0.1);
        }

        select.form-control {
            appearance: none;
            cursor: pointer;
        }

        .form-control[type="file"] {
            padding-top: 4px;
            padding-left: 38px;
            cursor: pointer;
        }

        /* Captcha Row */
        .captcha-container {
            grid-column: span 3; 
            background: #fdfdfd;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 8px 15px; /* Minimized padding */
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 2px;
        }

        @media (max-width: 768px) {
            .captcha-container {
                grid-column: span 1;
                flex-direction: column;
                align-items: stretch;
            }
        }

        .captcha-box {
            background: var(--brand-blue);
            color: white;
            padding: 5px 12px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: 2px;
            font-style: italic;
            text-align: center;
            min-width: 90px;
            user-select: none;
        }

        .btn-pro {
            grid-column: span 3;
            width: 100%;
            height: 38px; /* Nano height */
            background-color: var(--brand-orange);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(235, 153, 80, 0.1);
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .btn-pro {
                grid-column: span 1;
            }
        }

        .btn-pro:hover {
            background-color: var(--brand-dark);
            box-shadow: 0 12px 25px rgba(235, 153, 80, 0.4);
            transform: translateY(-2px);
        }

        .footer-links {
            text-align: center;
            margin-top: 10px; /* Minimized margin */
        }

        .footer-links a {
            color: var(--brand-blue);
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--brand-orange);
        }

        /* Alerts */
        .alert-error {
            background: #fff5f5;
            color: #c53030;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 5px solid #f56565;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class="register-wrapper">
        <div class="login-card">
            
            <div class="logo-container">
                <img src="{{ asset('Pvtnew1.png') }}" alt="PV Travels">
            </div>

            <div class="login-header">
                <h1>Create My Account</h1>
                <p>Join our professional travel network today.</p>
            </div>

            @if($errors->any())
                <div class="alert-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div style="margin-bottom: 2px;">{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-grid">
                    <!-- Column 1 -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user input-icon"></i>
                            <input type="text" name="first_name" class="form-control" placeholder="First Name *" value="{{ old('first_name') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user-tag input-icon"></i>
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name *" value="{{ old('last_name') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <input type="email" name="email" class="form-control" placeholder="E-mail *" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-link input-icon"></i>
                            <input type="text" name="url" class="form-control" placeholder="URL" value="{{ old('url') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-globe input-icon"></i>
                            <select name="country" class="form-control" required>
                                <option value="">Select Country *</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->name }}" {{ old('country') == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-city input-icon"></i>
                            <input type="text" name="city" class="form-control" placeholder="City" value="{{ old('city') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-building input-icon"></i>
                            <input type="text" name="company" class="form-control" placeholder="Company Name" value="{{ old('company') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-mobile-screen input-icon"></i>
                            <input type="text" name="mobile" class="form-control" placeholder="Mobile" value="{{ old('mobile') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-phone input-icon"></i>
                            <input type="text" name="telephone" class="form-control" placeholder="Telephone" value="{{ old('telephone') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-fax input-icon"></i>
                            <input type="text" name="fax" class="form-control" placeholder="Fax" value="{{ old('fax') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-map-location-dot input-icon"></i>
                            <input type="text" name="address" class="form-control" placeholder="Address" value="{{ old('address') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-calendar-day input-icon"></i>
                            <input type="date" name="birth_day" class="form-control" placeholder="Birth Date" value="{{ old('birth_day') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-venus-mars input-icon"></i>
                            <select name="gender" class="form-control">
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-image input-icon"></i>
                            <input type="file" name="avatar" class="form-control" title="Choose Avatar">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" name="password" class="form-control" placeholder="Password *" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fa-solid fa-check-double input-icon"></i>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Retype Password *" required>
                        </div>
                    </div>

                    <!-- Captcha -->
                    <div class="captcha-container">
                        <div class="captcha-box">
                            {{ $captchaCode }}
                        </div>
                        <div style="flex: 1;">
                            <input type="text" name="captcha" class="form-control" placeholder="Enter code from the blue box" required style="padding-left: 20px;">
                        </div>
                    </div>

                    <button type="submit" class="btn-pro">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Create My Account</span>
                    </button>
                </div>

                <div class="footer-links">
                    <a href="{{ route('login') }}">
                        <i class="fa-solid fa-arrow-left"></i>
                        Already have an account? Login
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
