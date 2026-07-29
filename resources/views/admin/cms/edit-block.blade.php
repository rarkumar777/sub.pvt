@extends('admin.layouts.app')
@section('title', 'Admin | Edit Block')
@section('content')
<div class="breadcrumb pad-t">
    <a class="active"><i class="fa-server"></i> CMS</a>
    <a href="{{ route('admin.customblocks.index') }}" class="green"><i class="fa-th"></i> Custom Blocks</a>
    <a class="active"><i class="fa-edit"></i> {{ $bname }}</a>
</div>

<div class="sd-12">
    <h1><i class="fa-edit"></i> edit ({{ $bname }}-{{ $blang }})</h1>
</div>

@if(session('success'))
<div class="row"><div class="sd-12 green-bg white pad">{{ session('success') }}</div></div>
@endif

<div class="row blue">
    <div class="md-9 bordered-t">
        <div class="btn-group">
            <span class="pull-left pad-t pad-l pad-r "><strong>Translate</strong></span>
            @foreach($langs as $tl)
                <a class="btn {{ $blang == $tl ? 'white' : 'blue' }} small" href="{{ route('admin.customblocks.edit', $bname) }}?lang={{ $tl }}">{{ $tl }}</a>
            @endforeach
        </div>
    </div>
    <div class="md-3 bordered-t">

        <div class="btn-group pull-right">
            <a href="{{ route('admin.customblocks.index') }}" class="btn red small"><i class="fa-close"></i> <span class="hide-sd">cancel</span></a>
            <a href="#" class="btn green small" onclick="document.forms.edit_block.submit(); return false"><i class="fa-check"></i> <span class="hide-sd">Save</span></a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.customblocks.update', $bname) }}?lang={{ $blang }}" name="edit_block">
    @csrf
    <input type="hidden" name="blang" value="{{ $blang }}">
    <textarea name="block_code" class="tinymce full-width" rows="20">{!! $blockContent !!}</textarea>
</form>

{{-- TinyMCE (local from old project) --}}
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
