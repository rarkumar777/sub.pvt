<style>
    /* ========== ADVANCED PRO SIDEBAR (V3.0) ========== */
    .sidebar-inner { height: 100%; display: flex; flex-direction: column; background: #0f172a; border-right: 1px solid rgba(255,255,255,0.03); }
    .sidebar-inner::-webkit-scrollbar { width: 0; }

    /* Brand Section */
    .sidebar-brand { 
        padding: 30px 24px; display: flex; align-items: center; gap: 14px; 
        background: linear-gradient(to bottom, rgba(255,255,255,0.02), transparent);
    }
    .brand-logo { 
        width: 42px; height: 42px; background: linear-gradient(135deg, #f97316, #ff9f43); 
        border-radius: 14px; display: flex; align-items: center; justify-content: center; 
        color: white; font-weight: 850; font-size: 20px; flex-shrink: 0;
        box-shadow: 0 8px 16px rgba(249,115,22,0.3);
    }
    .brand-info h4 { margin: 0; color: #fff; font-size: 16px; font-weight: 800; letter-spacing: -0.02em; }
    .brand-info p { margin: 2px 0 0 0; font-size: 10px; color: #f97316; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; }

    /* Navigation Sections */
    .sidebar-nav { padding: 15px 12px; flex-grow: 1; overflow-y: auto; }
    .nav-section-title { 
        font-size: 11px; font-weight: 800; color: rgba(255,255,255,0.15); 
        text-transform: uppercase; letter-spacing: 0.12em; padding: 25px 15px 10px; 
    }

    /* Menu Items */
    .nav-list { list-style: none; padding: 0; margin: 0; }
    .nav-item > a { 
        display: flex; align-items: center; padding: 12px 16px; margin: 4px 0; 
        color: #94a3b8; text-decoration: none !important; font-size: 14px; font-weight: 600; 
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 12px; gap: 12px; 
        position: relative;
    }
    .nav-item > a i:first-child { width: 22px; font-size: 16px; text-align: center; opacity: 0.6; transition: all 0.2s; }
    .nav-item > a:hover { color: #fff; background: rgba(255,255,255,0.05); }
    .nav-item > a:hover i:first-child { opacity: 1; transform: scale(1.1); }

    /* Active State */
    .nav-item.active > a { 
        color: #fff; 
        background: linear-gradient(90deg, rgba(249, 115, 22, 0.15) 0%, rgba(249, 115, 22, 0.05) 100%); 
        border: 1px solid rgba(249, 115, 22, 0.2) !important;
    }
    .nav-item.active > a i:first-child { color: #f97316; opacity: 1; }
    .nav-item.active > a::before {
        content: ''; position: absolute; left: 0; top: 10px; bottom: 10px; width: 4px; 
        background: #f97316; border-radius: 0 4px 4px 0; box-shadow: 0 0 10px #f97316;
    }

    /* Dropdown Logic */
    .has-dropdown .arrow { margin-left: auto; font-size: 10px; transition: transform 0.3s ease; opacity: 0.4; }
    .has-dropdown.open .arrow { transform: rotate(90deg); opacity: 0.8; }

    /* Sub Navigation */
    .sub-nav { list-style: none; padding: 5px 0 10px 0; margin: 0 0 0 12px; border-left: 1px solid rgba(255,255,255,0.05); display: none; }
    .has-dropdown.open .sub-nav { display: block; }
    .sub-nav li a { 
        display: block; padding: 8px 16px 8px 32px; font-size: 13px; color: #64748b; 
        text-decoration: none !important; transition: all 0.2s; border-radius: 10px; position: relative;
    }
    .sub-nav li a::before { 
        content: ''; position: absolute; left: 16px; top: 50%; width: 6px; height: 1px; 
        background: rgba(255,255,255,0.1); transform: translateY(-50%); 
    }
    .sub-nav li a:hover { color: #fff; background: rgba(255,255,255,0.03); }

    /* Sidebar Footer */
    .sidebar-footer { 
        padding: 24px; background: rgba(0,0,0,0.2); border-top: 1px solid rgba(255,255,255,0.05); 
        display: flex; align-items: center; gap: 12px;
    }
    .footer-avatar { width: 38px; height: 38px; border-radius: 12px; border: 2px solid rgba(255,255,255,0.1); object-fit: cover; }
    .footer-user-name { font-size: 13px; font-weight: 700; color: #f8fafc; }
    .logout-btn { 
        margin-left: auto; width: 34px; height: 34px; border-radius: 10px; 
        background: rgba(239, 68, 68, 0.1); color: #ef4444; 
        display: flex; align-items: center; justify-content: center; 
        transition: all 0.2s; text-decoration: none; 
    }
    .logout-btn:hover { background: #ef4444; color: #fff; transform: rotate(90deg); }
</style>

<div class="sidebar-inner" id="sidebarInner">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-logo">P</div>
        <div class="brand-info">
            <h4>PVT Travels</h4>
            <p>Admin Panel</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <!-- MAIN MENU -->
        <div class="nav-section-title">Main Menu</div>
        <ul class="nav-list">
            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fa fa-th-large"></i> Dashboard
                </a>
            </li>
        </ul>

        <!-- REQUEST MANAGER -->
        <ul class="nav-list">
            <li class="nav-item {{ request()->is('admin/request-manager*') ? 'active' : '' }}">
                <a href="{{ route('admin.request-manager') }}">
                    <i class="fa fa-inbox"></i> Request Manager
                </a>
            </li>
        </ul>

        <!-- MANAGEMENT -->
        <div class="nav-section-title">Management</div>
        <ul class="nav-list">
            {{-- Users --}}
            @if(auth()->user()->hasPermission('users_'))
            <li class="nav-item has-dropdown {{ (request()->is('admin/users*') || request()->is('admin/user-groups*')) ? 'open active' : '' }}">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fa fa-users"></i> User Access
                    <i class="fa fa-chevron-right arrow"></i>
                </a>
                <ul class="sub-nav">
                    <li><a href="{{ route('admin.users.index') }}">Manage Users</a></li>
                    <li><a href="{{ route('admin.user-groups.index') }}">User Groups</a></li>
                </ul>
            </li>
            @endif

            {{-- Tours --}}
            @if(auth()->user()->hasPermission('tours'))
            <li class="nav-item has-dropdown {{ (request()->is('admin/tours*') || request()->is('admin/tour-*')) ? 'open active' : '' }}">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fa fa-plane"></i> Tours
                    <i class="fa fa-chevron-right arrow"></i>
                </a>
                <ul class="sub-nav">
                    <li><a href="{{ route('admin.tours.index') }}">Manage Tours</a></li>
                    <li><a href="{{ route('admin.guaranteed-departures.index') }}">Guaranteed Departure</a></li>
                    <li><a href="{{ route('admin.tours-seasons') }}">Seasons</a></li>
                    <li><a href="{{ route('admin.tour-types') }}">Types</a></li>
                    <li><a href="{{ route('admin.tour-categories') }}">Categories</a></li>
                    <li><a href="{{ route('admin.tour-inclusions') }}">Inclusions</a></li>
                    <li><a href="{{ route('admin.tour-tec') }}">Technical Details</a></li>
                    <li><a href="{{ route('admin.tour-settings') }}">Settings</a></li>
                </ul>
            </li>
            @endif

            {{-- Booking --}}
            @if(auth()->user()->hasPermission('tours_index'))
            <li class="nav-item {{ request()->is('admin/bookings*') ? 'active' : '' }}">
                <a href="{{ route('admin.bookings.index') }}">
                    <i class="fa fa-calendar-check-o"></i> Bookings
                </a>
            </li>
            @endif

            {{-- Quotation --}}
            @if(auth()->user()->hasPermission('tours_quotations') || auth()->user()->hasPermission('services_'))
            <li class="nav-item has-dropdown {{ (request()->is('admin/quotations*') || request()->is('admin/quotation-*') || request()->is('admin/canned*') || request()->is('admin/library*') || (request()->is('admin/services*') && !request()->is('admin/services-venders*'))) ? 'open active' : '' }}">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fa fa-pie-chart"></i> Quotations
                    <i class="fa fa-chevron-right arrow"></i>
                </a>
                <ul class="sub-nav">
                    @if(auth()->user()->hasPermission('tours_quotations'))
                    <li><a href="{{ route('admin.quotations.index') }}">Manage Quotations</a></li>
                    <li><a href="{{ route('admin.quotation-pricing.index') }}">Pricing</a></li>
                    <li><a href="{{ route('admin.canned-days.index') }}">Canned Days</a></li>
                    <li><a href="{{ route('admin.quotation-fast-access') }}">Expenses Fast Access</a></li>
                    <li><a href="{{ route('admin.quotation-email-templates') }}">E-mail Templates</a></li>
                    @endif
                    @if(auth()->user()->hasPermission('services_'))
                    <li><a href="{{ route('admin.services.index') }}">Manage Services</a></li>
                    <li><a href="{{ route('admin.library') }}">Library</a></li>
                    @endif
                </ul>
            </li>
            @endif
        </ul>

        <!-- FINANCIAL -->
        <div class="nav-section-title">Financial</div>
        <ul class="nav-list">
            @if(auth()->user()->hasPermission('invoices') || auth()->user()->hasPermission('expenses'))
            <li class="nav-item has-dropdown {{ (request()->is('admin/invoices*') || request()->is('admin/expenses*') || request()->is('admin/services-venders*')) ? 'open active' : '' }}">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fa fa-bank"></i> Account
                    <i class="fa fa-chevron-right arrow"></i>
                </a>
                <ul class="sub-nav">
                    @if(auth()->user()->hasPermission('invoices'))
                    <li><a href="{{ route('admin.invoices.index') }}">Invoices</a></li>
                    @endif
                    @if(auth()->user()->hasPermission('expenses'))
                    <li><a href="{{ route('admin.expenses.index') }}">Expenses</a></li>
                    @endif
                    <li><a href="{{ route('admin.services.venders') }}">Venders</a></li>
                    <li><a href="{{ route('admin.customers.ledger') }}">Customers Ledger</a></li>
                </ul>
            </li>
            @endif
        </ul>

        <!-- CONFIGURATION -->
        <div class="nav-section-title">Configuration</div>
        <ul class="nav-list">
            {{-- CMS --}}
            @if(auth()->user()->hasPermission('cms_') || auth()->user()->hasPermission('seo_global') || auth()->user()->hasPermission('file_manager'))
            <li class="nav-item has-dropdown {{ (request()->is('admin/cms*') || request()->is('admin/settings/seo*') || request()->is('admin/settings/file-manager*')) ? 'open active' : '' }}">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fa fa-server"></i> CMS & SEO
                    <i class="fa fa-chevron-right arrow"></i>
                </a>
                <ul class="sub-nav">
                    <li><a href="{{ route('admin.pages.index') }}">Pages</a></li>
                    <li><a href="{{ route('admin.sliders.index') }}">Sliders</a></li>
                    <li><a href="{{ route('admin.settings.seo') }}">SEO Settings</a></li>
                    <li><a href="{{ route('admin.settings.file-manager') }}">File Manager</a></li>
                </ul>
            </li>
            @endif

            {{-- Global Settings --}}
            @if(auth()->user()->hasPermission('settings_global'))
            <li class="nav-item has-dropdown {{ request()->is('admin/settings*') && !request()->is('admin/settings/seo*') && !request()->is('admin/settings/file-manager*') ? 'open active' : '' }}">
                <a href="javascript:void(0)" class="dropdown-toggle">
                    <i class="fa fa-cog"></i> Global Settings
                    <i class="fa fa-chevron-right arrow"></i>
                </a>
                <ul class="sub-nav">
                    <li><a href="{{ route('admin.settings.global') }}" class="{{ request()->routeIs('admin.settings.global') ? 'tw-bg-indigo-50/5 tw-text-white tw-font-bold' : '' }}">Global</a></li>
                    <li><a href="{{ route('admin.settings.payments') }}" class="{{ request()->routeIs('admin.settings.payments') ? 'tw-bg-indigo-50/5 tw-text-white tw-font-bold' : '' }}">Payment Gate Ways</a></li>
                    <li><a href="{{ route('admin.settings.languages') }}" class="{{ request()->routeIs('admin.settings.languages') ? 'tw-bg-indigo-50/5 tw-text-white tw-font-bold' : '' }}">Languages</a></li>
                    <li><a href="{{ route('admin.settings.modules') }}" class="{{ request()->routeIs('admin.settings.modules') ? 'tw-bg-indigo-50/5 tw-text-white tw-font-bold' : '' }}">Modules</a></li>
                    <li><a href="{{ route('admin.settings.email-templates') }}" class="{{ request()->routeIs('admin.settings.email-templates') ? 'tw-bg-indigo-50/5 tw-text-white tw-font-bold' : '' }}">E-mail Templates</a></li>
                </ul>
            </li>
            @endif
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->first_name) . '&color=7F9CF5&background=EBF4FF' }}" class="footer-avatar">
        <div class="footer-user-name">{{ auth()->user()->first_name }}</div>
        <a href="{{ route('logout') }}" class="logout-btn" title="Logout">
            <i class="fa fa-power-off"></i>
        </a>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle dropdowns on click
        $(document).on('click', '.dropdown-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $item = $(this).closest('.has-dropdown');
            $item.toggleClass('open');
        });

        // Close dropdowns when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.has-dropdown').length) {
                // Don't close already active ones
                $('.has-dropdown:not(.active)').removeClass('open');
            }
        });
    });
</script>
@endpush
