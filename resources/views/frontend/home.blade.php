@extends('frontend.layout')

@section('title', 'Pv Travels - Tours in Jordan')
@section('description', 'Looking for a family holiday? Jordan is one of the ideal destination. Pv Travels provide best vacation packages for Jordan at affordable prices.')
@section('keywords', 'Jordan tours, Petra tours, travel Jordan, vacation Jordan')

@section('hero')
@php
    $t = [
        'en' => ['badge'=>'#1 Rated Travel Agency in Jordan','h1'=>'Experience the','h2'=>'Magic of Jordan','sub'=>'Journey through ancient history, breathtaking landscapes, and unforgettable adventures with our luxury tour packages.','dest'=>'Destination','cat'=>'Category','type'=>'Tour Type','minp'=>'Min Price','maxp'=>'Max Price','dur'=>'Duration','search'=>'Search','all_countries'=>'All Countries','all_cats'=>'All Categories','all_types'=>'All Types','min'=>'Min','max'=>'Max','days'=>'Days'],
        'ar' => ['badge'=>'وكالة السفر الأولى في الأردن#','h1'=>'عِش تجربة','h2'=>'سحر الأردن','sub'=>'اكتشف أكثر الوجهات حصرية في الأردن مع جولاتنا المميزة.','dest'=>'الوجهة','cat'=>'الفئة','type'=>'نوع الجولة','minp'=>'أقل سعر','maxp'=>'أعلى سعر','dur'=>'المدة','search'=>'بحث','all_countries'=>'جميع الدول','all_cats'=>'جميع الفئات','all_types'=>'جميع الأنواع','min'=>'أقل','max'=>'أعلى','days'=>'أيام'],
        'fr' => ['badge'=>'Agence de voyage #1 en Jordanie','h1'=>'Vivez la','h2'=>'Magie de la Jordanie','sub'=>'Découvrez les destinations les plus exclusives de Jordanie.','dest'=>'Destination','cat'=>'Catégorie','type'=>'Type de tour','minp'=>'Prix min','maxp'=>'Prix max','dur'=>'Durée','search'=>'Rechercher','all_countries'=>'Tous les pays','all_cats'=>'Toutes catégories','all_types'=>'Tous types','min'=>'Min','max'=>'Max','days'=>'Jours'],
        'it' => ['badge'=>'Agenzia di viaggi #1 in Giordania','h1'=>'Vivi la','h2'=>'Magia della Giordania','sub'=>'Scopri le destinazioni più esclusive della Giordania.','dest'=>'Destinazione','cat'=>'Categoria','type'=>'Tipo di tour','minp'=>'Prezzo min','maxp'=>'Prezzo max','dur'=>'Durata','search'=>'Cerca','all_countries'=>'Tutti i paesi','all_cats'=>'Tutte le categorie','all_types'=>'Tutti i tipi','min'=>'Min','max'=>'Max','days'=>'Giorni'],
        'es' => ['badge'=>'Agencia de viajes #1 en Jordania','h1'=>'Experimenta la','h2'=>'Magia de Jordania','sub'=>'Descubre los destinos más exclusivos de Jordania.','dest'=>'Destino','cat'=>'Categoría','type'=>'Tipo de tour','minp'=>'Precio mín','maxp'=>'Precio máx','dur'=>'Duración','search'=>'Buscar','all_countries'=>'Todos los países','all_cats'=>'Todas categorías','all_types'=>'Todos tipos','min'=>'Mín','max'=>'Máx','days'=>'Días'],
        'ge' => ['badge'=>'#1 Reiseagentur in Jordanien','h1'=>'Erleben Sie die','h2'=>'Magie Jordaniens','sub'=>'Entdecken Sie die exklusivsten Reiseziele in Jordanien.','dest'=>'Reiseziel','cat'=>'Kategorie','type'=>'Tourtyp','minp'=>'Mindestpreis','maxp'=>'Höchstpreis','dur'=>'Dauer','search'=>'Suchen','all_countries'=>'Alle Länder','all_cats'=>'Alle Kategorien','all_types'=>'Alle Typen','min'=>'Min','max'=>'Max','days'=>'Tage'],
    ];
    $tr = $t[strtolower($lang)] ?? $t['en'];
