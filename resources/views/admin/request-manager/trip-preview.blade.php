<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trip Preview - {{ $tripRequest->first_name }} {{ $tripRequest->last_name }}</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@php
    $coverImg = '';
    if($itinerary && $itinerary->cover_photo) $coverImg = $itinerary->cover_photo;
    if(!$coverImg) $coverImg = '/uploads/filemanager/Photos/Petra/petra_night.jpg';
@endphp
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:#fff;color:#333;}
a{text-decoration:none;}
.hero{display:flex;min-height:100vh;position:relative;}
.hero-left{flex:0 0 38%;background:#f97316;display:flex;flex-direction:column;justify-content:center;padding:60px 50px;position:relative;z-index:2;}
.hero-left::after{content:'';position:absolute;top:0;right:-40px;width:80px;height:100%;background:linear-gradient(90deg,#f97316 0%,transparent 100%);z-index:1;}
.hero-subtitle{font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:3px;color:#e8d48b;margin-bottom:8px;}
.hero-destination{font-family:'Playfair Display',serif;font-size:48px;font-weight:700;font-style:italic;color:#fff;margin-bottom:16px;line-height:1.1;}
.hero-for{font-size:14px;font-weight:600;color:#fff;margin-bottom:24px;}
.hero-divider{width:40px;height:2px;background:#e8d48b;margin-bottom:24px;}
.hero-tagline{font-size:14px;color:rgba(255,255,255,.85);line-height:1.7;margin-bottom:32px;}
.hero-agent{display:flex;align-items:center;gap:12px;margin-bottom:32px;}
.hero-agent-avatar{width:42px;height:42px;border-radius:50%;background:#e25822;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:16px;border:2px solid rgba(255,255,255,.3);}
.hero-agent-text{font-size:13px;color:rgba(255,255,255,.9);font-weight:500;}
.hero-agent-text strong{color:#fff;}
.hero-cta{display:inline-block;background:#e8d48b;color:#f97316;padding:14px 32px;border-radius:6px;font-size:14px;font-weight:700;border:2px solid #e8d48b;transition:all .2s;cursor:pointer;}
.hero-cta:hover{background:transparent;color:#e8d48b;}
.hero-right{flex:1;position:relative;overflow:hidden;}
.hero-right img{width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;}
.hero-logo{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:2;}
.hero-logo span{font-family:'Playfair Display',serif;font-size:48px;font-weight:700;color:#fff;text-shadow:0 2px 20px rgba(0,0,0,.4);letter-spacing:2px;}
.back-btn{position:fixed;top:20px;left:20px;z-index:100;background:rgba(0,0,0,.5);color:#fff;border:none;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;backdrop-filter:blur(8px);display:flex;align-items:center;gap:6px;transition:all .2s;}
.back-btn:hover{background:rgba(0,0,0,.7);color:#fff;}
@media(max-width:768px){.hero{flex-direction:column;}.hero-left{flex:none;padding:40px 24px;}.hero-left::after{display:none;}.hero-right{min-height:50vh;}.hero-destination{font-size:36px;}}
</style>
</head>
<body>
<a href="/admin/request-manager/{{ $tripRequest->id }}/trip-planner" class="back-btn"><i class="fa fa-arrow-left"></i> Back to Trip Planner</a>
<section class="hero">
    <div class="hero-left">
        <div class="hero-subtitle">YOUR TRIP IN</div>
        <div class="hero-destination">Jordan</div>
        <div class="hero-for">for {{ $tripRequest->first_name }} {{ strtoupper($tripRequest->last_name ?? '') }}</div>
        <div class="hero-divider"></div>
        <div class="hero-tagline">A tailor-made itinerary, designed for you.<br>For a journey that is more authentic and unforgettable.</div>
        <div class="hero-agent">
            <div class="hero-agent-avatar">{{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}</div>
            <div class="hero-agent-text">Co-created with local agency <strong>{{ $user->name ?? 'Admin' }}</strong></div>
        </div>
        <a href="/admin/request-manager/{{ $tripRequest->id }}/trip-planner/preview/my-trip" class="hero-cta">Discover my quote</a>
    </div>
    <div class="hero-right">
        <img src="{{ $coverImg }}" alt="Jordan">
        <div class="hero-logo"><span>PVT Travels</span></div>
    </div>
</section>
</body>
</html>
