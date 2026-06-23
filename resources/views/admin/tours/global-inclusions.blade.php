@extends('admin.layouts.app')

@section('title', 'Admin | Tour Inclusions')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">
    
    {{-- Header --}}
    <div class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-bold tw-text-orange-400 tw-uppercase tw-tracking-widest tw-mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-600 tw-transition-colors tw-no-underline">Dashboard</a>
                <span class="tw-opacity-50">/</span>
                <span class="tw-text-slate-500">Tours Settings</span>
            </div>
            <h1 class="tw-text-3xl tw-font-black tw-text-slate-900 tw-tracking-tight">
                <i class="fa fa-check-circle tw-text-orange-500 tw-mr-2"></i>Conditions Library
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1 tw-text-sm">Inclusions & exclusions items — used in Tours and Request Manager</p>
        </div>
        <a href="#addnew" class="tw-inline-flex tw-items-center tw-gap-2 tw-bg-orange-500 hover:tw-bg-orange-600 tw-text-white tw-px-5 tw-py-3 tw-rounded-xl tw-font-bold tw-text-sm tw-whitespace-nowrap tw-transition-all tw-shadow-lg tw-shadow-orange-200 tw-no-underline">
            <i class="fa fa-plus"></i> Add New Item
        </a>
    </div>

    @if(session('success'))
    <div class="tw-bg-orange-50 tw-border-l-4 tw-border-orange-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-orange-500"></i>
        <span class="tw-text-orange-800 tw-font-bold tw-text-sm">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Compact Table View --}}
    <div class="box !tw-p-0 !tw-mb-0">
        <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-justify-between">
            <span class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider">
                {{ $inclusions->count() }} items
            </span>
        </div>
        <table class="tw-w-full">
            <thead>
                <tr class="tw-bg-slate-50/50">
                    <th class="tw-text-left tw-px-6 tw-py-3 tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-w-12">#</th>
                    <th class="tw-text-left tw-px-4 tw-py-3 tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Name</th>
                    <th class="tw-text-center tw-px-4 tw-py-3 tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-w-20">ID</th>
                    <th class="tw-text-right tw-px-6 tw-py-3 tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-w-24">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inclusions as $idx => $i)
                <tr class="tw-border-b tw-border-slate-50 hover:tw-bg-orange-50/30 tw-transition-colors">
                    <td class="tw-px-6 tw-py-3 tw-text-xs tw-text-slate-400 tw-font-bold">{{ $idx + 1 }}</td>
                    <td class="tw-px-4 tw-py-3">
                        <span class="tw-text-sm tw-font-bold tw-text-slate-800">{{ $i->name }}</span>
                    </td>
                    <td class="tw-text-center tw-px-4 tw-py-3">
                        <span class="tw-text-[10px] tw-font-bold tw-text-slate-400">{{ $i->lang_id }}</span>
                    </td>
                    <td class="tw-text-right tw-px-6 tw-py-3">
                        <div class="tw-flex tw-items-center tw-justify-end tw-gap-1.5">
                            <a href="javascript:void(0);" onclick="do_ajax('#ajax','{{ url('admin/tour-inclusions/' . $i->lang_id . '/edit-ajax') }}','#edit_t'); return false;" class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-orange-50 tw-text-orange-500 hover:tw-bg-orange-500 hover:tw-text-white tw-flex tw-items-center tw-justify-center tw-transition-colors" title="Edit">
                                <i class="fa fa-pencil tw-text-[11px]"></i>
                            </a>
                            <a href="{{ route('admin.tour-inclusions.destroy', $i->lang_id) }}" onclick="return confirm('Delete: {{ $i->name }}?');" class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-rose-50 tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white tw-flex tw-items-center tw-justify-center tw-transition-colors" title="Delete">
                                <i class="fa fa-trash-o tw-text-[11px]"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="tw-text-center tw-py-16">
                        <p class="tw-text-slate-400 tw-font-bold tw-text-sm">No items yet</p>
                        <a href="#addnew" class="tw-text-orange-600 tw-text-xs tw-font-bold hover:tw-underline tw-no-underline">Add first item</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add New Inclusion Modal --}}
<div class="modal" id="addnew">
    <div class="tw-w-full tw-max-w-lg !tw-p-8 !tw-rounded-[2rem] tw-bg-white/95 tw-backdrop-blur-xl tw-border tw-border-slate-100 tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-mb-6">
            <div class="tw-flex tw-items-center tw-gap-3">
                <span class="tw-w-10 tw-h-10 tw-bg-orange-500 tw-text-white tw-rounded-xl tw-flex tw-items-center tw-justify-center">
                    <i class="fa fa-plus"></i>
                </span>
                <h3 class="tw-text-lg tw-font-black tw-text-slate-900 tw-mb-0">New Inclusion Item</h3>
            </div>
            <a href="#close" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-slate-50 tw-text-slate-400 hover:tw-bg-rose-50 hover:tw-text-rose-500 tw-transition-all tw-no-underline">
                <i class="fa fa-times"></i>
            </a>
        </div>
        
        <form method="POST" action="{{ route('admin.tour-inclusions.store') }}" class="tw-flex tw-flex-col tw-gap-4">
            @csrf
            <input type="hidden" name="add_new" value="1">
            
            {{-- English (Required) --}}
            <div>
                <label class="tw-text-xs tw-font-bold tw-text-slate-600 tw-uppercase tw-tracking-wider tw-mb-1.5 tw-block">
                    English <span class="tw-text-rose-500">*</span>
                </label>
                <input type="text" name="inclusion_en" id="inclusion_en" value="{{ old('inclusion_en') }}" placeholder="e.g. Airport Transfer, Guide..." class="tw-w-full" required>
                @error('inclusion_en') <span class="tw-text-xs tw-font-bold tw-text-rose-500">{{ $message }}</span> @enderror
            </div>

            {{-- Other languages (auto-filled, editable) --}}
            <div class="tw-border tw-border-slate-100 tw-rounded-xl tw-p-4 tw-bg-slate-50/50">
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                    <span class="tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Other Languages (optional — will copy English if empty)</span>
                </div>
                <div class="tw-grid tw-grid-cols-2 tw-gap-3">
                    @foreach(['fr' => 'French', 'it' => 'Italian', 'es' => 'Spanish', 'Ar' => 'Arabic', 'ge' => 'German', 'pt' => 'Portuguese'] as $code => $label)
                    <div>
                        <label class="tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-mb-1 tw-block">{{ $label }}</label>
                        <input type="text" name="inclusion_{{ $code }}" value="{{ old('inclusion_'.$code) }}" placeholder="{{ $label }}..." class="tw-w-full !tw-h-9 !tw-text-xs">
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="tw-flex tw-items-center tw-justify-end tw-gap-3 tw-mt-2">
                <a href="#close" class="tw-px-5 tw-py-2.5 tw-rounded-xl tw-font-bold tw-text-slate-500 hover:tw-bg-slate-100 tw-transition-colors tw-no-underline tw-text-sm">Cancel</a>
                <button type="submit" class="tw-bg-orange-500 hover:tw-bg-orange-600 tw-text-white tw-px-6 tw-py-2.5 tw-rounded-xl tw-font-bold tw-shadow-lg tw-shadow-orange-200 tw-transition-all tw-text-sm">
                    <i class="fa fa-check tw-mr-1"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<div id="ajax"></div>

@if($errors->any() && old('add_new'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.location.hash = '#addnew';
    });
</script>
@endif

@endsection
