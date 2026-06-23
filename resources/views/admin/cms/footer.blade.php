@extends('admin.layouts.app')
@section('title', 'Admin | Edit Footer Contents')

@section('breadcrumb')
<div class="breadcrumb pad-t">
    <a class="active"><i class="fa-server"></i> CMS</a>
    <a class="active"><i class="fa-level-down"></i> Footer contents</a>
</div>
@endsection

@section('content')
@if(session('success'))<div class="row"><div class="success-box pad"><i class="fa-check"></i> {{ session('success') }}</div></div>@endif

<h1><i class="fa-edit"></i> edit Footer contents({{ $currentLang }})</h1>

<div class="row blue">
    <div class="md-9 bordered-t">
        <div class="btn-group">
            <span class="pull-left pad-t pad-l pad-r"><strong>Translate</strong></span>
            @foreach($langs as $tl)
                <a class="btn {{ $currentLang == $tl ? 'white' : 'blue' }} small" href="{{ route('admin.footer.index', ['lang' => $tl]) }}">{{ $tl }}</a>
            @endforeach
        </div>
    </div>
    <div class="md-3 bordered-t">
        <div class="btn-group pull-right">
            <a href="#" class="btn green small" onclick="document.forms.edit_footer.submit(); return false"><i class="fa-check"></i> <span class="hide-sd">Save</span></a>
        </div>
    </div>
</div>

<form name="edit_footer" method="POST" action="{{ route('admin.footer.update') }}">
    @csrf
    <input type="hidden" name="lang" value="{{ $currentLang }}">
    <textarea name="footer_contents" class="tinymce full-width">{!! $footerContent !!}</textarea>
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
    height: 350,
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