@endphp

<!-- HERO SECTION -->
<section class="relative h-[600px] flex items-center justify-center overflow-hidden -mt-16">
    <!-- Background Slider -->
    <div class="absolute inset-0 z-0">
        @if(isset($sliderImages) && count($sliderImages) > 0)
            @foreach($sliderImages as $i => $slide)
            <div class="hero-bg-slide absolute inset-0 transition-opacity duration-[1500ms] ease-in-out {{ $i === 0 ? 'opacity-100' : 'opacity-0' }}" data-index="{{ $i }}">
                @php
                    $ext = strtolower(pathinfo($slide->image, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($ext, ['mp4', 'webm', 'ogg']))
                    <video autoplay loop muted playsinline class="w-full h-full object-cover hero-ken-burns {{ $i === 0 ? 'active' : '' }}">
                        <source src="{{ asset('uploads/sliders/' . $slide->image) }}" type="video/{{ $ext === 'ogg' ? 'ogg' : ($ext === 'webm' ? 'webm' : 'mp4') }}">
                    </video>
                @else
                    <img src="{{ asset('uploads/sliders/' . $slide->image) }}" alt="" class="w-full h-full object-cover hero-ken-burns {{ $i === 0 ? 'active' : '' }}">
                @endif
            </div>
            @endforeach
        @else
            <div class="hero-bg-slide absolute inset-0 opacity-100">
                <img src="https://pvt.jo/theme/pvt/video/Marvelous-Jordan-5.jpg" alt="Marvelous Jordan" class="w-full h-full object-cover">
            </div>
        @endif
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-gray-900/40 bg-gradient-to-b from-gray-900/60 via-transparent to-gray-900/60 z-[1]"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 text-center text-white pt-12">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md border border-white/30 text-white text-sm font-semibold px-5 py-2 rounded-full mb-6 shadow-lg">
            <i data-lucide="award" class="w-4 h-4 text-amber-400"></i>
            <span>{{ $tr['badge'] }}</span>
        </div>

        <!-- Slider Captions (from admin DB, fallback to hardcoded) -->
        <div class="grid grid-cols-1 grid-rows-1 items-center justify-items-center">
            @if(isset($sliderImages) && count($sliderImages) > 0)
                @foreach($sliderImages as $i => $slide)
                <div class="hero-caption col-start-1 row-start-1 flex flex-col items-center justify-center transition-opacity duration-[1000ms] {{ $i === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}" data-caption-index="{{ $i }}">
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-tight mb-6 drop-shadow-xl">
                        {{ !empty($slide->text) ? $slide->text : $tr['h1'] }} <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-orange-300 to-amber-200">{{ !empty($slide->text2) ? $slide->text2 : $tr['h2'] }}</span>
                    </h1>
                    
                    <div class="min-h-[60px] flex flex-col items-center justify-center gap-4">
                        <p class="text-lg sm:text-xl md:text-2xl text-gray-100 max-w-3xl mx-auto font-light leading-relaxed drop-shadow-md">
                            {{ !empty($slide->text3) ? $slide->text3 : $tr['sub'] }}
                        </p>
                        @if(!empty($slide->link))
                        <div>
                            <a href="{{ $slide->link }}" class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white font-bold rounded-full shadow-lg hover:shadow-orange-500/40 hover:scale-105 transition-all">
                                <span>{{ $tr['search'] ?? 'Explore' }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="hero-caption flex flex-col items-center">
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-tight mb-6 drop-shadow-xl">
                        {{ $tr['h1'] }} <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-orange-300 to-amber-200">{{ $tr['h2'] }}</span>
                    </h1>
                    <p class="text-lg sm:text-xl md:text-2xl text-gray-100 max-w-3xl mx-auto font-light leading-relaxed drop-shadow-md">
                        {{ $tr['sub'] }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Slide indicators -->
        @if(isset($sliderImages) && count($sliderImages) > 1)
        <div class="flex items-center justify-center gap-2 mt-8 relative z-20">
            @foreach($sliderImages as $i => $slide)
            <button class="hero-dot w-2.5 h-2.5 rounded-full transition-all duration-500 {{ $i === 0 ? 'bg-white w-8' : 'bg-white/40' }}" data-index="{{ $i }}"></button>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
    @keyframes kenBurns {
        0%   { transform: scale(1) translate(0, 0); }
        100% { transform: scale(1.15) translate(-1%, -1%); }
    }
    .hero-ken-burns { transform: scale(1); }
    .hero-ken-burns.active { animation: kenBurns 8s ease-out forwards; }
    @keyframes heroFadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    /* Uniform height for all form elements */
    .search-bar input[type="number"] {
        height: 46px !important;
        min-height: 46px !important;
        max-height: 46px !important;
        padding: 0 12px !important;
        box-sizing: border-box !important;
        border-radius: 12px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        background-color: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
        width: 100% !important;
    }
    /* Slim Select custom dropdown fix */
    .search-bar .ss-main {
        height: 46px !important;
        min-height: 46px !important;
        max-height: 46px !important;
        border-radius: 12px !important;
        border: 1px solid #e5e7eb !important;
        background-color: #f9fafb !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        padding: 0 12px !important;
        display: flex !important;
        align-items: center !important;
        cursor: pointer !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .search-bar .ss-main .ss-single-selected {
        height: 46px !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 !important;
    }
    .search-bar .ss-main .ss-single-selected .placeholder,
    .search-bar .ss-main .ss-single-selected .ss-arrow {
        line-height: 46px !important;
    }
    /* Fallback for native select (if Slim Select not active) */
    .search-bar select {
        height: 46px !important;
        min-height: 46px !important;
        border-radius: 12px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        background-color: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
        padding: 0 12px !important;
    }
    .search-bar .search-btn {
        height: 46px !important;
        min-height: 46px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        gap: 6px;
    }
</style>
@endpush

@section('content')

<!-- SEARCH BAR (Overlapping & Glassmorphic) -->
<div class="max-w-6xl mx-auto px-4 -mt-20 relative z-30 mb-20">
    <div class="search-bar bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/40 p-4 sm:p-6 ring-1 ring-black/5">
        <form method="GET" action="{{ route('frontend.tours', ['lang' => $lang]) }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-3 items-end">

                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">{{ $tr['dest'] }}</label>
                    <select name="country" class="w-full bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                        <option value="">{{ $tr['all_countries'] }}</option>
                        @if(isset($countries))
                            @foreach($countries as $cid => $cname)
                                <option value="{{ $cid }}">{{ $cname }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">{{ $tr['cat'] }}</label>
                    <select name="category" class="w-full bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                        <option value="">{{ $tr['all_cats'] }}</option>
                        @if(isset($tourCategories))
                            @foreach($tourCategories as $cat)
                                <option value="{{ $cat->lang_id }}">{{ $cat->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">{{ $tr['type'] }}</label>
                    <select name="type" class="w-full bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                        <option value="">{{ $tr['all_types'] }}</option>
                        @if(isset($tourTypes))
                            @foreach($tourTypes as $type)
                                <option value="{{ $type->lang_id }}">{{ $type->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">{{ $tr['minp'] }}</label>
                    <input type="number" name="min_price" placeholder="{{ $tr['min'] }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">{{ $tr['maxp'] }}</label>
                    <input type="number" name="max_price" placeholder="{{ $tr['max'] }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">{{ $tr['dur'] }}</label>
                    <input type="number" name="days" placeholder="{{ $tr['days'] }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>

                <div class="lg:col-span-1">
                    <button type="submit" class="search-btn w-full bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg shadow-orange-500/30 hover:shadow-xl hover:shadow-orange-500/40 hover:-translate-y-0.5 transition-all text-sm uppercase tracking-wide gap-2">
                        <i data-lucide="search" class="w-4 h-4 inline-block"></i>
                        <span>{{ $tr['search'] }}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@php
    $ct = [
        'en'=>['popular'=>'Popular Tours','sub_popular'=>'Discover our most loved experiences, handpicked for unforgettable adventures.','tours_in'=>'Tours in Jordan','sub_tours'=>'Explore the beauty and heritage of the Hashemite Kingdom.','days'=>'Days','from'=>'From','details'=>'Details','view_all'=>'View All Tours','starting'=>'From'],
        'ar'=>['popular'=>'الجولات الشائعة','sub_popular'=>'اكتشف تجاربنا الأكثر شهرة.','tours_in'=>'جولات في الأردن','sub_tours'=>'استكشف جمال المملكة الأردنية الهاشمية وتراثها.','days'=>'أيام','from'=>'يبدأ من','details'=>'التفاصيل','view_all'=>'عرض جميع الجولات','starting'=>'يبدأ من'],
        'fr'=>['popular'=>'Circuits Populaires','sub_popular'=>'Découvrez nos expériences les plus appréciées.','tours_in'=>'Circuits en Jordanie','sub_tours'=>'Explorez la beauté du Royaume Hachémite.','days'=>'Jours','from'=>'À partir de','details'=>'Détails','view_all'=>'Voir tous les circuits','starting'=>'À partir de'],
        'it'=>['popular'=>'Tour Popolari','sub_popular'=>'Scopri le nostre esperienze più amate.','tours_in'=>'Tour in Giordania','sub_tours'=>'Esplora la bellezza del Regno Hashemita.','days'=>'Giorni','from'=>'Da','details'=>'Dettagli','view_all'=>'Vedi tutti i tour','starting'=>'A partire da'],
        'es'=>['popular'=>'Tours Populares','sub_popular'=>'Descubre nuestras experiencias más queridas.','tours_in'=>'Tours en Jordania','sub_tours'=>'Explora la belleza del Reino Hachemita.','days'=>'Días','from'=>'Desde','details'=>'Detalles','view_all'=>'Ver todos los tours','starting'=>'Desde'],
        'ge'=>['popular'=>'Beliebte Touren','sub_popular'=>'Entdecken Sie unsere beliebtesten Erlebnisse.','tours_in'=>'Touren in Jordanien','sub_tours'=>'Entdecken Sie die Schönheit des Haschemitischen Königreichs.','days'=>'Tage','from'=>'Ab','details'=>'Details','view_all'=>'Alle Touren anzeigen','starting'=>'Ab'],
    ];
    $cl = $ct[strtolower($lang)] ?? $ct['en'];
@endphp

@php
    $layoutBlocks = array_merge($center_top ?? [], $center_bottom ?? []);
@endphp

@foreach($layoutBlocks as $block)
    @php
        $bModule = $block[0] ?? '';
        $bName   = $block[1] ?? '';
        $bTitle  = $block[2] ?? '';
        
        $tList = null;
        $titleStr = '';
        $subStr = '';
        
        if ($bModule == 'tours' && $bName == 'latest_tours' && isset($latestTours) && count($latestTours) > 0) {
            $tList = $latestTours;
            $titleStr = !empty($bTitle) ? $bTitle : $cl['popular'];
            $subStr = $cl['sub_popular'];
        } elseif ($bModule == 'tours' && $bName == 'jordan_tours' && isset($jordanTours) && count($jordanTours) > 0) {
            $tList = $jordanTours;
            $titleStr = !empty($bTitle) ? $bTitle : $cl['tours_in'];
            $subStr = $cl['sub_tours'];
        }
    @endphp

    @if($tList)
    <!-- DYNAMIC LATEST/JORDAN TOURS SECTION -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">{{ $titleStr }}</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">{{ $subStr }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($tList as $tour)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col h-full">
                <div class="relative h-56 overflow-hidden">
                    <img src="{{ $tour->image }}" alt="{{ $tour->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.style.background='linear-gradient(135deg,#667eea,#764ba2)'; this.style.minHeight='224px'; this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';">
                    @if($tour->min_price > 0)
                    <div class="absolute top-4 left-0 bg-gradient-to-r from-orange-500 to-purple-600 text-white text-sm font-semibold px-4 py-1 rounded-r-full shadow-lg">
                        @php $hSym = ['USD'=>'$','JOD'=>'JD','EUR'=>'€'][$activeCurrency] ?? '$'; $hRate = ['USD'=>1,'JOD'=>0.709,'EUR'=>0.92][$activeCurrency] ?? 1; @endphp
                        {{ $cl['starting'] }} {{ $hSym }}{{ number_format(round($tour->min_price * $hRate)) }}
                    </div>
                    @endif
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg shadow-sm">
                        <div class="flex items-center gap-1">
                            <i data-lucide="star" class="w-4 h-4 text-amber-400 fill-current"></i>
                            <span class="text-sm font-semibold">{{ number_format($tour->rating ?? 5, 1) }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-1 truncate">{{ $tour->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2 flex-grow">{{ $tour->meta_desc }}</p>
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-50">
                        <div class="flex items-center gap-2 text-gray-500 text-sm font-medium">
                            <i data-lucide="clock" class="w-4 h-4 text-orange-500"></i>
                            <span>{{ $tour->days }} {{ $cl['days'] }}</span>
                        </div>
                        <a href="{{ url($lang . '/tours/' . strtolower($countries[$tour->start_country] ?? 'jordan') . '/' . $tour->url) }}/" class="text-orange-600 font-bold text-sm hover:text-orange-600 transition flex items-center gap-1">
                            <span>{{ $cl['details'] }}</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="/{{ $lang }}/tours/" class="inline-flex items-center gap-2 bg-gray-900 text-white font-semibold px-8 py-4 rounded-xl hover:bg-gray-800 transition-all hover:-translate-y-0.5 shadow-lg">
                <span>{{ $cl['view_all'] }}</span>
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </a>
        </div>
    </section>
    @endif
@endforeach




<!-- WHY CHOOSE US SECTION -->
@php
    $wcu = [
        'en'=>['title'=>'Why Choose Us','sub'=>"We're committed to making your Jordan experience unforgettable.",'t1'=>'Trusted Agency','d1'=>'Licensed and certified with 15+ years of experience.','t2'=>'Expert Guides','d2'=>'Professional local guides with deep cultural knowledge.','t3'=>'Best Reviews','d3'=>'Rated 5-stars on TripAdvisor with thousands of reviews.','t4'=>'24/7 Support','d4'=>'Round-the-clock assistance throughout your journey.'],
        'ar'=>['title'=>'لماذا تختارنا','sub'=>'نحن ملتزمون بجعل تجربتك في الأردن لا تُنسى.','t1'=>'وكالة موثوقة','d1'=>'مرخصة ومعتمدة بأكثر من 15 عامًا من الخبرة.','t2'=>'مرشدون خبراء','d2'=>'مرشدون محليون محترفون بمعرفة ثقافية عميقة.','t3'=>'أفضل التقييمات','d3'=>'تقييم 5 نجوم على تريب أدفايزر مع آلاف المراجعات.','t4'=>'دعم 24/7','d4'=>'مساعدة على مدار الساعة طوال رحلتك.'],
        'fr'=>['title'=>'Pourquoi Nous Choisir','sub'=>'Nous nous engageons à rendre votre expérience inoubliable.','t1'=>'Agence de Confiance','d1'=>'Agréée avec plus de 15 ans d\'expérience.','t2'=>'Guides Experts','d2'=>'Guides locaux avec une connaissance culturelle profonde.','t3'=>'Meilleurs Avis','d3'=>'Noté 5 étoiles sur TripAdvisor.','t4'=>'Support 24/7','d4'=>'Assistance 24h/24 pendant votre voyage.'],
        'it'=>['title'=>'Perché Sceglierci','sub'=>'Ci impegniamo a rendere indimenticabile la tua esperienza.','t1'=>'Agenzia Affidabile','d1'=>'Certificata con oltre 15 anni di esperienza.','t2'=>'Guide Esperte','d2'=>'Guide locali con profonda conoscenza culturale.','t3'=>'Migliori Recensioni','d3'=>'5 stelle su TripAdvisor.','t4'=>'Supporto 24/7','d4'=>'Assistenza continua durante il viaggio.'],
        'es'=>['title'=>'¿Por Qué Elegirnos?','sub'=>'Nos comprometemos a hacer su experiencia inolvidable.','t1'=>'Agencia Confiable','d1'=>'Certificada con más de 15 años de experiencia.','t2'=>'Guías Expertos','d2'=>'Guías locales con conocimiento cultural profundo.','t3'=>'Mejores Reseñas','d3'=>'5 estrellas en TripAdvisor.','t4'=>'Soporte 24/7','d4'=>'Asistencia las 24 horas durante su viaje.'],
        'ge'=>['title'=>'Warum Uns Wählen','sub'=>'Wir machen Ihr Jordanien-Erlebnis unvergesslich.','t1'=>'Vertrauenswürdige Agentur','d1'=>'Lizenziert mit über 15 Jahren Erfahrung.','t2'=>'Erfahrene Reiseführer','d2'=>'Professionelle lokale Guides.','t3'=>'Beste Bewertungen','d3'=>'5 Sterne auf TripAdvisor.','t4'=>'24/7 Support','d4'=>'Rund-um-die-Uhr-Unterstützung.'],
    ];
    $wl = $wcu[strtolower($lang)] ?? $wcu['en'];
@endphp

<section class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">{{ $wl['title'] }}</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">{{ $wl['sub'] }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-gray-50 p-8 rounded-2xl text-center shadow-sm hover:shadow-lg transition-all">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="shield-check" class="w-8 h-8 text-orange-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $wl['t1'] }}</h3>
                <p class="text-gray-600 text-sm">{{ $wl['d1'] }}</p>
            </div>

            <div class="bg-gray-50 p-8 rounded-2xl text-center shadow-sm hover:shadow-lg transition-all">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="users" class="w-8 h-8 text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $wl['t2'] }}</h3>
                <p class="text-gray-600 text-sm">{{ $wl['d2'] }}</p>
            </div>

            <div class="bg-gray-50 p-8 rounded-2xl text-center shadow-sm hover:shadow-lg transition-all">
                <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="star" class="w-8 h-8 text-amber-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $wl['t3'] }}</h3>
                <p class="text-gray-600 text-sm">{{ $wl['d3'] }}</p>
            </div>

            <div class="bg-gray-50 p-8 rounded-2xl text-center shadow-sm hover:shadow-lg transition-all">
                <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="headphones" class="w-8 h-8 text-purple-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $wl['t4'] }}</h3>
                <p class="text-gray-600 text-sm">{{ $wl['d4'] }}</p>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.hero-bg-slide');
    const dots   = document.querySelectorAll('.hero-dot');
    if (slides.length <= 1) return;

    let current = 0;
    const total = slides.length;

    const captions = document.querySelectorAll('.hero-caption');

    function goTo(next) {
        // Background crossfade
        slides[current].classList.remove('opacity-100');
        slides[current].classList.add('opacity-0');
        const prevImg = slides[current].querySelector('.hero-ken-burns');
        if (prevImg) prevImg.classList.remove('active');

        slides[next].classList.remove('opacity-0');
        slides[next].classList.add('opacity-100');
        const nextImg = slides[next].querySelector('.hero-ken-burns');
        if (nextImg) { nextImg.classList.remove('active'); void nextImg.offsetWidth; nextImg.classList.add('active'); }

        // Captions crossfade
        if (captions.length > 0) {
            captions.forEach(c => { c.classList.remove('opacity-100'); c.classList.add('opacity-0', 'pointer-events-none'); });
            if (captions[next]) { captions[next].classList.remove('opacity-0', 'pointer-events-none'); captions[next].classList.add('opacity-100'); }
        }

        // Dots
        dots.forEach(d => { d.classList.remove('bg-white', 'w-8'); d.classList.add('bg-white/40'); });
        if (dots[next]) { dots[next].classList.remove('bg-white/40'); dots[next].classList.add('bg-white', 'w-8'); }

        current = next;
    }

    // Auto-advance every 6s
    let timer = setInterval(() => goTo((current + 1) % total), 6000);

    // Dot click
    dots.forEach(d => d.addEventListener('click', function() {
        clearInterval(timer);
        goTo(parseInt(this.dataset.index));
        timer = setInterval(() => goTo((current + 1) % total), 6000);
    }));

    // Start first Ken Burns
    const firstImg = slides[0].querySelector('.hero-ken-burns');
    if (firstImg) firstImg.classList.add('active');

    // Re-init lucide icons for dynamically rendered icons
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
@endpush
