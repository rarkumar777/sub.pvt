@extends('admin.layouts.app')
@section('title', 'Admin | Add New Page')

@section('content')
<div class="tw-flex tw-flex-col md:tw-flex-row tw-items-start md:tw-items-center tw-justify-between tw-mb-8 tw-gap-4">
    <div>
        <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-2">
            <i class="fa fa-server"></i> CMS
            <i class="fa fa-angle-right"></i>
            <a href="{{ route('admin.pages.index') }}" class="tw-text-orange-500 hover:tw-text-orange-600 tw-transition-all">Pages</a>
            <i class="fa fa-angle-right"></i>
            <span class="tw-text-slate-500">Add New</span>
        </div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-orange-900 tw-tracking-tight">Add New Page</h1>
        <p class="tw-text-slate-500 tw-mt-1 tw-font-medium">Define settings and main content for your new page.</p>
    </div>
    <div class="tw-flex tw-gap-3">
        <a href="{{ route('admin.pages.index') }}" class="btn red">
            <i class="fa fa-times"></i> Cancel
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.pages.store') }}">
    @csrf
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
                        <label>Internal Name (en)</label>
                        <input type="text" name="pname" value="{{ old('pname') }}" placeholder="e.g. About Us" required>
                    </div>
                    
                    <div>
                        <label>Display Title (en)</label>
                        <input type="text" name="ptitle" value="{{ old('ptitle') }}" placeholder="e.g. Welcome to PVT Travels">
                    </div>

                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                        <div>
                            <label>Published</label>
                            <select name="published">
                                <option value="0" {{ old('published') == 0 ? 'selected' : '' }}>No (Draft)</option>
                                <option value="1" {{ old('published') == 1 ? 'selected' : '' }}>Yes (Live)</option>
                            </select>
                        </div>
                        <div>
                            <label>Icon</label>
                            <select name="selected_icon">
                                <option value="">No icon</option>
                                <option value="fa-home">fa-home</option>
                                <option value="fa-info">fa-info</option>
                                <option value="fa-globe">fa-globe</option>
                                <option value="fa-plane">fa-plane</option>
                                <option value="fa-star">fa-star</option>
                                <option value="fa-heart">fa-heart</option>
                                <option value="fa-envelope">fa-envelope</option>
                                <option value="fa-phone">fa-phone</option>
                                <option value="fa-user">fa-user</option>
                                <option value="fa-cog">fa-cog</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label>Layout Template</label>
                        <select name="playout">
                            <option value="one col">One Column (Full Width)</option>
                            <option value="two col left">Two Column (Left Sidebar)</option>
                            <option value="two col right">Two Column (Right Sidebar)</option>
                            <option value="three col">Three Columns</option>
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
                        <textarea name="pmeta_desc" style="height: 120px !important;" placeholder="Brief description for search engines...">{{ old('pmeta_desc') }}</textarea>
                    </div>
                    
                    <div>
                        <label>Meta Keywords</label>
                        <input type="text" name="pmeta_keywords" value="{{ old('pmeta_keywords') }}" placeholder="keyword1, keyword2, ...">
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Page Content --}}
        <div class="lg:tw-col-span-8">
            <div class="box tw-flex tw-flex-col tw-min-h-full">
                <h3 class="tw-text-lg tw-font-bold tw-text-orange-900 tw-mb-6 tw-flex tw-items-center tw-justify-between">
                    <span class="tw-flex tw-items-center tw-gap-2">
                        <i class="fa fa-file-text-o tw-text-orange-500"></i> Page Content (en)
                    </span>
                    <button type="submit" class="btn orange">
                        <i class="fa fa-check"></i> Save Page
                    </button>
                </h3>
                
                <div class="tw-flex-1">
                    <textarea name="pcontents" class="tinymce">{!! old('pcontents') !!}</textarea>
                </div>

                <div class="tw-mt-8 tw-bg-slate-50 tw-p-6 tw-rounded-2xl tw-border tw-border-slate-100 tw-flex tw-flex-col sm:tw-flex-row tw-items-center tw-justify-between tw-gap-4">
                    <div class="tw-text-sm tw-text-slate-500 tw-font-medium">
                        <i class="fa fa-info-circle tw-mr-1"></i> Changes will be saved as an <strong>English</strong> variant.
                    </div>
                    <button type="submit" class="btn orange tw-w-full sm:tw-w-auto">
                        <i class="fa fa-check"></i> Create Page Now
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
