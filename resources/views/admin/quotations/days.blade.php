@extends('admin.layouts.app')
@section('title', 'Admin | Quotation Days')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Navigation Tabs (Sub-pages) --}}
    @include('admin.quotations._nav')
    {{-- Header Section --}}
    <div class="tw-flex tw-flex-col md:tw-flex-row tw-items-start md:tw-items-center tw-justify-between tw-gap-4">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-2">
                <i class="fa fa-pie-chart"></i> Quotations
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="tw-text-indigo-500 hover:tw-text-indigo-600 tw-transition-all">#{{ $quotation->id }}</a>
                <i class="fa fa-angle-right"></i>
                <span class="tw-text-slate-500">Day by Day</span>
            </div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">{{ $quotation->customer_name }}</h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Detailed day-by-day itinerary view</p>
        </div>
        <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="btn red">
            <i class="fa fa-arrow-left"></i> Back to Quotation
        </a>
    </div>

    @foreach($quotation->quotationDays->sortBy('day_number') as $day)
    <div class="box !tw-p-0 !tw-overflow-hidden">
        {{-- Day Header --}}
        <div class="tw-px-8 tw-py-5 tw-bg-slate-900 tw-text-white tw-flex tw-justify-between tw-items-center shadow-lg">
            <div class="tw-flex tw-items-center tw-gap-4">
                <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-lg tw-font-black">{{ sprintf('%02d', $day->day_number) }}</div>
                <div>
                    <span class="tw-text-indigo-400 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest">Itinerary Overview</span>
                    <h4 class="tw-text-sm tw-font-extrabold !tw-m-0">Day {{ $day->day_number }}</h4>
                </div>
            </div>
            <div class="tw-flex tw-items-center tw-gap-2">
                <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase">Total Cost:</span>
                <span class="tw-px-3 tw-py-1.5 tw-bg-white/10 tw-rounded-xl tw-text-sm tw-font-black shadow-inner">
                    {{ number_format($day->total_cost, 2) }} <small class="tw-opacity-50">JOD</small>
                </span>
            </div>
        </div>
        
        {{-- Day Content --}}
        <div class="tw-p-10">
            @if($day->contents)
            <div class="tw-prose tw-prose-slate tw-max-w-none prose-p:tw-text-slate-600 prose-headings:tw-text-slate-900 prose-strong:tw-text-slate-800">
                {!! $day->contents !!}
            </div>
            @else
            <div class="tw-flex tw-flex-col tw-items-center tw-py-16 tw-bg-slate-50/50 tw-rounded-[2.5rem] tw-border-2 tw-border-dashed tw-border-slate-100">
                <div class="tw-w-16 tw-h-16 tw-rounded-full tw-bg-white tw-shadow-sm tw-flex tw-items-center tw-justify-center tw-mb-4 tw-text-slate-200">
                    <i class="fa fa-sticky-note-o tw-text-2xl"></i>
                </div>
                <p class="tw-text-slate-400 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest">No detailed description provided for this day</p>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
