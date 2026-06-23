@extends('admin.layouts.app')
@section('title', 'Admin | Links')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Links <span class="tw-text-indigo-600">Management</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Manage external and internal links</p>
        </div>
        <a href="#add_link" class="btn indigo"><i class="fa fa-plus"></i> Add Link</a>
    </div>

    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-w-16">#</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Title</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">URL</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="4" class="tw-py-16 tw-text-center tw-text-slate-400 tw-text-sm tw-font-bold">No links yet</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal" id="add_link">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[450px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0"><i class="fa fa-plus-circle tw-text-emerald-400"></i> Add Link</h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <div class="tw-p-8">
            <div class="tw-space-y-5">
                <div><label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Title</label><input type="text" name="title" placeholder="Link title"></div>
                <div><label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">URL</label><input type="text" name="url" placeholder="https://..."></div>
            </div>
            <button class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save</button>
        </div>
    </div>
</div>
@endsection
