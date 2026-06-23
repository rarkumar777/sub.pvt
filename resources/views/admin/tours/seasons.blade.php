@extends('admin.layouts.app')
@section('title', 'Admin | Tour Seasons')

@section('content')
@php $content = $tour->contents->where('lang', 'en')->first(); @endphp

<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumb --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
        <a href="{{ route('admin.tours.index') }}" class="tw-text-indigo-600 tw-font-semibold tw-no-underline hover:tw-text-indigo-800 tw-transition-colors">
            <i class="fa fa-plane tw-mr-1"></i> Tours
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <a href="{{ route('admin.tours.edit', $tour->id) }}" class="tw-text-indigo-600 tw-font-semibold tw-no-underline hover:tw-text-indigo-800 tw-transition-colors">
            Edit Tour #{{ $tour->id }}
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <span class="tw-text-slate-500 tw-font-semibold">Seasons</span>
    </div>

    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">{{ $content->title ?? 'Tour #'.$tour->id }}</h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Manage seasonal date ranges for this tour</p>
        </div>
        <a href="#add_season" class="btn indigo">
            <i class="fa fa-plus"></i> Add Season
        </a>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-lg"></i>
        <span class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</span>
    </div>
    @endif

    @include('admin.tours._edit_menu', ['tour' => $tour])

    {{-- Seasons Table --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-w-16">#</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Season Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">From Date</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">To Date</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($seasons as $season)
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-400 tw-font-medium">{{ $season->id }}</td>
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-amber-50 tw-text-amber-600 tw-flex tw-items-center tw-justify-center tw-text-xs tw-font-bold">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <span class="tw-font-bold tw-text-slate-900 tw-text-sm">{{ $season->name }}</span>
                            </div>
                        </td>
                        <td class="tw-py-4 tw-px-6">
                            <span class="tw-text-sm tw-font-semibold tw-text-emerald-600">{{ $season->from_date }}</span>
                        </td>
                        <td class="tw-py-4 tw-px-6">
                            <span class="tw-text-sm tw-font-semibold tw-text-rose-600">{{ $season->to_date }}</span>
                        </td>
                        <td class="tw-py-4 tw-px-6 tw-text-right">
                            <div class="tw-flex tw-justify-end tw-items-center tw-gap-2">
                                <a href="#" class="tw-w-9 tw-h-9 tw-inline-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-600 hover:tw-bg-indigo-600 hover:tw-text-white tw-transition-all tw-no-underline" title="Edit">
                                    <i class="fa fa-edit tw-text-xs"></i>
                                </a>
                                <a href="#" onclick="return confirm('Delete this season?');" class="tw-w-9 tw-h-9 tw-inline-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-no-underline" title="Delete">
                                    <i class="fa fa-trash tw-text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-rounded-full tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-200">
                                    <i class="fa fa-calendar-times-o tw-text-3xl"></i>
                                </div>
                                <p class="tw-text-slate-400 tw-text-sm tw-font-bold">No seasons configured yet</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Season Modal --}}
<div class="modal" id="add_season">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[500px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-emerald-400"></i> Add Season
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.tours.seasons.store', $tour->id) }}" class="tw-p-8">
            @csrf
            <div class="tw-space-y-5">
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Season Name</label>
                    <input type="text" name="name" placeholder="e.g. High Season">
                </div>
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">From Date</label>
                    <input type="date" name="from_date" required>
                </div>
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">To Date</label>
                    <input type="date" name="to_date" required>
                </div>
            </div>
            <button type="submit" class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save Season</button>
        </form>
    </div>
</div>
@endsection
