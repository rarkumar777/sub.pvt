@extends('admin.layouts.app')
@section('title', 'Admin | Rooming List')

@section('content')
<div class="tw-bg-slate-50 tw-min-h-screen tw-p-2 lg:tw-p-6">
    {{-- Main Container --}}
    <div class="tw-max-w-6xl tw-mx-auto">
        
        {{-- Header & Actions --}}
        <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-items-start md:tw-items-center tw-gap-6 tw-mb-8">
            <div class="tw-flex tw-items-center tw-gap-4">
                <div class="tw-bg-indigo-600 tw-p-3 tw-rounded-2xl tw-shadow-lg tw-shadow-indigo-200">
                    <svg class="tw-w-8 tw-h-8 tw-text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2-2 0 00-2-2H7a2-2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="tw-text-2xl tw-font-black tw-text-slate-900 tw-tracking-tight tw-m-0">Rooming List <span class="tw-text-indigo-600">#{{ $booking->id }}</span></h1>
                    <div class="tw-flex tw-items-center tw-gap-2 tw-mt-1">
                        <span class="tw-flex tw-items-center tw-gap-1 tw-text-[10px] tw-font-bold tw-bg-emerald-100 tw-text-emerald-700 tw-px-2 tw-py-0.5 tw-rounded-full tw-uppercase tw-tracking-wider">Official Document</span>
                        <span class="tw-text-slate-400 tw-text-xs tw-font-medium">Passenger Manifest • {{ date('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="tw-flex tw-items-center tw-gap-3">
                <div class="tw-bg-white tw-border tw-border-slate-200 tw-rounded-xl tw-p-1.5 tw-shadow-sm tw-flex tw-items-center tw-gap-1">
                    <select class="tw-bg-transparent tw-text-xs tw-font-bold tw-text-slate-600 tw-px-3 tw-py-1 tw-outline-none tw-border-r tw-border-slate-100 cursor-pointer">
                        <option>PDF Report</option>
                        <option>Excel Sheet</option>
                    </select>
                    <button class="tw-p-2 tw-text-slate-400 hover:tw-text-indigo-600 tw-transition-colors">
                        <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    </button>
                </div>
                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="tw-bg-slate-900 hover:tw-bg-black tw-text-white tw-text-xs tw-font-bold tw-px-5 tw-py-3 tw-rounded-xl tw-shadow-sm tw-shadow-slate-200 tw-flex tw-items-center tw-gap-2 tw-transition-all active:tw-scale-95">
                    <i class="fa fa-pencil"></i> EDIT BOOKING
                </a>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="tw-bg-white tw-border tw-border-slate-200 tw-rounded-2xl tw-p-4 tw-mb-8 tw-shadow-sm tw-flex tw-flex-wrap tw-gap-4 tw-items-center">
            <div class="tw-flex-1 tw-min-w-[150px]">
                <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-1.5">Vender / Agency</label>
                <div class="tw-relative">
                    <select class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-px-3 tw-py-2 tw-text-xs tw-font-semibold tw-text-slate-700 tw-appearance-none tw-outline-none focus:tw-ring-2 focus:tw-ring-indigo-500/20">
                        <option>All Agencies</option>
                        <option>{{ $booking->user->company ?? 'Pv Travels' }}</option>
                    </select>
                    <div class="tw-absolute tw-right-3 tw-top-1/2 tw--translate-y-1/2 tw-pointer-events-none tw-text-slate-400">
                        <i class="fa fa-chevron-down tw-text-[10px]"></i>
                    </div>
                </div>
            </div>
            <div class="tw-w-32">
                <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-1.5">Itinerary View</label>
                <select class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-px-3 tw-py-2 tw-text-xs tw-font-semibold tw-text-slate-700 tw-outline-none">
                    <option>Compact</option>
                    <option>Full Show</option>
                </select>
            </div>
            <div class="tw-w-32">
                <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-1.5">Hotel Status</label>
                <select class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-px-3 tw-py-2 tw-text-xs tw-font-semibold tw-text-slate-700 tw-outline-none">
                    <option>Detailed</option>
                    <option>Simplified</option>
                </select>
            </div>
        </div>

        {{-- Info Grid --}}
        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-6 tw-mb-8">
            {{-- Card 1 --}}
            <div class="tw-bg-white tw-border tw-border-slate-200 tw-rounded-2xl tw-p-5 tw-shadow-sm group hover:tw-border-indigo-500 tw-transition-colors">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-3">
                    <div class="tw-w-10 tw-h-10 tw-bg-slate-50 group-hover:tw-bg-indigo-50 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-text-slate-400 group-hover:tw-text-indigo-600 tw-transition-colors">
                        <i class="fa fa-building-o tw-text-lg"></i>
                    </div>
                </div>
                <div class="tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Agency Name</div>
                <div class="tw-text-sm tw-font-black tw-text-slate-800 tw-mt-1">{{ $booking->user->company ?? 'Pv Travels' }}</div>
            </div>

            {{-- Card 2 --}}
            <div class="tw-bg-white tw-border tw-border-slate-200 tw-rounded-2xl tw-p-5 tw-shadow-sm group hover:tw-border-indigo-500 tw-transition-colors">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-3">
                    <div class="tw-w-10 tw-h-10 tw-bg-slate-50 group-hover:tw-bg-indigo-50 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-text-slate-400 group-hover:tw-text-indigo-600 tw-transition-colors">
                        <i class="fa fa-users tw-text-lg"></i>
                    </div>
                </div>
                <div class="tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Travel Capacity</div>
                <div class="tw-text-sm tw-font-black tw-text-slate-800 tw-mt-1">{{ intval($booking->adult) + intval($booking->child) + intval($booking->infant) }} Total PAX</div>
            </div>

            {{-- Card 3 --}}
            <div class="tw-bg-white tw-border tw-border-slate-200 tw-rounded-2xl tw-p-5 tw-shadow-sm group hover:tw-border-emerald-500 tw-transition-colors">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-3">
                    <div class="tw-w-10 tw-h-10 tw-bg-slate-50 group-hover:tw-bg-emerald-50 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-text-slate-400 group-hover:tw-text-emerald-600 tw-transition-colors">
                        <i class="fa fa-plane tw-text-lg"></i>
                    </div>
                </div>
                <div class="tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Arrival Check-in</div>
                <div class="tw-text-sm tw-font-black tw-text-slate-800 tw-mt-1">{{ $booking->travel_date }}</div>
            </div>

            {{-- Card 4 --}}
            <div class="tw-bg-white tw-border tw-border-slate-200 tw-rounded-2xl tw-p-5 tw-shadow-sm group hover:tw-border-rose-500 tw-transition-colors">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-3">
                    <div class="tw-w-10 tw-h-10 tw-bg-slate-50 group-hover:tw-bg-rose-50 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-text-slate-400 group-hover:tw-text-rose-600 tw-transition-colors">
                        <i class="fa fa-calendar tw-text-lg"></i>
                    </div>
                </div>
                <div class="tw-text-[10px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Departure Check-out</div>
                <div class="tw-text-sm tw-font-black tw-text-slate-800 tw-mt-1">
                    {{ $booking->travel_date && $booking->travel_date != '0000-00-00' && $booking->nights ? date('Y-m-d', strtotime($booking->travel_date . ' + ' . $booking->nights . ' days')) : 'Pending' }}
                </div>
            </div>
        </div>

        {{-- Travelers Roster --}}
        <div class="tw-bg-white tw-border tw-border-slate-200 tw-rounded-3xl tw-shadow-sm tw-overflow-hidden">
            <div class="tw-bg-slate-900 tw-px-8 tw-py-5 tw-flex tw-justify-between tw-items-center">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <div class="tw-w-2 tw-h-6 tw-bg-indigo-500 tw-rounded-full"></div>
                    <h2 class="tw-text-[13px] tw-font-black tw-text-white tw-uppercase tw-tracking-[0.2em] tw-m-0">Travelers Roster</h2>
                </div>
                <div class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">
                    {{ count($booking->travelers) }} Entries Localized
                </div>
            </div>
            
            <div class="tw-overflow-x-auto">
                <table class="tw-w-full tw-text-left">
                    <thead>
                        <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                            <th class="tw-py-4 tw-px-8 tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Full Name</th>
                            <th class="tw-py-4 tw-px-8 tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Passport Info</th>
                            <th class="tw-py-4 tw-px-8 tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Validity</th>
                            <th class="tw-py-4 tw-px-8 tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Birth Details</th>
                            <th class="tw-py-4 tw-px-8 tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Nationality</th>
                        </tr>
                    </thead>
                    <tbody class="tw-divide-y tw-divide-slate-50">
                        @forelse($booking->travelers as $t)
                        <tr class="hover:tw-bg-slate-50/40 tw-transition-colors group">
                            <td class="tw-py-5 tw-px-8">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-8 tw-h-8 tw-bg-indigo-100 tw-text-indigo-600 tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-text-xs tw-font-bold">
                                        {{ substr($t->name ?? $t->first_name, 0, 1) }}
                                    </div>
                                    <div class="tw-text-sm tw-font-extrabold tw-text-slate-900">{{ trim($t->name ?? ($t->first_name . ' ' . $t->last_name)) }}</div>
                                </div>
                            </td>
                            <td class="tw-py-5 tw-px-8">
                                <div class="tw-flex tw-flex-col">
                                    <span class="tw-text-[13px] tw-font-bold tw-text-slate-700 tw-flex tw-items-center tw-gap-2">
                                        <i class="fa fa-id-card-o tw-text-slate-300"></i>
                                        {{ $t->passport_number ?: '—' }}
                                    </span>
                                </div>
                            </td>
                            <td class="tw-py-5 tw-px-8">
                                <div class="tw-flex tw-flex-col tw-gap-1">
                                    <div class="tw-text-[11px] tw-text-slate-500 tw-flex tw-items-center tw-gap-2 font-medium">
                                        <span class="tw-w-1.5 tw-h-1.5 tw-bg-emerald-400 tw-rounded-full"></span>
                                        {{ $t->passport_issue ?: 'N/A' }}
                                    </div>
                                    <div class="tw-text-[11px] tw-text-rose-500 tw-flex tw-items-center tw-gap-2 font-medium">
                                        <span class="tw-w-1.5 tw-h-1.5 tw-bg-rose-400 tw-rounded-full"></span>
                                        {{ $t->passport_expire ?: 'N/A' }}
                                    </div>
                                </div>
                            </td>
                            <td class="tw-py-5 tw-px-8">
                                <span class="tw-text-[12px] tw-font-bold tw-text-slate-600">{{ $t->birth_date ?? $t->dob ?: '—' }}</span>
                            </td>
                            <td class="tw-py-5 tw-px-8">
                                <div class="tw-inline-flex tw-items-center tw-gap-2 tw-bg-slate-100 tw-px-3 tw-py-1 tw-rounded-full">
                                    <span class="tw-text-[11px] tw-font-black tw-text-slate-600 tw-uppercase">{{ $t->nationality ?: '—' }}</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="tw-py-20 tw-text-center">
                                <div class="tw-inline-flex tw-w-16 tw-h-16 tw-bg-slate-50 tw-rounded-full tw-items-center tw-justify-center tw-mb-4">
                                    <i class="fa fa-user-times tw-text-2xl tw-text-slate-200"></i>
                                </div>
                                <div class="tw-text-slate-400 tw-text-sm tw-font-black tw-uppercase tw-tracking-widest">No entries found</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer Brand --}}
        <div class="tw-mt-10 tw-pb-10 tw-flex tw-justify-center">
            <img src="{{ asset('assets/frontend/images/logo.png') }}" style="height:40px; opacity: 0.3; filter: grayscale(1);" alt="Logo" />
        </div>
    </div>
</div>
@endsection
