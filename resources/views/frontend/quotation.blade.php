@extends('frontend.layout')

@section('title', $quotation->description . ' | PVT')

@section('breadcrumb')
<div class="bg-slate-100 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-2 text-sm text-slate-500 font-medium">
        <a href="/{{ $lang }}/" class="hover:text-amber-600 transition-colors"><i class="fa fa-home"></i> Home</a>
        <i class="fa fa-angle-right text-slate-300"></i>
        <span class="text-slate-900 truncate">{{ $quotation->description }}</span>
    </div>
</div>
@endsection

@section('content')
<div id="quotation_contents" class="bg-slate-50 font-sans pb-16">
    
    {{-- Header Banner Section --}}
    <div class="relative bg-slate-900 overflow-hidden">
        {{-- Background Image with overlay --}}
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-slate-900/70 mix-blend-multiply z-10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 to-transparent z-10 opacity-90"></div>
            <img src="{{ asset('uploads/filemanager/quotation-header.jpg') }}" alt="Quotation Header" class="w-full h-full object-cover object-center opacity-60">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 relative z-20">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-8 justify-between">
                
                {{-- Quotation Details --}}
                <div class="w-full lg:w-2/3">
                    <img src="{{ asset('uploads/filemanager/logo-round-small.png') }}" alt="PVT Logo" class="w-24 h-24 mb-6 shadow-xl rounded-full border-4 border-white pb-0">
                    
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-8">
                        {{ $quotation->description }}
                    </h1>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-2xl">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-amber-500 uppercase tracking-widest">Customer</span>
                            <span class="text-white font-medium text-lg">{{ $quotation->customer_name }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-amber-500 uppercase tracking-widest">Travel Date</span>
                            <span class="text-white font-medium text-lg">{{ date('M d, Y', strtotime($quotation->travel_date)) }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-amber-500 uppercase tracking-widest">Reference No</span>
                            <span class="text-white font-medium text-lg">{{ $quotation->ref_number }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-amber-500 uppercase tracking-widest">Duration</span>
                            <span class="text-white font-medium text-lg">{{ $quotation->days }} Days / {{ $quotation->nights }} Nights</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-amber-500 uppercase tracking-widest">Travelers</span>
                            <span class="text-white font-medium text-lg">{{ $quotation->travelers_number }} People</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-amber-500 uppercase tracking-widest">Pricing</span>
                            <div class="text-white font-medium">
                                <span class="text-xl font-bold">{{ number_format($quotation->total ?? 0, 2) }}</span> <span class="text-sm">JOD Total</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Agent Box --}}
                @if($agent)
                <div class="w-full sm:w-80 shrink-0">
                    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-3xl p-8 shadow-2xl border border-amber-400 relative overflow-hidden">
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>
                        
                        <div class="relative z-10 flex flex-col items-center text-center">
                            <h3 class="text-white font-bold text-lg mb-6">Have Questions?</h3>
                            
                            <img src="{{ $agent->avatar ?: asset('uploads/filemanager/no-avatar.jpg') }}" alt="Agent Avatar" class="w-24 h-24 rounded-full border-4 border-white/30 shadow-lg mb-4 object-cover">
                            
                            <h4 class="text-xl font-extrabold text-white mb-4">{{ $agent->first_name }} {{ $agent->last_name }}</h4>
                            
                            <div class="flex flex-col gap-3 w-full">
                                <a href="mailto:{{ $agent->email }}" class="flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 transition-colors rounded-xl py-2 px-4 text-white text-sm font-medium">
                                    <i class="fa fa-envelope-o text-amber-200"></i> <span class="truncate">{{ $agent->email }}</span>
                                </a>
                                @if($agent->mobile)
                                <a href="tel:{{ $agent->mobile }}" class="flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 transition-colors rounded-xl py-2 px-4 text-white text-sm font-medium">
                                    <i class="fa fa-whatsapp text-emerald-300"></i> {{ $agent->mobile }}
                                </a>
                                @endif
                                @if($agent->phone)
                                <a href="tel:{{ $agent->phone }}" class="flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 transition-colors rounded-xl py-2 px-4 text-white text-sm font-medium">
                                    <i class="fa fa-phone text-amber-200"></i> {{ $agent->phone }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
            </div>
        </div>
    </div>

    {{-- Itinerary Section --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-center gap-4 mb-10">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Your Custom Itinerary</h2>
            <div class="h-1 flex-1 bg-gradient-to-r from-amber-200 to-transparent rounded-full"></div>
        </div>

        <div class="flex flex-col gap-8">
            @foreach($quotation->quotationDays->sortBy('day_number') as $day)
            @php
                $c = $day->day_number;
                $daysFix = $c - 1;
                $currentDate = $quotation->travel_date ? date('M d, Y', strtotime($quotation->travel_date . ' + ' . $daysFix . ' days')) : '';
                $included = !empty($day->included) ? (@unserialize($day->included) ?: []) : [];
                $excluded = !empty($day->excluded) ? (@unserialize($day->excluded) ?: []) : [];
                $images = !empty($day->images) ? (@unserialize($day->images) ?: []) : [];
            @endphp
            
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col md:flex-row hover:shadow-md transition-shadow group">
                
                {{-- Day Marker (Left) --}}
                <div class="bg-slate-50 w-full md:w-56 shrink-0 flex flex-col items-center justify-center p-8 border-b md:border-b-0 md:border-r border-slate-100 group-hover:bg-amber-50/30 transition-colors relative">
                    <div class="absolute top-0 right-0 w-full h-1 bg-amber-400"></div>
                    <i class="fa fa-calendar-check-o text-4xl text-amber-300 mb-4 opacity-50"></i>
                    <span class="text-amber-600 font-bold text-sm uppercase tracking-[0.2em] mb-1">Day</span>
                    <span class="text-6xl font-black text-slate-800 leading-none mb-3">{{ $c }}</span>
                    @if($currentDate)
                        <span class="inline-block bg-white border border-slate-200 text-slate-500 font-bold text-[11px] uppercase tracking-wider py-1 px-3 rounded-full shadow-sm">{{ $currentDate }}</span>
                    @endif
                </div>

                {{-- Content (Right) --}}
                <div class="p-8 md:p-10 flex-1 flex flex-col">
                    
                    {{-- HTML Description --}}
                    <div class="prose prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900 prose-a:text-amber-600 mb-8 html-content text-slate-600 leading-relaxed">
                        {!! html_entity_decode($day->contents) !!}
                    </div>

                    {{-- Included / Excluded Panels --}}
                    @if(count($included) > 0 || count($excluded) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-auto">
                        @if(count($included) > 0)
                        <div class="bg-emerald-50/50 border border-emerald-100 rounded-2xl p-5">
                            <h4 class="font-bold text-emerald-800 flex items-center gap-2 mb-4 text-sm uppercase tracking-wider">
                                <i class="fa fa-check-circle text-emerald-500"></i> Included
                            </h4>
                            <ul class="flex flex-col gap-2.5">
                                @foreach($included as $incName)
                                <li class="text-sm text-emerald-700 flex items-start gap-2">
                                    <i class="fa fa-check text-emerald-500 mt-1 shrink-0 text-xs"></i> 
                                    <span class="font-medium leading-tight">{{ $incName }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(count($excluded) > 0)
                        <div class="bg-rose-50/50 border border-rose-100 rounded-2xl p-5">
                            <h4 class="font-bold text-rose-800 flex items-center gap-2 mb-4 text-sm uppercase tracking-wider">
                                <i class="fa fa-times-circle text-rose-500"></i> Excluded
                            </h4>
                            <ul class="flex flex-col gap-2.5">
                                @foreach($excluded as $excName)
                                <li class="text-sm text-rose-700 flex items-start gap-2">
                                    <i class="fa fa-times text-rose-500 mt-1 shrink-0 text-xs"></i> 
                                    <span class="font-medium leading-tight">{{ $excName }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    {{-- Images Gallery --}}
                    @if(count($images) > 0)
                    <div class="mt-8">
                        <div class="flex flex-wrap gap-4">
                            @foreach($images as $img)
                            <a href="{{ $img }}" target="_blank" class="group/img block overflow-hidden rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                                <img src="{{ $img }}" alt="Gallery Image" class="w-32 h-32 object-cover group-hover/img:scale-110 transition-transform duration-500">
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    /* Ensure user HTML content from the editor renders nicely */
    .html-content p { margin-bottom: 1rem; }
    .html-content p:last-child { margin-bottom: 0; }
    .html-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
    .html-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }
    .html-content strong, .html-content b { color: #0f172a; }
</style>
@endsection
