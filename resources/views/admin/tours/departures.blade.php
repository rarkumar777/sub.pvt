@extends('admin.layouts.app')
@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumb --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
        <a href="{{ route('admin.tours.index') }}" class="tw-text-orange-600 tw-font-semibold tw-no-underline hover:tw-text-orange-800 tw-transition-colors">
            <i class="fa fa-plane tw-mr-1"></i> Tours
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <a href="{{ route('admin.tours.edit', $tour->id) }}" class="tw-text-orange-600 tw-font-semibold tw-no-underline hover:tw-text-orange-800 tw-transition-colors">
            Edit > Tour #{{ $tour->id }}
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <span class="tw-text-slate-500 tw-font-semibold">Guaranteed Departures</span>
    </div>

    @php $content = $tour->contents->where('lang', 'en')->first(); @endphp

    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-end">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
                {{ $content->title ?? 'Tour #'.$tour->id }} → Guaranteed Departures
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Manage departure schedules for this tour</p>
        </div>
        <a class="btn orange" href="{{ route('admin.guaranteed-departures.create') }}?tour_id={{ $tour->id }}">
            <i class="fa fa-plus"></i> <span class="tw-hidden sm:tw-inline">Add Departure</span>
        </a>
    </div>

    {{-- Tour Edit Sub-Nav --}}
    @include('admin.tours._edit_menu', ['tour' => $tour])

    {{-- Table Card --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">#</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">From Date</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">To Date</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Adult Price</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Child Price</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-center">Status</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($departures as $dep)
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-5 tw-px-6">
                            <span class="tw-bg-slate-100 tw-text-slate-600 tw-text-[11px] tw-font-bold tw-px-2.5 tw-py-1 tw-rounded-full">{{ $dep->id }}</span>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <span class="tw-text-sm tw-font-semibold tw-text-slate-700">{{ $dep->from_date }}</span>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <span class="tw-text-sm tw-font-semibold tw-text-slate-700">{{ $dep->to_date }}</span>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <span class="tw-text-sm tw-font-bold tw-text-orange-600">{{ $dep->adult_price }}</span>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <span class="tw-text-sm tw-font-bold tw-text-orange-600">{{ $dep->child_price }}</span>
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-text-center">
                            @if($dep->status)
                            <span class="tw-inline-flex tw-items-center tw-gap-1 tw-px-3 tw-py-1.5 tw-rounded-full tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wide tw-bg-orange-50 tw-text-orange-600">
                                <i class="fa fa-check-circle"></i> Active
                            </span>
                            @else
                            <span class="tw-inline-flex tw-items-center tw-gap-1 tw-px-3 tw-py-1.5 tw-rounded-full tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wide tw-bg-rose-50 tw-text-rose-600">
                                <i class="fa fa-times-circle"></i> Off
                            </span>
                            @endif
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-text-right">
                            <a href="{{ route('admin.guaranteed-departures.edit', $dep->id) }}" class="tw-inline-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-rounded-xl tw-bg-slate-50 tw-text-slate-600 tw-text-xs tw-font-bold hover:tw-bg-orange-50 hover:tw-text-orange-600 tw-transition-all tw-no-underline">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-rounded-full tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-300">
                                    <i class="fa fa-rocket tw-text-3xl"></i>
                                </div>
                                <div>
                                    <p class="tw-text-slate-600 tw-font-bold">No departures found</p>
                                    <p class="tw-text-slate-400 tw-text-xs tw-mt-1">Add a new departure to get started.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
