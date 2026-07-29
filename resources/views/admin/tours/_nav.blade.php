{{-- Modern Tours Admin Navigation --}}
<div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-2 tw-mb-8 tw-flex tw-flex-wrap tw-gap-1 tw-shadow-sm">
    @php
        $tourNavItems = [
            ['route' => 'admin.tours-seasons', 'label' => 'Seasons', 'icon' => 'fa-calendar'],
            ['route' => 'admin.tour-types', 'label' => 'Types', 'icon' => 'fa-th-large'],
            ['route' => 'admin.tour-categories', 'label' => 'Categories', 'icon' => 'fa-folder-open'],
            ['route' => 'admin.tour-inclusions', 'label' => 'Inclusions', 'icon' => 'fa-paperclip'],
            ['route' => 'admin.tour-tec', 'label' => 'Tech Details', 'icon' => 'fa-tags'],
            ['route' => 'admin.tour-settings', 'label' => 'Settings', 'icon' => 'fa-cog'],
        ];
    @endphp

    @foreach($tourNavItems as $item)
    <a href="{{ route($item['route']) }}" 
       class="tw-flex tw-items-center tw-gap-2 tw-px-5 tw-py-2.5 tw-rounded-xl tw-text-sm tw-font-bold tw-transition-all tw-no-underline
       {{ request()->routeIs($item['route']) ? 'tw-bg-orange-50 tw-text-orange-600' : 'tw-text-slate-500 hover:tw-bg-slate-50 hover:tw-text-slate-900' }}">
        <i class="fa {{ $item['icon'] }} tw-text-[13px] {{ request()->routeIs($item['route']) ? 'tw-text-orange-600' : 'tw-text-slate-400' }}"></i>
        {{ $item['label'] }}
    </a>
    @endforeach
</div>
