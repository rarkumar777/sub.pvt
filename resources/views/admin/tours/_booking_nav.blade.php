{{-- Tours Booking Module Navigation --}}
<div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-2 tw-flex tw-flex-wrap tw-gap-1 tw-shadow-sm">
    @php
        $tbNavItems = [
            ['route' => 'admin.bookings.index', 'label' => 'Booking', 'icon' => 'fa-calendar-check-o'],
            ['route' => 'admin.quotations.index', 'label' => 'Quotation', 'icon' => 'fa-file-text-o'],
            ['route' => 'admin.guaranteed-departures.index', 'label' => 'Guaranteed Departure', 'icon' => 'fa-plane'],
            ['route' => 'admin.tours.index', 'label' => 'Tours', 'icon' => 'fa-suitcase'],
            ['route' => 'admin.tours-seasons', 'label' => 'Seasons', 'icon' => 'fa-calendar'],
            ['route' => 'admin.tour-types', 'label' => 'Types', 'icon' => 'fa-th-large'],
            ['route' => 'admin.tour-categories', 'label' => 'Categories', 'icon' => 'fa-folder-open'],
            ['route' => 'admin.tour-inclusions', 'label' => 'Inclusions', 'icon' => 'fa-paperclip'],
            ['route' => 'admin.tour-tec', 'label' => 'Technical Details', 'icon' => 'fa-tags'],
            ['route' => 'admin.tour-settings', 'label' => 'settings', 'icon' => 'fa-cog'],
        ];
    @endphp

    @foreach($tbNavItems as $item)
    @php
        $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
    @endphp
    <a href="{{ route($item['route']) }}" 
       class="tw-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2.5 tw-rounded-xl tw-text-sm tw-font-bold tw-transition-all tw-no-underline
       {{ $isActive ? 'tw-bg-indigo-50 tw-text-indigo-600' : 'tw-text-slate-500 hover:tw-bg-slate-50 hover:tw-text-slate-900' }}">
        <i class="fa {{ $item['icon'] }} tw-text-[13px] {{ $isActive ? 'tw-text-indigo-600' : 'tw-text-slate-400' }}"></i>
        {{ $item['label'] }}
    </a>
    @endforeach
</div>
