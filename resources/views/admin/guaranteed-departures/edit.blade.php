@extends('admin.layouts.app')
@section('title', 'Edit Guaranteed Departure')
@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">


    <h1 class="tw-text-2xl tw-font-bold tw-text-slate-800 tw-flex tw-items-center tw-gap-2">
        <span class="tw-text-amber-500">✱</span> Edit Guaranteed Departure
    </h1>

    @if(session('success'))
    <div class="tw-bg-orange-50 tw-border tw-border-orange-200 tw-text-orange-700 tw-px-6 tw-py-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3 tw-text-sm tw-font-semibold">
        <i class="fa fa-check-circle tw-text-orange-500"></i> {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.guaranteed-departures.update', $departure->id) }}">
        @csrf
        @method('PUT')

        {{-- Basic Info --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
                <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Basic Information</h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Tour <span class="tw-text-rose-500">*</span></label>
                    <div>
                        <select name="tour_id" class="tw-w-full @error('tour_id') !tw-border-rose-400 !tw-ring-2 !tw-ring-rose-100 @enderror">
                            @foreach($tours as $t)
                            <option value="{{ $t->id }}" {{ old('tour_id', $departure->tour_id) == $t->id ? 'selected' : '' }}>{{ $t->contents->first()->title ?? 'Tour #'.$t->id }}</option>
                            @endforeach
                        </select>
                        @error('tour_id')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Title <span class="tw-text-rose-500">*</span></label>
                    <div>
                        <input type="text" name="title" value="{{ old('title', $departure->title) }}" class="tw-w-full @error('title') !tw-border-rose-400 !tw-ring-2 !tw-ring-rose-100 @enderror">
                        @error('title')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Date <span class="tw-text-rose-500">*</span></label>
                    <div>
                        <input type="date" name="date" value="{{ old('date', $departure->date) }}" class="tw-w-full @error('date') !tw-border-rose-400 !tw-ring-2 !tw-ring-rose-100 @enderror">
                        @error('date')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Min to Operate</label>
                    <div>
                        <input type="number" name="min_to_operate" value="{{ old('min_to_operate', $departure->min_to_operate) }}" class="tw-w-full @error('min_to_operate') !tw-border-rose-400 !tw-ring-2 !tw-ring-rose-100 @enderror">
                        @error('min_to_operate')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Max to Operate</label>
                    <div>
                        <input type="number" name="max_to_operate" value="{{ old('max_to_operate', $departure->max_to_operate) }}" class="tw-w-full @error('max_to_operate') !tw-border-rose-400 !tw-ring-2 !tw-ring-rose-100 @enderror">
                        @error('max_to_operate')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Status</label>
                    <div>
                        <select name="status" class="tw-w-full">
                            <option value="" {{ old('status', $departure->status) == '' ? 'selected' : '' }}>Active</option>
                            <option value="com" {{ old('status', $departure->status) == 'com' ? 'selected' : '' }}>Completed</option>
                            <option value="can" {{ old('status', $departure->status) == 'can' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Pricing --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
                <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Pricing</h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Adult Price</label>
                    <div>
                        <input type="number" name="adult_price" value="{{ old('adult_price', $departure->adult_price) }}" step="0.01" class="tw-w-full @error('adult_price') !tw-border-rose-400 @enderror">
                        @error('adult_price')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Early Bird Price</label>
                    <div>
                        <input type="number" name="early_bird_price" value="{{ old('early_bird_price', $departure->early_bird_price) }}" step="0.01" class="tw-w-full @error('early_bird_price') !tw-border-rose-400 @enderror">
                        @error('early_bird_price')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Last Minute Price</label>
                    <div>
                        <input type="number" name="last_minute_price" value="{{ old('last_minute_price', $departure->last_minute_price) }}" step="0.01" class="tw-w-full @error('last_minute_price') !tw-border-rose-400 @enderror">
                        @error('last_minute_price')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Child Price</label>
                    <div>
                        <input type="number" name="child_price" value="{{ old('child_price', $departure->child_price) }}" step="0.01" class="tw-w-full @error('child_price') !tw-border-rose-400 @enderror">
                        @error('child_price')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Child Early Bird Price</label>
                    <div>
                        <input type="number" name="child_early_bird_price" value="{{ old('child_early_bird_price', $departure->child_early_bird_price) }}" step="0.01" class="tw-w-full @error('child_early_bird_price') !tw-border-rose-400 @enderror">
                        @error('child_early_bird_price')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-start tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-pt-2">Child Last Minute Price</label>
                    <div>
                        <input type="number" name="child_last_minute_price" value="{{ old('child_last_minute_price', $departure->child_last_minute_price) }}" step="0.01" class="tw-w-full @error('child_last_minute_price') !tw-border-rose-400 @enderror">
                        @error('child_last_minute_price')<p class="tw-text-rose-500 tw-text-xs tw-font-semibold tw-mt-1.5 tw-m-0"><i class="fa fa-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Early Bird & Last Minute Date Ranges --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
                <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Date Ranges</h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Early Bird From</label>
                    <input type="date" name="early_bird_from_date" value="{{ $departure->early_bird_from_date }}" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Early Bird To</label>
                    <input type="date" name="early_bird_to_date" value="{{ $departure->early_bird_to_date }}" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Last Minute From</label>
                    <input type="date" name="last_minute_from_date" value="{{ $departure->last_minute_from_date }}" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Last Minute To</label>
                    <input type="date" name="last_minute_to_date" value="{{ $departure->last_minute_to_date }}" class="tw-w-full">
                </div>
            </div>
        </div>

        {{-- Hotel Grade & Supplements --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
                <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Hotel Grade & Supplements</h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Hotel Grade</label>
                    <select name="hotel_grade" id="hotel_grade" onchange="set_supplement_fees()" class="tw-w-full">
                        <option value="0" {{ $departure->hotel_grade == 0 ? 'selected' : '' }}>None</option>
                        @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ $departure->hotel_grade == $i ? 'selected' : '' }}>{{ $i }} Star</option>
                        @endfor
                    </select>
                </div>
                @for($i = 2; $i <= 5; $i++)
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">{{ $i }} Star Supplement</label>
                    <input type="number" name="{{ $i }}_star_supplements" id="hotel_{{ $i }}" value="{{ $departure->{$i.'_star_supplements'} ?? 0 }}" step="0.01" class="tw-w-full">
                </div>
                @endfor
                @for($i = 1; $i <= 5; $i++)
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">{{ $i }} Star Single Supplement</label>
                    <input type="number" name="{{ $i }}_single_supplement" id="single_supplement_fee_{{ $i }}" value="{{ $departure->{$i.'_single_supplement'} ?? 0 }}" step="0.01" class="tw-w-full">
                </div>
                @endfor
            </div>
        </div>

        {{-- Booked info (readonly display) --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
                <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Booking Info</h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Booked Paid</label>
                    <input type="number" name="booked_paid" value="{{ $departure->booked_paid }}" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Booked Pending</label>
                    <input type="number" name="booked_pending" value="{{ $departure->booked_pending }}" class="tw-w-full">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                    <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Booking ID</label>
                    <input type="number" name="booking_id" value="{{ $departure->booking_id }}" class="tw-w-full">
                </div>
            </div>
        </div>

        {{-- Save --}}
        <button type="submit" name="save" class="btn orange tw-w-full !tw-py-4 !tw-text-base">
            <i class="fa fa-save"></i> Save Changes
        </button>
    </form>

    {{-- Departure Bookings Table --}}
    @if($departure->departureBookings && $departure->departureBookings->count() > 0)
    <div class="box !tw-p-0 !tw-overflow-hidden tw-mt-6">
        <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
            <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Departure Bookings ({{ $departure->departureBookings->count() }})</h3>
        </div>
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse tw-text-sm">
                <thead>
                    <tr class="tw-bg-slate-50 tw-border-b tw-border-slate-200">
                        <th class="tw-py-3 tw-px-4 tw-text-xs tw-font-bold tw-text-slate-600">ID</th>
                        <th class="tw-py-3 tw-px-4 tw-text-xs tw-font-bold tw-text-slate-600">Adults</th>
                        <th class="tw-py-3 tw-px-4 tw-text-xs tw-font-bold tw-text-slate-600">Children</th>
                        <th class="tw-py-3 tw-px-4 tw-text-xs tw-font-bold tw-text-slate-600">Total</th>
                        <th class="tw-py-3 tw-px-4 tw-text-xs tw-font-bold tw-text-slate-600">Status</th>
                        <th class="tw-py-3 tw-px-4 tw-text-xs tw-font-bold tw-text-slate-600">Note</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-100">
                    @foreach($departure->departureBookings as $db)
                    <tr class="hover:tw-bg-slate-50/50">
                        <td class="tw-py-3 tw-px-4 tw-font-bold">{{ $db->id }}</td>
                        <td class="tw-py-3 tw-px-4">{{ $db->adult }}</td>
                        <td class="tw-py-3 tw-px-4">{{ $db->child }}</td>
                        <td class="tw-py-3 tw-px-4 tw-font-bold">{{ $db->counted_persons }}</td>
                        <td class="tw-py-3 tw-px-4">
                            @if($db->trip_status == 'con')
                            <span class="tw-text-orange-600 tw-font-bold tw-text-xs">Confirmed</span>
                            @elseif($db->trip_status == 'can')
                            <span class="tw-text-red-600 tw-font-bold tw-text-xs">Cancelled</span>
                            @else
                            <span class="tw-text-amber-600 tw-font-bold tw-text-xs">Pending</span>
                            @endif
                        </td>
                        <td class="tw-py-3 tw-px-4 tw-text-xs tw-text-slate-500">{{ $db->note }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
function set_supplement_fees(){
    var base_grade = document.getElementById('hotel_grade').value;
    for (var i = 1; i < 6; i++) {
        var hotelEl = document.getElementById('hotel_' + i);
        var singleEl = document.getElementById('single_supplement_fee_' + i);
        if (hotelEl) {
            if (base_grade == 0 || base_grade >= i) {
                hotelEl.disabled = true;
                hotelEl.style.backgroundColor = '#f1f5f9';
                hotelEl.style.color = '#cbd5e1';
            } else {
                hotelEl.disabled = false;
                hotelEl.style.backgroundColor = '';
                hotelEl.style.color = '';
            }
        }
        if (singleEl) {
            if (base_grade == 0 || base_grade > i) {
                singleEl.disabled = true;
                singleEl.style.backgroundColor = '#f1f5f9';
                singleEl.style.color = '#cbd5e1';
            } else if (base_grade == i) {
                singleEl.disabled = false;
                singleEl.style.backgroundColor = '';
                singleEl.style.color = '';
            } else {
                singleEl.disabled = false;
                singleEl.style.backgroundColor = '';
                singleEl.style.color = '';
            }
        }
    }
}
document.addEventListener('DOMContentLoaded', function(){ set_supplement_fees(); });
</script>
@endsection
