@extends('admin.layouts.app')
@section('title', 'Admin | Tour TEC Details')

@section('content')
@php $content = $tour->contents->where('lang', 'en')->first(); @endphp

<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumb --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
        <a href="{{ route('admin.tours.index') }}" class="tw-text-indigo-600 tw-font-semibold tw-no-underline hover:tw-text-indigo-800 tw-transition-colors">
            <i class="fa fa-plane tw-mr-1"></i> Tours
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <a href="{{ route('admin.tours.edit', $tour->id) }}" class="tw-text-indigo-600 tw-font-semibold tw-no-underline hover:tw-text-indigo-800 tw-transition-colors">
            Edit Tour #{{ $tour->id }}
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <span class="tw-text-slate-500 tw-font-semibold">TEC Details</span>
    </div>

    {{-- Header --}}
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">{{ $content->title ?? 'Tour #'.$tour->id }}</h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure technical experience & comfort ratings</p>
    </div>

    @include('admin.tours._edit_menu', ['tour' => $tour])

    {{-- TEC Form --}}
    @php
        $tec = @unserialize($tour->tec ?: '', ['allowed_classes' => false]);
        $tec = is_array($tec) ? $tec : ['enable' => [], 'rates' => []];
        $tecItems = ['fitness' => 'Fitness Level', 'comfort' => 'Comfort Level', 'adventure' => 'Adventure Level', 'cultural' => 'Cultural Exposure', 'wildlife' => 'Wildlife Exposure'];
    @endphp

    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-px-8 tw-py-5 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
            <h3 class="tw-text-sm tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                <i class="fa fa-star tw-text-amber-500"></i> Experience Ratings
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.tours.tec', $tour->id) }}">
            @csrf
            <div class="tw-overflow-x-auto">
                <table class="tw-w-full tw-text-left tw-border-collapse">
                    <thead>
                        <tr class="tw-bg-slate-50/30 tw-border-b tw-border-slate-100">
                            <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Detail</th>
                            <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-center">Enable</th>
                            <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Rating (1–5)</th>
                        </tr>
                    </thead>
                    <tbody class="tw-divide-y tw-divide-slate-50">
                        @foreach($tecItems as $key => $label)
                        <tr class="hover:tw-bg-slate-50/50 tw-transition-colors">
                            <td class="tw-py-5 tw-px-6">
                                <span class="tw-font-bold tw-text-slate-900 tw-text-sm">{{ $label }}</span>
                            </td>
                            <td class="tw-py-5 tw-px-6 tw-text-center">
                                <label class="tw-inline-flex tw-items-center tw-cursor-pointer">
                                    <input type="checkbox" name="tec[enable][]" value="{{ $key }}" {{ in_array($key, $tec['enable'] ?? []) ? 'checked' : '' }} class="tw-w-4 tw-h-4 tw-rounded tw-border-slate-300 tw-text-indigo-600">
                                </label>
                            </td>
                            <td class="tw-py-5 tw-px-6">
                                <select name="tec[rates][{{ $key }}]" class="!tw-w-24">
                                    @for($r=1; $r<=5; $r++)
                                    <option value="{{ $r }}" {{ ($tec['rates'][$key] ?? 0) == $r ? 'selected' : '' }}>{{ $r }} ★</option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="tw-px-8 tw-py-5 tw-bg-slate-50/30 tw-border-t tw-border-slate-100">
                <button type="submit" class="btn indigo"><i class="fa fa-check"></i> Save TEC Details</button>
            </div>
        </form>
    </div>
</div>
@endsection
