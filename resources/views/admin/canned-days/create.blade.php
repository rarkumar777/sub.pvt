@extends('admin.layouts.app')
@section('title', 'Admin | New Canned Day')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Navigation Tabs --}}
    @include('admin.quotations._nav')

    <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">
        <i class="fa fa-pie-chart"></i> Quotations
        <i class="fa fa-angle-right"></i>
        <a href="{{ route('admin.canned-days.index') }}" class="tw-text-indigo-500 hover:tw-text-indigo-600 tw-transition-all">Canned Days</a>
        <i class="fa fa-angle-right"></i>
        <span class="tw-text-slate-500">Create Template</span>
    </div>

    {{-- Header Section --}}
    <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-items-start md:tw-items-center tw-gap-4">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">New Canned <span class="tw-text-indigo-600">Day</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Define reusable itinerary content and budget resources</p>
        </div>
        <a href="{{ route('admin.canned-days.index') }}" class="btn red">
            <i class="fa fa-arrow-left"></i> Back to Archive
        </a>
    </div>

    @if($errors->any())
    <div class="tw-flex tw-flex-col tw-gap-2">
        @foreach($errors->all() as $error)
        <div class="tw-bg-rose-50 tw-border-l-4 tw-border-rose-500 tw-p-5 tw-rounded-2xl tw-text-rose-800 tw-text-xs tw-font-bold tw-shadow-sm">
            <i class="fa fa-exclamation-triangle tw-mr-2"></i> {{ $error }}
        </div>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.canned-days.store') }}" id="canned_day_form">
        @csrf
        
        <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-12 tw-gap-8">
            {{-- Left Column: Content & Translations --}}
            <div class="lg:tw-col-span-8 tw-flex tw-flex-col tw-gap-8">
                <div class="box !tw-p-10">
                    <div class="tw-flex tw-items-center tw-gap-4 tw-mb-10 tw-pb-6 tw-border-b tw-border-slate-100">
                        <div class="tw-w-12 tw-h-12 tw-rounded-2xl tw-bg-indigo-50 tw-text-indigo-600 tw-flex tw-items-center tw-justify-center shadow-inner">
                            <i class="fa fa-language tw-text-xl"></i>
                        </div>
                        <div>
                            <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Multilingual Content</h3>
                            <p class="tw-text-xs tw-text-slate-400 tw-font-medium">Manage itinerary descriptions across all supported languages</p>
                        </div>
                    </div>

                    @php $langs = ['en','fr','it','es','Ar','ge','pt']; @endphp
                    
                    {{-- Premium Language Switcher --}}
                    <div class="tw-flex tw-flex-wrap tw-gap-2 tw-mb-10 tw-p-2 tw-bg-slate-50 tw-rounded-[1.5rem] tw-w-fit shadow-inner">
                        @foreach($langs as $i => $L)
                        <button type="button" 
                            class="lang_switch tw-px-6 tw-py-3 tw-rounded-xl tw-text-xs tw-font-black tw-uppercase tw-transition-all {{ $i === 0 ? 'tw-bg-white tw-text-indigo-600 shadow-premium' : 'tw-text-slate-500 hover:tw-bg-white/50' }}" 
                            data-lang="#desc_{{ $L }}">{{ $L }}</button>
                        @endforeach
                    </div>

                    <div class="tw-flex tw-flex-col tw-gap-8">
                        @foreach($langs as $i => $L)
                        <div class="lang_tab_content {{ $i === 0 ? '' : 'tw-hidden' }}" id="desc_{{ $L }}">
                            <div class="tw-flex tw-flex-col tw-gap-8">
                                <div class="tw-flex tw-flex-col tw-gap-3">
                                    <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Itinerary Title ({{ strtoupper($L) }})</label>
                                    <input type="text" name="title_{{ $L }}" class="!tw-py-4.5 !tw-px-8 !tw-bg-slate-50 !tw-border-transparent focus:!tw-bg-white focus:!tw-border-indigo-500 !tw-rounded-[1.5rem] tw-transition-all tw-font-bold" placeholder="Give this day a clear title...">
                                </div>
                                <div class="tw-flex tw-flex-col tw-gap-3">
                                    <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Story & Details ({{ strtoupper($L) }})</label>
                                    <textarea name="description_{{ $L }}" class="tinymce"></textarea>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Inclusions Card --}}
                <div class="box !tw-p-10">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-10">
                        <div class="tw-flex tw-items-center tw-gap-4">
                            <div class="tw-w-12 tw-h-12 tw-rounded-2xl tw-bg-emerald-50 tw-text-emerald-600 tw-flex tw-items-center tw-justify-center shadow-inner">
                                <i class="fa fa-check-square-o tw-text-xl"></i>
                            </div>
                            <div>
                                <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Inclusions Checklist</h3>
                                <p class="tw-text-xs tw-text-slate-400 tw-font-medium">Define what is provided by default in this template</p>
                            </div>
                        </div>
                        <button type="button" class="btn ivory !tw-text-[11px] !tw-px-5 !tw-py-3 !tw-rounded-xl shadow-sm hover:tw-shadow-md tw-transition-all" onclick="$('#add_inclusion_modal').show();">
                            <i class="fa fa-cog tw-mr-1"></i> Manage Items
                        </button>
                    </div>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-10">
                        <div class="tw-p-8 tw-bg-emerald-50/20 tw-rounded-[3rem] tw-border-2 tw-border-dashed tw-border-emerald-100/50 tw-min-h-[200px] tw-transition-colors">
                            <span class="tw-text-[11px] tw-font-black tw-text-emerald-600 tw-uppercase tw-tracking-widest tw-block tw-mb-6 tw-flex tw-items-center tw-gap-2">
                                <i class="fa fa-plus-circle"></i> Included by Default
                            </span>
                            <div id="day_inc_0" class="tw-flex tw-flex-col tw-gap-3"></div>
                        </div>
                        <div class="tw-p-8 tw-bg-rose-50/20 tw-rounded-[3rem] tw-border-2 tw-border-dashed tw-border-rose-100/50 tw-min-h-[200px] tw-transition-colors">
                            <span class="tw-text-[11px] tw-font-black tw-text-rose-600 tw-uppercase tw-tracking-widest tw-block tw-mb-6 tw-flex tw-items-center tw-gap-2">
                                <i class="fa fa-minus-circle"></i> Excluded by Default
                            </span>
                            <div id="day_exc_0" class="tw-flex tw-flex-col tw-gap-3"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Assets & Resources --}}
            <div class="lg:tw-col-span-4 tw-flex tw-flex-col tw-gap-8">
                {{-- Expenses Card --}}
                <div class="box !tw-p-10">
                    <div class="tw-flex tw-items-center tw-gap-4 tw-mb-10">
                        <div class="tw-w-12 tw-h-12 tw-rounded-2xl tw-bg-amber-50 tw-text-amber-600 tw-flex tw-items-center tw-justify-center shadow-inner">
                            <i class="fa fa-dollar tw-text-xl"></i>
                        </div>
                        <div>
                            <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Budget Flow</h3>
                            <p class="tw-text-xs tw-text-slate-400 tw-font-medium">Attached financial resources</p>
                        </div>
                    </div>
                    
                    <div id="day_expenses" class="tw-flex tw-flex-col tw-gap-6">
                        <div id="expense_list_0" class="tw-flex tw-flex-col tw-gap-3"></div>
                        <a href="#add_expense" class="tw-w-full tw-py-10 tw-border-2 tw-border-dashed tw-border-slate-100 tw-rounded-[2.5rem] tw-text-slate-300 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest hover:tw-border-indigo-200 hover:tw-text-indigo-400 tw-transition-all tw-flex tw-flex-col tw-items-center tw-gap-4 tw-no-underline group" data-country="1">
                            <div class="tw-w-12 tw-h-12 tw-rounded-full tw-bg-slate-50 tw-flex tw-items-center tw-justify-center group-hover:tw-bg-indigo-50 tw-transition-colors shadow-sm">
                                <i class="fa fa-plus tw-text-lg"></i>
                            </div>
                            <span>Attach Expense</span>
                        </a>
                    </div>
                </div>

                {{-- Images Card --}}
                <div class="box !tw-p-10">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-10">
                        <div class="tw-flex tw-items-center tw-gap-4">
                            <div class="tw-w-12 tw-h-12 tw-rounded-2xl tw-bg-violet-50 tw-text-violet-600 tw-flex tw-items-center tw-justify-center shadow-inner">
                                <i class="fa fa-camera tw-text-xl"></i>
                            </div>
                            <div>
                                <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Media</h3>
                                <p class="tw-text-xs tw-text-slate-400 tw-font-medium">Visual assets</p>
                            </div>
                        </div>
                        <button type="button" class="image_selector tw-w-10 tw-h-10 tw-bg-slate-900 tw-text-white tw-rounded-xl hover:tw-bg-black tw-transition-colors shadow-lg shadow-slate-200" data-input-name="dimages">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <div id="images" class="tw-grid tw-grid-cols-2 tw-gap-4"></div>
                </div>

                {{-- Action Card --}}
                <div class="box !tw-p-10 tw-bg-slate-900 tw-text-white shadow-2xl shadow-indigo-100">
                    <div class="tw-flex tw-items-center tw-gap-4 tw-mb-8">
                        <div class="tw-w-12 tw-h-12 tw-rounded-2xl tw-bg-white/10 tw-flex tw-items-center tw-justify-center shadow-inner text-indigo-400">
                            <i class="fa fa-shield tw-text-xl"></i>
                        </div>
                        <div>
                            <h3 class="tw-text-lg tw-font-bold !tw-m-0">Publish</h3>
                            <p class="tw-text-xs tw-text-white/40 tw-font-medium">Save as reusable template</p>
                        </div>
                    </div>
                    <button type="submit" class="btn indigo !tw-w-full !tw-py-5 !tw-text-lg shadow-xl shadow-indigo-600/20 active:tw-scale-95 tw-transition-all">
                        <i class="fa fa-save tw-mr-2"></i> Save Canned Day
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Modernized Inclusion Modal --}}
<div class="modal" id="add_inclusion_modal" style="display:none;">
    <div class="tw-bg-white tw-rounded-[3rem] tw-p-0 tw-overflow-hidden !tw-w-[550px] !tw-max-w-[95vw] tw-shadow-premium-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-10 tw-py-8 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-4 !tw-m-0">
                <i class="fa fa-paperclip tw-text-indigo-400"></i> Append Inclusion
            </h3>
            <a href="javascript:void(0);" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-4xl tw-no-underline tw-leading-none" onclick="$('#add_inclusion_modal').hide();">&times;</a>
        </div>
        <div class="tw-p-10 tw-flex tw-flex-col tw-gap-8">
            <div class="tw-flex tw-flex-col tw-gap-3">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Description</label>
                <input type="text" id="inclusion_text" placeholder="e.g. Traditional Lunch in Wadi Rum" class="!tw-py-5 !tw-px-8 !tw-rounded-3xl">
            </div>
            <div class="tw-flex tw-flex-col tw-gap-3">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Set Classification</label>
                <select id="inclusion_type" class="!tw-py-5 !tw-px-8 !tw-rounded-3xl">
                    <option value="inc">Included in Draft</option>
                    <option value="exc">Exclude from Draft</option>
                </select>
            </div>
            <div class="tw-pt-4">
                <button type="button" class="btn indigo !tw-w-full !tw-py-5 !tw-rounded-3xl shadow-xl shadow-indigo-50" onclick="addInclusion()">
                    <i class="fa fa-plus-circle tw-mr-1"></i> Add to Checklist
                </button>
            </div>
        </div>
    </div>
