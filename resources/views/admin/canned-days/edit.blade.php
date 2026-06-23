@extends('admin.layouts.app')
@section('title', 'Admin | Edit Canned Day')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    
    {{-- Header Section --}}
    <div class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4 tw-bg-white tw-p-8 tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-bold tw-text-indigo-400 tw-uppercase tw-tracking-widest tw-mb-3">
                <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-indigo-600 tw-transition-colors tw-no-underline">Dashboard</a>
                <span class="tw-opacity-50">/</span>
                <span class="tw-text-slate-500">Quotations</span>
                <span class="tw-opacity-50">/</span>
                <span class="tw-text-slate-500">Canned Days</span>
            </div>
            <h1 class="tw-text-4xl tw-font-black tw-text-slate-900 tw-flex tw-items-center tw-gap-4 tw-tracking-tight">
                <div class="tw-w-14 tw-h-14 tw-bg-gradient-to-br tw-from-indigo-400 tw-to-violet-500 tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-indigo-200">
                    <i class="fa fa-calendar-check-o tw-text-white tw-text-2xl"></i>
                </div>
                Edit Canned Day #{{ $cannedDay->id }}
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-3 tw-text-sm max-w-2xl">Modify reusable itinerary parameters. Changes instantly apply to new quotations.</p>
        </div>
        <div>
            <a href="{{ route('admin.canned-days.index') }}" class="tw-inline-flex tw-items-center tw-gap-2 tw-bg-slate-100 hover:tw-bg-slate-200 tw-text-slate-600 tw-px-6 tw-py-3.5 tw-rounded-xl tw-font-bold tw-text-sm tw-whitespace-nowrap tw-transition-all tw-no-underline">
                <i class="fa fa-arrow-left"></i> Back to Archive
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="tw-flex tw-flex-col tw-gap-3">
        @foreach($errors->all() as $error)
        <div class="tw-bg-rose-50 tw-border-l-4 tw-border-rose-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-4 tw-shadow-sm">
            <div class="tw-w-10 tw-h-10 tw-bg-white tw-rounded-full tw-flex tw-items-center tw-justify-center tw-shadow-sm">
                <i class="fa fa-warning tw-text-rose-500 tw-text-xl"></i>
            </div>
            <div>
                <p class="tw-text-rose-800 tw-font-black tw-text-sm tw-uppercase tw-tracking-wide">Validation Error</p>
                <p class="tw-text-rose-600 tw-font-medium tw-text-sm">{{ $error }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.canned-days.update', $cannedDay->id) }}" id="canned_day_form">
        @csrf
        @method('PUT')
        
        <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-12 tw-gap-8 mt-2">
            {{-- Left Column: Content & Translations --}}
            <div class="lg:tw-col-span-8 tw-flex tw-flex-col tw-gap-8">
                <div class="tw-bg-white tw-rounded-[2.5rem] tw-shadow-sm tw-border tw-border-slate-100 tw-p-10">
                    <div class="tw-flex tw-items-center tw-gap-5 tw-mb-10 tw-pb-8 tw-border-b tw-border-slate-100">
                        <div class="tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-indigo-50 tw-text-indigo-600 tw-flex tw-items-center tw-justify-center tw-border tw-border-indigo-100">
                            <i class="fa fa-language tw-text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="tw-text-2xl tw-font-black tw-text-slate-900 !tw-m-0 tw-tracking-tight">Global Variants</h3>
                            <p class="tw-text-sm tw-text-slate-400 tw-font-medium tw-mt-1">Update multilingual content for this template</p>
                        </div>
                    </div>

                    @php
                        $langs = ['en','fr','it','es','Ar','ge','pt'];
                        $contentsByLang = [];
                        foreach($cannedDay->contents as $c) {
                            $contentsByLang[$c->lang] = $c;
                        }
                    @endphp
                    
                    {{-- Premium Language Switcher --}}
                    <div class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-10 tw-p-2 tw-bg-slate-50 tw-rounded-[1.5rem] tw-w-fit tw-border tw-border-slate-100">
                        @foreach($langs as $i => $L)
                        <button type="button" 
                            class="lang_switch tw-px-8 tw-py-3.5 tw-rounded-xl tw-text-xs tw-font-black tw-uppercase tw-transition-all {{ $i === 0 ? 'tw-bg-white tw-text-indigo-600 tw-shadow-md' : 'tw-text-slate-400 hover:tw-bg-slate-100' }}" 
                            data-lang="#desc_{{ $L }}">{{ $L }}</button>
                        @endforeach
                    </div>

                    <div class="tw-flex tw-flex-col tw-gap-8">
                        @foreach($langs as $i => $L)
                        <div class="lang_tab_content {{ $i === 0 ? '' : 'tw-hidden' }}" id="desc_{{ $L }}">
                            <div class="tw-flex tw-flex-col tw-gap-8">
                                <div class="tw-flex tw-flex-col tw-gap-3">
                                    <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-2">Itinerary Title ({{ strtoupper($L) }})</label>
                                    <input type="text" name="title_{{ $L }}" value="{{ isset($contentsByLang[$L]) ? html_entity_decode($contentsByLang[$L]->title) : '' }}" class="tw-w-full !tw-py-5 !tw-px-8 !tw-bg-slate-50 !tw-border-none focus:!tw-ring-2 focus:!tw-ring-indigo-200 focus:!tw-bg-white !tw-rounded-2xl tw-transition-all tw-font-bold tw-text-base tw-shadow-sm" placeholder="Title for {{ $L }} version...">
                                </div>
                                <div class="tw-flex tw-flex-col tw-gap-3">
                                    <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-2">Story & Details ({{ strtoupper($L) }})</label>
                                    <div class="tw-rounded-3xl tw-overflow-hidden tw-border tw-border-slate-100 tw-shadow-sm">
                                        <textarea name="description_{{ $L }}" class="tinymce">{!! isset($contentsByLang[$L]) ? html_entity_decode($contentsByLang[$L]->description) : '' !!}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Inclusions Card --}}
                <div class="tw-bg-white tw-rounded-[2.5rem] tw-shadow-sm tw-border tw-border-slate-100 tw-p-10">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-10">
                        <div class="tw-flex tw-items-center tw-gap-5">
                            <div class="tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-emerald-50 tw-text-emerald-600 tw-flex tw-items-center tw-justify-center tw-border tw-border-emerald-100">
                                <i class="fa fa-check-square-o tw-text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="tw-text-2xl tw-font-black tw-text-slate-900 !tw-m-0 tw-tracking-tight">Inclusions Checklist</h3>
                                <p class="tw-text-sm tw-text-slate-400 tw-font-medium tw-mt-1">Modify provided or excluded items</p>
                            </div>
                        </div>
                        <button type="button" class="tw-inline-flex tw-items-center tw-gap-2 tw-bg-slate-900 hover:tw-bg-emerald-500 tw-text-white tw-px-6 tw-py-3.5 tw-rounded-xl tw-font-bold tw-text-sm tw-transition-all tw-shadow-lg hover:tw-shadow-emerald-200" onclick="$('#add_inclusion_modal').show();">
                            <i class="fa fa-cog"></i> Update List
                        </button>
                    </div>

                    @php
                        $included = []; $excluded = [];
                        if($cannedDay->included) { $included = @unserialize($cannedDay->included) ?: []; }
                        if($cannedDay->excluded) { $excluded = @unserialize($cannedDay->excluded) ?: []; }
                    @endphp

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-8">
                        <div class="tw-p-8 tw-bg-emerald-50/50 tw-rounded-[2.5rem] tw-border-2 tw-border-dashed tw-border-emerald-200 tw-min-h-[300px]">
                            <span class="tw-text-[11px] tw-font-black tw-text-emerald-600 tw-uppercase tw-tracking-widest tw-block tw-mb-6 tw-flex tw-items-center tw-gap-2">
                                <i class="fa fa-check-circle tw-text-sm"></i> Included Components
                            </span>
                            <div id="day_inc_0" class="tw-flex tw-flex-col tw-gap-3">
                                @foreach($included as $k => $v)
                                <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-rounded-2xl tw-border tw-border-emerald-100 tw-group tw-shadow-sm hover:tw-border-emerald-300 hover:tw-shadow-md tw-transition-all">
                                    <span class="tw-text-sm tw-font-bold tw-text-slate-700 tw-flex tw-items-center tw-gap-3">
                                        <i class="fa fa-check tw-text-emerald-500 tw-bg-emerald-50 tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg"></i> {{ $v }}
                                    </span>
                                    <input type="hidden" name="day_inc_0[{{ $k }}]" value="{{ $v }}">
                                    <button type="button" onclick="$(this).parent().remove();" class="tw-opacity-0 group-hover:tw-opacity-100 tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-bg-rose-50 tw-text-rose-500 tw-rounded-xl hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="tw-p-8 tw-bg-rose-50/50 tw-rounded-[2.5rem] tw-border-2 tw-border-dashed tw-border-rose-200 tw-min-h-[300px]">
                            <span class="tw-text-[11px] tw-font-black tw-text-rose-600 tw-uppercase tw-tracking-widest tw-block tw-mb-6 tw-flex tw-items-center tw-gap-2">
                                <i class="fa fa-times-circle tw-text-sm"></i> Excluded Components
                            </span>
                            <div id="day_exc_0" class="tw-flex tw-flex-col tw-gap-3">
                                @foreach($excluded as $k => $v)
                                <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-rounded-2xl tw-border tw-border-rose-100 tw-group tw-shadow-sm hover:tw-border-rose-300 hover:tw-shadow-md tw-transition-all">
                                    <span class="tw-text-sm tw-font-bold tw-text-slate-700 tw-flex tw-items-center tw-gap-3">
                                        <i class="fa fa-times tw-text-rose-500 tw-bg-rose-50 tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg"></i> {{ $v }}
                                    </span>
                                    <input type="hidden" name="day_exc_0[{{ $k }}]" value="{{ $v }}">
                                    <button type="button" onclick="$(this).parent().remove();" class="tw-opacity-0 group-hover:tw-opacity-100 tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-bg-rose-50 tw-text-rose-500 tw-rounded-xl hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Assets & Resources --}}
            <div class="lg:tw-col-span-4 tw-flex tw-flex-col tw-gap-8">
                {{-- Expenses Card --}}
                <div class="tw-bg-white tw-rounded-[2.5rem] tw-shadow-sm tw-border tw-border-slate-100 tw-p-10">
                    <div class="tw-flex tw-items-center tw-gap-5 tw-mb-10">
                        <div class="tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-amber-50 tw-text-amber-600 tw-flex tw-items-center tw-justify-center tw-border tw-border-amber-100">
                            <i class="fa fa-money tw-text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="tw-text-2xl tw-font-black tw-text-slate-900 !tw-m-0 tw-tracking-tight">Expenses</h3>
                            <p class="tw-text-sm tw-text-slate-400 tw-font-medium tw-mt-1">Financial resources</p>
                        </div>
                    </div>

                    @php
                        $expenses = [];
                        if($cannedDay->expenses) { $expenses = @unserialize($cannedDay->expenses) ?: []; }
                    @endphp
                    
                    <div id="day_expenses" class="tw-flex tw-flex-col tw-gap-6">
                        <div id="expense_list_0" class="tw-flex tw-flex-col tw-gap-4">
                            @foreach($expenses as $k => $ex)
                            <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-group tw-shadow-sm hover:tw-border-amber-200 hover:tw-shadow-md tw-transition-all">
                                <span class="tw-text-sm tw-font-bold tw-text-slate-700 tw-flex tw-items-center tw-gap-3">
                                    <i class="fa fa-cubes tw-text-amber-500 tw-bg-amber-50 tw-rounded-lg tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center"></i> {{ $ex['desc'] ?? 'Resource' }}
                                </span>
                                <input type="hidden" name="expenses[{{ $k }}]" value="{{ $ex['id'] ?? 0 }}">
                                <input type="hidden" name="expenses_name[{{ $k }}]" value="{{ $ex['desc'] ?? '' }}">
                                <button type="button" onclick="$(this).parent().remove();" class="tw-opacity-0 group-hover:tw-opacity-100 tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-bg-rose-50 tw-text-rose-500 tw-rounded-xl hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        <a href="#add_expense" class="tw-w-full tw-py-12 tw-border-2 tw-border-dashed tw-border-slate-200 tw-rounded-[2rem] tw-text-slate-400 tw-text-sm tw-font-bold hover:tw-border-indigo-300 hover:tw-bg-indigo-50/50 hover:tw-text-indigo-600 tw-transition-all tw-flex tw-flex-col tw-items-center tw-gap-4 tw-no-underline group" style="background:transparent" data-country="1">
                            <div class="tw-w-14 tw-h-14 tw-rounded-full tw-bg-indigo-50 tw-flex tw-items-center tw-justify-center group-hover:tw-scale-110 tw-transition-transform tw-text-indigo-500 shadow-sm">
                                <i class="fa fa-plus tw-text-xl"></i>
                            </div>
                            <span>Amend Resources</span>
                        </a>
                    </div>
                </div>

                {{-- Images Card --}}
                <div class="tw-bg-white tw-rounded-[2.5rem] tw-shadow-sm tw-border tw-border-slate-100 tw-p-10">
                    @php
                        $images = [];
                        if($cannedDay->images) { $images = @unserialize($cannedDay->images) ?: []; }
                    @endphp
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-10">
                        <div class="tw-flex tw-items-center tw-gap-5">
                            <div class="tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-cyan-50 tw-text-cyan-600 tw-flex tw-items-center tw-justify-center tw-border tw-border-cyan-100">
                                <i class="fa fa-camera tw-text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="tw-text-2xl tw-font-black tw-text-slate-900 !tw-m-0 tw-tracking-tight">Media</h3>
                                <p class="tw-text-sm tw-text-slate-400 tw-font-medium tw-mt-1">{{ count($images) }} Visual Assets</p>
                            </div>
                        </div>
                        <button type="button" class="image_selector tw-w-12 tw-h-12 tw-bg-slate-900 tw-text-white tw-rounded-2xl hover:tw-bg-cyan-500 tw-transition-colors tw-shadow-xl hover:tw-shadow-cyan-200" data-input-name="dimages">
                            <i class="fa fa-plus tw-text-lg"></i>
                        </button>
                    </div>
                    <div id="images" class="tw-grid tw-grid-cols-2 tw-gap-4">
                        @foreach($images as $k => $v)
                        <div id="img_wrap_{{ $k }}" class="tw-group tw-relative tw-aspect-square tw-rounded-[1.5rem] tw-overflow-hidden tw-border tw-border-slate-100 tw-shadow-sm">
                            <input type="hidden" name="dimages[]" value="{{ $v }}">
                            <img src="{{ $v }}" class="tw-w-full tw-h-full tw-object-cover tw-transition-transform tw-duration-500 group-hover:tw-scale-110">
                            <div class="tw-absolute tw-inset-0 tw-bg-slate-900/40 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-opacity tw-flex tw-items-center tw-justify-center tw-backdrop-blur-sm">
                                <button type="button" onclick="$(this).closest('.tw-relative').remove();" class="tw-w-12 tw-h-12 tw-bg-rose-500 tw-text-white tw-rounded-xl hover:tw-scale-110 tw-transition-all tw-shadow-lg">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Action Card --}}
                <div class="tw-bg-gradient-to-br tw-from-indigo-600 tw-to-violet-700 tw-rounded-[2.5rem] tw-shadow-2xl tw-shadow-indigo-200 tw-p-10 tw-text-white tw-relative tw-overflow-hidden">
                    <div class="tw-absolute -tw-top-10 -tw-right-10 tw-w-32 tw-h-32 tw-bg-white/10 tw-rounded-full tw-blur-2xl"></div>
                    <div class="tw-relative tw-z-10">
                        <div class="tw-flex tw-items-center tw-gap-5 tw-mb-10">
                            <div class="tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-border tw-border-white/20 tw-text-emerald-400 tw-shadow-inner">
                                <i class="fa fa-rocket tw-text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="tw-text-2xl tw-font-black !tw-m-0">Synchronize</h3>
                                <p class="tw-text-sm tw-text-indigo-100 tw-font-medium tw-mt-1">Apply changes permanently</p>
                            </div>
                        </div>
                        <button type="submit" class="tw-w-full tw-bg-white hover:tw-bg-emerald-400 tw-text-indigo-700 hover:tw-text-white tw-font-black tw-text-lg tw-py-5 tw-rounded-2xl tw-shadow-lg tw-transition-all hover:-tw-translate-y-1">
                            <i class="fa fa-check-circle tw-mr-2"></i> Update Template
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Add Inclusion Modal --}}
<div class="modal" id="add_inclusion_modal" style="display:none;">
    <div class="tw-bg-white tw-rounded-[2.5rem] tw-p-0 tw-overflow-hidden !tw-w-[550px] !tw-max-w-[95vw] tw-shadow-2xl tw-border tw-border-slate-100">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-10 tw-py-8 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-4 !tw-m-0">
                <i class="fa fa-hashtag tw-text-indigo-400"></i> Local Entry
            </h3>
            <a href="javascript:void(0);" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-4xl tw-no-underline tw-leading-none" onclick="$('#add_inclusion_modal').hide();">&times;</a>
        </div>
        <div class="tw-p-10 tw-flex tw-flex-col tw-gap-8">
            <div class="tw-flex tw-flex-col tw-gap-3">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Description</label>
                <input type="text" id="inclusion_text" placeholder="e.g. Traditional Lunch in Wadi Rum" class="tw-w-full !tw-py-4 !tw-px-6 !tw-bg-slate-50 tw-border-none tw-shadow-sm tw-rounded-2xl focus:tw-ring-2 focus:tw-ring-indigo-100 focus:tw-bg-white tw-font-bold">
            </div>
            <div class="tw-flex tw-flex-col tw-gap-3">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Category</label>
                <select id="inclusion_type" class="tw-w-full !tw-py-4 !tw-px-6 !tw-bg-slate-50 tw-border-none tw-shadow-sm tw-rounded-2xl focus:tw-ring-2 focus:tw-ring-indigo-100 focus:tw-bg-white tw-font-bold">
                    <option value="inc">Included Item</option>
                    <option value="exc">Excluded Item</option>
                </select>
            </div>
            <div class="tw-pt-4">
                <button type="button" class="tw-w-full tw-bg-indigo-600 hover:tw-bg-indigo-700 tw-text-white tw-py-4 tw-rounded-2xl tw-font-bold tw-shadow-lg tw-shadow-indigo-200 tw-transition-all" onclick="addInclusion()">
                    <i class="fa fa-plus-circle tw-mr-1"></i> Add to List
                </button>
            </div>
        </div>
    </div>
