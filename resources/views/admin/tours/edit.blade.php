@extends('admin.layouts.app')
@section('title', 'Admin | Edit Tour #' . $tour->id)

@section('content')
<div class="tw-mb-8">
    <nav class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest tw-text-slate-400 tw-mb-4">
        <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-500 tw-transition-colors tw-no-underline">Dashboard</a>
        <i class="fa fa-chevron-right tw-text-[11px]"></i>
        <a href="{{ route('admin.tours.index') }}" class="hover:tw-text-orange-500 tw-transition-colors tw-no-underline">Tours</a>
        <i class="fa fa-chevron-right tw-text-[11px]"></i>
        <span class="tw-text-slate-900">Edit Tour #{{ $tour->id }}</span>
    </nav>
    
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-flex tw-items-center tw-gap-4">
                <span class="tw-w-12 tw-h-12 tw-bg-orange-50 tw-text-orange-500 tw-rounded-2xl tw-flex tw-items-center tw-justify-center">
                    <i class="fa fa-edit"></i>
                </span>
                {{ $content->title ?? 'Edit Tour Package' }}
            </h1>
            <p class="subtitle">Modify general settings, logistics, and technical specifications for this tour.</p>
        </div>
        <a href="{{ route('admin.tours.index') }}" class="btn white !tw-text-rose-500 hover:!tw-bg-rose-50 tw-border-rose-100">
            <i class="fa fa-times-circle"></i> Cancel
        </a>
    </div>
</div>

@include('admin.tours._edit_menu')

