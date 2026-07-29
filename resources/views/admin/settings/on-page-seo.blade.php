@extends('admin.layouts.app')
@section('title', 'Admin | On-Page SEO')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    @include('admin.settings._nav')
    
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">On-Page <span class="tw-text-indigo-600">SEO</span></h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure per-page SEO settings, meta tags, and Open Graph data</p>
    </div>

    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Page</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Title</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @foreach(['Home', 'Tours', 'Contact'] as $page)
                    <tr class="hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-5 tw-px-6 tw-text-sm tw-font-bold tw-text-slate-900">{{ $page }}</td>
                        <td class="tw-py-5 tw-px-6 tw-text-sm tw-text-slate-400">—</td>
                        <td class="tw-py-5 tw-px-6 tw-text-right">
                            <a href="#" class="tw-w-9 tw-h-9 tw-inline-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-600 hover:tw-bg-indigo-600 hover:tw-text-white tw-transition-all tw-no-underline"><i class="fa fa-edit tw-text-xs"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
