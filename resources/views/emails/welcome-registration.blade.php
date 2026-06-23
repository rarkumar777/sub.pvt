<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#2c3e50; padding:30px 40px; text-align:center;">
                            <h1 style="color:#ffffff; margin:0; font-size:24px;">Welcome to {{ config('app.name') }}!</h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:16px; color:#333333; margin:0 0 20px;">
                                Dear <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,
                            </p>

                            <p style="font-size:15px; color:#555555; margin:0 0 20px; line-height:1.6;">
                                Thank you for registering with us! Your account has been created successfully. Below are your login details for your reference:
                            </p>

                            {{-- Login Details Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8f9fa; border:1px solid #e9ecef; border-radius:6px; margin:0 0 25px;">
                                <tr>
                                    <td style="padding:20px 25px;">
                                        <h3 style="margin:0 0 15px; color:#2c3e50; font-size:16px; border-bottom:1px solid #dee2e6; padding-bottom:10px;">
                                            Your Login Details
                                        </h3>
                                        <table cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding:5px 0; color:#666; font-size:14px; width:120px;"><strong>Email:</strong></td>
                                                <td style="padding:5px 0; color:#333; font-size:14px;">{{ $user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:5px 0; color:#666; font-size:14px;"><strong>Password:</strong></td>
                                                <td style="padding:5px 0; color:#333; font-size:14px;">{{ $plainPassword }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Login Button --}}
                            <table cellpadding="0" cellspacing="0" width="100%" style="margin:0 0 25px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $loginUrl }}" style="display:inline-block; background-color:#2c3e50; color:#ffffff; text-decoration:none; padding:12px 35px; border-radius:5px; font-size:15px; font-weight:bold;">
                                            Login to Your Account
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:13px; color:#999999; margin:0 0 10px; line-height:1.5;">
                                For security reasons, we recommend changing your password after your first login.
                            </p>

                            <p style="font-size:14px; color:#555555; margin:20px 0 0; line-height:1.6;">
                                If you did not create this account, please ignore this email.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#f8f9fa; padding:20px 40px; text-align:center; border-top:1px solid #e9ecef;">
                            <p style="font-size:12px; color:#999999; margin:0;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
