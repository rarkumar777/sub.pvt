@extends('admin.layouts.app')
@section('content')
<div class="breadcrumb pad-t">
    <a href="{{ route('admin.tours.index') }}" class="orange"><i class="fa-plane"></i> Tours</a>
    <a href="{{ route('admin.tours.edit', $tour->id) }}" class="orange"><i class="fa-edit"></i> Edit > Tour #{{ $tour->id }}</a>
    <a class="active"><i class="fa-film"></i> Images</a>
</div>

@php $content = $tour->contents->where('lang', 'en')->first(); @endphp
<div class="sd-12">
    <h3><i class="fa-plane"></i> {{ $content->title ?? 'Tour #'.$tour->id }} -> Images</h3>
    <a class="btn orange absolute top right h-gap" href="#add_image"><i class="fa-plus"></i> <span class="hide-sd">Add New</span></a>
</div>

@if(session('success'))
<div class="row"><div class="success-box pad"><i class="fa-check"></i> {{ session('success') }}</div></div>
@endif

@include('admin.tours._edit_menu', ['tour' => $tour])

<div class="row">
    @foreach($images as $img)
    <div class="bd-3 md-4 h-pad">
        <div style="max-width:350px; width:100%; margin:auto;">
            <div class="btn-group absolute">
                @if($tour->image == $img->image)
                    <span class="pad pull-left orange small">Main Image</span>
                @else
                    <a href="{{ route('admin.tours.images', $tour->id) }}?setmain={{ $img->id }}" class="btn small pad orange"><i class="fa-flag"></i></a>
                    <form action="{{ route('admin.tours.images.destroy', [$tour->id, $img->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this image?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn small pad red"><i class="fa-trash-o"></i></button>
                    </form>
                @endif
            </div>
            <img class="full-width" src="{{ $img->image }}">
        </div>
    </div>
    @endforeach
</div>

{{-- Add Image Modal --}}
<div class="modal" id="add_image">
    <div>
        <a href="#close" title="Close" class="close">&times;</a>
        <h3><i class="fa-plus"></i> Add New</h3><br>
        <form method="POST" action="{{ route('admin.tours.images.store', $tour->id) }}">
            @csrf
            <div class="pad align-center">
                <input type="text" name="image" placeholder="Enter image URL" class="full-width" required>
            </div>
            <div id="current_image"></div>
            <hr>
            <button type="submit" class="btn orange gap-t"><i class="fa-check"></i> Save</button>
        </form>
    </div>
</div>
@endsection
