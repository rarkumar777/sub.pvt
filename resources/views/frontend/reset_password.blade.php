@extends('frontend.layout')
@section('title', 'Reset Password')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --brand-orange: #eb9950;
        --brand-dark-orange: #d35400;
        --brand-blue: #337ab7;
        --text-dark: #2d3436;
        --text-gray: #636e72;
    }
    #main-nav { background-color: #0f1729 !important; }
    footer { margin-top: 0 !important; }

    .rp-wrapper {
        font-family: 'Montserrat', sans-serif;
        background: #fdfdfd;
        padding: 20px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-image: radial-gradient(circle at 5% 5%, rgba(235,153,80,0.05) 0%, transparent 35%),
                          radial-gradient(circle at 95% 95%, rgba(51,122,183,0.05) 0%, transparent 35%);
    }
    .rp-card {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.5);
        border-radius: 30px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 500px;
        padding: 40px 50px;
        text-align: center;
    }
    .rp-icon {
        width: 60px; height: 60px;
        background: linear-gradient(135deg, #eb9950, #d35400);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 18px;
        box-shadow: 0 8px 20px rgba(235,153,80,0.3);
    }
    .rp-icon i { color: #fff; font-size: 24px; }
    .rp-title { font-size: 24px; font-weight: 700; color: var(--text-dark); margin-bottom: 8px; }
    .rp-subtitle { color: var(--text-gray); font-size: 14px; margin-bottom: 28px; line-height: 1.6; }
    .rp-input-wrap { position: relative; margin-bottom: 16px; }
    .rp-input-wrap i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--brand-orange); font-size: 16px; }
    .rp-input {
        width: 100%; height: 48px;
        padding: 0 18px 0 50px !important;
        border: 2px solid #edf2f7;
        border-radius: 12px;
        font-size: 15px; font-family: 'Montserrat', sans-serif;
        color: var(--text-dark);
        box-sizing: border-box;
        transition: border-color 0.2s;
        background: #fff;
    }
    .rp-input:focus { outline: none; border-color: var(--brand-orange); box-shadow: 0 0 0 4px rgba(235,153,80,0.1); }
    .rp-btn {
        width: 100%; height: 48px;
        background: linear-gradient(135deg, var(--brand-orange), var(--brand-dark-orange));
        color: #fff; border: none; border-radius: 12px;
        font-size: 15px; font-weight: 700; font-family: 'Montserrat', sans-serif;
        cursor: pointer; transition: all 0.3s;
        box-shadow: 0 8px 15px rgba(235,153,80,0.3);
        margin-bottom: 18px;
    }
    .rp-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(235,153,80,0.4); }
    .rp-back { font-size: 13px; font-weight: 600; color: var(--brand-blue); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .rp-back:hover { color: var(--brand-orange); }
    .rp-alert-error {
        background: #fff5f5; border-left: 4px solid #f56565; color: #c53030;
        border-radius: 10px; padding: 12px 15px;
        font-size: 13px; font-weight: 600; margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px; text-align: left;
    }
    .rp-label {
        display: block; text-align: left; font-size: 12px;
        font-weight: 600; color: var(--text-gray);
        margin-bottom: 6px; padding-left: 4px;
    }
    @media(max-width:600px) { .rp-card { padding: 30px 22px; margin: 0 10px; } }
</style>

<div class="rp-wrapper">
    <div class="rp-card">
        <div class="rp-icon"><i class="fa-solid fa-lock"></i></div>
        <div class="rp-title">Set New Password</div>
        <div class="rp-subtitle">Enter your new password below.</div>

        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="rp-alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $error }}
                </div>
            @endforeach
        @endif

        <form method="POST" action="{{ route('frontend.reset.update', ['lang' => $lang]) }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <label class="rp-label">Email Address</label>
            <div class="rp-input-wrap">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" class="rp-input" value="{{ $email }}" readonly style="background:#f9fafb; color:#9ca3af;">
            </div>

            <label class="rp-label">New Password</label>
            <div class="rp-input-wrap">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" class="rp-input" placeholder="Enter new password (min 4 chars)" required>
            </div>

            <label class="rp-label">Confirm New Password</label>
            <div class="rp-input-wrap">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password_confirmation" class="rp-input" placeholder="Confirm new password" required>
            </div>

            <button type="submit" class="rp-btn">
                <i class="fa-solid fa-check" style="margin-right:8px;"></i> Reset Password
            </button>
        </form>

        <a href="/{{ $lang }}/users/login/" class="rp-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Login
        </a>
    </div>
</div>
@endsection
