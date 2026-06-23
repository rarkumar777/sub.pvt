@extends('admin.layouts.app')
@section('title', 'Guaranteed Departure')
@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">

    @if(session('success'))
    <div class="tw-bg-orange-50 tw-border tw-border-orange-200 tw-text-orange-700 tw-px-5 tw-py-3.5 tw-rounded-xl tw-flex tw-items-center tw-gap-3 tw-text-sm tw-font-semibold tw-shadow-sm">
        <i class="fa fa-check-circle tw-text-orange-500 tw-text-lg"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-600 tw-transition-colors tw-no-underline">Dashboard</a>
                <span class="tw-opacity-50">/</span>
                <span>Tours</span>
                <span class="tw-opacity-50">/</span>
                <span class="tw-text-slate-600">Guaranteed Departure</span>
            </div>
            <h1 class="tw-text-3xl tw-font-black tw-text-slate-900 tw-flex tw-items-center tw-gap-3">
                <div class="tw-w-10 tw-h-10 tw-bg-amber-500 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-amber-200">
                    <i class="fa fa-calendar-check-o tw-text-white tw-text-base"></i>
                </div>
                Guaranteed Departures
            </h1>
            <p class="subtitle">Filter and manage scheduled guaranteed departure dates.</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="get" action="{{ route('admin.guaranteed-departures.index') }}">
        <div class="box !tw-p-5 !tw-mb-0">
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-slate-400 tw-mb-4">
                <i class="fa fa-filter tw-text-sm"></i>
                <span class="tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest">Filters</span>
            </div>
            <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-4 lg:tw-grid-cols-7 tw-gap-4 tw-items-end">
                
                <div class="tw-flex tw-flex-col tw-gap-1.5">
                    <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-0">Completed</label>
                    <select name="completed">
                        <option value="h" {{ request('completed')=='s' ? '' : 'selected' }}>Hide Complete</option>
                        <option value="s" {{ request('completed')=='s' ? 'selected' : '' }}>Show Complete</option>
                    </select>
                </div>
                
                <div class="tw-flex tw-flex-col tw-gap-1.5">
                    <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-0">Cancelled</label>
                    <select name="cancelled">
                        <option value="h" {{ request('cancelled')=='s' ? '' : 'selected' }}>Hide Cancelled</option>
                        <option value="s" {{ request('cancelled')=='s' ? 'selected' : '' }}>Show Cancelled</option>
                    </select>
                </div>
                
                <div class="tw-flex tw-flex-col tw-gap-1.5">
                    <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-0">Booking #</label>
                    <input type="text" name="booking_number" value="{{ request('booking_number') }}" placeholder="Enter ID">
                </div>
                
                <div class="tw-flex tw-flex-col tw-gap-1.5">
                    <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-0">Title</label>
                    <input type="text" name="title" value="{{ request('title') }}" placeholder="Search Title">
                </div>
                
                <div class="tw-flex tw-flex-col tw-gap-1.5">
                    <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-0">Date From</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}">
                </div>
                
                <div class="tw-flex tw-flex-col tw-gap-1.5">
                    <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-0">Date To</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}">
                </div>
                
                <div class="tw-flex tw-gap-2 tw-items-end">
                    <button type="submit" class="btn orange tw-flex-1">
                        <i class="fa fa-search"></i> Search
                    </button>
                    <a href="{{ route('admin.guaranteed-departures.index') }}" class="btn red !tw-px-3" title="Reset Filters">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- Main Table --}}
    <div class="box !tw-p-0 tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/80 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Date</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Title / Days</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Travelers</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-center">Pricing</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-center">Booked</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($departures as $r)
                    @php
                        $days = $r->tour ? $r->tour->days : 0;
                        $depDate = date('Y-m-d', strtotime($r->date . ' + ' . ($days - 1) . ' days'));
                    @endphp
                    <tr class="hover:tw-bg-orange-50/30 tw-transition-colors">
                        {{-- Date --}}
                        <td class="tw-py-4 tw-px-6 tw-align-top tw-whitespace-nowrap">
                            <div class="tw-flex tw-flex-col tw-gap-1">
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <span class="tw-w-5 tw-h-5 tw-rounded-md tw-bg-orange-50 tw-text-orange-600 tw-flex tw-items-center tw-justify-center tw-text-[9px] tw-font-black">A</span>
                                    <span class="tw-font-bold tw-text-slate-800">{{ $r->date }}</span>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <span class="tw-w-5 tw-h-5 tw-rounded-md tw-bg-rose-50 tw-text-rose-600 tw-flex tw-items-center tw-justify-center tw-text-[9px] tw-font-black">D</span>
                                    <span class="tw-font-bold tw-text-slate-800">{{ $depDate }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Title / Days --}}
                        <td class="tw-py-4 tw-px-6 tw-align-top">
                            <div class="tw-font-bold tw-text-orange-600 tw-text-sm tw-mb-2">{{ $r->title }}</div>
                            <div class="tw-flex tw-flex-wrap tw-gap-1.5">
                                <span class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-slate-100 tw-text-slate-700 tw-text-[11px] tw-font-bold">
                                    <i class="fa fa-clock-o tw-mr-1.5 tw-text-slate-400"></i> {{ $days }} Days
                                </span>
                                <span class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-orange-50 tw-text-orange-600 tw-text-[11px] tw-font-bold">
                                    ID: {{ $r->id }}
                                </span>
                            </div>
                        </td>

                        {{-- Travelers --}}
                        <td class="tw-py-4 tw-px-6 tw-align-top tw-whitespace-nowrap">
                            <div class="tw-flex tw-flex-col tw-gap-1.5">
                                <div class="tw-text-xs"><span class="tw-text-slate-400 tw-font-semibold">Min:</span> <span class="tw-font-bold tw-text-slate-800">{{ $r->min_to_operate }}</span></div>
                                <div class="tw-text-xs"><span class="tw-text-slate-400 tw-font-semibold">Max:</span> <span class="tw-font-bold tw-text-slate-800">{{ $r->max_to_operate }}</span></div>
                            </div>
                        </td>

                        {{-- Pricing --}}
                        <td class="tw-py-4 tw-px-6 tw-align-top tw-text-center">
                            <select class="!tw-h-10 !tw-text-[12px] !tw-rounded-xl !tw-border-slate-200 !tw-bg-slate-50/50 !tw-px-3 !tw-py-1 tw-text-slate-700" onchange="return false;" style="min-width: 160px; max-width: 200px; margin: 0 auto;">
                                <option>Adult: {{ number_format($r->adult_price, 2) }} JOD</option>
                                <option>Early Bird: {{ number_format($r->early_bird_price, 2) }} JOD</option>
                                <option>Last Minute: {{ number_format($r->last_minute_price, 2) }} JOD</option>
                                <option>Child: {{ number_format($r->child_price, 2) }} JOD</option>
                                <option>Child Early: {{ number_format($r->child_early_bird_price, 2) }} JOD</option>
                                <option>Child Late: {{ number_format($r->child_last_minute_price, 2) }} JOD</option>
                                <option>Early Bird From: {{ $r->early_bird_from_date }}</option>
                                <option>Early Bird To: {{ $r->early_bird_to_date }}</option>
                                <option>Last Minute From: {{ $r->last_minute_from_date }}</option>
                                <option>Last Minute To: {{ $r->last_minute_to_date }}</option>
                            </select>
                        </td>

                        {{-- Booked Status --}}
                        <td class="tw-py-4 tw-px-6 tw-align-top tw-text-center tw-whitespace-nowrap">
                            <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-gap-1.5">
                                <span class="tw-text-slate-800 tw-font-bold tw-text-sm">{{ $r->booked_paid }} confirmed</span>
                                @if($r->booked_pending > 0)
                                    <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-rose-50 tw-text-rose-600 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-wide tw-border tw-border-rose-100">
                                        <span class="tw-w-1.5 tw-h-1.5 tw-bg-rose-500 tw-rounded-full tw-animate-pulse"></span>
                                        {{ $r->booked_pending }} Pending
                                    </span>
                                @else
                                    <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-orange-50 tw-text-orange-600 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-wide tw-border tw-border-orange-100">
                                        <span class="tw-w-1.5 tw-h-1.5 tw-bg-orange-500 tw-rounded-full"></span>
                                        0 Pending
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="tw-py-4 tw-px-6 tw-align-top tw-text-right tw-whitespace-nowrap">
                            <div class="tw-flex tw-items-center tw-justify-end tw-gap-2">
                                <a href="mailto:" title="Send Email" class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-slate-50 tw-text-slate-500 hover:tw-bg-orange-50 hover:tw-text-orange-600 tw-transition-all tw-no-underline tw-border tw-border-slate-100 hover:tw-border-orange-200">
                                    <i class="fa fa-envelope-o tw-text-xs"></i>
                                </a>
                                
                                <a href="/admin/guaranteed-departures/{{ $r->id }}/edit" title="Edit" class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-orange-100 hover:tw-border-orange-600">
                                    <i class="fa fa-pencil tw-text-xs"></i>
                                </a>

                                @if($r->booking_id && $r->booking_id > 0)
                                <a href="/admin/bookings/{{ $r->booking_id }}/edit" title="View Booking" class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-orange-100 hover:tw-border-orange-600">
                                    <i class="fa fa-eye tw-text-xs"></i>
                                </a>
                                @endif
                                
                                <form id="gd-delete-form-{{ $r->id }}" method="POST" action="/admin/guaranteed-departures/{{ $r->id }}" class="tw-inline tw-m-0" onsubmit="return confirm('Are you sure you want to delete this departure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-border tw-border-rose-100 hover:tw-border-rose-600 tw-cursor-pointer">
                                        <i class="fa fa-trash-o tw-text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="tw-px-6 tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-300">
                                    <i class="fa fa-calendar-times-o tw-text-3xl"></i>
                                </div>
                                <div>
                                    <p class="tw-text-slate-600 tw-font-bold tw-text-base">No departures found</p>
                                    <p class="tw-text-slate-400 tw-text-xs tw-mt-1">Try adjusting your filters to find specific records.</p>
                                </div>
                                <a href="{{ route('admin.guaranteed-departures.index') }}" class="btn red !tw-text-xs !tw-py-2 !tw-px-4 tw-mt-2">
                                    <i class="fa fa-refresh"></i> Clear Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($departures->hasPages())
        <div class="tw-px-6 tw-py-4 tw-border-t tw-border-slate-100 tw-bg-slate-50/30">
            {{ $departures->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* Pagination Overrides */
    .pagination { display: flex; list-style: none; gap: 6px; justify-content: flex-end; align-items: center; margin: 0; padding: 0; }
    .pagination li { display: inline-block; }
    .pagination li span, .pagination li a {
        display: flex; align-items: center; justify-content: center;
        min-width: 36px; height: 36px; border-radius: 10px; font-size: 13px; font-weight: 700;
        text-decoration: none; border: 1px solid #f1f5f9; background: #fff; color: #475569;
        padding: 0 10px; transition: all 0.2s;
    }
    .pagination li.active span {
        background: var(--brand-primary); color: #fff; border-color: var(--brand-primary);
        box-shadow: 0 4px 10px rgba(249, 115, 22, 0.25);
    }
    .pagination li a:hover { background: #f8fafc; border-color: #e2e8f0; color: var(--brand-primary); }
    .pagination li.disabled span { opacity: 0.4; cursor: not-allowed; }
</style>
@endsection
