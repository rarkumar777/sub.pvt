@extends('frontend.layout')
@section('title', 'Tours | PVT')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">

    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-900 mb-3">All Tours</h1>
        <p class="text-gray-500 max-w-2xl mx-auto">Browse our complete collection of tours and find your perfect adventure.</p>
    </div>

    <!-- Search / Filter Bar -->
    <div class="search-bar bg-white rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 mb-12">
        <form method="GET" action="{{ route('frontend.tours', ['lang' => $lang]) }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-3">
                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Destination</label>
                    <select name="country" class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                        <option value="">All Countries</option>
                        @if(isset($countries))
                            @foreach($countries as $cid => $cname)
                                <option value="{{ $cid }}" {{ request('country') == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Category</label>
                    <select name="category" class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                        <option value="">All Categories</option>
                        @if(isset($tourCategories))
                            @foreach($tourCategories as $cat)
                                <option value="{{ $cat->lang_id }}" {{ request('category') == $cat->lang_id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Tour Type</label>
                    <select name="type" class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                        <option value="">All Types</option>
                        @if(isset($tourTypes))
                            @foreach($tourTypes as $type)
                                <option value="{{ $type->lang_id }}" {{ request('type') == $type->lang_id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Min Price</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Max Price</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5 ml-1">Duration</label>
                    <input type="number" name="days" value="{{ request('days') }}" placeholder="Days" class="w-full bg-gray-50/50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>
                <div class="lg:col-span-1 flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold h-[48px] rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-orange-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all text-base uppercase tracking-wide">
                        <i data-lucide="search" class="w-5 h-5"></i>
                        <span>Search</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Results -->
    @if(isset($tours) && count($tours) > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($tours as $tour)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300 hover:-translate-y-1 flex flex-col h-full">
            <div class="relative h-56 overflow-hidden">
                <a href="{{ url($lang . '/tours/' . strtolower($countries[$tour->start_country] ?? 'jordan') . '/' . $tour->url) }}/">
                    <img src="{{ $tour->image }}" alt="{{ $tour->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.style.background='linear-gradient(135deg,#667eea,#764ba2)'; this.style.minHeight='224px'; this.src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';">
                </a>
                @if($tour->min_price > 0)
                <div class="absolute top-4 left-0 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-semibold px-4 py-1 rounded-r-full shadow-lg">
                    @php $lSym = ['USD'=>'$','JOD'=>'JD','EUR'=>'€'][$activeCurrency] ?? '$'; $lRate = ['USD'=>1,'JOD'=>0.709,'EUR'=>0.92][$activeCurrency] ?? 1; @endphp
                    From {{ $lSym }}{{ number_format(round($tour->min_price * $lRate)) }}
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
                        <i data-lucide="clock" class="w-4 h-4 text-blue-500"></i>
                        <span>{{ $tour->days }} Days</span>
                    </div>
                    <a href="{{ url($lang . '/tours/' . strtolower($countries[$tour->start_country] ?? 'jordan') . '/' . $tour->url) }}/" class="text-blue-600 font-bold text-sm hover:text-orange-600 transition flex items-center gap-1">
                        <span>Details</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-20">
        <i data-lucide="search-x" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-500">No tours found</h3>
        <p class="text-gray-400 mt-2">Try adjusting your search filters.</p>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
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
    .search-bar select {
        height: 46px !important;
        min-height: 46px !important;
        border-radius: 12px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        background-color: #f9fafb !important;
        border: 1px solid #e5e7eb !important;
        padding: 0 12px !important;
        appearance: auto !important;
        -webkit-appearance: menulist !important;
        cursor: pointer;
    }
</style>
@endpush
