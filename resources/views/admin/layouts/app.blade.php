<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin | PVT Travels')</title>
    <link rel="stylesheet" href="{{ asset('gogies3d/css/gogies.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { prefix: 'tw-', theme: { extend: { colors: { brand: '#6366f1', dark: '#0f172a' } } } }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ========== ADVANCED PRO DESIGN SYSTEM (V3.0) ========== */
        :root {
            --brand-primary: #6366f1;
            --brand-secondary: #8b5cf6;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-heading: #0f172a;
            --text-body: #475569;
            --text-muted: #94a3b8;
            --border-light: rgba(0,0,0,0.06);
            --shadow-soft: 0 2px 4px rgba(0,0,0,0.02), 0 10px 20px rgba(0,0,0,0.04);
            --radius-xl: 16px;
            --radius-2xl: 20px;
        }

        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            -webkit-font-smoothing: antialiased; 
            margin: 0; 
            background: var(--bg-main); 
            color: var(--text-heading); 
            overflow-x: hidden;
        }

        /* Layout Architecture */
        .admin-layout-container { display: flex; width: 100%; min-height: 100vh; }
        
        .admin-sidebar-wrapper { 
            width: 270px; 
            background: #0f172a; 
            flex-shrink: 0; 
            z-index: 1000; 
            position: fixed; 
            top: 0; bottom: 0; left: 0; 
            box-shadow: 10px 0 40px rgba(0,0,0,0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-main-wrapper { 
            flex: 1; 
            min-width: 0; 
            margin-left: 270px; 
            display: flex; 
            flex-direction: column; 
            background: var(--bg-main); 
            transition: margin-left 0.3s ease;
        }

        .admin-topbar-wrapper { 
            height: 72px; 
            background: rgba(255,255,255,0.75); 
            border-bottom: 1px solid var(--border-light); 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            padding: 0 40px; 
            position: sticky; 
            top: 0; 
            z-index: 900; 
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }

        .admin-content-inner { 
            padding: 45px 40px; 
            flex: 1; 
            max-width: 1540px; 
            margin: 0 auto; 
            width: 100%; 
            box-sizing: border-box; 
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Modern Components */
        .box { 
            background: var(--bg-card); 
            border-radius: var(--radius-2xl); 
            border: 1px solid var(--border-light) !important; 
            box-shadow: var(--shadow-soft); 
            padding: 35px !important; 
            margin-bottom: 40px !important; 
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .box:hover { transform: translateY(-2px); box-shadow: 0 15px 35px rgba(0,0,0,0.06); }

        /* Premium Forms */
        label { display: block; font-size: 13px; font-weight: 600; color: var(--text-heading); margin-bottom: 10px; opacity: 0.8; }
        
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="url"], input[type="date"], select, textarea {
            width: 100%; height: 50px !important; padding: 12px 18px !important; 
            border-radius: 14px !important; border: 1px solid #e2e8f0 !important; 
            background: #fff !important; font-size: 14px !important; color: var(--text-heading) !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, select:focus, textarea:focus {
            border-color: var(--brand-primary) !important; 
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12) !important;
            outline: none !important;
        }
        input:disabled, select:disabled, textarea:disabled {
            background: #f1f5f9 !important; opacity: 0.5 !important; cursor: not-allowed !important;
        }

        /* Modern Typography */
        h1 { font-size: 34px !important; font-weight: 800 !important; color: var(--text-heading) !important; letter-spacing: -0.04em !important; margin: 0 !important; }
        p.subtitle { margin-top: 10px; color: var(--text-muted); font-size: 15px; font-weight: 500; }

        /* Advanced Buttons */
        .btn { 
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px 24px !important; border-radius: 14px !important; 
            font-size: 14px !important; font-weight: 600 !important; 
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important; 
            cursor: pointer; border: none;
        }
        .btn:active { transform: scale(0.97); }
        
        .btn.indigo, .btn.blue { 
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)) !important; 
            color: #fff !important; 
            box-shadow: 0 8px 16px rgba(99,102,241,0.25) !important; 
        }
        .btn.indigo:hover, .btn.blue:hover { 
            box-shadow: 0 12px 24px rgba(99,102,241,0.35) !important;
            filter: brightness(1.05);
        }

        .btn.red { 
            background: #fff !important; color: #ef4444 !important; 
            border: 1px solid #fee2e2 !important; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.05) !important;
        }
        .btn.red:hover { background: #fee2e2 !important; border-color: #ef4444 !important; }

        /* Pro Status Badges */
        .badge {
            padding: 6px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; 
            text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 5px;
        }
        .badge.success { background: #ecfdf5; color: #059669; }
        .badge.warning { background: #fffbeb; color: #d97706; }
        .badge.danger { background: #fef2f2; color: #dc2626; }

        /* Utility */
        /* Modern Global Modals */
        .modal {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 99999;
            display: none;
            align-items: center; justify-content: center;
            padding: 20px; opacity: 1; pointer-events: auto;
        }
        .modal:target { display: flex !important; }
        .modal > div { animation: modalSlideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
        @keyframes modalSlideUp { 
            from { opacity: 0; transform: translateY(30px) scale(0.95); } 
            to { opacity: 1; transform: translateY(0) scale(1); } 
        }

        .tw-shadow-premium { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); }

        /* ========== GLOBAL TABLE & BUTTON OVERRIDES ========== */
        
        /* Table headers */
        .admin-content-inner table thead tr th {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            padding: 12px 14px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            white-space: nowrap !important;
        }

        /* Table body cells */
        .admin-content-inner table tbody tr td {
            font-size: 13px !important;
            color: #1e293b !important;
            padding: 12px 14px !important;
            vertical-align: middle !important;
            line-height: 1.5 !important;
        }

        /* Prevent font-size inherit on everything */
        .admin-content-inner table tbody td span,
        .admin-content-inner table tbody td div,
        .admin-content-inner table tbody td a {
            font-size: inherit !important;
        }

        /* Bold text in tables */
        .admin-content-inner table tbody td [class*="font-bold"] {
            font-size: 13px !important;
            color: #0f172a !important;
        }

        /* Action icon buttons — visible but compact */
        .admin-content-inner table tbody td a[title],
        .admin-content-inner table tbody td button[title] {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 10px !important;
            font-size: 14px !important;
        }
        .admin-content-inner table tbody td a[title] i,
        .admin-content-inner table tbody td button[title] i {
            font-size: 14px !important;
        }

        /* Badges — readable but compact */
        .admin-content-inner span[class*="rounded-full"],
        .admin-content-inner span[class*="rounded-xl"] {
            font-size: 11px !important;
            padding: 4px 10px !important;
            font-weight: 700 !important;
        }

        /* Prevent table horizontal overflow */
        .admin-content-inner table {
            table-layout: auto !important;
            width: 100% !important;
        }
        .admin-content-inner .tw-overflow-x-auto {
            overflow-x: visible !important;
        }

        /* Green btn style for nav page buttons */
        .btn.green {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: #fff !important;
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.25) !important;
        }
        .btn.green:hover {
            box-shadow: 0 12px 24px rgba(16, 185, 129, 0.35) !important;
            filter: brightness(1.05);
        }


        @media (max-width: 1024px) {
            .admin-sidebar-wrapper { left: -270px; }
            .admin-sidebar-wrapper.mobile-open { left: 0; }
            .admin-main-wrapper { margin-left: 0; }
        }
    </style>
    @stack('head')
</head>
<body>
    <div class="admin-layout-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar-wrapper" id="adminSidebar">
            @include('admin.partials.nav')
        </aside>

        <!-- Main Content Area -->
        <main class="admin-main-wrapper" id="adminMain">
            <!-- Topbar (Glassmorphic) -->
            <header class="admin-topbar-wrapper">
                <div style="display: flex; align-items: center; gap: 12px; background: #fff; border-radius: 14px; padding: 0 18px; border: 1px solid var(--border-light); box-shadow: 0 2px 4px rgba(0,0,0,0.02); height: 44px;">
                    <i class="fa fa-search" style="color: var(--text-muted); font-size: 14px;"></i>
                    <input type="text" placeholder="Search resources..." style="background: transparent !important; border: none !important; box-shadow: none !important; width: 240px; font-size: 14px !important; color: var(--text-body) !important; padding: 0 !important; height: 100% !important;">
                </div>
                
                <div class="tw-flex tw-items-center tw-gap-5">
                    <a href="{{ url('/') }}" target="_blank" class="tw-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-rounded-xl tw-text-slate-600 tw-bg-slate-50 tw-border tw-border-slate-200 hover:tw-bg-slate-100 tw-transition-all tw-no-underline tw-text-xs tw-font-bold">
                        <i class="fa fa-external-link"></i> Live Site
                    </a>
                    
                    <!-- Profile Dropdown -->
                    <div class="tw-relative" id="topProfileDropdown">
                        <div onclick="document.getElementById('topProfileMenu').classList.toggle('tw-hidden'); document.getElementById('topProfileMenu').classList.toggle('tw-opacity-0');" class="tw-flex tw-items-center tw-gap-3 tw-bg-white tw-pl-4 tw-pr-2 tw-py-1.5 tw-rounded-2xl tw-border tw-border-slate-200 tw-shadow-sm tw-cursor-pointer hover:tw-shadow-md hover:tw-border-indigo-100 tw-transition-all">
                            <div class="tw-text-right tw-hidden sm:tw-block">
                                <div class="tw-text-xs tw-font-bold tw-text-slate-900">{{ Auth::user()->first_name ?? 'Admin' }}</div>
                                <div class="tw-text-[11px] tw-text-indigo-500 tw-font-bold tw-uppercase tw-tracking-wider">{{ session('pro_user_group') ?? 'Super Admin' }}</div>
                            </div>
                            <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->first_name ?? 'Admin') . '&color=7F9CF5&background=EBF4FF' }}" class="tw-w-9 tw-h-9 tw-rounded-xl tw-object-cover tw-border-2 tw-border-slate-50 tw-shadow-sm">
                            <i class="fa fa-chevron-down tw-text-slate-400 tw-text-[10px] tw-mr-1"></i>
                        </div>
                        
                        <!-- Dropdown Menu -->
                        <div id="topProfileMenu" class="tw-hidden tw-opacity-0 tw-absolute tw-right-0 tw-top-full tw-mt-2 tw-w-56 tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-shadow-xl tw-transition-all tw-duration-200 tw-z-[9999] tw-overflow-hidden">
                            <div class="tw-px-4 tw-py-3 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                                <p class="tw-text-sm tw-font-bold tw-text-slate-900 tw-m-0">{{ Auth::user()->first_name ?? 'Admin' }} {{ Auth::user()->last_name ?? '' }}</p>
                                <p class="tw-text-xs tw-font-medium tw-text-slate-500 tw-m-0 tw-truncate">{{ Auth::user()->email ?? 'admin@example.com' }}</p>
                            </div>
                            <div class="tw-py-2">
                                <a href="{{ route('admin.my-account') ?? '#' }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-slate-700 hover:tw-bg-slate-50 hover:tw-text-indigo-600 tw-no-underline tw-transition-colors">
                                    <i class="fa fa-user-circle-o tw-text-lg tw-text-slate-400"></i> My Profile
                                </a>
                                @if(auth()->check() && auth()->user()->hasPermission('settings_global'))
                                <a href="{{ route('admin.settings.global') ?? '#' }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-slate-700 hover:tw-bg-slate-50 hover:tw-text-indigo-600 tw-no-underline tw-transition-colors">
                                    <i class="fa fa-cog tw-text-lg tw-text-slate-400"></i> Account Settings
                                </a>
                                @endif
                            </div>
                            <div class="tw-h-px tw-bg-slate-100"></div>
                            <div class="tw-py-2">
                                <a href="{{ route('logout') ?? '#' }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-rose-600 hover:tw-bg-rose-50 tw-no-underline tw-transition-colors">
                                    <i class="fa fa-power-off tw-text-lg tw-text-rose-400"></i> Secure Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="admin-content-inner">
                @if(session('success'))
                    <div class="box success" style="margin-bottom:16px;display:flex;align-items:center;gap:8px;padding:15px !important;border-left:4px solid #10b981 !important;"><i class="fa fa-check" style="color:#10b981;"></i> <b style="color:#059669;">{{ session('success') }}</b></div>
                @endif
                @if(session('error'))
                    <div class="box danger" style="margin-bottom:16px;display:flex;align-items:center;gap:8px;padding:15px !important;border-left:4px solid #ef4444 !important;"><i class="fa fa-exclamation-triangle" style="color:#ef4444;"></i> <b style="color:#dc2626;">{{ session('error') }}</b></div>
                @endif
                @if($errors->any())
                    <div class="box danger" style="margin-bottom:16px;padding:15px !important;border-left:4px solid #ef4444 !important;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;"><i class="fa fa-exclamation-circle" style="color:#ef4444;"></i> <b style="color:#dc2626;">Please fix the following errors:</b></div>
                        <ul style="margin:0;padding-left:30px;color:#ef4444;font-size:13px;font-weight:600;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>

            <footer class="admin-footer-wrapper" style="padding: 20px 40px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #94a3b8;">
                &copy; {{ date('Y') }} {{ config('app.name', 'PVT Travels') }} | Version: <span class="tw-font-bold tw-text-brand">3.0 PRO</span>
            </footer>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Profile Menu Click Outside Closure
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('topProfileDropdown');
            const menu = document.getElementById('topProfileMenu');
            if (dropdown && menu && !dropdown.contains(e.target)) {
                menu.classList.add('tw-hidden', 'tw-opacity-0');
            }
        });
    </script>
    <script src="{{ asset('gogies3d/js/gogies.js') }}"></script>
    @stack('footer')
    @stack('scripts')
</body>
</html>
