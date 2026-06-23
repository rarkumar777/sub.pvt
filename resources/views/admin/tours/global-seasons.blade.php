@extends('admin.layouts.app')

@section('title', 'Admin | Global Seasons')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">
    {{-- Navigation Tabs --}}
    @include('admin.tours._nav')

    {{-- Header Section --}}
    <div class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-600 tw-transition-colors tw-no-underline">Dashboard</a>
                <span class="tw-opacity-50">/</span>
                <span class="tw-text-slate-600">Tours Settings</span>
            </div>
            <h1 class="tw-text-3xl tw-font-black tw-text-slate-900 tw-flex tw-items-center tw-gap-3">
                <div class="tw-w-10 tw-h-10 tw-bg-orange-600 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-orange-200">
                    <i class="fa fa-calendar-check-o tw-text-white tw-text-base"></i>
                </div>
                Global Seasons
            </h1>
            <p class="subtitle">Define seasonal periods for tour pricing and availability constraints.</p>
        </div>
        <div>
            <a href="#add_new" class="btn orange tw-shadow-premium">
                <i class="fa fa-plus-circle"></i> Add New Season
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="tw-bg-orange-50 tw-border-l-4 tw-border-orange-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3 tw-shadow-sm">
        <i class="fa fa-check-circle tw-text-orange-500 tw-text-xl"></i>
        <p class="tw-text-orange-800 tw-font-bold tw-text-sm">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Seasons Listing Card --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/80 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Period Configuration</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Classification</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($seasons as $season)
                    <tr class="hover:tw-bg-orange-50/30 tw-transition-colors">
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-flex tw-items-center tw-gap-4">
                                <div class="tw-w-12 tw-h-12 tw-rounded-xl tw-bg-orange-50 tw-flex tw-items-center tw-justify-center tw-text-orange-500 tw-border tw-border-orange-100">
                                    <i class="fa fa-calendar-o"></i>
                                </div>
                                <div class="tw-flex tw-flex-col tw-gap-1">
                                    <span class="tw-text-sm tw-font-black tw-text-slate-900">{{ date('M d, Y', strtotime($season->from_date)) }}</span>
                                    <div class="tw-flex tw-items-center tw-gap-2">
                                        <div class="tw-w-4 tw-h-[2px] tw-bg-slate-200"></div>
                                        <span class="tw-text-xs tw-font-bold tw-text-slate-500">{{ date('M d, Y', strtotime($season->to_date)) }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="tw-py-4 tw-px-6">
                            @if($season->type == 'H')
                            <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-amber-50 tw-text-amber-600 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-border tw-border-amber-100">
                                <span class="tw-w-1.5 tw-h-1.5 tw-bg-amber-500 tw-rounded-full"></span> High Season
                            </span>
                            @elseif($season->type == 'L')
                            <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-orange-50 tw-text-orange-600 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-border tw-border-orange-100">
                                <span class="tw-w-1.5 tw-h-1.5 tw-bg-orange-500 tw-rounded-full"></span> Low Season
                            </span>
                            @else
                            <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-slate-50 tw-text-slate-600 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-border tw-border-slate-100">
                                <span class="tw-w-1.5 tw-h-1.5 tw-bg-slate-400 tw-rounded-full"></span> {{ $season->type }}
                            </span>
                            @endif
                        </td>
                        <td class="tw-py-4 tw-px-6 tw-text-right">
                            <a href="{{ route('admin.tours-seasons.delete', $season->id) }}" onclick="return confirm('Delete this seasonal period?');" class="tw-w-9 tw-h-9 tw-ml-auto tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-border tw-border-rose-100 hover:tw-border-rose-600" title="Delete Period">
                                <i class="fa fa-trash-o tw-text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-300">
                                    <i class="fa fa-calendar-times-o tw-text-3xl"></i>
                                </div>
                                <div>
                                    <p class="tw-text-slate-600 tw-font-bold tw-text-base">No seasons defined</p>
                                    <p class="tw-text-slate-400 tw-text-xs tw-mt-1">Add your first high or low season period to get started.</p>
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

{{-- Add New Season Modal --}}
<div id="add_new" class="modal">
    <div class="tw-w-full tw-max-w-md !tw-p-8 sm:!tw-p-10 !tw-rounded-3xl tw-bg-white/95 tw-backdrop-blur-xl tw-border tw-border-slate-100 tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-mb-8">
            <div class="tw-flex tw-items-center tw-gap-4">
                <span class="tw-w-12 tw-h-12 tw-bg-orange-50 tw-text-orange-600 tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-shadow-sm tw-border tw-border-orange-100">
                    <i class="fa fa-plus-circle tw-text-xl"></i>
                </span>
                <div>
                    <h3 class="tw-text-2xl tw-font-black tw-text-slate-900 tw-mb-0">New Season</h3>
                </div>
            </div>
            <a href="#close" title="Close" class="tw-w-10 tw-h-10 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-slate-50 tw-text-slate-400 hover:tw-bg-rose-50 hover:tw-text-rose-500 tw-transition-all tw-no-underline">
                <i class="fa fa-times tw-text-lg"></i>
            </a>
        </div>
        
        <form method="POST" action="{{ route('admin.tours-seasons') }}" class="tw-flex tw-flex-col tw-gap-5">
            @csrf
            <div>
                <label class="tw-text-sm tw-font-bold tw-text-slate-700 tw-mb-2">Start Date</label>
                <div class="tw-relative">
                    <i class="fa fa-calendar tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400"></i>
                    <input type="text" name="date_from" class="datepicker !tw-pl-11 tw-w-full tw-bg-slate-50 focus:tw-bg-white" placeholder="YYYY-MM-DD" required>
                </div>
            </div>
            
            <div>
                <label class="tw-text-sm tw-font-bold tw-text-slate-700 tw-mb-2">End Date</label>
                <div class="tw-relative">
                    <i class="fa fa-calendar tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400"></i>
                    <input type="text" name="date_to" class="datepicker !tw-pl-11 tw-w-full tw-bg-slate-50 focus:tw-bg-white" placeholder="YYYY-MM-DD" required>
                </div>
            </div>
            
            <div>
                <label class="tw-text-sm tw-font-bold tw-text-slate-700 tw-mb-2">Season Classification</label>
                <select name="type" required class="tw-w-full tw-bg-slate-50 focus:tw-bg-white">
                    <option value="H" selected>High Season (Peak)</option>
                    <option value="L">Low Season (Off-peak)</option>
                </select>
            </div>
            
            <div class="tw-mt-8 tw-pt-6 tw-border-t tw-border-slate-100 tw-flex tw-items-center tw-justify-end tw-gap-4">
                <a href="#close" class="btn tw-bg-slate-100 tw-text-slate-600 hover:tw-bg-slate-200 !tw-px-6 !tw-py-3">Cancel</a>
                <button type="submit" class="btn orange !tw-px-8 !tw-py-3">
                    <i class="fa fa-check"></i> <span>Save Period</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
