@extends('admin.layouts.app')
@section('title', 'Admin | Tour Itinerary')

@section('content')
@php
    $content = $tour->contents->where('lang', 'en')->first();
    $itineraryData = [];
    if ($tour->itinerary_data) {
        $itineraryData = json_decode($tour->itinerary_data, true) ?: [];
    }
@endphp

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
        <span class="tw-text-slate-500 tw-font-semibold">Day by Day Itinerary</span>
    </div>

    {{-- Header --}}
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">{{ $content->title ?? 'Tour #'.$tour->id }}</h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">
            Configure day-by-day itinerary with hotels, activities & meals
            <span class="tw-inline-flex tw-items-center tw-ml-2 tw-px-3 tw-py-1 tw-rounded-full tw-bg-orange-50 tw-text-orange-600 tw-text-xs tw-font-bold">
                {{ $tour->days }} Days / {{ $tour->nights }} Nights
            </span>
        </p>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-lg"></i>
        <span class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</span>
    </div>
    @endif

    @include('admin.tours._edit_menu', ['tour' => $tour])

    <form method="POST" action="{{ route('admin.tours.itinerary.update', $tour->id) }}">
        @csrf

        @for($day = 1; $day <= $tour->days; $day++)
        @php
            $dayData = $itineraryData[$day] ?? [];
            $dayActivities = $dayData['activities'] ?? [];
        @endphp
        <div class="box !tw-p-0 !tw-mb-6">
            {{-- Day Header --}}
            <div class="tw-px-8 tw-py-5 tw-bg-slate-50/50 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-justify-between tw-cursor-pointer" onclick="toggleDay({{ $day }})">
                <h3 class="tw-text-sm tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                    <span class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-orange-500 tw-text-white tw-flex tw-items-center tw-justify-center tw-text-xs tw-font-bold">{{ $day }}</span>
                    Day {{ $day }}
                    @if(!empty($dayData['title']))
                    <span class="tw-text-slate-400 tw-font-medium tw-normal-case tw-tracking-normal tw-text-xs tw-ml-2">— {{ $dayData['title'] }}</span>
                    @endif
                </h3>
                <div class="tw-flex tw-items-center tw-gap-3">
                    @if($day == 1)
                    <span class="tw-text-[11px] tw-font-bold tw-text-emerald-500 tw-uppercase">Departure</span>
                    @elseif($day == $tour->days)
                    <span class="tw-text-[11px] tw-font-bold tw-text-rose-500 tw-uppercase">Arrival / End</span>
                    @endif
                    <i class="fa fa-chevron-down tw-text-slate-400 tw-text-xs tw-transition-transform" id="chevron-{{ $day }}"></i>
                </div>
            </div>

            <div class="tw-p-8" id="day-content-{{ $day }}">

                {{-- Row 1: Title, Destination, Transport --}}
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-5 tw-mb-6">
                    <div>
                        <label class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider tw-mb-2 tw-block">Day Title</label>
                        <input type="text" name="days[{{ $day }}][title]" value="{{ $dayData['title'] ?? '' }}" placeholder="e.g. Amman City Tour & Dead Sea" class="!tw-mb-0">
                    </div>
                    <div>
                        <label class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider tw-mb-2 tw-block">Destination / City</label>
                        <input type="text" name="days[{{ $day }}][destination]" value="{{ $dayData['destination'] ?? '' }}" placeholder="e.g. Amman, Petra, Wadi Rum" class="!tw-mb-0">
                    </div>
                    <div>
                        <label class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider tw-mb-2 tw-block">
                            <i class="fa fa-car tw-text-slate-400 tw-mr-1"></i> Transport
                        </label>
                        <input type="text" name="days[{{ $day }}][transport]" value="{{ $dayData['transport'] ?? '' }}" placeholder="e.g. Private vehicle, Bus, 4x4 Jeep" class="!tw-mb-0">
                    </div>
                </div>

                {{-- Description --}}
                <div class="tw-mb-6">
                    <label class="tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider tw-mb-2 tw-block">Day Description</label>
                    <textarea name="days[{{ $day }}][description]" class="tinymce" rows="4" placeholder="Describe the day's journey...">{{ $dayData['description'] ?? '' }}</textarea>
                </div>

                {{-- Hotel / Accommodation Section --}}
                <div class="tw-mb-6 tw-p-5 tw-rounded-2xl tw-bg-orange-50/50 tw-border tw-border-orange-100">
                    <div class="tw-flex tw-items-center tw-gap-2 tw-mb-4">
                        <span class="tw-w-8 tw-h-8 tw-rounded-xl tw-bg-orange-100 tw-text-orange-600 tw-flex tw-items-center tw-justify-center">
                            <i class="fa fa-bed tw-text-sm"></i>
                        </span>
                        <span class="tw-text-xs tw-font-bold tw-text-orange-800 tw-uppercase tw-tracking-wider">Hotel / Accommodation</span>
                    </div>
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-4">
                        <div>
                            <label class="tw-text-xs tw-font-semibold tw-text-slate-600 tw-mb-1.5 tw-block">Hotel Name</label>
                            <input type="text" name="days[{{ $day }}][hotel_name]" value="{{ $dayData['hotel_name'] ?? '' }}" placeholder="e.g. Kempinski Hotel" class="!tw-mb-0 !tw-h-10 !tw-text-sm !tw-bg-white">
                        </div>
                        <div>
                            <label class="tw-text-xs tw-font-semibold tw-text-slate-600 tw-mb-1.5 tw-block">Stars</label>
                            <select name="days[{{ $day }}][hotel_stars]" class="!tw-mb-0 !tw-h-10 !tw-text-sm !tw-bg-white">
                                <option value="" {{ empty($dayData['hotel_stars'] ?? '') ? 'selected' : '' }}>Select Stars</option>
                                @for($s = 1; $s <= 5; $s++)
                                <option value="{{ $s }}" {{ ($dayData['hotel_stars'] ?? '') == $s ? 'selected' : '' }}>{{ $s }} Star{{ $s > 1 ? 's' : '' }} {!! str_repeat('★', $s) !!}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="tw-text-xs tw-font-semibold tw-text-slate-600 tw-mb-1.5 tw-block">Category</label>
                            <select name="days[{{ $day }}][hotel_category]" class="!tw-mb-0 !tw-h-10 !tw-text-sm !tw-bg-white">
                                <option value="" {{ empty($dayData['hotel_category'] ?? '') ? 'selected' : '' }}>Select Category</option>
                                <option value="standard" {{ ($dayData['hotel_category'] ?? '') == 'standard' ? 'selected' : '' }}>Standard</option>
                                <option value="superior" {{ ($dayData['hotel_category'] ?? '') == 'superior' ? 'selected' : '' }}>Superior</option>
                                <option value="deluxe" {{ ($dayData['hotel_category'] ?? '') == 'deluxe' ? 'selected' : '' }}>Deluxe</option>
                                <option value="boutique" {{ ($dayData['hotel_category'] ?? '') == 'boutique' ? 'selected' : '' }}>Boutique</option>
                                <option value="luxury" {{ ($dayData['hotel_category'] ?? '') == 'luxury' ? 'selected' : '' }}>Luxury</option>
                                <option value="camp" {{ ($dayData['hotel_category'] ?? '') == 'camp' ? 'selected' : '' }}>Camp / Glamping</option>
                                <option value="guesthouse" {{ ($dayData['hotel_category'] ?? '') == 'guesthouse' ? 'selected' : '' }}>Guesthouse</option>
                            </select>
                        </div>
                        <div>
                            <label class="tw-text-xs tw-font-semibold tw-text-slate-600 tw-mb-1.5 tw-block">Website</label>
                            <input type="text" name="days[{{ $day }}][hotel_website]" value="{{ $dayData['hotel_website'] ?? '' }}" placeholder="https://..." class="!tw-mb-0 !tw-h-10 !tw-text-sm !tw-bg-white">
                        </div>
                    </div>
                </div>

                {{-- Meals --}}
                <div class="tw-mb-6 tw-p-5 tw-rounded-2xl tw-bg-amber-50/50 tw-border tw-border-amber-100">
                    <div class="tw-flex tw-items-center tw-gap-2 tw-mb-4">
                        <span class="tw-w-8 tw-h-8 tw-rounded-xl tw-bg-amber-100 tw-text-amber-600 tw-flex tw-items-center tw-justify-center">
                            <i class="fa fa-cutlery tw-text-sm"></i>
                        </span>
                        <span class="tw-text-xs tw-font-bold tw-text-amber-800 tw-uppercase tw-tracking-wider">Meals</span>
                    </div>
                    <div class="tw-flex tw-flex-wrap tw-gap-6">
                        @php $meals = $dayData['meals'] ?? []; @endphp
                        <label class="tw-flex tw-items-center tw-gap-2.5 tw-cursor-pointer tw-px-4 tw-py-2.5 tw-rounded-xl tw-border tw-transition-all {{ in_array('breakfast', (array)$meals) ? 'tw-bg-amber-100 tw-border-amber-300 tw-text-amber-800' : 'tw-bg-white tw-border-slate-200 tw-text-slate-500' }}">
                            <input type="checkbox" name="days[{{ $day }}][meals][]" value="breakfast" class="tw-w-4 tw-h-4 tw-accent-amber-500" {{ in_array('breakfast', (array)$meals) ? 'checked' : '' }}>
                            <span class="tw-text-sm tw-font-bold">🥐 Breakfast</span>
                        </label>
                        <label class="tw-flex tw-items-center tw-gap-2.5 tw-cursor-pointer tw-px-4 tw-py-2.5 tw-rounded-xl tw-border tw-transition-all {{ in_array('lunch', (array)$meals) ? 'tw-bg-amber-100 tw-border-amber-300 tw-text-amber-800' : 'tw-bg-white tw-border-slate-200 tw-text-slate-500' }}">
                            <input type="checkbox" name="days[{{ $day }}][meals][]" value="lunch" class="tw-w-4 tw-h-4 tw-accent-amber-500" {{ in_array('lunch', (array)$meals) ? 'checked' : '' }}>
                            <span class="tw-text-sm tw-font-bold">🍽️ Lunch</span>
                        </label>
                        <label class="tw-flex tw-items-center tw-gap-2.5 tw-cursor-pointer tw-px-4 tw-py-2.5 tw-rounded-xl tw-border tw-transition-all {{ in_array('dinner', (array)$meals) ? 'tw-bg-amber-100 tw-border-amber-300 tw-text-amber-800' : 'tw-bg-white tw-border-slate-200 tw-text-slate-500' }}">
                            <input type="checkbox" name="days[{{ $day }}][meals][]" value="dinner" class="tw-w-4 tw-h-4 tw-accent-amber-500" {{ in_array('dinner', (array)$meals) ? 'checked' : '' }}>
                            <span class="tw-text-sm tw-font-bold">🌙 Dinner</span>
                        </label>
                        <label class="tw-flex tw-items-center tw-gap-2.5 tw-cursor-pointer tw-px-4 tw-py-2.5 tw-rounded-xl tw-border tw-transition-all {{ in_array('lunchbox', (array)$meals) ? 'tw-bg-amber-100 tw-border-amber-300 tw-text-amber-800' : 'tw-bg-white tw-border-slate-200 tw-text-slate-500' }}">
                            <input type="checkbox" name="days[{{ $day }}][meals][]" value="lunchbox" class="tw-w-4 tw-h-4 tw-accent-amber-500" {{ in_array('lunchbox', (array)$meals) ? 'checked' : '' }}>
                            <span class="tw-text-sm tw-font-bold">📦 Lunchbox</span>
                        </label>
                    </div>
                </div>

                {{-- Overnight (kept for backward compat) --}}
                <input type="hidden" name="days[{{ $day }}][overnight]" value="{{ $dayData['overnight'] ?? $dayData['hotel_name'] ?? '' }}">

                {{-- Activities --}}
                <div class="tw-p-5 tw-rounded-2xl tw-bg-emerald-50/50 tw-border tw-border-emerald-100">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <span class="tw-w-8 tw-h-8 tw-rounded-xl tw-bg-emerald-100 tw-text-emerald-600 tw-flex tw-items-center tw-justify-center">
                                <i class="fa fa-bolt tw-text-sm"></i>
                            </span>
                            <span class="tw-text-xs tw-font-bold tw-text-emerald-800 tw-uppercase tw-tracking-wider">Activities & Visits</span>
                        </div>
                        <button type="button" onclick="addActivity({{ $day }})" class="tw-flex tw-items-center tw-gap-1.5 tw-px-4 tw-py-2 tw-rounded-xl tw-bg-emerald-500 tw-text-white tw-text-xs tw-font-bold hover:tw-bg-emerald-600 tw-transition-colors tw-border-0 tw-cursor-pointer tw-shadow-sm">
                            <i class="fa fa-plus tw-text-[10px]"></i> Add Activity
                        </button>
                    </div>
                    <div id="activities-day-{{ $day }}" class="tw-space-y-2">
                        @if(!empty($dayActivities))
                            @foreach($dayActivities as $aIdx => $activity)
                            <div class="tw-flex tw-items-center tw-gap-2 activity-row">
                                <span class="tw-w-6 tw-h-6 tw-rounded-md tw-bg-emerald-100 tw-text-emerald-500 tw-flex tw-items-center tw-justify-center tw-flex-shrink-0">
                                    <i class="fa fa-bolt tw-text-[10px]"></i>
                                </span>
                                <input type="text" name="days[{{ $day }}][activities][]" value="{{ $activity }}" placeholder="e.g. Visit Petra Treasury, Camel Ride..." class="tw-flex-1 !tw-mb-0 !tw-h-10 !tw-text-sm">
                                <button type="button" onclick="this.closest('.activity-row').remove()" class="tw-w-7 tw-h-7 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-white tw-text-slate-400 hover:tw-bg-rose-50 hover:tw-text-rose-500 tw-transition-all tw-border tw-border-slate-200 tw-cursor-pointer tw-flex-shrink-0">
                                    <i class="fa fa-times tw-text-[10px]"></i>
                                </button>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    <div id="activities-empty-{{ $day }}" class="tw-py-4 tw-text-center {{ !empty($dayActivities) ? 'tw-hidden' : '' }}">
                        <p class="tw-text-emerald-300 tw-text-xs tw-font-medium">No activities added — click "Add Activity" above</p>
                    </div>
                </div>

            </div>
        </div>
        @endfor

        <button type="submit" class="btn orange tw-w-full !tw-py-4 !tw-text-base tw-shadow-lg tw-shadow-orange-100">
            <i class="fa fa-check-circle"></i> Save Itinerary
        </button>
    </form>
