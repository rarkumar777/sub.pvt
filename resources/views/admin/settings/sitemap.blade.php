@extends('admin.layouts.app')
@section('title', 'Admin | Sitemap Generator')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    @include('admin.settings._nav')

    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
            <span class="tw-w-12 tw-h-12 tw-bg-indigo-50 tw-text-indigo-600 tw-rounded-2xl tw-inline-flex tw-items-center tw-justify-center tw-mr-2">
                <i class="fa fa-sitemap"></i>
            </span>
            Sitemap <span class="tw-text-indigo-600">Generator</span>
        </h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Generate XML sitemap for search engines</p>
    </div>

    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-px-8 tw-py-8 tw-flex tw-flex-col tw-items-center tw-text-center tw-gap-6 tw-bg-slate-50">
            <div class="tw-w-20 tw-h-20 tw-bg-white tw-rounded-full tw-shadow-sm border border-slate-100 tw-flex tw-items-center tw-justify-center">
                <i class="fa fa-sitemap tw-text-4xl tw-text-emerald-500"></i>
            </div>
            
            <div class="tw-max-w-md">
                <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 tw-mb-2">Sitemap Status</h3>
                <p class="tw-text-slate-500 tw-text-sm">Click the button below to regenerate the XML sitemap. Ensure this is done whenever new content is added to keep search engines up-to-date.</p>
            </div>

            <a href="#" class="btn indigo tw-shadow-lg tw-shadow-indigo-100 tw-px-8 tw-py-3">
                <i class="fa fa-refresh"></i> Regenerate XML Sitemap
            </a>
            
            <p class="tw-text-xs tw-font-bold tw-text-slate-400 tw-px-4 tw-py-2 tw-bg-slate-100 tw-rounded-full">
                <i class="fa fa-clock-o"></i> Last generated: {{ date('Y-m-d H:i:s') }}
            </p>
        </div>
    </div>
</div>
@endsection
