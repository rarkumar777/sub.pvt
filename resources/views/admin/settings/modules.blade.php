@extends('admin.layouts.app')
@section('title', 'Admin | Modules')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">

    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
            <span class="tw-w-12 tw-h-12 tw-bg-indigo-50 tw-text-indigo-600 tw-rounded-2xl tw-inline-flex tw-items-center tw-justify-center tw-mr-2">
                <i class="fa fa-random"></i>
            </span>
            Manage <span class="tw-text-indigo-600">Modules</span>
        </h1>
    </div>

    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-bold tw-text-slate-700">Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-bold tw-text-slate-700">Status</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-bold tw-text-slate-700">Action</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @foreach($modules as $mod)
                    <tr class="hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-800">{{ $mod['name'] }}</td>
                        <td class="tw-py-4 tw-px-6">
                            @if($mod['active'])
                                <i class="fa fa-check tw-text-emerald-500 tw-font-bold tw-text-lg" title="Active"></i>
                            @else
                                <i class="fa fa-times tw-text-rose-600 tw-font-bold tw-text-lg" title="Inactive"></i>
                            @endif
                        </td>
                        <td class="tw-py-4 tw-px-6">
                            @if($mod['active'])
                                <a href="{{ route('admin.settings.modules.toggle', $mod['id']) }}" class="btn red tw-text-xs !tw-py-1.5 !tw-px-4 tw-shadow-md tw-shadow-rose-100">
                                    <i class="fa fa-trash"></i> Uninstall
                                </a>
                            @else
                                <a href="{{ route('admin.settings.modules.toggle', $mod['id']) }}" class="btn green tw-text-xs !tw-py-1.5 !tw-px-4 tw-shadow-md tw-shadow-emerald-100">
                                    <i class="fa fa-refresh"></i> Install
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
