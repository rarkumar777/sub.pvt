<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $itinerary->title ?? ($tripRequest->first_name."'s Trip") }}</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@php
  $totalPax = max(1, ($tripRequest->adults??0)+($tripRequest->children??0));
  $dayCount = ($itinerary&&$itinerary->days)?$itinerary->days->count():0;
  $groupTotal = $itinerary ? floatval($itinerary->group_total ?? 0) : 0;
  // If group_total not set, calculate from service costs
  if($groupTotal <= 0 && $itinerary && $itinerary->days){
    foreach($itinerary->days as $dd){
      $svcs = $dd->services;
      if(is_string($svcs)) $svcs = json_decode($svcs, true);
      if(is_array($svcs)){
        foreach($svcs as $svc){
          $cost = floatval($svc['cost'] ?? 0);
          $qty  = intval($svc['qty'] ?? 1);
          $days = intval($svc['stay_duration'] ?? 1);
          $groupTotal += $cost * max(1,$qty) * max(1,$days);
        }
      }
    }
  }
  $currency = $tripRequest->currency??'USD';
  $sym = $currency=='EUR'?'€':($currency=='JOD'?'JOD ':'$');
  $depDate = $tripRequest->departure_date?\Carbon\Carbon::parse($tripRequest->departure_date):null;
  $retDate = $tripRequest->return_date?\Carbon\Carbon::parse($tripRequest->return_date):null;
  $dateRange = ($depDate&&$retDate)?$depDate->format('d M').' – '.$retDate->format('d M Y'):'Flexible';
  $itinTitle = $itinerary?($itinerary->title?:'Trip to Jordan for '.$dayCount.' '.($dayCount==1?'day':'days')):'Your Trip to Jordan';
  $clientName = trim(($tripRequest->first_name??'').' '.($tripRequest->last_name??''));
  $agentName = $user->name??'Admin';
  $agentInitial = strtoupper(substr($agentName,0,1));

  // Helper: resolve photo URL correctly for all path types
  $resolvePhotoUrl = function($photo) {
    if(!$photo) return null;
    // Encode spaces in URL for browser compatibility
    $encode = function($url) { return str_replace(' ', '%20', $url); };
    // Already a full URL
    if(Str::startsWith($photo, 'http')) return $encode($photo);
    // Paths in /storage/ or storage/
    if(Str::startsWith($photo, '/storage/') || Str::startsWith($photo, 'storage/')) {
      return $encode(asset($photo));
    }
    // Paths in /uploads/ (public/uploads/)
    if(Str::startsWith($photo, '/uploads/') || Str::startsWith($photo, 'uploads/')) {
      return $encode(asset($photo));
    }
    // Paths starting with / (other public paths)
    if(Str::startsWith($photo, '/')) {
      return $encode(asset($photo));
    }
    // Default: assume it's in storage
    return $encode(asset('storage/' . ltrim($photo, '/')));
  };

  // collect all photos from all days
  $allPhotos = [];
  if($itinerary&&$itinerary->days){
    foreach($itinerary->days->sortBy('day_number') as $d){
      $ph=$d->photos; if(is_string($ph))$ph=json_decode($ph,true);
      if(is_array($ph)) foreach($ph as $p) $allPhotos[]=$p;
    }
  }
  // collect accommodations
  $accoms=[];
  if($itinerary&&$itinerary->days){
    foreach($itinerary->days->sortBy('day_number') as $d){
      if($d->accommodation_name) $accoms[]=[ 'day'=>$d->day_number,'name'=>$d->accommodation_name,'desc'=>$d->accommodation_description,'cat'=>$d->accommodation_category,'stars'=>$d->accommodation_stars ];
    }
  }