<form method="POST" action="{{ route('admin.tours.update', $tour->id) }}" class="tw-space-y-8">
    @csrf
    @method('PUT')
    
    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-12 tw-gap-8">
        
        {{-- LEFT COLUMN: Logistics & Settings --}}
        <div class="lg:tw-col-span-4 tw-space-y-8">
            
            <!-- Basics Card -->
            <div class="box !tw-p-0">
                <div class="tw-bg-slate-50 tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3">
                    <i class="fa fa-info-circle tw-text-orange-500"></i>
                    <span class="tw-text-xs tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider">Basic Information</span>
                </div>
                <div class="tw-p-6 tw-space-y-5">
                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                        <div>
                            <label>Nights <span class="tw-text-rose-500">*</span></label>
                            <input type="number" name="number_of_nights" value="{{ old('number_of_nights', $tour->nights) }}" placeholder="0" class="@error('number_of_nights') !tw-border-rose-500 !tw-bg-rose-50 @enderror">
                            @error('number_of_nights') <p class="tw-text-rose-500 tw-text-[10px] tw-mt-1.5 tw-font-bold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label>Days <span class="tw-text-rose-500">*</span></label>
                            <input type="number" name="number_of_days" value="{{ old('number_of_days', $tour->days) }}" placeholder="0" class="@error('number_of_days') !tw-border-rose-500 !tw-bg-rose-50 @enderror">
                            @error('number_of_days') <p class="tw-text-rose-500 tw-text-[10px] tw-mt-1.5 tw-font-bold">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label>Visibility Status</label>
                        <select name="status">
                            <option value="0" {{ $tour->status == 0 ? 'selected' : '' }}>Draft (Hidden)</option>
                            <option value="1" {{ $tour->status == 1 ? 'selected' : '' }}>Published (Live)</option>
                        </select>
                    </div>

                    <div>
                        <label>Category</label>
                        <select name="category">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->lang_id ?? $cat->id }}" {{ $tour->category == ($cat->lang_id ?? $cat->id) ? 'selected' : '' }}>{{ $cat->name ?? $cat->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Tour Type</label>
                        <select name="type">
                            @foreach($types as $tp)
                                <option value="{{ $tp->lang_id ?? $tp->id }}" {{ $tour->type == ($tp->lang_id ?? $tp->id) ? 'selected' : '' }}>{{ $tp->name ?? $tp->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label>Star Rating</label>
                        <select name="rating">
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $tour->rating == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <!-- Logistics Card -->
            <div class="box !tw-p-0">
                <div class="tw-bg-slate-50 tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3">
                    <i class="fa fa-map-marker tw-text-orange-500"></i>
                    <span class="tw-text-xs tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider">Logistics & Route</span>
                </div>
                <div class="tw-p-6 tw-space-y-5">
                    <div>
                        <label>Departure (Start)</label>
                        <div class="tw-space-y-3">
                            <select name="start_country" id="start_country" onchange="loadCities(this.value, 'start_city')">
                                <option value="">Select Country</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->lang_id ?? $c->id }}" {{ $tour->start_country == ($c->lang_id ?? $c->id) ? 'selected' : '' }}>{{ $c->name ?? $c->title }}</option>
                                @endforeach
                            </select>
                            <select name="start_city" id="start_city">
                                <option value="">Select City</option>
                                @foreach($startCities as $city)
                                    <option value="{{ $city->lang_id ?? $city->id }}" {{ $tour->start_city == ($city->lang_id ?? $city->id) ? 'selected' : '' }}>{{ $city->name ?? $city->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label>Arrival (Finish)</label>
                        <div class="tw-space-y-3">
                            <select name="finish_country" id="finish_country" onchange="loadCities(this.value, 'finish_city')">
                                <option value="">Select Country</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->lang_id ?? $c->id }}" {{ $tour->finish_country == ($c->lang_id ?? $c->id) ? 'selected' : '' }}>{{ $c->name ?? $c->title }}</option>
                                @endforeach
                            </select>
                            <select name="finish_city" id="finish_city">
                                <option value="">Select City</option>
                                @foreach($finishCities as $city)
                                    <option value="{{ $city->lang_id ?? $city->id }}" {{ $tour->finish_city == ($city->lang_id ?? $city->id) ? 'selected' : '' }}>{{ $city->name ?? $city->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="tw-flex tw-justify-between">
                            Google Map URL
                            <a target="_blank" href="https://www.google.com/mymaps" class="tw-text-orange-500 tw-capitalize tw-text-[11px] hover:tw-underline">Edit Map <i class="fa fa-external-link"></i></a>
                        </label>
                        <input type="text" name="map" value="{{ $tour->map }}" placeholder="https://google.com/maps/...">
                    </div>
                </div>
            </div>

            <!-- Offers & Promotions -->
            <div class="box !tw-p-0">
                <div class="tw-bg-slate-50 tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3">
                    <i class="fa fa-tags tw-text-orange-500"></i>
                    <span class="tw-text-xs tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider">Promotions</span>
                </div>
                <div class="tw-p-6 tw-space-y-5">
                    <div>
                        <label>Featured Period</label>
                        <div class="tw-grid tw-grid-cols-2 tw-gap-3">
                            <input type="text" name="featured_start" class="datepicker" placeholder="Starts" value="{{ $tour->f_start }}">
                            <input type="text" name="featured_finish" class="datepicker" placeholder="Ends" value="{{ $tour->f_finish }}">
                        </div>
                    </div>
                    <div>
                        <label>Special Offer Period</label>
                        <div class="tw-grid tw-grid-cols-2 tw-gap-3">
                            <input type="text" name="offer_start" class="datepicker" placeholder="Starts" value="{{ $tour->sp_start }}">
                            <input type="text" name="offer_finish" class="datepicker" placeholder="Ends" value="{{ $tour->sp_finish }}">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Technical Content & Details --}}
        <div class="lg:tw-col-span-8 tw-space-y-8">
            
            <!-- Main Content Card -->
            <div class="box">
                <div class="tw-space-y-6">
                    <div>
                        <label>Tour Package Title (English) <span class="tw-text-rose-500">*</span></label>
                        <input type="text" name="en_title" value="{{ old('en_title', $content->title ?? '') }}" placeholder="e.g. 7 Days Classic Jordan Tour" class="tw-text-lg tw-font-bold @error('en_title') !tw-border-rose-500 !tw-bg-rose-50 @enderror">
                        @error('en_title') <p class="tw-text-rose-500 tw-text-xs tw-mt-1.5 tw-font-bold"><i class="fa fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
                    </div>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                        <div>
                            <label>Package Slug / URL</label>
                            <input type="text" name="en_url" value="{{ old('en_url', $content->url ?? '') }}" placeholder="auto-generated-from-title">
                        </div>
                        <div>
                            <label>SEO Keywords</label>
                            <input type="text" name="en_meta_key_words" value="{{ old('en_meta_key_words', $content->meta_key_words ?? '') }}" placeholder="jordan, tour, luxury, adventure">
                        </div>
                    </div>

                    <div>
                        <label>Short Description (Meta Description) <span class="tw-text-rose-500">*</span></label>
                        <textarea name="en_meta_desc" class="tinymce @error('en_meta_desc') !tw-border-rose-500 @enderror" rows="3" placeholder="Describe your package in up to 160 characters for SEO...">{!! old('en_meta_desc', $content->meta_desc ?? '') !!}</textarea>
                        @error('en_meta_desc') <p class="tw-text-rose-500 tw-text-xs tw-mt-1.5 tw-font-bold"><i class="fa fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
                    </div>

                    <input type="hidden" name="en_desc" value="{{ old('en_desc', $content->desc ?? '') }}">
                </div>
            </div>

            <!-- Technical Details Card -->
            <div class="box !tw-p-0">
                <div class="tw-bg-slate-50 tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3">
                    <i class="fa fa-cog tw-text-orange-500"></i>
                    <span class="tw-text-xs tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider">Technical Details & Ratings</span>
                </div>
                <div class="tw-p-6">
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                        @php $tecIdList = ''; @endphp
                        @foreach($tecItems as $tec)
                            @php
                                $tecId = $tec->lang_id ?? $tec->id;
                                $tecIdList .= '-' . $tecId;
                                $tecEnabled = null;
                                if (!empty($tourTec['disable']) && in_array($tecId, $tourTec['disable'])) {
                                    $tecEnabled = 0;
                                } elseif (!empty($tourTec['enable']) && in_array($tecId, $tourTec['enable'])) {
                                    $tecEnabled = 1;
                                }
                                $tecRating = $tourTec['rates'][$tecId] ?? 1;
                            @endphp
                            <div class="tw-p-4 tw-rounded-2xl tw-border tw-border-slate-100 tw-bg-slate-50/50 hover:tw-bg-white hover:tw-shadow-sm tw-transition-all">
                                <div class="tw-flex tw-justify-between tw-items-start tw-mb-3">
                                    <div>
                                        <div class="tw-font-bold tw-text-slate-900 tw-text-sm">{{ $tec->name }}</div>
                                        <div class="tw-flex tw-mt-1">
                                            @for($s = 1; $s <= 5; $s++)
                                                <i class="fa fa-circle tw-text-[8px] tw-mr-1 {{ $s <= $tecRating ? 'tw-text-amber-400' : 'tw-text-slate-200' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-bg-white tw-rounded-lg tw-border tw-border-slate-100 tw-p-1">
                                        <label class="tw-mb-0 tw-cursor-pointer">
                                            <input type="radio" name="tec_{{ $tecId }}" value="1" class="tw-sr-only tw-peer" {{ $tecEnabled === 1 ? 'checked' : '' }}>
                                            <span class="tw-px-3 tw-py-1 tw-rounded-md tw-text-[11px] tw-font-bold tw-text-slate-400 peer-checked:tw-bg-orange-500 peer-checked:tw-text-white tw-transition-all">ON</span>
                                        </label>
                                        <label class="tw-mb-0 tw-cursor-pointer">
                                            <input type="radio" name="tec_{{ $tecId }}" value="0" class="tw-sr-only tw-peer" {{ $tecEnabled === 0 ? 'checked' : '' }}>
                                            <span class="tw-px-3 tw-py-1 tw-rounded-md tw-text-[11px] tw-font-bold tw-text-slate-400 peer-checked:tw-bg-slate-400 peer-checked:tw-text-white tw-transition-all">OFF</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="tw-text-[11px] tw-text-slate-400 tw-uppercase">Difficulty / Level</label>
                                    <select name="tec_rating{{ $tecId }}" class="!tw-h-9 !tw-py-1 !tw-text-xs">
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ $tecRating == $i ? 'selected' : '' }}>Level {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <input type="hidden" name="tec" value="{{ $tecIdList }}">

            <!-- Inclusions Card -->
            <div class="box !tw-p-0">
                <div class="tw-bg-slate-50 tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3">
                    <i class="fa fa-check-circle tw-text-orange-500"></i>
                    <span class="tw-text-xs tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider">Inclusions & Exclusions</span>
                </div>
                <div class="tw-p-6">
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-5">
                        @php $incIdList = ''; @endphp
                        @foreach($incItems as $incItem)
                            @php
                                $incId = $incItem->lang_id ?? $incItem->id;
                                $incIdList .= '-' . $incId;
                                $incVal = 0;
                                if (!empty($tourInc['exc']) && in_array($incId, $tourInc['exc'])) {
                                    $incVal = 1;
                                } elseif (!empty($tourInc['inc']) && in_array($incId, $tourInc['inc'])) {
                                    $incVal = 2;
                                }
                            @endphp
                            <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-rounded-2xl tw-bg-slate-50/30 tw-border tw-border-slate-100">
                                <span class="tw-text-sm tw-font-bold tw-text-slate-700">{{ $incItem->name }}</span>
                                <select name="inc_{{ $incId }}" class="!tw-w-32 !tw-h-9 !tw-py-0 !tw-text-xs">
                                    <option value="0" {{ $incVal == 0 ? 'selected' : '' }}>Disabled</option>
                                    <option value="1" {{ $incVal == 1 ? 'selected' : '' }}>Excluded</option>
                                    <option value="2" {{ $incVal == 2 ? 'selected' : '' }}>Included</option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <input type="hidden" name="incs" value="{{ $incIdList }}">

            <!-- Other Settings -->
            <div class="box !tw-p-0">
                <div class="tw-bg-slate-50 tw-px-6 tw-py-4 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-gap-3">
                    <i class="fa fa-sliders tw-text-orange-500"></i>
                    <span class="tw-text-xs tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider">Advanced Configuration</span>
                </div>
                <div class="tw-p-6 tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                    <div>
                        <label>Relative Tours Display</label>
                        <select name="relative_tours_number">
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ $tour->relative_count == $i ? 'selected' : '' }}>Show {{ $i }} Tours</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label>Partial Payment ($)</label>
                        <input type="text" name="partly_payment" value="{{ $tour->partly_payment }}" placeholder="0 for full payment">
                        <p class="tw-text-[11px] tw-text-slate-400 tw-mt-1.5">Enter $0 to disable partial payments.</p>
                    </div>

                    <div class="md:tw-col-span-2">
                        <label>Contact Person Email (Leave empty for system default)</label>
                        <input type="text" name="contact_email" value="{{ $tour->contact_person }}" placeholder="email@example.com">
                    </div>
                </div>
            </div>

            <!-- Save Action -->
            <div class="tw-flex tw-justify-end tw-gap-4 tw-pt-10">
                <a href="{{ route('admin.tours.index') }}" class="btn white !tw-text-slate-500">Discard Changes</a>
                <button type="submit" class="btn orange !tw-px-16 !tw-h-16 tw-text-lg tw-shadow-xl tw-shadow-orange-200/50">
                    <i class="fa fa-save"></i> Save Tour Configuration
                </button>
            </div>
            
        </div>
    </div>