</div>

<div id="ajax"></div>

{{-- TinyMCE & Assets --}}
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
    content_style: "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'); body { font-family: 'Inter', sans-serif; font-size: 14px; color: #334155; line-height: 1.6; padding: 25px; }",
    setup: function (editor) {
        editor.on('change', function () {
            tinymce.triggerSave();
        });
    }
});

// Language Switcher Logic
$('.lang_switch').on('click', function() {
    var target = $(this).attr('data-lang');
    $('.lang_tab_content').addClass('tw-hidden');
    $(target).removeClass('tw-hidden');
    $('.lang_switch').removeClass('tw-bg-white tw-text-indigo-600 shadow-premium').addClass('tw-text-slate-500 hover:tw-bg-white/50');
    $(this).addClass('tw-bg-white tw-text-indigo-600 shadow-premium').removeClass('tw-text-slate-500 hover:tw-bg-white/50');
});

// Inclusion Logic
var inc_count = 1;
function addInclusion() {
    var text = $('#inclusion_text').val();
    if(!text) return;
    var type = $('#inclusion_type').val();
    var container = type === 'inc' ? '#day_inc_0' : '#day_exc_0';
    var isInc = type === 'inc';
    var fieldName = isInc ? 'day_inc_0' : 'day_exc_0';
    
    var html = `
        <div class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-group shadow-sm hover:tw-border-indigo-100 tw-transition-all">
            <span class="tw-text-xs tw-font-bold tw-text-slate-600 tw-flex tw-items-center tw-gap-3">
                <i class="fa ${isInc ? 'fa-check tw-text-emerald-500' : 'fa-times tw-text-rose-500'}"></i> ${text}
            </span>
            <input type="hidden" name="${fieldName}[${inc_count}]" value="${text}">
            <button type="button" onclick="$(this).parent().remove();" class="tw-opacity-0 group-hover:tw-opacity-100 tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-bg-rose-50 tw-text-rose-500 tw-rounded-lg hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all">
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
