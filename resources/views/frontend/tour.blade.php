@extends('frontend.layout')

@section('title', htmlspecialchars_decode($content->title ?? 'Tour') . ' | PVT')

@section('content')
@php
    $currSymbols = ['USD'=>'$','JOD'=>'JD','EUR'=>'€'];
    $currRates   = ['USD'=>1,'JOD'=>0.709,'EUR'=>0.92];
    $sym  = $currSymbols[$activeCurrency] ?? '$';
    $rate = $currRates[$activeCurrency] ?? 1;
    $currCode = $activeCurrency ?? 'USD';

    // Try min_price first; if 0, calculate from pricing_bases
    $rawPrice = floatval($tour->min_price ?? 0);

    if ($rawPrice <= 0) {
        // Try regular season bases
        foreach (['pricing_bases', 'pricing_bases_low', 'pricing_bases_high'] as $field) {
            $bases = $tour->$field ? @unserialize($tour->$field, ['allowed_classes' => false]) : [];
            if (is_array($bases) && count($bases) > 0) {
                foreach ($bases as $base) {
                    $p = floatval($base['price'] ?? 0);
                    if ($p > 0 && ($rawPrice <= 0 || $p < $rawPrice)) {
                        $rawPrice = $p;
                    }
                }
                if ($rawPrice > 0) break;
            }
        }
    }

    $displayPrice = round($rawPrice * $rate);

    // Pricing modal data
    $hotelCategoryNames = [
        0 => 'Without Hotel Accommodations',
        1 => '1 Star',
        2 => '2 Star',
        3 => '3 Star',
        4 => '4 Star',
        5 => '5 Star',
    ];
    $modalBases  = $tour->pricing_bases  ? @unserialize($tour->pricing_bases)  : [];
    $modalGroups = $tour->pricing_groups ? @unserialize($tour->pricing_groups) : [];
    
    if (!is_array($modalBases)) {
        $modalBases = [];
    }
    
    $hasPricingModal = count($modalBases) > 0;