</form>

<style>
    /* Datepicker Styling Overrides */
    .datepicker { cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' /%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; background-size: 18px; }
</style>

<script src="{{ asset('assets/admin/tinymce/tinymce.min.js') }}"></script>
<script>
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '.tinymce',
            height: 400,
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
        
        // Ensure TinyMCE is saved before form submit
        $('form').on('submit', function() {
            tinymce.triggerSave();
        });
    }

    // === City AJAX Loading ===
    function loadCities(countryId, targetSelectId) {
        var sel = document.getElementById(targetSelectId);
        sel.innerHTML = '<option value="">Loading...</option>';
        if (!countryId) { sel.innerHTML = '<option value="">Select City</option>'; return; }
        fetch('{{ route("admin.ajax.get-cities") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ country_id: countryId, lang: 'en' })
        })
        .then(function(r) { return r.json(); })
        .then(function(cities) {
            var html = '<option value="">Select City</option>';
            if (Array.isArray(cities)) {
                cities.forEach(function(c) {
                    html += '<option value="' + (c.lang_id || c.id) + '">' + (c.name || c.title) + '</option>';
                });
            }
            sel.innerHTML = html;
        })
        .catch(function(err) {
            console.error('loadCities error:', err);
            sel.innerHTML = '<option value="">Select City</option>';
        });
    }
</script>

@endsection
