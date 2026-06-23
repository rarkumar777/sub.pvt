@extends('admin.layouts.app')
@section('title', 'Admin | Layouts')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Page <span class="tw-text-indigo-600">Layouts</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Manage page layout templates and column configurations</p>
        </div>
        <a href="#add_new_layout" class="btn indigo"><i class="fa fa-plus"></i> Add Layout</a>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-lg"></i>
        <span class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="tw-bg-rose-50 tw-border-l-4 tw-border-rose-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-exclamation-circle tw-text-rose-500 tw-text-lg"></i>
        <span class="tw-text-rose-800 tw-font-bold tw-text-sm">{{ session('error') }}</span>
    </div>
    @endif

    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Layout Columns</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($layouts as $name => $colType)
                    @php
                        if($colType == '2r') $colLabel = '2 columns with right side';
                        elseif($colType == '2l') $colLabel = '2 columns with left side';
                        elseif($colType == '3') $colLabel = '3 columns with left & right sides';
                        else $colLabel = '1 column';
                    @endphp
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-5 tw-px-6 tw-text-sm tw-font-bold tw-text-slate-900">{{ $name }}</td>
                        <td class="tw-py-5 tw-px-6">
                            <span class="tw-inline-flex tw-items-center tw-px-3 tw-py-1 tw-rounded-full tw-text-[11px] tw-font-bold tw-bg-indigo-50 tw-text-indigo-600">{{ $colLabel }}</span>
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-text-right">
                            <div class="tw-flex tw-justify-end tw-items-center tw-gap-2">
                                <a href="{{ route('admin.layouts.blocks', $name) }}" class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-3 tw-py-1.5 tw-rounded-xl tw-bg-emerald-50 tw-text-emerald-600 tw-text-[11px] tw-font-bold tw-uppercase hover:tw-bg-emerald-600 hover:tw-text-white tw-transition-all tw-no-underline">
                                    <i class="fa fa-th"></i> Blocks
                                </a>
                                @if($defaultLayout == $name)
                                    <span class="tw-px-3 tw-py-1.5 tw-rounded-xl tw-bg-slate-100 tw-text-slate-500 tw-text-[11px] tw-font-bold tw-uppercase">Default</span>
                                @else
                                    <a href="{{ route('admin.layouts.delete', $name) }}" onclick="return confirm('Delete {{ $name }}?');" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-no-underline"><i class="fa fa-trash tw-text-xs"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="tw-py-16 tw-text-center tw-text-slate-400 tw-text-sm tw-font-bold">No layouts</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal" id="add_new_layout">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[500px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0"><i class="fa fa-plus-circle tw-text-emerald-400"></i> Add Layout</h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.layouts.store') }}" class="tw-p-8">@csrf
            <div class="tw-space-y-5">
                <div><label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Name</label><input type="text" name="layoutname" required placeholder="Layout name"></div>
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Layout Columns</label>
                    <select name="columns_num" required>
                        <option value="">Select</option>
                        <option value="1">1 column</option>
                        <option value="2l">2 columns with left side</option>
                        <option value="2r">2 columns with right side</option>
                        <option value="3">3 columns with left & right sides</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save</button>
        </form>
    </div>
</div>
@endsection
