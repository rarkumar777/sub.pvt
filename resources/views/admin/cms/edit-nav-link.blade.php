@extends('admin.layouts.app')
@section('title', 'Admin | Edit Nav Link')

@section('breadcrumb')
<div class="breadcrumb pad-t">
    <a class="active"><i class="fa-server"></i> CMS</a>
    <a href="{{ route('admin.nav.index') }}" class="green"><i class="fa-indent"></i> Top Nav</a>
    <a class="active"><i class="fa-edit"></i> Edit</a>
</div>
@endsection

@section('content')
<div class="sd-12 relative">
    <h1><i class="fa-edit"></i> edit</h1>
    <a href="{{ route('admin.nav.index') }}" class="btn red absolute top h-gap right"><i class="fa-close"></i> <span class="hide-sd">cancel</span></a>
</div>

<div class="row">
    <form method="POST" action="{{ route('admin.nav.update', $navLinkId) }}">
        @csrf
        @method('PUT')
        
        <div class="bordered pad">
            @foreach($langs as $lang)
                <div class="row pad-v">
                    <div class="sd-2"><label>label ({{ $lang }})</label></div>
                    <div class="sd-10"><input type="text" name="link_label{{ $lang }}" class="full-width" value="{{ $navLinks[$lang]->label ?? '' }}"></div>
                </div>
                <div class="row pad-v">
                    <div class="sd-2"><label>Url ({{ $lang }})</label></div>
                    <div class="sd-10"><input type="text" name="link_url{{ $lang }}" class="full-width" value="{{ $navLinks[$lang]->link ?? '' }}"></div>
                </div>
                @if(isset($navLinks[$lang]))
                    <input type="hidden" name="action_{{ $lang }}" value="edit">
                @else
                    <input type="hidden" name="action_{{ $lang }}" value="insert">
                @endif
                <hr>
            @endforeach

            <input type="hidden" name="parent" value="{{ $navLink->parent_id }}">

            <div class="row pad-v">
                <div class="sd-2"><label>Select Icon</label></div>
                <div class="sd-10">
                    <select name="selected_icon" class="full-width">
                        <option value="" {{ empty($navLink->icon) ? 'selected' : '' }}>no icon</option>
                        <option value="fa-home" {{ $navLink->icon == 'fa-home' ? 'selected' : '' }}>fa-home</option>
                        <option value="fa-info" {{ $navLink->icon == 'fa-info' ? 'selected' : '' }}>fa-info</option>
                        <option value="fa-globe" {{ $navLink->icon == 'fa-globe' ? 'selected' : '' }}>fa-globe</option>
                        <option value="fa-map-marker" {{ $navLink->icon == 'fa-map-marker' ? 'selected' : '' }}>fa-map-marker</option>
                        <option value="fa-plane" {{ $navLink->icon == 'fa-plane' ? 'selected' : '' }}>fa-plane</option>
                        <option value="fa-car" {{ $navLink->icon == 'fa-car' ? 'selected' : '' }}>fa-car</option>
                        <option value="fa-hotel" {{ $navLink->icon == 'fa-hotel' ? 'selected' : '' }}>fa-hotel</option>
                        <option value="fa-cutlery" {{ $navLink->icon == 'fa-cutlery' ? 'selected' : '' }}>fa-cutlery</option>
                        <option value="fa-camera" {{ $navLink->icon == 'fa-camera' ? 'selected' : '' }}>fa-camera</option>
                        <option value="fa-star" {{ $navLink->icon == 'fa-star' ? 'selected' : '' }}>fa-star</option>
                        <option value="fa-heart" {{ $navLink->icon == 'fa-heart' ? 'selected' : '' }}>fa-heart</option>
                        <option value="fa-envelope" {{ $navLink->icon == 'fa-envelope' ? 'selected' : '' }}>fa-envelope</option>
                        <option value="fa-phone" {{ $navLink->icon == 'fa-phone' ? 'selected' : '' }}>fa-phone</option>
                        <option value="fa-user" {{ $navLink->icon == 'fa-user' ? 'selected' : '' }}>fa-user</option>
                        <option value="fa-cog" {{ $navLink->icon == 'fa-cog' ? 'selected' : '' }}>fa-cog</option>
                        @if($navLink->icon && !in_array($navLink->icon, ['', 'fa-home', 'fa-info', 'fa-globe', 'fa-map-marker', 'fa-plane', 'fa-car', 'fa-hotel', 'fa-cutlery', 'fa-camera', 'fa-star', 'fa-heart', 'fa-envelope', 'fa-phone', 'fa-user', 'fa-cog']))
                            <option value="{{ $navLink->icon }}" selected>{{ $navLink->icon }}</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="row pad-v">
                <div class="sd-2"><label>Target</label></div>
                <div class="sd-10">
                    <select name="link_target" class="full-width">
                        <option value="_self" {{ $navLink->target == '_self' ? 'selected' : '' }}>Open in Same Window</option>
                        <option value="_blank" {{ $navLink->target == '_blank' ? 'selected' : '' }}>Open in New Window</option>
                    </select>
                </div>
            </div>

            <hr>

            <button type="submit" class="btn blue gap-t"><i class="fa-check"></i> Save</button>
        </div>
    </form>
</div>
@endsection
