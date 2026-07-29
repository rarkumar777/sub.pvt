<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favpvt1.png') }}?v=1" />
    <title>Reset Password | PV Travels</title>
    
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
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 500px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            padding: 50px 45px;
            text-align: center;
            border: 1px solid #edf2f7;
        }

        .logo-container {
            margin-bottom: 25px;
        }

        .logo-container img {
            height: 60px;
        }

        .login-header {
            margin-bottom: 35px;
        }

        .login-header h1 {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .login-header p {
            color: var(--text-gray);
            font-size: 14px;
        }

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
        }

        .form-control {
            width: 100%;
            height: 52px;
            padding: 0 20px 0 60px !important;
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

        .btn-pro {
            width: 100%;
            height: 52px;
            background-color: var(--brand-orange);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
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
            margin-top: 10px;
        }

        .btn-pro:hover {
            background-color: var(--brand-dark);
            box-shadow: 0 12px 25px rgba(235, 153, 80, 0.4);
            transform: translateY(-2px);
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

    <div class="login-wrapper">
        <div class="login-card">
            
            <div class="logo-container">
                <img src="{{ asset('Pvtnew1.png') }}" alt="PV Travels">
            </div>

            <div class="login-header">
                <h1>Set New Password</h1>
                <p>Please enter your new password below.</p>
            </div>

            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $error }}
                    </div>
                @endforeach
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <i class="fa-solid fa-envelope input-icon"></i>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}"
                           class="form-control" placeholder="Email Address" required readonly>
                </div>

                <div class="form-group">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" 
                           placeholder="New Password" required autofocus>
                </div>

                <div class="form-group">
                    <i class="fa-solid fa-check-double input-icon"></i>
                    <input type="password" name="password_confirmation" class="form-control" 
                           placeholder="Confirm New Password" required>
                </div>

                <button type="submit" class="btn-pro">
                    <span>Update Password</span>
                    <i class="fa-solid fa-key"></i>
                </button>
            </form>
        </div>
    </div>

</body>
</html>
