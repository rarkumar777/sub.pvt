{{-- Modern High-End Tab Navigation --}}
<div class="tw-flex tw-items-center tw-gap-1 tw-bg-white tw-p-2 tw-rounded-2xl tw-border tw-border-slate-100 tw-shadow-sm tw-mb-8 tw-overflow-x-auto">
    <a href="{{ route('admin.tours.edit', $tour->id) }}" class="tw-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-rounded-xl tw-text-sm tw-font-bold tw-no-underline tw-transition-all {{ request()->routeIs('admin.tours.edit') ? 'tw-bg-orange-50 tw-text-orange-600' : 'tw-text-slate-500 hover:tw-bg-slate-50 hover:tw-text-slate-900' }}">
        <i class="fa fa-cog tw-text-[13px] {{ !request()->routeIs('admin.tours.edit') ? 'tw-opacity-60' : '' }}"></i> General Settings
    </a>
    <a href="{{ route('admin.tours.itinerary', $tour->id) }}" class="tw-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-rounded-xl tw-text-sm tw-font-bold tw-no-underline tw-transition-all {{ request()->routeIs('admin.tours.itinerary') ? 'tw-bg-orange-50 tw-text-orange-600' : 'tw-text-slate-500 hover:tw-bg-slate-50 hover:tw-text-slate-900' }}">
        <i class="fa fa-list-ul tw-text-[13px] {{ !request()->routeIs('admin.tours.itinerary') ? 'tw-opacity-60' : '' }}"></i> Itinerary
    </a>
    <a href="{{ route('admin.tours.inclusions', $tour->id) }}" class="tw-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-rounded-xl tw-text-sm tw-font-bold tw-no-underline tw-transition-all {{ request()->routeIs('admin.tours.inclusions') ? 'tw-bg-orange-50 tw-text-orange-600' : 'tw-text-slate-500 hover:tw-bg-slate-50 hover:tw-text-slate-900' }}">
        <i class="fa fa-check-circle tw-text-[13px] {{ !request()->routeIs('admin.tours.inclusions') ? 'tw-opacity-60' : '' }}"></i> Inclusions
    </a>
    <a href="{{ route('admin.tours.pricing', $tour->id) }}" class="tw-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-rounded-xl tw-text-sm tw-font-bold tw-no-underline tw-transition-all {{ request()->routeIs('admin.tours.pricing') ? 'tw-bg-orange-50 tw-text-orange-600' : 'tw-text-slate-500 hover:tw-bg-slate-50 hover:tw-text-slate-900' }}">
        <i class="fa fa-money tw-text-[13px] {{ !request()->routeIs('admin.tours.pricing') ? 'tw-opacity-60' : '' }}"></i> Pricing
    </a>
    <a href="{{ route('admin.tours.images', $tour->id) }}" class="tw-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-rounded-xl tw-text-sm tw-font-bold tw-no-underline tw-transition-all {{ request()->routeIs('admin.tours.images') ? 'tw-bg-orange-50 tw-text-orange-600' : 'tw-text-slate-500 hover:tw-bg-slate-50 hover:tw-text-slate-900' }}">
        <i class="fa fa-image tw-text-[13px] {{ !request()->routeIs('admin.tours.images') ? 'tw-opacity-60' : '' }}"></i> Images
    </a>
    <a href="{{ route('admin.tours.departures', $tour->id) }}" class="tw-flex tw-items-center tw-gap-2 tw-px-6 tw-py-3 tw-rounded-xl tw-text-sm tw-font-bold tw-no-underline tw-transition-all {{ request()->routeIs('admin.tours.departures') ? 'tw-bg-orange-50 tw-text-orange-600' : 'tw-text-slate-500 hover:tw-bg-slate-50 hover:tw-text-slate-900' }}">
        <i class="fa fa-calendar-check-o tw-text-[13px] {{ !request()->routeIs('admin.tours.departures') ? 'tw-opacity-60' : '' }}"></i> Guaranteed Departure
    </a>
</div>
