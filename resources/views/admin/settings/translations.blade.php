@extends('admin.layouts.app')
@section('content')
<div class="breadcrumb pad-t"><a class="active"><i class="fa-language"></i> Translations</a></div>
<div class="sd-12"><h3><i class="fa-language"></i> Translation Editor</h3></div>
<div class="row"><div class="bordered pad">
    <div class="row pad">
        <label>Select Language File</label>
        <select class="full-width" id="lang_file">
            <option value="">Select...</option>
            <option value="en">English</option>
            <option value="ar">Arabic</option>
            <option value="de">German</option>
            <option value="zh">Chinese</option>
        </select>
    </div>
    <hr>
    <div id="translation_editor">
        <p class="align-center pad grey">Select a language file to edit translations</p>
    </div>
</div></div>
@endsection
