@extends('admin.layouts.app')
@section('title', 'Admin | Edit Page')

@section('content')
<div class="tw-flex tw-flex-col md:tw-flex-row tw-items-start md:tw-items-center tw-justify-between tw-mb-8 tw-gap-4">
    <div>
        <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-2">
            <i class="fa fa-server"></i> CMS
            <i class="fa fa-angle-right"></i>
            <a href="{{ route('admin.pages.index') }}" class="tw-text-orange-500 hover:tw-text-orange-600 tw-transition-all">Pages</a>
            <i class="fa fa-angle-right"></i>
            <span class="tw-text-slate-500">Edit Page</span>
        </div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-orange-900 tw-tracking-tight">Edit: {{ $page->name ?? 'None' }}</h1>
        <p class="tw-text-slate-500 tw-mt-1 tw-font-medium">Update settings and content for the <span class="tw-text-orange-600 tw-font-bold">{{ strtoupper($currentLang) }}</span> variant.</p>
    </div>
    <div class="tw-flex tw-gap-3">
        <a href="{{ route('admin.pages.index') }}" class="btn red">
            <i class="fa fa-times"></i> Cancel
        </a>
    </div>
</div>

{{-- Language Selection Tabs --}}
<div class="tw-mb-8 tw-bg-white tw-p-2 tw-rounded-2xl tw-border tw-border-slate-200 tw-shadow-sm tw-inline-flex tw-flex-wrap tw-items-center tw-gap-1">
    <div class="tw-px-4 tw-py-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Translate variant:</div>
    @foreach($langs as $tl)
        <a href="{{ route('admin.pages.edit', $pageId) }}?PL={{ $tl }}" 
           class="tw-px-4 tw-py-2 tw-rounded-xl tw-text-sm tw-font-bold tw-transition-all tw-no-underline {{ $currentLang == $tl ? 'tw-bg-orange-600 tw-text-white tw-shadow-md' : 'tw-text-slate-500 hover:tw-bg-slate-100 hover:tw-text-slate-700' }}">
            {{ strtoupper($tl) }}
        </a>
    @endforeach
</div>