@endphp
<!-- TOUR HERO SECTION -->
<section class="relative h-[650px] flex items-end overflow-hidden -mt-[92px]">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0 group">
        @if($tour->image)
            <img src="{{ Str::startsWith($tour->image, 'http') ? $tour->image : asset($tour->image) }}" alt="{{ $content->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
        @else
            <div class="w-full h-full bg-slate-900"></div>
        @endif
        <!-- Enhanced Gradients for better readability -->
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-900/40 to-transparent"></div>
        <div class="absolute inset-0 bg-black/10"></div>
    </div>
    
    <!-- Content Overlay -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 pb-20 w-full">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10">
            <div class="flex-1">
                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-gray-300 text-xs font-bold uppercase tracking-widest mb-8 bg-black/30 backdrop-blur-md w-fit px-5 py-2 rounded-full border border-white/10 shadow-lg">
                    <a href="/{{ $lang }}/" class="hover:text-amber-400 transition">HOME</a>
                    <span class="opacity-30">/</span>
                    <a href="/{{ $lang }}/tours/" class="hover:text-amber-400 transition">TOURS</a>
                    <span class="opacity-30">/</span>
                    <span class="text-white">{{ strtoupper($startCountry->name ?? 'JORDAN') }}</span>
                </nav>
                
                <h1 class="text-5xl md:text-7xl font-black text-white leading-[1.05] mb-8 drop-shadow-2xl max-w-4xl">
                    {!! htmlspecialchars_decode($content->title ?? '') !!}
                </h1>
                
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-xl px-5 py-2.5 rounded-2xl border border-white/20 shadow-xl">
                        <i data-lucide="map-pin" class="w-5 h-5 text-amber-400"></i>
                        <span class="text-sm font-bold text-white tracking-tight">
                            {{ $startCity->name ?? '' }} TO {{ $finishCity->name ?? '' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/10 backdrop-blur-xl px-5 py-2.5 rounded-2xl border border-white/20 shadow-xl">
                        <i data-lucide="calendar" class="w-5 h-5 text-amber-400"></i>
                        <span class="text-sm font-bold text-white tracking-tight">
                            {{ $tour->days }} DAYS / {{ $tour->nights }} NIGHTS
                        </span>
                    </div>
                    <div class="flex items-center gap-2 bg-amber-500 text-white px-5 py-2.5 rounded-2xl font-black shadow-2xl shadow-amber-500/40 border border-amber-400">
                        <i data-lucide="star" class="w-5 h-5 fill-current"></i>
                        <span class="text-base">{{ number_format($tour->rating ?? 5.0, 1) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Price Card on Hero -->
            <div class="hidden lg:block w-72">
                <div class="bg-white/10 backdrop-blur-3xl p-8 rounded-[2.5rem] border border-white/20 shadow-2xl">
                    <p class="text-[11px] font-black text-white/60 mb-1 uppercase tracking-[0.3em]">LIVE PRICING FROM</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-sm font-bold text-amber-400">{{ $sym }}</span>
                        <p class="text-6xl font-black text-amber-400 leading-none">{{ number_format($displayPrice, 0) }}</p>
                    </div>
                    <p class="text-[10px] text-white/40 mt-4 font-bold uppercase tracking-widest italic font-sans">*ALL TAXES INCLUDED</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MAIN CONTENT GRID -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-20">
    <div class="flex flex-col lg:flex-row gap-20">
        
        <!-- LEFT COLUMN: MAIN INFO -->
        <div class="lg:w-2/3">
            
            <!-- HIGHLIGHTS GRID -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-20">
                <div class="bg-blue-50/50 p-8 rounded-[2.5rem] border border-blue-100/50 group hover:bg-white hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition-transform duration-500">
                        <i data-lucide="compass" class="w-7 h-7 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">CATEGORY</p>
                        <p class="text-lg font-black text-gray-900 leading-tight">{{ $category->name ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="bg-emerald-50/50 p-8 rounded-[2.5rem] border border-emerald-100/50 group hover:bg-white hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition-transform duration-500">
                        <i data-lucide="zap" class="w-7 h-7 text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">TOUR TYPE</p>
                        <p class="text-lg font-black text-gray-900 leading-tight">{{ $type->name ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="bg-amber-50/50 p-8 rounded-[2.5rem] border border-amber-100/50 group hover:bg-white hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition-transform duration-500">
                        <i data-lucide="map" class="w-7 h-7 text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">STARTING POINT</p>
                        <p class="text-lg font-black text-gray-900 leading-tight">{{ $startCity->name ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="bg-rose-50/50 p-8 rounded-[2.5rem] border border-rose-100/50 group hover:bg-white hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500">
                    <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition-transform duration-500">
                        <i data-lucide="check-circle" class="w-7 h-7 text-rose-600"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">STATUS</p>
                        <p class="text-lg font-black text-emerald-600 leading-tight">CONFIRMED</p>
                    </div>
                </div>
            </div>

            <!-- TABBED NAV -->
            <div class="flex items-center gap-4 mb-12">
                <div class="inline-flex p-1.5 bg-gray-100/80 backdrop-blur-sm rounded-2xl border border-gray-200/50 shadow-inner">
                    <button id="tab-itinerary" onclick="switchTab('itinerary')" class="px-10 py-3.5 rounded-xl text-sm font-black bg-white shadow-xl shadow-black/5 text-gray-900 transition-all">ITINERARY</button>
                    @if($images->count() > 0)
                    <button id="tab-gallery" onclick="switchTab('gallery')" class="px-10 py-3.5 rounded-xl text-sm font-bold text-gray-500 hover:text-gray-900 transition-all">GALLERY</button>
                    @endif
                </div>
                <div class="h-[1px] flex-grow bg-gray-100"></div>
            </div>

            <!-- DESCRIPTION / ITINERARY -->
            <div id="content-itinerary" class="prose prose-xl max-w-none text-gray-800 leading-[1.8] tour-details-content">
                {!! htmlspecialchars_decode($content->desc ?? '') !!}
            </div>

            <!-- GALLERY SECTION -->
            @if($images->count() > 0)
            <div id="content-gallery" class="hidden">
                <div class="flex items-center gap-6 mb-12">
                    <h3 class="text-4xl font-black text-gray-900">Experience in Photos</h3>
                    <div class="h-[2px] flex-1 bg-gray-100"></div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($images as $img)
                    <div class="group relative aspect-square rounded-[2rem] overflow-hidden cursor-zoom-in" onclick="openLightbox('{{ $img->image }}')">
                        <img src="{{ $img->image }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" alt="Gallery Image">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                            <i data-lucide="maximize" class="w-10 h-10 text-white scale-75 group-hover:scale-100 transition-transform duration-500"></i>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Lightbox Modal -->
            <div id="lightbox-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/90" onclick="closeLightbox()">
                <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white text-4xl font-bold hover:text-gray-300 z-10">&times;</button>
                <img id="lightbox-img" src="" class="max-w-[90vw] max-h-[90vh] object-contain rounded-2xl" onclick="event.stopPropagation()">
            </div>

            <script>
            function switchTab(tab) {
                const itineraryContent = document.getElementById('content-itinerary');
                const galleryContent = document.getElementById('content-gallery');
                const itineraryTab = document.getElementById('tab-itinerary');
                const galleryTab = document.getElementById('tab-gallery');

                if (tab === 'itinerary') {
                    itineraryContent.classList.remove('hidden');
                    if (galleryContent) galleryContent.classList.add('hidden');
                    itineraryTab.className = 'px-10 py-3.5 rounded-xl text-sm font-black bg-white shadow-xl shadow-black/5 text-gray-900 transition-all';
                    if (galleryTab) galleryTab.className = 'px-10 py-3.5 rounded-xl text-sm font-bold text-gray-500 hover:text-gray-900 transition-all';
                } else {
                    itineraryContent.classList.add('hidden');
                    if (galleryContent) galleryContent.classList.remove('hidden');
                    itineraryTab.className = 'px-10 py-3.5 rounded-xl text-sm font-bold text-gray-500 hover:text-gray-900 transition-all';
                    if (galleryTab) galleryTab.className = 'px-10 py-3.5 rounded-xl text-sm font-black bg-white shadow-xl shadow-black/5 text-gray-900 transition-all';
                }
            }

            function openLightbox(src) {
                document.getElementById('lightbox-img').src = src;
                document.getElementById('lightbox-modal').classList.remove('hidden');
                document.getElementById('lightbox-modal').classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                document.getElementById('lightbox-modal').classList.add('hidden');
                document.getElementById('lightbox-modal').classList.remove('flex');
                document.body.style.overflow = '';
            }
            </script>

        </div>

        <!-- RIGHT COLUMN: SIDEBAR -->
        <div class="lg:w-1/3">
            <div class="sticky top-28 space-y-10">
                
                <!-- PREMIUM SIDEBAR CARD -->
                <div class="bg-white rounded-[3rem] shadow-[0_48px_80px_-24px_rgba(30,41,59,0.15)] border border-gray-100 overflow-hidden ring-1 ring-gray-950/5">
                    <div class="bg-[#0f172a] p-10 text-white">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-[11px] font-black text-blue-400 uppercase tracking-[0.3em]">PACKAGE DEAL</p>
                            <span class="bg-amber-500 text-[10px] font-black px-2.5 py-1 rounded text-[#0f172a] uppercase shadow-lg shadow-amber-500/20">SAVE 15%</span>
                        </div>
                        <div class="flex items-baseline gap-2" style="flex-wrap:wrap;">
                            <span class="text-6xl font-black text-amber-400 leading-none">{{ $sym }}{{ number_format($displayPrice, 0) }}</span>
                            <span class="text-sm font-bold opacity-40 italic font-serif">/ person</span>
                            @if($hasPricingModal)
                            <button type="button" onclick="document.getElementById('pvtPricingModal').classList.add('pvt-modal-active'); document.body.style.overflow='hidden';" title="View full pricing" style="background:#3b5fa0;color:white;border:none;border-radius:50%;width:24px;height:24px;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;margin-left:4px;">&#x2139;</button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-10 pb-12 space-y-10">
                        <div class="bg-slate-50 border border-slate-100 rounded-[2rem] p-8 space-y-5 shadow-inner">
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center ring-1 ring-slate-100 group-hover:scale-110 transition-transform">
                                        <i data-lucide="check" class="w-5 h-5 text-emerald-500"></i>
                                    </div>
                                    <span class="text-base font-bold text-slate-600">VIP Concierge</span>
                                </div>
                                <span class="bg-emerald-100 text-emerald-700 text-[9px] font-black px-2 py-1 rounded-md uppercase tracking-wide">INCLUDED</span>
                            </div>
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center ring-1 ring-slate-100 group-hover:scale-110 transition-transform">
                                        <i data-lucide="hotel" class="w-5 h-5 text-emerald-500"></i>
                                    </div>
                                    <span class="text-base font-bold text-slate-600">Luxury Hotels</span>
                                </div>
                                <span class="bg-emerald-100 text-emerald-700 text-[9px] font-black px-2 py-1 rounded-md uppercase tracking-wide">INCLUDED</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <a href="/{{ $lang }}/tours/book_tour/{{ $tour->id }}/" class="group relative w-full bg-gradient-to-r from-orange-500 to-rose-600 hover:from-orange-600 hover:to-rose-700 text-white font-black py-6 rounded-2xl flex items-center justify-center gap-4 shadow-2xl shadow-rose-500/30 hover:shadow-rose-500/50 hover:-translate-y-1.5 active:translate-y-0 transition-all duration-500 overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                <i data-lucide="zap" class="w-7 h-7 fill-current transition-transform duration-500 group-hover:rotate-12"></i>
                                <span class="text-xl uppercase tracking-widest">RESERVE NOW</span>
                            </a>
                            
                            <a href="/{{ $lang }}/tours/inquery/{{ $tour->id }}/" class="w-full bg-white border-2 border-slate-100 text-slate-800 font-black py-5 rounded-2xl flex items-center justify-center gap-3 hover:bg-slate-50 hover:border-slate-200 hover:-translate-y-1 transition-all duration-500 shadow-sm">
                                <i data-lucide="message-square" class="w-6 h-6 text-slate-400"></i>
                                <span class="text-sm uppercase tracking-widest">CUSTOM REQUEST</span>
                            </a>
                        </div>
                        
                        <!-- Trust Badges -->
                        <div class="flex items-center justify-center gap-8 pt-8 border-t border-slate-100 opacity-40 grayscale group hover:grayscale-0 hover:opacity-100 transition-all duration-700">
                            <div class="flex flex-col items-center gap-2 font-black">
                                <i data-lucide="shield-check" class="w-7 h-7"></i>
                                <span class="text-[9px] uppercase tracking-widest">SECURE</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 font-black">
                                <i data-lucide="credit-card" class="w-7 h-7"></i>
                                <span class="text-[9px] uppercase tracking-widest">VISA</span>
                            </div>
                            <div class="flex flex-col items-center gap-2 font-black">
                                <i data-lucide="globe-2" class="w-7 h-7"></i>
                                <span class="text-[9px] uppercase tracking-widest">ONLINE</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HELP CENTER CARD -->
                <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-[3rem] p-12 text-white shadow-2xl shadow-amber-900/20 relative overflow-hidden group">
                    <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-6 mb-8">
                            <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center ring-1 ring-white/20 shadow-xl">
                                <i data-lucide="phone-forwarded" class="w-8 h-8 text-amber-100"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black opacity-60 uppercase tracking-widest mb-1">AVAILABLE 24/7</p>
                                <p class="text-2xl font-black">Fast Support</p>
                            </div>
                        </div>
                        <p class="text-amber-50 text-base mb-10 leading-relaxed font-medium">Need immediate assistance or want to talk to a local expert? We're just a call away.</p>
                        <a href="tel:+96277996601" class="block w-full bg-white text-amber-600 py-5 rounded-2xl text-center text-xl font-black shadow-xl hover:bg-white hover:-translate-y-1.5 transition-all duration-500 ring-2 ring-white hover:ring-white">
                            <span dir="ltr">+962 77996601</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
/* CONTENT TYPOGRAPHY CLEANUP */
.tour-details-content {
    font-family: 'Inter', sans-serif !important;
}

.tour-details-content h1, 
.tour-details-content h2, 
.tour-details-content h3 {
    @apply font-black text-gray-950 mt-16 mb-8 tracking-tight !important;
}

.tour-details-content p {
    @apply mb-8 leading-[1.8] text-gray-700 !important;
}

.tour-details-content p strong {
    @apply text-orange-600 bg-orange-50 px-4 py-2 rounded-xl border border-orange-100 font-black inline-block mb-4 shadow-sm !important;
}

.tour-details-content ul {
    @apply space-y-6 mb-12 list-none p-0 !important;
}

.tour-details-content li {
    @apply relative pl-12 text-gray-700 font-medium !important;
}

.tour-details-content li::before {
    content: '';
    @apply absolute left-0 top-1 w-8 h-8 bg-white rounded-xl border-2 border-orange-200 flex items-center justify-center shadow-sm !important;
}

.tour-details-content li::after {
    content: '✓';
    @apply absolute left-2.5 top-2.5 w-3 h-3 text-orange-600 text-[12px] font-black flex items-center justify-center !important;
}

/* Image zoom effect on hover */
.cursor-zoom-in img {
    transition: transform 1.2s cubic-bezier(0.165, 0.84, 0.44, 1);
}
</style>

{{-- ═══ FULL PRICING MODAL ═══ --}}
@if($hasPricingModal)
<style>
@keyframes modalFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes modalScaleIn {
    from { opacity: 0; transform: translateY(-40px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.pvt-modal-active {
    display: flex !important;
    animation: modalFadeIn 0.3s ease-out forwards;
}
.pvt-modal-active .pvt-modal-content {
    animation: modalScaleIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}
</style>
<div id="pvtPricingModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:99999; align-items:flex-start; justify-content:center; padding:40px 16px; overflow-y:auto;" onclick="if(event.target===this){this.classList.remove('pvt-modal-active');document.body.style.overflow='';}">
    <div class="pvt-modal-content" style="background:white; border-radius:6px; width:100%; max-width:700px; box-shadow:0 20px 60px rgba(0,0,0,0.35); position:relative;">
        {{-- Modal Header --}}
        <div style="padding:14px 20px; border-bottom:1px solid #ddd; display:flex; align-items:center; justify-content:space-between;">
            <span style="font-weight:bold; font-size:16px; display:flex; align-items:center; gap:8px;">
                <span style="background:#3b5fa0; color:white; border-radius:50%; width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0;">&#x2139;</span>
                Pricing
            </span>
            <button type="button" onclick="document.getElementById('pvtPricingModal').classList.remove('pvt-modal-active'); document.body.style.overflow='';" style="font-size:24px; background:none; border:none; cursor:pointer; color:#666; line-height:1; padding:0 4px; transition:color 0.2s;" onmouseover="this.style.color='#f00'" onmouseout="this.style.color='#666'">&times;</button>
        </div>

        {{-- Modal Body --}}
        <div style="max-height:75vh; overflow-y:auto;">
            @foreach($modalBases as $hotelKey => $base)
            @php
                $hotelName  = $hotelCategoryNames[intval($hotelKey)] ?? (intval($hotelKey).' Star');
                $basePrice  = number_format(floatval($base['price'] ?? 0) * $rate, 2);
                $suppPrice  = number_format(floatval($base['single_supplement'] ?? 0) * $rate, 2);
                $grpRows    = $modalGroups[$hotelKey] ?? [];
            @endphp
            {{-- Hotel Category Header --}}
            <div style="background:#555555; color:white; padding:10px 16px; font-weight:bold; font-size:14px;">{{ $hotelName }}</div>

            {{-- Base Price Row --}}
            <div style="padding:10px 16px; display:flex; justify-content:space-between; font-size:13px; border-bottom:1px solid #eeeeee; flex-wrap:wrap; gap:6px;">
                <span>Price: {{ $basePrice }} {{ $currCode }}</span>
                <span>Single supplement fee: {{ $suppPrice }} {{ $currCode }}</span>
            </div>

            @if(count($grpRows) > 0)
            {{-- Traveler Ranges Table --}}
            <div style="padding:12px 16px 4px;">
                <div style="text-align:center; font-weight:bold; font-size:13px; margin-bottom:8px;">Price Ranges Based on Travelers Number</div>
                <table style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom:16px;">
                    <thead>
                        <tr style="background:#d0e4f7;">
                            <th style="padding:8px 10px; text-align:center; color:#2c5282; font-weight:bold;">Min Number Of Travelers</th>
                            <th style="padding:8px 10px; text-align:center; color:#2c5282; font-weight:bold;">Adult</th>
                            <th style="padding:8px 10px; text-align:center; color:#2c5282; font-weight:bold;">Child</th>
                            <th style="padding:8px 10px; text-align:center; color:#2c5282; font-weight:bold;">Infant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grpRows as $minPax => $prices)
                        <tr style="border-bottom:1px solid #eeeeee;">
                            <td style="padding:8px 10px; text-align:center;">{{ $minPax }}</td>
                            <td style="padding:8px 10px; text-align:center;">{{ number_format(floatval($prices['adult'] ?? 0) * $rate, 2) }} {{ $currCode }}</td>
                            <td style="padding:8px 10px; text-align:center;">{{ number_format(floatval($prices['child'] ?? 0) * $rate, 2) }} {{ $currCode }}</td>
                            <td style="padding:8px 10px; text-align:center;">{{ number_format(floatval($prices['infant'] ?? 0) * $rate, 2) }} {{ $currCode }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
