@php
    $navTr = [
        'en' => ['login'=>'Login','register'=>'Create New Account','get_quote'=>'Get Quote','my_account'=>'My Account','my_bookings'=>'My Bookings','my_messages'=>'My Messages','edit_account'=>'Edit Account','logout'=>'Logout'],
        'ar' => ['login'=>'تسجيل الدخول','register'=>'إنشاء حساب جديد','get_quote'=>'احصل على عرض','my_account'=>'حسابي','my_bookings'=>'حجوزاتي','my_messages'=>'رسائلي','edit_account'=>'تعديل الحساب','logout'=>'تسجيل الخروج'],
        'fr' => ['login'=>'Connexion','register'=>'Créer un compte','get_quote'=>'Obtenir un devis','my_account'=>'Mon compte','my_bookings'=>'Mes réservations','my_messages'=>'Mes messages','edit_account'=>'Modifier le compte','logout'=>'Déconnexion'],
        'it' => ['login'=>'Accedi','register'=>'Crea account','get_quote'=>'Richiedi preventivo','my_account'=>'Mio account','my_bookings'=>'Le mie prenotazioni','my_messages'=>'I miei messaggi','edit_account'=>'Modifica account','logout'=>'Esci'],
        'es' => ['login'=>'Iniciar sesión','register'=>'Crear cuenta','get_quote'=>'Solicitar presupuesto','my_account'=>'Mi cuenta','my_bookings'=>'Mis reservas','my_messages'=>'Mis mensajes','edit_account'=>'Editar cuenta','logout'=>'Cerrar sesión'],
        'ge' => ['login'=>'Anmelden','register'=>'Konto erstellen','get_quote'=>'Angebot anfordern','my_account'=>'Mein Konto','my_bookings'=>'Meine Buchungen','my_messages'=>'Meine Nachrichten','edit_account'=>'Konto bearbeiten','logout'=>'Abmelden'],
    ];
    $nt = $navTr[strtolower($lang)] ?? $navTr['en'];
    $langNames = ['en'=>'English','ar'=>'العربية','fr'=>'Français','it'=>'Italiano','es'=>'Español','ge'=>'Deutsch','de'=>'Deutsch','zh'=>'中文','ru'=>'Русский','ja'=>'日本語'];
    $currencyList = [
        ['code'=>'USD', 'label'=>'USD'],
        ['code'=>'JOD', 'label'=>'JOD'],
        ['code'=>'EUR', 'label'=>'EUR'],
    ];
    $activeCurrencyCode = $activeCurrency ?? 'USD';
    
    $headerBg = (isset($isHome) && $isHome) ? 'bg-transparent' : 'bg-[#0f1729]';
    $headerPos = (isset($isHome) && $isHome) ? 'absolute' : 'fixed';
    $headerOverlay = (isset($isHome) && $isHome) ? 'bg-gradient-to-b from-black/80 via-black/40 to-transparent' : '';
@endphp