<form method="POST" action="{{ route('admin.pages.update', $pageId) }}?PL={{ $currentLang }}">
    @csrf
    @method('PUT')
    <input type="hidden" name="page_lang" value="{{ $currentLang }}">
    <input type="hidden" name="insert_new" value="{{ $insertNew ? '1' : '0' }}">

    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-12 tw-gap-8">
        {{-- Left Column: Settings & SEO --}}
        <div class="lg:tw-col-span-4 tw-space-y-8">
            {{-- General Settings --}}
            <div class="box">
                <h3 class="tw-text-lg tw-font-bold tw-text-orange-900 tw-mb-6 tw-flex tw-items-center tw-gap-2">
                    <i class="fa fa-cog tw-text-orange-500"></i> Page Settings
                </h3>
                
                <div class="tw-space-y-6">
                    <div>
                        <label>Internal Name ({{ $currentLang }})</label>
                        <input type="text" name="pname" value="{{ $page->name ?? '' }}" placeholder="e.g. About Us" required>
                    </div>
                    
                    <div>
                        <label>Display Title ({{ $currentLang }})</label>
                        <input type="text" name="ptitle" value="{{ $page->title ?? '' }}" placeholder="e.g. Welcome to PVT Travels">
                    </div>

                    <div>
                        <label>Url / Slug ({{ $currentLang }})</label>
                        <div class="tw-relative tw-flex tw-items-center">
                            <span class="tw-absolute tw-left-4 tw-text-slate-400 tw-font-bold tw-text-sm">/</span>
                            <input type="text" name="url" value="{{ $page->url ?? '' }}" style="padding-left: 28px !important;" placeholder="page-slug">
                        </div>
                    </div>

                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                        <div>
                            <label>Published</label>
                            <select name="published">
                                <option value="0" {{ ($page->published ?? 0) == 0 ? 'selected' : '' }}>No (Draft)</option>
                                <option value="1" {{ ($page->published ?? 0) == 1 ? 'selected' : '' }}>Yes (Live)</option>
                            </select>
                        </div>
                        <div>
                            <label>Icon</label>
                            <select name="selected_icon">
                                <option value="" {{ empty($page->icon) ? 'selected' : '' }}>No icon</option>
                                @php
                                    $standardIcons = ['fa-home', 'fa-info', 'fa-globe', 'fa-plane', 'fa-star', 'fa-heart', 'fa-envelope', 'fa-phone', 'fa-user', 'fa-cog'];
                                @endphp
                                @foreach($standardIcons as $icon)
                                    <option value="{{ $icon }}" {{ ($page->icon ?? '') == $icon ? 'selected' : '' }}>{{ $icon }}</option>
                                @endforeach
                                @if(($page->icon ?? '') && !in_array($page->icon, array_merge([''], $standardIcons)))
                                    <option value="{{ $page->icon }}" selected>{{ $page->icon }}</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div>
                        <label>Layout Template</label>
                        <select name="playout">
                            <option value="one col" {{ ($page->layout ?? '') == 'one col' ? 'selected' : '' }}>One Column (Full Width)</option>
                            <option value="two col left" {{ ($page->layout ?? '') == 'two col left' ? 'selected' : '' }}>Two Column (Left Sidebar)</option>
                            <option value="two col right" {{ ($page->layout ?? '') == 'two col right' ? 'selected' : '' }}>Two Column (Right Sidebar)</option>
                            <option value="three col" {{ ($page->layout ?? '') == 'three col' ? 'selected' : '' }}>Three Columns</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- SEO Settings --}}
            <div class="box">
                <h3 class="tw-text-lg tw-font-bold tw-text-orange-900 tw-mb-6 tw-flex tw-items-center tw-gap-2">
                    <i class="fa fa-search tw-text-orange-500"></i> SEO Metadata
                </h3>
                
                <div class="tw-space-y-6">
                    <div>
                        <label>Meta Description</label>
                        <textarea name="pmeta_desc" style="height: 120px !important;" placeholder="Brief description for search engines...">{{ $page->meta_desc ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label>Meta Keywords</label>
                        <input type="text" name="pmeta_keywords" value="{{ $page->meta_key ?? '' }}" placeholder="keyword1, keyword2, ...">
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Page Content --}}
        <div class="lg:tw-col-span-8">
            <div class="box tw-flex tw-flex-col tw-min-h-full">
                <h3 class="tw-text-lg tw-font-bold tw-text-orange-900 tw-mb-6 tw-flex tw-items-center tw-justify-between">
                    <span class="tw-flex tw-items-center tw-gap-2">
                        <i class="fa fa-file-text-o tw-text-orange-500"></i> Content ({{ strtoupper($currentLang) }})
                    </span>
                    <button type="submit" class="btn orange">
                        <i class="fa fa-save"></i> Update Content
                    </button>
                </h3>
                
                <div class="tw-flex-1">
                    <textarea name="pcontents" class="tinymce">{!! $page->contents ?? '' !!}</textarea>
                </div>

                <div class="tw-mt-8 tw-bg-slate-50 tw-p-6 tw-rounded-2xl tw-border tw-border-slate-100 tw-flex tw-flex-col sm:tw-flex-row tw-items-center tw-justify-between tw-gap-4">
                    <div class="tw-text-sm tw-text-slate-500 tw-font-medium">
                        <i class="fa fa-info-circle tw-mr-1"></i> You are editing the <strong class="tw-text-orange-600">{{ strtoupper($currentLang) }}</strong> version.
                    </div>
                    <button type="submit" class="btn orange tw-w-full sm:tw-w-auto">
                        <i class="fa fa-save"></i> Save Content
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- TinyMCE --}}
<script type="text/javascript" src="/assets/admin/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea.tinymce",
    plugins: [
        "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
        "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern"
    ],
    toolbar1: "bold italic underline strikethrough alignleft aligncenter alignright alignjustify | styleselect | formatselect | fontselect | fontsizeselect |  cut copy paste | searchreplace | bullist numlist | outdent indent | undo redo | link unlink | anchor | image media | code | forecolor backcolor | table | hr removeformat | subscript | superscript | charmap  fullscreen | ltr rtl  | visualchars visualblocks  restoredraft",
    menubar: false,
    toolbar_items_size: 'small',
    entity_encoding: 'raw',
    extended_valid_elements: 'pre[*],script[*],style[*]',
    valid_children: '+body[style|script],pre[script|div|p|br|span|img|style|h1|h2|h3|h4|h5],*[*]',
    valid_elements: '*[*]',
    height: 500,
    verify_html: false,
    force_p_newlines: false,
    relative_urls: true,
    remove_script_host: false,
    document_base_url: "{{ url('/') }}/",
    content_css: ["/assets/admin/gogies.css", "/assets/admin/tinymce_content.css"],
    content_style: "@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap'); body { font-family: Roboto, sans-serif; font-size: 11pt; } .mce-content-body .scroll-animate{opacity:1 !important;}"
});
</script>
@endsection
