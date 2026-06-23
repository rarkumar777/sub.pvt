@extends('admin.layouts.app')
@section('title', 'Admin | Custom Blocks')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
                Custom <span class="tw-text-indigo-600">Blocks</span>
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Create and manage custom content blocks</p>
        </div>
        <a href="#add_new_block" class="btn indigo">
            <i class="fa fa-plus"></i> Add Block
        </a>
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

    {{-- Custom Blocks Table --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Description</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($blocks as $key => $block)
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-600 tw-flex tw-items-center tw-justify-center tw-text-xs tw-font-bold">
                                    <i class="fa fa-th"></i>
                                </div>
                                <span class="tw-font-bold tw-text-slate-900 tw-text-sm">{{ $key }}</span>
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-text-sm tw-text-slate-500">{{ $block['desc'] ?? '' }}</td>
                        <td class="tw-py-5 tw-px-6 tw-text-right">
                            <div class="tw-flex tw-justify-end tw-items-center tw-gap-2">
                                <a href="{{ route('admin.customblocks.edit', $block['name']) }}" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-600 hover:tw-bg-indigo-600 hover:tw-text-white tw-transition-all tw-no-underline" title="Edit">
                                    <i class="fa fa-edit tw-text-xs"></i>
                                </a>
                                <a href="{{ route('admin.customblocks.delete', $key) }}" onclick="return confirm('Delete {{ $block['name'] }}?');" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-no-underline" title="Delete">
                                    <i class="fa fa-trash tw-text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-rounded-full tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-200">
                                    <i class="fa fa-th tw-text-3xl"></i>
                                </div>
                                <p class="tw-text-slate-400 tw-text-sm tw-font-bold">No custom blocks</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add New Block Modal --}}
<div class="modal" id="add_new_block">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[500px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-emerald-400"></i> Add Custom Block
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.customblocks.store') }}" class="tw-p-8">
            @csrf
            <div class="tw-space-y-5">
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Name</label>
                    <input type="text" name="bname" required placeholder="Block name">
                </div>
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Description</label>
                    <input type="text" name="bdesc" placeholder="Optional description">
                </div>
            </div>
            <button type="submit" class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save Block</button>
        </form>
    </div>
</div>
@endsection
