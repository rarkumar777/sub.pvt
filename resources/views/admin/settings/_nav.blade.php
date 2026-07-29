{{-- Global Settings Navigation --}}
<div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-2 tw-flex tw-flex-wrap tw-gap-1 tw-shadow-sm">
    @php
        $settingsNavItems = [
            ['route' => 'admin.settings.global', 'label' => 'Global Settings', 'icon' => 'fa-cog'],
            ['route' => 'admin.settings.countries', 'label' => 'Countries', 'icon' => 'fa-map-marker'],
            ['route' => 'admin.settings.currency', 'label' => 'Currency', 'icon' => 'fa-bank'],
            ['route' => 'admin.settings.company-profile', 'label' => 'Company Profile', 'icon' => 'fa-id-card-o'],
        ];
    @endphp

    @foreach($settingsNavItems as $item)
    <a href="{{ route($item['route']) }}" 
       class="tw-flex tw-items-center tw-gap-2 tw-px-5 tw-py-2.5 tw-rounded-xl tw-text-sm tw-font-bold tw-transition-all tw-no-underline
       {{ request()->routeIs($item['route']) ? 'tw-bg-indigo-50 tw-text-indigo-600' : 'tw-text-slate-500 hover:tw-bg-slate-50 hover:tw-text-slate-900' }}">
        <i class="fa {{ $item['icon'] }} tw-text-[13px] {{ request()->routeIs($item['route']) ? 'tw-text-indigo-600' : 'tw-text-slate-400' }}"></i>
        {{ $item['label'] }}
    </a>
    @endforeach
</div>
