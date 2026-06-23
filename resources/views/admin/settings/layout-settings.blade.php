@extends('admin.layouts.app')
@section('title', 'Admin | Layouts Settings')
@section('content')
<div class="breadcrumb pad-t">
    <a class="active"><i class="fa-server"></i> CMS</a>
    <a href="{{ route('admin.settings.layouts') }}" class="green"><i class="fa-sliders"></i> Layouts</a>
    <a class="active"><i class="fa-columns"></i> Layouts Settings</a>
</div>
<h1><i class="fa-columns"></i> Layouts Settings</h1><hr>

@if(session('success'))
<div class="row"><div class="sd-12 green-bg white pad">{{ session('success') }}</div></div>
@endif
@if(session('error'))
<div class="row"><div class="sd-12 red-bg white pad">{{ session('error') }}</div></div>
@endif

<form method="POST" action="{{ route('admin.settings.layout-settings.save') }}">
    @csrf
    @foreach($items as $key => $label)
    <div class="cell">
        <div class="row" style="padding:8px 0;">
            <div class="sd-5"><label>{{ $label }}</label></div>
            <div class="sd-7">
                <select name="{{ $key }}" class="full-width">
                    @foreach($layouts as $layoutName => $colType)
                    <option value="{{ $layoutName }}" {{ ($settings[$key] ?? '') == $layoutName ? 'selected' : '' }}>{{ $layoutName }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @endforeach
    <div class="align-center gap-t">
        <button type="submit" class="btn green gap-t"><i class="fa-check"></i> Save</button>
    </div>
</form>
@endsection
