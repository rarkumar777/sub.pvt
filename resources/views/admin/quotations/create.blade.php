@extends('admin.layouts.app')

@section('title', 'Admin | New Quotation')



@section('content')
<style>
.qf-wrap{background:#fff;border:1px solid #ddd;border-radius:4px;margin-bottom:16px;}
.qf-header{background:#f5f5f5;padding:10px 16px;border-bottom:1px solid #ddd;font-size:14px;font-weight:700;color:#333;}
.qf-header i{color:#ea580c;margin-right:6px;}
.qf-body{padding:12px 16px;}
.qf-row{display:flex;gap:12px;margin-bottom:10px;flex-wrap:wrap;}
.qf-field{flex:1;min-width:160px;}
.qf-field label{display:block;font-size:11px;font-weight:600;color:#666;margin-bottom:3px;}
.qf-field input,.qf-field select{width:100%;padding:6px 10px;border:1px solid #ccc;border-radius:3px;font-size:13px;box-sizing:border-box;background:#fff;}
.qf-field input:focus,.qf-field select:focus{border-color:#ea580c;outline:none;box-shadow:0 0 0 2px rgba(234,88,12,.1);}
.qf-field .req{color:#e74c3c;font-weight:700;}
.qf-back{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;background:#e74c3c;color:#fff;border-radius:4px;font-size:12px;font-weight:600;text-decoration:none;}
.qf-back:hover{background:#c0392b;color:#fff;}
</style>

<div style="max-width:1200px;margin:0 auto;">
    @include('admin.quotations._nav')

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin:12px 0 16px;">
        <h2 style="font-size:18px;font-weight:800;color:#333;margin:0;"><i class="fa fa-plus" style="color:#ea580c;margin-right:6px;"></i> Add New → Quotation</h2>
        <a href="{{ route('admin.quotations.index') }}" class="qf-back"><i class="fa fa-arrow-left"></i> Back to List</a>
    </div>

    <form method="POST" action="{{ route('admin.quotations.store') }}" id="quotation_form">
        @csrf

        {{-- Customer Information --}}
        <div class="qf-wrap">
            <div class="qf-header"><i class="fa fa-user"></i> Customer Information</div>
            <div class="qf-body">
                <div class="qf-row">
                    <div class="qf-field">
                        <label>Customer Name <span class="req">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $prefill['customer_name'] ?? '') }}" placeholder="Full Name" required>
                    </div>
                    <div class="qf-field">
                        <label>Customer E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $prefill['email'] ?? '') }}" placeholder="example@mail.com">
                    </div>
                    <div class="qf-field">
                        <label>Customer Telephone</label>
                        <input type="text" name="phone" value="{{ old('phone', $prefill['phone'] ?? '') }}" placeholder="+000 000 000">
                    </div>
                    <div class="qf-field">
                        <label>Description</label>
                        <input type="text" name="description" value="{{ old('description', $prefill['description'] ?? '') }}" placeholder="Short trip title">
                    </div>
                </div>
            </div>
        </div>

        {{-- Trip Architecture --}}
        <div class="qf-wrap">
            <div class="qf-header"><i class="fa fa-map-signs"></i> Trip Architecture</div>
            <div class="qf-body">
                <div class="qf-row">
                    <div class="qf-field">
                        <label>Reference #</label>
                        <input type="text" name="ref_number" value="{{ old('ref_number', $prefill['ref_number'] ?? '') }}" placeholder="AUTO-GEN">
                    </div>
                    <div class="qf-field">
                        <label>Travel Date <span class="req">*</span></label>
                        <input type="date" name="travel_date" id="travel_date" value="{{ old('travel_date', $prefill['travel_date'] ?? date('Y-m-d')) }}" required>
                    </div>
                    <div class="qf-field" style="max-width:100px;">
                        <label>Days <span class="req">*</span></label>
                        <input type="number" name="days" id="days_input" value="{{ old('days', $prefill['days'] ?? 1) }}" min="1" required onchange="buildDaySections();">
                    </div>
                    <div class="qf-field" style="max-width:100px;">
                        <label>Nights <span class="req">*</span></label>
                        <input type="number" name="nights" value="{{ old('nights', $prefill['nights'] ?? 0) }}" min="0" required>
                    </div>
                </div>
                <div class="qf-row">
                    <div class="qf-field" style="max-width:120px;">
                        <label>Travelers <span class="req">*</span></label>
                        <input type="number" name="travelers_number" value="{{ old('travelers_number', $prefill['travelers_number'] ?? 1) }}" min="1" required>
                        @if(!empty($prefill['trip_request_id']))
                        <input type="hidden" name="trip_request_id" value="{{ $prefill['trip_request_id'] }}">
                        @endif
                    </div>
                    <div class="qf-field">
                        <label>Pricing</label>
                        <select name="pricing_base">
                            @foreach($pricingBases as $pb)
                            <option value="{{ $pb->id }}">{{ $pb->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="qf-field">
                        <label>Languages</label>
                        <select name="lang">
                            <option value="en">English</option>
                            <option value="ar">Arabic</option>
                            <option value="es">Spanish</option>
                            <option value="fr">French</option>
                            <option value="it">Italian</option>
                            <option value="de">German</option>
                            <option value="zh">Chinese</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dynamic Day Sections --}}
        {{-- Dynamic Day Sections --}}
        <div id="day_sections_container" style="margin-top:8px;"></div>

        <div id="service_validation_error" style="display:none;background:#fef2f2;border:1px solid #fca5a5;border-radius:4px;padding:10px 16px;margin:10px 0;text-align:center;">
            <i class="fa fa-exclamation-circle" style="color:#e74c3c;margin-right:6px;"></i>
            <span style="font-size:13px;font-weight:600;color:#b91c1c;">Service resource ka cost 0 nahi ho sakta. Saare services ka cost fill karein.</span>
        </div>
        <div style="text-align:center;padding:20px 0 40px;">
            <button type="submit" style="padding:10px 30px;background:#ea580c;color:#fff;border:none;border-radius:4px;font-size:14px;font-weight:700;cursor:pointer;"><i class="fa fa-save" style="margin-right:6px;"></i> Finalize and Store Quotation</button>
        </div>
    </form>
</div>

{{-- Expense Service Modal --}}
<div class="modal" id="expense_modal">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[1000px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-orange-400"></i> Add Expense Service
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        
        <div class="tw-flex tw-h-[650px] tw-max-h-[80vh]">
            {{-- Left Panel: Sub-categories (cities) --}}
            <div class="tw-w-[350px] tw-flex tw-flex-col tw-bg-slate-50 tw-border-r tw-border-slate-100">
                <div class="tw-p-6 tw-flex tw-flex-col tw-gap-4 tw-bg-white tw-border-b tw-border-slate-100">
                    <div class="tw-relative">
                        <i class="fa fa-search tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400"></i>
                        <input type="text" id="modal_search" placeholder="Search categories..." onkeyup="filterModalItems();" class="!tw-pl-11 !tw-h-11">
                    </div>
                    <div class="tw-flex tw-gap-2">
                        <button class="tw-flex-1 tw-px-3 tw-py-2 tw-bg-slate-50 tw-text-slate-600 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest tw-rounded-lg hover:tw-bg-slate-100 tw-transition-colors" onclick="modalCollapseAll();">
                            <i class="fa fa-minus-circle tw-mr-1"></i> Collapse
                        </button>
                        <button class="tw-flex-1 tw-px-3 tw-py-2 tw-bg-slate-50 tw-text-slate-600 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest tw-rounded-lg hover:tw-bg-slate-100 tw-transition-colors" onclick="modalExpandAll();">
                            <i class="fa fa-plus-circle tw-mr-1"></i> Expand
                        </button>
                    </div>
                </div>
                <div id="modal_left_panel" class="tw-flex-1 tw-overflow-y-auto tw-p-6">
                    <div class="tw-flex tw-items-center tw-gap-3 tw-p-3 tw-rounded-xl tw-bg-white tw-border tw-border-slate-100 tw-hover:tw-border-orange-200 tw-transition-all tw-cursor-pointer">
                        <input type="radio" name="modal_cat_radio" value="0" checked onclick="resetModalRightPanel();" class="!tw-w-4 !tw-h-4 !tw-shadow-none">
                        <span class="tw-text-sm tw-font-bold tw-text-slate-700">All Parents</span>
                    </div>
                </div>
            </div>

            {{-- Right Panel: Services List --}}
            <div class="tw-flex-1 tw-flex tw-flex-col tw-bg-white">
                <div id="modal_right_header" class="tw-px-8 tw-py-4 tw-bg-orange-50/50 tw-border-b tw-border-orange-100"></div>
                <div id="modal_right_vendor" class="tw-px-8 tw-py-4 tw-bg-slate-50/50 tw-border-b tw-border-slate-100"></div>
                <div class="tw-flex-1 tw-overflow-y-auto">
                    <div id="modal_right_table">
                        <table class="tw-w-full">
                            <thead>
                                <tr class="tw-bg-slate-50 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">
                                    <th class="tw-py-3 tw-px-8 tw-text-left">Service Description</th>
                                    <th class="tw-py-3 tw-px-4 tw-text-left">Base Cost</th>
                                    <th class="tw-py-3 tw-px-4 tw-text-left">Vendor</th>
                                    <th class="tw-py-3 tw-px-8 tw-text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="expense_day_number">
        <input type="hidden" id="expense_parent_cat_id">
    </div>
</div>

{{-- Inclusion Modal --}}
<div class="modal" id="add_inclusion">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[700px] !tw-max-w-[90vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-paperclip tw-text-orange-400"></i> Manage Inclusions
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <div class="tw-p-8">
            <div class="tw-relative tw-mb-6">
                <i class="fa fa-search tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400"></i>
                <input type="text" id="inclusion_search" placeholder="Search inclusion or exclusion..." onkeyup="filterInclusions();" class="!tw-pl-11">
            </div>
            <input type="hidden" id="inclusion_day">
            <div id="inclusion_items_list" class="tw-max-height-[450px] tw-overflow-y-auto tw-flex tw-flex-col tw-gap-2">
                @foreach($inclusions as $inc)
                <div class="inclusion-row tw-flex tw-items-center tw-justify-between tw-p-4 tw-rounded-2xl tw-border tw-border-slate-50 hover:tw-bg-slate-50 tw-transition-all" data-name="{{ strtolower($inc->name) }}">
                    <span class="tw-text-sm tw-font-bold tw-text-slate-700">{{ $inc->name }}</span>
                    <div class="tw-flex tw-gap-2">
                        <button onclick="addInclusionItem(this, 'included', '{{ addslashes($inc->name) }}');" class="tw-px-4 tw-py-2 tw-bg-emerald-50 tw-text-emerald-600 tw-text-xs tw-font-bold tw-rounded-xl hover:tw-bg-emerald-100 tw-transition-colors">
                            <i class="fa fa-check tw-mr-1"></i> Include
                        </button>
                        <button onclick="addInclusionItem(this, 'excluded', '{{ addslashes($inc->name) }}');" class="tw-px-4 tw-py-2 tw-bg-rose-50 tw-text-rose-600 tw-text-xs tw-font-bold tw-rounded-xl hover:tw-bg-rose-100 tw-transition-colors">
                            <i class="fa fa-times tw-mr-1"></i> Exclude
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.cat-submenu a:hover { background: #f0f0f0 !important; }
/* Animation for dynamic elements */
#day_sections_container > div { animation: slideIn 0.4s ease-out forwards; }
@keyframes slideIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
// Fast access categories from server
var expenseCategories = @json($expenseCategories);
var expenseCountries = @json($expenseCountries);

function buildDaySections() {
    var days = parseInt(document.getElementById('days_input').value) || 1;
    var travelDate = document.getElementById('travel_date').value || '';
    var container = document.getElementById('day_sections_container');
    container.innerHTML = '';

    for (var c = 1; c <= days; c++) {
        var currentDate = '';
        if (travelDate) {
            var d = new Date(travelDate);
            d.setDate(d.getDate() + (c - 1));
            currentDate = d.toISOString().split('T')[0];
        }

        var html = '<div class="qf-wrap" style="margin-bottom:16px;">';
        
        // Day Header - compact colored bar like reference
        html += '<div style="background:#c2410c;padding:8px 16px;display:flex;align-items:center;justify-content:space-between;border-radius:4px 4px 0 0;">';
        html += '<span style="font-size:13px;font-weight:700;color:#fff;">Day ' + c + ' - ' + currentDate + '</span>';
        html += '<a href="javascript:void(0);" style="padding:4px 12px;background:#ea580c;color:#fff;border-radius:3px;font-size:11px;font-weight:600;text-decoration:none;">Fill From Canned Days</a>';
        html += '</div>';

        // Two column layout: Left=Editor, Right=Expenses
        html += '<div style="display:flex;border:1px solid #ddd;border-top:none;">';

        // LEFT: TinyMCE Editor
        html += '<div style="flex:1;border-right:1px solid #ddd;padding:0;">';
        html += '<textarea class="tinymce" name="desc_day_' + c + '" style="min-height:350px;"></textarea>';
        html += '</div>';

        // RIGHT: Expenses
        html += '<div style="flex:1;padding:0;">';
        html += '<div style="background:#f5f5f5;padding:8px 12px;border-bottom:1px solid #ddd;display:flex;align-items:center;justify-content:space-between;">';
        html += '<span style="font-size:12px;font-weight:700;color:#555;">Day ' + c + ' - Expenses</span>';

        // Add New Dropdown
        html += '<div class="country-add-wrapper" style="position:relative;">';
        html += '<button type="button" style="padding:4px 10px;background:#ea580c;color:#fff;border:none;border-radius:3px;font-size:11px;font-weight:600;cursor:pointer;" onclick="event.stopPropagation(); toggleCountryDropdown(' + c + ');"><i class="fa fa-plus"></i> Add New</button>';
        html += '<div id="country_dropdown_' + c + '" class="country-dropdown" style="position:absolute;right:0;top:100%;margin-top:4px;width:160px;background:#fff;border:1px solid #ddd;border-radius:4px;z-index:100;display:none;box-shadow:0 4px 12px rgba(0,0,0,.1);">';
        for (var ci = 0; ci < expenseCountries.length; ci++) {
            var country = expenseCountries[ci];
            html += '<a href="javascript:void(0);" onclick="loadCountryCategories(' + c + ', ' + country.id + ', \'' + (country.name || '').replace(/'/g, "\\'") + '\');" style="display:block;padding:6px 12px;font-size:11px;font-weight:600;color:#555;text-decoration:none;border-bottom:1px solid #f5f5f5;">' + country.name + '</a>';
        }
        html += '</div></div></div>';

        // Fast access category buttons like reference (Hotels, Activities, Restaurants, Transportation)
        html += '<div style="padding:8px 12px;display:flex;flex-wrap:wrap;gap:4px;border-bottom:1px solid #eee;">';
        html += '<span style="font-size:10px;font-weight:600;color:#888;padding:4px 0;margin-right:4px;">Jordan Expenses</span>';
        for (var ki = 0; ki < expenseCategories.length; ki++) {
            var cat = expenseCategories[ki];
            html += '<div style="position:relative;display:inline-block;">';
            html += '<button type="button" class="cat-btn" style="padding:3px 10px;border-radius:3px;color:#fff;font-size:10px;font-weight:700;border:none;cursor:pointer;background:' + cat.color + ';" onclick="toggleCatDropdown(this, event);">' + cat.name + ' ▾</button>';
            html += '<div class="cat-submenu" style="position:absolute;left:0;top:100%;margin-top:2px;min-width:160px;background:#fff;border:1px solid #ddd;border-radius:4px;z-index:100;display:none;box-shadow:0 4px 12px rgba(0,0,0,.1);">';
            for (var si = 0; si < cat.sub_categories.length; si++) {
                var sub = cat.sub_categories[si];
                html += '<a href="javascript:void(0);" onclick="loadSubCategoryServices(' + c + ', ' + sub.id + ', \'' + (sub.name || '').replace(/'/g, "\\'") + '\'); closeCatDropdowns();" style="display:block;padding:5px 12px;font-size:11px;color:#555;text-decoration:none;border-bottom:1px solid #f5f5f5;font-weight:500;">' + sub.name + '</a>';
            }
            html += '</div></div>';
        }
        html += '</div>';

        // Selected Expenses List
        html += '<div id="expense_list_' + c + '" style="padding:8px 12px;min-height:80px;"></div>';
        html += '</div></div>'; // End two-column

        // Inclusions Section
        html += '<div style="border:1px solid #ddd;border-top:none;padding:8px 12px;">';
        html += '<div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">';
        html += '<button type="button" onclick="addInclusion(' + c + ');" style="padding:4px 12px;background:#f97316;color:#fff;border:none;border-radius:3px;font-size:11px;font-weight:600;cursor:pointer;"><i class="fa fa-plus"></i> Add Inclusions</button>';
        html += '</div>';
        html += '<div style="display:flex;gap:16px;">';
        html += '<div style="flex:1;">';
        html += '<div style="font-size:11px;font-weight:700;color:#27ae60;margin-bottom:4px;">Included</div>';
        html += '<div id="day_inc_' + c + '" style="min-height:40px;"></div>';
        html += '</div>';
        html += '<div style="flex:1;">';
        html += '<div style="font-size:11px;font-weight:700;color:#e74c3c;margin-bottom:4px;">Excluded</div>';
        html += '<div id="day_exc_' + c + '" style="min-height:40px;"></div>';
        html += '</div>';
        html += '</div></div>';

        // Day Images
        html += '<div style="border:1px solid #ddd;border-top:none;padding:8px 12px;border-radius:0 0 4px 4px;">';
        html += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">';
        html += '<span style="font-size:12px;font-weight:700;color:#555;">Day ' + c + ' - Images</span>';
        html += '<button type="button" class="image_selector" data-input-name="day_images_' + c + '" style="padding:4px 10px;background:#ea580c;color:#fff;border:none;border-radius:3px;font-size:11px;font-weight:600;cursor:pointer;"><i class="fa fa-plus"></i> Add Image</button>';
        html += '</div>';
        html += '<div id="images_' + c + '" style="display:flex;flex-wrap:wrap;gap:8px;"></div>';
        html += '</div>';

        html += '</div>'; // End qf-wrap
        container.innerHTML += html;
    }

    // Re-init TinyMCE
    if (typeof tinymce !== 'undefined') {
        tinymce.remove('.tinymce');
        setTimeout(function() {
            tinymce.init({
                selector: '.tinymce',
                plugins: [
                    "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern"
                ],
                toolbar1: "bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | forecolor backcolor | table | code fullscreen",
                menubar: false,
                toolbar_items_size: 'small',
                entity_encoding: 'raw',
                extended_valid_elements: 'pre[*],script[*],style[*]',
                height: 350,
                verify_html: false,
                force_p_newlines: false,
                content_style: "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'); body { font-family: 'Inter', sans-serif; font-size: 14px; color: #334155; line-height: 1.6; padding: 20px; }",
                setup: function (editor) {
                    editor.on('change', function () {
                        tinymce.triggerSave();
                    });
                }
            });
        }, 100);
    }

    // Re-init image selector
    if (typeof initImageSelector === 'function') {
        setTimeout(function() { initImageSelector(); }, 200);
    }
}

// ===== DROPDOWN MENUS =====
function toggleCatDropdown(btn, evt) {
    if (evt) { evt.stopPropagation(); evt.preventDefault(); }
    var submenu = btn.nextElementSibling;
    if (!submenu) return;
    var isOpen = submenu.style.display === 'block';
    closeCatDropdowns();
    if (!isOpen) submenu.style.display = 'block';
}

function closeCatDropdowns() {
    document.querySelectorAll('.cat-submenu').forEach(function(m) { m.style.display = 'none'; });
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.cat-btn') && !e.target.closest('.cat-submenu')) closeCatDropdowns();
    if (!e.target.closest('.country-add-wrapper')) {
        document.querySelectorAll('.country-dropdown').forEach(function(dd) {
            dd.style.display = 'none';
        });
    }
});

// ===== COUNTRY DROPDOWN =====
function toggleCountryDropdown(dayNumber) {
    var dd = document.getElementById('country_dropdown_' + dayNumber);
    if (dd) {
        dd.style.display = (dd.style.display === 'none' || dd.style.display === '') ? 'block' : 'none';
    }
}

function loadCountryCategories(dayNumber, countryId, countryName) {
    var dd = document.getElementById('country_dropdown_' + dayNumber);
    if (dd) dd.style.display = 'none';
    document.getElementById('expense_day_number').value = dayNumber;
    document.getElementById('modal_search').value = '';
    resetModalRightPanel();
    document.getElementById('modal_left_panel').innerHTML = '<div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-py-20 tw-gap-4"><i class="fa fa-spinner fa-spin tw-text-2xl tw-text-orange-500"></i></div>';
    window.location.hash = 'expense_modal';
    
    fetch('/admin/ajax/get-country-categories?country_id=' + countryId)
        .then(function(r) { return r.json(); })
        .then(function(categories) {
            var html = '<div class="tw-flex tw-items-center tw-gap-3 tw-p-3 tw-rounded-xl tw-bg-white tw-border tw-border-slate-100 tw-mb-4"><input type="radio" name="modal_cat_radio" value="0" checked onclick="resetModalRightPanel();"> <span class="tw-text-sm tw-font-bold tw-text-slate-700">All Parents</span></div>';
            categories.forEach(function(cat) {
                var hasChildren = cat.has_children;
                html += '<div class="modal-cat-item tw-mb-2" data-name="' + (cat.name || '').toLowerCase() + '">';
                html += '<div class="tw-flex tw-items-center tw-gap-3">';
                if (hasChildren) {
                    html += '<span class="modal-toggle tw-cursor-pointer tw-text-slate-400 hover:tw-text-orange-500" onclick="modalToggle(this);"><i class="fa fa-plus-square-o"></i></span> ';
                } else {
                    html += '<span class="tw-w-4"></span>';
                }
                html += '<label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer"><input type="radio" name="modal_cat_radio" value="' + cat.id + '" onclick="loadModalServices(' + cat.id + ');" class="!tw-w-4 !tw-h-4"> <span class="tw-text-sm tw-font-medium tw-text-slate-600">' + cat.name + '</span></label></div>';
                if (hasChildren) {
                    html += '<div class="modal-children tw-hidden tw-pl-6 tw-mt-2 tw-border-l tw-border-slate-100 tw-ml-2" data-parent="' + cat.id + '"></div>';
                }
                html += '</div>';
            });
            document.getElementById('modal_left_panel').innerHTML = html;
        })
        .catch(function(err) { console.error(err); });
}

// ===== EXPENSE MODAL & SERVICES =====
function resetModalRightPanel() {
    document.getElementById('modal_right_header').innerHTML = '';
    document.getElementById('modal_right_vendor').innerHTML = '';
    document.getElementById('modal_right_table').querySelector('tbody').innerHTML = '';
}

function loadSubCategoryServices(dayNumber, categoryId, categoryName) {
    document.getElementById('expense_day_number').value = dayNumber;
    document.getElementById('expense_parent_cat_id').value = categoryId;
    document.getElementById('modal_search').value = '';
    window.location.hash = 'expense_modal';
    loadModalChildren(categoryId);
    loadModalServices(categoryId);
}

function loadModalChildren(parentCatId) {
    document.getElementById('modal_left_panel').innerHTML = '<div class="tw-flex tw-items-center tw-justify-center tw-py-20"><i class="fa fa-spinner fa-spin tw-text-orange-500"></i></div>';
    fetch('/admin/ajax/get-subcategories?parent_id=' + parentCatId)
        .then(function(r) { return r.json(); })
        .then(function(children) {
            var html = '<div class="tw-flex tw-items-center tw-gap-3 tw-p-3 tw-rounded-xl tw-bg-white tw-border tw-border-slate-100 tw-mb-4"><input type="radio" name="modal_cat_radio" value="0" checked onclick="resetModalRightPanel();"> <span class="tw-text-sm tw-font-bold tw-text-slate-700">Root Categories</span></div>';
            children.forEach(function(child) {
                html += '<div class="modal-cat-item tw-mb-2" data-name="' + (child.name || '').toLowerCase() + '">';
                html += '<div class="tw-flex tw-items-center tw-gap-3">';
                if (child.has_children) {
                    html += '<span class="modal-toggle tw-cursor-pointer" onclick="modalToggle(this);"><i class="fa fa-plus-square-o"></i></span> ';
                } else {
                    html += '<span class="tw-w-4"></span>';
                }
                html += '<label class="tw-flex tw-items-center tw-gap-2"><input type="radio" name="modal_cat_radio" value="' + child.id + '" onclick="loadModalServices(' + child.id + ');"> <span class="tw-text-sm tw-font-medium tw-text-slate-600">' + child.name + '</span></label></div>';
                if (child.has_children) {
                    html += '<div class="modal-children tw-hidden tw-pl-6 tw-mt-2" data-parent="' + child.id + '"></div>';
                }
                html += '</div>';
            });
            document.getElementById('modal_left_panel').innerHTML = html;
        })
        .catch(function(err) { console.error(err); });
}

function loadModalServices(categoryId, venderFilter) {
    var url = '/admin/expenses/services?category=' + categoryId;
    if (venderFilter) url += '&vender=' + venderFilter;

    document.getElementById('modal_right_table').querySelector('tbody').innerHTML = '<tr><td colspan="4" class="tw-py-20 tw-text-center"><i class="fa fa-spinner fa-spin tw-text-3xl tw-text-orange-500"></i></td></tr>';

    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            document.getElementById('modal_right_header').innerHTML = '<div class="tw-flex tw-items-center tw-gap-2 tw-text-orange-600 tw-font-bold tw-text-sm"><i class="fa fa-cubes"></i> ' + data.categoryName + '</div>';
            
            var vHtml = '<div class="tw-flex tw-items-center tw-gap-4"><label class="tw-text-[11px] tw-font-bold tw-uppercase tw-text-slate-400">Filter Vendor</label><select onchange="loadModalServices(' + categoryId + ', this.value);" class="!tw-h-10 !tw-text-xs !tw-w-64">';
            vHtml += '<option value="">All Registered Vendors</option>';
            if (data.vendors) {
                for (var vid in data.vendors) {
                    var sel = (venderFilter && venderFilter == vid) ? ' selected' : '';
                    vHtml += '<option value="' + vid + '"' + sel + '>' + data.vendors[vid] + '</option>';
                }
            }
            vHtml += '</select></div>';
            document.getElementById('modal_right_vendor').innerHTML = vHtml;

            var tHtml = '';
            if (data.services && data.services.length > 0) {
                data.services.forEach(function(s) {
                    tHtml += '<tr class="tw-border-b tw-border-slate-50 hover:tw-bg-slate-50/50 tw-transition-colors">';
                    tHtml += '<td class="tw-py-4 tw-px-8"><div class="tw-text-xs tw-font-bold tw-text-slate-800">' + s.description + '</div></td>';
                    tHtml += '<td class="tw-py-4 tw-px-4"><div class="tw-text-xs tw-font-black tw-text-emerald-600">' + s.cost + ' <small class="tw-text-[8px] tw-text-slate-400">JOD</small></div></td>';
                    tHtml += '<td class="tw-py-4 tw-px-4"><div class="tw-text-xs tw-text-slate-500 tw-font-medium">' + (s.vender_name || 'Generic') + '</div></td>';
                    tHtml += '<td class="tw-py-4 tw-px-8 tw-text-right"><button type="button" class="tw-px-4 tw-py-2 tw-bg-orange-50 tw-text-orange-600 tw-text-[11px] tw-font-black tw-rounded-xl hover:tw-bg-orange-600 hover:tw-text-white tw-transition-all modal-select-btn" data-sid="' + s.id + '" data-sdesc="' + (s.description || '').replace(/"/g, '&quot;') + '" data-scost="' + (s.cost_raw || 0) + '" data-svender="' + (s.vender_name || '').replace(/"/g, '&quot;') + '"><i class="fa fa-plus"></i> Select</button></td>';
                    tHtml += '</tr>';
                });
            } else {
                tHtml = '<tr><td colspan="4" class="tw-py-20 tw-text-center tw-text-slate-400 tw-text-sm tw-font-medium">No active services found in this category</td></tr>';
            }
            document.getElementById('modal_right_table').querySelector('tbody').innerHTML = tHtml;

            document.querySelectorAll('.modal-select-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var sId = this.getAttribute('data-sid');
                    var sDesc = this.getAttribute('data-sdesc');
                    var sCost = this.getAttribute('data-scost') || 0;
                    var sVender = this.getAttribute('data-svender') || '';
                    var dayNumber = document.getElementById('expense_day_number').value;
                    addServiceExpense(dayNumber, sId, sDesc, sCost, sVender);
                    
                    // Visual feedback
                    this.innerHTML = '<i class="fa fa-check"></i> Added';
                    this.classList.remove('tw-bg-orange-50', 'tw-text-orange-600');
                    this.classList.add('tw-bg-emerald-500', 'tw-text-white');
                    
                    this.disabled = true;
                });
            });
        })
        .catch(function(err) { console.error(err); });
}

function modalToggle(el) {
    var children = el.parentElement.nextElementSibling;
    if (children && children.classList.contains('modal-children')) {
        var isHidden = children.classList.contains('tw-hidden');
        if (isHidden) {
            children.classList.remove('tw-hidden');
            el.querySelector('i').className = 'fa fa-minus-square-o';
            if (children.innerHTML === '') {
                var parentId = children.getAttribute('data-parent');
                fetch('/admin/ajax/get-subcategories?parent_id=' + parentId)
                    .then(function(r) { return r.json(); })
                    .then(function(subs) {
                        var html = '';
                        subs.forEach(function(sub) {
                            html += '<div class="modal-cat-item tw-mb-2" data-name="' + (sub.name || '').toLowerCase() + '">';
                            html += '<div class="tw-flex tw-items-center tw-gap-3"><label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer"><input type="radio" name="modal_cat_radio" value="' + sub.id + '" onclick="loadModalServices(' + sub.id + ');"> <span class="tw-text-sm tw-font-medium tw-text-slate-600">' + sub.name + '</span></label></div>';
                            html += '</div>';
                        });
                        children.innerHTML = html;
                    });
            }
        } else {
            children.classList.add('tw-hidden');
            el.querySelector('i').className = 'fa fa-plus-square-o';
        }
    }
}

function modalCollapseAll() {
    document.querySelectorAll('.modal-children').forEach(function(el) { el.classList.add('tw-hidden'); });
    document.querySelectorAll('.modal-toggle i').forEach(function(el) { el.className = 'fa fa-plus-square-o'; });
}

function modalExpandAll() {
    document.querySelectorAll('.modal-children').forEach(function(el) { el.classList.remove('tw-hidden'); });
    document.querySelectorAll('.modal-toggle i').forEach(function(el) { el.className = 'fa fa-minus-square-o'; });
}

function filterModalItems() {
    var query = document.getElementById('modal_search').value.toLowerCase();
    document.querySelectorAll('.modal-cat-item').forEach(function(item) {
        var name = item.getAttribute('data-name');
        item.style.display = (!query || (name && name.indexOf(query) !== -1)) ? '' : 'none';
    });
}

function addServiceExpense(dayNumber, serviceId, description, cost, vender) {
    var expList = document.getElementById('expense_list_' + dayNumber);
    var key = Date.now() + Math.floor(Math.random() * 1000);
    cost = cost || 0;
    vender = vender || '';
    // Default date from day header
    var dayHeader = document.querySelector('#day_sections_container > div:nth-child(' + dayNumber + ') span');
    var defaultDate = '';
    if (dayHeader) { var parts = dayHeader.textContent.split(' - '); defaultDate = parts[1] ? parts[1].trim() : ''; }
    
    var div = document.createElement('div');
    div.className = 'exp-row-item';
    div.style.cssText = 'display:flex;align-items:center;gap:6px;padding:6px 0;border-bottom:1px solid #eee;font-size:11px;';
    div.innerHTML = `
        <div style="flex:1;overflow:hidden;">
            <div style="font-weight:600;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${description}">${description}</div>
        </div>
        <input type="number" name="expenses_qty_${dayNumber}[${key}]" value="1" min="1" style="width:40px;padding:3px;border:1px solid #ccc;border-radius:2px;font-size:11px;text-align:center;">
        <input type="number" name="expenses_cost_${dayNumber}[${key}]" value="${cost}" min="0" step="0.01" style="width:60px;padding:3px;border:1px solid #ccc;border-radius:2px;font-size:11px;text-align:center;color:#27ae60;font-weight:700;">
        <input type="hidden" name="expenses_day_${dayNumber}[${key}]" value="${serviceId}">
        <input type="hidden" name="expenses_name_${dayNumber}[${key}]" value="${description}">
        <input type="hidden" name="expenses_date_${dayNumber}[${key}]" value="${defaultDate}">
        <button type="button" onclick="this.parentElement.remove();" style="width:20px;height:20px;background:#e74c3c;color:#fff;border:none;border-radius:2px;font-size:9px;cursor:pointer;flex-shrink:0;">✕</button>
    `;
    expList.appendChild(div);
}

// ===== INCLUSIONS =====
function addInclusion(day) {
    document.getElementById('inclusion_day').value = day;
    document.getElementById('inclusion_search').value = '';
    filterInclusions();
    window.location.hash = 'add_inclusion';
}

function filterInclusions() {
    var search = document.getElementById('inclusion_search').value.toLowerCase();
    var rows = document.querySelectorAll('.inclusion-row');
    rows.forEach(function(row) {
        var name = row.getAttribute('data-name');
        row.style.display = name.indexOf(search) !== -1 ? 'flex' : 'none';
    });
}

function addInclusionItem(btn, type, text) {
    var day = document.getElementById('inclusion_day').value;
    var containerId = type === 'included' ? 'day_inc_' + day : 'day_exc_' + day;
    var container = document.getElementById(containerId);
    
    var isInc = type === 'included';
    var icon = isInc ? '✓' : '✗';
    var color = isInc ? '#27ae60' : '#e74c3c';
    var inputName = isInc ? 'day_inc_' + day : 'day_exc_' + day;
    
    var key = Date.now() + Math.floor(Math.random() * 1000);
    
    var div = document.createElement('div');
    div.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:4px 0;border-bottom:1px solid #f0f0f0;font-size:12px;';
    div.innerHTML = `
        <span style="color:${color};font-weight:500;display:flex;align-items:center;gap:4px;"><span style="font-weight:700;">${icon}</span> ${text}</span>
        <input type="hidden" name="${inputName}[${key}]" value="${text}">
        <button type="button" onclick="this.parentElement.remove();" style="width:18px;height:18px;background:#e74c3c;color:#fff;border:none;border-radius:2px;font-size:9px;cursor:pointer;">✕</button>
    `;
    container.appendChild(div);
}

// Global submit handler with validation
document.getElementById('quotation_form').addEventListener('submit', function(e) {
    if (typeof tinymce !== 'undefined') {
        tinymce.triggerSave();
    }
    // Validate: all cost fields must be > 0
    var costFields = document.querySelectorAll('input[name*="expenses_cost_"]');
    var invalid = false;
    var errorDiv = document.getElementById('service_validation_error');
    costFields.forEach(function(field) {
        var val = parseFloat(field.value);
        if (isNaN(val) || val <= 0) {
            field.style.border = '2px solid #ef4444';
            field.style.background = '#fef2f2';
            invalid = true;
        } else {
            field.style.border = '';
            field.style.background = '';
        }
    });
    if (invalid) {
        e.preventDefault();
        if (errorDiv) {
            errorDiv.style.display = 'flex';
            errorDiv.scrollIntoView({behavior: 'smooth', block: 'center'});
        }
        return false;
    }
    if (errorDiv) errorDiv.style.display = 'none';
});

// Load initial day
document.addEventListener('DOMContentLoaded', function() { buildDaySections(); });
</script>

{{-- Libraries --}}
<script type="text/javascript" src="/assets/admin/tinymce/tinymce.min.js"></script>
<script src="/assets/admin/gogiesfm/image_selector.js"></script>
@endsection
