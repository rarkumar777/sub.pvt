@extends('admin.layouts.app')
@section('title', 'Admin | Booking Travelers')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumb --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
        <a href="{{ route('admin.bookings.index') }}" class="tw-text-indigo-600 tw-font-semibold tw-no-underline hover:tw-text-indigo-800 tw-transition-colors">
            <i class="fa fa-calendar tw-mr-1"></i> Bookings
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <span class="tw-text-slate-500 tw-font-semibold">Travelers — Booking #{{ $booking->id }}</span>
    </div>

    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Travelers</h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Booking #{{ $booking->id }}</p>
        </div>
        <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn indigo"><i class="fa fa-arrow-left"></i> Back to Booking</a>
    </div>

    {{-- Add Traveler Form --}}
    <div class="box">
        <form method="POST" action="{{ route('admin.bookings.travelers.store', $booking->id) }}" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-5 tw-gap-4 tw-items-end">
            @csrf
            <div>
                <label class="tw-text-xs tw-font-semibold tw-text-slate-500 tw-mb-1.5 tw-block">First Name *</label>
                <input type="text" name="first_name" placeholder="First Name" required>
            </div>
            <div>
                <label class="tw-text-xs tw-font-semibold tw-text-slate-500 tw-mb-1.5 tw-block">Last Name</label>
                <input type="text" name="last_name" placeholder="Last Name">
            </div>
            <div>
                <label class="tw-text-xs tw-font-semibold tw-text-slate-500 tw-mb-1.5 tw-block">Passport #</label>
                <input type="text" name="passport_number" placeholder="Passport #">
            </div>
            <div>
                <label class="tw-text-xs tw-font-semibold tw-text-slate-500 tw-mb-1.5 tw-block">Nationality</label>
                <input type="text" name="nationality" placeholder="Nationality">
            </div>
            <div>
                <button type="submit" class="btn indigo tw-w-full"><i class="fa fa-plus"></i> Add</button>
            </div>
        </form>
    </div>

    {{-- Travelers Table --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-w-12">#</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Passport</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Nationality</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Gender</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($booking->travelers as $i => $traveler)
                    <tr class="hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-400">{{ $i + 1 }}</td>
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-font-bold tw-text-slate-900">{{ $traveler->first_name }} {{ $traveler->last_name }}</td>
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-600">{{ $traveler->passport_number ?? '—' }}</td>
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-600">{{ $traveler->nationality ?? '—' }}</td>
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-600">{{ $traveler->gender ?? '—' }}</td>
                        <td class="tw-py-4 tw-px-6 tw-text-right">
                            <a class="tw-w-9 tw-h-9 tw-inline-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-no-underline tw-cursor-pointer" title="Remove">
                                <i class="fa fa-trash tw-text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="tw-py-16 tw-text-center tw-text-slate-400 tw-text-sm tw-font-bold">No travelers added yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
