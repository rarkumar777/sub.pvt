@extends('admin.layouts.app')
@section('title', 'Admin | New Service')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Navigation Tabs --}}
    @include('admin.quotations._nav')

    {{-- Breadcrumb --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">
        <i class="fa fa-pie-chart"></i> Quotations
        <i class="fa fa-angle-right"></i>
        <a href="{{ route('admin.services.index') }}" class="tw-text-indigo-500 hover:tw-text-indigo-600 tw-transition-all">Services</a>
        <i class="fa fa-angle-right"></i>
        <span class="tw-text-slate-500">Add Service</span>
    </div>

    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">New <span class="tw-text-indigo-600">Service</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Add a new expense service resource</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="btn red">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="box !tw-p-8">
        <form method="POST" action="{{ route('admin.services.store') }}" class="tw-flex tw-flex-col tw-gap-6">
            @csrf
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Description <span class="tw-text-rose-500">*</span></label>
                    <input type="text" name="description" value="{{ old('description') }}" placeholder="Service description..." required>
                </div>
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Category</label>
                    <select name="category">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Base Cost</label>
                    <input type="text" name="cost" value="{{ old('cost', 0) }}" placeholder="0.00">
                </div>
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Vendor</label>
                    <select name="vender">
                        <option value="">None</option>
                        @foreach($venders as $v)
                        <option value="{{ $v->id }}">{{ $v->first_name }} {{ $v->last_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="tw-pt-4 tw-flex tw-justify-center">
                <button type="submit" class="btn indigo !tw-px-10 !tw-py-4">
                    <i class="fa fa-check-circle"></i> Create Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
