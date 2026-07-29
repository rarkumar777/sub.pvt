@extends('admin.layouts.app')
@section('title', 'Admin | SEO Settings')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    @include('admin.settings._nav')
    
    {{-- Header --}}
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
            <span class="tw-w-12 tw-h-12 tw-bg-emerald-50 tw-text-emerald-600 tw-rounded-2xl tw-inline-flex tw-items-center tw-justify-center tw-mr-2">
                <i class="fa fa-search"></i>
            </span>
            SEO <span class="tw-text-indigo-600">Settings</span>
        </h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure meta tags and SEO for each language</p>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-lg"></i>
        <span class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.seo.update') }}">
        @csrf

        @foreach($langs as $L)
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-8 tw-py-5 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                <h3 class="tw-text-sm tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                    <i class="fa fa-language tw-text-indigo-500"></i> SEO — {{ strtoupper($L) }}
                </h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Title</label>
                    <div class="md:tw-col-span-3">
                        <input type="text" name="title{{ $L }}" value="{{ $seoData[$L]['title'] ?? '' }}" placeholder="Page title">
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Website Name</label>
                    <div class="md:tw-col-span-3">
                        <input type="text" name="websitename{{ $L }}" value="{{ $seoData[$L]['name'] ?? '' }}" placeholder="Website name">
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Meta Keywords</label>
                    <div class="md:tw-col-span-3">
                        <input type="text" name="websitekeywords{{ $L }}" value="{{ $seoData[$L]['keywords'] ?? '' }}" placeholder="keyword1, keyword2, ...">
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Meta Description</label>
                    <div class="md:tw-col-span-3">
                        <input type="text" name="websitemetadescription{{ $L }}" value="{{ $seoData[$L]['description'] ?? '' }}" placeholder="Site description">
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-4 tw-items-start tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-pt-2">Extra Head Tags</label>
                    <div class="md:tw-col-span-3">
                        <textarea name="extra_head_tags{{ $L }}" rows="10" class="tw-w-full tw-font-mono tw-text-sm tw-p-3 tw-min-h-[200px]" placeholder="<meta ...>">{!! $seoData[$L]['other_head_tags'] ?? '' !!}</textarea>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <button type="submit" class="btn indigo tw-w-full !tw-py-4 !tw-text-base tw-shadow-lg tw-shadow-indigo-100">
            <i class="fa fa-check-circle"></i> Save SEO Settings
        </button>
    </form>
</div>
@endsection