@endphp
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;background:#fff;color:#333}
a{text-decoration:none;color:inherit}
/* NAV */
.bt-nav{position:sticky;top:0;z-index:10000;background:#f97316;height:52px;display:flex;align-items:center;justify-content:space-between;padding:0 24px;box-shadow:0 2px 8px rgba(0,0,0,.15)}
.bt-nav-left{display:flex;align-items:center;gap:0;height:100%}
.bt-brand{font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:#fff;margin-right:24px;white-space:nowrap}
.bt-links{display:flex;align-items:center;height:100%;gap:0;overflow-x:auto}
.bt-links a{color:rgba(255,255,255,.85);font-size:12px;font-weight:500;padding:0 12px;height:100%;display:flex;align-items:center;border-bottom:3px solid transparent;white-space:nowrap;transition:.15s}
.bt-links a:hover,.bt-links a.active{color:#fff;border-bottom-color:#ffffff}
.bt-nav-right{display:flex;align-items:center;gap:16px;flex-shrink:0}
.bt-nav-price{color:#fff;font-size:14px;font-weight:700}
.bt-nav-price small{font-size:10px;color:rgba(255,255,255,.7);font-weight:400;margin-left:3px}
.bt-nav-detail{color:#fff;font-size:11px;font-weight:600;text-decoration:underline}
.bt-btn-pdf{background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);padding:5px 12px;border-radius:4px;font-size:11px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px}
/* HERO */
.bt-hero{position:relative;width:100%;height:100vh;overflow:hidden}
.bt-hero img.hero-cover{width:100%;height:100%;object-fit:cover;position:absolute;inset:0}
.bt-hero-video{position:absolute;inset:0;width:100%;height:100%;overflow:hidden;pointer-events:none}
.bt-hero-video video{position:absolute;top:50%;left:50%;width:120%;height:120%;min-width:100%;min-height:100%;transform:translate(-50%,-50%);object-fit:cover;border:0}
.bt-hero-video iframe{position:absolute;top:50%;left:50%;width:177.78vh;height:100vh;min-width:100%;min-height:56.25vw;transform:translate(-50%,-50%);border:0;pointer-events:none}
.bt-hero-overlay{position:absolute;inset:0;background:linear-gradient(transparent 20%,rgba(0,0,0,.55));z-index:2}
.bt-hero-content{position:absolute;bottom:80px;left:0;right:0;padding:0 48px;color:#fff;z-index:3;text-align:center}
.bt-hero-sub{font-size:14px;color:rgba(255,255,255,.8);font-weight:500;margin-bottom:12px;text-transform:uppercase;letter-spacing:2px}
.bt-hero h1{font-family:'Playfair Display',serif;font-size:52px;font-weight:700;line-height:1.2;margin-bottom:18px;text-shadow:0 2px 12px rgba(0,0,0,.4)}
.bt-hero-meta{display:flex;gap:24px;flex-wrap:wrap;font-size:14px;color:rgba(255,255,255,.85);justify-content:center}
.bt-hero-meta span{display:flex;align-items:center;gap:6px}
.bt-hero-scroll{position:absolute;bottom:24px;left:50%;transform:translateX(-50%);z-index:3;color:#fff;font-size:28px;animation:heroFloat 2s ease-in-out infinite}
@keyframes heroFloat{0%,100%{transform:translateX(-50%) translateY(0)}50%{transform:translateX(-50%) translateY(-8px)}}
.bt-no-hero{background:linear-gradient(135deg,#f97316,#ff9f43);height:240px;display:flex;align-items:center;justify-content:center;text-align:center;padding:48px;color:#fff}
.bt-no-hero h1{font-family:'Playfair Display',serif;font-size:36px;font-weight:700;font-style:italic}
/* CONTAINER */
.bt-wrap{max-width:1040px;margin:0 auto;padding:0 32px}
/* SECTIONS */
.bt-section{padding:56px 0}
.bt-section-title{font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:#1a1a1a;margin-bottom:28px}
/* ADVISOR */
.bt-advisor{display:flex;gap:48px;align-items:flex-start;padding:48px 0}
.bt-advisor-text{flex:1}
.bt-advisor-text p{font-size:14px;color:#555;line-height:1.8}
.bt-advisor-card{background:#f9f8f5;border:1px solid #e5e7eb;border-radius:10px;padding:24px;width:280px;flex-shrink:0}
.bt-advisor-card .lbl{font-size:12px;color:#888;font-style:italic;margin-bottom:14px}
.bt-advisor-body{display:flex;gap:14px;align-items:flex-start}
.bt-avatar{width:60px;height:60px;border-radius:50%;background:#f97316;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;flex-shrink:0;overflow:hidden}
.bt-avatar img{width:100%;height:100%;object-fit:cover}
.bt-advisor-info h4{font-size:15px;font-weight:700;color:#1a1a1a;margin-bottom:3px}
.bt-advisor-info p{font-size:12px;color:#666;line-height:1.7}
.bt-stars{color:#e8b445;font-size:12px;margin-top:5px}
/* PRESENTATION */
.bt-pres p{font-size:15px;color:#444;line-height:1.9;margin-bottom:16px}
/* PHOTOS */
.bt-gallery{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:8px}
.bt-gallery img{width:100%;height:200px;object-fit:cover;border-radius:8px}
/* HIGHLIGHTS */
.bt-highlights{list-style:none;padding:0;display:flex;flex-direction:column;gap:10px}
.bt-highlights li{display:flex;align-items:center;gap:10px;font-size:14px;color:#333;font-weight:500}
.bt-highlights li::before{content:'✔️';font-size:14px;flex-shrink:0}
/* BRIEF ITINERARY */
.bt-brief{display:flex;gap:0;position:relative;z-index:1;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb}
.bt-brief-map{flex:1.5;min-height:560px;position:relative;z-index:1}
#tripMap{width:100%;height:100%;min-height:560px}
.bt-brief-list{width:280px;flex-shrink:0;padding:28px 24px;background:#fff;overflow-y:auto;max-height:560px}
.bt-brief-list h3{font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#1a1a1a;margin-bottom:24px}
.bt-jday{display:flex;gap:12px;align-items:flex-start;position:relative;padding-bottom:6px;margin-bottom:6px}
.bt-jday:last-child{padding-bottom:0;margin-bottom:0}
.bt-jday-lbl{font-size:13px;font-weight:700;color:#f97316}
.bt-jday-dest{font-size:11px;color:#888;margin-top:1px;display:flex;align-items:center;gap:4px}
.bt-jday-dest i{font-size:10px;color:#f97316}
.map-marker{width:32px;height:32px;border-radius:50%;background:#f97316;color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;border:3px solid #fff;box-shadow:0 2px 12px rgba(249,115,22,.5),0 1px 4px rgba(0,0,0,.2);position:relative}
.map-marker::after{content:'';position:absolute;inset:-4px;border-radius:50%;border:2px solid rgba(249,115,22,.25)}
/* DAY BY DAY */
.bt-day-block{margin-bottom:48px}
.bt-day-hero{position:relative;width:100%;height:260px;border-radius:12px;overflow:hidden;margin-bottom:18px}
.bt-day-hero img{width:100%;height:100%;object-fit:cover}
.bt-day-hero-overlay{position:absolute;bottom:0;left:0;right:0;padding:18px 22px;background:linear-gradient(transparent,rgba(0,0,0,.65))}
.bt-day-num{font-size:11px;font-weight:700;color:#e8d48b;text-transform:uppercase;letter-spacing:1px}
.bt-day-title{font-family:'Playfair Display',serif;font-size:20px;color:#fff;font-weight:700;margin-top:3px}
/* Date Badge */
.bt-date-badge{position:absolute;top:16px;right:16px;background:rgba(255,255,255,.95);border-radius:10px;padding:8px 14px;text-align:center;box-shadow:0 4px 16px rgba(0,0,0,.2);min-width:64px;z-index:2}
.bt-date-badge .db-day{font-size:28px;font-weight:800;color:#f97316;line-height:1}
.bt-date-badge .db-month{font-size:11px;font-weight:700;color:#333;text-transform:uppercase;letter-spacing:1px;margin-top:2px}
.bt-date-badge .db-year{font-size:10px;font-weight:500;color:#888;margin-top:1px}
.bt-day-nophoto{background:#f3f4f6;border-radius:12px;height:140px;display:flex;align-items:center;justify-content:center;color:#aaa;margin-bottom:18px}
.bt-day-body{padding:0 6px}
.bt-day-loc{display:inline-flex;align-items:center;gap:5px;background:#f0f0f0;color:#555;font-size:11px;font-weight:700;padding:4px 10px;border-radius:4px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px}
.bt-day-head{font-size:17px;font-weight:700;color:#f97316;margin-bottom:10px}
.bt-day-desc{font-size:14px;color:#555;line-height:1.8;margin-bottom:14px}
.bt-meals{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:12px}
.bt-meal{display:flex;align-items:center;gap:5px;font-size:13px;color:#f97316;font-weight:600}
.bt-day-photos-extra{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px}
.bt-day-photos-extra img{width:130px;height:88px;border-radius:8px;object-fit:cover}
/* ACCOM */
.bt-accom-card{background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;margin-bottom:20px}
.bt-accom-hdr{font-size:11px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:.5px;padding:10px 16px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:6px}
.bt-accom-body{display:flex;gap:16px;padding:16px;align-items:flex-start}
.bt-accom-icon{width:72px;height:56px;border-radius:8px;background:#e5e7eb;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:20px;flex-shrink:0}
.bt-accom-name{font-size:14px;font-weight:700;color:#1a1a1a;margin-bottom:4px}
.bt-accom-sub{font-size:12px;color:#888;line-height:1.6}
/* PRICING */
.bt-price-box{background:#f97316;border-radius:12px;padding:32px;color:#fff;display:flex;align-items:center;justify-content:space-between;margin-bottom:32px}
.bt-price-box .lbl{font-size:13px;color:rgba(255,255,255,.7)}
.bt-price-box .amt{font-family:'Playfair Display',serif;font-size:38px;font-weight:700}
.bt-price-grid{display:grid;grid-template-columns:1fr 1fr;gap:32px;margin-bottom:24px}
.bt-price-block h4{font-size:14px;font-weight:700;color:#1a1a1a;margin-bottom:12px;display:flex;align-items:center;gap:7px}
.bt-price-block h4 i{color:#f97316}
.bt-price-block ul{list-style:none;padding:0}
.bt-price-block ul li{font-size:13px;color:#555;line-height:1.8;padding-left:14px;position:relative}
.bt-price-block ul li::before{content:'•';position:absolute;left:0;color:#f97316;font-weight:700}
.bt-price-block.exc ul li::before{color:#c0392b}
.bt-conds{background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:18px 22px;margin-bottom:16px}
.bt-conds h4{font-size:13px;font-weight:700;color:#1a1a1a;margin-bottom:6px;display:flex;align-items:center;gap:6px}
.bt-conds p{font-size:13px;color:#555;line-height:1.8}
/* RESERVATION */
.bt-reservation{background:#fff7ed;border-radius:16px;padding:40px;text-align:center}
.bt-reservation h2{font-family:'Playfair Display',serif;font-size:26px;font-weight:700;color:#1a1a1a;margin-bottom:12px}
.bt-reservation p{font-size:14px;color:#555;line-height:1.7;margin-bottom:24px}
.bt-pay-btn{display:inline-flex;align-items:center;gap:8px;background:#f97316;color:#fff;padding:14px 36px;border-radius:8px;font-size:15px;font-weight:700;box-shadow:0 4px 16px rgba(249,115,22,.3);transition:.2s}
.bt-pay-btn:hover{background:#e25822;transform:translateY(-1px)}
/* FOOTER */
.bt-footer{background:#333;padding:40px 24px;text-align:center;margin-top:64px}
.bt-footer .brand{font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:8px}
.bt-footer p{font-size:12px;color:rgba(255,255,255,.7);line-height:1.7;max-width:600px;margin:0 auto}
/* DIVIDER */
.bt-divider{width:100%;height:1px;background:#e5e7eb;margin:0}
/* VIDEO */
.bt-video-wrap{position:relative;padding-top:56.25%;border-radius:12px;overflow:hidden;background:#000}
.bt-video-wrap iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
.bt-video-thumb{display:none;position:relative;width:100%;border-radius:12px;overflow:hidden}
.bt-video-thumb img{width:100%;height:auto;display:block;border-radius:12px}
.bt-video-thumb .play-overlay{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.35)}
.bt-video-thumb .play-overlay i{font-size:48px;color:#fff;opacity:.9}
/* SERVICES in day */
.bt-svc-list{list-style:none;padding:0;margin:10px 0 0 0;display:flex;flex-direction:column;gap:6px}
.bt-svc-item{display:flex;align-items:center;gap:8px;font-size:13px;color:#444;padding:8px 12px;background:#f0f4f8;border-radius:6px;border-left:3px solid #f97316}
.bt-svc-item i{color:#f97316;font-size:14px;flex-shrink:0;width:18px;text-align:center}
.bt-svc-item .svc-name{flex:1;font-weight:500}

@media print{
  .bt-hero-video{display:none!important}
  .bt-hero img.hero-cover{display:block!important}
  .bt-nav{position:static!important}
}
@media(max-width:768px){
  .bt-hero h1{font-size:26px}.bt-hero-content{padding:0 20px;bottom:24px}
  .bt-brief{flex-direction:column}.bt-brief-list{width:100%}
  .bt-advisor{flex-direction:column}.bt-advisor-card{width:100%}
  .bt-price-grid{grid-template-columns:1fr}.bt-gallery{grid-template-columns:repeat(2,1fr)}
  .bt-links a{font-size:11px;padding:0 8px}
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="bt-nav">
  <div class="bt-nav-left">
    <span class="bt-brand">{{ config('app.name','PVT Travels') }}</span>
    <div class="bt-links">
      <a href="#advisor">Your advisor</a>
      <a href="#presentation">Presentation</a>
      @if(count($allPhotos)>0)<a href="#photos">Photos</a>@endif
      <a href="#highlights">Key points</a>
      <a href="#itinerary">Brief itinerary</a>
      <a href="#daybyay">Day by day</a>
      @if(count($accoms)>0)<a href="#accommodations">Accommodations</a>@endif
      <a href="#pricing">Pricing</a>
      <a href="#reservation">Reservation</a>
    </div>
  </div>
  <div class="bt-nav-right">
    @if($groupTotal>0)
    <div>
      <div class="bt-nav-price">{!! $sym !!}{{ number_format($groupTotal,0) }} <small>total</small></div>
      @if($totalPax>1)<div class="bt-nav-detail">{!! $sym !!}{{ number_format($groupTotal/$totalPax,0) }} / person</div>@endif
    </div>
    @endif
    <button class="bt-btn-pdf" onclick="window.print()"><i class="fa fa-download"></i> PDF</button>
  </div>
</nav>

<!-- HERO with video background -->
@php
  $heroVideoId  = null;
  $heroVideoEmbed = null;
  $heroMp4 = 'https://pvt.jo/theme/pvt/video/Marvelous-Jordan-5.mp4'; // default background video
  if($itinerary && $itinerary->video_url) {
    $vurl = $itinerary->video_url;
    if(preg_match('/\.mp4(\?.*)?$/i', $vurl)) {
      $heroMp4 = $vurl;
    } elseif(preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/', $vurl, $hvm)){
      $heroMp4 = null;
      $heroVideoId  = $hvm[1];
      $heroVideoEmbed = 'https://www.youtube.com/embed/'.$hvm[1].'?autoplay=1&mute=1&loop=1&controls=0&showinfo=0&modestbranding=1&rel=0&playlist='.$hvm[1];
    }
  }
@endphp
<div class="bt-hero">
  @if($heroMp4)
  <div class="bt-hero-video">
    <video id="heroVideo" autoplay muted loop playsinline preload="auto">
      <source src="{{ $heroMp4 }}" type="video/mp4">
    </video>
  </div>
  @elseif($heroVideoId)
  <div class="bt-hero-video">
    <iframe src="{{ $heroVideoEmbed }}" allow="autoplay; encrypted-media" allowfullscreen></iframe>
  </div>
  @endif
  <div class="bt-hero-overlay"></div>
  <div class="bt-hero-content">
    @if($clientName)<div class="bt-hero-sub">Tailor-made proposal for {{ $clientName }}</div>@endif
    <h1>{{ $itinTitle }}</h1>
    <div class="bt-hero-meta">
      <span><i class="fa fa-calendar-o"></i> {{ $dateRange }}</span>
      <span><i class="fa fa-users"></i> {{ $totalPax }} traveller{{ $totalPax>1?'s':'' }}</span>
      @if($dayCount>0)<span><i class="fa fa-moon-o"></i> {{ $dayCount }} day{{ $dayCount>1?'s':'' }}</span>@endif
    </div>
  </div>
  <div class="bt-hero-scroll"><i class="fa fa-angle-double-down"></i></div>
</div>

<!-- ADVISOR -->
<section id="advisor">
  <div class="bt-wrap">
    <div class="bt-advisor">
      <div class="bt-advisor-text">
        <h2 class="bt-section-title">Your travel advisor</h2>
        <p>I have designed this tailor-made program specifically for you, based on your wishes and travel style. Do not hesitate to contact me to adjust it together so that it perfectly matches your expectations.</p>
        <p style="margin-top:12px;">My local expertise and network of partners allow me to offer you unique experiences and exclusive access to the best sites in the region.</p>
      </div>
      <div class="bt-advisor-card">
        <div class="lbl">Your local advisor</div>
        <div class="bt-advisor-body">
          <div class="bt-avatar">{{ $agentInitial }}</div>
          <div class="bt-advisor-info">
            <h4>{{ $agentName }}</h4>
            <p>Travel expert<br>{{ config('app.name','PVT Travels') }}<br>
            @if($user->phone ?? false)Tel: {{ $user->phone }}@endif</p>
            <div class="bt-stars">★★★★★</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="bt-divider"></div>

<!-- PRESENTATION -->
<section id="presentation">
  <div class="bt-wrap">
    <div class="bt-section">
      <h2 class="bt-section-title">Presentation</h2>
      <div class="bt-pres">
        @if($itinerary && $itinerary->description)
          {!! $itinerary->description !!}
        @else
          <p>Discover an extraordinary journey through unique landscapes and authentic encounters. Every moment of this trip has been carefully crafted to offer you an unforgettable experience.</p>
          <p>Let yourself be enchanted by unique itineraries and original experiences, designed to reveal the soul of this region. With an expert guide, explore hidden gems and savor an unforgettable and authentic journey.</p>
        @endif
      </div>
    </div>
  </div>
</section>

<!-- PHOTOS GALLERY -->
@if(count($allPhotos)>0)
<div class="bt-divider"></div>
<section id="photos">
  <div class="bt-wrap">
    <div class="bt-section">
      <h2 class="bt-section-title">Photos</h2>
      <div class="bt-gallery">
        @foreach($allPhotos as $photo)
          @php $photoUrl = $resolvePhotoUrl($photo); @endphp
          @if($photoUrl)
          <img src="{{ $photoUrl }}" alt="Trip photo" loading="lazy">
          @endif
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

<div class="bt-divider"></div>

<!-- KEY POINTS -->
<section id="highlights">
  <div class="bt-wrap">
    <div class="bt-section">
      <h2 class="bt-section-title">Key points</h2>
      @php
        $highlights = [];
        if($itinerary && $itinerary->price_includes){
          $lines = preg_split('/\r\n|\r|\n/', $itinerary->price_includes);
          foreach($lines as $l){ $l=trim($l); if($l) $highlights[]=strip_tags($l); }
        }
        if(empty($highlights)){
          $highlights=['Must-see sites and hidden gems','Authentic local experiences','Expert francophone guide','Private transfers included','Handpicked accommodations'];
        }
      @endphp
      <ul class="bt-highlights">
        @foreach($highlights as $h)
          <li>{{ $h }}</li>
        @endforeach
      </ul>
    </div>
  </div>
</section>

<div class="bt-divider"></div>

<!-- BRIEF ITINERARY + MAP -->
<section id="itinerary">
  <div class="bt-wrap">
    <div class="bt-section">
      <div class="bt-brief">
        <div class="bt-brief-map">
          <div id="tripMap"></div>
        </div>
        <div class="bt-brief-list">
          <h3>Brief itinerary</h3>
          @if($itinerary && $itinerary->days && $itinerary->days->count()>0)
            @foreach($itinerary->days->sortBy('day_number') as $d)
            <div class="bt-jday">
              <div>
                <div class="bt-jday-lbl">Day {{ $d->day_number }}</div>
                <div class="bt-jday-dest"><i class="fa fa-map-marker"></i> {{ strtoupper($d->destinations ?? $d->title ?? 'JORDAN') }}</div>
              </div>
            </div>
            @endforeach
          @else
            <div class="bt-jday">
              <div>
                <div class="bt-jday-lbl">Day 1</div>
                <div class="bt-jday-dest"><i class="fa fa-map-marker"></i> JORDAN</div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

<div class="bt-divider"></div>

<!-- DAY BY DAY -->
<section id="daybyay">
  <div class="bt-wrap">
    <div class="bt-section">
      <h2 class="bt-section-title">Day by day program</h2>
      @if($itinerary && $itinerary->days && $itinerary->days->count()>0)
        @foreach($itinerary->days->sortBy('day_number') as $d)
        @php
          $dPhotos=$d->photos; if(is_string($dPhotos))$dPhotos=json_decode($dPhotos,true);
          if(!is_array($dPhotos))$dPhotos=[];
          $firstPhoto=count($dPhotos)>0?$dPhotos[0]:null;
          $extraPhotos=array_slice($dPhotos,1);
        @endphp
        <div class="bt-day-block">
          @php
            $heroUrl = $firstPhoto
              ? $resolvePhotoUrl($firstPhoto)
              : asset('/uploads/filemanager/Photos/Petra/petra_night.jpg');
          @endphp
          <div class="bt-day-hero" style="background:url('{{ $heroUrl }}') center/cover no-repeat;">
            @php
              $dayDate = $depDate ? $depDate->copy()->addDays($d->day_number - 1) : null;
            @endphp
            <div class="bt-date-badge">
              @if($dayDate)
              <div class="db-day">{{ $dayDate->format('d') }}</div>
              <div class="db-month">{{ $dayDate->format('M') }}</div>
              <div class="db-year">{{ $dayDate->format('Y') }}</div>
              @else
              <div class="db-day">{{ $d->day_number }}</div>
              <div class="db-month">DAY</div>
              @endif
            </div>
            <div class="bt-day-hero-overlay">
              <div class="bt-day-num">Day {{ $d->day_number }}</div>
              <div class="bt-day-title">{{ $d->title ?? 'Day '.$d->day_number }}</div>
            </div>
          </div>
          <div class="bt-day-body">
            @if($d->destinations)
            <div class="bt-day-loc"><i class="fa fa-map-marker"></i> {{ $d->destinations }}</div>
            @endif
            @if(!$firstPhoto)<div class="bt-day-head">Day {{ $d->day_number }} – {{ $d->title ?? '' }}</div>@endif
            @if(count($extraPhotos)>0)
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;margin-bottom:14px;">
              @foreach($extraPhotos as $ep)
                @php $epUrl = $resolvePhotoUrl($ep); @endphp
                <div style="height:140px;border-radius:8px;overflow:hidden;background:url('{{ $epUrl }}') center/cover no-repeat;"></div>
              @endforeach
            </div>
            @endif
            @if($d->description)
            <div class="bt-day-desc">{!! $d->description !!}</div>
            @endif
            @php $meals=[]; if($d->breakfast)$meals[]='🍳 Breakfast'; if($d->lunch)$meals[]='🥗 Lunch'; if($d->dinner)$meals[]='🍽️ Dinner'; @endphp
            @if(count($meals)>0)
            <div class="bt-meals">@foreach($meals as $m)<span class="bt-meal">{{ $m }}</span>@endforeach</div>
            @endif
            @if($d->accommodation_name)
            <div class="bt-accom-card">
              <div class="bt-accom-hdr"><i class="fa fa-bed"></i> Accommodation</div>
              <div class="bt-accom-body">
                <div class="bt-accom-icon"><i class="fa fa-building-o"></i></div>
                <div>
                  <div class="bt-accom-name">{{ $d->accommodation_name }}
                    @if($d->accommodation_stars){!! str_repeat('★', intval($d->accommodation_stars)) !!}@endif
                  </div>
                  @if($d->accommodation_category)<div class="bt-accom-sub">{{ $d->accommodation_category }}</div>@endif
                  @if($d->accommodation_description)<div class="bt-accom-sub" style="margin-top:4px;">{{ $d->accommodation_description }}</div>@endif
                </div>
              </div>
            </div>
            @endif
            @php
              $daySvcs = $d->services ?? [];
              if($daySvcs instanceof \Illuminate\Support\Collection) $daySvcs = $daySvcs->toArray();
              if(is_string($daySvcs)) $daySvcs = json_decode($daySvcs, true);
              if(!is_array($daySvcs)) $daySvcs = [];
              // Group services by type
              $svcGroups = [];
              foreach($daySvcs as $svc) {
                $svcArr = is_array($svc) ? $svc : (array)$svc;
                $rawType = strtolower((string)($svcArr['type'] ?? ''));
                $sNameLower = strtolower((string)($svcArr['name'] ?? ''));
                if(str_contains($rawType,'accommod')||str_contains($rawType,'hotel')||str_contains($sNameLower,'hotel')||str_contains($sNameLower,'camp')||str_contains($sNameLower,'room')||str_contains($sNameLower,'rotana')||str_contains($sNameLower,'suite')) {
                  $groupKey = 'Accommodation';
                } elseif(str_contains($rawType,'transport')||str_contains($rawType,'car')||str_contains($sNameLower,'transfer')||str_contains($sNameLower,'driver')||str_contains($sNameLower,'transport')) {
                  $groupKey = 'Transport';
                } elseif(str_contains($rawType,'restaur')||str_contains($sNameLower,'lunch')||str_contains($sNameLower,'dinner')||str_contains($sNameLower,'meal')||str_contains($sNameLower,'food')) {
                  $groupKey = 'Restaurant';
                } elseif(str_contains($rawType,'guide')||str_contains($sNameLower,'guide')) {
                  $groupKey = 'Guide';
                } else {
                  $groupKey = 'Activity';
                }
                $svcGroups[$groupKey][] = $svcArr;
              }
            @endphp
            @if(count($daySvcs)>0)
              @foreach($svcGroups as $groupName => $groupSvcs)
                @foreach($groupSvcs as $gsvc)
                @php
                  $sName  = (string)($gsvc['name'] ?? 'Service');
                  $sLoc   = (string)($gsvc['loc'] ?? '');
                  $sDesc  = (string)($gsvc['description'] ?? '');
                  $sImage = (string)($gsvc['image'] ?? '');
                  $vendorId = (string)($gsvc['vendor'] ?? '');
                  if($vendorId && (!$sImage || !$sDesc)) {
                    $libSvc = \App\Models\Service::find($vendorId);
                    if($libSvc) {
                      if(!$sImage && $libSvc->image) $sImage = $libSvc->image;
                      if(!$sDesc) $sDesc = $libSvc->notes ?? $libSvc->description ?? '';
                    }
                  }
                  $sImageUrl = $sImage ? (str_starts_with($sImage,'http') ? $sImage : asset($sImage)) : '';
                @endphp
                <div style="margin-top:22px;">
                  <h4 style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#1a1a1a;margin:0 0 12px;">{{ $groupName }}</h4>
                  <div style="border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;background:#fff;">
                    @if($sImageUrl)
                    <div style="width:100%;height:280px;background:url('{{ $sImageUrl }}') center/cover no-repeat;"></div>
                    @endif
                    <div style="padding:18px 20px;">
                      @if($sLoc)
                      <div style="font-size:13px;color:#666;margin-bottom:8px;display:flex;align-items:center;gap:5px;">
                        <i class="fa fa-map-marker" style="color:#f97316;font-size:14px;"></i> {{ $sLoc }}
                      </div>
                      @endif
                      <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:#1a1a1a;margin-bottom:8px;line-height:1.3;">{{ $sName }}</div>
                      @if($sDesc)
                      <div id="svcDesc{{ $loop->parent->index }}_{{ $loop->index }}" style="font-size:14px;color:#555;line-height:1.7;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $sDesc }}</div>
                      <a href="javascript:void(0)" id="svcMore{{ $loop->parent->index }}_{{ $loop->index }}" onclick="var d=document.getElementById('svcDesc{{ $loop->parent->index }}_{{ $loop->index }}');d.style.webkitLineClamp='unset';d.style.display='block';this.style.display='none';" style="display:inline-block;margin-top:10px;font-size:14px;font-weight:600;color:#1a1a1a;text-decoration:underline;text-underline-offset:3px;">See more →</a>
                      @endif
                    </div>
                  </div>
                </div>
                @endforeach
              @endforeach
            @endif
          </div>
        </div>
        @endforeach
      @else
        <p style="color:#888;font-size:14px;">No days added yet.</p>
      @endif
    </div>
  </div>
</section>

<!-- ACCOMMODATIONS SUMMARY -->
@if(count($accoms)>0)
<div class="bt-divider"></div>
<section id="accommodations">
  <div class="bt-wrap">
    <div class="bt-section">
      <h2 class="bt-section-title">Accommodations</h2>
      @foreach($accoms as $ac)
      <div class="bt-accom-card">
        <div class="bt-accom-hdr"><i class="fa fa-bed"></i> Day {{ $ac['day'] }}</div>
        <div class="bt-accom-body">
          <div class="bt-accom-icon"><i class="fa fa-building-o"></i></div>
          <div>
            <div class="bt-accom-name">{{ $ac['name'] }}
              @if($ac['stars']){!! str_repeat('★', intval($ac['stars'])) !!}@endif
            </div>
            @if($ac['cat'])<div class="bt-accom-sub">{{ $ac['cat'] }}</div>@endif
            @if($ac['desc'])<div class="bt-accom-sub" style="margin-top:4px;">{{ $ac['desc'] }}</div>@endif
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<div class="bt-divider"></div>

<!-- PRICING -->
<section id="pricing">
  <div class="bt-wrap">
    <div class="bt-section">
      <h2 class="bt-section-title">Pricing</h2>
      @if($groupTotal>0)
      <div class="bt-price-box">
        <div>
          <div class="lbl">Starting from</div>
          <div class="amt">
            @if($totalPax>0){!! $sym !!}{{ number_format($groupTotal/$totalPax,0) }}@else{!! $sym !!}{{ number_format($groupTotal,0) }}@endif
          </div>
          <div class="lbl">per person @if($totalPax>1)· {{ $totalPax }} travellers @endif</div>
        </div>
        <div style="text-align:right;">
          <div class="lbl">Total group</div>
          <div style="font-size:22px;font-weight:700;">{!! $sym !!}{{ number_format($groupTotal,0) }}</div>
          @if($dayCount>0)<div class="lbl">{{ $dayCount }} day{{ $dayCount>1?'s':'' }}</div>@endif
        </div>
      </div>
      @endif
      <div class="bt-price-grid">
        <div class="bt-price-block">
          <h4><i class="fa fa-check-circle"></i> Price includes</h4>
          <ul>
            @if($itinerary && $itinerary->price_includes)
              @php $includeLines = array_filter(array_map('trim', explode("\n", str_replace(["\r\n","\r"], "\n", strip_tags($itinerary->price_includes))))); @endphp
              @foreach($includeLines as $line)
                <li>{{ $line }}</li>
              @endforeach
            @else
              <li>Airport transfers</li>
              <li>Accommodation as per itinerary</li>
              <li>Meals as mentioned</li>
              <li>Expert guide</li>
              <li>Private transportation</li>
            @endif
          </ul>
        </div>
        <div class="bt-price-block exc">
          <h4><i class="fa fa-times-circle" style="color:#c0392b;"></i> Price excludes</h4>
          <ul>
            @if($itinerary && $itinerary->price_excludes)
              @php $excludeLines = array_filter(array_map('trim', explode("\n", str_replace(["\r\n","\r"], "\n", strip_tags($itinerary->price_excludes))))); @endphp
              @foreach($excludeLines as $line)
                <li>{{ $line }}</li>
              @endforeach
            @else
              <li>International flights</li>
              <li>Meals not mentioned</li>
              <li>Tips and gratuities</li>
              <li>Personal expenses</li>
              <li>Travel insurance</li>
            @endif
          </ul>
        </div>
      </div>
      @if($itinerary && $itinerary->booking_conditions)
      <div class="bt-conds">
        <h4><i class="fa fa-file-text-o"></i> Booking conditions</h4>
        <p>{{ $itinerary->booking_conditions }}</p>
      </div>
      @endif
      @if($itinerary && $itinerary->payment_conditions)
      <div class="bt-conds">
        <h4><i class="fa fa-credit-card"></i> Payment conditions</h4>
        <p>{{ $itinerary->payment_conditions }}</p>
      </div>
      @endif
    </div>
  </div>
</section>

<div class="bt-divider"></div>

<!-- RESERVATION -->
<section id="reservation">
  <div class="bt-wrap">
    <div class="bt-section">
      <div class="bt-reservation">
        <h2>Does this program suit you?</h2>
        <p>Don't wait any longer – confirm your interest and we will get back to you within 24 hours to finalize the details of your dream trip.</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;">
          <a href="mailto:{{ $user->email ?? '' }}" class="bt-pay-btn"><i class="fa fa-envelope"></i> Contact us to book</a>
          <a href="{{ route('admin.request-manager.generate-payment', $tripRequest->id) }}" class="bt-pay-btn" style="background:#27ae60;box-shadow:0 4px 16px rgba(39,174,96,.3);"><i class="fa fa-credit-card"></i> Generate Payment</a>
          @if($totalPax > 1)
          <button onclick="document.getElementById('splitPanel').style.display=document.getElementById('splitPanel').style.display==='none'?'block':'none'" class="bt-pay-btn" style="background:#2980b9;box-shadow:0 4px 16px rgba(41,128,185,.3);border:none;cursor:pointer;"><i class="fa fa-users"></i> Split Payment ({{ $totalPax }} persons)</button>
          @endif
        </div>
      </div>

      @if($totalPax > 1)
      <div id="splitPanel" style="display:none;margin-top:28px;background:#fff;border-radius:14px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.06);">
        <div style="background:linear-gradient(135deg,#2980b9,#3498db);padding:18px 24px;color:#fff;display:flex;align-items:center;justify-content:space-between;">
          <div>
            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;">Split Payment by Participant</div>
            <div style="font-size:12px;opacity:.8;margin-top:3px;">{{ $totalPax }} travelers · {!! $sym !!}{{ number_format($groupTotal/$totalPax, 2) }} per person</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:12px;opacity:.8;">Total</div>
            <div style="font-size:20px;font-weight:700;">{!! $sym !!}{{ number_format($groupTotal, 0) }}</div>
          </div>
        </div>

        <form action="{{ route('admin.request-manager.generate-payment', $tripRequest->id) }}" method="GET">
          <input type="hidden" name="split" value="1">
          <div style="padding:20px 24px;">
            <table style="width:100%;border-collapse:collapse;">
              <thead>
                <tr style="border-bottom:2px solid #e5e7eb;">
                  <th style="text-align:left;padding:10px 0;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#888;font-weight:600;">#</th>
                  <th style="text-align:left;padding:10px 0;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#888;font-weight:600;">Participant Name</th>
                  <th style="text-align:left;padding:10px 0;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#888;font-weight:600;">Email (optional)</th>
                  <th style="text-align:right;padding:10px 0;font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#888;font-weight:600;">Amount</th>
                </tr>
              </thead>
              <tbody>
                @for($p = 1; $p <= $totalPax; $p++)
                <tr style="border-bottom:1px solid #f3f4f6;">
                  <td style="padding:12px 0;font-size:14px;color:#888;width:30px;">{{ $p }}</td>
                  <td style="padding:12px 8px 12px 0;">
                    <input type="text" name="participants[{{ $p-1 }}][name]" value="{{ $p === 1 ? $clientName : '' }}" placeholder="Enter name..." required style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;transition:.15s;" onfocus="this.style.borderColor='#2980b9'" onblur="this.style.borderColor='#e5e7eb'">
                  </td>
                  <td style="padding:12px 8px 12px 0;">
                    <input type="email" name="participants[{{ $p-1 }}][email]" value="{{ $p === 1 ? ($tripRequest->email ?? '') : '' }}" placeholder="email@example.com" style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;transition:.15s;" onfocus="this.style.borderColor='#2980b9'" onblur="this.style.borderColor='#e5e7eb'">
                  </td>
                  <td style="padding:12px 0;text-align:right;font-size:16px;font-weight:700;color:#2980b9;white-space:nowrap;width:120px;">
                    {!! $sym !!}{{ number_format($groupTotal/$totalPax, 2) }}
                  </td>
                </tr>
                @endfor
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" style="padding:14px 0;text-align:right;font-size:14px;font-weight:700;color:#333;">Total:</td>
                  <td style="padding:14px 0;text-align:right;font-size:18px;font-weight:700;color:#1a1a1a;">{!! $sym !!}{{ number_format($groupTotal, 2) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div style="padding:0 24px 20px;display:flex;justify-content:flex-end;gap:10px;">
            <button type="button" onclick="document.getElementById('splitPanel').style.display='none'" style="padding:12px 24px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;color:#555;font-size:14px;font-weight:600;cursor:pointer;">Cancel</button>
            <button type="submit" style="padding:12px 28px;border:none;border-radius:8px;background:linear-gradient(135deg,#2980b9,#3498db);color:#fff;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;box-shadow:0 4px 16px rgba(41,128,185,.3);"><i class="fa fa-credit-card"></i> Generate {{ $totalPax }} Invoices</button>
          </div>
        </form>
      </div>
      @endif

    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="bt-footer">
  <div class="brand">{{ config('app.name','PVT Travels') }}</div>
  <p>{{ $agentName }} · {{ config('app.name','PVT Travels') }}<br>This proposal has been prepared exclusively for {{ $clientName ?: 'you' }}. All prices are indicative and subject to availability at time of booking.</p>
</footer>

<script>
// Init Leaflet map with geocoding from destination names
(function(){
  var days = @json($itinerary ? $itinerary->days->sortBy('day_number')->values() : collect());
  // Jordan bounding box for filtering results
  var jordanBounds = {minLat:29.0, maxLat:33.5, minLng:34.8, maxLng:39.5};
  var map = L.map('tripMap',{scrollWheelZoom:false,zoomControl:true}).setView([31.2,35.9],8);
  // Use CartoDB Voyager nolabels base + English labels overlay for English-only map
  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png',{
    attribution:'&copy; <a href="https://carto.com/">CARTO</a> &copy; <a href="https://osm.org/">OSM</a>',
    subdomains:'abcd',maxZoom:19
  }).addTo(map);
  // Add English-only labels on top
  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png',{
    subdomains:'abcd',maxZoom:19,pane:'shadowPane'
  }).addTo(map);

  // Collect destinations to geocode
  var dayDestinations = [];
  days.forEach(function(d){
    var dest = (d.destinations || d.title || '').trim();
    if(dest && dest.length > 1) {
      dayDestinations.push({ dayNum: d.day_number, dest: dest });
    }
  });

  if(dayDestinations.length === 0) return;

  var geocoded = [];

  function isInJordan(lat, lng){
    return lat >= jordanBounds.minLat && lat <= jordanBounds.maxLat &&
           lng >= jordanBounds.minLng && lng <= jordanBounds.maxLng;
  }

  // Fetch actual driving route from OSRM and draw it on the map
  function drawRoadRoute(coords){
    if(coords.length < 2) return;
    // Build OSRM waypoints string: lng,lat;lng,lat;...
    var waypoints = coords.map(function(c){ return c.lng+','+c.lat; }).join(';');
    var osrmUrl = 'https://router.project-osrm.org/route/v1/driving/' + waypoints
      + '?overview=full&geometries=geojson';
    fetch(osrmUrl)
      .then(function(r){ return r.json(); })
      .then(function(data){
        if(data && data.routes && data.routes.length > 0){
          var routeCoords = data.routes[0].geometry.coordinates.map(function(c){
            return [c[1], c[0]]; // GeoJSON is [lng,lat], Leaflet needs [lat,lng]
          });
          // Draw shadow line first for depth effect
          L.polyline(routeCoords,{color:'rgba(0,0,0,0.15)',weight:7,lineCap:'round',lineJoin:'round'}).addTo(map);
          // Draw main route line
          L.polyline(routeCoords,{color:'#f97316',weight:4,lineCap:'round',lineJoin:'round',opacity:0.9}).addTo(map);
        }
      })
      .catch(function(){
        // Fallback: draw straight dashed line if OSRM fails
        var lls = coords.map(function(c){ return [c.lat, c.lng]; });
        L.polyline(lls,{color:'#f97316',weight:3,dashArray:'8,6',opacity:0.8}).addTo(map);
      });
  }

  function renderMap(){
    geocoded.sort(function(a,b){ return a.dayNum - b.dayNum; });
    if(geocoded.length === 0) return;
    var lls = geocoded.map(function(c){ return [c.lat, c.lng]; });
    // Draw actual road route
    if(geocoded.length > 1){
      drawRoadRoute(geocoded);
    }
    // Add numbered markers
    geocoded.forEach(function(c){
      var icon = L.divIcon({
        className:'',
        html:'<div class="map-marker">'+c.dayNum+'</div>',
        iconSize:[32,32],
        iconAnchor:[16,16]
      });
      L.marker([c.lat,c.lng],{icon:icon}).addTo(map)
        .bindPopup('<b>Day '+c.dayNum+'</b><br>'+c.dest);
    });
    // Fit bounds with padding
    if(lls.length > 1){
      map.fitBounds(lls,{padding:[50,50]});
    } else {
      map.setView(lls[0], 10);
    }
  }

  // Geocode with Jordan context using viewbox + bounded parameters
  function geocodeNext(index){
    if(index >= dayDestinations.length){
      renderMap();
      return;
    }
    var item = dayDestinations[index];
    var searchQuery = item.dest.replace(/[–—]/g, ' ').trim();
    // Use viewbox and bounded to restrict results to Jordan area
    var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=3'
      + '&viewbox=34.8,33.5,39.5,29.0&bounded=1'
      + '&q=' + encodeURIComponent(searchQuery);
    fetch(url)
      .then(function(r){ return r.json(); })
      .then(function(data){
        if(data && data.length > 0){
          for(var i=0; i<data.length; i++){
            var lat = parseFloat(data[i].lat);
            var lng = parseFloat(data[i].lon);
            if(isInJordan(lat, lng)){
              geocoded.push({ dayNum: item.dayNum, dest: item.dest, lat: lat, lng: lng });
              break;
            }
          }
        }
      })
      .catch(function(){})
      .finally(function(){
        setTimeout(function(){ geocodeNext(index + 1); }, 1100);
      });
  }
  geocodeNext(0);

  // Active nav link on scroll
  var sections = document.querySelectorAll('section[id]');
  var navLinks = document.querySelectorAll('.bt-links a');
  window.addEventListener('scroll',function(){
    var pos = window.scrollY+80;
    sections.forEach(function(s){
      if(s.offsetTop<=pos && s.offsetTop+s.offsetHeight>pos){
        navLinks.forEach(function(a){ a.classList.remove('active'); });
        var active = document.querySelector('.bt-links a[href="#'+s.id+'"]');
        if(active) active.classList.add('active');
      }
    });
  });
})();
</script>
<script>
(function(){
  var v = document.getElementById('heroVideo');
  if(!v) return;
  function forcePlay(){
    v.muted = true;
    var p = v.play();
    if(p !== undefined){
      p.catch(function(){
        // If autoplay blocked, try on first user interaction
        document.addEventListener('click', function once(){
          v.play();
          document.removeEventListener('click', once);
        }, {once:true});
        document.addEventListener('touchstart', function once(){
          v.play();
          document.removeEventListener('touchstart', once);
        }, {once:true});
      });
    }
  }
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', forcePlay);
  } else {
    forcePlay();
  }
  v.addEventListener('canplay', function(){
    if(v.paused) v.play();
  });
})();
</script>

</body>
</html>
