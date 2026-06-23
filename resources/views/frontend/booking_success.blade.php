@extends('frontend.layout')
@section('title', 'Booking Confirmed - PV Travels')

@section('content')
<div id="main-contents">

    {{-- Hero Success Banner --}}
    <div style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); padding: 80px 20px 60px; text-align: center; position: relative; overflow: hidden;">
        {{-- Animated background circles --}}
        <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: rgba(16,185,129,0.1); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -30px; right: -30px; width: 150px; height: 150px; background: rgba(255,102,0,0.1); border-radius: 50%;"></div>

        {{-- Success checkmark --}}
        <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #10b981, #34d399); border-radius: 50%; margin: 0 auto 30px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 40px rgba(16,185,129,0.3); animation: bounceIn 0.6s;">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>

        <h1 style="color: #fff; font-size: 42px; font-weight: 800; margin: 0 0 5px; font-family: 'Inter', 'Open Sans Condensed', sans-serif;">
            Request <span style="color: #10b981;">Received!</span>
        </h1>
        <p style="color: #34d399; font-size: 18px; margin: 10px 0 0; font-weight: 500;">
            Thank you for choosing. Your booking request has been submitted successfully.
        </p>
    </div>

    {{-- Booking Details Card --}}
    <div style="max-width: 700px; margin: -30px auto 40px; padding: 0 20px; position: relative; z-index: 10;">
        <div style="background: #fff; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); overflow: hidden;">

            {{-- PIN & Total Row --}}
            <div style="display: flex; border-bottom: 1px solid #f0f0f0;">
                <div style="flex: 1; padding: 35px 20px; text-align: center; border-right: 1px solid #f0f0f0;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 12px;">Request PIN</div>
                    <div style="font-size: 32px; font-weight: 800; color: #1a1a2e; border: 2px solid #e2e8f0; border-radius: 12px; padding: 12px 20px; display: inline-block; font-family: 'Inter', monospace;">
                        #BK-{{ $booking->id }}
                    </div>
                </div>
                <div style="flex: 1; padding: 35px 20px; text-align: center;">
                    <div style="font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: 0.15em; text-transform: uppercase; margin-bottom: 12px;">Invoice Total</div>
                    <div style="font-size: 32px; font-weight: 800; color: #1a1a2e; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-radius: 12px; padding: 12px 20px; display: inline-block; font-family: 'Inter', monospace;">
                        ${{ number_format($invoice->total, 2) }}
                    </div>
                </div>
            </div>

            {{-- Booking Summary --}}
            <div style="padding: 25px 30px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1a1a2e; margin: 0 0 18px; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ff6600" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    Booking Summary
                </h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px 0; color: #64748b; font-size: 14px;">Tour</td>
                        <td style="padding: 10px 0; color: #1a1a2e; font-weight: 600; text-align: right; font-size: 14px;">{{ $tourTitle }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px 0; color: #64748b; font-size: 14px;">Travel Date</td>
                        <td style="padding: 10px 0; color: #1a1a2e; font-weight: 600; text-align: right; font-size: 14px;">{{ $booking->travel_date }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px 0; color: #64748b; font-size: 14px;">Duration</td>
                        <td style="padding: 10px 0; color: #1a1a2e; font-weight: 600; text-align: right; font-size: 14px;">{{ $booking->days }} Days / {{ $booking->nights }} Nights</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px 0; color: #64748b; font-size: 14px;">Travelers</td>
                        <td style="padding: 10px 0; color: #1a1a2e; font-weight: 600; text-align: right; font-size: 14px;">
                            {{ $booking->adult }} Adult{{ $booking->adult > 1 ? 's' : '' }}
                            @if($booking->child > 0), {{ $booking->child }} Child{{ $booking->child > 1 ? 'ren' : '' }}@endif
                            @if($booking->infant > 0), {{ $booking->infant }} Infant{{ $booking->infant > 1 ? 's' : '' }}@endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #64748b; font-size: 14px;">Invoice #</td>
                        <td style="padding: 10px 0; color: #1a1a2e; font-weight: 600; text-align: right; font-size: 14px;">#{{ $invoice->id }}</td>
                    </tr>
                </table>
            </div>

            {{-- Action Buttons --}}
            <div style="padding: 0 30px 30px; display: flex; gap: 12px;">
                <a href="/{{ $lang }}/invoice/{{ $invoice->id }}/" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 16px; background: linear-gradient(135deg, #ff6600, #ff8533); color: #fff; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 15px; transition: all 0.3s; box-shadow: 0 4px 15px rgba(255,102,0,0.3);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                    Pay Now
                </a>
                <a href="/{{ $lang }}/" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 16px; background: #f1f5f9; color: #475569; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 15px; transition: all 0.3s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    {{-- Info Note --}}
    <div style="max-width: 700px; margin: 0 auto 60px; padding: 0 20px;">
        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 16px 20px; display: flex; align-items: flex-start; gap: 12px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div style="font-size: 13px; color: #92400e; line-height: 1.5;">
                <strong>Important:</strong> Please complete the payment to confirm your booking. Your reservation will be held for 48 hours. After that, it may be released.
            </div>
        </div>
    </div>
</div>

<style>
@keyframes bounceIn {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); opacity: 1; }
}
</style>
@endsection
