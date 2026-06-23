@extends('admin.layouts.app')

@section('title', 'Admin | Fast Access Configuration')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">

    @include('admin.quotations._nav')

    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Fast Access <span class="tw-text-orange-600">Configuration</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Select categories to appear in the quick-add panel during quotation building.</p>
        </div>
        <button type="submit" form="fast-access-form" class="btn orange !tw-px-8">
            <i class="fa fa-save"></i> Save Changes
        </button>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-xl"></i>
        <p class="tw-text-emerald-800 tw-font-bold tw-text-sm tw-m-0">{{ session('success') }}</p>
    </div>
    @endif

    @php
        $colorMap = ['#4f46e5', '#0ea5e9', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444'];

        function buildFlatTree($categories, $parentId, $countryId, $selected, $depth = 0) {
            $items = [];
            foreach ($categories as $k => $v) {
                if ($v['parent_id'] == $parentId) {
                    $fieldName = 'country_' . $countryId . '_' . $k;
                    $isChecked = isset($selected[$k]);
                    $children = buildFlatTree($categories, $k, $countryId, $selected, $depth + 1);
                    $items[] = [
                        'id' => $k,
                        'name' => $v['name'],
                        'field' => $fieldName,
                        'checked' => $isChecked,
                        'depth' => $depth,
                        'children' => $children,
                    ];
                }
            }
            return $items;
        }

        function renderCheckboxGroup($items) {
            $html = '';
            foreach ($items as $item) {
                $isTop = $item['depth'] === 0;
                $hasChildren = !empty($item['children']);
                $checkedAttr = $item['checked'] ? ' checked' : '';

                if ($isTop && $hasChildren) {
                    // Top level with children = collapsible group header
                    $html .= '<div class="tw-mb-3">';
                    $html .= '<div class="tw-flex tw-items-center tw-gap-3 tw-bg-slate-800 tw-text-white tw-px-4 tw-py-2.5 tw-rounded-xl">';
                    $html .= '<input type="checkbox" name="' . $item['field'] . '" id="' . $item['field'] . '" value="1"' . $checkedAttr . ' class="group-check tw-w-4 tw-h-4 tw-accent-orange-400 tw-flex-shrink-0">';
                    $html .= '<label for="' . $item['field'] . '" class="tw-text-sm tw-font-bold tw-text-white tw-cursor-pointer tw-flex-1 tw-m-0">' . htmlspecialchars($item['name']) . '</label>';
                    $html .= '<span class="tw-text-xs tw-text-slate-400">' . count($item['children']) . ' sub</span>';
                    $html .= '</div>';
                    $html .= '<div class="tw-grid tw-grid-cols-2 tw-gap-2 tw-mt-2 tw-pl-3">';
                    $html .= renderCheckboxGroup($item['children']);
                    $html .= '</div>';
                    $html .= '</div>';
                } elseif ($isTop) {
                    // Top level, no children
                    $html .= '<div class="tw-flex tw-items-center tw-gap-2 tw-bg-slate-100 tw-px-4 tw-py-2.5 tw-rounded-xl tw-mb-2">';
                    $html .= '<input type="checkbox" name="' . $item['field'] . '" id="' . $item['field'] . '" value="1"' . $checkedAttr . ' class="tw-w-4 tw-h-4 tw-accent-orange-600 tw-flex-shrink-0">';
                    $html .= '<label for="' . $item['field'] . '" class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-cursor-pointer tw-m-0">' . htmlspecialchars($item['name']) . '</label>';
                    $html .= '</div>';
                } else {
                    // Sub-level
                    $html .= '<label class="tw-flex tw-items-center tw-gap-2 tw-bg-white tw-border tw-border-slate-100 tw-px-3 tw-py-2 tw-rounded-lg tw-cursor-pointer hover:tw-bg-orange-50 hover:tw-border-orange-200 tw-transition-all tw-group">';
                    $html .= '<input type="checkbox" name="' . $item['field'] . '" id="' . $item['field'] . '" value="1"' . $checkedAttr . ' class="sub-check tw-w-4 tw-h-4 tw-accent-orange-600 tw-flex-shrink-0">';
                    $html .= '<span class="tw-text-xs tw-font-medium tw-text-slate-600 group-hover:tw-text-orange-700">' . htmlspecialchars($item['name']) . '</span>';
                    $html .= '</label>';
                    if (!empty($item['children'])) {
                        $html .= '<div class="tw-col-span-2 tw-pl-4 tw-grid tw-grid-cols-2 tw-gap-2">';
                        $html .= renderCheckboxGroup($item['children']);
                        $html .= '</div>';
                    }
                }
            }
            return $html;
        }
    @endphp

    <form method="POST" action="{{ route('admin.quotation-fast-access') }}" id="fast-access-form">
        @csrf

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
            @foreach($categoriesByCountry as $countryId => $countryData)
            @php
                $loopColor = $colorMap[$loop->index % count($colorMap)];
                $tree = buildFlatTree($countryData['categories'], 0, $countryId, $fastExpenses[$countryId] ?? []);
                $selectedCount = count($fastExpenses[$countryId] ?? []);
            @endphp
            <div class="box !tw-p-0 tw-overflow-hidden">
                {{-- Country Header --}}
                <div class="tw-px-6 tw-py-4 tw-flex tw-justify-between tw-items-center tw-border-b tw-border-slate-100" style="border-top: 4px solid {{ $loopColor }};">
                    <div>
                        <h3 class="tw-text-lg tw-font-black tw-text-slate-900 tw-m-0">{{ $countryData['name'] }}</h3>
                        <span class="tw-text-xs tw-text-slate-400 tw-font-medium">
                            <span class="selected-count-{{ $countryId }} tw-font-bold tw-text-orange-600">{{ $selectedCount }}</span> selected
                        </span>
                    </div>
                    <div class="tw-flex tw-gap-2">
                        <button type="button" onclick="selectAll({{ $countryId }})" class="tw-text-[11px] tw-font-bold tw-text-orange-600 tw-bg-orange-50 tw-px-3 tw-py-1.5 tw-rounded-lg hover:tw-bg-orange-100 tw-transition-all">
                            Select All
                        </button>
                        <button type="button" onclick="clearAll({{ $countryId }})" class="tw-text-[11px] tw-font-bold tw-text-slate-500 tw-bg-slate-100 tw-px-3 tw-py-1.5 tw-rounded-lg hover:tw-bg-slate-200 tw-transition-all">
                            Clear
                        </button>
                    </div>
                </div>

                {{-- Category List --}}
                <div class="tw-p-5 tw-flex tw-flex-col tw-gap-1 country-section" data-country="{{ $countryId }}">
                    @if(count($countryData['categories']) > 0)
                        {!! renderCheckboxGroup($tree) !!}
                    @else
                        <div class="tw-text-center tw-py-8 tw-text-slate-400 tw-text-sm">No categories configured</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="tw-mt-8 tw-flex tw-justify-center">
            <button type="submit" class="btn orange !tw-px-12 !tw-py-4 !tw-text-base">
                <i class="fa fa-save"></i> Synchronize Fast Access
            </button>
        </div>
    </form>
</div>

<script>
function selectAll(countryId) {
    document.querySelectorAll('.country-section[data-country="' + countryId + '"] input[type="checkbox"]').forEach(function(cb) {
        cb.checked = true;
    });
    updateCount(countryId);
}
function clearAll(countryId) {
    document.querySelectorAll('.country-section[data-country="' + countryId + '"] input[type="checkbox"]').forEach(function(cb) {
        cb.checked = false;
    });
    updateCount(countryId);
}
function updateCount(countryId) {
    var count = document.querySelectorAll('.country-section[data-country="' + countryId + '"] input[type="checkbox"]:checked').length;
    var el = document.querySelector('.selected-count-' + countryId);
    if (el) el.textContent = count;
}

// Group check: checking parent checks all children
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('group-check')) {
        var parentDiv = e.target.closest('.tw-mb-3');
        if (parentDiv) {
            parentDiv.querySelectorAll('input[type="checkbox"]').forEach(function(cb) {
                cb.checked = e.target.checked;
            });
        }
    }
    // Update count for parent country
    var section = e.target.closest('.country-section');
    if (section) {
        updateCount(section.dataset.country);
    }
});
</script>
@endsection
