@extends('admin.layouts.app')
@section('title', 'Admin | Tours Management')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">
    
    {{-- Header Area --}}
    <div class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-500 tw-transition-colors tw-no-underline">Dashboard</a>
                <span class="tw-opacity-50">/</span>
                <span class="tw-text-slate-600">Tours Management</span>
            </div>
            <h1 class="tw-text-3xl tw-font-black tw-text-slate-900 tw-flex tw-items-center tw-gap-3">
                <div class="tw-w-10 tw-h-10 tw-bg-orange-500 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-orange-200">
                    <i class="fa fa-plane tw-text-white tw-text-base"></i>
                </div>
                Tours Inventory
            </h1>
            <p class="subtitle">Manage and organize your tour packages, pricing, and availability.</p>
        </div>
        <div>
            <a href="{{ route('admin.tours.create') }}" class="btn orange tw-shadow-premium">
                <i class="fa fa-plus-circle"></i> Add New Tour
            </a>
        </div>
    </div>

    {{-- Advanced Filters Bar --}}
    <div class="box !tw-p-5 !tw-mb-0 tw-flex tw-flex-wrap tw-items-center tw-gap-5">
        <div class="tw-flex tw-items-center tw-gap-2 tw-text-slate-400">
            <i class="fa fa-filter tw-text-sm"></i>
            <span class="tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest">Filters</span>
        </div>

        <!-- Category Filter -->
        <div class="tw-relative tw-group">
            <button class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2.5 tw-bg-transparent tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-text-slate-700 hover:tw-border-orange-300 tw-transition-colors focus:tw-outline-none focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-100 tw-font-semibold">
                <i class="fa fa-folder-open tw-text-slate-400 tw-text-sm group-hover:tw-text-orange-500 tw-transition-colors"></i>
                <span class="tw-font-bold">{{ $activeCategory ?? 'All Categories' }}</span>
                <i class="fa fa-caret-down tw-text-slate-400"></i>
            </button>
            <div class="tw-absolute tw-top-full tw-left-0 tw-mt-2 tw-w-64 tw-bg-white tw-rounded-2xl tw-shadow-premium tw-border tw-border-slate-100 tw-opacity-0 tw-invisible group-hover:tw-opacity-100 group-hover:tw-visible tw-transition-all tw-z-50 tw-py-2" style="max-height: 320px; overflow-y: auto;">
                <a href="{{ route('admin.tours.index', ['type' => request('type')]) }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2.5 tw-text-sm tw-font-semibold tw-text-slate-700 hover:tw-bg-orange-50 hover:tw-text-orange-600 tw-transition-colors tw-no-underline tw-rounded-lg tw-mx-1.5">
                    <i class="fa fa-th-large tw-text-xs tw-text-slate-300"></i> All Categories
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('admin.tours.index', ['category' => $cat->cat_id ?? $cat->id, 'type' => request('type')]) }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2.5 tw-text-sm tw-font-semibold tw-text-slate-700 hover:tw-bg-orange-50 hover:tw-text-orange-600 tw-transition-colors tw-no-underline tw-rounded-lg tw-mx-1.5">
                        <i class="fa fa-tag tw-text-xs tw-text-slate-300"></i> {{ $cat->name ?? $cat->title ?? '' }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Type Filter -->
        <div class="tw-relative tw-group">
            <button class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2.5 tw-bg-transparent tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-text-slate-700 hover:tw-border-orange-300 tw-transition-colors focus:tw-outline-none focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-100 tw-font-semibold">
                <i class="fa fa-th-large tw-text-slate-400 tw-text-sm group-hover:tw-text-orange-500 tw-transition-colors"></i>
                <span class="tw-font-bold">{{ $activeType ?? 'All Types' }}</span>
                <i class="fa fa-caret-down tw-text-slate-400"></i>
            </button>
            <div class="tw-absolute tw-top-full tw-left-0 tw-mt-2 tw-w-64 tw-bg-white tw-rounded-2xl tw-shadow-premium tw-border tw-border-slate-100 tw-opacity-0 tw-invisible group-hover:tw-opacity-100 group-hover:tw-visible tw-transition-all tw-z-50 tw-py-2" style="max-height: 320px; overflow-y: auto;">
                <a href="{{ route('admin.tours.index', ['category' => request('category')]) }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2.5 tw-text-sm tw-font-semibold tw-text-slate-700 hover:tw-bg-orange-50 hover:tw-text-orange-600 tw-transition-colors tw-no-underline tw-rounded-lg tw-mx-1.5">
                    <i class="fa fa-th-large tw-text-xs tw-text-slate-300"></i> All Types
                </a>
                @foreach($types as $type)
                    <a href="{{ route('admin.tours.index', ['category' => request('category'), 'type' => $type->type_id ?? $type->id]) }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2.5 tw-text-sm tw-font-semibold tw-text-slate-700 hover:tw-bg-orange-50 hover:tw-text-orange-600 tw-transition-colors tw-no-underline tw-rounded-lg tw-mx-1.5">
                        <i class="fa fa-tag tw-text-xs tw-text-slate-300"></i> {{ $type->name ?? $type->title ?? '' }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="tw-ml-auto tw-flex tw-items-center tw-gap-2 tw-bg-orange-50 tw-text-orange-600 tw-px-4 tw-py-2 tw-rounded-xl">
            <i class="fa fa-database tw-text-xs"></i>
            <span class="tw-text-sm tw-font-black">{{ $tours->total() }}</span>
            <span class="tw-text-xs tw-font-bold tw-text-orange-400">Tours</span>
        </div>
    </div>

    {{-- Main Data Table --}}
    <div class="box !tw-p-0 tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/80 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Tour Information</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Status</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Pricing</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($tours as $tour)
                        @php
                            $content = $tour->contents->where('lang', 'en')->first();
                            $title = $content->title ?? 'Untitled Tour';
                            $startCountryName = strtolower(optional($tour->startCountryRelation)->name ?? 'tour');
                            $minPrice = ($tour->min_price && $tour->min_price != '0.00') ? $tour->min_price : null;
                            $maxPrice = ($tour->max_price && $tour->max_price != '0.00') ? $tour->max_price : null;
                        @endphp
                        <tr class="hover:tw-bg-orange-50/30 tw-transition-colors">
                            <!-- Tour Info -->
                            <td class="tw-py-4 tw-px-6">
                                <div class="tw-flex tw-items-center tw-gap-4">
                                    <div class="tw-w-14 tw-h-14 tw-flex-shrink-0 tw-rounded-xl tw-overflow-hidden tw-border tw-border-slate-100 tw-shadow-sm">
                                        @if($tour->image)
                                            <img src="{{ $tour->image }}" class="tw-w-full tw-h-full tw-object-cover">
                                        @else
                                            <div class="tw-w-full tw-h-full tw-bg-gradient-to-br tw-from-slate-50 tw-to-slate-100 tw-flex tw-items-center tw-justify-center tw-text-slate-300">
                                                <i class="fa fa-image tw-text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="tw-font-bold tw-text-slate-900 tw-text-sm">{{ $title }}</div>
                                        <div class="tw-flex tw-items-center tw-flex-wrap tw-gap-1.5 tw-mt-2">
                                            <span class="tw-inline-flex tw-items-center tw-px-2 tw-py-0.5 tw-rounded-md tw-bg-orange-50 tw-text-orange-600 tw-text-[10px] tw-font-black tw-uppercase tw-tracking-widest">
                                                {{ optional($tour->categoryRelation)->name ?? 'General' }}
                                            </span>
                                            <span class="tw-text-slate-200 tw-text-xs">•</span>
                                            <span class="tw-text-[11px] tw-font-bold tw-text-slate-500 tw-flex tw-items-center tw-gap-1">
                                                <i class="fa fa-clock-o tw-text-slate-300"></i> {{ $tour->days }}D / {{ $tour->nights }}N
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="tw-py-4 tw-px-6">
                                @if($tour->status == 1)
                                    <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-emerald-50 tw-text-emerald-600 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-border tw-border-emerald-100">
                                        <span class="tw-w-1.5 tw-h-1.5 tw-bg-emerald-500 tw-rounded-full"></span> Active
                                    </span>
                                @else
                                    <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-slate-50 tw-text-slate-500 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-border tw-border-slate-100">
                                        <span class="tw-w-1.5 tw-h-1.5 tw-bg-slate-300 tw-rounded-full"></span> Draft
                                    </span>
                                @endif
                            </td>

                            <!-- Pricing -->
                            <td class="tw-py-4 tw-px-6">
                                @if($minPrice || $maxPrice)
                                    <div class="tw-font-bold tw-text-slate-800 tw-text-sm">
                                        ${{ $minPrice ?? '0' }} <span class="tw-text-slate-300 tw-mx-1">—</span> ${{ $maxPrice ?? '0' }}
                                    </div>
                                @else
                                    <span class="tw-text-slate-400 tw-text-sm tw-italic tw-font-medium">Pending</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="tw-py-4 tw-px-6">
                                <div class="tw-flex tw-items-center tw-justify-end tw-gap-2">
                                    <a href="/en/tours/{{ $startCountryName }}/{{ $content->url ?? '' }}/" target="_blank" 
                                       class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-emerald-50 tw-text-emerald-600 hover:tw-bg-emerald-600 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-emerald-100 hover:tw-border-emerald-600" 
                                       title="View Public Page">
                                        <i class="fa fa-eye tw-text-xs"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.tours.create', ['copy_tour' => $tour->id]) }}" 
                                       class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-amber-50 tw-text-amber-600 hover:tw-bg-amber-500 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-amber-100 hover:tw-border-amber-500" 
                                       title="Clone Tour">
                                        <i class="fa fa-copy tw-text-xs"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.tours.edit', $tour->id) }}" 
                                       class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-orange-100 hover:tw-border-orange-600" 
                                       title="Edit Tour">
                                        <i class="fa fa-pencil tw-text-xs"></i>
                                    </a>
                                    
                                    <form method="POST" action="{{ route('admin.tours.destroy', $tour->id) }}" class="tw-inline tw-m-0" onsubmit="return confirm('Archive tour ({{ $title }})?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-border tw-border-rose-100 hover:tw-border-rose-600 tw-cursor-pointer" title="Delete Tour">
                                            <i class="fa fa-trash tw-text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="tw-py-20 tw-text-center">
                                <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                    <div class="tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-300">
                                        <i class="fa fa-plane tw-text-3xl"></i>
                                    </div>
                                    <div>
                                        <p class="tw-text-slate-600 tw-font-bold tw-text-base">No tours found</p>
                                        <p class="tw-text-slate-400 tw-text-xs tw-mt-1">Try adjusting your filters or create a new tour package.</p>
                                    </div>
                                    <a href="{{ route('admin.tours.index') }}" class="btn red !tw-text-xs !tw-py-2 !tw-px-4 tw-mt-2">
                                        <i class="fa fa-refresh"></i> Clear Filters
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination Footer --}}
        @if($tours->hasPages())
        <div class="tw-px-6 tw-py-4 tw-border-t tw-border-slate-100 tw-bg-slate-50/30">
            {{ $tours->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* Pagination Overrides */
    .pagination { display: flex; list-style: none; gap: 6px; justify-content: flex-end; align-items: center; margin: 0; padding: 0; }
    .pagination li { display: inline-block; }
    .pagination li span, .pagination li a {
        display: flex; align-items: center; justify-content: center;
        min-width: 36px; height: 36px; border-radius: 10px; font-size: 13px; font-weight: 700;
        text-decoration: none; border: 1px solid #f1f5f9; background: #fff; color: #475569;
        padding: 0 10px; transition: all 0.2s;
    }
    .pagination li.active span {
        background: var(--brand-primary); color: #fff; border-color: var(--brand-primary);
        box-shadow: 0 4px 10px rgba(249, 115, 22, 0.25);
    }
    .pagination li a:hover { background: #f8fafc; border-color: #e2e8f0; color: var(--brand-primary); }
    .pagination li.disabled span { opacity: 0.4; cursor: not-allowed; }
</style>
@endsection