</div>

<div id="ajax"></div>

<script type="text/javascript" src="/assets/admin/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea.tinymce",
    plugins: [
        "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern"
    ],
    toolbar1: "bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | forecolor backcolor | table | code fullscreen",
    menubar: false,
    toolbar_items_size: 'small',
    height: 380,
    verify_html: false,
    content_css: false,
    content_style: "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'); body { font-family: 'Inter', sans-serif; font-size: 15px; color: #334155; line-height: 1.7; padding: 25px; border:none; outline:none;}",
    setup: function (editor) {
        editor.on('change', function () {
            tinymce.triggerSave();
        });
    }
});

$('.lang_switch').on('click', function() {
    var target = $(this).attr('data-lang');
    $('.lang_tab_content').addClass('tw-hidden');
    $(target).removeClass('tw-hidden');
    $('.lang_switch').removeClass('tw-bg-white tw-text-indigo-600 tw-shadow-md').addClass('tw-text-slate-400 hover:tw-bg-slate-100');
    $(this).addClass('tw-bg-white tw-text-indigo-600 tw-shadow-md').removeClass('tw-text-slate-400 hover:tw-bg-slate-100');
});

var inc_count = {{ count($included) + count($excluded) + 1 }};
function addInclusion() {
    var text = $('#inclusion_text').val();
    if(!text) return;
    var type = $('#inclusion_type').val();
    var container = type === 'inc' ? '#day_inc_0' : '#day_exc_0';
    var isInc = type === 'inc';
    var fieldName = isInc ? 'day_inc_0' : 'day_exc_0';
    
    var html = `
        <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-rounded-2xl tw-border ${isInc ? 'tw-border-emerald-100 hover:tw-border-emerald-300' : 'tw-border-rose-100 hover:tw-border-rose-300'} tw-group tw-shadow-sm hover:tw-shadow-md tw-transition-all">
            <span class="tw-text-sm tw-font-bold tw-text-slate-700 tw-flex tw-items-center tw-gap-3">
                <i class="fa ${isInc ? 'fa-check tw-text-emerald-500 tw-bg-emerald-50' : 'fa-times tw-text-rose-500 tw-bg-rose-50'} tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg"></i> ${text}
            </span>
            <input type="hidden" name="${fieldName}[${inc_count}]" value="${text}">
            <button type="button" onclick="$(this).parent().remove();" class="tw-opacity-0 group-hover:tw-opacity-100 tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-bg-rose-50 tw-text-rose-500 tw-rounded-xl hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    `;
    $(container).append(html);
    inc_count++;
    $('#inclusion_text').val('');
    $('#add_inclusion_modal').hide();
}
</script>
<script src="/assets/admin/gogiesfm/image_selector.js"></script>
@endsection
