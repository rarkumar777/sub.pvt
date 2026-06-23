@extends('admin.layouts.app')
@section('title', 'Admin | Services Settings')

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
        <span class="tw-text-slate-500">Settings</span>
    </div>

    {{-- Header --}}
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Services <span class="tw-text-indigo-600">Settings</span></h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure global service parameters</p>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-xl"></i>
        <p class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <div class="box !tw-p-8">
        <form action="{{ route('admin.services.settings.update') }}" method="POST" class="tw-flex tw-flex-col tw-gap-6">
            @csrf
            
            <div class="tw-flex tw-flex-col tw-gap-2">
                <label>Venders User Group</label>
                <select name="venders_group">
                    @foreach($userGroups as $group)
                    <option value="{{ $group }}" {{ $vendersGroup == $group ? 'selected' : '' }}>{{ $group }}</option>
                    @endforeach
                </select>
            </div>

            <div class="tw-flex tw-flex-col tw-gap-2">
                <label>E-mail Templates</label>
                <textarea name="email_template" class="!tw-min-h-[120px] !tw-font-mono !tw-text-sm" style="min-height:120px; resize:vertical; font-family:monospace;">{{ $emailTemplate }}</textarea>
            </div>

            <div class="tw-flex tw-flex-col tw-gap-2">
                <label>Countries</label>
                <select name="countries[]" id="countries_select" multiple>
                    @foreach($countries as $c)
                    <option value="{{ $c->id }}" {{ in_array($c->id, $selectedCountries) ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="tw-pt-4 tw-border-t tw-border-slate-100 tw-flex tw-justify-center">
                <button type="submit" class="btn indigo !tw-px-10 !tw-py-4">
                    <i class="fa fa-check-circle"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    new SlimSelect({
        select: '#countries_select',
        placeholder: 'Select Countries',
        @if(empty($selectedCountries))
        data: [{ 'placeholder': true, 'text': 'Select Value' }]
        @endif
    });
});
</script>
@endsection
