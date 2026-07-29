@extends('admin.layouts.app')
@section('content')
<div class="breadcrumb pad-t">
    <a href="{{ route('admin.tours.index') }}" class="green"><i class="fa-plane"></i> Tours</a>
    <a href="{{ route('admin.tours.edit', $tour->id) }}" class="green"><i class="fa-edit"></i> Edit > Tour #{{ $tour->id }}</a>
    <a class="active"><i class="fa-bars"></i> Tour Menu</a>
</div>

@php $content = $tour->contents->where('lang', 'en')->first(); @endphp
<div class="sd-12">
    <h3><i class="fa-plane"></i> {{ $content->title ?? 'Tour #'.$tour->id }} -> Edit Menu</h3>
</div>

@include('admin.tours._edit_menu', ['tour' => $tour])

<div class="row">
    <div class="bordered pad">
        <p>Tour management sub-pages for Tour #{{ $tour->id }}:</p>
        <div class="row">
            <div class="md-4 pad"><a href="{{ route('admin.tours.edit', $tour->id) }}" class="btn blue full-width"><i class="fa-edit"></i> Edit Tour</a></div>
            <div class="md-4 pad"><a href="{{ route('admin.tours.images', $tour->id) }}" class="btn blue full-width"><i class="fa-film"></i> Images</a></div>
            <div class="md-4 pad"><a href="{{ route('admin.tours.pricing', $tour->id) }}" class="btn blue full-width"><i class="fa-money"></i> Pricing</a></div>
        </div>
        <div class="row">
            <div class="md-4 pad"><a href="{{ route('admin.tours.inclusions', $tour->id) }}" class="btn blue full-width"><i class="fa-list"></i> Inclusions</a></div>
            <div class="md-4 pad"><a href="{{ route('admin.tours.itinerary', $tour->id) }}" class="btn blue full-width"><i class="fa-map-marker"></i> Itinerary</a></div>
            <div class="md-4 pad"><a href="{{ route('admin.tours.seasons', $tour->id) }}" class="btn blue full-width"><i class="fa-calendar"></i> Seasons</a></div>
        </div>
        <div class="row">
            <div class="md-4 pad"><a href="{{ route('admin.tours.tec', $tour->id) }}" class="btn blue full-width"><i class="fa-star"></i> TEC Details</a></div>
            <div class="md-4 pad"><a href="{{ route('admin.tours.departures', $tour->id) }}" class="btn blue full-width"><i class="fa-rocket"></i> Departures</a></div>
        </div>
    </div>
</div>
@endsection