<header id="main-nav" class="{{ $headerPos }} top-0 left-0 right-0 z-[100] h-[64px] transition-all duration-300 {{ $headerBg }} {{ $headerOverlay }}">
    <div class="max-w-[1400px] mx-auto px-4 h-full flex items-stretch">
        
        <!-- Logo Section -->
        <div class="flex items-center shrink-0 mr-6">
            <a href="{{ url($lang) }}/" class="block">
                <img src="{{ asset((isset($profile['logo']) && $profile['logo']) ? 'uploads/' . $profile['logo'] : 'Pvtnew1.png') }}" alt="PV Travels" class="h-10 w-auto">
            </a>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-stretch gap-1">
            {{-- HOME --}}
            <a href="{{ url($lang) }}/" class="flex items-center gap-2 px-4 h-full text-white hover:text-amber-200 transition-colors group">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                <span class="text-[14px] font-bold tracking-tight">Home</span>
            </a>

            @foreach($navItems as $item)
                @php $label = strtolower($item->label); @endphp
                @if($label === 'home') @continue @endif

                <div class="relative group flex items-stretch">
                    <a href="{{ $item->link }}" class="flex items-center gap-1.5 px-4 h-full text-white hover:text-amber-200 transition-colors">
                        <span class="text-[14px] font-bold tracking-tight">{{ $item->label }}</span>
                        @if(isset($navChildren[$item->lang_id]) && count($navChildren[$item->lang_id]) > 0)
                        <svg class="w-3 h-3 opacity-60 group-hover:rotate-180 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        @endif
                    </a>

                    @if(isset($navChildren[$item->lang_id]) && count($navChildren[$item->lang_id]) > 0)
                    <div class="absolute left-0 top-[64px] hidden group-hover:block transition-all z-[110]">
                        <div class="bg-white shadow-xl py-1 min-w-[220px] rounded-b-md border-t-2 border-[#ba6a38]">
                            @foreach($navChildren[$item->lang_id] as $child)
                                <a href="{{ $child->link }}" class="block px-5 py-3 text-[13px] font-semibold text-gray-800 hover:bg-orange-50 hover:text-[#a44b11] border-b border-gray-50 last:border-0">{{ $child->label }}</a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </nav>

        <!-- Right Side Controls -->
        <div class="flex-grow flex justify-end items-stretch">
            <div class="hidden sm:flex items-stretch">
                <!-- Currency -->
                <div class="relative group flex items-stretch">
                    <button class="flex items-center gap-1.5 px-4 h-full text-white text-[13px] font-bold uppercase hover:bg-black/10">
                        {{ $activeCurrencyCode }}
                        <svg class="w-3 h-3 opacity-60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <div class="absolute right-0 top-[64px] hidden group-hover:block z-[110]">
                        <div class="bg-white shadow-xl py-1 min-w-[100px] border-t-2 border-[#ba6a38]">
                            @foreach($currencyList as $cur)
                                <a href="javascript:void(0);" onclick="document.cookie='user_currency={{ $cur['code'] }};path=/'; location.reload();" 
                                   class="block px-4 py-2.5 text-[12px] font-bold text-gray-700 hover:bg-gray-100 {{ $activeCurrencyCode == $cur['code'] ? 'text-[#a44b11]' : '' }}">
                                    {{ $cur['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Language -->
                @if(isset($activeLangs) && count($activeLangs) > 1)
                <div class="relative group flex items-stretch">
                    <button class="flex items-center gap-1.5 px-4 h-full text-white text-[13px] font-bold uppercase hover:bg-black/10">
                        {{ $lang }}
                        <svg class="w-3 h-3 opacity-60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <div class="absolute right-0 top-[64px] hidden group-hover:block z-[110]">
                        <div class="bg-white shadow-xl py-1 min-w-[100px] border-t-2 border-[#ba6a38]">
                            @foreach($activeLangs as $al)
                                @php
                                    if(isset($alternateUrls) && isset($alternateUrls[$al])) {
                                        $newUrl = $alternateUrls[$al];
                                    } else {
                                        $segments = request()->segments();
                                        if(count($segments) > 0) {
                                            $segments[0] = $al;
                                            $newUrl = url(implode('/', $segments));
                                        } else {
                                            $newUrl = url($al) . '/';
                                        }
                                    }
                                    // Preserve query strings if any
                                    if(request()->getQueryString() && !str_contains($newUrl, '?')) {
                                        $newUrl .= '?' . request()->getQueryString();
                                    }
                                @endphp
                                <a href="{{ $newUrl }}" class="block px-4 py-2.5 text-[12px] font-bold text-gray-700 hover:bg-gray-100 {{ strtolower($al) == strtolower($lang) ? 'text-[#a44b11]' : '' }}">
                                    {{ strtoupper($al) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Get Quotation Button -->
                <div class="flex items-center px-2">
                    <a href="/create-trip" class="flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[13px] font-bold rounded transition-colors whitespace-nowrap">
                        {{ $nt['get_quote'] }}
                    </a>
                </div>

                <!-- Account -->
                <div class="relative group flex items-stretch">
                    <button class="flex items-center gap-1.5 px-4 h-full text-white text-[13px] font-bold hover:bg-black/10">
                        {{ $nt['my_account'] }}
                        <svg class="w-3 h-3 opacity-60" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    </button>
                    <div class="absolute right-0 top-[64px] hidden group-hover:block z-[110]">
                        <div class="bg-white shadow-xl py-1 min-w-[180px] border-t-2 border-[#ba6a38]">
                            @auth
                                <a href="{{ url($lang) }}/users/account/my-bookings/" class="block px-4 py-3 text-[13px] font-bold text-gray-700 hover:bg-gray-100 border-b border-gray-50">{{ $nt['my_bookings'] }}</a>
                                <a href="{{ url($lang) }}/users/account/my-messages/" class="block px-4 py-3 text-[13px] font-bold text-gray-700 hover:bg-gray-100 border-b border-gray-50">{{ $nt['my_messages'] }}</a>
                                <a href="{{ url($lang) }}/users/account/edit-account/" class="block px-4 py-3 text-[13px] font-bold text-gray-700 hover:bg-gray-100 border-b border-gray-50">{{ $nt['edit_account'] }}</a>
                                <a href="{{ url($lang) }}/users/logout/" class="block px-4 py-3 text-[13px] font-bold text-red-600 hover:bg-red-50">{{ $nt['logout'] }}</a>
                            @else
                                <a href="{{ url($lang) }}/users/login/" class="block px-4 py-3 text-[13px] font-bold text-gray-700 hover:bg-gray-100 border-b border-gray-50">{{ $nt['login'] }}</a>
                                <a href="{{ url($lang) }}/users/register/" class="block px-4 py-3 text-[13px] font-bold text-gray-700 hover:bg-gray-100">{{ $nt['register'] }}</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Btn -->
            <button class="lg:hidden px-4 text-white hover:bg-white/10" onclick="toggleMobileMenu()">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
</header>

<!-- Mobile Navigation Drawer -->
<div id="mobile-nav" class="fixed inset-0 z-[200] hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="toggleMobileMenu()"></div>
    <div class="absolute top-0 right-0 bottom-0 w-[80%] max-w-sm bg-white shadow-2xl flex flex-col p-6 overflow-y-auto">
        <div class="flex justify-between items-center mb-10 pb-4 border-b">
            <img src="{{ asset((isset($profile['logo']) && $profile['logo']) ? 'uploads/' . $profile['logo'] : 'Pvtnew1.png') }}" alt="PV" class="h-9 brightness-0">
            <button onclick="toggleMobileMenu()" class="p-2 hover:bg-gray-100 rounded-full">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="space-y-6">
            <a href="{{ url($lang) }}/" class="flex items-center gap-3 text-lg font-bold text-gray-900">
                <svg class="w-5 h-5 text-[#a44b11]" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg> 
                Home
            </a>
            @foreach($navItems as $item)
                <div class="space-y-3">
                    <a href="{{ $item->link }}" class="block text-[16px] font-bold text-gray-800">{{ $item->label }}</a>
                    @if(isset($navChildren[$item->lang_id]) && count($navChildren[$item->lang_id]) > 0)
                        <div class="pl-4 space-y-2 border-l-2 border-orange-200">
                            @foreach($navChildren[$item->lang_id] as $child)
                                <a href="{{ $child->link }}" class="block text-sm text-gray-600 font-medium hover:text-[#a44b11] transition-colors">{{ $child->label }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-auto pt-8 border-t space-y-4">
            @auth
                <a href="{{ url($lang) }}/users/account/my-bookings/" class="block text-gray-700 font-bold text-center py-2 bg-gray-50 rounded-lg">My Bookings</a>
                <a href="{{ url($lang) }}/users/account/my-messages/" class="block text-gray-700 font-bold text-center py-2 bg-gray-50 rounded-lg">My Messages</a>
                <a href="{{ url($lang) }}/users/logout/" class="block text-red-600 font-bold text-center py-2 border border-red-100 rounded-lg">Logout</a>
            @else
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ url($lang) }}/users/login/" class="flex justify-center p-3 rounded-lg border border-gray-200 text-sm font-bold text-gray-800">{{ $nt['login'] }}</a>
                    <a href="{{ url($lang) }}/users/register/" class="flex justify-center p-3 rounded-lg bg-[#a44b11] text-sm font-bold text-white shadow-md">{{ $nt['register'] }}</a>
                </div>
            @endauth
        </div>
    </div>
</div>

{{-- Dynamic Sticky Script --}}
@if(isset($isHome) && $isHome)
<script>
    window.addEventListener('scroll', function() {
        const header = document.getElementById('main-nav');
        if (window.scrollY > 50) {
            header.classList.remove('bg-transparent', 'absolute', 'bg-gradient-to-b', 'from-black/80', 'via-black/40', 'to-transparent');
            header.classList.add('fixed', 'bg-[#0f1729]', 'shadow-xl');
        } else {
            header.classList.add('bg-transparent', 'absolute', 'bg-gradient-to-b', 'from-black/80', 'via-black/40', 'to-transparent');
            header.classList.remove('fixed', 'bg-[#0f1729]', 'shadow-xl');
        }
    });
</script>
@endif

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-nav');
        menu.classList.toggle('hidden');
    }
</script>

@if(!(isset($isHome) && $isHome))
<div class="h-[64px]"></div>
@endif
