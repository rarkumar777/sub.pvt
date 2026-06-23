@extends('admin.layouts.app')
@section('title', 'Admin | Database Backup')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
            Database <span class="tw-text-indigo-600">Backup</span>
        </h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Create and download database backups</p>
    </div>

    <div class="box !tw-p-8">
        <a href="#" class="btn indigo"><i class="fa fa-download"></i> Create Backup</a>
    </div>

    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-px-8 tw-py-5 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
            <h3 class="tw-text-sm tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                <i class="fa fa-history tw-text-blue-500"></i> Previous Backups
            </h3>
        </div>
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/30 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">File Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Date</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Size</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="tw-py-16 tw-text-center">
                            <p class="tw-text-slate-400 tw-text-sm tw-font-bold">No backups found</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
