<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PVT - Trip Planner</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css">
<link rel="stylesheet" href="/css/trip-planner.css?v={{ time() }}">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.svc-cat-chip { padding:5px 14px; border-radius:20px; border:1.5px solid #cbd5e1; background:#fff; color:#475569; font-size:12px; font-weight:600; cursor:pointer; transition:all .2s; }
.svc-cat-chip:hover { border-color:#0891b2; color:#0891b2; }
.svc-cat-chip-active { border-color:#0891b2; color:#0891b2; font-weight:700; }
.svc-cat-chip-active:hover { background:#0891b2; color:#fff; }

/* ORANGE COLOR SCHEME OVERRIDES */
.tp-navbar{background:linear-gradient(180deg,#e25822 0%,#f97316 100%) !important;}
.tp-navbar .tp-avatar{background:#e25822 !important;}
.tp-cover{background:linear-gradient(135deg,#e25822,#f97316 40%,#ff9f43) !important;}
.btn-continue{background:#f97316 !important;}
.btn-continue:hover{background:#e25822 !important;}
.btn-outline:hover{border-color:#f97316 !important;color:#f97316 !important;}
.tp-link{color:#f97316 !important;}
.btn-save{background:#f97316 !important;}
.btn-save:hover{background:#e25822 !important;}
.back-link{color:#f97316 !important;}
.tp-editor-tabs a.active{color:#f97316 !important;border-bottom-color:#f97316 !important;}
.tp-editor-tabs a:hover{color:#f97316 !important;}
.btn-act:hover{border-color:#f97316 !important;color:#f97316 !important;}
.btn-share{background:#f97316 !important;border-color:#f97316 !important;color:#fff !important;}
.btn-share:hover{background:#e25822 !important;color:#fff !important;}
.btn-share:disabled{background:#f1f5f9 !important;border-color:#e2e8f0 !important;color:#94a3b8 !important;cursor:not-allowed !important;}
button[type="submit"].btn-act{background:#f97316 !important;border-color:#f97316 !important;color:#fff !important;}
button[type="submit"].btn-act:hover{background:#e25822 !important;border-color:#e25822 !important;color:#fff !important;}
.ev-dots span{background:#f97316 !important;}
.ev-alert i{color:#f97316 !important;}
.ev-alert a{color:#f97316 !important;}
.ev-alert{background:#fff7ed !important;border: 1px solid #ffedd5 !important;}
.fl-group .fl-input:focus{border-color:#f97316 !important;}
.fl-group .fl-input:focus ~ .fl-label{color:#f97316 !important;}
.tp-day-card.active{background:#fff7ed !important;border-color:#f97316 !important;box-shadow:0 2px 4px rgba(249,115,22,0.08) !important;}
.btn-add-day{background:#f97316 !important;}
.btn-add-day:hover{background:#e25822 !important;}
.photos-label a{color:#f97316 !important;}
.photo-add:hover{border-color:#f97316 !important;}
.site-tag{background:#fff7ed !important;color:#f97316 !important;}
.add-dest{color:#f97316 !important;}
.meal-option input[type="radio"]{accent-color:#f97316 !important;}
.meal-checks input[type="checkbox"]{accent-color:#f97316 !important;}
.price-row .check-icon{color:#f97316 !important;}
.apply-all a{color:#f97316 !important;}
.apply-all i{color:#f97316 !important;}
.accom-hotel-card .hotel-info .hotel-name{color:#f97316 !important;}
.btn-add-alt:hover{border-color:#f97316 !important;color:#f97316 !important;}
.svc-btn:hover{border-color:#f97316 !important;color:#f97316 !important;}
.svc-type-btn:hover{border-color:#f97316 !important;color:#f97316 !important;background:#fff7ed !important;}
.cover-upload:hover{border-color:#f97316 !important;color:#f97316 !important;}
.btn-create-day{background:#f97316 !important;}
.btn-create-day:hover{background:#e25822 !important;}
.sdc-locations span{background:rgba(249,115,22,.8) !important;}
.sdc-cost{background:#f97316 !important;}
.fi:focus{border-color:#f97316 !important;}
.desc-area:focus{border-color:#f97316 !important;}
.lib-cat-tab:hover{color:#f97316 !important;}
.lib-cat-tab.active{color:#f97316 !important;border-bottom-color:#f97316 !important;}
.lib-cat-tab.active .lib-count{background:#fff7ed !important;color:#f97316 !important;}

/* INLINE STYLE OVERRIDES VIA ATTRIBUTE SELECTORS */
[style*="background:#005e46"], [style*="background: #005e46"] { background: #f97316 !important; }
[style*="background:#ecfdf5"], [style*="background: #ecfdf5"] { background: #fff7ed !important; }
[style*="background:#e6f4f1"], [style*="background: #e6f4f1"] { background: #fff7ed !important; }
[style*="background:#e6f4ef"], [style*="background: #e6f4ef"] { background: #fff7ed !important; }
[style*="background:linear-gradient(135deg,#005e46,#00a86b)"], [style*="background: linear-gradient(135deg,#005e46,#00a86b)"] { background: linear-gradient(135deg,#f97316,#ff9f43) !important; }
[style*="color:#005e46"], [style*="color: #005e46"] { color: #f97316 !important; }
[style*="color:#059669"], [style*="color: #059669"] { color: #f97316 !important; }
[style*="border-color:#005e46"], [style*="border-color: #005e46"] { border-color: #f97316 !important; }
[style*="border:1px solid #005e46"], [style*="border: 1px solid #005e46"] { border: 1px solid #f97316 !important; }
[style*="border:2px solid #005e46"], [style*="border: 2px solid #005e46"] { border: 2px solid #f97316 !important; }
[style*="border:1px solid #6ee7b7"], [style*="border: 1px solid #6ee7b7"] { border: 1px solid #ffedd5 !important; }
[style*="border:1px solid #d1fae5"], [style*="border: 1px solid #d1fae5"] { border: 1px solid #ffedd5 !important; }
[style*="accent-color:#005e46"], [style*="accent-color: #005e46"] { accent-color: #f97316 !important; }

/* HEADER DROPDOWN MENU */
.tp-header-dropdown {
    position: absolute;
    top: 48px;
    left: 0;
    width: 260px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    border: 1px solid #e2e8f0;
    z-index: 1000;
    padding: 12px 0;
    display: none;
    text-align: left;
}
.tp-header-dropdown.open {
    display: block !important;
}
.tp-header-dropdown .dropdown-section-title {
    font-size: 10px;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .08em;
    padding: 10px 16px 4px;
}
.tp-header-dropdown a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    color: #475569 !important;
    text-decoration: none !important;
    font-size: 13px;
    font-weight: 600;
    transition: all .15s;
    line-height: 1.4;
}
.tp-header-dropdown a:hover {
    background: #fff7ed !important;
    color: #f97316 !important;
}
.tp-header-dropdown a i {
    width: 18px;
    text-align: center;
    font-size: 14px;
    color: #94a3b8;
}
.tp-header-dropdown a:hover i {
    color: #f97316 !important;
}
.tp-header-dropdown a.active {
    color: #f97316 !important;
    background: #fff7ed !important;
}
.tp-header-dropdown a.active i {
    color: #f97316 !important;
}

@media (min-width: 1025px) {
    body {
        padding-left: 260px !important;
    }
    .tp-admin-sidebar {
        left: 0 !important;
        width: 260px !important;
        box-shadow: none !important;
        border-right: 1px solid rgba(255,255,255,0.05);
    }
    .tp-sidebar-overlay {
        display: none !important;
    }
    .tp-sb-close {
        display: none !important;
    }
    .tp-navbar {
        left: 260px !important;
    }
}

/* ACTIVE SIDEBAR LINK ORANGE STATE */
.tp-sb-link.active {
    color: #fff !important;
    background: linear-gradient(90deg, rgba(249, 115, 22, 0.15), rgba(249, 115, 22, 0.05)) !important;
    border: 1px solid rgba(249, 115, 22, 0.2) !important;
}
.tp-sb-link.active i {
    color: #f97316 !important;
    opacity: 1 !important;
}

/* SIDEBAR BRAND ORANGE HIGHLIGHTS */
.tp-sb-logo {
    background: linear-gradient(135deg, #f97316, #ff9f43) !important;
    box-shadow: 0 6px 12px rgba(249, 115, 22, 0.3) !important;
}
.tp-sb-sub {
    color: #f97316 !important;
}
</style>
</head>
<body>
@php
    $calculatedTotalCost = 0;
    if ($tripRequest->latestItinerary) {
        foreach ($tripRequest->latestItinerary->days as $day) {
            $services = is_string($day->services) ? json_decode($day->services, true) : $day->services;
            if (is_array($services)) {
                foreach ($services as $svc) {
                    if (isset($svc['cost'])) {
                        $calculatedTotalCost += (float)$svc['cost'];
                    }
                }
            }
        }
    }
    
    $savedGroupTotal = optional($tripRequest->latestItinerary)->group_total ?? 0;
    $displayTotal = $savedGroupTotal > 0 ? $savedGroupTotal : $calculatedTotalCost;
    
    if ($displayTotal == 0) {
        $displayTotal = (float)($tripRequest->ideal_budget ?? 0);
    }
    
    $currency = $tripRequest->currency ?? 'USD';
    $currencySym = '$';
    if ($currency == 'EUR') $currencySym = '€';
    elseif ($currency == 'JOD') $currencySym = 'JOD ';
    
    $minBudget = (float)($tripRequest->ideal_budget ?? 0);
    $maxBudget = (float)($tripRequest->max_budget ?? 0);
    $budgetRange = "";
    if ($minBudget > 0 && $maxBudget > 0) {
        $budgetRange = $currencySym . number_format($minBudget, 0) . ' - ' . $currencySym . number_format($maxBudget, 0);
    } elseif ($minBudget > 0) {
        $budgetRange = $currencySym . number_format($minBudget, 0);
    } elseif ($maxBudget > 0) {
        $budgetRange = 'Up to ' . $currencySym . number_format($maxBudget, 0);
    } else {
        $budgetRange = $currencySym . '0';
    }
@endphp
<div class="tp-navbar" style="justify-content: flex-end; padding: 0 20px;">
    <div class="tp-nav-right" style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
        <a href="/admin/request-manager/{{ $tripRequest->id }}" class="tp-nav-item" style="color: #fff !important; text-decoration: none !important; background: rgba(255, 255, 255, 0.15); padding: 6px 12px; border-radius: 6px; border: none; display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600;"><i class="fa fa-arrow-left"></i> Back to Request</a>
    </div>
</div>

{{-- SLIDE-OUT ADMIN SIDEBAR DRAWER --}}
<div class="tp-sidebar-overlay" id="tpSidebarOverlay" onclick="toggleTpSidebar()"></div>
<aside class="tp-admin-sidebar" id="tpAdminSidebar">
    <div class="tp-sb-header">
        <div class="tp-sb-brand">
            <div class="tp-sb-logo">P</div>
            <div><div class="tp-sb-title">PVT Travels</div><div class="tp-sb-sub">Admin Panel</div></div>
        </div>
        <button class="tp-sb-close" onclick="toggleTpSidebar()"><i class="fa fa-times"></i></button>
    </div>
    <nav class="tp-sb-nav">
        <div class="tp-sb-section">Main Menu</div>
        <a href="/admin/dashboard" class="tp-sb-link"><i class="fa fa-th-large"></i> Dashboard</a>
        <a href="{{ route('admin.request-manager') }}" class="tp-sb-link active"><i class="fa fa-inbox"></i> Request Manager</a>

        <div class="tp-sb-section">Management</div>
        <a href="{{ route('admin.users.index') }}" class="tp-sb-link"><i class="fa fa-users"></i> User Access</a>
        <a href="{{ route('admin.tours.index') }}" class="tp-sb-link"><i class="fa fa-plane"></i> Tours</a>
        <a href="{{ route('admin.bookings.index') }}" class="tp-sb-link"><i class="fa fa-calendar-check-o"></i> Bookings</a>
        <a href="{{ route('admin.quotations.index') }}" class="tp-sb-link"><i class="fa fa-pie-chart"></i> Quotations</a>
        <a href="{{ route('admin.services.index') }}" class="tp-sb-link"><i class="fa fa-cogs"></i> Services</a>
        <a href="{{ route('admin.library') }}" class="tp-sb-link"><i class="fa fa-book"></i> Library</a>
        <a href="{{ route('admin.canned-days.index') }}" class="tp-sb-link"><i class="fa fa-clone"></i> Canned Days</a>

        <div class="tp-sb-section">Financial</div>
        <a href="{{ route('admin.invoices.index') }}" class="tp-sb-link"><i class="fa fa-file-text-o"></i> Invoices</a>
        <a href="{{ route('admin.expenses.index') }}" class="tp-sb-link"><i class="fa fa-money"></i> Expenses</a>

        <div class="tp-sb-section">Current Request</div>
        <a href="/admin/request-manager/{{ $tripRequest->id }}" class="tp-sb-link"><i class="fa fa-arrow-left"></i> Back to Request #{{ $tripRequest->id }}</a>
        <a href="/admin/request-manager/{{ $tripRequest->id }}/trip-planner/preview" target="_blank" class="tp-sb-link"><i class="fa fa-eye"></i> Preview Trip</a>
    </nav>
    <div class="tp-sb-footer">
        <div class="tp-sb-user">{{ $user->name ?? 'Admin' }}</div>
        <a href="{{ route('logout') }}" class="tp-sb-logout"><i class="fa fa-power-off"></i></a>
    </div>
</aside>

<div class="tp-main">
{{-- ===== LANDING ===== --}}
<div class="tp-landing" id="tpLanding" style="display:{{ $autoOpenEditor ? 'none' : 'block' }}">
    <div class="tp-landing-card" style="background:#fff; border-radius:8px; box-shadow:0 4px 20px rgba(0,0,0,0.08); overflow:hidden; border:1px solid #e5e7eb;">
        @if($tripRequest->latestItinerary)
        @php
            $heroCover = optional($tripRequest->latestItinerary)->cover_photo;
            if (!$heroCover && $tripRequest->latestItinerary->days->count() > 0) {
                $firstDay = $tripRequest->latestItinerary->days->first();
                $firstDayPhotos = is_string($firstDay->photos) ? json_decode($firstDay->photos, true) : $firstDay->photos;
                if (is_array($firstDayPhotos) && !empty($firstDayPhotos)) {
                    $heroCover = $firstDayPhotos[0];
                }
            }
            if (!$heroCover) $heroCover = '/uploads/filemanager/Photos/Petra/Kahzneh.jpg';
            
            $heroPrice = ($displayTotal > 0 && $displayTotal != (float)($tripRequest->ideal_budget ?? 0)) ? $currencySym . number_format($displayTotal, 2) : $budgetRange;
            $heroDays = $tripRequest->latestItinerary->days->count();
        @endphp
        {{-- GREEN HEADER AREA --}}
        <div style="background:#005e46; padding:24px; position:relative;">
            {{-- INSET IMAGE CARD --}}
            <div style="background:url('{{ $heroCover }}') center/cover no-repeat; height:120px; border-radius:4px; position:relative; box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                {{-- FLOATING INFO BOXES --}}
                <div style="position:absolute; top:20px; left:20px; display:flex; flex-direction:column; gap:8px;">
                    <div style="background:rgba(0,0,0,0.5); padding:8px 12px; border-radius:4px; color:#fff; display:flex; align-items:center; gap:8px;">
                        <div style="text-align:center; border-right:1px solid rgba(255,255,255,0.3); padding-right:8px;">
                            <div style="font-size:14px; font-weight:800;">{{ $heroDays }}</div>
                            <div style="font-size:9px; opacity:0.8; text-transform:uppercase;">days</div>
                        </div>
                        <div style="font-size:16px; font-weight:700;">{{ $heroPrice }}</div>
                    </div>
                    <div style="background:rgba(0,0,0,0.5); padding:6px 12px; border-radius:4px; color:#fff; font-size:11px; font-weight:600; display:flex; align-items:center; gap:6px; width:fit-content;">
                        <i class="fa fa-users" style="font-size:10px; opacity:0.7;"></i>
                        {{ ($tripRequest->adults ?? 0) + ($tripRequest->children ?? 0) }} travelers
                    </div>
                </div>
            </div>
        </div>

        <div style="padding:12px 24px; border-bottom:1px solid #f3f4f6; text-align:right;">
            <button class="btn-continue" onclick="window.location.href='/admin/request-manager/{{ $tripRequest->id }}/trip-planner/My-quote'" style="background:#005e46; color:#fff; border:none; padding:10px 24px; border-radius:4px; font-weight:700; font-size:13px; cursor:pointer;">
                Continue with this version
            </button>
        </div>
        @else
        <div style="background:#005e46; padding:48px 24px; text-align:center; color:#fff;">
            <h2 style="font-size:24px; font-weight:800; margin:0 0 12px 0;">Welcome to Trip Planner</h2>
            <p style="opacity:0.8; font-size:14px;">Build your dream itinerary from scratch or copy a template.</p>
        </div>
        @endif

        <div style="padding:24px;">
            {{-- Start from an old quote --}}
            <div class="tp-section-block" style="padding:16px 0; border-bottom:1px solid #f3f4f6;">
                <div class="tp-row-between">
                    <div style="flex:1;">
                        <h4 style="font-size:16px; font-weight:700; color:#1a1a1a; margin:0 0 4px 0;">Start from an old quote</h4>
                        <p style="font-size:13px; color:#6b7280; margin:0;">You can start from a quote that was previously shared with another traveller.</p>
                    </div>
                    <button class="btn-outline" onclick="openPreviousQuotes()" style="border:1px solid #d1d5db; background:#fff; color:#1a1a1a; padding:8px 24px; border-radius:4px; font-weight:600; font-size:13px; cursor:pointer; width:160px;">
                        Select a quote
                    </button>
                </div>
            </div>

            {{-- Create a new quote --}}
            <div class="tp-section-block" style="padding:16px 0; border-bottom:1px solid #f3f4f6;">
                <div class="tp-row-between">
                    <div style="flex:1;">
                        <h4 style="font-size:16px; font-weight:700; color:#1a1a1a; margin:0 0 4px 0;">Create a new quote</h4>
                        <p style="font-size:13px; color:#6b7280; margin:0;">You can create a new quote (empty).</p>
                    </div>
                    <button class="btn-outline" onclick="openEditor()" style="border:1px solid #d1d5db; background:#fff; color:#1a1a1a; padding:8px 24px; border-radius:4px; font-weight:600; font-size:13px; cursor:pointer; width:160px;">
                        Start
                    </button>
                </div>
            </div>

            {{-- Manage your library --}}
            <div class="tp-section-block" style="padding:16px 0; border-bottom:none;">
                <div style="color:#005e46; font-size:15px; font-weight:700; margin-bottom:16px;">Manage your library</div>
                <div class="tp-row-between">
                    <div style="flex:1;">
                        <h4 style="font-size:16px; font-weight:700; color:#1a1a1a; margin:0 0 4px 0;">What is the purpose of the library?</h4>
                        <p style="font-size:13px; color:#6b7280; margin:0;">The library allows you to create cards for your days, activities, accommodation and transport. Each card is reusable and customizable for each quote.</p>
                    </div>
                    <a href="{{ route('admin.library') }}" class="btn-outline" style="border:1px solid #d1d5db; background:#fff; color:#1a1a1a; padding:8px 24px; border-radius:4px; font-weight:600; font-size:13px; cursor:pointer; width:160px; text-decoration:none; text-align:center; display:inline-block;">
                        Manage your library
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== PREVIOUS QUOTES ===== --}}
<div class="tp-previous-quotes" id="tpPreviousQuotes" style="display:none; padding:40px; background:#fff; min-height:calc(100vh - 60px);">
    <div style="max-width:1200px; margin:0 auto;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <h2 style="font-size:24px; font-weight:800; color:#1a1a1a; margin:0;">List of previous quotes</h2>
            <button onclick="backToLanding()" style="background:none; border:1px solid #d1d5db; color:#1a1a1a; padding:8px 20px; border-radius:4px; font-weight:600; font-size:13px; cursor:pointer; display:flex; align-items:center; gap:8px;">
                <i class="fa fa-arrow-left"></i> Back to main page
            </button>
        </div>
        
        {{-- FILTERS BAR --}}
        <div style="border:1px solid #e2e8f0; border-radius:8px; padding:20px; margin-bottom:24px; background:#fff;">
            <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; margin-bottom:16px;">
                <div class="fl-group" style="margin:0;">
                    <select class="fl-input" style="padding:10px; height:44px;"><option>fr</option><option>en</option></select>
                    <label class="fl-label">Quote's language</label>
                </div>
                <div class="fl-group" style="margin:0;">
                    <select class="fl-input" style="padding:10px; height:44px;"><option>Destination / Region</option></select>
                    <label class="fl-label">Destination / Region</label>
                </div>
                <div class="fl-group" style="margin:0;">
                    <select class="fl-input" style="padding:10px; height:44px;"><option>Route duration</option></select>
                    <label class="fl-label">Route duration</label>
                </div>
                <div class="fl-group" style="margin:0;">
                    <select class="fl-input" style="padding:10px; height:44px;"><option>POI / Cities</option></select>
                    <label class="fl-label">POI / Cities</label>
                </div>
            </div>
            <div style="display:flex; gap:16px; align-items:center;">
                <div class="fl-group" style="margin:0; flex:2; position:relative;">
                    <i class="fa fa-search" style="position:absolute; left:12px; top:14px; color:#94a3b8; font-size:14px;"></i>
                    <input class="fl-input" style="padding:10px 10px 10px 36px; height:44px;" placeholder=" ">
                    <label class="fl-label" style="left:36px;">Itinerary name</label>
                </div>
                <div class="fl-group" style="margin:0; flex:1">
                    <select class="fl-input" style="padding:10px; height:44px;"><option>Traveller name / File number</option></select>
                    <label class="fl-label">Traveller name / File number</label>
                </div>
                <div class="fl-group" style="margin:0; flex:2">
                    <select class="fl-input" style="padding:10px; height:44px;"><option>Itinerary version name</option></select>
                    <label class="fl-label">Itinerary version name</label>
                </div>
                <button style="background:none; border:none; color:#1a1a1a; font-weight:700; cursor:pointer; padding:10px; font-size:13px; white-space:nowrap;">Delete filters</button>
            </div>
        </div>

        <div style="text-align:right; font-size:12px; color:#64748b; font-weight:600; margin-bottom:12px;">{{ $previousQuotes->count() }} results found</div>

        <div style="border-top:1px solid #e2e8f0; overflow:hidden;">
            {{-- TABLE HEADER --}}
            <div style="display:flex; padding:12px 24px; font-size:12px; color:#94a3b8; font-weight:600; text-transform:none; border-bottom:1px solid #f1f5f9;">
                <div style="width:48px;"></div>
                <div style="flex:3; padding-left:12px;">Itinerary</div>
                <div style="flex:1; text-align:center;">Destination</div>
                <div style="flex:1; text-align:center;">Duration</div>
                <div style="flex:1; text-align:center;">Departure date</div>
                <div style="flex:1; text-align:center;">Shared on</div>
                <div style="width:140px;"></div>
            </div>            
            
            @foreach($previousQuotes as $quote)
            @php
                $qTitle = $quote->title ?: 'Untitled Quote';
                $qTraveler = $quote->tripRequest ? ($quote->tripRequest->first_name . ' ' . $quote->tripRequest->last_name) : 'Unknown';
                $qDuration = $quote->days ? $quote->days->count() : 0;
                $qPax = $quote->num_travelers ?? ($quote->tripRequest ? (($quote->tripRequest->adults ?? 0) + ($quote->tripRequest->children ?? 0)) : 0);
                $qPrice = $quote->group_total ?? 0;
                $qDate = $quote->arrival_date ? \Carbon\Carbon::parse($quote->arrival_date)->format('F d, Y') : '-';
                $qShared = $quote->created_at ? $quote->created_at->diffForHumans() : '-';

                $locations = $quote->days ? $quote->days->pluck('destinations')->filter()->unique()->take(15) : collect();
                
                $qThumb = $quote->cover_photo;
                if (!$qThumb && $quote->days && $quote->days->count() > 0) {
                    $qFirstDay = $quote->days->first();
                    $qDayPhotos = is_string($qFirstDay->photos) ? json_decode($qFirstDay->photos, true) : $qFirstDay->photos;
                    if (is_array($qDayPhotos) && !empty($qDayPhotos)) { $qThumb = $qDayPhotos[0]; }
                }
                if (!$qThumb) {
                    $qThumb = '/uploads/filemanager/Photos/Petra/Kahzneh.jpg';
                }
            @endphp
            <div class="quote-row-wrapper" style="border-bottom:1px solid #f1f5f9;">
                <div style="background:#fff; padding:12px 24px; display:flex; align-items:center; transition:0.2s;" class="quote-row">
                    <div style="width:24px; color:#64748b; cursor:pointer; font-size:10px;" class="chevron-toggle" onclick="toggleQuoteRow(this)"><i class="fa fa-chevron-down"></i></div>
                    <div style="width:24px; color:#005e46; font-size:14px;"><i class="fa fa-star-o"></i></div>
                    <div style="flex:3; display:flex; align-items:center; gap:12px; padding-left:12px;">
                        <img src="{{ $qThumb }}" style="width:36px; height:36px; object-fit:cover; border-radius:4px;">
                        <span style="font-weight:700; color:#1e293b; font-size:13px;">{{ $qTitle }}</span>
                    </div>
                    <div style="flex:1; font-size:13px; color:#1e293b; text-align:center;">Jordan</div>
                    <div style="flex:1; font-size:13px; color:#1e293b; text-align:center;">{{ $qDuration }}</div>
                    <div style="flex:1; font-size:13px; color:#1e293b; text-align:center;">{{ $qDate }}</div>
                    <div style="flex:1; font-size:13px; color:#64748b; text-align:center;">{{ $qShared }}</div>
                    <div style="width:140px; display:flex; align-items:center; justify-content:flex-end; gap:16px;">
                        <i class="fa fa-eye" style="color:#1e293b; cursor:pointer; font-size:16px;" onclick="toggleQuoteRow(this)"></i>
                        <a href="/admin/request-manager/{{ $tripRequest->id }}/copy-itinerary/{{ $quote->id }}" onclick="return confirm('Start from this quote?')" style="background:#005e46; color:#fff; border:none; padding:8px 20px; border-radius:4px; font-weight:700; font-size:12px; cursor:pointer; text-decoration:none; display:inline-block; text-transform:none;">Choose</a>
                    </div>
                </div>
                
                {{-- EXPANDED DETAILS --}}
                <div class="quote-details" style="display:none; background:#fff; padding:8px 24px 24px 72px; border-top:none;">
                    <div style="display:flex; flex-direction:column; gap:16px;">
                        <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                            <div style="background:#e6f4f1; color:#005e46; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; display:flex; align-items:center; gap:8px;">
                                <i class="fa fa-user-circle" style="font-size:14px; opacity:0.8;"></i> {{ strtolower($qTraveler) }}
                            </div>
                            <div style="background:#f3f4f6; color:#1e293b; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; display:flex; align-items:center; gap:8px;">
                                <i class="fa fa-users" style="opacity:0.6;"></i> {{ $qPax }} PAX
                            </div>
                            <div style="background:#f3f4f6; color:#1e293b; padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; display:flex; align-items:center; gap:8px;">
                                <i class="fa fa-money" style="opacity:0.6;"></i> {{ $currencySym }}{{ number_format($qPrice, 0) }}
                            </div>
                        </div>
                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            @if($locations->count() > 0)
                                @foreach($locations as $loc)
                                <div style="background:#f3f4f6; color:#4b5563; padding:4px 14px; border-radius:4px; font-size:11px; font-weight:700; border:none;">{{ $loc }}</div>
                                @endforeach
                            @else
                                <div style="background:#f3f4f6; color:#4b5563; padding:4px 14px; border-radius:4px; font-size:11px; font-weight:700; border:none;">Jordan</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach     
        </div>
            
        <div style="padding:32px; text-align:center;">
            <button style="background:none; border:none; color:#005e46; font-weight:700; font-size:14px; cursor:pointer; text-decoration:none;">Load more</button>
        </div>
        
        <div style="margin-top:24px; text-align:center;">
            <a href="#" onclick="backToLanding();return false;" style="color:#64748b; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                <i class="fa fa-arrow-left"></i> Back to main page
            </a>
        </div>
    </div>
</div>

{{-- ===== EDITOR ===== --}}
<div class="tp-editor" id="tpEditor" style="display:{{ $autoOpenEditor ? 'block' : 'none' }}">
    <div class="tp-editor-bar">
        <div class="tp-editor-tabs">
            <a href="/admin/request-manager/{{ $tripRequest->id }}/trip-planner/My-quote" class="{{ (!isset($activeTab)||!$activeTab||$activeTab==='My-quote')?'active':'' }}">My quote</a>
            <a href="/admin/request-manager/{{ $tripRequest->id }}/trip-planner/daybyday" class="{{ (isset($activeTab)&&$activeTab==='daybyday')?'active':'' }}">Day by day</a>
            <a href="/admin/request-manager/{{ $tripRequest->id }}/trip-planner/price" class="{{ (isset($activeTab)&&$activeTab==='price')?'active':'' }}">Price</a>
        </div>
        <div class="tp-actions" style="display:flex; align-items:center; gap:12px;">
            <a href="{{ route('admin.library') }}" target="_blank" class="btn-act" style="border-radius:20px; padding:7px 18px;"><i class="fa fa-th"></i> Library</a>
            <a href="/admin/request-manager/{{ $tripRequest->id }}/trip-planner/preview" target="_blank" class="btn-act" style="border-radius:20px; padding:7px 18px;"><i class="fa fa-eye"></i> View preview</a>
            <div style="display:flex; align-items:center; gap:8px;">
                <button class="btn-act btn-share" style="background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0; border-radius:20px; padding:7px 18px; cursor:not-allowed;" disabled><i class="fa fa-link"></i> Share to the traveller</button>
                <span style="color:#ef4444; font-size:18px; display:flex; align-items:center; justify-content:center;" title="Personalization not complete"><i class="fa fa-exclamation-circle"></i></span>
            </div>
            @if($tripRequest->latestItinerary)
                <form action="{{ route('admin.request-manager.sync-quote', ['id' => $tripRequest->id, 'itinId' => $tripRequest->latestItinerary->id]) }}" method="POST" style="display:inline; margin:0;">
                    @csrf
                    <button type="submit" class="btn-act" style="background:#f97316; color:#fff; border-color:#f97316; font-weight:700; border-radius:20px; padding:7px 18px;"><i class="fa fa-calculator"></i> Generate Quotation</button>
                </form>
            @endif
        </div>
    </div>

    {{-- ══════ MY QUOTE ══════ --}}
    <div class="itin-panel" id="panelPers" style="display:{{ (!isset($activeTab)||!$activeTab||$activeTab==='My-quote')?'block':'none' }}">
        {{-- Traveller request card --}}
        <div class="ev-card">
            <div class="ev-dots"><span></span><span></span><span></span></div>
            <h3 onclick="toggleCard(this)">Traveller request <i class="fa fa-chevron-up"></i></h3>
            <div class="card-body">
                <div class="ev-info-row">
                    <div class="ev-info-item"><div class="lbl">Request ID</div><div class="val">{{ $tripRequest->id }}</div></div>
                    <div class="ev-info-item"><div class="lbl">Traveller name</div><div class="val">{{ $tripRequest->first_name ?? '' }} {{ strtoupper($tripRequest->last_name ?? '') }}</div></div>
                </div>
                <div class="ev-info-row">
                    <div class="ev-info-item"><div class="lbl">Email</div><div class="val">{{ $tripRequest->email ?? 'N/A' }}</div></div>
                    <div class="ev-info-item"><div class="lbl">Phone</div><div class="val">{{ $tripRequest->phone ?: 'N/A' }}</div></div>
                </div>
                <div class="ev-info-row">
                    <div class="ev-info-item"><div class="lbl">Destination</div><div class="val">{{ $tripRequest->destination ?? 'Jordan' }}</div></div>
                    @php
                        $countryFlags = ['US'=>'🇺🇸','UK'=>'🇬🇧','FR'=>'🇫🇷','DE'=>'🇩🇪','IN'=>'🇮🇳','JO'=>'🇯🇴','AE'=>'🇦🇪','SA'=>'🇸🇦','CA'=>'🇨🇦','AU'=>'🇦🇺'];
                        $cCode = $tripRequest->country ?? '';
                        $cFlag = $countryFlags[$cCode] ?? '🌍';
                    @endphp
                    <div class="ev-info-item"><div class="lbl">Market</div><div class="val">{{ $cCode ? "$cFlag $cCode" : 'N/A' }}</div></div>
                </div>
                <div class="ev-info-row">
                    <div class="ev-info-item"><div class="lbl">Travel dates</div>
                        <div class="val">
                            @if($tripRequest->departure_date && $tripRequest->return_date)
                                {{ \Carbon\Carbon::parse($tripRequest->departure_date)->format('d M Y') }} → {{ \Carbon\Carbon::parse($tripRequest->return_date)->format('d M Y') }}
                            @else
                                Flexible
                            @endif
                        </div>
                    </div>
                    <div class="ev-info-item"><div class="lbl">Number of travellers</div><div class="val">{{ ($tripRequest->adults ?? 0) + ($tripRequest->children ?? 0) }}</div></div>
                </div>
                <div class="ev-info-row">
                    <div class="ev-info-item"><div class="lbl">Profile</div><div class="val">{{ ucfirst(str_replace('_',' ',$tripRequest->participant_type ?? 'N/A')) }}</div></div>
                    <div class="ev-info-item"><div class="lbl">Project</div><div class="val">{{ ucwords(str_replace('_',' ',$tripRequest->project_stage ?? 'N/A')) }}</div></div>
                </div>
                <div class="ev-info-row">
                    <div class="ev-info-item"><div class="lbl">Budget</div>
                        <div class="val">
                            ${{ number_format($tripRequest->ideal_budget ?? 0) }}{{ $tripRequest->max_budget ? ' - $'.number_format($tripRequest->max_budget) : '' }}
                        </div>
                    </div>
                    <div class="ev-info-item"><div class="lbl">Accommodation</div>
                        <div class="val">
                            @php
                                $accom = $tripRequest->accommodation_prefs;
                                if(is_string($accom)) { $accom = json_decode($accom, true); }
                            @endphp
                            {{ is_array($accom) ? implode(', ', $accom) : ($accom ?? 'N/A') }}
                        </div>
                    </div>
                </div>
                <div class="ev-info-row">
                    <div class="ev-info-item"><div class="lbl">Guided</div><div class="val">{{ ucwords(str_replace(['-','_'],' ',$tripRequest->guide_type ?? 'N/A')) }}</div></div>
                    @php
                        $gLangs = $tripRequest->guide_languages;
                        if(is_string($gLangs)) { $gLangs = json_decode($gLangs, true); }
                    @endphp
                    <div class="ev-info-item"><div class="lbl">Guide language</div><div class="val">{{ is_array($gLangs) ? implode(', ', $gLangs) : ($gLangs ?? 'N/A') }}</div></div>
                </div>
                @if($tripRequest->travel_plan)
                <div class="ev-info-row">
                    <div class="ev-info-item" style="flex:1"><div class="lbl">Description</div>
                        <div class="val" style="background:#fafaf8;padding:12px;border-radius:6px;line-height:1.6;margin-top:4px;white-space:pre-wrap;">{{ $tripRequest->travel_plan }}</div>
                    </div>
                </div>
                @endif
                <div class="ev-alert">
                    <i class="fa fa-info-circle"></i>
                    <div><strong>Important</strong><br>If you need to adjust the number of pax please go to Request Manager. Please refresh this page when you've done the modifications in Request Manager.</div>
                    <a href="/admin/request-manager/{{ $tripRequest->id }}">GOT TO REQUEST MANAGER</a>
                    <button class="close-alert" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            </div>
        </div>
        {{-- Personalize card --}}
        <div class="ev-card">
            <div class="ev-dots"><span></span><span></span><span></span></div>
            <h3 onclick="toggleCard(this)">Personalize <i class="fa fa-chevron-up"></i></h3>
            <div class="card-body" style="margin-top:20px">
                <div class="fl-group">
                    <input class="fl-input" id="itinTitle" value="{{ $tripRequest->latestItinerary->title ?? '' }}" placeholder=" ">
                    <label class="fl-label">Quote title</label>
                </div>
                <div class="fl-group">
                    <input class="fl-input" id="itinSurname" value="{{ $tripRequest->latestItinerary->traveler_surname ?? ($tripRequest->first_name.' '.$tripRequest->last_name) }}" placeholder=" " oninput="updateCharCount(this,'surnameCount',255)">
                    <label class="fl-label">Traveller surname</label>
                </div>
                <div class="char-count" id="surnameCount">({{ strlen($tripRequest->latestItinerary->traveler_surname ?? ($tripRequest->first_name.' '.$tripRequest->last_name)) }}/255)</div>
                <div class="fl-group">
                    <select class="fl-input" id="itinLang">
                        <option value="en" {{ ($tripRequest->latestItinerary->language ?? 'en')=='en'?'selected':'' }}>🇬🇧 English</option>
                        <option value="fr" {{ ($tripRequest->latestItinerary->language ?? '')=='fr'?'selected':'' }}>🇫🇷 Français</option>
                        <option value="ar" {{ ($tripRequest->latestItinerary->language ?? '')=='ar'?'selected':'' }}>🇯🇴 Arabic</option>
                    </select>
                    <label class="fl-label">Language of quote</label>
                </div>
                <div class="fl-group">
                    <input type="date" class="fl-input" id="itinArrival" value="{{ optional($tripRequest->latestItinerary)->arrival_date ? $tripRequest->latestItinerary->arrival_date->format('Y-m-d') : '' }}" placeholder=" ">
                    <label class="fl-label">Arrival date</label>
                </div>
                <div class="cover-photo-section">
                    <h5>Cover photo</h5>
                    <div class="cover-photo-row">
                        <div class="cover-preview" id="coverPreview">
                            <img src="{{ optional($tripRequest->latestItinerary)->cover_photo ?: '/uploads/filemanager/Photos/Petra/Kahzneh.jpg' }}" alt="Cover" id="coverImg">
                            <button class="remove-cover" onclick="document.getElementById('coverImg').src='';document.getElementById('itinCover').value=''">×</button>
                        </div>
                        <div class="cover-upload" onclick="document.getElementById('coverFileInput').click()">
                            <i class="fa fa-camera"></i>
                        </div>
                        <input type="file" id="coverFileInput" accept="image/*" style="display:none" onchange="uploadCoverPhoto(this)">
                        <input type="hidden" id="itinCover" value="{{ $tripRequest->latestItinerary->cover_photo ?? '' }}">
                    </div>
                </div>
                <div class="fl-group" style="margin-top:16px;">
                    <input class="fl-input" id="itinVideoUrl" value="{{ optional($tripRequest->latestItinerary)->video_url ?? '' }}" placeholder=" ">
                    <label class="fl-label">Video URL (YouTube or website link)</label>
                </div>
                <button class="btn-save" onclick="saveItin()" style="margin-top:20px">Save Personalization</button>
            </div>
        </div>
    </div>

    {{-- ══════ DAY BY DAY ══════ --}}
    <div class="itin-panel panel-dbd" id="panelDbd" style="display:{{ (isset($activeTab)&&$activeTab==='daybyday')?'flex':'none' }}">
        <div class="tp-editor-body" style="height:100%">
            <div class="tp-sidebar">
                @if($tripRequest->latestItinerary && $tripRequest->latestItinerary->days->count())
                    @foreach($tripRequest->latestItinerary->days as $day)
                    <div class="tp-day-card {{ $loop->first?'active':'' }}" data-day-id="{{ $day->id }}" data-duration="{{ $day->duration ?? 1 }}" onclick="selDay({{ $day->id }},this)">
                        @php
                            $dayPhotos = is_string($day->photos) ? json_decode($day->photos, true) : $day->photos;
                            $thumbUrl = is_array($dayPhotos) && !empty($dayPhotos) ? $dayPhotos[0] : null;
                        @endphp
                        @if($thumbUrl)
                        <div class="tp-day-thumb" style="background-image:url('{{ str_replace(' ', '%20', $thumbUrl) }}');background-size:cover;background-position:center;"></div>
                        @else
                        <div class="tp-day-thumb"><i class="fa fa-image"></i></div>
                        @endif
                        <div class="tp-day-info">
                            <div class="day-label">Day {{ $day->day_number }}</div>
                            <div class="day-title">{{ $day->title ?: 'Untitled' }}</div>
                            @if($day->destinations)
                                <div class="day-loc"><i class="fa fa-map-marker"></i> {{ $day->destinations }}</div>
                            @else
                                <div class="day-loc"><i class="fa fa-map-marker"></i> No location</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                    <div style="padding:30px;text-align:center;color:#aaa;font-size:12px"><i class="fa fa-calendar-plus-o" style="font-size:28px;opacity:.4;display:block;margin-bottom:8px"></i>No days yet</div>
                @endif
                <button class="btn-add-day" onclick="addDay()"><i class="fa fa-plus"></i> Add another day</button>
            </div>
            <div class="tp-content">

                {{-- ═══ LIBRARY POPUP MODAL (overlay) ═══ --}}
                <div id="libraryModalOverlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:50;">
                    {{-- Backdrop --}}
                    <div style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5);" onclick="closeLibraryModal()"></div>
                    {{-- Modal Panel --}}
                    <div style="position:fixed; top:0; left:0; right:0; bottom:0; display:flex; align-items:center; justify-content:center; padding:1rem; pointer-events:none;">
                        <div style="background:#fff; border-radius:12px; box-shadow:0 25px 50px -12px rgba(0,0,0,.25); width:100%; max-width:48rem; max-height:85vh; display:flex; flex-direction:column; pointer-events:auto; position:relative; font-family:'Inter',sans-serif;">
                            {{-- Header --}}
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800 m-0" id="libModalTitle">Add another day</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="day-badge" id="modalDayBadge">D1</span>
                                        <span class="text-sm text-gray-500" id="modalDayDate"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button class="btn-create-day" onclick="createBlankDay()"><i class="fa fa-plus"></i> CREATE ANOTHER DAY</button>
                                    <button onclick="closeLibraryModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 text-xl cursor-pointer" style="border:none;background:none">&times;</button>
                                </div>
                            </div>
                            {{-- Search --}}
                            <div class="px-6 py-3 border-b border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 relative">
                                        <i class="fa fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                        <input type="text" class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-green-500" placeholder="Search in my library..." id="searchSavedDay" oninput="searchLibrary(this.value)">
                                    </div>
                                    <div class="flex items-center gap-1 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                        <span>🇬🇧</span>
                                        <select id="modalLang" class="border-0 text-sm bg-transparent cursor-pointer outline-none">
                                            <option value="en">English</option>
                                            <option value="fr">Français</option>
                                            <option value="ar">Arabic</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- Scrollable card grid — ONLY Library Days --}}
                            <div class="overflow-y-auto flex-1 px-6 py-4">
                                <div class="saved-days-grid" id="savedDaysGrid">
                                    @foreach($cannedDays as $cd)
                                    @php
                                        $cdContent = $cd->contents->first();
                                        $cdTitle = $cdContent ? $cdContent->title : 'Untitled';
                                        $cdDesc = $cdContent ? $cdContent->description : '';
                                        $cdImages = @unserialize($cd->images);
                                        if (!is_array($cdImages)) $cdImages = [];
                                        // Normalize all image paths
                                        $cdImages = array_values(array_filter(array_map(function($img) {
                                            if (!$img) return null;
                                            return (!str_starts_with($img, 'http')) ? '/' . ltrim($img, '/') : $img;
                                        }, $cdImages)));
                                        $cdImg = !empty($cdImages) ? $cdImages[0] : null;
                                        $cdImagesJson = json_encode($cdImages);
                                    @endphp
                                    <div class="saved-day-card lib-item lib-days" data-type="days" data-id="{{ $cd->id }}" data-title="{{ e($cdTitle) }}" data-desc="{{ e($cdDesc) }}" data-images="{{ e($cdImagesJson) }}" onclick="useSavedDay(this)" style="background-image:url('{{ $cdImg ?: '/uploads/filemanager/Photos/Petra/Kahzneh.jpg' }}')">
                                        
                                        <div class="sdc-title">{{ $cdTitle }}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Day edit form (Inline) --}}
                <div id="dayFormInline" style="display:none; height:100%; flex-direction:column; background:transparent;">
                    <div class="flex flex-col h-full relative" style="font-family:'Inter',sans-serif">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-8 pt-6 pb-2">
                            <div class="flex items-center gap-2 bg-gray-200 px-3 py-1 rounded-full text-sm font-medium text-gray-700">
                                <span class="w-6 h-6 rounded-full bg-gray-400 text-white flex items-center justify-center text-xs font-bold" id="detailDayBadge">D1</span>
                                <span id="detailDayDate" class="text-xs text-gray-600 font-semibold"></span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span id="saveStatusIndicator" style="font-size:12px; color:#999; display:none;"><i class="fa fa-spinner fa-spin"></i> Saving...</span>
                            </div>
                        </div>
                        {{-- Scrollable content --}}
                        <div class="overflow-y-auto flex-1 px-8 py-4">
                            <div class="day-content-card" style="position:relative;">
                                {{-- Three dot menu inside card --}}
                                <button class="day-three-dot" onclick="toggleDayDropdown(event)" style="border:none;background:none;position:absolute;top:16px;right:16px;font-size:20px;color:#9ca3af;cursor:pointer;padding:4px 8px;line-height:1;border-radius:4px;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='none'"><i class="fa fa-ellipsis-v"></i></button>
                                <div id="dayDropdownMenu" style="display:none; position:absolute; right:0; top:44px; background:#fff; border:1px solid #e5e7eb; border-radius:8px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.12),0 4px 10px -4px rgba(0,0,0,0.06); z-index:100; min-width:190px; overflow:hidden;">
                                    <button onclick="extendDuration()" style="display:flex;align-items:center;gap:10px;width:100%;padding:11px 16px;border:none;background:none;cursor:pointer;font-size:13px;font-weight:500;color:#111827;text-align:left;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='none'"><span style="width:18px;height:18px;border:1.5px solid #374151;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">+</span> Extend duration</button>
                                    <div style="height:1px;background:#f3f4f6;margin:0 12px;"></div>
                                    <button onclick="reduceDuration()" style="display:flex;align-items:center;gap:10px;width:100%;padding:11px 16px;border:none;background:none;cursor:pointer;font-size:13px;font-weight:500;color:#111827;text-align:left;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='none'"><span style="width:18px;height:18px;border:1.5px solid #374151;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">&minus;</span> Reduce duration</button>
                                    <div style="height:1px;background:#f3f4f6;margin:0 12px;"></div>
                                    <button onclick="deleteCurrentDay()" style="display:flex;align-items:center;gap:10px;width:100%;padding:11px 16px;border:none;background:none;cursor:pointer;font-size:13px;font-weight:500;color:#dc2626;text-align:left;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'"><i class="fa fa-trash-o" style="font-size:14px;width:18px;text-align:center;"></i> Delete this day</button>
                                </div>

                                <input type="hidden" id="editDayId">
                                {{-- Photos --}}
                                <div class="photos-section">
                                    <div class="photos-label" style="color:#005e46; font-size:14px; font-weight:700;">Photos <a href="#" style="color:#005e46; font-weight:600; text-decoration:none; margin-left:12px; font-size:13px;">How to choose the right photos?</a></div>
                                    <div class="photos-scroll" id="photosScroll">
                                        <div class="photo-item photo-add" onclick="document.getElementById('dayPhotoInput').click()"><i class="fa fa-camera"></i></div>
                                    </div>
                                    <input type="file" id="dayPhotoInput" accept="image/*" multiple style="display:none" onchange="uploadDayPhoto(this)">
                                </div>
                                {{-- Day title --}}
                                <div style="margin-bottom:24px; position:relative; margin-top:20px;">
                                    <label for="dayTitle" style="position:absolute; top:-8px; left:12px; background:#fff; padding:0 4px; font-size:11px; color:#6b7280; font-weight:500;">Day title</label>
                                    <input id="dayTitle" style="width:100%; padding:12px 14px; border:1px solid #d1d5db; border-radius:6px; outline:none; font-size:14px; background:#fff;" oninput="updateCharCount(this,'dayTitleCount',255); debouncedAutoSaveDay()">
                                    <div class="char-count" id="dayTitleCount" style="position:absolute; bottom:-18px; right:4px; font-size:11px; color:#9ca3af;">(0/255)</div>
                                </div>
                                {{-- Description --}}
                                <div class="section-label" style="font-size:14px; font-weight:700; color:#111827; margin:24px 0 8px;">Description</div>
                                <div id="dayDescQuill" style="min-height:120px; background:#fff; border:1px solid #d1d5db; border-radius:6px; overflow:hidden;"></div>
                                <div style="margin-bottom:20px"></div>
                                {{-- Sites --}}
                                <div class="section-label" style="font-size:14px; font-weight:700; color:#111827; margin:24px 0 8px;">Site(s)</div>
                                <div style="margin-bottom:20px">
                                    <div class="site-tags" id="siteTags" style="margin-bottom:12px;"></div>
                                    <div style="position:relative;display:flex;align-items:center;gap:12px;margin-top:8px">
                                        <div style="flex:1;position:relative;">
                                            <input class="fi" id="dayDest" placeholder="Type destination..." autocomplete="off" style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:10px 12px; font-size:13px;" oninput="destAutocomplete(this.value)" onkeydown="if(event.key==='Enter'){addSiteTag();closeDestAutocomplete();event.preventDefault()} else if(event.key==='Escape'){closeDestAutocomplete()}">
                                            <div id="destAutocompleteDropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #d1d5db;border-top:none;border-radius:0 0 6px 6px;box-shadow:0 4px 12px rgba(0,0,0,0.1);z-index:999;max-height:200px;overflow-y:auto;"></div>
                                        </div>
                                        <span class="add-dest" onclick="addSiteTag();closeDestAutocomplete()" style="color:#005e46; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px; white-space:nowrap;"><i class="fa fa-plus"></i> Add a destination</span>
                                    </div>
                                </div>
                                {{-- Meals --}}
                                <div class="section-label" style="font-size:14px; font-weight:700; color:#111827; margin:24px 0 8px;">Meal</div>
                                <div class="meal-section" style="margin-top:8px; margin-bottom:24px;">
                                    <div class="meal-option" style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                                        <input type="radio" name="mealType" value="included" id="mealIncluded" onchange="document.getElementById('mealChecks').style.display='inline-flex'; autoSaveDay()" style="accent-color:#005e46; width:16px; height:16px;">
                                        <label for="mealIncluded" style="font-size:13px; font-weight:500; color:#374151;">Meals included</label>
                                        <div class="meal-checks" id="mealChecks" style="display:none; gap:16px; margin-left:12px;">
                                            <label style="font-size:13px; font-weight:500; color:#4b5563; display:flex; align-items:center; gap:4px; cursor:pointer;"><input type="checkbox" id="mB" onchange="autoSaveDay()" style="accent-color:#005e46; width:15px; height:15px;"> breakfast</label>
                                            <label style="font-size:13px; font-weight:500; color:#4b5563; display:flex; align-items:center; gap:4px; cursor:pointer;"><input type="checkbox" id="mL" onchange="autoSaveDay()" style="accent-color:#005e46; width:15px; height:15px;"> lunch</label>
                                            <label style="font-size:13px; font-weight:500; color:#4b5563; display:flex; align-items:center; gap:4px; cursor:pointer;"><input type="checkbox" id="mD" onchange="autoSaveDay()" style="accent-color:#005e46; width:15px; height:15px;"> dinner</label>
                                        </div>
                                    </div>
                                    <div class="meal-option" style="display:flex; align-items:center; gap:10px;">
                                        <input type="radio" name="mealType" value="none" id="mealNone" checked onchange="document.getElementById('mealChecks').style.display='none'; autoSaveDay()" style="accent-color:#005e46; width:16px; height:16px;">
                                        <label for="mealNone" style="font-size:13px; font-weight:500; color:#374151;">No meals</label>
                                    </div>
                                </div>

                                {{-- Add a service --}}
                                <div class="service-section" style="margin-top:24px; border-top:1px solid #eee; padding-top:20px;">
                                    <div class="svc-label" style="font-size:14px; font-weight:700; color:#111827; margin-bottom:8px;">Add a service:</div>
                                    <div id="servicesContainer" style="margin-top:8px"></div>
                                    <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
                                        <button type="button" class="svc-type-btn" onclick="openServiceSelector('activ')">Activity</button>
                                        <button type="button" class="svc-type-btn" onclick="openServiceSelector('transport')">Transport</button>
                                        <button type="button" class="svc-type-btn" onclick="openServiceSelector('accommod')">Accommodation</button>
                                        <button type="button" class="svc-type-btn" onclick="openServiceSelector('restaurants')">Restaurant</button>
                                        <button type="button" class="svc-type-btn" onclick="openServiceSelector('guides')">Guide</button>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div id="dayEmpty" class="empty-state"><i class="fa fa-hand-pointer-o"></i><p style="font-weight:600">Select a day or add a new one</p></div>
            </div>
        </div>
    </div>

            {{-- ══════ SERVICE SELECTOR MODAL (Evaneos Clone) ══════ --}}
    <div id="serviceSelectorOverlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:60;">
        <div style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5);" onclick="closeServiceSelector()"></div>
        <div style="position:fixed; top:0; left:0; right:0; bottom:0; display:flex; align-items:center; justify-content:center; padding:1rem; pointer-events:none;">
            <div class="bg-white pointer-events-auto relative flex flex-col" style="width:100%; max-width:850px; height:85vh; border-radius:4px; box-shadow:0 10px 25px rgba(0,0,0,0.2); font-family:'Inter',sans-serif">
                {{-- Header --}}
                <div style="padding:20px 24px 16px 24px; position:relative;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <h3 style="font-size:16px; font-weight:700; color:#1f2937; margin:0 0 2px 0;">Add a service:</h3>
                            <div id="svcSelectorTitle" style="color:#005e46; font-size:14px; font-weight:600;">Accommodation</div>
                        </div>
                        <div style="display:flex; align-items:center; gap:24px;">
                            <button style="color:#005e46; font-size:12px; font-weight:700; background:none; border:none; cursor:pointer; display:flex; align-items:center; gap:6px;">
                                <i class="fa fa-plus-circle" style="font-size:14px;"></i> <span id="svcSelectorAddBtn">ADD ACCOMMODATION</span>
                            </button>
                            <button onclick="closeServiceSelector()" style="background:none; border:none; font-size:24px; color:#9ca3af; cursor:pointer; margin-top:-4px; padding:0 8px;">&times;</button>
                        </div>
                    </div>
                    
                    {{-- Search Row --}}
                    <div style="display:flex; gap:16px; margin-top:20px;">
                        <div style="position:relative; flex:1;">
                            <i class="fa fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9ca3af;"></i>
                            <input type="text" id="svcSelectorSearch" onkeyup="debounceFetchServices(this.value)" placeholder="Search for an accommodation" style="width:100%; padding:8px 12px 8px 36px; border:1px solid #d1d5db; border-radius:4px; font-size:13px; color:#4b5563; outline:none;">
                        </div>
                        <div style="width:140px; border:1px solid #d1d5db; border-radius:4px; display:flex; align-items:center; padding:0 12px; cursor:pointer;">
                            <img src="https://upload.wikimedia.org/wikipedia/en/c/c3/Flag_of_France.svg" style="width:16px; height:12px; object-fit:cover; margin-right:8px;">
                            <span style="font-size:13px; color:#4b5563; flex:1;">Français</span>
                            <i class="fa fa-caret-down" style="color:#9ca3af; font-size:12px;"></i>
                        </div>
                    </div>
                    
                    {{-- Subcategory Chips (Dynamically shown for Accommodations) --}}
                    <div id="svcSelectorSubCats" style="display:none; flex-wrap:wrap; gap:8px; margin-top:16px;">
                        <button onclick="filterServiceSelector('all', this)" class="svc-cat-chip svc-cat-chip-active">All</button>
                        <button onclick="filterServiceSelector('Camps', this)" class="svc-cat-chip">Camps</button>
                        <button onclick="filterServiceSelector('Homestay', this)" class="svc-cat-chip">Homestay</button>
                        <button onclick="filterServiceSelector('Hotels', this)" class="svc-cat-chip">Hotels</button>
                        <button onclick="filterServiceSelector('Mobile Camp', this)" class="svc-cat-chip">Mobile Camp</button>
                        <button onclick="filterServiceSelector('Wild Jordan RSCN', this)" class="svc-cat-chip">Wild Jordan RSCN</button>
                    </div>
                </div>

                {{-- List Container --}}
                <div style="flex:1; overflow-y:auto; border-top:1px solid #e5e7eb;">
                    <div id="mockedServiceList">
                        {{-- Items populated by JS --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>{{-- close panelDbd --}}

<div class="itin-panel" id="panelPrice" style="display:{{ (isset($activeTab)&&$activeTab==='price')?'block':'none' }}">
    @php
        /* ── Pax count first (needed for qty default) ── */
        $numPax = ($tripRequest->adults ?? 0) + ($tripRequest->children ?? 0);
        if($numPax < 1) $numPax = 1;
        $numAdults = $tripRequest->adults ?? $numPax;

        /* ── Collect all services from days ── */
        $allServices = [];
        $serviceTotal = 0;
        if($tripRequest->latestItinerary && $tripRequest->latestItinerary->days) {
            foreach($tripRequest->latestItinerary->days->sortBy('day_number') as $day) {
                $dayServices = $day->services ?? [];
                foreach($dayServices as $svc) {
                    $cost = floatval($svc['cost'] ?? 0);
                    $qty  = intval($svc['qty'] ?? $numPax);
                    if($qty < 1) $qty = 1;
                    $days = intval($svc['stay_duration'] ?? 1);
                    if($days < 1) $days = 1;
                    $lineTotal = $cost * $qty * $days;
                    $allServices[] = [
                        'day'          => $day->day_number,
                        'name'         => $svc['name'] ?? 'Service',
                        'vendor'       => $svc['vendor'] ?? '',
                        'cost'         => $cost,
                        'qty'          => $qty,
                        'days'         => $days,
                        'total'        => $lineTotal,
                        'type'         => $svc['type'] ?? '',
                    ];
                    $serviceTotal += $lineTotal;
                }
            }
        }

        $savedPpp = optional($tripRequest->latestItinerary)->price_per_person ?? null;
        $ppp = $savedPpp !== null ? $savedPpp : ($serviceTotal > 0 ? round($serviceTotal / $numPax, 2) : ($displayTotal > 0 ? round($displayTotal / $numPax, 2) : ''));
        $effectiveTotal = $displayTotal > 0 ? $displayTotal : $serviceTotal;
    @endphp

    {{-- ══ SERVICE COST BREAKDOWN CARD ══ --}}
    @if(count($allServices) > 0)
    <div class="ev-card">
        <div class="ev-dots"><span></span><span></span><span></span></div>
        <h3 onclick="toggleCard(this)">Service costs <i class="fa fa-chevron-up"></i></h3>
        <div class="card-body" style="margin-top:16px;padding:0">
            {{-- Table header --}}
            <div style="display:grid;grid-template-columns:50px 45px 45px 1fr 130px 85px 90px;gap:0;border-bottom:2px solid #e5e7eb;padding:8px 16px;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;">
                <div>Day</div>
                <div>QTY</div>
                <div>Days</div>
                <div>Description</div>
                <div>Vendor &amp; Status</div>
                <div style="text-align:right">Unit</div>
                <div style="text-align:right">Total</div>
            </div>
            {{-- Rows --}}
            @foreach($allServices as $svc)
            <div class="svc-cost-row" data-unit="{{ $svc['cost'] }}" style="display:grid;grid-template-columns:50px 45px 45px 1fr 130px 85px 90px;gap:0;padding:12px 16px;border-bottom:1px solid #f1f5f9;align-items:center;{{ $loop->even ? 'background:#fafafa;' : '' }}">
                <div style="font-size:13px;font-weight:700;color:#005e46;">D{{ $svc['day'] }}</div>
                <div><span style="background:#eff6ff;color:#3b82f6;font-size:11px;font-weight:700;padding:3px 8px;border-radius:4px;display:inline-block;">{{ $svc['qty'] }}</span></div>
                <div><span style="background:#fdf4ff;color:#9333ea;font-size:11px;font-weight:700;padding:3px 8px;border-radius:4px;display:inline-block;">{{ $svc['days'] }}d</span></div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:#1f2937;">{{ $svc['name'] }}</div>
                    @if($svc['type'])<div style="font-size:11px;color:#9ca3af;margin-top:2px;">{{ $svc['type'] }}</div>@endif
                </div>
                <div>
                    @if($svc['vendor'])
                    <div style="font-size:12px;font-weight:600;color:#374151;">{{ $svc['vendor'] }}</div>
                    @endif
                    <div style="display:inline-block;margin-top:3px;background:#fef2f2;color:#dc2626;font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;text-transform:uppercase;">UNPAID</div>
                </div>
                <div style="text-align:right;font-size:13px;color:#6b7280;">{{ number_format($svc['cost'], 2) }}</div>
                <div class="svc-row-total" style="text-align:right;font-size:14px;font-weight:700;color:#ea580c;">
                    {{ number_format($svc['total'], 2) }}
                    <span style="font-size:11px;color:#9ca3af;font-weight:400;">{{ $tripRequest->currency ?? 'JOD' }}</span>
                </div>
            </div>
            @endforeach
            {{-- Total row --}}
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 16px;background:#f8fafc;border-top:2px solid #e5e7eb;border-radius:0 0 12px 12px;">
                <span style="font-size:13px;font-weight:700;color:#374151;">TOTAL COST</span>
                <span style="font-size:20px;font-weight:800;color:#1f2937;"><span id="svcGrandTotal">{{ number_format($serviceTotal, 2) }}</span> <span style="font-size:13px;font-weight:400;color:#9ca3af;">{{ $tripRequest->currency ?? 'JOD' }}</span></span>
            </div>
        </div>
    </div>
    @endif

    {{-- ══ AGENCY COMMISSION CARD ══ --}}
    <div class="ev-card">
        <div class="ev-dots"><span></span><span></span><span></span></div>
        <h3 onclick="toggleCard(this)">Agency margin / Commission <i class="fa fa-chevron-up"></i></h3>
        <div class="card-body" style="margin-top:16px;">
            @php
                $savedCommission     = optional($tripRequest->latestItinerary)->agency_commission ?: 10.45;
                $savedCommissionType = optional($tripRequest->latestItinerary)->commission_type    ?: 'fixed';
            @endphp
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;align-items:end;">
                {{-- Service total (readonly) --}}
                <div>
                    <div style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Service Total (cost)</div>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 16px;font-size:18px;font-weight:800;color:#1f2937;">
                        {{ number_format($serviceTotal, 2) }} <span style="font-size:12px;color:#9ca3af;font-weight:400;">{{ $tripRequest->currency ?? 'JOD' }}</span>
                    </div>
                </div>
                {{-- Commission input --}}
                <div>
                    <div style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Agency Margin</div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <select id="commissionType" onchange="calcCommission()" style="border:1px solid #d1d5db;border-radius:6px;padding:10px 8px;font-size:13px;font-weight:600;color:#374151;background:#fff;cursor:pointer;">
                            <option value="percent" {{ $savedCommissionType=='percent'?'selected':'' }}>%</option>
                            <option value="fixed" {{ $savedCommissionType=='fixed'?'selected':'' }}>Fixed</option>
                        </select>
                        <div class="fl-group" style="margin:0;flex:1;">
                            <input type="number" min="0" step="0.01" class="fl-input" id="commissionVal" placeholder=" " value="{{ $savedCommission }}" oninput="calcCommission()">
                            <label class="fl-label">Commission</label>
                        </div>
                    </div>
                </div>
                {{-- Selling price (auto-calculated) --}}
                <div>
                    <div style="font-size:12px;font-weight:600;color:#005e46;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Selling Price (client)</div>
                    <div style="background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:12px 16px;font-size:18px;font-weight:800;color:#005e46;">
                        <span id="sellingPriceDisp">{{ number_format($serviceTotal, 2) }}</span>
                        <span style="font-size:12px;color:#047857;font-weight:400;"> {{ $tripRequest->currency ?? 'JOD' }}</span>
                    </div>
                </div>
            </div>
            <div style="margin-top:12px;font-size:12px;color:#9ca3af;">
                <i class="fa fa-info-circle"></i> Set your agency margin (% or fixed amount) to calculate the final price to show the client. The Trip price below will be updated automatically.
            </div>
        </div>
    </div>

    {{-- ══ TRIP PRICE CARD ══ --}}
    <div class="ev-card">
        <div class="ev-dots"><span></span><span></span><span></span></div>
        <h3 onclick="toggleCard(this)">Trip price <i class="fa fa-chevron-up"></i></h3>
        <div class="card-body">
            <div class="ev-alert">
                <i class="fa fa-info-circle"></i>
                <div><strong>Important</strong><br>If you need to adjust the number of pax please go to Request Manager. Please refresh this page when you've done the modifications in Request Manager.</div>
            </div>
            <div class="price-total-row">
                <div class="fl-group" style="margin:0;width:120px">
                    <select class="fl-input" id="currencySelect" style="padding:10px" onchange="calcTotal()">
                        <option value="USD" {{ ($tripRequest->currency??'')=='USD'?'selected':'' }}>USD</option>
                        <option value="EUR" {{ ($tripRequest->currency??'')=='EUR'?'selected':'' }}>EUR</option>
                        <option value="JOD" {{ ($tripRequest->currency??'')=='JOD'?'selected':'' }}>JOD</option>
                    </select>
                    <label class="fl-label">Currency</label>
                </div>
                <div style="text-align:right">
                    <div class="price-total-label">Total price including all taxes</div>
                    <div class="price-total-val"><span id="currencySymbol">{{ $currencySym }}</span><span id="totalDisp">{{ number_format($effectiveTotal, 2) }}</span></div>
                </div>
            </div>
            {{-- Per-traveller rows --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                <button type="button" onclick="recalcFromServices()" style="background:none;border:1px solid #005e46;color:#005e46;padding:5px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;"><i class="fa fa-refresh"></i> Sync from services</button>
                <div class="apply-all" style="margin:0">
                    <a href="#" onclick="applyToAll();return false">Apply to all</a> <i class="fa fa-info-circle"></i>
                </div>
            </div>
            @for($i = 0; $i < $numPax; $i++)
            <div class="price-row">
                <div class="check-icon">
                    @if($i === 0)
                        <i class="fa fa-check-circle" style="color:#005e46;font-size:20px;"></i>
                    @else
                        <i class="fa fa-circle-o" style="color:#d1d5db;font-size:20px;"></i>
                    @endif
                </div>
                <div class="fl-group" style="margin:0">
                    <select class="fl-input pax-type">
                        <option value="adult" {{ $i < $numAdults ? 'selected' : '' }}>adult</option>
                        <option value="child" {{ $i >= $numAdults ? 'selected' : '' }}>child</option>
                    </select>
                    <label class="fl-label">Pax type</label>
                </div>
                <div class="fl-group" style="margin:0">
                    <input class="fl-input pax-details" placeholder=" ">
                    <label class="fl-label">Price details</label>
                </div>
                <div class="fl-group" style="margin:0">
                    <input class="fl-input pax-price" type="number" placeholder="{{ $i === 0 ? ' ' : '-' }}" oninput="calcTotal()" value="{{ $i === 0 ? $ppp : '' }}">
                    <label class="fl-label">Price for this traveller</label>
                </div>
            </div>
            @endfor
        </div>
    </div>
        {{-- Price Details Card --}}
        <div class="ev-card">
            <div class="ev-dots"><span></span><span></span><span></span></div>
            <h3 onclick="toggleCard(this)">Price details <i class="fa fa-chevron-up"></i></h3>
            <div class="card-body" style="margin-top:16px">
                {{-- Hidden textareas for data storage --}}
                <textarea id="pInc" style="display:none">{{ $tripRequest->latestItinerary->price_includes ?? '' }}</textarea>
                <textarea id="pExc" style="display:none">{{ $tripRequest->latestItinerary->price_excludes ?? '' }}</textarea>
                {{-- Nights included in the price --}}
                <div style="margin-bottom:24px">
                    <div class="fl-group" style="margin-bottom:0">
                        <input type="text" class="fl-input" id="pNights" placeholder=" " value="{{ $tripRequest->latestItinerary->nights_included ?? '' }}">
                        <label class="fl-label">Nights included in the price</label>
                    </div>
                </div>
                {{-- This price includes --}}
                <div style="margin-bottom:24px">
                    <div style="font-size:14px;font-weight:600;color:#1a1a1a;margin-bottom:12px;">This price includes</div>
                    <div class="price-item-list" id="pIncList"></div>
                    <a href="#" onclick="addPriceItem('pIncList');return false;" style="color:#9ca3af;font-size:13px;font-weight:500;text-decoration:none;display:inline-block;margin-top:8px;padding:4px 0;">Add more</a>
                </div>
                {{-- The price does not include --}}
                <div style="margin-bottom:8px">
                    <div style="font-size:14px;font-weight:600;color:#1a1a1a;margin-bottom:12px;">The price does not include</div>
                    <div class="price-item-list" id="pExcList"></div>
                    <a href="#" onclick="addPriceItem('pExcList');return false;" style="color:#9ca3af;font-size:13px;font-weight:500;text-decoration:none;display:inline-block;margin-top:8px;padding:4px 0;">Add more</a>
                </div>
                {{-- Load from Library / Load defaults --}}
                <div style="display:flex;gap:8px;margin-top:16px;flex-wrap:wrap;">
                    <button type="button" onclick="openConditionsLibrary()" style="background:#005e46;border:1px solid #005e46;color:#fff;padding:6px 14px;border-radius:4px;font-size:12px;font-weight:600;cursor:pointer;"><i class="fa fa-book"></i> Load from Library</button>
                    <button type="button" onclick="loadDefaults()" style="background:none;border:1px solid #005e46;color:#005e46;padding:6px 14px;border-radius:4px;font-size:12px;font-weight:600;cursor:pointer;"><i class="fa fa-magic"></i> Load default templates</button>
                    <button type="button" onclick="clearPriceDetails()" style="background:none;border:1px solid #d1d5db;color:#666;padding:6px 14px;border-radius:4px;font-size:12px;font-weight:600;cursor:pointer;"><i class="fa fa-eraser"></i> Clear all</button>
                </div>
            </div>
        </div>

        {{-- Terms and Conditions Card --}}
        <div class="ev-card">
            <div class="ev-dots"><span></span><span></span><span></span></div>
            <h3 onclick="toggleCard(this)">Terms and conditions <i class="fa fa-chevron-up"></i></h3>
            <div class="card-body" style="margin-top:16px">
                @if(empty($tripRequest->latestItinerary->booking_conditions))
                <div class="ev-alert" style="background:#fff3e0;color:#b45309;border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;gap:10px;align-items:flex-start;font-size:13px;">
                    <i class="fa fa-exclamation-triangle" style="color:#f59e0b;font-size:16px;margin-top:1px;flex-shrink:0;"></i>
                    <div>Your cancellation conditions are empty. This field is mandatory to share this quote. For your next quotes, update your <a href="#" style="color:#005e46;font-weight:700;text-decoration:none;">Legal Information</a> app and they will be automatically pre-filled at creation.</div>
                </div>
                @endif
                {{-- Payment conditions --}}
                <div style="margin-bottom:20px;">
                    <div style="font-size:14px;font-weight:500;color:#374151;margin-bottom:8px;">Payment conditions</div>
                    <input type="text" id="pPayment" class="price-plain-input" placeholder="See payment conditions here" value="{{ $tripRequest->latestItinerary->payment_conditions ?? '' }}">
                    <div style="height:1px;background:#e5e7eb;margin-top:8px;"></div>
                </div>
                {{-- Terms and conditions for cancelation --}}
                <div style="margin-bottom:20px;">
                    <div style="font-size:14px;font-weight:500;color:#374151;margin-bottom:8px;">Terms and conditions for cancelation <span style="color:#ef4444;font-size:12px;">⊙ (required)</span></div>
                    <input type="text" id="pCond" class="price-plain-input" placeholder="Terms and conditions for cancellation here" value="{{ $tripRequest->latestItinerary->booking_conditions ?? '' }}">
                    <div style="height:1px;background:#e5e7eb;margin-top:8px;"></div>
                </div>
                {{-- Reduced mobility checkbox --}}
                <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
                    <input type="checkbox" id="pReducedMobility" style="width:16px;height:16px;cursor:pointer;accent-color:#005e46;" {{ optional($tripRequest->latestItinerary)->reduced_mobility ? 'checked' : '' }}>
                    <label for="pReducedMobility" style="font-size:14px;color:#374151;cursor:pointer;">This trip is adapted to reduced mobility person.</label>
                </div>
            </div>
        </div>

        {{-- Passports and Insurance Card --}}
        <div class="ev-card">
            <div class="ev-dots"><span></span><span></span><span></span></div>
            <h3 onclick="toggleCard(this)">Passports and insurance <i class="fa fa-chevron-up"></i></h3>
            <div class="card-body" style="margin-top:16px">
                <div style="margin-bottom:20px;">
                    <div style="font-size:14px;font-weight:500;color:#374151;margin-bottom:8px;">Passports and visas</div>
                    <input type="text" id="pPassports" class="price-plain-input" placeholder="" value="{{ $tripRequest->latestItinerary->passports_visas ?? '' }}">
                    <div style="height:1px;background:#e5e7eb;margin-top:8px;"></div>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:500;color:#374151;margin-bottom:8px;">Travel insurance</div>
                    <input type="text" id="pInsurance" class="price-plain-input" placeholder="" value="{{ $tripRequest->latestItinerary->travel_insurance ?? '' }}">
                    <div style="height:1px;background:#e5e7eb;margin-top:8px;"></div>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div style="max-width:720px;margin:0 auto 16px auto;">
            <button class="btn-save" onclick="savePrice()" style="width:100%;">Save Pricing</button>
        </div>

        {{-- Convert to Booking --}}
        <div style="max-width:720px;margin:0 auto 32px auto;">
            <div style="background:#fff;border:2px solid #005e46;border-radius:12px;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:15px;font-weight:800;color:#1f2937;margin-bottom:4px;">✅ Ready to book?</div>
                    <div style="font-size:12px;color:#6b7280;">This will create a real booking from the trip request data and the selling price set above.</div>
                </div>
                <form method="POST" action="{{ route('admin.request-manager.convert-to-booking', $tripRequest->id) }}" onsubmit="return confirm('Convert this trip request into a booking? This will create a new booking with the selling price calculated from services + agency commission.')">
                    @csrf
                    <button type="submit" style="background:linear-gradient(135deg,#005e46,#00a86b);color:#fff;border:none;padding:12px 28px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(0,94,70,0.35);white-space:nowrap;">
                        <i class="fa fa-check-circle"></i> Convert to Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Conditions Library Modal -->
<div id="conditionsLibraryModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:99999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;max-width:600px;width:100%;max-height:80vh;display:flex;flex-direction:column;box-shadow:0 25px 50px rgba(0,0,0,0.2);">
        <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:800;color:#1e293b;">📋 Conditions Library</h3>
                <p style="margin:4px 0 0;font-size:12px;color:#64748b;">Select items as Included or Excluded</p>
            </div>
            <button onclick="closeConditionsLibrary()" style="background:none;border:none;font-size:20px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <div id="conditionsLibraryBody" style="padding:16px 24px;overflow-y:auto;flex:1;">
            <p style="text-align:center;color:#94a3b8;padding:40px 0;">Loading...</p>
        </div>
        <div style="padding:16px 24px;border-top:1px solid #e2e8f0;display:flex;gap:8px;justify-content:flex-end;">
            <button onclick="closeConditionsLibrary()" style="padding:8px 20px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;color:#64748b;font-weight:600;font-size:13px;cursor:pointer;">Cancel</button>
            <button onclick="applyConditionsLibrary()" style="padding:8px 20px;border-radius:8px;border:none;background:#005e46;color:#fff;font-weight:600;font-size:13px;cursor:pointer;">Apply Selected</button>
        </div>
    </div>
</div>

<script>
var itinId={{ optional($tripRequest->latestItinerary)->id ?? 'null' }},reqId={{ $tripRequest->id }},csrf='{{ csrf_token() }}',browsingForAccom=false,calculatedSvcCost={{ $calculatedTotalCost }},numTravelers={{ ($tripRequest->adults ?? 0) + ($tripRequest->children ?? 0) ?: 1 }};

function decodeHtml(html) {
    if (!html) return '';
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}

function toggleDayDropdown(e) {
    e.stopPropagation();
    var menu = document.getElementById('dayDropdownMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
// Close dropdown on click outside
document.addEventListener('click', function() {
    var menu = document.getElementById('dayDropdownMenu');
    if (menu) menu.style.display = 'none';
});
function deleteCurrentDay() {
    var id = document.getElementById('editDayId').value;
    if (id) {
        delDay(id);
    }
}

function updateAllDayLabelsAndDates() {
    var sidebar = document.querySelector('#panelDbd .tp-sidebar');
    if (!sidebar) return;
    
    var cards = sidebar.querySelectorAll('.tp-day-card');
    var currentDayNum = 1;
    var arrival = document.getElementById('itinArrival').value;
    
    cards.forEach(function(card) {
        var duration = parseInt(card.getAttribute('data-duration')) || 1;
        var startDay = currentDayNum;
        var endDay = currentDayNum + duration - 1;
        
        // Update the card's day label text
        var labelEl = card.querySelector('.day-label');
        if (labelEl) {
            if (duration > 1) {
                labelEl.textContent = 'Day ' + startDay + ' and ' + endDay;
            } else {
                labelEl.textContent = 'Day ' + startDay;
            }
        }
        
        // If this card is active, update the editor badges
        if (card.classList.contains('active')) {
            var badgeText = 'D' + startDay;
            if (duration > 1) {
                badgeText += ' - D' + endDay;
            }
            document.getElementById('detailDayBadge').textContent = badgeText;
            
            // Format dates
            var dObjStart = arrival ? new Date(arrival) : new Date();
            dObjStart.setDate(dObjStart.getDate() + startDay - 1);
            var dateStrStart = dObjStart.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            if (duration > 1) {
                var dObjEnd = arrival ? new Date(arrival) : new Date();
                dObjEnd.setDate(dObjEnd.getDate() + endDay - 1);
                var dateStrEnd = dObjEnd.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                document.getElementById('detailDayDate').textContent = 'From ' + dateStrStart + ' to ' + dateStrEnd;
            } else {
                document.getElementById('detailDayDate').textContent = dateStrStart;
            }
        }
        
        currentDayNum = endDay + 1;
    });
}

function extendDuration() {
    document.getElementById('dayDropdownMenu').style.display = 'none';
    var id = document.getElementById('editDayId').value;
    if (!id) return;
    
    var activeCard = document.querySelector('.tp-day-card.active');
    if (!activeCard) return;
    
    var currentDuration = parseInt(activeCard.getAttribute('data-duration')) || 1;
    var newDuration = currentDuration + 1;
    
    activeCard.setAttribute('data-duration', newDuration);
    
    // Save to DB
    var d = { duration: newDuration };
    fetch('/admin/request-manager/' + reqId + '/itinerary/' + itinId + '/day/' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(d)
    }).then(function(r) { return r.json(); }).then(function(r) {
        if (r.success) {
            updateAllDayLabelsAndDates();
            toast('Duration extended to ' + newDuration + ' days');
        } else {
            activeCard.setAttribute('data-duration', currentDuration);
            updateAllDayLabelsAndDates();
            toast('Failed to extend duration');
        }
    }).catch(function() {
        activeCard.setAttribute('data-duration', currentDuration);
        updateAllDayLabelsAndDates();
        toast('Error extending duration');
    });
}

function reduceDuration() {
    document.getElementById('dayDropdownMenu').style.display = 'none';
    var id = document.getElementById('editDayId').value;
    if (!id) return;
    
    var activeCard = document.querySelector('.tp-day-card.active');
    if (!activeCard) return;
    
    var currentDuration = parseInt(activeCard.getAttribute('data-duration')) || 1;
    if (currentDuration <= 1) {
        toast('Cannot reduce duration below 1 day');
        return;
    }
    
    var newDuration = currentDuration - 1;
    activeCard.setAttribute('data-duration', newDuration);
    
    // Save to DB
    var d = { duration: newDuration };
    fetch('/admin/request-manager/' + reqId + '/itinerary/' + itinId + '/day/' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(d)
    }).then(function(r) { return r.json(); }).then(function(r) {
        if (r.success) {
            updateAllDayLabelsAndDates();
            toast('Duration reduced to ' + newDuration + ' days');
        } else {
            activeCard.setAttribute('data-duration', currentDuration);
            updateAllDayLabelsAndDates();
            toast('Failed to reduce duration');
        }
    }).catch(function() {
        activeCard.setAttribute('data-duration', currentDuration);
        updateAllDayLabelsAndDates();
        toast('Error reducing duration');
    });
}

var dayQuill=new Quill('#dayDescQuill',{theme:'snow',placeholder:'Describe activities...',modules:{toolbar:[['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],['link'],['clean']]}});
dayQuill.on('text-change',function(){debouncedAutoSaveDay();});

function toggleTpSidebar(){
    document.getElementById('tpAdminSidebar').classList.toggle('open');
    document.getElementById('tpSidebarOverlay').classList.toggle('open');
}

function toggleTpHeaderMenu(event) {
    event.stopPropagation();
    document.getElementById('tpHeaderMenu').classList.toggle('open');
}

document.addEventListener('click', function(e) {
    var menu = document.getElementById('tpHeaderMenu');
    if (menu && !menu.contains(e.target)) {
        menu.classList.remove('open');
    }
});

function updateCharCount(el,countId,max){document.getElementById(countId).textContent='('+el.value.length+'/'+max+')'}

var siteTags=[];
function addSiteTag(){var inp=document.getElementById('dayDest');var v=inp.value.trim();if(!v)return;siteTags.push(v);inp.value='';renderSiteTags();autoSaveDay();}

var JORDAN_DESTINATIONS = [
    'Amman','Petra','Wadi Rum','Aqaba','Dead Sea','Jerash','Madaba','Ajloun','Karak','Zarqa',
    'Salt','Irbid','Ma\'an','Tafilah','Mafraq','Azraq','Wadi Mujib','Dana','Shobak','Umm Qais',
    'Pella','Umm el-Jimal','Qasr Amra','Qasr Kharana','Hallabat','Azraq Wetland','Wadi Dana',
    'Wadi Al-Hasa','Little Petra','Beidha','Hammamat Ma\'in','Baptism Site','Mount Nebo','Nebo',
    'Bethany','Mukawir','Lot\'s Cave','Wadi Zarqa Ma\'in','Wadi Araba','Al-Karak','Al-Tafilah',
    'Dibeen','Ajloun Forest','Ramtha','Al-Mafraq','Jordan River','Wadi Seer','Fuhais'
];

function destAutocomplete(val) {
    var dd = document.getElementById('destAutocompleteDropdown');
    if (!val || val.length < 1) { dd.style.display = 'none'; return; }
    var q = val.toLowerCase();
    var matches = JORDAN_DESTINATIONS.filter(function(d){ return d.toLowerCase().indexOf(q) !== -1; });
    if (matches.length === 0) { dd.style.display = 'none'; return; }
    dd.innerHTML = matches.map(function(m) {
        return '<div style="padding:9px 14px;cursor:pointer;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;" '
            +'onmouseover="this.style.background=\'#f0fdf4\'" '
            +'onmouseout="this.style.background=\'#fff\'" '
            +'onclick="selectDestSuggestion(\''+m.replace(/'/g,"\\'")+'\')">' 
            +'<i class="fa fa-map-marker" style="color:#9ca3af;font-size:11px;margin-right:6px;"></i>'+m+'</div>';
    }).join('');
    dd.style.display = 'block';
}

function selectDestSuggestion(val) {
    document.getElementById('dayDest').value = val;
    closeDestAutocomplete();
    addSiteTag();
}

function closeDestAutocomplete() {
    var dd = document.getElementById('destAutocompleteDropdown');
    if (dd) dd.style.display = 'none';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('#dayDest') && !e.target.closest('#destAutocompleteDropdown')) {
        closeDestAutocomplete();
    }
});
function removeSiteTag(i){siteTags.splice(i,1);renderSiteTags();autoSaveDay()}
function renderSiteTags(){var c=document.getElementById('siteTags');c.innerHTML='';siteTags.forEach(function(t,i){c.innerHTML+='<span class="site-tag">'+t+' <span class="remove-tag" onclick="removeSiteTag('+i+')">&times;</span></span>'})}

function toast(m){var t=document.createElement('div');t.textContent=m;t.style.cssText='position:fixed;bottom:20px;right:20px;background:#f97316;color:#fff;padding:10px 20px;border-radius:6px;font-size:13px;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,.15)';document.body.appendChild(t);setTimeout(function(){t.remove()},2000)}

function toggleQuoteRow(el) {
    var wrapper = el.closest('.quote-row-wrapper');
    var details = wrapper.querySelector('.quote-details');
    var chevron = wrapper.querySelector('.chevron-toggle i');
    if (details.style.display === 'none' || details.style.display === '') {
        details.style.display = 'block';
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-up');
    } else {
        details.style.display = 'none';
        chevron.classList.remove('fa-chevron-up');
        chevron.classList.add('fa-chevron-down');
    }
}

function openPreviousQuotes(){
    document.getElementById('tpLanding').style.display='none';
    document.getElementById('tpPreviousQuotes').style.display='block';
}

function openEditor(){
    document.getElementById('tpLanding').style.display='none';
    document.getElementById('tpPreviousQuotes').style.display='none';
    document.getElementById('tpEditor').style.display='block';
    
    // Auto-select "My quote" tab
    var persTab = document.querySelector('.tp-editor-tabs a');
    if(persTab) showTab('pers', persTab);
}
function backToLanding(){
    document.getElementById('tpEditor').style.display='none';
    document.getElementById('tpPreviousQuotes').style.display='none';
    document.getElementById('tpLanding').style.display='block';
}

function openDayFormInline(){
    var df = document.getElementById('dayFormInline');
    var de = document.getElementById('dayEmpty');
    if(df) df.style.display='flex';
    if(de) de.style.display='none';
}
function closeDayFormInline(){
    var df = document.getElementById('dayFormInline');
    var de = document.getElementById('dayEmpty');
    if(df) df.style.display='none';
    if(de) de.style.display='flex';
}

function showTab(t,el){
    document.querySelectorAll('.itin-panel').forEach(function(p){p.style.display='none'});
    document.querySelectorAll('.tp-editor-tabs a').forEach(function(a){a.classList.remove('active')});
    var m={pers:'panelPers',dbd:'panelDbd',price:'panelPrice'};
    var p=document.getElementById(m[t]);
    if(p){p.style.display=t==='dbd'?'flex':'block'}
    
    if(t === 'dbd') {
        var activeDay = document.querySelector('.tp-day-card.active');
        if(!activeDay) activeDay = document.querySelector('.tp-day-card');
        if(activeDay && (document.getElementById('dayEmpty').style.display !== 'none' || !document.getElementById('editDayId').value)) {
            activeDay.click();
        }
    }
    el.classList.add('active');
}

function toggleCard(el){
    var body=el.parentElement.querySelector('.card-body');
    var icon=el.querySelector('i');
    if(body.style.display==='none'){body.style.display='';icon.className='fa fa-chevron-up'}
    else{body.style.display='none';icon.className='fa fa-chevron-down'}
}

function uploadCoverPhoto(input){
    if(!input.files||!input.files[0])return;
    var fd=new FormData();
    fd.append('photo',input.files[0]);
    fd.append('_token',csrf);
    fetch('/admin/request-manager/'+reqId+'/upload-cover',{
        method:'POST',body:fd
    }).then(function(r){return r.json()}).then(function(r){
        if(r.success){
            document.getElementById('coverImg').src=r.url;
            document.getElementById('itinCover').value=r.url;
            toast('Cover photo updated');
        } else { toast(r.message||'Upload failed'); }
    }).catch(function(){toast('Upload failed')});
    input.value='';
}

function saveItin(){
    var d={title:document.getElementById('itinTitle').value,traveler_surname:document.getElementById('itinSurname').value,language:document.getElementById('itinLang').value,arrival_date:document.getElementById('itinArrival').value,cover_photo:document.getElementById('itinCover').value,video_url:document.getElementById('itinVideoUrl').value};
    var u='/admin/request-manager/'+reqId+'/itinerary'+(itinId?'/'+itinId:'');
    fetch(u,{method:itinId?'PUT':'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify(d)}).then(function(r){return r.json()}).then(function(r){if(r.success){itinId=r.id;toast('Saved')}})
}

function addDay(){
    // Show the modal with saved day templates (no itinId check needed for browsing)
    openLibraryModal();
    closeDayFormInline();
    document.getElementById('dayEmpty').style.display='none';
    document.querySelectorAll('.tp-day-card').forEach(function(c){c.classList.remove('active')});
    var cards=document.querySelectorAll('.tp-day-card');
    var nextDay=cards.length+1;
    document.getElementById('modalDayBadge').textContent='D'+nextDay;
    // Calculate date based on arrival date
    var arrival=document.getElementById('itinArrival').value;
    if(arrival){
        var d=new Date(arrival);
        d.setDate(d.getDate()+nextDay-1);
        document.getElementById('modalDayDate').textContent=d.toLocaleDateString('en-US',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
    }
}

function createBlankDay(){
    if(!itinId){toast('Save personalization first');return}
    closeLibraryModal();
    openDayFormInline();
    document.getElementById('editDayId').value='';
    document.getElementById('dayTitle').value='';
    if(window.dayQuill)dayQuill.root.innerHTML='';
    ['mB','mL','mD'].forEach(function(x){document.getElementById(x).checked=false});
    document.getElementById('mealNone').checked=true;
    document.getElementById('mealChecks').style.display='none';
    siteTags=[];renderSiteTags();
    renderPhotos([]);
    document.getElementById('servicesContainer').innerHTML='';
    var cards=document.querySelectorAll('.tp-day-card');
    var nextDayNum = cards.length + 1;
    document.getElementById('detailDayBadge').textContent = 'D' + nextDayNum;
    var arrival=document.getElementById('itinArrival').value;
    if(arrival){
        var d=new Date(arrival);
        d.setDate(d.getDate() + nextDayNum - 1);
        document.getElementById('detailDayDate').textContent = d.toLocaleDateString('en-US',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
    } else {
        document.getElementById('detailDayDate').textContent = '';
    }
}

// ── Photo Upload ──
function renderPhotos(photos){
    var scroll=document.getElementById('photosScroll');
    scroll.innerHTML='';
    if(photos && photos.length){
        photos.forEach(function(url){
            // Encode spaces in URL to prevent broken images
            var safeUrl = url.replace(/ /g, '%20');
            scroll.innerHTML+='<div class="photo-item"><img src="'+safeUrl+'" alt="Photo" style="width:100%;height:100%;object-fit:cover;border-radius:6px;"><button class="photo-del" onclick="deleteDayPhoto(\''+url+'\')">&#x2715;</button></div>';
        });
    }
    scroll.innerHTML+='<div class="photo-item photo-add" onclick="document.getElementById(\'dayPhotoInput\').click()"><i class="fa fa-camera"></i></div>';
}

function uploadDayPhoto(input){
    var dayId=document.getElementById('editDayId').value;
    if(!dayId){toast('Save the day first before uploading photos');input.value='';return}
    if(!input.files||!input.files.length)return;
    var files=Array.from(input.files);

    // Show instant local previews before upload starts
    var scroll=document.getElementById('photosScroll');
    var addBtn=scroll.querySelector('.photo-add');
    files.forEach(function(file){
        var localUrl=URL.createObjectURL(file);
        var div=document.createElement('div');
        div.className='photo-item';
        div.style.cssText='width:160px;height:110px;border-radius:8px;background:#e0e0e0;flex-shrink:0;overflow:hidden;position:relative;display:flex;align-items:center;justify-content:center;';
        div.innerHTML='<img src="'+localUrl+'" alt="Preview" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">'
            +'<div style="position:absolute;inset:0;background:rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;">'
            +'<i class="fa fa-spinner fa-spin" style="color:#fff;font-size:20px;"></i></div>';
        scroll.insertBefore(div, addBtn);
    });

    input.value='';
    var idx=0;
    var total=files.length;
    function uploadOne(){
        if(idx>=total)return;
        var fd=new FormData();
        fd.append('photo',files[idx]);
        var current=idx+1;
        idx++;
        fetch('/admin/request-manager/'+reqId+'/itinerary/'+itinId+'/day/'+dayId+'/photo',{
            method:'POST',headers:{'X-CSRF-TOKEN':csrf},body:fd
        }).then(function(r){return r.json()}).then(function(r){
            if(r.success){renderPhotos(r.photos);toast(total>1?'Photo '+current+'/'+total+' uploaded':'Photo uploaded');}
            else{toast(r.message||'Upload failed');}
            uploadOne();
        }).catch(function(){toast('Upload failed');uploadOne();});
    }
    uploadOne();
}

function deleteDayPhoto(url){
    var dayId=document.getElementById('editDayId').value;
    if(!dayId)return;
    fetch('/admin/request-manager/'+reqId+'/itinerary/'+itinId+'/day/'+dayId+'/photo',{
        method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({url:url})
    }).then(function(r){return r.json()}).then(function(r){
        if(r.success){renderPhotos(r.photos);toast('Photo removed')}
    });
}

// ── Service Blocks (Activity / Transport / Accommodation) ──
var svcCounter=0;
function addServiceBlock(type, prefill){
    svcCounter++;
    prefill=prefill||{};
    var id='svc_'+svcCounter;
    
    var displayType = type;
    if(type === 'accommodations') displayType = 'Accommodation';
    if(type === 'activities') displayType = 'Activity';
    if(type === 'transport') displayType = 'Transport';
    if(type === 'guides') displayType = 'Guide';
    if(type === 'restaurants') displayType = 'Restaurant';

    var color = displayType==='Transport'?'#8b1553':(displayType==='Activity'?'#e65100':(displayType==='Guide'?'#d97706':(displayType==='Restaurant'?'#c05621':'#f97316')));
    var icon = displayType==='Transport'?'fa-car':(displayType==='Activity'?'fa-camera':(displayType==='Guide'?'fa-user-o':(displayType==='Restaurant'?'fa-cutlery':'fa-bed')));
    
    var html='<div class="svc-block" id="'+id+'" style="border:1px solid #e5e7eb;border-radius:4px;padding:16px;margin-bottom:12px;position:relative;background:#fff;display:flex;align-items:flex-start;">';
    
    html+='<input type="hidden" class="svc-name" data-type="'+type+'" value="'+(prefill.name||'').replace(/"/g,'&quot;')+'">';
    html+='<input type="hidden" class="svc-cost" value="'+(prefill.cost||'')+'">';
    html+='<input type="hidden" class="svc-vendor" value="'+(prefill.vendor||'').replace(/"/g,'&quot;')+'">';
    html+='<textarea class="svc-desc" style="display:none;" data-type="'+type+'">'+(prefill.description||'')+'</textarea>';
    html+='<input type="hidden" class="svc-image" value="'+(prefill.image||'').replace(/"/g,'&quot;')+'">';
    html+='<input type="hidden" class="svc-loc" value="'+(prefill.loc||'').replace(/"/g,'&quot;')+'">';

    html+='<button type="button" onclick="this.parentElement.remove();autoSaveDay();" style="position:absolute;top:10px;right:10px;background:transparent;border:none;font-size:16px;cursor:pointer;color:#9ca3af;line-height:1;">×</button>';

    if(displayType === 'Transport') {
        html+='<div style="width:60px;height:60px;border:1.5px solid '+color+';border-radius:4px;display:flex;align-items:center;justify-content:center;margin-right:14px;flex-shrink:0;"><i class="fa '+icon+'" style="color:'+color+';font-size:22px;"></i></div>';
    } else {
        var imgSrc = prefill.image || '';
        if(imgSrc && !imgSrc.startsWith('http') && !imgSrc.startsWith('/') && !imgSrc.startsWith('data:')) imgSrc = '/' + imgSrc;
        if(!imgSrc) imgSrc = 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&h=120&fit=crop';
        html+='<img src="'+imgSrc+'" style="width:80px;height:60px;border-radius:4px;object-fit:cover;margin-right:14px;flex-shrink:0;">';
    }

    html+='<div style="flex:1;min-width:0;padding-right:20px;">';
    
    html+='<div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">';
    if(displayType !== 'Transport') html+='<i class="fa '+icon+'" style="color:'+color+';font-size:13px;"></i>';
    html+='<div style="font-size:14px;font-weight:600;color:'+color+';overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">'+(prefill.name||'Library Service')+'</div>';
    html+='</div>';

    if(displayType === 'Transport') {
        html+='<div style="display:flex;align-items:center;font-size:11px;color:#1f2937;margin-bottom:6px;flex-wrap:wrap;gap:4px;">';
        html+='<span style="color:#4b5563;font-weight:500;">From</span>';
        html+='<div style="background:#f3f4f6;padding:2px 8px;border-radius:4px;display:inline-flex;align-items:center;gap:3px;"><i class="fa fa-map-marker" style="color:#9ca3af;font-size:10px;"></i><span>'+(prefill.loc||'Origin')+'</span></div>';
        html+='<span style="color:#4b5563;font-weight:500;">to</span>';
        html+='<div style="background:#f3f4f6;padding:2px 8px;border-radius:4px;display:inline-flex;align-items:center;gap:3px;"><i class="fa fa-map-marker" style="color:#9ca3af;font-size:10px;"></i><span>'+(prefill.destination||prefill.dest||'Destination')+'</span></div>';
        html+='</div>';
        if(prefill.distance||prefill.duration) {
            html+='<div style="font-size:11px;color:#6b7280;margin-bottom:4px;display:flex;align-items:center;gap:12px;">';
            if(prefill.distance) html+='<span><i class="fa fa-road" style="width:14px;"></i> '+prefill.distance+'</span>';
            if(prefill.duration) html+='<span><i class="fa fa-hourglass-half" style="width:14px;"></i> '+prefill.duration+'</span>';
            html+='</div>';
        }
    } else {
        html+='<div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;flex-wrap:wrap;">';
        if(prefill.loc) html+='<div style="background:#f3f4f6;padding:2px 8px;border-radius:4px;display:inline-flex;align-items:center;gap:3px;font-size:11px;color:#374151;"><i class="fa fa-map-marker" style="color:#9ca3af;font-size:10px;"></i>'+prefill.loc+'</div>';
        if(displayType==='Accommodation'&&prefill.stars) html+='<span style="color:#fbbf24;font-size:12px;">'+prefill.stars+'</span>';
        html+='</div>';
        if(prefill.description) {
            var snippet=prefill.description.replace(/<[^>]*>/g,'').substring(0,90);
            if(prefill.description.length>90)snippet+='...';
            html+='<div style="font-size:12px;color:#6b7280;margin-bottom:8px;line-height:1.4;">'+snippet+'</div>';
        }
    }

    // ── Cost × QTY × Days → Live Total row ──
    var unitCost = parseFloat(prefill.cost)||0;
    var initQty  = parseInt(prefill.qty)||1;
    var initDays = parseInt(prefill.stay_duration)||1;
    var initTotal = unitCost * initQty * initDays;

    html+='<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">';
    // Unit cost badge (readonly display)
    if(unitCost > 0) {
        html+='<div style="display:inline-flex;align-items:center;gap:4px;background:#ecfdf5;border:1px solid #6ee7b7;border-radius:4px;padding:2px 10px;font-size:12px;font-weight:700;color:#005e46;">'
            +'<i class="fa fa-tag" style="font-size:10px;"></i> '+unitCost.toFixed(2)
            +(prefill.vendor ? ' · '+prefill.vendor : '')
            +'</div>';
    }
    // QTY box
    html+='<div style="display:inline-flex;align-items:center;gap:4px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;padding:2px 8px;">'
        +'<span style="font-size:10px;color:#9ca3af;font-weight:600;text-transform:uppercase;">QTY</span>'
        +'<input type="number" min="1" value="'+(prefill.qty||1)+'" class="svc-qty" oninput="calcSvcTotal(this);autoSaveDay();" '
        +'style="width:38px;border:none;background:transparent;font-size:12px;font-weight:700;color:#374151;padding:0;text-align:center;" title="Quantity">'
        +'</div>';
    // Duration of Stay box
    html+='<div style="display:inline-flex;align-items:center;gap:5px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:4px;padding:3px 10px;" title="Duration of stay (nights)">'
        +'<i class="fa fa-bed" style="font-size:11px;color:#0284c7;"></i>'
        +'<span style="font-size:10px;color:#0284c7;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;">Duration of Stay</span>'
        +'<input type="number" min="1" value="'+(parseInt(prefill.stay_duration)||1)+'" class="svc-stay-duration" oninput="calcSvcTotal(this);autoSaveDay();" '
        +'style="width:34px;border:none;background:transparent;font-size:12px;font-weight:800;color:#0c4a6e;padding:0 2px;text-align:center;" title="Number of nights/days">'
        +'<span style="font-size:10px;color:#0284c7;font-weight:500;">nt(s)</span>'
        +'</div>';
    // = Total
    html+='<div style="display:inline-flex;align-items:center;gap:4px;background:#fff7ed;border:1px solid #fed7aa;border-radius:4px;padding:2px 10px;">'
        +'<span style="font-size:10px;color:#9ca3af;font-weight:600;">=</span>'
        +'<span class="svc-line-total" style="font-size:13px;font-weight:800;color:#ea580c;">'+(unitCost>0?initTotal.toFixed(2):'—')+'</span>'
        +'</div>';
    html+='</div>';

    html+='<button type="button" style="background:#fff;border:1px solid #d1d5db;border-radius:3px;padding:4px 12px;font-size:11px;font-weight:600;color:#374151;cursor:pointer;display:inline-flex;align-items:center;gap:4px;margin-top:4px;"><i class="fa fa-plus" style="font-size:9px;"></i> Add an alternative</button>';

    html+='</div></div>';
    document.getElementById('servicesContainer').insertAdjacentHTML('beforeend',html);
}

function collectServices(){
    var svcs=[];
    document.querySelectorAll('.svc-block').forEach(function(b){
        var nameEl=b.querySelector('.svc-name');
        var descEl=b.querySelector('.svc-desc');
        var costEl=b.querySelector('.svc-cost');
        var qtyEl=b.querySelector('.svc-qty');
        var durEl=b.querySelector('.svc-stay-duration');
        var vendorEl=b.querySelector('.svc-vendor');
        var imgEl=b.querySelector('.svc-image');
        var locEl=b.querySelector('.svc-loc');
        var type=nameEl?nameEl.getAttribute('data-type'):'';
        var svc={
            type:type,
            name:nameEl?nameEl.value:'',
            description:descEl?descEl.value:'',
            cost:costEl?costEl.value:'',
            qty:qtyEl?qtyEl.value:'1',
            stay_duration:durEl?durEl.value:'',
            vendor:vendorEl?vendorEl.value:'',
            image:imgEl?imgEl.value:'',
            loc:locEl?locEl.value:''
        };
        svcs.push(svc);
    });
    return svcs;
}

function renderServices(services){
    var container=document.getElementById('servicesContainer');
    container.innerHTML='';
    if(!services||!services.length)return;
    services.forEach(function(s){
        addServiceBlock(s.type||'Service',{
            name:s.name||'',
            cost:s.cost||'',
            qty:s.qty||1,
            stay_duration:s.stay_duration||'',
            vendor:s.vendor||'',
            description:s.description||'',
            image:s.image||'',
            loc:s.loc||''
        });
    });
}

function filterLibCategory(cat,btn){
    document.querySelectorAll('.lib-item').forEach(function(c){
        c.style.display='';
    });
}

function openLibraryModal(){
    var overlay=document.getElementById('libraryModalOverlay');
    overlay.style.display='block';
    document.body.style.overflow='hidden';
    document.querySelectorAll('.lib-item').forEach(function(c){
        c.style.display='';
    });
}

function searchLibrary(q){
    q=q.toLowerCase();
    document.querySelectorAll('.lib-item').forEach(function(el){
        var t=(el.getAttribute('data-title')||'').toLowerCase();
        var d=(el.getAttribute('data-desc')||'').toLowerCase();
        if(t.includes(q)||d.includes(q)){el.style.display='block'}else{el.style.display='none'}
    });
}

function useSavedDay(el){
    if(!itinId){toast('Save personalization first');return}
    // Read all data safely from DOM attributes
    var cannedDayId = el.getAttribute('data-id') || '';
    var title = el.getAttribute('data-title') || '';
    var desc = el.getAttribute('data-desc') || '';
    var imgsRaw = el.getAttribute('data-images') || '[]';
    var imgsArr = [];
    try { imgsArr = JSON.parse(imgsRaw); if(!Array.isArray(imgsArr)) imgsArr = []; } catch(e){ imgsArr = []; }
    // Normalize paths
    imgsArr = imgsArr.filter(function(u){ return !!u; }).map(function(u){
        return (u.indexOf('http') === 0 || u.indexOf('/') === 0) ? u : '/' + u;
    });
    var firstImg = imgsArr.length > 0 ? imgsArr[0] : null;
    var d={title:title,description:desc,photos:imgsArr,canned_day_id:cannedDayId};
    fetch('/admin/request-manager/'+reqId+'/itinerary/'+itinId+'/day',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},
        body:JSON.stringify(d)
    }).then(function(r){return r.json()}).then(function(r){
        if(r.success && r.id){
            closeLibraryModal();
            // Force stay on Day by Day panel
            document.querySelectorAll('.itin-panel').forEach(function(p){p.style.display='none'});
            var dbdPanel = document.getElementById('panelDbd');
            if(dbdPanel) dbdPanel.style.display='flex';
            // Add day card to sidebar
            var sidebar = document.querySelector('#panelDbd .tp-sidebar');
            var emptyMsg = sidebar.querySelector('div[style*="text-align:center"]');
            if(emptyMsg) emptyMsg.remove();
            var cards = sidebar.querySelectorAll('.tp-day-card');
            var dayNum = cards.length + 1;
            var newCard = document.createElement('div');
            newCard.className = 'tp-day-card active';
            newCard.setAttribute('data-day-id', r.id);
            newCard.setAttribute('onclick', 'selDay('+r.id+',this)');
            // Use photos returned by server (from canned day DB lookup)
            var serverPhotos = r.photos || imgsArr;
            var thumbImg = (serverPhotos.length > 0) ? serverPhotos[0] : firstImg;
            var thumbHtml = thumbImg ? '<div class="tp-day-thumb" style="background-image:url(\''+thumbImg+'\');background-size:cover;background-position:center;"></div>' : '<div class="tp-day-thumb"><i class="fa fa-image"></i></div>';
            newCard.innerHTML = thumbHtml + '<div class="tp-day-info"><div class="day-label">Day '+dayNum+'</div><div class="day-title">'+(title||'Untitled')+'</div><div class="day-loc"><i class="fa fa-map-marker"></i> No location</div></div>';
            document.querySelectorAll('.tp-day-card').forEach(function(c){c.classList.remove('active')});
            newCard.classList.add('active');
            var addBtn = sidebar.querySelector('.btn-add-day');
            sidebar.insertBefore(newCard, addBtn);
            if(typeof initDragAndDrop === 'function') initDragAndDrop();
            // Open the day editor form immediately with known data
            openDayFormInline();
            document.getElementById('editDayId').value = r.id;
            document.getElementById('dayTitle').value = title || '';
            if(typeof updateCharCount === 'function') updateCharCount(document.getElementById('dayTitle'),'dayTitleCount',255);
            if(window.dayQuill)dayQuill.root.innerHTML=decodeHtml(desc||'');
            // Set details badge and date
            document.getElementById('detailDayBadge').textContent = 'D' + dayNum;
            var arrival=document.getElementById('itinArrival').value;
            if(arrival){
                var dObj=new Date(arrival);
                dObj.setDate(dObj.getDate() + dayNum - 1);
                document.getElementById('detailDayDate').textContent = dObj.toLocaleDateString('en-US',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
            } else {
                document.getElementById('detailDayDate').textContent = '';
            }
            // Render ALL images from server (canned day images from DB)
            renderPhotos(serverPhotos);

            document.getElementById('mealNone').checked = true;
            document.getElementById('mealChecks').style.display = 'none';
            document.getElementById('mB').checked = false;
            document.getElementById('mL').checked = false;
            document.getElementById('mD').checked = false;
            document.getElementById('servicesContainer').innerHTML = '';
            siteTags = []; renderSiteTags();
            toast('Day added — you can now edit it');
        } else {
            toast('Error adding day');
        }
    }).catch(function(err){
        console.error('useSavedDay error:', err);
        toast('Error adding day');
    });
}
function closeLibraryModal(){
    document.getElementById('libraryModalOverlay').style.display='none';
    document.body.style.overflow='';
}
function openLibraryForService(catPattern){
    closeDayFormInline();
    browsingForAccom=true;
    closeDayFormInline();
    openLibraryModal('accommod');
}

// ── Service Selector Modal ──
var currentServiceType = 'accommodations';
var svcSearchTimer = null;

function openServiceSelector(catFilter) {
    closeDayFormInline();
    document.getElementById('serviceSelectorOverlay').style.display = 'block';
    document.body.style.overflow = 'hidden';
    currentSubCatFilter = ''; // Reset subcat

    
    var title = "Accommodation";
    var btnText = "ADD ACCOMMODATION";
    var searchPlaceholder = "Search for an accommodation";
    var color = "#f97316";
    currentServiceType = 'accommodations';
    
    if(catFilter === 'activ') { 
        title = "Activity"; 
        btnText = "CREATE ACTIVITY"; 
        searchPlaceholder = "Search for an activity"; 
        currentServiceType = 'activities'; 
        color = "#e65100";
    }
    else if(catFilter === 'transport') { 
        title = "Transport"; 
        btnText = "CREATE TRANSPORT TYPE"; 
        searchPlaceholder = "Search for transport"; 
        currentServiceType = 'transport'; 
        color = "#8b1553";
    }
    else if(catFilter === 'hotels') {
        title = "Hotel";
        btnText = "ADD HOTEL";
        searchPlaceholder = "Search for a hotel";
        currentServiceType = 'hotels';
        color = "#f97316";
    }
    else if(catFilter === 'restaurants') {
        title = "Restaurant";
        btnText = "ADD RESTAURANT";
        searchPlaceholder = "Search for a restaurant";
        currentServiceType = 'restaurants';
        color = "#c05621";
    }
    else if(catFilter === 'guides') {
        title = "Guide";
        btnText = "ADD GUIDE";
        searchPlaceholder = "Search for a guide";
        currentServiceType = 'guides';
        color = "#d97706";
    }
    
    document.getElementById('svcSelectorTitle').textContent = title;
    document.getElementById('svcSelectorTitle').style.color = color;
    
    var btn = document.getElementById('svcSelectorAddBtn');
    btn.textContent = btnText;
    btn.parentElement.style.color = color;
    
    document.getElementById('svcSelectorSearch').placeholder = searchPlaceholder;
    document.getElementById('svcSelectorSearch').value = '';
    
    // Show/hide subcategories for Accommodations
    if(currentServiceType === 'accommodations') {
        document.getElementById('svcSelectorSubCats').style.display = 'flex';
        var chips = document.querySelectorAll('#svcSelectorSubCats .svc-cat-chip');
        chips.forEach(c => c.className = 'svc-cat-chip');
        if(chips.length > 0) chips[0].className = 'svc-cat-chip svc-cat-chip-active';
    } else {
        document.getElementById('svcSelectorSubCats').style.display = 'none';
    }
    
    fetchServicesForSelector('');
}

var currentSubCatFilter = '';
function filterServiceSelector(subCat, btn) {
    document.querySelectorAll('#svcSelectorSubCats .svc-cat-chip').forEach(c => c.className = 'svc-cat-chip');
    btn.className = 'svc-cat-chip svc-cat-chip-active';
    currentSubCatFilter = subCat === 'all' ? '' : subCat;
    fetchServicesForSelector(document.getElementById('svcSelectorSearch').value);
}

function debounceFetchServices(query) {
    clearTimeout(svcSearchTimer);
    svcSearchTimer = setTimeout(function() {
        fetchServicesForSelector(query);
    }, 500);
}

// ── State for two-step provider→service drill-down ──
var svcCachedItems = [];
var svcCurrentView = 'providers'; // 'providers' | 'services'
var svcCurrentProvider = '';

function fetchServicesForSelector(query) {
    var list = document.getElementById('mockedServiceList');
    var color = currentServiceType === 'transport' ? '#8b1553' : (currentServiceType === 'activities' ? '#e65100' : '#f97316');
    var hoverBg = currentServiceType === 'transport' ? '#fcf7f9' : '#f9fafb';

    list.innerHTML = '<div style="padding:40px;text-align:center;color:'+color+';"><i class="fa fa-spinner fa-spin fa-2x"></i></div>';

    fetch('/admin/request-manager/search-library?type=' + currentServiceType + '&q=' + encodeURIComponent(query) + '&subCat=' + encodeURIComponent(currentSubCatFilter))
    .then(r => r.json())
    .then(data => {
        svcCachedItems = (data && data.items) ? data.items : [];
        svcCurrentView = 'providers';
        svcCurrentProvider = '';
        renderProviderList(svcCachedItems, color, hoverBg);
    })
    .catch(function(error) {
        console.error('Error fetching services:', error);
        list.innerHTML = '<div style="padding:40px;text-align:center;color:#ef4444;font-size:14px;font-weight:600;">Failed to load services. Please try again.</div>';
    });
}

function renderProviderList(items, color, hoverBg) {
    var list = document.getElementById('mockedServiceList');
    if (!items || items.length === 0) {
        list.innerHTML = '<div style="padding:40px;text-align:center;color:#9ca3af;font-size:14px;font-weight:600;">No providers found matching your search.</div>';
        return;
    }

    var icon = 'fa-bed';
    if(currentServiceType === 'transport') icon = 'fa-car';
    if(currentServiceType === 'activities') icon = 'fa-camera';
    if(currentServiceType === 'restaurants') icon = 'fa-cutlery';
    if(currentServiceType === 'guides') icon = 'fa-user-o';

    // Group by vendor
    var providers = {};
    items.forEach(function(item) {
        var vendor = (item.vendor && item.vendor.trim()) ? item.vendor.trim() : (item.category || 'Other');
        if (!providers[vendor]) providers[vendor] = [];
        providers[vendor].push(item);
    });

    var html = '';
    Object.keys(providers).sort().forEach(function(vendorName) {
        var svcs = providers[vendorName];
        var firstImg = svcs[0].image || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=100&h=100&fit=crop';
        var firstLoc = svcs[0].category || svcs[0].arrival || 'Jordan';
        var vendorEsc = vendorName.replace(/\\/g,'\\\\').replace(/'/g,"\\'");

        var mediaHtml = currentServiceType === 'transport'
            ? '<div style="width:54px;height:54px;border:1px solid '+color+';display:flex;align-items:center;justify-content:center;margin-right:16px;flex-shrink:0;border-radius:4px;"><i class="fa '+icon+'" style="color:'+color+';font-size:20px;"></i></div>'
            : '<img src="'+firstImg+'" style="width:64px;height:64px;border-radius:4px;object-fit:cover;margin-right:16px;flex-shrink:0;">';

        html += '<div style="display:flex;align-items:center;padding:14px 24px;border-bottom:1px solid #e5e7eb;cursor:pointer;transition:background 0.15s;"'
            +' onmouseover="this.style.background=\''+hoverBg+'\'"'
            +' onmouseout="this.style.background=\'transparent\'"'
            +' onclick="showProviderServices(\''+vendorEsc+'\')">'
            + mediaHtml
            +'<div style="flex:1;overflow:hidden;">'
              +'<div style="display:flex;align-items:center;gap:6px;">'
                +'<i class="fa '+icon+'" style="color:'+color+';font-size:12px;"></i>'
                +'<span style="color:'+color+';font-size:14px;font-weight:700;">'+vendorName+'</span>'
              +'</div>'
              +'<div style="display:flex;align-items:center;gap:4px;margin-top:4px;">'
                +'<i class="fa fa-map-marker" style="color:#9ca3af;font-size:11px;"></i>'
                +'<span style="color:#4b5563;font-size:11px;">'+firstLoc+'</span>'
              +'</div>'
              +'<div style="margin-top:4px;font-size:11px;color:#9ca3af;font-weight:500;">'+svcs.length+' service'+(svcs.length>1?'s':'')+' available</div>'
            +'</div>'
            +'<i class="fa fa-chevron-right" style="color:#d1d5db;font-size:13px;margin-left:8px;flex-shrink:0;"></i>'
            +'</div>';
    });
    list.innerHTML = html;
}

function showProviderServices(vendorName) {
    svcCurrentView = 'services';
    svcCurrentProvider = vendorName;

    var color = currentServiceType === 'transport' ? '#8b1553' : (currentServiceType === 'activities' ? '#e65100' : '#f97316');
    var hoverBg = currentServiceType === 'transport' ? '#fcf7f9' : '#f9fafb';
    var icon = 'fa-bed';
    if(currentServiceType === 'transport') icon = 'fa-car';
    if(currentServiceType === 'activities') icon = 'fa-camera';
    if(currentServiceType === 'restaurants') icon = 'fa-cutlery';
    if(currentServiceType === 'guides') icon = 'fa-user-o';

    var filteredItems = svcCachedItems.filter(function(item) {
        var v = (item.vendor && item.vendor.trim()) ? item.vendor.trim() : (item.category || 'Other');
        return v === vendorName;
    });

    var list = document.getElementById('mockedServiceList');

    var html = '<div style="display:flex;align-items:center;gap:8px;padding:10px 24px;background:#f8fafc;border-bottom:2px solid #e5e7eb;cursor:pointer;" onclick="backToProviders()">'
        +'<i class="fa fa-arrow-left" style="color:'+color+';font-size:12px;"></i>'
        +'<span style="font-size:11px;color:#6b7280;font-weight:500;">All providers</span>'
        +'<i class="fa fa-chevron-right" style="color:#d1d5db;font-size:10px;"></i>'
        +'<span style="font-size:13px;font-weight:700;color:'+color+';">'+vendorName+'</span>'
        +'</div>';

    if (filteredItems.length === 0) {
        html += '<div style="padding:40px;text-align:center;color:#9ca3af;font-size:14px;font-weight:600;">No services found for this provider.</div>';
    } else {
        filteredItems.forEach(function(item) {
            var titleEsc = (item.title||'Untitled').replace(/\\/g,'\\\\').replace(/'/g,"\\'");
            var notesEsc = (item.description||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'");
            var img = item.image || 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=100&h=100&fit=crop';
            var cost = item.cost || 0;
            var loc = item.category || 'Jordan';
            var locEsc = loc.replace(/'/g,"\\'");

            var mediaHtml = currentServiceType === 'transport'
                ? '<div style="width:54px;height:54px;border:1px solid '+color+';display:flex;align-items:center;justify-content:center;margin-right:16px;flex-shrink:0;"><i class="fa '+icon+'" style="color:'+color+';font-size:20px;"></i></div>'
                : '<img src="'+img+'" style="width:64px;height:64px;border-radius:4px;object-fit:cover;margin-right:16px;flex-shrink:0;">';

            var locHtml = '<div style="display:flex;align-items:center;gap:4px;margin-top:4px;"><i class="fa fa-map-marker" style="color:#9ca3af;font-size:11px;"></i><span style="color:#4b5563;font-size:11px;">'+loc+'</span></div>';

            html += '<div style="display:flex;align-items:center;padding:16px 24px;border-bottom:1px solid #e5e7eb;cursor:pointer;transition:background 0.15s;"'
                +' onmouseover="this.style.background=\''+hoverBg+'\'"'
                +' onmouseout="this.style.background=\'transparent\'"'
                +' onclick="selectTpService('+item.id+',\''+currentServiceType+'\',\''+titleEsc+'\',\''+cost+'\',\'\',\''+notesEsc+'\',\''+img.replace(/'/g,"\\'")+'\',\''+locEsc+'\')">'
                + mediaHtml
                +'<div style="flex:1;overflow:hidden;">'
                  +'<div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">'
                    +(currentServiceType!=='transport'?'<i class="fa '+icon+'" style="color:'+color+';font-size:12px;"></i>':'')
                    +'<span style="color:'+color+';font-size:14px;font-weight:600;">'+item.title+'</span>'
                    +(cost>0?'<span style="display:inline-flex;align-items:center;gap:3px;background:#ecfdf5;border:1px solid #6ee7b7;border-radius:4px;padding:1px 8px;font-size:12px;font-weight:700;color:#005e46;"><i class="fa fa-tag" style="font-size:9px;"></i> '+parseFloat(cost).toFixed(2)+'</span>':'')
                  +'</div>'
                  + locHtml
                  +'<div style="color:#4b5563;font-size:12px;margin-top:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">'+(item.description||'No description available.')+'</div>'
                +'</div>'
                +'</div>';
        });
    }
    list.innerHTML = html;
}

function backToProviders() {
    svcCurrentView = 'providers';
    svcCurrentProvider = '';
    var color = currentServiceType === 'transport' ? '#8b1553' : (currentServiceType === 'activities' ? '#e65100' : '#f97316');
    var hoverBg = currentServiceType === 'transport' ? '#fcf7f9' : '#f9fafb';
    renderProviderList(svcCachedItems, color, hoverBg);
}

function closeServiceSelector(){
    document.getElementById('serviceSelectorOverlay').style.display='none';
    document.body.style.overflow='';
}



function toggleChildren(el){
    var container=el.closest('.category-item');
    var children=container?container.querySelector('.category-children'):null;
    var icon=el.querySelector('i');
    if(children){
        if(children.classList.contains('hidden')){children.classList.remove('hidden');icon.className='fa fa-minus-square-o';}
        else{children.classList.add('hidden');icon.className='fa fa-plus-square-o';}
    }
}

function tpCollapseAll(){
    document.querySelectorAll('#tp_category_tree .category-children').forEach(function(el){el.classList.add('hidden')});
    document.querySelectorAll('#tp_category_tree .category-toggle i').forEach(function(el){el.className='fa fa-plus-square-o'});
}
function tpExpandAll(){
    document.querySelectorAll('#tp_category_tree .category-children').forEach(function(el){el.classList.remove('hidden')});
    document.querySelectorAll('#tp_category_tree .category-toggle i').forEach(function(el){el.className='fa fa-minus-square-o'});
}
function filterTpCategories(q){
    q=q.toLowerCase();
    document.querySelectorAll('#tp_category_tree .category-item').forEach(function(item){
        var name=item.getAttribute('data-name')||'';
        if(!q || name.indexOf(q)!==-1){
            item.classList.remove('hidden');
            var cur=item.closest('.category-children');
            while(cur){cur.classList.remove('hidden');cur=cur.parentElement?cur.parentElement.closest('.category-children'):null;}
        } else {
            item.classList.add('hidden');
        }
    });
}

function loadTpServices(categoryId, vendorFilter){
    var list=document.getElementById('tp_services_list');
    list.innerHTML='<div style="flex:1;display:flex;align-items:center;justify-content:center"><i class="fa fa-spinner fa-spin" style="font-size:28px;color:#f97316"></i></div>';
    var url='/admin/expenses/services?category='+categoryId;
    if(vendorFilter) url+='&vender='+vendorFilter;
    fetch(url).then(function(r){return r.json()}).then(function(data){
        var html='<div style="margin-bottom:16px"><h4 style="font-size:15px;font-weight:800;color:#1e293b;margin:0">'+data.categoryName+'</h4><p style="font-size:11px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-top:4px">Available Services</p></div>';
        html+='<div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#f8fafc;border-radius:12px;margin-bottom:16px"><i class="fa fa-filter" style="color:#94a3b8"></i><select onchange="loadTpServices('+categoryId+',this.value)" style="flex:1;background:transparent;border:none;font-size:12px;font-weight:700;color:#475569;outline:none"><option value="">All Vendors</option>';
        if(data.vendors){Object.entries(data.vendors).forEach(function(e){html+='<option value="'+e[0]+'"'+(vendorFilter==e[0]?' selected':'')+'>'+e[1]+'</option>'});}
        html+='</select></div>';
        html+='<div style="max-height:350px;overflow-y:auto;padding-right:4px">';
        if(data.services && data.services.length>0){
            data.services.forEach(function(s){
                var titleEsc = (s.description||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,"&quot;");
                var vendorEsc = (s.vender_name||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,"&quot;");
                var notesEsc = (s.notes||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,"&quot;").replace(/\n/g,"\\n").replace(/\r/g,"\\r");
                html+='<div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:#fff;border:1px solid #f1f5f9;border-radius:14px;margin-bottom:8px;transition:all 0.15s" onmouseover="this.style.borderColor=\'#ffedd5\';this.style.boxShadow=\'0 4px 12px rgba(0,0,0,0.06)\'" onmouseout="this.style.borderColor=\'#f1f5f9\';this.style.boxShadow=\'none\'">';
                html+='<div style="flex:1"><div style="font-size:13px;font-weight:700;color:#1e293b">'+(s.description||'')+'</div><div style="font-size:10px;color:#94a3b8;margin-top:3px;font-weight:600">'+(s.vender_name||'')+'</div></div>';
                html+='<div style="text-align:right;padding:0 16px"><div style="font-size:13px;font-weight:900;color:#f97316">'+(s.cost||'0.00')+' <small style="font-size:10px;color:#94a3b8;font-weight:700">JOD</small></div></div>';
                html+='<button type="button" onclick="selectTpService('+s.id+',\''+titleEsc+'\',\''+(s.cost_raw||'')+'\',\''+vendorEsc+'\',\''+notesEsc+'\')" style="padding:6px 14px;background:#fff7ed;color:#f97316;border:none;border-radius:8px;font-size:11px;font-weight:800;cursor:pointer;transition:all 0.15s" onmouseover="this.style.background=\'#f97316\';this.style.color=\'#fff\'" onmouseout="this.style.background=\'#fff7ed\';this.style.color=\'#f97316\'">SELECT</button>';
                html+='</div>';
            });
        } else {
            html+='<div style="padding:40px 0;text-align:center"><i class="fa fa-info-circle" style="font-size:24px;color:#cbd5e1;margin-bottom:10px;display:block"></i><p style="font-size:13px;color:#94a3b8;font-weight:600">No services found for this category.</p></div>';
        }
        html+='</div>';
        list.innerHTML=html;
    }).catch(function(err){list.innerHTML='<div style="padding:16px;background:#fef2f2;color:#dc2626;border-radius:12px;font-size:13px">Error: '+err.message+'</div>'});
}

function selectTpService(svcId, p1, p2, p3, p4, p5, p6, p7){
    var title, cost, vendor, notes, type, image, loc;
    if(p1 === 'accommodations' || p1 === 'activities' || p1 === 'transport' || p1 === 'restaurants' || p1 === 'guides') {
        type = p1;
        title = p2;
        cost = p3;
        vendor = p4;
        notes = p5;
        image = p6;
        loc = p7;
    } else {
        type = 'Service';
        title = p1;
        cost = p2;
        vendor = p3;
        notes = p4;
        image = '';
        loc = '';
    }



    var editDayId=document.getElementById('editDayId').value;
    var prefill={name:title,cost:cost,vendor:vendor||'',description:notes||'',image:image||'',loc:loc||''};
    if(!editDayId){
        closeServiceSelector();
        openDayFormInline();
        addServiceBlock(type,prefill);
        toast('Service added: '+title);
        return;
    }
    
    var svc={type:type,name:title,description:notes||'',cost:cost,vendor:vendor||'',image:image||'',loc:loc||''};
    var existing=collectServices();
    existing.push(svc);
    var dayData={services:existing};
    
    fetch('/admin/request-manager/'+reqId+'/itinerary/'+itinId+'/day/'+editDayId,{
        method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify(dayData)
    }).then(function(r){return r.json()}).then(function(r){
        if(r.success){
            addServiceBlock(type,prefill);
            closeServiceSelector();
            openDayFormInline();
            toast('Service added: '+title);
        }
    });
}

function selDay(id,el){
    closeLibraryModal();
    // Ensure Day by Day panel is visible
    document.querySelectorAll('.itin-panel').forEach(function(p){p.style.display='none'});
    var dbdPanel = document.getElementById('panelDbd');
    if(dbdPanel) dbdPanel.style.display='flex';
    openDayFormInline();
    document.querySelectorAll('.tp-day-card').forEach(function(c){c.classList.remove('active')});
    if(el)el.classList.add('active');
    fetch('/admin/request-manager/'+reqId+'/itinerary/'+itinId+'/day/'+id,{headers:{'X-CSRF-TOKEN':csrf}}).then(function(r){return r.json()}).then(function(d){
        document.getElementById('editDayId').value=d.id;
        document.getElementById('dayTitle').value=d.title||'';
        updateCharCount(document.getElementById('dayTitle'),'dayTitleCount',255);
        siteTags=d.destinations?d.destinations.split(', ').filter(function(s){return s.trim()!==''}):[]; renderSiteTags();
        if(window.dayQuill)dayQuill.root.innerHTML=decodeHtml(d.description||'');
        if(el) {
            el.setAttribute('data-duration', d.duration || 1);
        }
        updateAllDayLabelsAndDates();
        if(d.breakfast||d.lunch||d.dinner){
            document.getElementById('mealIncluded').checked=true;
            document.getElementById('mealChecks').style.display='flex';
        }else{
            document.getElementById('mealNone').checked=true;
            document.getElementById('mealChecks').style.display='none';
        }
        document.getElementById('mB').checked=!!d.breakfast;
        document.getElementById('mL').checked=!!d.lunch;
        document.getElementById('mD').checked=!!d.dinner;

        // Load photos
        renderPhotos(d.photos||[]);
        // Load services
        renderServices(d.services||[]);
    }).catch(function(err){
        console.error('selDay error:', err);
        toast('Error loading day data');
    })
}

var autoSaveTimer = null;
function debouncedAutoSaveDay() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(autoSaveDay, 1000);
}

function autoSaveDay(){
    if(!itinId) return;
    var did=document.getElementById('editDayId').value;
    var ind=document.getElementById('saveStatusIndicator');
    if(ind){ ind.style.display='inline-block'; ind.innerHTML='<i class="fa fa-spinner fa-spin"></i> Saving...'; }
    var d={title:document.getElementById('dayTitle').value,destinations:siteTags.join(', '),description:window.dayQuill?dayQuill.root.innerHTML:'',breakfast:document.getElementById('mB').checked,lunch:document.getElementById('mL').checked,dinner:document.getElementById('mD').checked,services:collectServices()};
    var u='/admin/request-manager/'+reqId+'/itinerary/'+itinId+'/day'+(did?'/'+did:'');
    fetch(u,{method:did?'PUT':'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify(d)}).then(function(r){return r.json()}).then(function(r){
        if(r.success){
            if(!did && r.day && r.day.id) {
                document.getElementById('editDayId').value = r.day.id;
                var sidebar = document.querySelector('#panelDbd .tp-sidebar');
                var emptyMsg = sidebar.querySelector('div[style*="text-align:center"]');
                if(emptyMsg) emptyMsg.remove();
                var cards = sidebar.querySelectorAll('.tp-day-card');
                var dayNum = cards.length + 1;
                var newCard = document.createElement('div');
                newCard.className = 'tp-day-card active';
                newCard.setAttribute('data-day-id', r.day.id);
                newCard.setAttribute('onclick', 'selDay('+r.day.id+',this)');
                var thumbHtml = '<div class="tp-day-thumb"><i class="fa fa-image"></i></div>';
                newCard.innerHTML = thumbHtml + '<div class="tp-day-info"><div class="day-label">Day '+dayNum+'</div><div class="day-title">'+(d.title||'Untitled')+'</div><div class="day-loc"><i class="fa fa-map-marker"></i> '+(d.destinations||'No location')+'</div></div>';
                document.querySelectorAll('.tp-day-card').forEach(function(c){c.classList.remove('active')});
                newCard.classList.add('active');
                var addBtn = sidebar.querySelector('.btn-add-day');
                sidebar.insertBefore(newCard, addBtn);
                if(typeof initDragAndDrop === 'function') initDragAndDrop();
            }
            if(ind){ ind.innerHTML='<i class="fa fa-check-circle"></i> Saved'; setTimeout(function(){ind.style.display='none';}, 3000); }
            if(did) {
                var activeCard = document.querySelector('.tp-day-card.active');
                if(activeCard) {
                    var titleEl = activeCard.querySelector('.day-title');
                    if(titleEl) titleEl.textContent = d.title || 'Untitled';
                    var locEl = activeCard.querySelector('.day-loc');
                    if(locEl) {
                        if(d.destinations) {
                            locEl.innerHTML = '<i class="fa fa-map-marker"></i> ' + d.destinations;
                        } else {
                            locEl.innerHTML = '<i class="fa fa-map-marker"></i> No location';
                        }
                    }
                    var firstImg = document.querySelector('#photosScroll img');
                    if(firstImg) {
                        var thumbEl = activeCard.querySelector('.tp-day-thumb');
                        if(thumbEl) {
                            thumbEl.style.backgroundImage = "url('" + firstImg.src + "')";
                            thumbEl.style.backgroundSize = "cover";
                            thumbEl.style.backgroundPosition = "center";
                            thumbEl.innerHTML = '';
                        }
                    }
                }
            }
        } else {
            if(ind){ ind.innerHTML='<i class="fa fa-times-circle" style="color:red"></i> Error'; }
        }
    }).catch(function(){
        if(ind){ ind.innerHTML='<i class="fa fa-times-circle" style="color:red"></i> Error'; }
    });
}

function delDay(id){if(!confirm('Delete this day?'))return;fetch('/admin/request-manager/'+reqId+'/itinerary/'+itinId+'/day/'+id,{method:'DELETE',headers:{'X-CSRF-TOKEN':csrf}}).then(function(r){return r.json()}).then(function(r){if(r.success){toast('Deleted');var cards=document.querySelectorAll('.tp-day-card');cards.forEach(function(c){if(c.getAttribute('onclick')&&c.getAttribute('onclick').indexOf(id)>=0)c.remove()});closeDayFormInline();updateAllDayLabelsAndDates()}})}

function calcTotal(){
    var prices=document.querySelectorAll('.pax-price');var sum=0;
    prices.forEach(function(p){sum+=parseFloat(p.value)||0});
    document.getElementById('totalDisp').textContent=sum.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
    
    var curr = document.getElementById('currencySelect');
    if(curr) {
        var sym = '$';
        if(curr.value === 'EUR') sym = '€';
        else if(curr.value === 'JOD') sym = 'JOD ';
        var symEl = document.getElementById('currencySymbol');
        if(symEl) symEl.textContent = sym;
    }
}

function recalcSvcRow(input) {
    var qty = parseInt(input.value) || 1;
    if(qty < 1){ qty = 1; input.value = 1; }
    var row = input.closest('.svc-cost-row');
    var unit = parseFloat(row.getAttribute('data-unit')) || 0;
    var rowTotal = unit * qty;
    var cur = document.getElementById('currencySelect') ? document.getElementById('currencySelect').value : 'JOD';
    var totalEl = row.querySelector('.svc-row-total');
    if(totalEl) totalEl.innerHTML = rowTotal.toFixed(2) + ' <span style="font-size:11px;color:#9ca3af;font-weight:400;">'+cur+'</span>';
    // Recalculate grand total
    var grand = 0;
    document.querySelectorAll('.svc-cost-row').forEach(function(r){
        var u = parseFloat(r.getAttribute('data-unit')) || 0;
        var qEl = r.querySelector('.svc-qty-input');
        var q = qEl ? (parseInt(qEl.value) || 1) : 1;
        grand += u * q;
    });
    var grandEl = document.getElementById('svcGrandTotal');
    if(grandEl) grandEl.textContent = grand.toFixed(2);
}

function calcSvcTotal(input) {
    var block = input.closest('.svc-block');
    if(!block) return;
    var costEl   = block.querySelector('.svc-cost');
    var qtyEl    = block.querySelector('.svc-qty');
    var daysEl   = block.querySelector('.svc-stay-duration');
    var totalEl  = block.querySelector('.svc-line-total');
    if(!totalEl) return;
    var cost = parseFloat(costEl ? costEl.value : 0) || 0;
    var qty  = parseInt(qtyEl  ? qtyEl.value  : 1) || 1;
    var days = parseInt(daysEl ? daysEl.value : 1) || 1;
    totalEl.textContent = cost > 0 ? (cost * qty * days).toFixed(2) : '—';
}

function calcCommission() {
    // Use PHP-rendered JS variable (most reliable) — falls back to DOM element
    var svcTotal = (typeof calculatedSvcCost !== 'undefined' && calculatedSvcCost > 0) ? calculatedSvcCost : 0;
    var grandEl = document.getElementById('svcGrandTotal');
    if (grandEl) {
        // number_format adds commas for thousands — strip them before parsing
        var domVal = parseFloat(grandEl.textContent.replace(/,/g, '')) || 0;
        if (domVal > 0) svcTotal = domVal;
    }
    var commType = document.getElementById('commissionType') ? document.getElementById('commissionType').value : 'percent';
    var commVal  = parseFloat(document.getElementById('commissionVal') ? document.getElementById('commissionVal').value : 0) || 0;
    var selling  = commType === 'percent' ? svcTotal * (1 + commVal / 100) : svcTotal + commVal;
    var dispEl = document.getElementById('sellingPriceDisp');
    if (dispEl) dispEl.textContent = selling.toFixed(2);
    // Also update the Service Total display in the commission card
    var svcTotalEl = document.getElementById('commSvcTotalVal');
    if (svcTotalEl) svcTotalEl.textContent = svcTotal.toFixed(2);
    // Auto-fill pax prices from selling price
    if (selling > 0) {
        var paxInputs = document.querySelectorAll('.pax-price');
        var perPax = paxInputs.length > 0 ? (selling / paxInputs.length).toFixed(2) : selling.toFixed(2);
        paxInputs.forEach(function(p){ p.value = perPax; });
        calcTotal();
    }
}

document.addEventListener('DOMContentLoaded', function(){
    calcTotal();
    calcCommission();
    initPriceList('pIncList', 'pInc');
    initPriceList('pExcList', 'pExc');
});

function applyToAll(){
    var prices=document.querySelectorAll('.pax-price');
    if(prices.length>0){var v=prices[0].value;prices.forEach(function(p){p.value=v})}
    calcTotal();
}

function recalcFromServices(){
    if(!itinId){toast('Save personalization first');return;}
    var btn = document.querySelector('button[onclick="recalcFromServices()"]');
    if(btn){ btn.disabled=true; btn.innerHTML='<i class="fa fa-spinner fa-spin"></i> Loading...'; }
    fetch('/admin/request-manager/'+reqId+'/itinerary/'+itinId+'/service-total',{
        headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}
    })
    .then(function(r){return r.json();})
    .then(function(d){
        if(btn){ btn.disabled=false; btn.innerHTML='<i class="fa fa-calculator"></i> Recalculate from services'; }
        if(!d.breakdown || d.breakdown.length===0){
            toast('No services with costs found in Day by Day');
            return;
        }
        // Show breakdown panel
        var listEl = document.getElementById('serviceBreakdownList');
        var html = '';
        d.breakdown.forEach(function(s){
            html += '<div style="display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px solid #f1f5f9;">'
                  + '<span style="color:#555;">Day '+s.day+' — '+s.name+'</span>'
                  + '<span style="color:#005e46;font-weight:600;">'+s.cost.toFixed(2)+'</span>'
                  + '</div>';
        });
        listEl.innerHTML = html;
        document.getElementById('serviceBreakdownTotal').textContent = d.total.toFixed(2);
        document.getElementById('serviceBreakdown').style.display = 'block';
        // Fill pax prices with per_person value
        var prices = document.querySelectorAll('.pax-price');
        prices.forEach(function(p){ p.value = d.per_person; });
        calcTotal();
        toast('Prices updated from '+d.breakdown.length+' service(s) — '+d.per_person.toFixed(2)+' per person');
    })
    .catch(function(){
        if(btn){ btn.disabled=false; btn.innerHTML='<i class="fa fa-calculator"></i> Recalculate from services'; }
        toast('Error loading service costs');
    });
}

function addPriceItem(listId, value) {
    var list = document.getElementById(listId);
    if (!list) return;
    var div = document.createElement('div');
    div.className = 'price-item';
    div.innerHTML = '<span class="drag-handle">⋮⋮</span>'
        + '<input type="text" placeholder="Add item..." value="' + (value||'').replace(/"/g,'&quot;') + '">'
        + '<button class="del-item" onclick="this.parentElement.remove()" title="Remove">&times;</button>';
    list.appendChild(div);
    div.querySelector('input').focus();
}
function initPriceList(listId, textareaId) {
    var ta = document.getElementById(textareaId);
    var list = document.getElementById(listId);
    if (!ta || !list) return;
    list.innerHTML = '';
    var lines = ta.value.split('\n').map(function(l){return l.trim();}).filter(function(l){return l.length > 0;});
    if (lines.length === 0) { addPriceItem(listId, ''); return; }
    lines.forEach(function(line){ addPriceItem(listId, line); });
}
function collectPriceList(listId) {
    var list = document.getElementById(listId);
    if (!list) return '';
    var vals = [];
    list.querySelectorAll('input[type=text]').forEach(function(inp){
        var v = inp.value.trim();
        if (v) vals.push(v);
    });
    return vals.join('\n');
}
function loadDefaults(){
    var incItems = document.getElementById('pIncList');
    if(incItems && incItems.querySelectorAll('input').length > 0 && collectPriceList('pIncList').trim()) {
        if(!confirm('This will replace existing items. Continue?')) return;
    }
    var incLines = ['• Private transportation with air-conditioned vehicle','• Professional English-speaking driver guide','• All hotel accommodations as described in the itinerary','• Daily breakfast at hotels','• Entrance fees to all sites mentioned in the itinerary','• Airport transfers (arrival & departure)','• Bottled water during tours','• All taxes and service charges'];
    var excLines = ['• International flights','• Travel insurance','• Meals not mentioned in the itinerary','• Personal expenses (laundry, phone calls, etc.)','• Tips and gratuities','• Visa fees (if applicable)','• Optional activities not mentioned'];
    document.getElementById('pIncList').innerHTML = '';
    document.getElementById('pExcList').innerHTML = '';
    incLines.forEach(function(l){ addPriceItem('pIncList', l); });
    excLines.forEach(function(l){ addPriceItem('pExcList', l); });
    toast('Default templates loaded - you can modify them');
}
function clearPriceDetails(){
    if(!confirm('Clear all price details?')) return;
    document.getElementById('pIncList').innerHTML = '';
    document.getElementById('pExcList').innerHTML = '';
    addPriceItem('pIncList', '');
    addPriceItem('pExcList', '');
    toast('Cleared');
}

function openConditionsLibrary(){
    var modal = document.getElementById('conditionsLibraryModal');
    modal.style.display='flex';
    var body = document.getElementById('conditionsLibraryBody');
    body.innerHTML='<p style="text-align:center;color:#94a3b8;padding:40px 0;">Loading...</p>';
    fetch('{{ route("admin.ajax.get-inclusions") }}')
    .then(function(r){return r.json()})
    .then(function(items){
        var html='';
        items.forEach(function(item){
            html+='<div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-bottom:1px solid #f1f5f9;" data-cond-name="'+item.name+'">';
            html+='<span style="font-size:13px;font-weight:600;color:#334155;">'+item.name+'</span>';
            html+='<div style="display:flex;gap:4px;">';
            html+='<label style="display:flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;border:1px solid #e2e8f0;cursor:pointer;font-size:11px;font-weight:600;color:#64748b;"><input type="radio" name="cond_'+item.lang_id+'" value="none" checked style="margin:0;"> —</label>';
            html+='<label style="display:flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;border:1px solid #d1fae5;background:#ecfdf5;cursor:pointer;font-size:11px;font-weight:700;color:#059669;"><input type="radio" name="cond_'+item.lang_id+'" value="inc" style="margin:0;"> ✓ Inc</label>';
            html+='<label style="display:flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;border:1px solid #fee2e2;background:#fef2f2;cursor:pointer;font-size:11px;font-weight:700;color:#dc2626;"><input type="radio" name="cond_'+item.lang_id+'" value="exc" style="margin:0;"> ✗ Exc</label>';
            html+='</div></div>';
        });
        body.innerHTML=html||'<p style="text-align:center;color:#94a3b8;padding:40px 0;">No items in library</p>';
    })
    .catch(function(){body.innerHTML='<p style="text-align:center;color:#ef4444;padding:40px 0;">Failed to load</p>';});
}
function closeConditionsLibrary(){
    document.getElementById('conditionsLibraryModal').style.display='none';
}
function applyConditionsLibrary(){
    var rows=document.querySelectorAll('#conditionsLibraryBody [data-cond-name]');
    var incLines=[],excLines=[];
    rows.forEach(function(row){
        var name=row.getAttribute('data-cond-name');
        var radios=row.querySelectorAll('input[type=radio]');
        radios.forEach(function(r){
            if(r.checked&&r.value==='inc') incLines.push('• '+name);
            if(r.checked&&r.value==='exc') excLines.push('• '+name);
        });
    });
    if(incLines.length===0&&excLines.length===0){toast('No items selected');return;}
    incLines.forEach(function(l){ addPriceItem('pIncList', l); });
    excLines.forEach(function(l){ addPriceItem('pExcList', l); });
    closeConditionsLibrary();
    toast(incLines.length+' inclusions, '+excLines.length+' exclusions added');
}
function savePrice(){
    if(!itinId){toast('Save personalization first');return}
    var prices=document.querySelectorAll('.pax-price');var sum=0;var count=prices.length;
    prices.forEach(function(p){sum+=parseFloat(p.value)||0});
    var avg = count > 0 ? (sum / count) : 0;
    var d={
        price_per_person:avg,
        num_travelers:count,
        group_total:sum,
        price_includes:collectPriceList('pIncList'),
        price_excludes:collectPriceList('pExcList'),
        nights_included:document.getElementById('pNights')?document.getElementById('pNights').value:'',
        booking_conditions:document.getElementById('pCond')?document.getElementById('pCond').value:'',
        payment_conditions:document.getElementById('pPayment')?document.getElementById('pPayment').value:'',
        reduced_mobility:document.getElementById('pReducedMobility')?document.getElementById('pReducedMobility').checked:false,
        passports_visas:document.getElementById('pPassports')?document.getElementById('pPassports').value:'',
        travel_insurance:document.getElementById('pInsurance')?document.getElementById('pInsurance').value:'',
        agency_commission:document.getElementById('commissionVal')?document.getElementById('commissionVal').value:'0',
        commission_type:document.getElementById('commissionType')?document.getElementById('commissionType').value:'percent'
    };
    fetch('/admin/request-manager/'+reqId+'/itinerary/'+itinId,{
        method:'PUT',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},
        body:JSON.stringify(d)
    }).then(function(r){return r.json()}).then(function(r){
        if(r.success){
            toast('Pricing saved');
            var lp = document.querySelector('.price-total-val #totalDisp');
            if(lp) lp.textContent = sum.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
        }
    });
}




function syncPriceFromServices(){
    var prices=document.querySelectorAll('.pax-price');
    if(prices.length === 0) return;
    
    // Recalculate total cost from current services in DOM
    var totalSvc = 0;
    document.querySelectorAll('.svc-cost').forEach(function(c){
        totalSvc += parseFloat(c.value) || 0;
    });
    
    var ppp = totalSvc / prices.length;
    prices.forEach(function(p){ p.value = ppp.toFixed(2); });
    calcTotal();
    toast('Prices updated from services sum: ' + totalSvc.toFixed(2));
}

// ── Sidebar Day drag and drop sorting ──
var draggedCard = null;
var dropTarget = null;
var dropBefore = true;

function initDragAndDrop() {
    var cards = document.querySelectorAll('.tp-day-card');
    cards.forEach(function(card) {
        card.setAttribute('draggable', 'true');
        card.removeEventListener('dragstart', handleDragStart);
        card.removeEventListener('dragover', handleDragOver);
        card.removeEventListener('dragleave', handleDragLeave);
        card.removeEventListener('drop', handleDrop);
        card.removeEventListener('dragend', handleDragEnd);
        card.addEventListener('dragstart', handleDragStart);
        card.addEventListener('dragover', handleDragOver);
        card.addEventListener('dragleave', handleDragLeave);
        card.addEventListener('drop', handleDrop);
        card.addEventListener('dragend', handleDragEnd);
    });
}

function handleDragStart(e) {
    draggedCard = this;
    e.dataTransfer.effectAllowed = 'move';
    setTimeout(function() { draggedCard.style.opacity = '0.4'; }, 0);
}

function clearDropIndicators() {
    document.querySelectorAll('.tp-day-card').forEach(function(c) {
        c.style.outline = '';
        c.style.outlineOffset = '';
    });
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    var card = this;
    if (card === draggedCard) return false;
    clearDropIndicators();
    var rect = card.getBoundingClientRect();
    dropBefore = (e.clientY - rect.top) < (rect.height / 2);
    card.style.outline = '2px solid #f97316';
    card.style.outlineOffset = dropBefore ? '-2px' : '-2px';
    dropTarget = card;
    return false;
}

function handleDragLeave(e) {
    var rel = e.relatedTarget;
    if (!rel || !this.contains(rel)) {
        this.style.outline = '';
        this.style.outlineOffset = '';
    }
}

function handleDrop(e) {
    e.stopPropagation();
    e.preventDefault();
    clearDropIndicators();
    if (!draggedCard || !dropTarget || dropTarget === draggedCard) return false;
    var parent = dropTarget.parentNode;
    if (dropBefore) {
        parent.insertBefore(draggedCard, dropTarget);
    } else {
        parent.insertBefore(draggedCard, dropTarget.nextSibling);
    }
    dropTarget = null;
    return false;
}

function handleDragEnd(e) {
    if (draggedCard) draggedCard.style.opacity = '1';
    clearDropIndicators();
    draggedCard = null;
    dropTarget = null;
    updateSidebarSequence();
}

function updateSidebarSequence() {
    var cards = document.querySelectorAll('.tp-sidebar .tp-day-card');
    var ids = [];
    cards.forEach(function(card) {
        var dayId = card.getAttribute('data-day-id');
        if (dayId) ids.push(dayId);
    });
    
    // Update all labels and dates using the central function
    updateAllDayLabelsAndDates();
    
    if (ids.length > 0 && itinId) {
        fetch('/admin/request-manager/' + reqId + '/itinerary/' + itinId + '/day/reorder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ ids: ids })
        }).then(function(r) { return r.json(); }).then(function(r) {
            if (r.success) {
                toast('Day order updated successfully!');
            }
        }).catch(function(err) {
            console.error('Reorder days error:', err);
            toast('Failed to save day order');
        });
    }
}

// Auto-open editor if redirected from a quote copy
document.addEventListener('DOMContentLoaded', function(){
    initDragAndDrop();
    updateAllDayLabelsAndDates();
    var params = new URLSearchParams(window.location.search);
    if(params.get('openEditor') === '1' && itinId) {
        openEditor();
        window.history.replaceState({}, '', window.location.pathname);
    }
    @if(isset($activeTab) && $activeTab === 'daybyday')
    // Auto-load first active day data on page load
    var firstDayCard = document.querySelector('.tp-day-card.active');
    if(firstDayCard) {
        var firstDayId = firstDayCard.getAttribute('data-day-id');
        if(firstDayId) selDay(parseInt(firstDayId), firstDayCard);
    }
    @endif
    @if(isset($activeTab) && $activeTab === 'price')
    // Auto-load service expenses into price fields on page load
    if(itinId) recalcFromServices();
    @endif
});
</script>
</body>
</html>
