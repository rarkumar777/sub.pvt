@extends('admin.layouts.app')
@section('title', 'Admin | Languages')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
                <span class="tw-w-12 tw-h-12 tw-bg-violet-50 tw-text-violet-600 tw-rounded-2xl tw-inline-flex tw-items-center tw-justify-center tw-mr-2">
                    <i class="fa fa-language"></i>
                </span>
                Manage <span class="tw-text-indigo-600">Languages</span>
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure available languages and translations</p>
        </div>
        <a href="#add_new_lang" class="btn indigo">
            <i class="fa fa-plus"></i> Add Language
        </a>
    </div>

    {{-- Languages Table --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Language</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Translations</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-center">Status</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @foreach($languages as $lang)
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                @if(file_exists(public_path('lang/' . $lang['code'] . '/' . $lang['flag'])))
                                    <img src="{{ asset('lang/' . $lang['code'] . '/' . $lang['flag']) }}" width="28" height="18" class="tw-rounded tw-shadow-sm tw-border tw-border-slate-100">
                                @else
                                    <div class="tw-w-7 tw-h-5 tw-rounded tw-bg-slate-100 tw-flex tw-items-center tw-justify-center tw-text-[8px] tw-font-bold tw-text-slate-400 tw-uppercase">{{ $lang['code'] }}</div>
                                @endif
                                <div>
                                    <span class="tw-font-bold tw-text-slate-900 tw-text-sm tw-uppercase">{{ $lang['code'] }}</span>
                                    <span class="tw-text-xs tw-text-slate-400 tw-ml-1">({{ $lang['name'] }})</span>
                                </div>
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-flex-wrap tw-gap-1.5">
                                <a href="{{ url('admin/settings/translations?lang=' . $lang['code'] . '&file=main') }}" class="tw-inline-flex tw-items-center tw-gap-1 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-slate-50 tw-text-slate-600 tw-text-[11px] tw-font-bold hover:tw-bg-indigo-50 hover:tw-text-indigo-600 tw-transition-all tw-no-underline tw-border tw-border-slate-100">
                                    <i class="fa fa-file-text-o"></i> Main
                                </a>
                                <a href="{{ url('admin/settings/translations?lang=' . $lang['code'] . '&file=mod_names') }}" class="tw-inline-flex tw-items-center tw-gap-1 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-slate-50 tw-text-slate-600 tw-text-[11px] tw-font-bold hover:tw-bg-indigo-50 hover:tw-text-indigo-600 tw-transition-all tw-no-underline tw-border tw-border-slate-100">
                                    <i class="fa fa-cubes"></i> Modules
                                </a>
                                <a href="{{ url('admin/settings/translations?lang=' . $lang['code'] . '&mod_=tours_booking') }}" class="tw-inline-flex tw-items-center tw-gap-1 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-slate-50 tw-text-slate-600 tw-text-[11px] tw-font-bold hover:tw-bg-indigo-50 hover:tw-text-indigo-600 tw-transition-all tw-no-underline tw-border tw-border-slate-100">
                                    <i class="fa fa-plane"></i> Tours
                                </a>
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-text-center">
                            @if($lang['active'])
                                <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-3 tw-py-1.5 tw-rounded-full tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wide tw-bg-emerald-50 tw-text-emerald-600">
                                    <i class="fa fa-check-circle"></i> Active
                                </span>
                            @else
                                <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-3 tw-py-1.5 tw-rounded-full tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wide tw-bg-rose-50 tw-text-rose-600">
                                    <i class="fa fa-times-circle"></i> Disabled
                                </span>
                            @endif
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-text-right">
                            <div class="tw-flex tw-justify-end tw-items-center tw-gap-2">
                                @if($lang['code'] === $defaultLang)
                                    <span class="tw-px-3 tw-py-1.5 tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-600 tw-text-[11px] tw-font-bold tw-uppercase">Default</span>
                                @else
                                    <form id="toggle-{{ $lang['code'] }}" action="{{ route('admin.settings.languages.toggle', $lang['code']) }}" method="POST" class="tw-inline">@csrf
                                        <button type="submit" class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-3 tw-py-1.5 tw-rounded-xl tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wider tw-transition-all tw-border-none tw-cursor-pointer {{ $lang['active'] ? 'tw-bg-amber-50 tw-text-amber-600 hover:tw-bg-amber-600 hover:tw-text-white' : 'tw-bg-emerald-50 tw-text-emerald-600 hover:tw-bg-emerald-600 hover:tw-text-white' }}">
                                            <i class="fa {{ $lang['active'] ? 'fa-pause' : 'fa-check' }}"></i>
                                            {{ $lang['active'] ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                    <form id="delete-{{ $lang['code'] }}" action="{{ route('admin.settings.languages.delete', $lang['code']) }}" method="POST" class="tw-inline" onsubmit="return confirm('Delete {{ $lang['code'] }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-border-none tw-cursor-pointer" title="Delete">
                                            <i class="fa fa-trash tw-text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add New Language Modal --}}
<div class="modal" id="add_new_lang">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[500px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-emerald-400"></i> Add New Language
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.settings.languages.store') }}" enctype="multipart/form-data" class="tw-p-8">
            @csrf
            <div class="tw-space-y-5">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Code</label>
                    <div class="md:tw-col-span-2">
                        <input type="text" name="code" maxlength="3" required placeholder="e.g. en">
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Name</label>
                    <div class="md:tw-col-span-2">
                        <input type="text" name="name" maxlength="25" required placeholder="e.g. English">
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Right to Left</label>
                    <div class="md:tw-col-span-2">
                        <label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer">
                            <input type="checkbox" name="dir" value="rtl" class="tw-w-4 tw-h-4 tw-rounded tw-border-slate-300 tw-text-indigo-600">
                            <span class="tw-text-xs tw-text-slate-500">Enable RTL direction</span>
                        </label>
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Flag Image</label>
                    <div class="md:tw-col-span-2">
                        <input type="file" name="flag" class="tw-text-sm tw-text-slate-600">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save Language</button>
        </form>
    </div>
</div>
@endsection