</div>

<script src="{{ asset('assets/admin/tinymce/tinymce.min.js') }}"></script>
<script>
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '.tinymce',
            height: 200,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                     'bold italic backcolor | alignleft aligncenter ' +
                     'alignright alignjustify | bullist numlist outdent indent | ' +
                     'removeformat | image code table | help',
            content_css: ["/assets/admin/gogies.css", "/assets/admin/tinymce_content.css"],
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });

        $('form').on('submit', function() {
            tinymce.triggerSave();
        });
    }

    function addActivity(dayNum) {
        var container = document.getElementById('activities-day-' + dayNum);
        var html = '<div class="tw-flex tw-items-center tw-gap-2 activity-row">' +
            '<span class="tw-w-6 tw-h-6 tw-rounded-md tw-bg-emerald-100 tw-text-emerald-500 tw-flex tw-items-center tw-justify-center tw-flex-shrink-0">' +
                '<i class="fa fa-bolt tw-text-[10px]"></i>' +
            '</span>' +
            '<input type="text" name="days[' + dayNum + '][activities][]" value="" placeholder="e.g. Visit Petra Treasury, Camel Ride..." class="tw-flex-1 !tw-mb-0 !tw-h-10 !tw-text-sm">' +
            '<button type="button" onclick="this.closest(\'.activity-row\').remove()" class="tw-w-7 tw-h-7 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-white tw-text-slate-400 hover:tw-bg-rose-50 hover:tw-text-rose-500 tw-transition-all tw-border tw-border-slate-200 tw-cursor-pointer tw-flex-shrink-0">' +
                '<i class="fa fa-times tw-text-[10px]"></i>' +
            '</button>' +
        '</div>';
        container.insertAdjacentHTML('beforeend', html);
        document.getElementById('activities-empty-' + dayNum).classList.add('tw-hidden');
        container.lastElementChild.querySelector('input').focus();
    }

    function toggleDay(dayNum) {
        var content = document.getElementById('day-content-' + dayNum);
        var chevron = document.getElementById('chevron-' + dayNum);
        if (content.style.display === 'none') {
            content.style.display = 'block';
            chevron.style.transform = 'rotate(0deg)';
        } else {
            content.style.display = 'none';
            chevron.style.transform = 'rotate(-90deg)';
        }
    }

    // Toggle meal styling on change
    document.querySelectorAll('input[type="checkbox"][name*="meals"]').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var label = this.closest('label');
            if (this.checked) {
                label.classList.remove('tw-bg-white', 'tw-border-slate-200', 'tw-text-slate-500');
                label.classList.add('tw-bg-amber-100', 'tw-border-amber-300', 'tw-text-amber-800');
            } else {
                label.classList.remove('tw-bg-amber-100', 'tw-border-amber-300', 'tw-text-amber-800');
                label.classList.add('tw-bg-white', 'tw-border-slate-200', 'tw-text-slate-500');
            }
        });
    });
</script>
@endsection
