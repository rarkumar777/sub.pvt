@extends('admin.layouts.app')
@section('title', 'Admin | Global Pricing')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumb --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
        <a href="{{ route('admin.tours.index') }}" class="tw-text-indigo-600 tw-font-semibold tw-no-underline hover:tw-text-indigo-800 tw-transition-colors">
            <i class="fa fa-plane tw-mr-1"></i> Tours
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <span class="tw-text-slate-500 tw-font-semibold">Global Pricing</span>
    </div>

    {{-- Header --}}
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
            Global <span class="tw-text-indigo-600">Pricing</span>
        </h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Overview of all tour pricing — click to edit individual tours</p>
    </div>

    {{-- Pricing Table --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-w-16">#</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Tour Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Min Price</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Max Price</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @foreach($tours as $tour)
                    @php $content = $tour->contents->where('lang', 'en')->first(); @endphp
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-400 tw-font-medium">{{ $tour->id }}</td>
                        <td class="tw-py-4 tw-px-6">
                            <span class="tw-font-bold tw-text-slate-900 tw-text-sm">{{ $content->title ?? 'Tour #'.$tour->id }}</span>
                        </td>
                        <td class="tw-py-4 tw-px-6">
                            <span class="tw-text-sm tw-font-bold {{ $tour->min_price ? 'tw-text-emerald-600' : 'tw-text-slate-300' }}">
                                {{ $tour->min_price ? number_format($tour->min_price, 2) : '—' }}
                            </span>
                        </td>
                        <td class="tw-py-4 tw-px-6">
                            <span class="tw-text-sm tw-font-bold {{ $tour->max_price ? 'tw-text-indigo-600' : 'tw-text-slate-300' }}">
                                {{ $tour->max_price ? number_format($tour->max_price, 2) : '—' }}
                            </span>
                        </td>
                        <td class="tw-py-4 tw-px-6 tw-text-right">
                            <a href="{{ route('admin.tours.pricing', $tour->id) }}" class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-3 tw-py-1.5 tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-600 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wider hover:tw-bg-indigo-600 hover:tw-text-white tw-transition-all tw-no-underline">
                                <i class="fa fa-edit"></i> Edit Pricing
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
