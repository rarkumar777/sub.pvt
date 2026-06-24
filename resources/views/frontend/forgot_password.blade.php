@extends('frontend.layout')
@section('title', 'Forgot Password')

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

    .fp-wrapper {
        font-family: 'Montserrat', sans-serif;
        background: #fdfdfd;
        padding: 20px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background-image: radial-gradient(circle at 5% 5%, rgba(235,153,80,0.05) 0%, transparent 35%),
                          radial-gradient(circle at 95% 95%, rgba(51,122,183,0.05) 0%, transparent 35%);
    }
    .fp-card {
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
    .fp-icon {
        width: 60px; height: 60px;
        background: linear-gradient(135deg, #eb9950, #d35400);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 18px;
        box-shadow: 0 8px 20px rgba(235,153,80,0.3);
    }
    .fp-icon i { color: #fff; font-size: 24px; }
    .fp-title { font-size: 24px; font-weight: 700; color: var(--text-dark); margin-bottom: 8px; }
    .fp-subtitle { color: var(--text-gray); font-size: 14px; margin-bottom: 28px; line-height: 1.6; }
    .fp-input-wrap { position: relative; margin-bottom: 18px; }
    .fp-input-wrap i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--brand-orange); font-size: 16px; }
    .fp-input {
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
    .fp-input:focus { outline: none; border-color: var(--brand-orange); box-shadow: 0 0 0 4px rgba(235,153,80,0.1); }
    .fp-btn {
        width: 100%; height: 48px;
        background: linear-gradient(135deg, var(--brand-orange), var(--brand-dark-orange));
        color: #fff; border: none; border-radius: 12px;
        font-size: 15px; font-weight: 700; font-family: 'Montserrat', sans-serif;
        cursor: pointer; transition: all 0.3s;
        box-shadow: 0 8px 15px rgba(235,153,80,0.3);
        margin-bottom: 18px;
    }
    .fp-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(235,153,80,0.4); }
    .fp-back { font-size: 13px; font-weight: 600; color: var(--brand-blue); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .fp-back:hover { color: var(--brand-orange); }
    .fp-alert-success {
        background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;
        border-radius: 10px; padding: 14px 18px;
        font-size: 14px; font-weight: 600; margin-bottom: 20px;
        display: flex; align-items: center; gap: 10px; text-align: left;
    }
    .fp-alert-error {
        background: #fff5f5; border-left: 4px solid #f56565; color: #c53030;
        border-radius: 10px; padding: 12px 15px;
        font-size: 13px; font-weight: 600; margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px; text-align: left;
    }
    @media(max-width:600px) { .fp-card { padding: 30px 22px; margin: 0 10px; } }
</style>

<div class="fp-wrapper">
    <div class="fp-card">
        <div class="fp-icon"><i class="fa-solid fa-key"></i></div>
        <div class="fp-title">Forgot Password?</div>
        <div class="fp-subtitle">Enter your email address and we'll send you a link to reset your password.</div>

        @if(session('status'))
            <div class="fp-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="fp-alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $error }}
                </div>
            @endforeach
        @endif

        @if(!session('status'))
        <form method="POST" action="{{ route('frontend.forgot.send', ['lang' => $lang]) }}">
            @csrf
            <div class="fp-input-wrap">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" class="fp-input" placeholder="Enter your email address" value="{{ old('email') }}" required autofocus>
            </div>
            <button type="submit" class="fp-btn">
                <i class="fa-solid fa-paper-plane" style="margin-right:8px;"></i> Send Reset Link
            </button>
        </form>
        @endif

        <a href="/{{ $lang }}/users/login/" class="fp-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Login
        </a>
    </div>
</div>
@endsection
