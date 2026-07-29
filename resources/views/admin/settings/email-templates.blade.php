@extends('admin.layouts.app')
@section('title', 'Admin | Email Templates')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    <div class="tw-flex tw-justify-between tw-items-center">
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
            <span class="tw-w-12 tw-h-12 tw-bg-indigo-50 tw-text-indigo-600 tw-rounded-2xl tw-inline-flex tw-items-center tw-justify-center tw-mr-2">
                <i class="fa fa-envelope-o"></i>
            </span>
            E-mail <span class="tw-text-indigo-600">Templates</span>
        </h1>
        @if($mod)
        <a href="{{ route('admin.settings.email-templates') }}" class="btn red !tw-py-2 !tw-px-4 tw-shadow-md tw-shadow-rose-100">
            <i class="fa fa-times"></i> cancel
        </a>
        @endif
    </div>

    <div class="box !tw-p-0 !tw-overflow-hidden">
        @if($mod && array_key_exists($mod, $templates))
            {{-- SUB PAGE --}}
            <div class="tw-bg-slate-50 tw-px-6 tw-py-4 tw-border-b tw-border-slate-100">
                <h3 class="tw-text-sm tw-font-bold tw-text-slate-800 !tw-m-0">Edit E-Mails ({{ $templates[$mod]['name'] }})</h3>
            </div>
            <div class="tw-overflow-x-auto">
                <table class="tw-w-full tw-text-left tw-border-collapse">
                    <tbody class="tw-divide-y tw-divide-slate-50">
                        @foreach($templates[$mod]['emails'] as $key => $templateData)
                        <tr class="hover:tw-bg-slate-50/50 tw-transition-colors">
                            <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-800">{{ $templateData['title'] }}</td>
                            <td class="tw-py-4 tw-px-6 tw-w-40 tw-text-right">
                                <a href="{{ route('admin.settings.email-templates.edit', [$mod, $key]) }}" class="btn blue tw-text-xs !tw-py-1.5 !tw-px-4 tw-shadow-md tw-shadow-blue-100">
                                    <i class="fa fa-edit"></i> edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            {{-- MAIN PAGE --}}
            <div class="tw-overflow-x-auto">
                <table class="tw-w-full tw-text-left tw-border-collapse">
                    <thead>
                        <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                            <th class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-bold tw-text-slate-700">Name</th>
                            <th class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-bold tw-text-slate-700">Edit E-Mails</th>
                        </tr>
                    </thead>
                    <tbody class="tw-divide-y tw-divide-slate-50">
                        @foreach($templates as $key => $temp)
                        <tr class="hover:tw-bg-slate-50/50 tw-transition-colors">
                            <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-800">{{ $temp['name'] }}</td>
                            <td class="tw-py-4 tw-px-6">
                                <a href="{{ route('admin.settings.email-templates', $key) }}" class="btn blue tw-text-xs !tw-py-1.5 !tw-px-4 tw-shadow-md tw-shadow-blue-100">
                                    <i class="fa fa-edit"></i> Edit E-mails
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
