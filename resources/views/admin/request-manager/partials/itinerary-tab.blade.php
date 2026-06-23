<style>
.tp-landing{padding:32px;max-width:720px;margin:0 auto;overflow-y:auto;height:100%;}
/* Quote card */
.tp-quote-card{position:relative;border-radius:10px;overflow:hidden;margin-bottom:28px;box-shadow:0 2px 12px rgba(0,0,0,.12);}
.tp-quote-card .tp-cover{height:160px;background:linear-gradient(135deg,#e25822 0%,#f97316 40%,#fdba74 100%);position:relative;display:flex;align-items:flex-end;padding:16px 20px;}
.tp-quote-card .tp-cover img{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;}
.tp-quote-card .tp-cover-overlay{position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(to bottom,rgba(0,0,0,.1),rgba(0,0,0,.55));}
.tp-quote-card .tp-cover-info{position:relative;z-index:2;color:#fff;}
.tp-quote-card .tp-cover-info .tp-price{font-size:22px;font-weight:800;margin-bottom:2px;}
.tp-quote-card .tp-cover-info .tp-meta{font-size:12px;opacity:.9;}
.tp-quote-card .tp-cover-info .tp-meta span{margin-right:12px;}
.tp-quote-card .tp-card-bottom{background:#fff;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;}
.tp-quote-card .tp-card-bottom .tp-title{font-size:14px;font-weight:600;color:#333;}
.tp-quote-card .tp-card-bottom .tp-btn-continue{background:#f97316;color:#fff;border:none;padding:8px 20px;border-radius:5px;font-size:12px;font-weight:600;cursor:pointer;}
.tp-quote-card .tp-card-bottom .tp-btn-continue:hover{background:#ea580c;}
/* Sections */
.tp-section-block{padding:18px 0;border-bottom:1px solid #eee;}
.tp-section-block:last-child{border-bottom:none;}
.tp-section-block h4{font-size:15px;font-weight:700;color:#1a1a1a;margin:0 0 4px;}
.tp-section-block p{font-size:13px;color:#777;margin:0;line-height:1.5;}
.tp-section-block .tp-row-between{display:flex;align-items:center;justify-content:space-between;}
.tp-section-block .tp-btn-outline{background:#fff;border:1px solid #ddd;color:#333;padding:8px 20px;border-radius:5px;font-size:12px;font-weight:600;cursor:pointer;}
.tp-section-block .tp-btn-outline:hover{border-color:#f97316;color:#f97316;}
.tp-section-block .tp-link{color:#f97316;font-size:13px;font-weight:600;text-decoration:none;}
.tp-section-block .tp-link:hover{text-decoration:underline;}
/* Editor wrapper (after clicking Continue/Start) */
.tp-editor{display:none;flex-direction:column;height:100%;overflow:hidden;}
.tp-editor-header{display:flex;align-items:center;justify-content:space-between;padding:0 20px;border-bottom:1px solid #e5e5e5;background:#fff;height:46px;flex-shrink:0;}
.tp-editor-tabs{display:flex;gap:0;height:100%;}
.tp-editor-tabs a{padding:12px 18px;font-size:13px;font-weight:600;color:#555;text-decoration:none;border-bottom:2px solid transparent;display:flex;align-items:center;gap:5px;height:100%;}
.tp-editor-tabs a.active{color:#f97316;border-bottom-color:#f97316;}
.tp-editor-tabs a:hover{color:#f97316;}
.tp-editor-actions{display:flex;align-items:center;gap:10px;}
.tp-editor-actions .tp-btn-action{font-size:12px;font-weight:500;color:#555;text-decoration:none;display:flex;align-items:center;gap:4px;padding:6px 12px;border:1px solid #ddd;border-radius:4px;background:#fff;cursor:pointer;}
.tp-editor-actions .tp-btn-action:hover{border-color:#f97316;color:#f97316;}
.tp-editor-actions .tp-btn-share{background:#f97316;color:#fff;border-color:#f97316;font-weight:600;}
.tp-editor-actions .tp-btn-share:hover{background:#ea580c;}
.tp-editor-body{display:flex;flex:1;overflow:hidden;}
.tp-editor-sidebar{width:260px;min-width:260px;border-right:1px solid #e5e5e5;overflow-y:auto;background:#fafafa;padding:8px 0;}
.tp-editor-content{flex:1;overflow-y:auto;padding:24px 28px;background:#fff;}
/* Day cards sidebar */
.tp-day-card{display:flex;gap:10px;padding:10px 14px;cursor:pointer;border-left:3px solid transparent;transition:all .15s;}
.tp-day-card:hover{background:#f0f0f0;}
.tp-day-card.active{background:#fff7ed;border-left-color:#f97316;}
.tp-day-thumb{width:44px;height:44px;border-radius:6px;background:#ddd;flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;}
.tp-day-thumb img{width:100%;height:100%;object-fit:cover;}
.tp-day-thumb i{color:#aaa;font-size:16px;}
.tp-day-info{flex:1;min-width:0;}
.tp-day-info .day-label{font-size:13px;font-weight:700;color:#333;}
.tp-day-info .day-title{font-size:12px;color:#555;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.tp-day-info .day-loc{font-size:11px;color:#999;display:flex;align-items:center;gap:3px;margin-top:2px;}
.tp-add-day{margin:12px 14px;background:#f97316;color:#fff;border:none;padding:9px 16px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;width:calc(100% - 28px);}
.tp-add-day:hover{background:#ea580c;}
/* Form elements */
.tp-form-group{margin-bottom:16px;}
.tp-form-group label{display:block;font-size:12px;font-weight:600;color:#555;margin-bottom:5px;}
.tp-input{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;font-family:inherit;outline:none;background:#fff;box-sizing:border-box;}
.tp-input:focus{border-color:#f97316;}
.tp-textarea{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px;font-family:inherit;outline:none;resize:vertical;min-height:80px;box-sizing:border-box;}
.tp-textarea:focus{border-color:#f97316;}
.tp-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.tp-meal-row{display:flex;gap:20px;align-items:center;}
.tp-meal-row label{display:flex;align-items:center;gap:5px;font-size:12px;color:#555;cursor:pointer;}
.tp-meal-row input[type="checkbox"]{accent-color:#f97316;width:16px;height:16px;}
.tp-accom-card{background:#fafaf8;border:1px solid #e5e5e5;border-radius:8px;padding:16px;margin-top:8px;}
.tp-accom-card h5{font-size:12px;font-weight:700;color:#f97316;margin:0 0 12px;display:flex;align-items:center;gap:5px;}
.tp-btn-save{background:#f97316;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;margin-top:16px;}
.tp-btn-save:hover{background:#ea580c;}
.tp-price-total{background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:16px;margin:12px 0;}
.tp-price-total .total-label{font-size:12px;font-weight:700;color:#555;text-transform:uppercase;}
.tp-price-total .total-value{font-size:24px;font-weight:800;color:#f97316;margin-top:4px;}
.tp-empty-day{text-align:center;padding:60px 20px;color:#999;}
.tp-empty-day i{font-size:40px;opacity:.3;margin-bottom:10px;display:block;}
.tp-status{font-size:11px;font-weight:600;padding:3px 10px;border-radius:4px;display:inline-block;}
.tp-status-draft{background:#fff3cd;color:#856404;}
.tp-status-sent{background:#d1ecf1;color:#0c5460;}
.tp-back-link{color:#f97316;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:4px;margin-bottom:16px;}
.tp-back-link:hover{text-decoration:underline;}
</style>

{{-- ========== LANDING VIEW ========== --}}
<div class="tp-landing" id="tpLanding">

    {{-- Existing quote card --}}
    @if($tripRequest->latestItinerary)
    <div class="tp-quote-card">
        <div class="tp-cover">
            @if($tripRequest->latestItinerary->cover_photo)
            <img src="{{ $tripRequest->latestItinerary->cover_photo }}" alt="Cover">
            @endif
            <div class="tp-cover-overlay"></div>
            <div class="tp-cover-info">
                <div class="tp-price">${{ number_format($tripRequest->latestItinerary->group_total ?? 0, 2) }}</div>
                <div class="tp-meta">
                    <span>{{ $tripRequest->latestItinerary->days->count() ?? 0 }} days</span>
                    <span>{{ $tripRequest->latestItinerary->num_travelers ?? (($tripRequest->adults ?? 0)+($tripRequest->children ?? 0)) }} travelers</span>
                </div>
            </div>
        </div>
        <div class="tp-card-bottom">
            <div class="tp-title">{{ $tripRequest->latestItinerary->title ?: 'Untitled Quote' }}</div>
            <button class="tp-btn-continue" onclick="openTripEditor()">Continue with this version</button>
        </div>
    </div>
    @endif

    {{-- Start from old quote --}}
    <div class="tp-section-block">
        <div class="tp-row-between">
            <div>
                <h4>Start from an old quote</h4>
                <p>You can start from a quote that was previously shared with another traveller.</p>
            </div>
            <button class="tp-btn-outline">Select a quote</button>
        </div>
    </div>

    {{-- Create new quote --}}
    <div class="tp-section-block">
        <div class="tp-row-between">
            <div>
                <h4>Create a new quote</h4>
                <p>You can create a new quote (empty)</p>
            </div>
            <button class="tp-btn-outline" onclick="openTripEditor()">Start</button>
        </div>
    </div>

    {{-- Manage library --}}
    <div class="tp-section-block">
        <a href="#" class="tp-link">Manage your library</a>
        <div style="margin-top:12px;">
            <div class="tp-row-between">
                <div>
                    <h4>What is the purpose of the library?</h4>
                    <p>The library allows you to create cards for your days, activities, accommodation and transport. Each card is reusable and customizable for each quote.</p>
                </div>
                <button class="tp-btn-outline">Manage your library</button>
            </div>
        </div>
    </div>
</div>

{{-- ========== EDITOR VIEW ========== --}}
<div class="tp-editor" id="tpEditor">
    <div class="tp-editor-header">
        <div class="tp-editor-tabs">
            <a href="javascript:void(0)" class="active" onclick="showItinTab('personalization',this)">My quote</a>
            <a href="javascript:void(0)" onclick="showItinTab('dayByDay',this)">Day by day</a>
            <a href="javascript:void(0)" onclick="showItinTab('pricing',this)">Price</a>
        </div>
        <div class="tp-editor-actions">
            <button class="tp-btn-action"><i class="fa fa-th"></i> Library</button>
            <button class="tp-btn-action"><i class="fa fa-eye"></i> View preview</button>
            <button class="tp-btn-action tp-btn-share"><i class="fa fa-share"></i> Share to the traveller</button>
        </div>
    </div>

    {{-- MY QUOTE / PERSONALIZATION --}}
    <div class="itin-tab-content" id="itinPersonalization" style="overflow-y:auto;padding:28px;">
        <a href="javascript:void(0)" class="tp-back-link" onclick="backToLanding()"><i class="fa fa-arrow-left"></i> Back to quotes</a>
        <div style="max-width:560px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <h3 style="font-size:16px;font-weight:800;color:#1a1a1a;margin:0;">Trip Personalization</h3>
                <span class="tp-status tp-status-{{ $tripRequest->latestItinerary->status ?? 'draft' }}">
                    {{ ucfirst($tripRequest->latestItinerary->status ?? 'Draft') }}
                </span>
            </div>
            <div class="tp-form-group">
                <label>Quote Title</label>
                <input type="text" class="tp-input" id="itinTitle" placeholder="e.g. Jordan Discovery - 8 Days" value="{{ $tripRequest->latestItinerary->title ?? '' }}">
            </div>
            <div class="tp-form-group">
                <label>Traveler Surname</label>
                <input type="text" class="tp-input" id="itinSurname" placeholder="Surname" value="{{ $tripRequest->latestItinerary->traveler_surname ?? $tripRequest->last_name }}">
            </div>
            <div class="tp-grid-2">
                <div class="tp-form-group">
                    <label>Language</label>
                    <select class="tp-input" id="itinLang">
                        <option value="en" {{ ($tripRequest->latestItinerary->language ?? 'en')=='en'?'selected':'' }}>English</option>
                        <option value="fr" {{ ($tripRequest->latestItinerary->language ?? '')=='fr'?'selected':'' }}>French</option>
                        <option value="ar" {{ ($tripRequest->latestItinerary->language ?? '')=='ar'?'selected':'' }}>Arabic</option>
                    </select>
                </div>
                <div class="tp-form-group">
                    <label>Arrival Date</label>
                    <input type="date" class="tp-input" id="itinArrival" value="{{ optional($tripRequest->latestItinerary)->arrival_date ? $tripRequest->latestItinerary->arrival_date->format('Y-m-d') : ($tripRequest->departure_date ?? '') }}">
                </div>
            </div>
            <div class="tp-form-group">
                <label>Cover Photo URL</label>
                <input type="text" class="tp-input" id="itinCover" placeholder="https://..." value="{{ $tripRequest->latestItinerary->cover_photo ?? '' }}">
            </div>
            <button class="tp-btn-save" onclick="saveItinerary()">Save Personalization</button>
        </div>
    </div>

    {{-- DAY BY DAY --}}
    <div class="itin-tab-content" id="itinDayByDay" style="display:none;flex:1;overflow:hidden;">
        <div class="tp-editor-body">
            <div class="tp-editor-sidebar">
                @if($tripRequest->latestItinerary && $tripRequest->latestItinerary->days->count())
                    @foreach($tripRequest->latestItinerary->days as $day)
                    <div class="tp-day-card {{ $loop->first ? 'active' : '' }}" data-day-id="{{ $day->id }}" onclick="selectDay({{ $day->id }},this)">
                        <div class="tp-day-thumb"><i class="fa fa-image"></i></div>
                        <div class="tp-day-info">
                            <div class="day-label">Day {{ $day->day_number }}</div>
                            <div class="day-title">{{ $day->title ?: 'Untitled' }}</div>
                            <div class="day-loc"><i class="fa fa-map-marker"></i> {{ $day->destinations ?: 'No location' }}</div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div style="padding:20px;text-align:center;color:#aaa;font-size:12px;">
                        <i class="fa fa-calendar-plus-o" style="font-size:24px;opacity:.4;display:block;margin-bottom:8px;"></i>
                        No days yet
                    </div>
                @endif
                <button class="tp-add-day" onclick="addDay()"><i class="fa fa-plus"></i> Add another day</button>
            </div>
            <div class="tp-editor-content">
                <div id="dayDetail" style="display:none;">
                    <input type="hidden" id="editDayId" value="">
                    <div class="tp-form-group"><label>Day title</label><input type="text" class="tp-input" id="dayTitle" placeholder="e.g. Arrival in Amman"></div>
                    <div class="tp-form-group"><label>Description</label><textarea class="tp-textarea" id="dayDesc" rows="4" placeholder="Describe the day activities..."></textarea></div>
                    <div class="tp-form-group"><label><i class="fa fa-map-marker"></i> Site(s)</label><input type="text" class="tp-input" id="dayDest" placeholder="e.g. Amman, Dead Sea"></div>
                    <div class="tp-form-group">
                        <label><i class="fa fa-cutlery"></i> Meals included</label>
                        <div class="tp-meal-row">
                            <label><input type="checkbox" id="dayBreakfast"> Breakfast</label>
                            <label><input type="checkbox" id="dayLunch"> Lunch</label>
                            <label><input type="checkbox" id="dayDinner"> Dinner</label>
                        </div>
                    </div>
                    <div class="tp-accom-card">
                        <h5><i class="fa fa-bed"></i> Accommodation</h5>
                        <div class="tp-form-group"><label>Name</label><input type="text" class="tp-input" id="dayAccomName" placeholder="Hotel name"></div>
                        <div class="tp-grid-2">
                            <div class="tp-form-group"><label>Type</label><select class="tp-input" id="dayAccomCat"><option value="">Select</option><option value="Hotel">Hotel</option><option value="Boutique">Boutique</option><option value="Camp">Desert Camp</option><option value="Guesthouse">Guesthouse</option><option value="Resort">Resort</option></select></div>
                            <div class="tp-form-group"><label>Category</label><select class="tp-input" id="dayAccomStars"><option value="">Select</option><option value="2-star">★★</option><option value="3-star">★★★</option><option value="4-star">★★★★</option><option value="5-star">★★★★★</option></select></div>
                        </div>
                        <div class="tp-form-group"><label>Description</label><textarea class="tp-textarea" id="dayAccomDesc" rows="2" placeholder="Hotel description"></textarea></div>
                    </div>
                    <button class="tp-btn-save" onclick="saveDay()">Save Day</button>
                </div>
                <div id="dayEmptyState" class="tp-empty-day">
                    <i class="fa fa-hand-pointer-o"></i>
                    <p style="font-size:14px;font-weight:600;">Select a day or add a new one</p>
                    <p style="font-size:12px;">Click on a day in the sidebar or use "Add another day"</p>
                </div>
            </div>
        </div>
    </div>

    {{-- PRICING --}}
    <div class="itin-tab-content" id="itinPricing" style="display:none;overflow-y:auto;padding:28px;">
        <div style="max-width:560px;">
            <h3 style="font-size:16px;font-weight:800;color:#1a1a1a;margin:0 0 20px;">Pricing</h3>
            <div class="tp-grid-2">
                <div class="tp-form-group"><label>Price per person ($)</label><input type="number" class="tp-input" id="itinPPP" placeholder="0" value="{{ $tripRequest->latestItinerary->price_per_person ?? '' }}"></div>
                <div class="tp-form-group"><label>Number of travelers</label><input type="number" class="tp-input" id="itinNumTrav" value="{{ $tripRequest->latestItinerary->num_travelers ?? (($tripRequest->adults ?? 0) + ($tripRequest->children ?? 0)) }}"></div>
            </div>
            <div class="tp-price-total">
                <div class="total-label">Group Total</div>
                <div class="total-value">$<span id="itinTotalDisplay">{{ number_format($tripRequest->latestItinerary->group_total ?? 0, 2) }}</span></div>
                <input type="hidden" id="itinTotal" value="{{ $tripRequest->latestItinerary->group_total ?? '' }}">
            </div>
            <div class="tp-form-group"><label>Price includes</label><textarea class="tp-textarea" id="itinIncludes" rows="4" placeholder="- Airport transfers&#10;- Accommodation">{{ $tripRequest->latestItinerary->price_includes ?? '' }}</textarea></div>
            <div class="tp-form-group"><label>Price excludes</label><textarea class="tp-textarea" id="itinExcludes" rows="3" placeholder="- International flights&#10;- Travel insurance">{{ $tripRequest->latestItinerary->price_excludes ?? '' }}</textarea></div>
            <div class="tp-form-group"><label>Booking conditions</label><textarea class="tp-textarea" id="itinConditions" rows="3" placeholder="Payment terms, cancellation policy...">{{ $tripRequest->latestItinerary->booking_conditions ?? '' }}</textarea></div>
            <button class="tp-btn-save" onclick="savePricing()">Save Pricing</button>
        </div>
    </div>
</div>
