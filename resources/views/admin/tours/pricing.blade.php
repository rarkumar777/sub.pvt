@extends('admin.layouts.app')
@section('title', 'Admin | Tour Pricing')

@section('content')
@php
    $content = $tour->contents->where('lang', 'en')->first();
    $extras = [];
    if ($tour->pricing_extras) {
        $extras = json_decode($tour->pricing_extras, true) ?: [];
    }
@endphp

<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumb --}}
    <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm">
        <a href="{{ route('admin.tours.index') }}" class="tw-text-orange-600 tw-font-semibold tw-no-underline hover:tw-text-orange-800 tw-transition-colors">
            <i class="fa fa-plane tw-mr-1"></i> Tours
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <a href="{{ route('admin.tours.edit', $tour->id) }}" class="tw-text-orange-600 tw-font-semibold tw-no-underline hover:tw-text-orange-800 tw-transition-colors">
            Edit Tour #{{ $tour->id }}
        </a>
        <i class="fa fa-chevron-right tw-text-slate-300 tw-text-[11px]"></i>
        <span class="tw-text-slate-500 tw-font-semibold">Pricing</span>
    </div>

    {{-- Header --}}
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">{{ $content->title ?? 'Tour #'.$tour->id }}</h1>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure base pricing, hotel grades, transportation, and all additional costs</p>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-lg"></i>
        <span class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</span>
    </div>
    @endif

    @include('admin.tours._edit_menu', ['tour' => $tour])

    <form method="POST" action="{{ route('admin.tours.pricing.update', $tour->id) }}">
        @csrf

        {{-- Base Pricing --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-8 tw-py-5 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                <h3 class="tw-text-sm tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                    <i class="fa fa-money tw-text-emerald-500"></i> Base Pricing
                </h3>
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6 tw-p-8">
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Min Price</label>
                    <input type="text" name="min_price" value="{{ $tour->min_price }}" placeholder="0.00">
                </div>
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Max Price</label>
                    <input type="text" name="max_price" value="{{ $tour->max_price }}" placeholder="0.00">
                </div>
            </div>
        </div>

        {{-- Pricing Bases by Hotel Grade --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-8 tw-py-5 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                <h3 class="tw-text-sm tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                    <i class="fa fa-star tw-text-amber-500"></i> Pricing by Hotel Grade
                </h3>
                <p class="tw-text-xs tw-text-slate-400 tw-mt-1">Configure pricing per hotel star rating</p>
            </div>
            @php
                $bases = @unserialize($tour->pricing_bases ?: '', ['allowed_classes' => false]);
                $bases = is_array($bases) ? $bases : [];
            @endphp
            <div class="tw-overflow-x-auto">
                <table class="tw-w-full tw-text-left tw-border-collapse">
                    <thead>
                        <tr class="tw-bg-slate-50/30 tw-border-b tw-border-slate-100">
                            <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Grade</th>
                            <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Price</th>
                            <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Single Supplement</th>
                        </tr>
                    </thead>
                    <tbody class="tw-divide-y tw-divide-slate-50">
                        @for($i = 0; $i <= 5; $i++)
                        <tr class="hover:tw-bg-slate-50/50 tw-transition-colors">
                            <td class="tw-py-4 tw-px-6">
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    @if($i == 0)
                                        <span class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-slate-100 tw-text-slate-500 tw-flex tw-items-center tw-justify-center tw-text-xs tw-font-bold">—</span>
                                        <span class="tw-font-bold tw-text-slate-900 tw-text-sm">No Hotel</span>
                                    @else
                                        <span class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-amber-50 tw-text-amber-600 tw-flex tw-items-center tw-justify-center tw-text-xs tw-font-bold">{{ $i }}★</span>
                                        <span class="tw-font-bold tw-text-slate-900 tw-text-sm">{{ $i }} Star</span>
                                    @endif
                                </div>
                            </td>
                            <td class="tw-py-4 tw-px-6">
                                <input type="text" name="bases[{{ $i }}][price]" value="{{ $bases[$i]['price'] ?? '' }}" placeholder="0.00" class="!tw-w-40">
                            </td>
                            <td class="tw-py-4 tw-px-6">
                                <input type="text" name="bases[{{ $i }}][single_supplement]" value="{{ $bases[$i]['single_supplement'] ?? '' }}" placeholder="0.00" class="!tw-w-40">
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Additional Cost Items (Transportation, Guides, Extras) --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-8 tw-py-5 tw-bg-orange-50/50 tw-border-b tw-border-orange-100 tw-flex tw-items-center tw-justify-between">
                <div>
                    <h3 class="tw-text-sm tw-font-bold tw-text-orange-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                        <i class="fa fa-truck tw-text-orange-500"></i> Additional Cost Items
                    </h3>
                    <p class="tw-text-xs tw-text-orange-400 tw-mt-1">Transportation, guides, entrance fees, and all other costs</p>
                </div>
                <button type="button" onclick="addPricingExtra()" class="tw-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-rounded-xl tw-bg-orange-500 tw-text-white tw-text-xs tw-font-bold hover:tw-bg-orange-600 tw-transition-colors tw-border-0 tw-cursor-pointer">
                    <i class="fa fa-plus"></i> Add Cost Item
                </button>
            </div>
            <div class="tw-p-6">
                {{-- Header Row --}}
                <div class="tw-grid tw-grid-cols-12 tw-gap-3 tw-mb-3 tw-px-2">
                    <div class="tw-col-span-5">
                        <span class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Description</span>
                    </div>
                    <div class="tw-col-span-2">
                        <span class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Cost</span>
                    </div>
                    <div class="tw-col-span-2">
                        <span class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Pricing Type</span>
                    </div>
                    <div class="tw-col-span-2">
                        <span class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Category</span>
                    </div>
                    <div class="tw-col-span-1"></div>
                </div>

                <div id="extras-body" class="tw-space-y-3">
                    @foreach($extras as $eIdx => $extra)
                    <div class="tw-grid tw-grid-cols-12 tw-gap-3 tw-items-center tw-p-3 tw-rounded-xl tw-bg-slate-50/50 tw-border tw-border-slate-100 pricing-extra-row">
                        <div class="tw-col-span-5">
                            <input type="text" name="extras[{{ $eIdx }}][name]" value="{{ $extra['name'] ?? '' }}" placeholder="e.g. Airport Transfer, Local Guide..." class="!tw-mb-0" required>
                        </div>
                        <div class="tw-col-span-2">
                            <input type="text" name="extras[{{ $eIdx }}][cost]" value="{{ $extra['cost'] ?? '' }}" placeholder="0.00" class="!tw-mb-0">
                        </div>
                        <div class="tw-col-span-2">
                            <select name="extras[{{ $eIdx }}][pricing_type]" class="!tw-mb-0 !tw-text-xs">
                                <option value="per_person" {{ ($extra['pricing_type'] ?? '') == 'per_person' ? 'selected' : '' }}>Per Person</option>
                                <option value="per_group" {{ ($extra['pricing_type'] ?? '') == 'per_group' ? 'selected' : '' }}>Per Group</option>
                                <option value="fixed" {{ ($extra['pricing_type'] ?? '') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                            </select>
                        </div>
                        <div class="tw-col-span-2">
                            <select name="extras[{{ $eIdx }}][category]" class="!tw-mb-0 !tw-text-xs">
                                <option value="transportation" {{ ($extra['category'] ?? '') == 'transportation' ? 'selected' : '' }}>Transportation</option>
                                <option value="guide" {{ ($extra['category'] ?? '') == 'guide' ? 'selected' : '' }}>Guide</option>
                                <option value="entrance" {{ ($extra['category'] ?? '') == 'entrance' ? 'selected' : '' }}>Entrance Fees</option>
                                <option value="meals" {{ ($extra['category'] ?? '') == 'meals' ? 'selected' : '' }}>Meals</option>
                                <option value="insurance" {{ ($extra['category'] ?? '') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                <option value="visa" {{ ($extra['category'] ?? '') == 'visa' ? 'selected' : '' }}>Visa</option>
                                <option value="other" {{ ($extra['category'] ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="tw-col-span-1 tw-text-right">
                            <button type="button" onclick="this.closest('.pricing-extra-row').remove(); toggleExtrasEmpty();" class="tw-w-8 tw-h-8 tw-inline-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-rose-50 tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all tw-border-0 tw-cursor-pointer">
                                <i class="fa fa-trash tw-text-xs"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div id="extras-empty" class="tw-py-10 tw-text-center {{ count($extras) ? 'tw-hidden' : '' }}">
                    <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                        <div class="tw-w-14 tw-h-14 tw-rounded-full tw-bg-orange-50 tw-flex tw-items-center tw-justify-center tw-text-orange-200">
                            <i class="fa fa-truck tw-text-2xl"></i>
                        </div>
                        <p class="tw-text-slate-400 tw-text-sm tw-font-bold">No additional costs added</p>
                        <button type="button" onclick="addPricingExtra()" class="tw-text-orange-600 tw-text-xs tw-font-bold hover:tw-underline tw-border-0 tw-bg-transparent tw-cursor-pointer">
                            <i class="fa fa-plus"></i> Add transportation, guides, fees...
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn orange tw-w-full !tw-py-4 !tw-text-base tw-shadow-lg tw-shadow-orange-100">
            <i class="fa fa-check-circle"></i> Save Pricing
        </button>
    </form>
</div>

<script>
var extraCounter = 500;

function addPricingExtra() {
    extraCounter++;
    var container = document.getElementById('extras-body');
    var html = '<div class="tw-grid tw-grid-cols-12 tw-gap-3 tw-items-center tw-p-3 tw-rounded-xl tw-bg-slate-50/50 tw-border tw-border-slate-100 pricing-extra-row">' +
        '<div class="tw-col-span-5">' +
            '<input type="text" name="extras[' + extraCounter + '][name]" value="" placeholder="e.g. Airport Transfer, Local Guide..." class="!tw-mb-0" required>' +
        '</div>' +
        '<div class="tw-col-span-2">' +
            '<input type="text" name="extras[' + extraCounter + '][cost]" value="" placeholder="0.00" class="!tw-mb-0">' +
        '</div>' +
        '<div class="tw-col-span-2">' +
            '<select name="extras[' + extraCounter + '][pricing_type]" class="!tw-mb-0 !tw-text-xs">' +
                '<option value="per_person">Per Person</option>' +
                '<option value="per_group">Per Group</option>' +
                '<option value="fixed">Fixed</option>' +
            '</select>' +
        '</div>' +
        '<div class="tw-col-span-2">' +
            '<select name="extras[' + extraCounter + '][category]" class="!tw-mb-0 !tw-text-xs">' +
                '<option value="transportation">Transportation</option>' +
                '<option value="guide">Guide</option>' +
                '<option value="entrance">Entrance Fees</option>' +
                '<option value="meals">Meals</option>' +
                '<option value="insurance">Insurance</option>' +
                '<option value="visa">Visa</option>' +
                '<option value="other">Other</option>' +
            '</select>' +
        '</div>' +
        '<div class="tw-col-span-1 tw-text-right">' +
            '<button type="button" onclick="this.closest(\'.pricing-extra-row\').remove(); toggleExtrasEmpty();" class="tw-w-8 tw-h-8 tw-inline-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-rose-50 tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all tw-border-0 tw-cursor-pointer">' +
                '<i class="fa fa-trash tw-text-xs"></i>' +
            '</button>' +
        '</div>' +
    '</div>';
    container.insertAdjacentHTML('beforeend', html);
    document.getElementById('extras-empty').classList.add('tw-hidden');
    container.lastElementChild.querySelector('input').focus();
}

function toggleExtrasEmpty() {
    var container = document.getElementById('extras-body');
    var empty = document.getElementById('extras-empty');
    if (container.children.length === 0) {
        empty.classList.remove('tw-hidden');
    } else {
        empty.classList.add('tw-hidden');
    }
}
</script>
@endsection
