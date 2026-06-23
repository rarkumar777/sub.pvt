<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Request Confirmation</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:#f97316; padding:30px 40px; text-align:center;">
                            <h1 style="color:#ffffff; margin:0; font-size:24px;">✈️ Trip Request Received!</h1>
                            <p style="color:#ffe4cc; margin:8px 0 0; font-size:14px;">{{ config('app.name') }}</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:40px;">
                            <p style="font-size:16px; color:#333333; margin:0 0 20px;">
                                Dear <strong>{{ $firstName }} {{ $lastName }}</strong>,
                            </p>

                            <p style="font-size:15px; color:#555555; margin:0 0 20px; line-height:1.6;">
                                Thank you for submitting your trip request to <strong>Jordan</strong>! 🌍<br>
                                Our local agency has received your request and will contact you shortly to start planning your perfect trip.
                            </p>

                            {{-- Trip Summary Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fff7ed; border:1px solid #fed7aa; border-radius:8px; margin:0 0 25px;">
                                <tr>
                                    <td style="padding:20px 25px;">
                                        <h3 style="margin:0 0 15px; color:#ea580c; font-size:16px; border-bottom:1px solid #fed7aa; padding-bottom:10px;">
                                            📋 Your Trip Summary
                                        </h3>
                                        <table cellpadding="0" cellspacing="0" width="100%">
                                            @if(!empty($tripData['participant_type']))
                                            <tr>
                                                <td style="padding:5px 0; color:#666; font-size:14px; width:160px;"><strong>Travelling with:</strong></td>
                                                <td style="padding:5px 0; color:#333; font-size:14px;">{{ $tripData['participant_type'] }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($tripData['adults']))
                                            <tr>
                                                <td style="padding:5px 0; color:#666; font-size:14px;"><strong>Adults:</strong></td>
                                                <td style="padding:5px 0; color:#333; font-size:14px;">{{ $tripData['adults'] }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($tripData['children']))
                                            <tr>
                                                <td style="padding:5px 0; color:#666; font-size:14px;"><strong>Children:</strong></td>
                                                <td style="padding:5px 0; color:#333; font-size:14px;">{{ $tripData['children'] }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($tripData['departure_date']))
                                            <tr>
                                                <td style="padding:5px 0; color:#666; font-size:14px;"><strong>Departure:</strong></td>
                                                <td style="padding:5px 0; color:#333; font-size:14px;">{{ $tripData['departure_date'] }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($tripData['return_date']))
                                            <tr>
                                                <td style="padding:5px 0; color:#666; font-size:14px;"><strong>Return:</strong></td>
                                                <td style="padding:5px 0; color:#333; font-size:14px;">{{ $tripData['return_date'] }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($tripData['departure_period']))
                                            <tr>
                                                <td style="padding:5px 0; color:#666; font-size:14px;"><strong>Travel Period:</strong></td>
                                                <td style="padding:5px 0; color:#333; font-size:14px;">{{ $tripData['departure_period'] }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($tripData['guide_type']))
                                            <tr>
                                                <td style="padding:5px 0; color:#666; font-size:14px;"><strong>Guide:</strong></td>
                                                <td style="padding:5px 0; color:#333; font-size:14px;">{{ ucwords(str_replace('-', ' ', $tripData['guide_type'])) }}</td>
                                            </tr>
                                            @endif
                                            @if(!empty($tripData['ideal_budget']))
                                            <tr>
                                                <td style="padding:5px 0; color:#666; font-size:14px;"><strong>Budget (ideal):</strong></td>
                                                <td style="padding:5px 0; color:#333; font-size:14px;">${{ number_format($tripData['ideal_budget']) }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:15px; color:#555555; margin:0 0 25px; line-height:1.6;">
                                Our team will review your request and get back to you within <strong>24-48 hours</strong> with a personalized itinerary and quote.
                            </p>

                            <p style="font-size:14px; color:#555555; margin:20px 0 0; line-height:1.6;">
                                If you have any questions, feel free to contact us.<br><br>
                                Warm regards,<br>
                                <strong>{{ config('app.name') }} Team</strong>
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
