@extends('admin.layouts.app')
@section('title', 'Admin | Edit Group Fields')

@section('content')
<div class="tw-mb-8">
    <nav class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest tw-text-slate-400 tw-mb-4">
        <a href="{{ route('admin.user-groups.index') }}" class="hover:tw-text-orange-500 tw-transition-colors tw-no-underline">User Groups</a>
        <i class="fa fa-chevron-right tw-text-[11px]"></i>
        <span class="tw-text-slate-900">Edit Group Fields</span>
    </nav>
    
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-flex tw-items-center tw-gap-4">
                <span class="tw-w-12 tw-h-12 tw-bg-amber-50 tw-text-amber-600 tw-rounded-2xl tw-flex tw-items-center tw-justify-center">
                    <i class="fa fa-th-list"></i>
                </span>
                Edit Group Fields: <span class="tw-text-orange-600">{{ $groupId }}</span>
            </h1>
            <p class="subtitle">Configure which data fields are asked or required for the <span class="tw-font-bold">{{ $groupId }}</span> group during sign-up.</p>
        </div>
        <a href="{{ route('admin.user-groups.index') }}" class="btn white !tw-text-slate-600">
            <i class="fa fa-arrow-left"></i> Back to Groups
        </a>
    </div>
</div>

@if(session('success'))
<div class="tw-bg-emerald-50 tw-border tw-border-emerald-100 tw-text-emerald-600 tw-px-6 tw-py-4 tw-rounded-2xl tw-mb-8 tw-flex tw-items-center tw-gap-3">
    <i class="fa fa-check-circle tw-text-xl"></i>
    <span class="tw-font-bold">{{ session('success') }}</span>
</div>
@endif

<form action="{{ route('admin.user-groups.fields.update', $groupId) }}" method="POST">
    @csrf
    <div class="box !tw-p-0 tw-overflow-hidden tw-border-none tw-shadow-sm">
        <!-- Table Header -->
        <div class="tw-grid tw-grid-cols-12 tw-bg-slate-50/80 tw-px-8 tw-py-5 tw-border-b tw-border-slate-100">
            <div class="tw-col-span-4 tw-text-[11px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest">Registration Field</div>
            <div class="tw-col-span-8 tw-text-[11px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest">Configuration Status</div>
        </div>

        <!-- Table Body -->
        <div class="tw-divide-y tw-divide-slate-50">
            @foreach($fieldLabels as $key => $label)
            <div class="tw-grid tw-grid-cols-12 tw-px-8 tw-py-6 tw-items-center hover:tw-bg-slate-50/30 tw-transition-all">
                <div class="tw-col-span-4">
                    <div class="tw-font-bold tw-text-slate-910 tw-text-sm">{{ $label }}</div>
                    <div class="tw-text-[11px] tw-text-slate-400 tw-mt-0.5 tw-font-medium tw-uppercase tw-tracking-wider">{{ $key }}</div>
                </div>
                <div class="tw-col-span-8">
                    <div class="tw-flex tw-items-center tw-gap-6">
                        @php
                            $current = $fields[$key] ?? 'd';
                            $options = [
                                'a' => ['label' => 'Ask', 'icon' => 'fa-eye', 'color' => 'orange'],
                                'r' => ['label' => 'Ask for and requier it', 'icon' => 'fa-exclamation-circle', 'color' => 'amber'],
                                'd' => ['label' => 'Disabled', 'icon' => 'fa-ban', 'color' => 'slate']
                            ];
                        @endphp

                        @foreach($options as $val => $opt)
                        <label class="tw-group tw-relative tw-flex tw-items-center tw-gap-2.5 tw-cursor-pointer">
                            <input type="radio" name="fields[{{ $key }}]" value="{{ $val }}" class="tw-sr-only tw-peer" {{ $current == $val ? 'checked' : '' }}>
                            <div class="tw-w-5 tw-h-5 tw-bg-white tw-border-2 tw-border-slate-200 tw-rounded-full tw-transition-all group-hover:tw-border-{{ $opt['color'] }}-300 peer-checked:tw-border-{{ $opt['color'] }}-500 peer-checked:tw-bg-{{ $opt['color'] }}-500 tw-flex tw-items-center tw-justify-center">
                                <div class="tw-w-2 tw-h-2 tw-bg-white tw-rounded-full tw-scale-0 tw-transition-transform peer-checked:tw-scale-100"></div>
                            </div>
                            <span class="tw-text-sm tw-font-bold tw-text-slate-500 group-hover:tw-text-slate-700 peer-checked:tw-text-slate-900 tw-transition-colors">
                                <i class="fa {{ $opt['icon'] }} tw-mr-1.5 tw-text-[11px] {{ $current == $val ? 'tw-text-'.$opt['color'].'-500' : '' }}"></i>
                                {{ $opt['label'] }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Footer Actions -->
        <div class="tw-bg-slate-50/50 tw-px-8 tw-py-8 tw-border-t tw-border-slate-100 tw-flex tw-justify-end tw-items-center tw-gap-4">
            <a href="{{ route('admin.user-groups.index') }}" class="tw-text-sm tw-font-bold tw-text-slate-400 hover:tw-text-slate-600 tw-transition-colors tw-no-underline">Discard Changes</a>
            <button type="submit" class="btn orange !tw-px-12 !tw-py-4 !tw-h-auto tw-shadow-lg tw-shadow-orange-200/50">
                <i class="fa fa-save tw-mr-2"></i> Save Field Settings
            </button>
        </div>
    </div>
</form>

<style>
    /* Custom spacing for the radio buttons */
    .tw-grid-cols-12 {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
    }
    .tw-col-span-4 { grid-column: span 4 / span 4; }
    .tw-col-span-8 { grid-column: span 8 / span 8; }
</style>
@endsection
