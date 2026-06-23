{{-- Modern Quotations Admin Navigation --}}
<div class="tw-bg-white tw-rounded-2xl tw-border tw-border-slate-100 tw-p-2 tw-mb-8 tw-flex tw-flex-wrap tw-gap-1 tw-shadow-sm">
    @php
        $quotNavItems = [
            ['route' => 'admin.quotations.index', 'label' => 'Manage Quotations', 'icon' => 'fa-pie-chart'],
            ['route' => 'admin.quotation-pricing.index', 'label' => 'Pricing', 'icon' => 'fa-money'],
            ['route' => 'admin.canned-days.index', 'label' => 'Canned Days', 'icon' => 'fa-calendar-o'],
            ['route' => 'admin.quotation-fast-access', 'label' => 'Expenses Fast Access', 'icon' => 'fa-bolt'],
            ['route' => 'admin.quotation-email-templates', 'label' => 'E-mail Templates', 'icon' => 'fa-envelope-o'],
        ];
    @endphp

    @foreach($quotNavItems as $item)
    <a href="{{ route($item['route']) }}" 
       class="tw-flex tw-items-center tw-gap-2 tw-px-5 tw-py-2.5 tw-rounded-xl tw-text-sm tw-font-bold tw-transition-all tw-no-underline
       {{ (request()->routeIs($item['route']) || (strpos($item['route'], 'canned-days') !== false && request()->is('admin/canned-days*'))) ? 'tw-bg-orange-50 tw-text-orange-600' : 'tw-text-slate-500 hover:tw-bg-slate-50 hover:tw-text-slate-900' }}">
        <i class="fa {{ $item['icon'] }} tw-text-[13px] {{ (request()->routeIs($item['route']) || (strpos($item['route'], 'canned-days') !== false && request()->is('admin/canned-days*'))) ? 'tw-text-orange-600' : 'tw-text-slate-400' }}"></i>
        {{ $item['label'] }}
    </a>
    @endforeach
</div>
