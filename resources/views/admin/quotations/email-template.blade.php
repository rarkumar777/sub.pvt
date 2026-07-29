@extends('admin.layouts.app')
@section('title', 'Admin | Email Preview - ' . $quotation->customer_name)

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Navigation Tabs (Sub-pages) --}}
    @include('admin.quotations._nav')
    {{-- Breadcrumbs --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">
        <i class="fa fa-pie-chart"></i> Quotations
        <i class="fa fa-angle-right"></i>
        <a href="{{ route('admin.quotations.index') }}" class="tw-text-indigo-500 hover:tw-text-indigo-600 tw-transition-all">Archive</a>
        <i class="fa fa-angle-right"></i>
        <span class="tw-text-slate-500">Email Draft #{{ $quotation->id }}</span>
    </div>

    {{-- Header Section --}}
    <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-items-start md:tw-items-center tw-gap-4">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Email <span class="tw-text-indigo-600">Draft</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Prepare and dispatch the quotation for {{ $quotation->customer_name }}</p>
        </div>
        <a href="{{ route('admin.quotations.index') }}" class="btn red">
            <i class="fa fa-arrow-left"></i> Return to List
        </a>
    </div>

    <div class="box !tw-p-10 shadow-2xl shadow-slate-100">
        <div class="tw-flex tw-flex-col tw-gap-10">
            {{-- Subject Line --}}
            <div class="tw-flex tw-flex-col tw-gap-3">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-flex tw-items-center tw-gap-2">
                    <i class="fa fa-tag tw-text-indigo-500"></i> Subject Line
                </label>
                <input type="text" class="!tw-py-5 !tw-px-8 !tw-bg-slate-50 !tw-border-transparent focus:!tw-bg-white focus:!tw-border-indigo-500 !tw-rounded-2xl tw-transition-all tw-font-bold tw-text-slate-700 tw-shadow-inner" 
                    value="Quotation #{{ $quotation->ref_number ?: $quotation->id }} - {{ $quotation->customer_name }}">
            </div>

            {{-- Body Content --}}
            <div class="tw-flex tw-flex-col tw-gap-3">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-flex tw-items-center tw-gap-2">
                    <i class="fa fa-align-left tw-text-indigo-500"></i> Correspondence Body
                </label>
                <textarea class="!tw-py-8 !tw-px-10 !tw-bg-slate-50 !tw-border-transparent focus:!tw-bg-white focus:!tw-border-indigo-500 !tw-rounded-[3rem] tw-transition-all tw-font-medium tw-text-slate-600 tw-min-h-[350px] tw-shadow-inner tw-leading-relaxed" rows="12">Dear {{ $quotation->customer_name }},

Please find attached your detailed tour quotation for the upcoming trip. We have carefully curated this itinerary to ensure a premium experience across Jordan's most iconic destinations.

You can view the interactive itinerary and live updates at: {{ url('/' . ($quotation->lang ?: 'en') . '/tours/quotation/' . $quotation->id) }}

We look forward to welcoming you soon and providing a world-class travel experience.

Best regards,
PVT Jordan Concierge Team</textarea>
            </div>

            {{-- Action Buttons --}}
            <div class="tw-mt-6 tw-pt-10 tw-border-t tw-border-slate-100 tw-flex tw-flex-wrap tw-gap-5">
                <button type="button" class="btn indigo !tw-px-10 !tw-py-5 !tw-rounded-2xl shadow-xl shadow-indigo-100 active:tw-scale-95 tw-transition-all">
                    <i class="fa fa-paper-plane tw-mr-2"></i> Deliver to Client
                </button>
                <button type="button" class="btn ivory !tw-px-10 !tw-py-5 !tw-rounded-2xl shadow-md active:tw-scale-95 tw-transition-all">
                    <i class="fa fa-file-pdf-o tw-mr-2"></i> Generate PDF
                </button>
                <a href="{{ url('/' . ($quotation->lang ?: 'en') . '/tours/quotation/' . $quotation->id) }}" target="_blank" class="btn blue !tw-px-10 !tw-py-5 !tw-rounded-2xl shadow-md active:tw-scale-95 tw-transition-all">
                    <i class="fa fa-external-link tw-mr-2"></i> Interactive Link
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
