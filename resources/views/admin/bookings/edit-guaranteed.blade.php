@extends('admin.layouts.app')
@section('title', 'Edit Guaranteed Booking')
@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">

    {{-- Header --}}
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
            <i class="fa fa-edit tw-text-orange-500 tw-mr-2"></i> Edit {{ $tourTitle }}
        </h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Manage guaranteed departure booking details</p>
    </div>

    @if(session('success'))
    <div class="tw-flex tw-items-center tw-gap-3 tw-bg-orange-50 tw-border tw-border-orange-200 tw-text-orange-700 tw-px-6 tw-py-4 tw-rounded-2xl tw-font-semibold tw-text-sm">
        <i class="fa fa-check-circle tw-text-orange-500 tw-text-lg"></i> {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.bookings.guaranteed.edit', $booking->id) }}">
    @csrf

    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-2 tw-gap-6">
        {{-- LEFT COLUMN --}}
        <div class="tw-flex tw-flex-col tw-gap-6">
            {{-- Date Section --}}
            <div class="box !tw-mb-0">
                <div class="tw-flex tw-items-center tw-gap-3 tw-mb-6">
                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-orange-50 tw-flex tw-items-center tw-justify-center tw-text-orange-500">
                        <i class="fa fa-calendar tw-text-lg"></i>
                    </div>
                    <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Date</h3>
                </div>
                <div class="tw-grid tw-grid-cols-3 tw-gap-4">
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Travel Date</label>
                        <input type="text" name="date" value="{{ $gd->date ?? $booking->travel_date }}" class="datepicker" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Days</label>
                        <input type="number" value="{{ $booking->days }}" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Nights</label>
                        <input type="number" value="{{ $booking->nights }}" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                </div>
            </div>

            {{-- General Section --}}
            <div class="box !tw-mb-0">
                <div class="tw-flex tw-items-center tw-gap-3 tw-mb-6">
                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-orange-50 tw-flex tw-items-center tw-justify-center tw-text-orange-500">
                        <i class="fa fa-cog tw-text-lg"></i>
                    </div>
                    <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">General</h3>
                </div>
                <div class="tw-grid tw-grid-cols-1 tw-gap-5">
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Select Accommodate</label>
                        <select disabled style="background-color:#f8fafc !important;">
                            <option value="0" {{ ($gd->hotel_grade ?? $booking->hotel_grade) == 0 ? 'selected' : '' }}>None</option>
                            <option value="1" {{ ($gd->hotel_grade ?? $booking->hotel_grade) == 1 ? 'selected' : '' }}>1 Star</option>
                            <option value="2" {{ ($gd->hotel_grade ?? $booking->hotel_grade) == 2 ? 'selected' : '' }}>2 Star</option>
                            <option value="3" {{ ($gd->hotel_grade ?? $booking->hotel_grade) == 3 ? 'selected' : '' }}>3 Star</option>
                            <option value="4" {{ ($gd->hotel_grade ?? $booking->hotel_grade) == 4 ? 'selected' : '' }}>4 Star</option>
                            <option value="5" {{ ($gd->hotel_grade ?? $booking->hotel_grade) == 5 ? 'selected' : '' }}>5 Star</option>
                        </select>
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Start Country</label>
                        <select disabled style="background-color:#f8fafc !important;">
                            @foreach($countries as $cid => $cname)
                            <option value="{{ $cid }}" {{ $booking->start_country == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Notes Section --}}
            <div class="box !tw-mb-0">
                <div class="tw-flex tw-items-center tw-gap-3 tw-mb-6">
                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-amber-50 tw-flex tw-items-center tw-justify-center tw-text-amber-500">
                        <i class="fa fa-sticky-note-o tw-text-lg"></i>
                    </div>
                    <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Notes</h3>
                </div>
                <textarea name="notes" style="width:100%; height:80px !important; min-height:80px;">{{ $booking->note }}</textarea>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="tw-flex tw-flex-col tw-gap-6">
            {{-- Travelers Section --}}
            <div class="box !tw-mb-0">
                <div class="tw-flex tw-items-center tw-gap-3 tw-mb-6">
                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-orange-50 tw-flex tw-items-center tw-justify-center tw-text-orange-500">
                        <i class="fa fa-users tw-text-lg"></i>
                    </div>
                    <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Travelers</h3>
                </div>
                <div class="tw-grid tw-grid-cols-3 tw-gap-4">
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Adult</label>
                        <input type="number" value="{{ $booking->adult }}" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Child</label>
                        <input type="number" value="{{ $booking->child }}" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Infant</label>
                        <input type="number" value="{{ $booking->infant }}" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                </div>
            </div>

            {{-- Hotel Rooms Section --}}
            <div class="box !tw-mb-0">
                <div class="tw-flex tw-items-center tw-gap-3 tw-mb-6">
                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-orange-50 tw-flex tw-items-center tw-justify-center tw-text-orange-500">
                        <i class="fa fa-bed tw-text-lg"></i>
                    </div>
                    <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Hotel Rooms</h3>
                </div>
                <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Single</label>
                        <input type="number" value="{{ $booking->room_single }}" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Double</label>
                        <input type="number" value="{{ $booking->rooms_double }}" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Twin</label>
                        <input type="number" value="{{ $booking->rooms_twin }}" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Triple</label>
                        <input type="number" value="{{ $booking->rooms_triple }}" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-1.5">
                        <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">Quad</label>
                        <input type="number" value="{{ $booking->rooms_quad }}" disabled style="background-color:#f8fafc !important; padding:12px 18px !important;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Section --}}
    <div class="tw-mt-8">
        <div class="tw-flex tw-items-center tw-gap-2 tw-mb-6">
            <span class="btn grey tw-px-6 tw-py-3 tab_switch tw-cursor-pointer tw-text-sm" data-tab="#itinerary-tab" style="border-radius:14px;">
                <i class="fa fa-map-o tw-mr-1"></i> Itinerary
            </span>
            <span class="btn orange tw-px-6 tw-py-3 tab_switch tw-cursor-pointer tw-text-sm" data-tab="#booked-tab" style="border-radius:14px;">
                <i class="fa fa-calendar-check-o tw-mr-1"></i> Booked
            </span>
            <span class="btn orange tw-px-6 tw-py-3 tab_switch tw-cursor-pointer tw-text-sm" data-tab="#travelers-tab" style="border-radius:14px;">
                <i class="fa fa-users tw-mr-1"></i> Travelers
            </span>
        </div>

        {{-- Itinerary Tab --}}
        <div class="active-tab box !tw-mb-0" id="itinerary-tab">
            <div class="tw-flex tw-items-center tw-justify-between tw-mb-6">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-orange-50 tw-flex tw-items-center tw-justify-center tw-text-orange-500">
                        <i class="fa fa-map-o tw-text-lg"></i>
                    </div>
                    <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Itinerary</h3>
                </div>
                <a href="#iframe" class="tw-inline-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-rounded-xl tw-bg-orange-50 tw-text-orange-600 tw-text-xs tw-font-bold hover:tw-bg-orange-100 tw-transition-all tw-no-underline">
                    <i class="fa fa-plus"></i> Add → iframe
                </a>
            </div>
            <textarea name="desc" class="tinymce">{!! $itinerary !!}</textarea>
        </div>

        {{-- iframe modal --}}
        <div class="modal" id="iframe">
            <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden tw-w-[500px] tw-max-w-full tw-shadow-2xl">
                <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
                    <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                        <i class="fa fa-code tw-text-orange-400"></i> Add Iframe
                    </h3>
                    <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
                </div>
                <div class="tw-p-8">
                    <div class="tw-flex tw-flex-col tw-gap-4">
                        <div class="tw-flex tw-flex-col tw-gap-1.5">
                            <label class="tw-text-[11px] tw-uppercase tw-tracking-wider tw-text-slate-400 tw-font-bold">URL</label>
                            <input type="text" id="iframe_url" placeholder="Enter iframe URL...">
                        </div>
                        <div class="tw-text-center tw-pt-2">
                            <span class="btn orange tw-cursor-pointer" onclick="add_iframe(); return false">
                                <i class="fa fa-plus"></i> Add
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Booked Tab --}}
        <div class="hide" id="booked-tab">
            <div class="box !tw-p-0 !tw-overflow-hidden !tw-mb-0">
                <div class="tw-overflow-x-auto">
                    <table class="tw-w-full tw-text-left tw-border-collapse">
                        <thead>
                            <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                                <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Travelers</th>
                                <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Booked By</th>
                                <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Hotel Rooms</th>
                                <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Hotel Grade</th>
                                <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-center">Status</th>
                                <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="tw-divide-y tw-divide-slate-50">
                            @if($gd && $gd->departureBookings)
                            @foreach($gd->departureBookings as $db)
                            @php
                                $dbUser = $db->user;
                                $bookingUserName = 'N/A';
                                if ($dbUser) {
                                    if (!empty($dbUser->company)) $bookingUserName = $dbUser->company;
                                    elseif (!empty($dbUser->first_name)) $bookingUserName = $dbUser->first_name . ' ' . $dbUser->last_name;
                                    else $bookingUserName = $dbUser->email;
                                }
                                $hotelRoomBadges = '';
                                if ($db->single > 0) $hotelRoomBadges .= '<span class="tw-inline-flex tw-items-center tw-px-2 tw-py-0.5 tw-rounded-full tw-text-[11px] tw-font-bold tw-bg-slate-100 tw-text-slate-600 tw-mr-1">' . $db->single . ' &times; Single</span>';
                                if ($db->double > 0) $hotelRoomBadges .= '<span class="tw-inline-flex tw-items-center tw-px-2 tw-py-0.5 tw-rounded-full tw-text-[11px] tw-font-bold tw-bg-slate-100 tw-text-slate-600 tw-mr-1">' . $db->double . ' &times; Double</span>';
                                if ($db->twin > 0) $hotelRoomBadges .= '<span class="tw-inline-flex tw-items-center tw-px-2 tw-py-0.5 tw-rounded-full tw-text-[11px] tw-font-bold tw-bg-slate-100 tw-text-slate-600 tw-mr-1">' . $db->twin . ' &times; Twin</span>';
                                if ($db->triple > 0) $hotelRoomBadges .= '<span class="tw-inline-flex tw-items-center tw-px-2 tw-py-0.5 tw-rounded-full tw-text-[11px] tw-font-bold tw-bg-slate-100 tw-text-slate-600 tw-mr-1">' . $db->triple . ' &times; Triple</span>';
                                if ($db->quad > 0) $hotelRoomBadges .= '<span class="tw-inline-flex tw-items-center tw-px-2 tw-py-0.5 tw-rounded-full tw-text-[11px] tw-font-bold tw-bg-slate-100 tw-text-slate-600">' . $db->quad . ' &times; Quad</span>';

                                $statusLabel = '<span class="tw-inline-flex tw-items-center tw-gap-1 tw-px-2.5 tw-py-1 tw-rounded-full tw-text-[11px] tw-font-bold tw-uppercase tw-bg-amber-50 tw-text-amber-600">Pending</span>';
                                if ($db->trip_status == 'con') $statusLabel = '<span class="tw-inline-flex tw-items-center tw-gap-1 tw-px-2.5 tw-py-1 tw-rounded-full tw-text-[11px] tw-font-bold tw-uppercase tw-bg-orange-50 tw-text-orange-600">Confirmed</span>';
                                elseif ($db->trip_status == 'can') $statusLabel = '<span class="tw-inline-flex tw-items-center tw-gap-1 tw-px-2.5 tw-py-1 tw-rounded-full tw-text-[11px] tw-font-bold tw-uppercase tw-bg-rose-50 tw-text-rose-600">Cancelled</span>';
                                elseif ($db->trip_status == 'com') $statusLabel = '<span class="tw-inline-flex tw-items-center tw-gap-1 tw-px-2.5 tw-py-1 tw-rounded-full tw-text-[11px] tw-font-bold tw-uppercase tw-bg-orange-50 tw-text-orange-600">Completed</span>';
                            @endphp
                            <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                                <td class="tw-py-4 tw-px-6">
                                    <div class="tw-flex tw-flex-col tw-gap-0.5">
                                        <span class="tw-text-xs tw-text-slate-600">Adult: <span class="tw-font-bold tw-text-slate-900">{{ $db->adult }}</span></span>
                                        <span class="tw-text-xs tw-text-slate-600">Child: <span class="tw-font-bold tw-text-slate-900">{{ $db->child }}</span></span>
                                    </div>
                                </td>
                                <td class="tw-py-4 tw-px-6">
                                    <span class="tw-text-sm tw-font-semibold tw-text-slate-700">{{ $bookingUserName }}</span>
                                </td>
                                <td class="tw-py-4 tw-px-6">
                                    <div class="tw-flex tw-flex-wrap tw-gap-1">{!! $hotelRoomBadges !!}</div>
                                </td>
                                <td class="tw-py-4 tw-px-6">
                                    @for($s = 1; $s <= ($db->hotel_grade ?? 0); $s++)
                                    <i class="fa fa-star" style="color:#f59e0b;"></i>
                                    @endfor
                                </td>
                                <td class="tw-py-4 tw-px-6 tw-text-center">{!! $statusLabel !!}</td>
                                <td class="tw-py-4 tw-px-6 tw-text-right">
                                    <span class="tw-text-sm tw-font-bold tw-text-orange-600">
                                    @if($db->invoice)
                                        {{ number_format($db->invoice->total ?? 0, 2) }}
                                    @else
                                        -
                                    @endif
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Travelers Tab --}}
        <div class="hide" id="travelers-tab">
            <div class="box !tw-mb-0">
                <div class="tw-flex tw-flex-col tw-items-center tw-py-8 tw-gap-3">
                    <div class="tw-w-16 tw-h-16 tw-rounded-full tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-300">
                        <i class="fa fa-users tw-text-3xl"></i>
                    </div>
                    <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Travelers</h3>
                    <div class="tw-flex tw-items-center tw-gap-2 tw-bg-orange-50 tw-border tw-border-orange-200 tw-text-orange-700 tw-px-5 tw-py-3 tw-rounded-xl tw-font-semibold tw-text-xs">
                        <i class="fa fa-info-circle tw-text-orange-500"></i> Traveler details will be shown here for confirmed bookings.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <div class="tw-flex tw-justify-center tw-pt-6">
        <button type="submit" class="btn orange tw-px-12">
            <i class="fa fa-check"></i> Save
        </button>
    </div>
    </form>

    {{-- Cancel Button --}}
    <div class="tw-flex tw-justify-end tw-pt-2">
        <a class="tw-inline-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 tw-text-sm tw-font-bold tw-border tw-border-rose-200 hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all tw-no-underline" href="{{ route('admin.bookings.guaranteed.edit', $booking->id) }}?mark_as=can" onclick="return confirm('Are you sure you want to cancel this item?');">
            <i class="fa fa-times-circle"></i> Mark as (Cancelled)
        </a>
    </div>
</div>

<div id="ajax"></div>

<script>
function add_iframe() {
    var iframe_url = $('#iframe_url').val();
    tinymce.activeEditor.execCommand('mceInsertContent', false, '<iframe width="100%" height="500" style="width: 100%; border: 0;" src="' + iframe_url + '"></iframe>');
    window.location = '#close';
}

$('.tab_switch').on('click', function() {
    var new_c = $(this).attr('data-tab');
    $('.active-tab').attr('class', 'hide');
    $(new_c).attr('class', 'active-tab block tw-animate-fade-in');
    $('.tab_switch').attr('class', 'btn orange tw-px-6 tw-py-3 tab_switch tw-cursor-pointer tw-text-sm');
    $(this).attr('class', 'btn grey tw-px-6 tw-py-3 tab_switch tw-cursor-pointer tw-text-sm');
});
</script>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .tw-animate-fade-in { animation: fade-in 0.3s ease-out; }
</style>
@endsection
