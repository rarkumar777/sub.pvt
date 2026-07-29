@extends('admin.layouts.app')
@section('title', 'Admin | Content Blocks')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
            Content <span class="tw-text-indigo-600">Blocks</span>
        </h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Manage reusable content blocks for your website</p>
    </div>

    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-w-16">#</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Block Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Status</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    <tr>
                        <td colspan="4" class="tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-rounded-full tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-200">
                                    <i class="fa fa-th-large tw-text-3xl"></i>
                                </div>
                                <p class="tw-text-slate-400 tw-text-sm tw-font-bold">No blocks configured</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
