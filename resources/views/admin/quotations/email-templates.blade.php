@extends('admin.layouts.app')
@section('title', 'Admin | E-mail Templates')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumb for context --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">
        <i class="fa fa-pie-chart"></i> Quotations
        <i class="fa fa-angle-right"></i>
        <span class="tw-text-slate-500">Email Templates</span>
    </div>

    {{-- Header Section --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">E-mail <span class="tw-text-orange-600">Templates</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure default messages for multi-language quotation delivery</p>
        </div>
        <button type="submit" form="email-template-form" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-full tw-bg-orange-600 tw-px-8 tw-py-2.5 tw-text-sm tw-font-medium tw-text-white tw-shadow-sm hover:tw-bg-orange-700 tw-transition-colors tw-flex-shrink-0">
            <i class="fa fa-check"></i> Save Templates
        </button>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-rounded-2xl tw-p-4 tw-flex tw-items-center tw-gap-3 tw-mb-8">
        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-emerald-500 tw-text-white tw-flex tw-items-center tw-justify-center tw-flex-shrink-0 tw-text-xs">
            <i class="fa fa-check"></i>
        </div>
        <p class="tw-text-emerald-800 tw-font-medium tw-text-sm tw-m-0">{{ session('success') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.quotation-email-templates') }}" id="email-template-form">
        @csrf
        
        <div class="tw-flex tw-flex-col tw-gap-6">
            @foreach($langs as $lang)
            <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden hover:tw-shadow-md tw-transition-all tw-duration-300 tw-flex tw-flex-col">
                <div class="tw-relative tw-px-6 tw-py-4 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                    <div class="tw-absolute tw-top-0 tw-left-0 tw-w-full tw-h-[3px] tw-bg-orange-500"></div>
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-orange-100 tw-text-orange-700 tw-flex tw-items-center tw-justify-center tw-text-sm tw-font-black tw-uppercase">
                            {{ $lang }}
                        </div>
                        <div>
                            <h3 class="tw-text-base tw-font-bold tw-text-slate-900 tw-m-0">Message Template ({{ strtoupper($lang) }})</h3>
                            <span class="tw-text-xs tw-font-medium tw-text-slate-400">Configure the email body sent to clients</span>
                        </div>
                    </div>
                </div>
                <div class="tw-p-6">
                    <textarea name="template_{{ $lang }}" 
                        class="tw-w-full tw-min-h-[200px] tw-p-4 tw-bg-white tw-border tw-border-slate-200 tw-rounded-2xl tw-text-sm tw-font-code tw-text-slate-700 tw-leading-relaxed focus:tw-border-orange-500 focus:tw-ring-4 focus:tw-ring-orange-500/10 tw-transition-all tw-outline-none tw-resize-y"
                        placeholder="Enter message template for {{ $lang }} variants..." spellcheck="false">{{ $templates[$lang] ?? '' }}</textarea>
                </div>
            </div>
            @endforeach
        </div>

        <div class="tw-mt-12 tw-flex tw-justify-center tw-pb-24">
            <button type="submit" class="btn orange !tw-px-12 !tw-py-5 !tw-text-lg shadow-xl shadow-orange-100">
                <i class="fa fa-save"></i> Save All Language Templates
            </button>
        </div>
    </form>
</div>
@endsection
