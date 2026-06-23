@extends('admin.layouts.app')

@section('title', 'Admin | Services')

@push('head')
@if($countryId && isset($countries[$countryId]))
<style>
    /* Ultra-Modern Material Treeview */
    .treeview, .treeview ul { padding: 0; margin: 0; list-style: none; }
    .treeview ul { padding-left: 8px; margin-top: 4px; border-left: 1px solid #e2e8f0; margin-left: 10px; }
    .treeview li { margin: 4px 0; padding: 0; position: relative; }
    
    /* The clickable text container */
    .treeview li a, .treeview li label {
        text-decoration: none;
        color: #475569;
        font-size: 13.5px;
        font-weight: 600;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        padding: 8px 12px 8px 30px; /* Space for the absolute hitarea */
        border-radius: 10px;
        cursor: pointer;
        white-space: normal;
        word-break: break-word;
        line-height: 1.4;
        box-sizing: border-box;
        border: 1px solid transparent;
        width: 100%;
    }
    .treeview li a:hover, .treeview li label:hover {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #ea580c;
    }
    
    /* Dynamic Folder Icons via CSS */
    .treeview li a::before, .treeview li label::before {
        content: '\f111'; /* default leaf dot */
        font-family: 'FontAwesome';
        margin-right: 8px;
        font-size: 6px;
        color: #cbd5e1;
        transition: all 0.2s;
    }
    .treeview li.expandable > a::before, .treeview li.expandable > label::before {
        content: '\f07b'; /* fa-folder */
        font-size: 13px;
        color: #f59e0b; /* yellow/amber */
    }
    .treeview li.collapsable > a::before, .treeview li.collapsable > label::before {
        content: '\f07c'; /* fa-folder-open */
        font-size: 13px;
        color: #f59e0b; /* yellow/amber */
    }
    .treeview li a.active-cat::before {
        color: #ffffff;
    }
    .treeview li a.is-star-category::before, .treeview li label.is-star-category::before {
        content: '\f005' !important; /* fa-star */
        font-size: 13px !important;
        color: #f59e0b !important; /* yellow/amber */
    }

    /* Active Category specific to this page */
    .active-cat {
        background: linear-gradient(135deg, #f97316, #ea580c) !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        box-shadow: 0 4px 12px -2px rgba(234, 88, 12, 0.3);
        border-color: transparent !important;
    }

    /* Hitarea (Expand/Collapse toggler) */
    .treeview .hitarea {
        position: absolute;
        left: 0px;
        top: 8px; /* Adjusted for alignment */
        width: 26px;
        height: 26px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        border: none;
        z-index: 10;
        transition: all 0.2s;
        border-radius: 6px;
    }
    .treeview .hitarea:hover { background-color: #e2e8f0; }
    
    /* Modern Chevrons instead of Plus/Minus blocks */
    .treeview .hitarea::after {
        content: '\f105'; /* fa-angle-right (expandable) */
        font-family: 'FontAwesome';
        font-size: 16px;
        color: #94a3b8;
        transition: transform 0.2s;
    }
    .treeview .collapsable-hitarea::after {
        content: '\f107'; /* fa-angle-down (collapsable) */
        color: #ea580c;
    }
    
    /* Sub-tree hiding */
    .treeview li.expandable > ul { display: none; }
    
    /* Hiding elements during search */
    .hide { display: none !important; }

    /* Custom thin scrollbar for tree */
    .tree-scroll {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    .tree-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .tree-scroll::-webkit-scrollbar-track { background: transparent; }
    .tree-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
</style>
@endif
@endpush

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8 lg:tw-gap-10 tw-max-w-[1400px] tw-mx-auto tw-pb-20">

    {{-- Breadcrumb --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">
        <i class="fa fa-pie-chart"></i> Quotations
        <i class="fa fa-angle-right"></i>
        <span class="tw-text-orange-600">Manage Services</span>
    </div>

    {{-- Header Section --}}
    <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-items-start md:tw-items-center tw-gap-6 tw-bg-white tw-p-8 tw-rounded-[2.5rem] tw-shadow-sm tw-border tw-border-slate-100">
        <div>
            <h1 class="tw-text-3xl tw-font-black tw-text-slate-900 tw-tracking-tight tw-m-0">Manage <span class="tw-text-orange-600">Services</span></h1>
            <p class="tw-text-sm tw-font-medium tw-text-slate-500 tw-mt-2 tw-mb-0">Configure expense services, core categories, and routing directly by country.</p>
        </div>
        <div class="tw-flex tw-items-center tw-gap-3">
            <select id="country_select" class="!tw-w-48 !tw-rounded-xl !tw-border-slate-200">
                <option value="">Select Country</option>
                @foreach($countries as $cid => $cname)
                <option value="{{ $cid }}" {{ $countryId == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                @endforeach
            </select>
            @if($countryId)
            <button type="button" onclick="openAddCategoryModal({{ $countryId }});" class="btn orange !tw-px-5 !tw-rounded-xl outline-none">
                <i class="fa fa-folder-plus tw-mr-1"></i> Category
            </button>
            @endif
            <a href="{{ route('admin.services.venders') }}" class="btn orange !tw-px-5 !tw-rounded-xl outline-none">
                <i class="fa fa-briefcase tw-mr-1"></i> Vendors
            </a>
            <a href="{{ route('admin.services.settings') }}" class="tw-w-10 tw-h-10 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-slate-50 tw-text-slate-600 hover:tw-bg-slate-200 tw-border tw-border-slate-200 tw-transition-all tw-no-underline" title="Settings">
                <i class="fa fa-gear"></i>
            </a>
        </div>
    </div>

@if($countryId && isset($countries[$countryId]))
    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-12 tw-gap-6 lg:tw-gap-8">
        {{-- Left Tree Sidebar --}}
        <div class="lg:tw-col-span-5 xl:tw-col-span-4">
            <div class="tw-bg-white tw-rounded-[2rem] tw-shadow-md tw-shadow-slate-200/50 tw-border tw-border-slate-100 tw-overflow-hidden tw-flex tw-flex-col">
                @if($tree)
                <div class="tw-relative tw-bg-gradient-to-r tw-from-orange-600 tw-to-orange-600 tw-px-5 tw-py-6">
                    <div class="tw-absolute tw-inset-0 tw-bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] tw-opacity-10 tw-mix-blend-overlay"></div>
                    <div class="tw-relative tw-flex tw-items-center tw-justify-between tw-mb-4">
                        <span class="tw-text-xs tw-font-bold tw-text-orange-100 tw-uppercase tw-tracking-widest"><i class="fa fa-sitemap tw-mr-1"></i> Structure</span>
                        <div id="sidetreecontrol" class="tw-flex tw-gap-1">
                            <a id="close-all" class="tw-w-7 tw-h-7 tw-flex tw-items-center tw-justify-center tw-rounded-[0.5rem] tw-bg-white/10 tw-text-white hover:tw-bg-white/20 tw-transition-all tw-no-underline" href="?#" title="Collapse All"><i class="fa fa-compress tw-text-[10px]"></i></a>
                            <a id="open-all" class="tw-w-7 tw-h-7 tw-flex tw-items-center tw-justify-center tw-rounded-[0.5rem] tw-bg-white/10 tw-text-white hover:tw-bg-white/20 tw-transition-all tw-no-underline" href="?#" title="Expand All"><i class="fa fa-expand tw-text-[10px]"></i></a>
                        </div>
                    </div>
                    <div class="tw-relative tw-shadow-sm tw-rounded-xl">
                        <i class="fa fa-search tw-absolute tw-text-orange-400 tw-text-xs" style="left: 14px; top: 12px;"></i>
                        <input type="text" placeholder="Search categories..." id="search_services" class="tw-w-full tw-py-2.5 tw-pr-4 tw-text-[13px] tw-font-semibold tw-text-slate-700 tw-rounded-xl tw-border-none focus:tw-ring-2 focus:tw-ring-orange-300 tw-outline-none tw-bg-white tw-shadow-inner" style="padding-left: 36px !important;">
                    </div>
                </div>
                @endif
                <div class="tw-p-5 tw-max-h-[650px] tw-overflow-x-hidden tw-overflow-y-auto tree-scroll tw-bg-slate-50/30">
                    <ul id="tree" class="treeview">{!! $tree !!}</ul>
                </div>
            </div>
        </div>

        {{-- Right Content Area --}}
        <div class="lg:tw-col-span-7 xl:tw-col-span-8">
            <div id="services_container">
                @if($categoryId)
                    <div class="tw-flex tw-items-center tw-justify-center tw-py-20">
                        <i class="fa fa-spinner fa-spin tw-text-4xl tw-text-orange-500"></i>
                    </div>
                @else
                    <div class="box !tw-p-0 !tw-overflow-hidden">
                        <div class="tw-px-8 tw-py-5 tw-bg-orange-50/50 tw-border-b tw-border-orange-100 tw-flex tw-items-center tw-gap-3">
                            <i class="fa fa-cubes tw-text-orange-600"></i>
                            <span class="tw-text-sm tw-font-bold tw-text-orange-800">{{ $countries[$countryId] }}</span>
                        </div>
                        <div class="tw-flex tw-flex-col tw-items-center tw-py-20 tw-gap-4">
                            <div class="tw-w-16 tw-h-16 tw-rounded-full tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-200">
                                <i class="fa fa-arrow-left tw-text-3xl"></i>
                            </div>
                            <p class="tw-text-slate-400 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest">Select a category from the tree</p>
                        </div>
                    </div>
                @endif
            </div>
@else
    {{-- No country selected --}}
    @php
        $colorMap = ['#ea580c', '#0ea5e9', '#ea580c', '#f59e0b', '#10b981', '#ef4444', '#ec4899'];
        $bgColorMap = ['tw-bg-orange-50', 'tw-bg-sky-50', 'tw-bg-orange-50', 'tw-bg-amber-50', 'tw-bg-emerald-50', 'tw-bg-rose-50', 'tw-bg-pink-50'];
    @endphp
    
    <div class="tw-bg-white tw-rounded-[3rem] tw-shadow-xl tw-shadow-slate-200/40 tw-p-12 tw-border tw-border-slate-100 tw-overflow-hidden">
        <div class="tw-text-center tw-mb-12">
            <div class="tw-w-24 tw-h-24 tw-rounded-[2rem] tw-bg-orange-50 tw-flex tw-items-center tw-justify-center tw-text-orange-500 tw-mx-auto tw-mb-6 tw-shadow-inner hover:-tw-translate-y-2 tw-transition-all tw-duration-500">
                <i class="fa fa-globe tw-text-5xl"></i>
            </div>
            <h3 class="tw-text-3xl tw-font-black tw-text-slate-900 tw-tracking-tight">Select a Destination</h3>
            <p class="tw-text-slate-500 tw-text-base tw-font-medium tw-mt-2">Choose a country to manage its expense services and core categories</p>
        </div>

        <div class="tw-grid tw-grid-cols-2 sm:tw-grid-cols-3 lg:tw-grid-cols-4 tw-gap-6 tw-max-w-5xl tw-mx-auto">
            @foreach($countries as $cid => $cname)
            @php
                $loopColor = $colorMap[$loop->index % count($colorMap)];
                $loopBg = $bgColorMap[$loop->index % count($bgColorMap)];
            @endphp
            <a href="{{ route('admin.services.index', ['country' => $cid]) }}" class="group tw-relative tw-p-6 tw-bg-white tw-border tw-border-slate-200 tw-rounded-[2rem] hover:tw-border-transparent hover:tw-shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] tw-transition-all tw-duration-300 tw-no-underline tw-flex tw-flex-col tw-items-center tw-justify-center tw-text-center hover:-tw-translate-y-1.5 tw-overflow-hidden">
                <div class="tw-absolute tw-inset-0 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-opacity tw-duration-300" style="background: linear-gradient(135deg, {{ $loopColor }}0A, transparent);"></div>
                <div class="tw-absolute tw-bottom-0 tw-left-0 tw-w-full tw-h-1.5 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-opacity tw-duration-300" style="background: {{ $loopColor }};"></div>
                <div class="tw-w-14 tw-h-14 tw-rounded-2xl {{ $loopBg }} tw-flex tw-items-center tw-justify-center tw-mb-4 tw-transition-transform group-hover:tw-scale-110 tw-duration-500" style="color: {{ $loopColor }};">
                    <i class="fa fa-map-marker tw-text-[28px]"></i>
                </div>
                <span class="tw-text-[15px] tw-font-black tw-text-slate-700 group-hover:tw-text-slate-900 tw-transition-colors">{{ $cname }}</span>
            </a>
            @endforeach
        </div>
    </div>
@endif
</div>

<div id="ajax_req"></div>

{{-- Category Modal (Add/Edit) --}}
<div class="modal" id="cat_modal">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[900px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-folder-open tw-text-orange-400"></i> Category Management
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <div id="cat_modal_content" class="tw-p-8"></div>
    </div>
</div>

{{-- Add Service Modal --}}
<div class="modal" id="add_service">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[900px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-orange-400"></i> Add Service
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <div id="add_service_content" class="tw-p-8"></div>
    </div>
</div>

{{-- Edit Service Modal --}}
<div class="modal" id="edit_service">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[900px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-edit tw-text-orange-400"></i> Edit Service
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <div id="edit_service_content" class="tw-p-8"></div>
    </div>
</div>

{{-- Seasons Modal --}}
<div class="modal" id="seasons">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[700px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-calendar tw-text-orange-400"></i> Season Pricing
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <div id="seasons_content" class="tw-p-8"></div>
    </div>
</div>
@endsection

@push('scripts')
@if($countryId && isset($countries[$countryId]))
<script src="{{ asset('gogies3d/assist/jquery.treeview.js') }}"></script>
<script>
$(function() {
    // ===== TREEVIEW INIT =====
    if ($("#tree").length && typeof $.fn.treeview !== 'undefined') {
        $("#tree").treeview({
            collapsed: true,
            animated: "medium",
            control: "#sidetreecontrol"
        });
    }

    // ===== STAR ICONS FOR TREE =====
    $('.treeview li a, .treeview li label').each(function() {
        if ($(this).text().toLowerCase().indexOf('star') !== -1) {
            $(this).addClass('is-star-category');
        }
    });

    // ===== AUTO-LOAD CATEGORY IF SET =====
    @if($categoryId)
    currentCategoryId = '{{ $categoryId }}';
    load_services('{{ $categoryId }}', 0, 1);
    // Expand parent nodes and highlight active
    var $activeLi = $('#category_{{ $categoryId }}');
    $activeLi.parents('ul').show();
    $activeLi.parents('li').each(function() {
        $(this).addClass('collapsable').removeClass('expandable');
        $(this).find('> .hitarea').addClass('collapsable-hitarea').removeClass('expandable-hitarea');
    });
    $activeLi.find('> a').addClass('active-cat');
    @endif
});

// ===== COUNTRY SELECTOR =====
$('#country_select').on('change', function() {
    var newCountryId = $(this).val();
    if (newCountryId) {
        window.location = '{{ route("admin.services.index") }}?country=' + newCountryId;
    }
});

// ===== CURRENT STATE =====
var currentCountryId = '{{ $countryId }}';
var currentCategoryId = 0;

// ===== CATEGORY TREE CLICK =====
$(document).on('click', '.get_category', function(e) {
    e.preventDefault();
    var categ_id = $(this).attr('data-id');
    currentCategoryId = categ_id;

    // Highlight active category
    $('.get_category').removeClass('active-cat');
    $(this).addClass('active-cat');

    load_services(categ_id, 0, 1);
    history.pushState(null, 'Admin | Services', '{{ route("admin.services.index") }}?country={{ $countryId }}&category=' + categ_id);
    return false;
});

// ===== LOAD SERVICES VIA AJAX =====
function load_services(categ_id, vender, page) {
    currentCategoryId = categ_id;
    $('#services_container').html('<div class="tw-flex tw-items-center tw-justify-center tw-py-20"><i class="fa fa-spinner fa-spin tw-text-4xl tw-text-orange-500"></i></div>');
    $.get('{{ route("admin.services.ajax") }}', {
        c: categ_id,
        country: '{{ $countryId }}',
        vender: vender || 0,
        page: page || 1
    }, function(data) {
        $('#services_container').html(data.html);
        bind_service_events(categ_id);
    }).fail(function() {
        $('#services_container').html('<div class="tw-flex tw-items-center tw-justify-center tw-py-20 tw-text-rose-500 tw-font-bold"><i class="fa fa-warning tw-mr-2"></i> Error loading services.</div>');
    });
}

// ===== BIND SERVICE PANEL EVENTS =====
function bind_service_events(categ_id) {
    // Vender filter
    $('.get_vender').off('change').on('change', function() {
        load_services(categ_id, $(this).val(), 1);
    });

    // Pagination
    $('#services_container').off('click', '[data-page]').on('click', '[data-page]', function(e) {
        e.preventDefault();
        var page = $(this).attr('href');
        load_services(categ_id, $('.get_vender').val() || 0, page);
    });

    // Delete service
    $(document).off('click', '.del-service-btn').on('click', '.del-service-btn', function() {
        var sid = $(this).data('id');
        var desc = $(this).data('desc');
        if (confirm('Are you sure you want to delete (' + desc + ')?')) {
            $.ajax({
                url: '{{ url("admin/services") }}/' + sid,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function() {
                    load_services(categ_id, $('.get_vender').val() || 0, 1);
                },
                error: function() {
                    alert('Error deleting service');
                }
            });
        }
    });

    // Seasons modal
    $(document).off('click', '.seasons-btn').on('click', '.seasons-btn', function() {
        var serviceId = $(this).data('id');
        load_seasons(serviceId);
    });

    // Edit service
    $(document).off('click', '.edit-service-btn').on('click', '.edit-service-btn', function() {
        var serviceId = $(this).data('id');
        openEditServiceModal(serviceId);
    });
}

// ===== SEASONS =====
var currentSeasonServiceId = 0;

function load_seasons(serviceId) {
    currentSeasonServiceId = serviceId;
    $('#seasons_content').html('<div class="tw-flex tw-items-center tw-justify-center tw-py-12"><i class="fa fa-spinner fa-spin tw-text-3xl tw-text-orange-500"></i></div>');
    window.location.hash = 'seasons';
    $.get('{{ url("admin/services") }}/' + serviceId + '/seasons', function(data) {
        $('#seasons_content').html(data.html);
        bind_season_events(serviceId);
    }).fail(function() {
        $('#seasons_content').html('<div class="tw-bg-rose-50 tw-text-rose-600 tw-p-4 tw-rounded-xl tw-text-xs tw-font-bold"><i class="fa fa-warning tw-mr-2"></i> Error loading seasons.</div>');
    });
}

function bind_season_events(serviceId) {
    $('#add_season_btn').off('click').on('click', function() {
        var $from = $('#season_from');
        var $to = $('#season_to');
        var $cost = $('#season_cost');
        var from = $from.val();
        var to = $to.val();
        var cost = $cost.val();
        
        // Reset errors
        $('#season_error_msg').hide();
        $from.css('border-color', '');
        $to.css('border-color', '');

        if (!from || !to) { 
            $('#season_error_msg').show().find('span').text('Please enter From and To dates');
            if(!from) $from.css('border-color', '#ef4444');
            if(!to) $to.css('border-color', '#ef4444');
            return; 
        }

        $.ajax({
            url: '{{ url("admin/services") }}/' + serviceId + '/seasons',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                date_from: from,
                date_to: to,
                cost: cost || 0
            },
            success: function(res) {
                if (res && res.success === false && res.error) {
                    $('#season_error_msg').show().find('span').text(res.error);
                    $from.css('border-color', '#ef4444');
                    $to.css('border-color', '#ef4444');
                } else {
                    load_seasons(serviceId);
                }
            },
            error: function() {
                $('#season_error_msg').show().find('span').text('Error adding season');
            }
        });
    });

    $('.del-season-btn').off('click').on('click', function() {
        var seasonId = $(this).data('id');
        if (confirm('Delete this season?')) {
            $.ajax({
                url: '{{ url("admin/services/seasons") }}/' + seasonId,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function() {
                    load_seasons(serviceId);
                },
                error: function() {
                    alert('Error deleting season');
                }
            });
        }
    });
}

// ===== SEARCH IN TREE =====
$('#search_services').keyup(function() {
    var searchText = $(this).val().toLowerCase();
    if (searchText === '') {
        $('#close-all').trigger('click');
    } else {
        $('#open-all').trigger('click');
    }
    $('#tree li').each(function() {
        var currentLiText = $(this).children('a').text().toLowerCase();
        if (currentLiText.indexOf(searchText) !== -1) {
            $(this).removeClass('hide');
        } else {
            $(this).addClass('hide');
        }
    });
});

// ===== CATEGORY MANAGEMENT =====
function openAddCategoryModal(countryId) {
    $('#cat_modal_content').html('<div class="tw-flex tw-items-center tw-justify-center tw-py-12"><i class="fa fa-spinner fa-spin tw-text-3xl tw-text-orange-500"></i></div>');
    window.location.hash = 'cat_modal';
    $.get('{{ route("admin.services.category.create") }}', { country: countryId }, function(data) {
        $('#cat_modal_content').html(data.html);
    });
}

function openEditCategoryModal(categoryId) {
    $('#cat_modal_content').html('<div class="tw-flex tw-items-center tw-justify-center tw-py-12"><i class="fa fa-spinner fa-spin tw-text-3xl tw-text-orange-500"></i></div>');
    window.location.hash = 'cat_modal';
    $.get('{{ url("admin/services-category") }}/' + categoryId + '/edit', function(data) {
        $('#cat_modal_content').html(data.html);
    });
}

function categorySubmit(categoryId) {
    var formId = categoryId ? '#edit_category_form' : '#add_category_form';
    var url = categoryId ? '{{ url("admin/services-category") }}/' + categoryId + '/update' : '{{ route("admin.services.store-category") }}';
    var formData = $(formId).serialize();

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        success: function(data) {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error saving category');
            }
        },
        error: function(xhr) {
            alert('Error saving category');
        }
    });
}

function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category?')) {
        $.ajax({
            url: '{{ url("admin/services-category") }}/' + categoryId,
            type: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(data) {
                if (data.success) {
                    window.location = '{{ route("admin.services.index", ["country" => $countryId]) }}';
                } else {
                    alert(data.message || 'Error deleting category');
                }
            },
            error: function() {
                alert('Error deleting category');
            }
        });
    }
}

// ===== ADD SERVICE MODAL =====
var currentAddServiceCategory = 0;

function openAddServiceModal(categoryId, countryId) {
    currentAddServiceCategory = categoryId;
    $('#add_service_content').html('<div class="tw-flex tw-items-center tw-justify-center tw-py-12"><i class="fa fa-spinner fa-spin tw-text-3xl tw-text-orange-500"></i></div>');
    window.location.hash = 'add_service';
    $.ajax({
        url: '{{ route("admin.services.create") }}',
        type: 'GET',
        data: { category: categoryId, country: countryId },
        success: function(data) {
            $('#add_service_content').html(data.html);
        },
        error: function() {
            $('#add_service_content').html('<div class="tw-bg-rose-50 tw-text-rose-600 tw-p-4 tw-rounded-xl tw-text-xs tw-font-bold"><i class="fa fa-warning tw-mr-2"></i> Error loading form.</div>');
        }
    });
}

function addServiceSubmit() {
    var formData = $('#add_service_form').serialize();
    $.ajax({
        url: '{{ route("admin.services.store") }}',
        type: 'POST',
        data: formData,
        success: function(data) {
            if (data.success) {
                window.location.hash = 'close';
                if (currentAddServiceCategory > 0) {
                    load_services(currentAddServiceCategory, 0, 1);
                }
            } else {
                alert(data.message || 'Error creating service');
            }
        },
        error: function(xhr) {
            var msg = 'Error creating service';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            alert(msg);
        }
    });
}

// ===== EDIT SERVICE MODAL =====
function openEditServiceModal(serviceId) {
    $('#edit_service_content').html('<div class="tw-flex tw-items-center tw-justify-center tw-py-12"><i class="fa fa-spinner fa-spin tw-text-3xl tw-text-orange-500"></i></div>');
    window.location.hash = 'edit_service';
    $.ajax({
        url: '{{ url("admin/services") }}/' + serviceId + '/edit',
        type: 'GET',
        data: { ajax: 1, source: 'manage_services' },
        success: function(data) {
            if (data.html) {
                $('#edit_service_content').html(data.html);
            } else {
                window.location = '{{ url("admin/services") }}/' + serviceId + '/edit';
            }
        },
        error: function() {
            window.location = '{{ url("admin/services") }}/' + serviceId + '/edit';
        }
    });
}

function editServiceSubmit(serviceId) {
    var formData = $('#edit_service_form').serialize();
    $.ajax({
        url: '{{ url("admin/services") }}/' + serviceId,
        type: 'POST',
        data: formData + '&_method=PUT',
        success: function(data) {
            if (data.success) {
                window.location.hash = 'close';
                if (currentCategoryId > 0) {
                    load_services(currentCategoryId, 0, 1);
                }
            } else {
                alert(data.message || 'Error updating service');
            }
        },
        error: function(xhr) {
            var msg = 'Error updating service';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            alert(msg);
        }
    });
}

// ===== TREE COLLAPSE/EXPAND HELPERS (for add service modal) =====
function catCollapseAll() {
    $('#parent_tree').find('ul').hide();
}

function catExpandAll() {
    $('#parent_tree').find('ul').show();
}
</script>
@endif
@endpush
