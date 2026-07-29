@extends('admin.layouts.app')
@section('title', 'New Guaranteed Departure')
@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">


    <h1 class="tw-text-2xl tw-font-bold tw-text-slate-800 tw-flex tw-items-center tw-gap-2">
        <span class="tw-text-amber-500">✱</span> Add Guaranteed Departure
    </h1>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border tw-border-emerald-200 tw-text-emerald-700 tw-px-6 tw-py-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3 tw-text-sm tw-font-semibold">
        <i class="fa fa-check-circle tw-text-emerald-500"></i> {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.guaranteed-departures.store') }}">
        @csrf

        {{-- Basic Info --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
                <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Basic Information</h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Tour</label>
                    <select name="tour_id" class="tw-w-full" required>
                        <option value="">-- Select Tour --</option>
                        @foreach($tours as $t)
                        <option value="{{ $t->id }}">{{ $t->contents->first()->title ?? 'Tour #'.$t->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Title</label>
                    <input type="text" name="title" value="" placeholder="Departure title" class="tw-w-full" required>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Date</label>
                    <input type="date" name="date" value="" class="tw-w-full" required>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Min to Operate</label>
                    <input type="number" name="min_to_operate" value="5" class="tw-w-full" required>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Max to Operate</label>
                    <input type="number" name="max_to_operate" value="18" class="tw-w-full" required>
                </div>
            </div>
        </div>

        {{-- Pricing --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
                <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Pricing</h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Adult Price</label>
                    <input type="number" name="adult_price" value="0" step="0.01" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Early Bird Price</label>
                    <input type="number" name="early_bird_price" value="0" step="0.01" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Last Minute Price</label>
                    <input type="number" name="last_minute_price" value="0" step="0.01" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Child Price</label>
                    <input type="number" name="child_price" value="0" step="0.01" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Child Early Bird Price</label>
                    <input type="number" name="child_early_bird_price" value="0" step="0.01" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Child Last Minute Price</label>
                    <input type="number" name="child_last_minute_price" value="0" step="0.01" class="tw-w-full">
                </div>
            </div>
        </div>

        {{-- Date Ranges --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
                <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Date Ranges</h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Early Bird From</label>
                    <input type="date" name="early_bird_from_date" value="" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Early Bird To</label>
                    <input type="date" name="early_bird_to_date" value="" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Last Minute From</label>
                    <input type="date" name="last_minute_from_date" value="" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Last Minute To</label>
                    <input type="date" name="last_minute_to_date" value="" class="tw-w-full">
                </div>
            </div>
        </div>

        {{-- Hotel Grade --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
                <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Hotel Grade & Supplements</h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Hotel Grade</label>
                    <select name="hotel_grade" class="tw-w-full">
                        <option value="0">None</option>
                        @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }} Star</option>
                        @endfor
                    </select>
                </div>
                @for($i = 2; $i <= 5; $i++)
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">{{ $i }} Star Supplement</label>
                    <input type="number" name="{{ $i }}_star_supplements" value="0" step="0.01" class="tw-w-full">
                </div>
                @endfor
                @for($i = 1; $i <= 5; $i++)
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">{{ $i }} Star Single Supplement</label>
                    <input type="number" name="{{ $i }}_single_supplement" value="0" step="0.01" class="tw-w-full">
                </div>
                @endfor
            </div>
        </div>

        {{-- Hidden defaults --}}
        <input type="hidden" name="booked_paid" value="0">
        <input type="hidden" name="booked_pending" value="0">
        <input type="hidden" name="booking_id" value="0">
        <input type="hidden" name="status" value="">

        {{-- Save --}}
        <button type="submit" name="save" class="btn blue tw-w-full !tw-py-4 !tw-text-base">
            <i class="fa fa-save"></i> Create Departure
        </button>
    </form>
</div>
@endsection
