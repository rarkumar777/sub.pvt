@extends('admin.layouts.app')
@section('title', 'Admin | Tour Inclusions')

@section('content')
@php $content = $tour->contents->where('lang', 'en')->first(); @endphp

<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumb --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
        <a href="{{ route('admin.tours.index') }}" class="tw-text-orange-600 tw-font-semibold tw-no-underline hover:tw-text-orange-800 tw-transition-colors">
            <i class="fa fa-plane tw-mr-1"></i> Tours
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <a href="{{ route('admin.tours.edit', $tour->id) }}" class="tw-text-orange-600 tw-font-semibold tw-no-underline hover:tw-text-orange-800 tw-transition-colors">
            Edit Tour #{{ $tour->id }}
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <span class="tw-text-slate-500 tw-font-semibold">Inclusions & Exclusions</span>
    </div>

    {{-- Header --}}
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">{{ $content->title ?? 'Tour #'.$tour->id }}</h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Freely add inclusions and exclusions for this tour</p>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-lg"></i>
        <span class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</span>
    </div>
    @endif

    @include('admin.tours._edit_menu', ['tour' => $tour])

    <form method="POST" action="{{ route('admin.tours.inclusions.update', $tour->id) }}" id="inclusionsForm">
        @csrf

        {{-- Inclusions Section --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-8 tw-py-5 tw-bg-orange-50/50 tw-border-b tw-border-orange-100 tw-flex tw-items-center tw-justify-between">
                <h3 class="tw-text-sm tw-font-bold tw-text-orange-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                    <i class="fa fa-check-circle tw-text-orange-500"></i> Included in Package
                </h3>
                <button type="button" onclick="addRow('inclusions-body', 'inclusion')" class="tw-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-rounded-xl tw-bg-orange-500 tw-text-white tw-text-xs tw-font-bold hover:tw-bg-orange-600 tw-transition-colors tw-border-0 tw-cursor-pointer">
                    <i class="fa fa-plus"></i> Add Inclusion
                </button>
            </div>
            <div class="tw-p-6">
                <div id="inclusions-body" class="tw-space-y-3">
                    @foreach($inclusions->where('type', 'inclusion') as $idx => $inc)
                    <div class="tw-flex tw-items-center tw-gap-3 inclusion-row" data-type="inclusion">
                        <span class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-orange-50 tw-text-orange-500 tw-flex tw-items-center tw-justify-center tw-flex-shrink-0">
                            <i class="fa fa-check tw-text-xs"></i>
                        </span>
                        <input type="hidden" name="items[{{ $idx }}][type]" value="inclusion">
                        <input type="hidden" name="items[{{ $idx }}][sort_order]" value="{{ $inc->sort_order }}">
                        <input type="text" name="items[{{ $idx }}][name]" value="{{ $inc->name }}" placeholder="e.g. Airport Transfer, Guide, Entrance Fees..." class="tw-flex-1 !tw-mb-0" required>
                        <button type="button" onclick="removeRow(this)" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all tw-border-0 tw-cursor-pointer tw-flex-shrink-0">
                            <i class="fa fa-trash tw-text-xs"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                <div id="inclusions-empty" class="tw-py-10 tw-text-center {{ $inclusions->where('type', 'inclusion')->count() ? 'tw-hidden' : '' }}">
                    <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                        <div class="tw-w-14 tw-h-14 tw-rounded-full tw-bg-orange-50 tw-flex tw-items-center tw-justify-center tw-text-orange-200">
                            <i class="fa fa-check-circle tw-text-2xl"></i>
                        </div>
                        <p class="tw-text-slate-400 tw-text-sm tw-font-bold">No inclusions added yet</p>
                        <button type="button" onclick="addRow('inclusions-body', 'inclusion')" class="tw-text-orange-600 tw-text-xs tw-font-bold hover:tw-underline tw-border-0 tw-bg-transparent tw-cursor-pointer">
                            <i class="fa fa-plus"></i> Add your first inclusion
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Exclusions Section --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-8 tw-py-5 tw-bg-rose-50/50 tw-border-b tw-border-rose-100 tw-flex tw-items-center tw-justify-between">
                <h3 class="tw-text-sm tw-font-bold tw-text-rose-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                    <i class="fa fa-times-circle tw-text-rose-500"></i> Not Included (Exclusions)
                </h3>
                <button type="button" onclick="addRow('exclusions-body', 'exclusion')" class="tw-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-rounded-xl tw-bg-rose-500 tw-text-white tw-text-xs tw-font-bold hover:tw-bg-rose-600 tw-transition-colors tw-border-0 tw-cursor-pointer">
                    <i class="fa fa-plus"></i> Add Exclusion
                </button>
            </div>
            <div class="tw-p-6">
                <div id="exclusions-body" class="tw-space-y-3">
                    @foreach($inclusions->where('type', 'exclusion') as $idx => $exc)
                    <div class="tw-flex tw-items-center tw-gap-3 inclusion-row" data-type="exclusion">
                        <span class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-rose-50 tw-text-rose-500 tw-flex tw-items-center tw-justify-center tw-flex-shrink-0">
                            <i class="fa fa-times tw-text-xs"></i>
                        </span>
                        <input type="hidden" name="items[{{ 100 + $idx }}][type]" value="exclusion">
                        <input type="hidden" name="items[{{ 100 + $idx }}][sort_order]" value="{{ $exc->sort_order }}">
                        <input type="text" name="items[{{ 100 + $idx }}][name]" value="{{ $exc->name }}" placeholder="e.g. International Flights, Travel Insurance..." class="tw-flex-1 !tw-mb-0" required>
                        <button type="button" onclick="removeRow(this)" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all tw-border-0 tw-cursor-pointer tw-flex-shrink-0">
                            <i class="fa fa-trash tw-text-xs"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                <div id="exclusions-empty" class="tw-py-10 tw-text-center {{ $inclusions->where('type', 'exclusion')->count() ? 'tw-hidden' : '' }}">
                    <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                        <div class="tw-w-14 tw-h-14 tw-rounded-full tw-bg-rose-50 tw-flex tw-items-center tw-justify-center tw-text-rose-200">
                            <i class="fa fa-times-circle tw-text-2xl"></i>
                        </div>
                        <p class="tw-text-slate-400 tw-text-sm tw-font-bold">No exclusions added yet</p>
                        <button type="button" onclick="addRow('exclusions-body', 'exclusion')" class="tw-text-rose-600 tw-text-xs tw-font-bold hover:tw-underline tw-border-0 tw-bg-transparent tw-cursor-pointer">
                            <i class="fa fa-plus"></i> Add your first exclusion
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn orange tw-w-full !tw-py-4 !tw-text-base tw-shadow-lg tw-shadow-orange-100">
            <i class="fa fa-check-circle"></i> Save Inclusions & Exclusions
        </button>
    </form>
</div>

<script>
var rowCounter = 500;

function addRow(containerId, type) {
    rowCounter++;
    var container = document.getElementById(containerId);
    var iconClass = type === 'inclusion' ? 'fa-check' : 'fa-times';
    var colorClass = type === 'inclusion' ? 'orange' : 'rose';
    var placeholder = type === 'inclusion'
        ? 'e.g. Airport Transfer, Guide, Entrance Fees...'
        : 'e.g. International Flights, Travel Insurance...';

    var html = '<div class="tw-flex tw-items-center tw-gap-3 inclusion-row" data-type="' + type + '">' +
        '<span class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-' + colorClass + '-50 tw-text-' + colorClass + '-500 tw-flex tw-items-center tw-justify-center tw-flex-shrink-0">' +
            '<i class="fa ' + iconClass + ' tw-text-xs"></i>' +
        '</span>' +
        '<input type="hidden" name="items[' + rowCounter + '][type]" value="' + type + '">' +
        '<input type="hidden" name="items[' + rowCounter + '][sort_order]" value="' + rowCounter + '">' +
        '<input type="text" name="items[' + rowCounter + '][name]" value="" placeholder="' + placeholder + '" class="tw-flex-1 !tw-mb-0" required>' +
        '<button type="button" onclick="removeRow(this)" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all tw-border-0 tw-cursor-pointer tw-flex-shrink-0">' +
            '<i class="fa fa-trash tw-text-xs"></i>' +
        '</button>' +
    '</div>';

    container.insertAdjacentHTML('beforeend', html);

    // Hide empty state
    var emptyId = type === 'inclusion' ? 'inclusions-empty' : 'exclusions-empty';
    document.getElementById(emptyId).classList.add('tw-hidden');

    // Focus the new input
    container.lastElementChild.querySelector('input[type="text"]').focus();
}

function removeRow(btn) {
    var row = btn.closest('.inclusion-row');
    var type = row.dataset.type;
    var container = row.parentElement;
    row.remove();

    // Show empty state if no rows left
    if (container.children.length === 0) {
        var emptyId = type === 'inclusion' ? 'inclusions-empty' : 'exclusions-empty';
        document.getElementById(emptyId).classList.remove('tw-hidden');
    }
}
</script>
@endsection
