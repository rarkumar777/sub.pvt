@extends('admin.layouts.app')
@section('title', 'Admin | Bookings')

@section('content')
<div class="tw-max-w-7xl tw-mx-auto tw-flex tw-flex-col tw-gap-8 tw-mb-12">
    
    {{-- Vibrant Header --}}
    <header class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4">
        <div class="tw-flex tw-items-center tw-gap-5">
            <div class="tw-w-14 tw-h-14 tw-rounded-xl tw-bg-gradient-to-br tw-from-orange-500 tw-to-orange-600 tw-text-white tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-orange-500/30 tw-transform tw-rotate-[-3deg]">
                <i class="fa fa-calendar-check-o tw-text-2xl tw-transform tw-rotate-[3deg]"></i>
            </div>
            <div>
                {{-- Breadcrumbs --}}
                <nav class="tw-flex tw-mb-1.5" aria-label="Breadcrumb">
                    <ol role="list" class="tw-flex tw-items-center tw-space-x-2 tw-text-xs tw-font-bold tw-text-orange-400 tw-uppercase tw-tracking-wider">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-600 tw-transition-colors tw-no-underline">Dashboard</a>
                        </li>
                        <li>
                            <div class="tw-flex tw-items-center">
                                <span class="tw-mx-1.5 tw-text-orange-200">/</span>
                                <span class="tw-text-orange-900">Bookings</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="tw-text-2xl sm:tw-text-3xl tw-font-black tw-tracking-tight tw-text-slate-900 tw-m-0">
                    Bookings Management
                </h1>
            </div>
        </div>
        <div class="tw-flex tw-items-center tw-gap-3">
            @if(auth()->user()->hasPermission('expenses'))
            <a href="{{ route('admin.expenses.index') }}" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-lg tw-bg-white tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-slate-700 tw-ring-1 tw-ring-inset tw-ring-slate-200 hover:tw-ring-slate-300 hover:tw-bg-slate-50 tw-shadow-sm tw-transition tw-no-underline">
                <i class="fa fa-pie-chart tw-text-rose-500"></i> Expenses
            </a>
            @endif
            <a href="{{ route('admin.bookings.create') }}" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-lg tw-bg-gradient-to-r tw-from-orange-500 tw-to-orange-600 tw-px-5 tw-py-2.5 tw-text-sm tw-font-bold tw-text-white tw-shadow-md tw-shadow-orange-500/20 hover:tw-from-orange-600 hover:tw-to-orange-700 focus-visible:tw-outline focus-visible:tw-outline-2 focus-visible:tw-outline-offset-2 focus-visible:tw-outline-orange-600 tw-transition tw-no-underline">
                <i class="fa fa-plus"></i> New Booking
            </a>
        </div>
    </header>

    {{-- Vibrant Main Card --}}
    <div class="tw-bg-white tw-rounded-2xl tw-shadow-sm tw-ring-1 tw-ring-slate-200">
        
        {{-- Colorful Filters Section --}}
        <div class="tw-px-6 tw-py-5 tw-border-b tw-border-slate-100 tw-bg-gradient-to-r tw-from-slate-50 tw-to-white">
            <div class="tw-flex tw-items-center tw-gap-2 tw-mb-4">
                <div class="tw-w-6 tw-h-6 tw-rounded-md tw-bg-orange-100 tw-text-orange-600 tw-flex tw-items-center tw-justify-center">
                    <i class="fa fa-filter tw-text-[10px]"></i>
                </div>
                <h2 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-m-0">Filter Records</h2>
            </div>
            
            <form method="get" action="{{ route('admin.bookings.index') }}" class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-6 tw-gap-4 tw-items-end">
                <div class="tw-flex tw-flex-col tw-gap-1.5 lg:tw-col-span-1">
                    <label class="tw-text-xs tw-font-bold tw-text-slate-600 tw-uppercase tw-tracking-wider">Country</label>
                    <select name="start_country" class="tw-block tw-w-full tw-rounded-lg tw-border-0 tw-py-2.5 tw-px-3 tw-text-slate-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-slate-300 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-orange-600 sm:tw-text-sm tw-font-medium tw-bg-white">
                        <option value="0">All Countries</option>
                        @foreach($bookingCountries as $cid => $cname)
                        <option value="{{ $cid }}" {{ request('start_country') == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="tw-flex tw-flex-col tw-gap-1.5 lg:tw-col-span-1">
                    <label class="tw-text-xs tw-font-bold tw-text-slate-600 tw-uppercase tw-tracking-wider">Status</label>
                    <select name="completed" class="tw-block tw-w-full tw-rounded-lg tw-border-0 tw-py-2.5 tw-px-3 tw-text-slate-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-slate-300 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-orange-600 sm:tw-text-sm tw-font-medium tw-bg-white">
                        <option value="h" {{ request('completed') != 's' ? 'selected' : '' }}>Ongoing</option>
                        <option value="s" {{ request('completed') == 's' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                
                <div class="tw-flex tw-flex-col tw-gap-1.5 lg:tw-col-span-1">
                    <label class="tw-text-xs tw-font-bold tw-text-slate-600 tw-uppercase tw-tracking-wider">Booking Number</label>
                    <input type="text" name="booking_number" placeholder="BK#" value="{{ request('booking_number') }}" class="tw-block tw-w-full tw-rounded-lg tw-border-0 tw-py-2.5 tw-px-3 tw-text-slate-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-slate-300 placeholder:tw-text-slate-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-orange-600 sm:tw-text-sm tw-font-medium">
                </div>
                
                <div class="tw-flex tw-flex-col tw-gap-1.5 lg:tw-col-span-1">
                    <label class="tw-text-xs tw-font-bold tw-text-slate-600 tw-uppercase tw-tracking-wider">Invoice Number</label>
                    <input type="text" name="invoice" placeholder="INV#" value="{{ request('invoice') }}" class="tw-block tw-w-full tw-rounded-lg tw-border-0 tw-py-2.5 tw-px-3 tw-text-slate-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-slate-300 placeholder:tw-text-slate-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-orange-600 sm:tw-text-sm tw-font-medium">
                </div>
                
                <div class="tw-flex tw-flex-col tw-gap-1.5 lg:tw-col-span-1">
                    <label class="tw-text-xs tw-font-bold tw-text-slate-600 tw-uppercase tw-tracking-wider">Client Info</label>
                    <input type="text" name="user_email" placeholder="Email or Guest..." value="{{ request('user_email') }}" class="tw-block tw-w-full tw-rounded-lg tw-border-0 tw-py-2.5 tw-px-3 tw-text-slate-900 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-slate-300 placeholder:tw-text-slate-400 focus:tw-ring-2 focus:tw-ring-inset focus:tw-ring-orange-600 sm:tw-text-sm tw-font-medium">
                </div>
                
                <div class="tw-flex tw-items-center tw-gap-3 lg:tw-col-span-1">
                    <button type="submit" class="tw-flex-1 tw-rounded-lg tw-bg-orange-600 tw-px-4 tw-py-2.5 tw-text-sm tw-font-bold tw-text-white tw-shadow-sm hover:tw-bg-orange-700 focus-visible:tw-outline focus-visible:tw-outline-2 focus-visible:tw-outline-offset-2 focus-visible:tw-outline-orange-600 tw-transition">
                        Filter
                    </button>
                    <a href="{{ route('admin.bookings.index') }}" class="tw-inline-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-100 tw-w-[42px] tw-h-[42px] tw-transition tw-no-underline" title="Reset Filters">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="tw-overflow-x-auto tw-pb-24 -tw-mb-24">
            <table class="tw-min-w-full tw-divide-y tw-divide-slate-200">
                <thead class="tw-bg-slate-50 tw-border-b tw-border-slate-200">
                    <tr>
                        <th scope="col" class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-bold tw-text-orange-900 tw-uppercase tw-tracking-wider">Booking ID</th>
                        <th scope="col" class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-bold tw-text-orange-900 tw-uppercase tw-tracking-wider">Trip Detail</th>
                        <th scope="col" class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-bold tw-text-orange-900 tw-uppercase tw-tracking-wider">Status</th>
                        <th scope="col" class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-bold tw-text-orange-900 tw-uppercase tw-tracking-wider">Invoice Info</th>
                        <th scope="col" class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-bold tw-text-orange-900 tw-uppercase tw-tracking-wider">Client / Guest</th>
                        <th scope="col" class="tw-px-6 tw-py-4 tw-text-left tw-text-xs tw-font-bold tw-text-orange-900 tw-uppercase tw-tracking-wider">Dates</th>
                        <th scope="col" class="tw-px-6 tw-py-4 tw-text-right tw-text-xs tw-font-bold tw-text-orange-900 tw-uppercase tw-tracking-wider tw-w-40 tw-min-w-[160px]">
                            <span class="tw-sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-100 tw-bg-white">
                    @forelse($bookings as $booking)
                    @php
                        $arrDate = !empty($booking->travel_date) && $booking->travel_date != '0000-00-00'
                            ? date('M d, Y', strtotime($booking->travel_date)) : 'N/A';
                        $depDate = !empty($booking->travel_date) && $booking->travel_date != '0000-00-00'
                            ? date('M d, Y', strtotime($booking->travel_date . ' + ' . (int)($booking->nights ?? 0) . ' days')) : 'N/A';
                        
                        // Vibrant color mappings
                        $statusMap = [
                            'pen'=>['Pending','amber'],
                            'con'=>['Confirmed','emerald'],
                            'can'=>['Cancelled','rose'],
                            'com'=>['Completed','emerald'],
                            'inp'=>['Progress','orange'],
                            'pai'=>['Paid','emerald'],
                            'unp'=>['Unpaid','rose'],
                            'ref'=>['Refunded','sky']
                        ];
                        $statusData = $statusMap[$booking->trip_status] ?? ['N/A', 'slate'];
                        $totalValue = $booking->invoice ? number_format($booking->invoice->total, 2) . ' JOD' : 'N/A';
                    @endphp
                    <tr class="hover:tw-bg-orange-50/40 tw-transition-colors">
                        {{-- Booking ID --}}
                        <td class="tw-whitespace-nowrap tw-px-6 tw-py-4">
                            <a href="{{ url('en/tours/booking/' . $booking->id . '-' . substr(md5($booking->id), 0, 5) . '/') }}" target="_blank" class="tw-text-sm tw-font-black tw-text-orange-600 hover:tw-text-orange-900 tw-no-underline">
                                #BK{{ $booking->id }}
                            </a>
                            <div class="tw-mt-1 tw-flex tw-items-center tw-gap-1.5 tw-text-xs tw-font-bold tw-text-slate-500">
                                <i class="fa fa-users tw-text-slate-400"></i> {{ $booking->adult+$booking->child+$booking->infant }} PAX
                            </div>
                        </td>
                        
                        {{-- Trip Detail --}}
                        <td class="tw-px-6 tw-py-4">
                            <div class="tw-text-sm tw-font-bold tw-text-slate-800 tw-max-w-[200px] tw-truncate" title="{{ str_replace('Booking > ', '', $booking->invoice->desc ?? 'Custom Booking') }}">
                                {{ str_replace('Booking > ', '', $booking->invoice->desc ?? 'Custom Booking') }}
                            </div>
                            @if($booking->note)
                            <div class="tw-mt-1.5 tw-flex">
                                <span class="tw-inline-flex tw-items-center tw-gap-1 tw-rounded-md tw-bg-amber-100 tw-px-2 tw-py-1 tw-text-[10px] tw-font-bold tw-text-amber-800 tw-ring-1 tw-ring-inset tw-ring-amber-500/30 tw-uppercase tw-tracking-wider">
                                    <i class="fa fa-sticky-note-o"></i> Note
                                </span>
                            </div>
                            @endif
                        </td>
                        
                        {{-- Status Badge --}}
                        <td class="tw-whitespace-nowrap tw-px-6 tw-py-4">
                            <span class="tw-inline-flex tw-items-center tw-rounded-lg tw-bg-{{ $statusData[1] }}-100 tw-px-3 tw-py-1.5 tw-text-xs tw-font-black tw-text-{{ $statusData[1] }}-700 tw-ring-1 tw-ring-inset tw-ring-{{ $statusData[1] }}-600/20 tw-uppercase tw-tracking-widest">
                                {{ $statusData[0] }}
                            </span>
                        </td>
                        
                        {{-- Invoice --}}
                        <td class="tw-whitespace-nowrap tw-px-6 tw-py-4">
                            @if($booking->invoice_id)
                                <a href="{{ url('en/invoice/' . $booking->invoice_id . '/') }}" target="_blank" class="tw-flex tw-flex-col tw-no-underline tw-group">
                                    <span class="tw-text-sm tw-font-black tw-text-slate-800 group-hover:tw-text-orange-600 tw-transition-colors">{{ $totalValue }}</span>
                                    <span class="tw-mt-0.5 tw-text-xs tw-font-bold tw-text-slate-500 tw-flex tw-items-center tw-gap-1">
                                        <i class="fa fa-file-text-o tw-text-amber-500"></i> INV-{{ $booking->invoice_id }}
                                    </span>
                                </a>
                            @else
                                <span class="tw-text-sm tw-font-black tw-text-slate-800">{{ $totalValue }}</span>
                            @endif
                        </td>
                        
                        {{-- Guest Info --}}
                        <td class="tw-px-6 tw-py-4">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <div class="tw-h-10 tw-w-10 tw-flex-shrink-0 tw-rounded-xl tw-bg-orange-100 tw-flex tw-items-center tw-justify-center tw-ring-1 tw-ring-orange-500/20">
                                    <i class="fa fa-user tw-text-orange-600 tw-text-sm"></i>
                                </div>
                                <div class="tw-flex tw-flex-col tw-max-w-[150px]">
                                    @if($booking->user_id)
                                        <a href="{{ route('admin.users.edit', $booking->user_id) }}" target="_blank" class="tw-truncate tw-text-sm tw-font-bold tw-text-orange-600 hover:tw-text-orange-900 tw-no-underline">
                                            {{ $booking->user->company ?? 'Guest Account' }}
                                        </a>
                                    @else
                                        <span class="tw-truncate tw-text-sm tw-font-bold tw-text-slate-900">{{ $booking->user->company ?? 'Guest Account' }}</span>
                                    @endif
                                    <span class="tw-truncate tw-text-xs tw-font-semibold tw-text-slate-500 tw-mt-0.5">{{ $booking->guest_name ?: 'Unknown Guest' }}</span>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Dates --}}
                        <td class="tw-whitespace-nowrap tw-px-6 tw-py-4">
                            <div class="tw-flex tw-flex-col tw-gap-2 tw-text-sm">
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <span class="tw-flex tw-items-center tw-justify-center tw-w-6 tw-h-6 tw-rounded-md tw-bg-emerald-100 tw-text-emerald-600">
                                        <i class="fa fa-plane tw-text-[10px] fa-rotate-45"></i>
                                    </span>
                                    <span class="tw-text-slate-700 tw-font-bold tw-text-xs">{{ $arrDate }}</span>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <span class="tw-flex tw-items-center tw-justify-center tw-w-6 tw-h-6 tw-rounded-md tw-bg-rose-100 tw-text-rose-600">
                                        <i class="fa fa-plane tw-text-[10px] fa-rotate-[135deg]"></i>
                                    </span>
                                    <span class="tw-text-slate-700 tw-font-bold tw-text-xs">{{ $depDate }}</span>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Actions --}}
                        <td class="tw-whitespace-nowrap tw-pl-4 tw-pr-6 tw-py-4 tw-text-right tw-w-40 tw-min-w-[160px]">
                            <div class="tw-flex tw-items-center tw-justify-end tw-gap-2">
                                @if(auth()->user()->hasPermission('tours_edit_booking'))
                                <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="tw-w-8 tw-h-8 tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-shadow-sm tw-transition-all tw-no-underline" title="Edit Booking">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                @endif
                                
                                @if(auth()->user()->hasPermission('expenses') && $booking->invoice_id)
                                <a href="{{ route('admin.expenses.index') }}?invoice={{ $booking->invoice_id }}" class="tw-w-8 tw-h-8 tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-shadow-sm tw-transition-all tw-no-underline" title="Manage Expenses">
                                    <i class="fa fa-calculator"></i>
                                </a>
                                @endif
                                
                                <div class="tw-relative dropdown-container tw-ml-1">
                                    <button type="button" class="dropdown-toggle tw-w-8 tw-h-8 tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-bg-slate-100 tw-text-slate-600 hover:tw-bg-slate-800 hover:tw-text-white tw-shadow-sm tw-transition-all focus:tw-outline-none" title="More options">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu tw-hidden tw-absolute tw-right-0 tw-top-full tw-z-50 tw-mt-2 tw-w-48 tw-origin-top-right tw-rounded-xl tw-bg-white tw-py-2 tw-shadow-xl tw-ring-1 tw-ring-slate-900/5 focus:tw-outline-none">
                                        <a href="{{ url('en/tours/booking/' . $booking->id . '-' . substr(md5($booking->id), 0, 5) . '/') }}" target="_blank" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2 tw-text-sm tw-font-bold tw-text-slate-700 hover:tw-bg-orange-50 hover:tw-text-orange-600 tw-no-underline tw-transition-colors">
                                            <i class="fa fa-external-link tw-w-4 tw-text-center tw-text-slate-400"></i> View public link
                                        </a>
                                        <a href="{{ route('admin.bookings.manifest', $booking->id) }}" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2 tw-text-sm tw-font-bold tw-text-slate-700 hover:tw-bg-emerald-50 hover:tw-text-emerald-600 tw-no-underline tw-transition-colors">
                                            <i class="fa fa-file-text-o tw-w-4 tw-text-center tw-text-slate-400"></i> Rooming list
                                        </a>
                                        <div class="tw-h-px tw-bg-slate-100 tw-my-1"></div>
                                        <a href="{{ route('admin.bookings.mark-cancelled', $booking->id) }}" onclick="return confirm('Are you sure you want to cancel this booking?');" class="tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2 tw-text-sm tw-font-bold tw-text-rose-600 hover:tw-bg-rose-50 tw-no-underline tw-transition-colors">
                                            <i class="fa fa-times-circle tw-w-4 tw-text-center tw-text-rose-400"></i> Cancel booking
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="tw-px-6 tw-py-20 tw-text-center">
                            <div class="tw-mx-auto tw-flex tw-max-w-sm tw-flex-col tw-items-center tw-justify-center">
                                <div class="tw-flex tw-h-16 tw-w-16 tw-items-center tw-justify-center tw-rounded-2xl tw-bg-orange-50 tw-text-orange-500 tw-ring-1 tw-ring-inset tw-ring-orange-100">
                                    <i class="fa fa-inbox tw-text-3xl"></i>
                                </div>
                                <h3 class="tw-mt-4 tw-text-base tw-font-black tw-text-slate-900">No bookings configured</h3>
                                <p class="tw-mt-2 tw-text-sm tw-font-medium tw-text-slate-500">Get started by creating a new booking or adjusting your filter criteria.</p>
                                <div class="tw-mt-6 tw-flex tw-gap-3">
                                    <a href="{{ route('admin.bookings.index') }}" class="tw-inline-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-white tw-px-4 tw-py-2 tw-text-sm tw-font-bold tw-text-slate-700 tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-slate-300 hover:tw-bg-slate-50 tw-no-underline">
                                        Clear filters
                                    </a>
                                    <a href="{{ route('admin.bookings.create') }}" class="tw-inline-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-orange-600 tw-px-4 tw-py-2 tw-text-sm tw-font-bold tw-text-white tw-shadow-sm hover:tw-bg-orange-500 tw-no-underline">
                                        New booking
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Vibrant Pagination Footer --}}
        @if($bookings->hasPages())
        <div class="tw-flex tw-items-center tw-justify-between tw-border-t tw-border-slate-200 tw-bg-slate-50 tw-px-6 tw-py-4">
            {{ $bookings->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .datepicker { cursor: pointer; }
    .dropdown-container.active .dropdown-menu { display: block !important; }
    
    /* Vibrant Pagination Styles Override */
    .pagination { display: flex; list-style: none; gap: 0.35rem; margin: 0; padding: 0; align-items: center; }
    .pagination li { display: block; }
    .pagination li span, .pagination li a {
        display: flex; align-items: center; justify-content: center;
        min-width: 2.25rem; height: 2.25rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 700;
        text-decoration: none; border: 1px solid #e2e8f0; background: #fff; color: #64748b;
        padding: 0 0.5rem; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .pagination li.active span {
        background: rgb(249, 115, 22); color: #fff; font-weight: 800; border-color: rgb(249, 115, 22);
        box-shadow: 0 4px 6px -1px rgba(249, 115, 22, 0.2), 0 2px 4px -1px rgba(249, 115, 22, 0.1);
    }
    .pagination li a:hover { background: #fff7ed; border-color: #fdba74; color: rgb(249, 115, 22); }
    .pagination li.disabled span { opacity: 0.5; box-shadow: none; }
</style>

@push('footer')
<script>
    $(document).ready(function() {
        $(document).on('click', '.dropdown-toggle', function(e) {
            e.stopPropagation();
            const $cont = $(this).closest('.dropdown-container');
            $('.dropdown-container').not($cont).removeClass('active');
            $cont.toggleClass('active');
        });
        $(document).on('click', function() { 
            $('.dropdown-container').removeClass('active'); 
        });
    });
</script>
@endpush
@endsection
