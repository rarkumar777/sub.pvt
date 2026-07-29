@extends('admin.layouts.app')

@section('title', 'Admin | Edit Service #' . $service->id)

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
        <span class="tw-text-slate-500">Edit #{{ $service->id }}</span>
    </div>

    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Edit <span class="tw-text-indigo-600">Service</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">{{ $service->description }}</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="btn red">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.services.update', $service->id) }}" class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-12 tw-gap-8">
        @csrf
        @method('PUT')

        {{-- Left: Form Fields --}}
        <div class="lg:tw-col-span-7">
            <div class="box !tw-p-8">
                <div class="tw-flex tw-items-center tw-gap-3 tw-mb-8 tw-pb-4 tw-border-b tw-border-slate-100">
                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-600 tw-flex tw-items-center tw-justify-center">
                        <i class="fa fa-cubes tw-text-xl"></i>
                    </div>
                    <div>
                        <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 !tw-m-0">Service Details</h3>
                        <p class="tw-text-xs tw-text-slate-400 tw-font-medium">Modify service configuration</p>
                    </div>
                </div>

                <div class="tw-flex tw-flex-col tw-gap-6">
                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <label>Description <span class="tw-text-rose-500">*</span></label>
                        <input type="text" name="description" value="{{ $service->description }}" required>
                    </div>
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label>Category</label>
                            <select name="category">
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $service->category == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label>Vendor</label>
                            <select name="vender">
                                <option value="">None</option>
                                @foreach($venders as $v)
                                <option value="{{ $v->id }}" {{ $service->vender == $v->id ? 'selected' : '' }}>{{ $v->first_name }} {{ $v->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="tw-flex tw-flex-col tw-gap-2">
                        <label>Base Cost</label>
                        <input type="text" name="cost" value="{{ $service->cost }}">
                    </div>
                    <div class="tw-flex tw-items-center tw-gap-3 tw-p-4 tw-bg-slate-50 tw-rounded-xl tw-border tw-border-slate-100">
                        <input type="checkbox" name="restricted" value="1" {{ $service->restricted ? 'checked' : '' }} class="!tw-w-5 !tw-h-5 !tw-shadow-none" style="height:20px !important; width:20px !important;">
                        <label class="!tw-mb-0 tw-text-sm tw-font-bold tw-text-slate-700">Restricted Access</label>
                    </div>
                    <div class="tw-pt-4">
                        <button type="submit" class="btn indigo !tw-w-full !tw-py-4">
                            <i class="fa fa-check-circle"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Seasonal Pricing --}}
        <div class="lg:tw-col-span-5">
            <div class="box !tw-p-0 !tw-overflow-hidden">
                <div class="tw-px-8 tw-py-5 tw-bg-slate-50 tw-border-b tw-border-slate-100 tw-flex tw-items-center tw-justify-between">
                    <div class="tw-flex tw-items-center tw-gap-3">
                        <div class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-amber-50 tw-text-amber-600 tw-flex tw-items-center tw-justify-center">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <span class="tw-text-sm tw-font-bold tw-text-slate-900">Seasonal Pricing</span>
                    </div>
                    <span class="tw-px-2.5 tw-py-1 tw-bg-white tw-border tw-border-slate-100 tw-text-slate-500 tw-text-[11px] tw-font-bold tw-rounded-lg">{{ $service->seasons->count() }} seasons</span>
                </div>
                <div class="tw-overflow-x-auto">
                    <table class="tw-w-full tw-text-left tw-border-collapse">
                        <thead>
                            <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                                <th class="tw-py-3 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">From</th>
                                <th class="tw-py-3 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">To</th>
                                <th class="tw-py-3 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Cost</th>
                            </tr>
                        </thead>
                        <tbody class="tw-divide-y tw-divide-slate-50">
                            @forelse($service->seasons as $season)
                            <tr class="hover:tw-bg-slate-50/50 tw-transition-colors">
                                <td class="tw-py-3 tw-px-6 tw-text-xs tw-font-medium tw-text-slate-700">{{ $season->date_from }}</td>
                                <td class="tw-py-3 tw-px-6 tw-text-xs tw-font-medium tw-text-slate-700">{{ $season->date_to }}</td>
                                <td class="tw-py-3 tw-px-6 tw-text-xs tw-font-bold tw-text-emerald-600">{{ number_format($season->cost, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="tw-py-10 tw-text-center tw-text-slate-400 tw-text-xs tw-font-bold">No seasonal pricing configured</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
