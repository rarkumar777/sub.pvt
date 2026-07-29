@extends('admin.layouts.app')
@section('title', 'Admin | Edit Booking')

@section('content')
<div class="tw-bg-slate-50 tw-min-h-screen tw--mx-8 tw--mt-8 tw-px-6 tw-pb-20">
    {{-- Top Breadcrumb & Header --}}
    <div class="tw-pt-6 tw-mb-6">
        <nav class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest tw-text-slate-400 tw-mb-3">
            <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-600 tw-transition-colors">Dashboard</a>
            <i class="fa fa-chevron-right tw-text-[8px]"></i>
            <a href="{{ route('admin.bookings.index') }}" class="hover:tw-text-orange-600 tw-transition-colors">Bookings</a>
            <i class="fa fa-chevron-right tw-text-[8px]"></i>
            <span class="tw-text-slate-900">Edit Reservation</span>
        </nav>
        
        <div class="tw-flex tw-flex-col lg:tw-flex-row tw-justify-between tw-items-start lg:tw-items-center tw-gap-4">
            <div class="tw-flex tw-items-center tw-gap-4">
                <div class="tw-w-10 tw-h-10 tw-bg-orange-50 tw-rounded-lg tw-flex tw-items-center tw-justify-center tw-text-orange-600">
                    <i class="fa fa-edit tw-text-lg"></i>
                </div>
                <div>
                    <h1 class="tw-text-xl tw-font-bold tw-text-slate-800 tw-m-0">
                        {!! $booking->invoice ? $booking->invoice->desc : 'Booking #'.$booking->id !!}
                    </h1>
                    <p class="tw-text-xs tw-text-slate-500 tw-mt-1 tw-flex tw-items-center tw-gap-2">
                        <span class="tw-px-2 tw-py-0.5 tw-bg-slate-200 tw-text-slate-700 tw-rounded tw-text-[10px] tw-font-bold">#{{ $booking->id }}</span>
                        Manage core logistics, pricing and manifest details.
                    </p>
                </div>
            </div>
            <div class="tw-flex tw-items-center tw-gap-2">
                @if($booking->invoice_id)
                <a href="{{ route('admin.expenses.index') }}?invoice={{ $booking->invoice_id }}" class="btn shadow-sm !tw-bg-orange-600 !tw-text-white hover:!tw-bg-orange-700 !tw-px-4 !tw-py-1.5 !tw-text-xs !tw-h-auto !tw-flex !tw-items-center tw-font-bold">
                    <i class="fa fa-calculator tw-mr-1.5"></i> Reservations & Costs
                </a>
                @endif
                 <a href="{{ route('admin.bookings.mark-cancelled', $booking->id) }}" class="btn shadow-sm !tw-bg-white !tw-text-rose-600 tw-border-rose-100 hover:!tw-bg-rose-50 !tw-px-4 !tw-py-1.5 !tw-text-xs !tw-h-auto !tw-flex !tw-items-center tw-font-bold" onclick="return confirm('Confirm cancellation?');">
                    <i class="fa fa-times-circle tw-mr-1.5"></i> Cancel Reservation
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border tw-border-emerald-100 tw-text-emerald-600 tw-px-4 tw-py-3 tw-rounded-lg tw-mb-6 tw-flex tw-items-center tw-gap-2 tw-shadow-sm">
        <i class="fa fa-check-circle tw-text-base"></i>
        <span class="tw-text-xs tw-font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}" id="editBookingForm">
        @csrf
        @method('PUT')

        <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-12 tw-gap-5 tw-mb-8">
            <!-- Left: Information Column -->
            <div class="lg:tw-col-span-12 xl:tw-col-span-7 tw-space-y-5">
                {{-- General Information Card --}}
                <div class="tw-bg-white tw-rounded-xl tw-border tw-border-slate-200 tw-p-5 tw-shadow-sm">
                    <h3 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-mb-5 tw-flex tw-items-center tw-gap-2">
                        <i class="fa fa-user-circle tw-text-orange-500"></i> General Information
                    </h3>
                    
                    <div id="check_user_msg" class="tw-mb-4"></div>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <div class="md:tw-col-span-2">
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">Agent / User Email</label>
                            <div class="tw-relative">
                                <i class="fa fa-envelope tw-absolute tw-left-3 tw-top-1/2 tw--translate-y-1/2 tw-text-slate-400"></i>
                                <input type="email" name="user_email" id="user" value="{{ $booking->user ? $booking->user->email : '' }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-pl-9 tw-pr-3 tw-text-sm tw-text-slate-700 focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500/20 focus:tw-border-orange-500 tw-outline-none tw-transition-colors" placeholder="agent@agency.jo">
                            </div>
                        </div>

                        <div>
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">First Name</label>
                            <input type="text" id="first_name" value="{{ $booking->user ? $booking->user->first_name : '' }}" disabled class="tw-w-full tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-px-3 tw-text-sm tw-text-slate-500 tw-cursor-not-allowed">
                        </div>

                        <div>
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">Last Name</label>
                            <input type="text" id="last_name" value="{{ $booking->user ? $booking->user->last_name : '' }}" disabled class="tw-w-full tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-px-3 tw-text-sm tw-text-slate-500 tw-cursor-not-allowed">
                        </div>

                        <div class="md:tw-col-span-2">
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">Organization / Agency</label>
                            <input type="text" id="company" value="{{ $booking->user ? $booking->user->company : '' }}" disabled class="tw-w-full tw-bg-slate-100 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-px-3 tw-text-sm tw-text-slate-500 tw-cursor-not-allowed">
                        </div>

                        <div class="md:tw-col-span-2 tw-pt-4 tw-mt-1 tw-border-t tw-border-slate-100">
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">Internal Reference / Header</label>
                            <input type="text" name="title" value="{{ $booking->invoice ? html_entity_decode($booking->invoice->desc) : '' }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-px-3 tw-text-sm tw-font-medium tw-text-slate-800 focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500/20 focus:tw-border-orange-500 tw-outline-none tw-transition-colors" placeholder="Trip Header...">
                        </div>

                        <div class="md:tw-col-span-2">
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">Guest / Group Name</label>
                            <input type="text" name="guest_name" value="{{ $booking->guest_name }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-px-3 tw-text-sm tw-font-medium tw-text-slate-800 focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500/20 focus:tw-border-orange-500 tw-outline-none tw-transition-colors" placeholder="Guest Name">
                        </div>
                    </div>
                </div>

                {{-- Notes Card --}}
                <div class="tw-bg-white tw-rounded-xl tw-border tw-border-slate-200 tw-p-5 tw-shadow-sm">
                    <h3 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-mb-3 tw-flex tw-items-center tw-gap-2">
                        <i class="fa fa-sticky-note tw-text-orange-500"></i> Operation Notes
                    </h3>
                    <textarea name="notes" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-p-3 tw-text-sm tw-font-medium tw-text-slate-700 tw-min-h-[100px] focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500/20 focus:tw-border-orange-500 tw-outline-none tw-transition-colors" placeholder="Internal documentation here...">{{ $booking->note }}</textarea>
                </div>
            </div>

            <!-- Right: Logistics Column -->
            <div class="lg:tw-col-span-12 xl:tw-col-span-5 tw-space-y-5">
                {{-- Logistics Card --}}
                <div class="tw-bg-white tw-rounded-xl tw-border tw-border-slate-200 tw-p-5 tw-shadow-sm">
                    <h3 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-mb-5 tw-flex tw-items-center tw-gap-2">
                        <i class="fa fa-clock-o tw-text-amber-500"></i> Trip Logistics
                    </h3>

                    <div class="tw-space-y-4">
                         <div>
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">Travel Date</label>
                            <div class="tw-relative">
                                <i class="fa fa-calendar tw-absolute tw-left-3 tw-top-1/2 tw--translate-y-1/2 tw-text-slate-400"></i>
                                <input type="text" name="date" id="date" class="datepicker tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-pl-9 tw-pr-3 tw-text-sm tw-font-bold tw-text-slate-800 focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500/20 focus:tw-border-orange-500 tw-outline-none tw-transition-colors" value="{{ $booking->travel_date }}">
                            </div>
                        </div>

                        <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                            <div>
                                <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">Days</label>
                                <input type="number" name="days" id="days" value="{{ $booking->days }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-px-3 tw-text-sm tw-font-medium tw-text-slate-800 focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500/20 focus:tw-border-orange-500 tw-outline-none tw-transition-colors">
                            </div>
                            <div>
                                <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">Nights</label>
                                <input type="number" name="nights" id="nights" value="{{ $booking->nights }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-px-3 tw-text-sm tw-font-medium tw-text-slate-800 focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500/20 focus:tw-border-orange-500 tw-outline-none tw-transition-colors">
                            </div>
                        </div>

                        <div class="tw-bg-slate-50 tw-p-3 tw-rounded-lg tw-border tw-border-slate-100 tw-flex tw-gap-3">
                            <div class="tw-flex-1 tw-text-center">
                                <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-mb-1">Adult</label>
                                <input type="number" name="adult" id="adult" value="{{ $booking->adult }}" class="tw-w-full tw-bg-white tw-border tw-border-slate-200 tw-rounded tw-py-1 tw-text-center tw-text-sm tw-font-bold tw-text-slate-800 tw-shadow-sm outline-none">
                            </div>
                            <div class="tw-flex-1 tw-text-center">
                                <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-mb-1">Child</label>
                                <input type="number" name="child" id="child" value="{{ $booking->child }}" class="tw-w-full tw-bg-white tw-border tw-border-slate-200 tw-rounded tw-py-1 tw-text-center tw-text-sm tw-font-bold tw-text-slate-800 tw-shadow-sm outline-none">
                            </div>
                            <div class="tw-flex-1 tw-text-center">
                                <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-mb-1">Infant</label>
                                <input type="number" name="infant" id="infant" value="{{ $booking->infant }}" class="tw-w-full tw-bg-white tw-border tw-border-slate-200 tw-rounded tw-py-1 tw-text-center tw-text-sm tw-font-bold tw-text-slate-800 tw-shadow-sm outline-none">
                            </div>
                        </div>

                        <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                            <div>
                                <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">Arrival Start</label>
                                <select name="start_country" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-px-3 tw-text-xs tw-font-medium tw-text-slate-700 tw-outline-none focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500/20 focus:tw-border-orange-500">
                                    @foreach($countries as $cid => $cname)
                                    <option value="{{ $cid }}" {{ $booking->start_country == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-widest tw-mb-1.5">Hotel Grade</label>
                                <select name="hotel_grade" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-2 tw-px-3 tw-text-xs tw-font-medium tw-text-slate-700 tw-outline-none focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500/20 focus:tw-border-orange-500">
                                    @foreach($accommodations as $val => $label)
                                    <option value="{{ $val }}" {{ $booking->hotel_grade == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Room Allocation Hub --}}
                <div class="tw-bg-white tw-rounded-xl tw-border tw-border-slate-200 tw-p-5 tw-shadow-sm">
                    <h3 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-mb-4 tw-flex tw-items-center tw-gap-2">
                        <i class="fa fa-bed tw-text-orange-500"></i> Room Allocation
                    </h3>
                    <div class="tw-grid tw-grid-cols-5 tw-gap-2">
                        @foreach(['room_single' => 'Sgl', 'rooms_double' => 'Dbl', 'rooms_twin' => 'Twn', 'rooms_triple' => 'Trp', 'rooms_quad' => 'Qad'] as $f => $l)
                        <div class="tw-text-center">
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-mb-1">{{ $l }}</label>
                            <input type="number" name="{{ str_replace(['room_','rooms_'], '', $f) }}" value="{{ $booking->$f }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-1.5 tw-text-center tw-text-sm tw-font-bold tw-text-slate-800 focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500/20 focus:tw-border-orange-500 tw-outline-none">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Tabs -->
        <div class="tw-bg-white tw-rounded-xl tw-border tw-border-slate-200 tw-shadow-sm tw-overflow-hidden tw-mb-16">
            <div class="tw-flex tw-bg-slate-50 tw-border-b tw-border-slate-200">
                <button type="button" class="tab-btn active tw-px-6 tw-py-3 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest tw-transition-all tw-flex tw-items-center tw-gap-2" data-tab="#itinerary-tab">
                    <i class="fa fa-map-signs"></i> Itinerary
                </button>
                <button type="button" class="tab-btn tw-px-6 tw-py-3 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest tw-transition-all tw-flex tw-items-center tw-gap-2" data-tab="#invoice-tab">
                    <i class="fa fa-file-invoice-dollar"></i> Financials
                </button>
                <button type="button" class="tab-btn tw-px-6 tw-py-3 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest tw-transition-all tw-flex tw-items-center tw-gap-2" data-tab="#travelers-tab">
                    <i class="fa fa-users"></i> Travelers
                </button>
            </div>

            <div class="tw-p-5">
                {{-- Dynamic Itinerary Content --}}
                <div class="tab-content animated fadeIn" id="itinerary-tab">
                    <div class="tw-mb-4">
                        <h4 class="tw-text-base tw-font-bold tw-text-slate-800">Booking Itinerary</h4>
                    </div>
                    <div class="tw-border tw-border-slate-200 tw-rounded-lg tw-overflow-hidden">
                        <textarea name="desc" class="tinymce" id="desc">{!! $itinerary !!}</textarea>
                    </div>
                </div>

                {{-- Financial Ledger Content --}}
                <div class="tab-content tw-hidden animated fadeIn" id="invoice-tab">
                     <div class="tw-flex tw-justify-between tw-items-center tw-mb-5">
                        <h4 class="tw-text-base tw-font-bold tw-text-slate-800">Financial Ledger</h4>
                        <div class="tw-flex tw-gap-2">
                            @if($booking->invoice_id)
                            <a href="{{ route('admin.invoices.transactions', $booking->invoice_id) }}" class="btn !tw-bg-white !tw-text-orange-600 tw-border tw-border-orange-100 !tw-text-[11px] !tw-font-bold !tw-px-3 !tw-py-1 ajax-load" data-target="#ajax"><i class="fa fa-history tw-mr-1"></i> Transactions</a>
                            @if(auth()->user()->hasPermission('expenses'))
                            <a href="{{ route('admin.expenses.index') }}?invoice={{ $booking->invoice_id }}" class="btn !tw-bg-white !tw-text-amber-600 tw-border tw-border-amber-100 !tw-text-[11px] !tw-font-bold !tw-px-3 !tw-py-1"><i class="fa fa-bar-chart tw-mr-1"></i> Expenses</a>
                            @endif
                            @endif
                        </div>
                    </div>

                    <div class="tw-grid tw-grid-cols-4 tw-gap-4 tw-mb-5">
                        <div>
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-mb-1">Invoice Date</label>
                            <input type="text" name="invoice_date" class="datepicker tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-1.5 tw-px-3 tw-text-xs focus:tw-bg-white focus:tw-border-orange-500 tw-outline-none" value="{{ $booking->invoice ? $booking->invoice->date : '' }}">
                        </div>
                        <div>
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-mb-1">Due Date</label>
                            <input type="text" name="due_to_date" class="datepicker tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-1.5 tw-px-3 tw-text-xs focus:tw-bg-white focus:tw-border-orange-500 tw-outline-none" value="{{ $booking->invoice ? $booking->invoice->due_to_date : '' }}">
                        </div>
                        <div>
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-mb-1">Part Pmt Req.</label>
                            <input type="number" name="partly_payment" value="{{ $booking->invoice ? $booking->invoice->partly_payment : 0 }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-1.5 tw-px-3 tw-text-xs focus:tw-bg-white focus:tw-border-orange-500 tw-outline-none">
                        </div>
                        <div>
                            <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-mb-1">Tax (%)</label>
                            <input type="text" name="tax" value="{{ $booking->invoice ? $booking->invoice->tax : 0 }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-py-1.5 tw-px-3 tw-text-xs focus:tw-bg-white focus:tw-border-orange-500 tw-outline-none">
                        </div>
                    </div>

                    <div class="tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-lg tw-p-4 tw-mb-6">
                         <div class="tw-flex tw-justify-between tw-items-center tw-mb-3">
                            <span class="tw-text-xs tw-font-bold tw-text-slate-700 tw-uppercase">Line Items</span>
                            <button type="button" class="tw-text-xs tw-font-bold tw-text-orange-600 hover:tw-text-orange-800" id="add_new_item">
                                <i class="fa fa-plus-circle tw-mr-1"></i> Add Service
                            </button>
                        </div>
                        
                        <div id="items-ledger">
                            <div class="tw-grid tw-grid-cols-12 tw-gap-3 tw-px-2 tw-mb-2 tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase text-center">
                                <div class="tw-col-span-1">Del</div>
                                <div class="tw-col-span-5 tw-text-left">Service</div>
                                <div class="tw-col-span-2">Qty</div>
                                <div class="tw-col-span-2">Price</div>
                                <div class="tw-col-span-2 tw-text-right">Total</div>
                            </div>
                            <div id="items" class="tw-space-y-2">
                                @php $itemsTotal = 0; $c = 0; @endphp
                                @foreach($invoiceItems as $item)
                                @php $c++; $lineTotal = $item['qty'] * $item['price']; $itemsTotal += $lineTotal; @endphp
                                <div class="tw-grid tw-grid-cols-12 tw-gap-3 tw-items-center tw-bg-white tw-p-2 tw-rounded tw-border tw-border-slate-200 item-row" id="item_c{{ $c }}">
                                    <div class="tw-col-span-1 tw-text-center">
                                        <button type="button" onclick="$('#item_c{{ $c }}').remove(); recalcTotals();" class="tw-text-rose-400 hover:tw-text-rose-600 tw-transition-colors">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="tw-col-span-5">
                                        <input type="text" name="item_{{ $c }}" value="{{ $item['name'] }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-p-1.5 tw-text-xs tw-rounded focus:tw-bg-white focus:tw-border-orange-500 tw-outline-none">
                                    </div>
                                    <div class="tw-col-span-2">
                                        <input type="text" name="item_qty_{{ $c }}" value="{{ $item['qty'] }}" class="item-qty tw-w-full tw-text-center tw-bg-slate-50 tw-border tw-border-slate-200 tw-p-1.5 tw-text-xs tw-rounded focus:tw-bg-white focus:tw-border-orange-500 tw-outline-none" onkeyup="recalcTotals()">
                                    </div>
                                    <div class="tw-col-span-2">
                                        <input type="text" name="item_price_{{ $c }}" value="{{ $item['price'] }}" class="item-price tw-w-full tw-text-center tw-bg-slate-50 tw-border tw-border-slate-200 tw-p-1.5 tw-text-xs tw-rounded focus:tw-bg-white focus:tw-border-orange-500 tw-outline-none" onkeyup="recalcTotals()">
                                    </div>
                                    <div class="tw-col-span-2 tw-text-right tw-text-xs tw-font-bold tw-text-slate-800 tw-pr-2">
                                        <span class="item-line-total">{{ number_format($lineTotal, 2) }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="current_count" id="current_count" value="{{ $c }}">
                        </div>
                    </div>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                        <div class="tw-bg-white tw-p-4 tw-rounded-lg tw-border tw-border-slate-200">
                             <h5 class="tw-text-xs tw-font-bold tw-text-orange-600 tw-uppercase tw-mb-3 tw-flex tw-items-center tw-gap-2">
                                <i class="fa fa-percent"></i> Discount
                            </h5>
                            <div class="tw-grid tw-grid-cols-3 tw-gap-3">
                                @php
                                    $discountType = ''; $discountAmount = 0;
                                    if ($booking->invoice) {
                                        $disc = $booking->invoice->discount;
                                        if (strpos($disc, '%') !== false) { $discountType = '%'; $discountAmount = str_replace('%', '', $disc); } else { $discountAmount = $disc; }
                                    }
                                @endphp
                                <div>
                                    <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 uppercase tw-mb-1">Type</label>
                                    <select name="discount_type" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded tw-py-1.5 tw-px-2 tw-text-[11px] focus:tw-border-orange-500 tw-outline-none">
                                        <option value="" {{ $discountType == '' ? 'selected' : '' }}>Flat</option>
                                        <option value="%" {{ $discountType == '%' ? 'selected' : '' }}>%</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 uppercase tw-mb-1">Amount</label>
                                    <input type="text" name="discount_amount" value="{{ $discountAmount }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded tw-py-1.5 tw-px-2 tw-text-[11px] focus:tw-border-orange-500 tw-outline-none" onkeyup="recalcTotals()">
                                </div>
                                <div>
                                    <label class="tw-block tw-text-[10px] tw-font-bold tw-text-slate-500 uppercase tw-mb-1">Reason</label>
                                    <input type="text" name="discount_description" value="{{ $booking->invoice ? $booking->invoice->discount_description : '' }}" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded tw-py-1.5 tw-px-2 tw-text-[11px] focus:tw-border-orange-500 tw-outline-none">
                                </div>
                            </div>
                        </div>
                        <div class="tw-bg-slate-800 tw-text-white tw-p-5 tw-rounded-lg tw-shadow-sm tw-space-y-2">
                            @php
                                $discount = 0;
                                if ($booking->invoice) {
                                    $disc = $booking->invoice->discount;
                                    if (strpos($disc, '%') !== false) { $discount = (intval($disc) / 100) * $itemsTotal; } else { $discount = floatval($disc); }
                                }
                                $taxPercent = $booking->invoice ? $booking->invoice->tax : 0;
                                $taxAmount = ($taxPercent / 100) * ($itemsTotal - $discount);
                                $grandTotal = ($itemsTotal - $discount) + $taxAmount;
                            @endphp
                            <div class="tw-flex tw-justify-between tw-text-xs tw-text-slate-300">
                                <span>Subtotal</span>
                                <span><span id="display_total">{{ number_format($itemsTotal, 2) }}</span> JOD</span>
                            </div>
                            <div class="tw-flex tw-justify-between tw-text-xs tw-text-rose-400">
                                <span>Discount</span>
                                <span>- <span id="display_discount">{{ number_format($discount, 2) }}</span> JOD</span>
                            </div>
                            <div class="tw-flex tw-justify-between tw-text-xs tw-text-slate-300">
                                <span>Tax ({{ $taxPercent }}%)</span>
                                <span><span id="display_tax">{{ number_format($taxAmount, 2) }}</span> JOD</span>
                            </div>
                            <div class="tw-pt-3 tw-mt-2 tw-border-t tw-border-slate-700 tw-flex tw-justify-between tw-items-center">
                                <span class="tw-text-xs tw-font-bold tw-uppercase tw-text-slate-300">Grand Total</span>
                                <span class="tw-text-xl tw-font-bold tw-text-white"><span id="display_grand_total">{{ number_format($grandTotal, 2) }}</span> <span class="tw-text-xs tw-font-normal text-slate-400">JOD</span></span>
                            </div>
                            @if($booking->invoice && $booking->invoice->total_paid > 0)
                            <div class="tw-bg-slate-700/50 tw-p-3 tw-rounded tw-mt-4">
                                 <div class="tw-flex tw-justify-between tw-text-[11px] tw-text-emerald-400 tw-mb-1">
                                    <span>Paid Funds</span>
                                    <span>{{ number_format($booking->invoice->total_paid, 2) }} JOD</span>
                                </div>
                                @if($booking->invoice->total_paid < $grandTotal)
                                <div class="tw-flex tw-justify-between tw-text-[11px] tw-text-amber-400">
                                    <span>Balance Due</span>
                                    <span>{{ number_format($grandTotal - $booking->invoice->total_paid, 2) }} JOD</span>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Passengers Content --}}
                <div class="tab-content tw-hidden animated fadeIn" id="travelers-tab">
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                        <h4 class="tw-text-base tw-font-bold tw-text-slate-800">Travelers Configuration</h4>
                         <span class="tw-bg-slate-100 tw-text-slate-600 tw-px-3 tw-py-1 tw-rounded tw-text-[10px] tw-font-bold tw-uppercase">
                            PAX: {{ ($booking->adult ?? 0) + ($booking->child ?? 0) }}
                         </span>
                    </div>
                    
                    @php
                        $singleCount = $booking->room_single ?? 0; $doubleCount = $booking->rooms_double ?? 0; $twinCount = $booking->rooms_twin ?? 0; $tripleCount = $booking->rooms_triple ?? 0; $quadCount = $booking->rooms_quad ?? 0;
                        $nightsVal = $booking->nights ?? 0; $hotelGrade = $booking->hotel_grade ?? 0; $totalPaxCount = ($booking->adult ?? 0) + ($booking->child ?? 0);
                        if ($nightsVal == 0 || $hotelGrade == 0) { $singleCount = $totalPaxCount; $twinCount = 0; $doubleCount = 0; $tripleCount = 0; $quadCount = 0; }
                        $travelerMap = []; if ($booking->travelers) { foreach ($booking->travelers as $t) { $key = ($t->room_id ?: '0') . '_' . ($t->traveler_id ?: '0'); $travelerMap[$key] = $t; } }
                        $roomCounter = 0;
                    @endphp

                    <div class="tw-overflow-hidden tw-rounded-lg tw-border tw-border-slate-200">
                        <div class="tw-overflow-x-auto">
                            <table class="tw-w-full tw-text-left">
                                <thead>
                                    <tr class="tw-bg-slate-50 tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider tw-border-b tw-border-slate-200">
                                        <th class="tw-px-4 tw-py-2">Name</th>
                                        <th class="tw-px-4 tw-py-2">Passport</th>
                                        <th class="tw-px-4 tw-py-2">Issue / Expiry</th>
                                        <th class="tw-px-4 tw-py-2">DOB</th>
                                        <th class="tw-px-4 tw-py-2">Nationality</th>
                                    </tr>
                                </thead>
                                <tbody class="tw-divide-y tw-divide-slate-100">
                                    @foreach([['count'=>$singleCount, 'label'=>'Single Room', 'slots'=>1, 'prefix'=>'s'], ['count'=>$doubleCount, 'label'=>'Double Room', 'slots'=>2, 'prefix'=>'d'], ['count'=>$twinCount, 'label'=>'Twin Room', 'slots'=>2, 'prefix'=>'t'], ['count'=>$tripleCount,'label'=>'Triple Room','slots'=>3, 'prefix'=>'tr'], ['count'=>$quadCount,'label'=>'Quad Room','slots'=>4, 'prefix'=>'q']] as $type)
                                        @for($i = 1; $i <= $type['count']; $i++)
                                            @php $roomCounter++; $roomId = $type['prefix'] . '_' . $i; @endphp
                                            <tr class="tw-bg-orange-50/50">
                                                <td colspan="5" class="tw-px-4 tw-py-1.5 tw-text-[11px] tw-font-bold tw-text-orange-600 tw-border-y tw-border-orange-100">
                                                    <i class="fa fa-bed tw-mr-1.5"></i> Room #{{ $roomCounter }} - {{ $type['label'] }}
                                                </td>
                                            </tr>
                                            @for($slot = 1; $slot <= $type['slots']; $slot++)
                                                @php $t = $travelerMap[$roomId . '_' . $slot] ?? null; $fieldSuffix = $roomId . '_' . $slot; @endphp
                                                <tr class="hover:tw-bg-slate-50 transition-colors">
                                                    <td class="tw-px-4 tw-py-2">
                                                        <input type="text" name="traveler_name_{{ $fieldSuffix }}" value="{{ $t->name ?? '' }}" class="tw-w-full tw-bg-transparent tw-border tw-border-transparent hover:tw-border-slate-200 focus:tw-bg-white focus:tw-border-orange-500 tw-rounded tw-px-2 tw-py-1 tw-text-[11px] tw-font-bold tw-text-slate-800 tw-outline-none" placeholder="Name">
                                                    </td>
                                                    <td class="tw-px-4 tw-py-2">
                                                        <div class="tw-relative">
                                                            <input type="text" name="traveler_passport_number_{{ $fieldSuffix }}" value="{{ $t->passport_number ?? '' }}" class="tw-w-full tw-bg-transparent tw-border tw-border-transparent hover:tw-border-slate-200 focus:tw-bg-white focus:tw-border-orange-500 tw-rounded tw-px-2 tw-py-1 tw-text-[11px] tw-text-slate-700 tw-outline-none" placeholder="PPT Number">
                                                        </div>
                                                    </td>
                                                    <td class="tw-px-4 tw-py-2">
                                                        <div class="tw-flex tw-gap-1">
                                                            <input type="text" name="traveler_passport_issue_{{ $fieldSuffix }}" value="{{ $t->passport_issue ?? '' }}" class="datepicker tw-w-1/2 tw-bg-transparent tw-border tw-border-transparent hover:tw-border-slate-200 focus:tw-bg-white focus:tw-border-orange-500 tw-rounded tw-px-2 tw-py-1 tw-text-[10px] tw-text-slate-600 tw-outline-none" placeholder="Issue">
                                                            <input type="text" name="traveler_passport_expire_{{ $fieldSuffix }}" value="{{ $t->passport_expire ?? '' }}" class="datepicker tw-w-1/2 tw-bg-transparent tw-border tw-border-transparent hover:tw-border-slate-200 focus:tw-bg-white focus:tw-border-orange-500 tw-rounded tw-px-2 tw-py-1 tw-text-[10px] tw-text-slate-600 tw-outline-none" placeholder="Expiry">
                                                        </div>
                                                    </td>
                                                    <td class="tw-px-4 tw-py-2">
                                                        <input type="text" name="traveler_birth_date_{{ $fieldSuffix }}" value="{{ $t->birth_date ?? '' }}" class="datepicker tw-w-full tw-bg-transparent tw-border tw-border-transparent hover:tw-border-slate-200 focus:tw-bg-white focus:tw-border-orange-500 tw-rounded tw-px-2 tw-py-1 tw-text-[11px] tw-text-slate-700 tw-outline-none" placeholder="DOB">
                                                    </td>
                                                    <td class="tw-px-4 tw-py-2">
                                                        <input type="text" name="traveler_nationality_{{ $fieldSuffix }}" value="{{ $t->nationality ?? '' }}" class="tw-w-full tw-bg-transparent tw-border tw-border-transparent hover:tw-border-slate-200 focus:tw-bg-white focus:tw-border-orange-500 tw-rounded tw-px-2 tw-py-1 tw-text-[11px] tw-text-slate-600 tw-uppercase tw-outline-none" placeholder="Nat.">
                                                    </td>
                                                </tr>
                                            @endfor
                                        @endfor
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Master Action Bar -->
        <div class="tw-fixed tw-bottom-0 tw-left-0 tw-w-full tw-bg-white tw-border-t tw-border-slate-200 tw-shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] tw-py-3 tw-px-6 tw-flex tw-justify-between tw-items-center tw-z-50 ml-[230px]" style="width: calc(100% - 230px);">
            <div class="tw-flex tw-items-center tw-gap-2">
                <div class="tw-w-2 tw-h-2 tw-bg-emerald-500 tw-rounded-full tw-animate-pulse"></div>
                <span class="tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase">Editing Mode</span>
            </div>
            
            <div class="tw-flex tw-gap-3">
                <a href="{{ route('admin.bookings.index') }}" class="btn !tw-bg-white !tw-text-slate-600 tw-border tw-border-slate-300 hover:!tw-bg-slate-50 tw-font-bold tw-text-xs !tw-px-6 !tw-py-2 !tw-flex !tw-items-center !tw-h-auto tw-rounded-lg">
                    Cancel
                </a>
                <button type="submit" class="btn !tw-bg-orange-600 !tw-text-white hover:!tw-bg-orange-700 tw-font-bold tw-text-xs !tw-px-6 !tw-py-2 !tw-flex !tw-items-center !tw-h-auto tw-rounded-lg shadow-sm">
                    <i class="fa fa-save tw-mr-2"></i> Save Changes
                </button>
            </div>
        </div>
    </form>

    {{-- Financial Audit Overlay --}}
    <div id="ajax" class="tw-fixed tw-inset-0 tw-z-[100] tw-hidden tw-bg-slate-900/50 tw-backdrop-blur-sm tw-flex tw-items-center tw-justify-center">
        <div class="tw-bg-white tw-rounded-xl tw-p-6 tw-max-w-4xl tw-w-full tw-shadow-2xl tw-relative tw-mx-4 tw-border tw-border-slate-100">
            <button type="button" onclick="$('#ajax').fadeOut()" class="tw-absolute tw-top-4 tw-right-4 tw-w-8 tw-h-8 tw-bg-slate-100 tw-text-slate-500 tw-rounded hover:tw-text-rose-500 tw-flex tw-items-center tw-justify-center">&times;</button>
            <div id="ajax_content" class="tw-max-h-[70vh] tw-overflow-y-auto tw-pr-2 scrollbar-thin"></div>
        </div>
    </div>
</div>

<style>
    .datepicker { cursor: pointer; }
    .tab-btn { color: #64748b; border-bottom: 2px solid transparent; }
    .tab-btn.active { color: #ea580c; border-bottom-color: #ea580c; background: transparent !important; }
    .tab-btn:not(.active):hover { color: #334155; }
</style>

<script src="{{ asset('assets/admin/tinymce/tinymce.min.js') }}"></script>
@endsection

@push('footer')
<script>
function recalcTotals() {
    var total = 0;
    $('#items .item-row').each(function() {
        var qty = parseFloat($(this).find('.item-qty').val()) || 0;
        var price = parseFloat($(this).find('.item-price').val()) || 0;
        var lineTotal = qty * price;
        $(this).find('.item-line-total').text(lineTotal.toFixed(2));
        total += lineTotal;
    });
    var discountAmount = parseFloat($('input[name="discount_amount"]').val()) || 0;
    var discountType = $('select[name="discount_type"]').val();
    var discount = (discountType === '%') ? (discountAmount / 100) * total : discountAmount;
    var taxPercent = parseFloat($('input[name="tax"]').val()) || 0;
    var taxAmount = (taxPercent / 100) * (total - discount);
    var grandTotal = (total - discount) + taxAmount;
    
    $('#display_total').text(total.toLocaleString(undefined, {minimumFractionDigits: 2}));
    $('#display_discount').text(discount.toLocaleString(undefined, {minimumFractionDigits: 2}));
    $('#display_tax').text(taxAmount.toLocaleString(undefined, {minimumFractionDigits: 2}));
    $('#display_grand_total').text(grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
}

$(function() {
    // Init TinyMCE
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: "textarea.tinymce",
            plugins: [
                "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern"
            ],
            toolbar1: "bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | forecolor backcolor | table | code fullscreen",
            menubar: false,
            toolbar_items_size: 'small',
            entity_encoding: 'raw',
            extended_valid_elements: 'pre[*],script[*],style[*]',
            height: 400,
            verify_html: false,
            force_p_newlines: false,
            relative_urls: true,
            remove_script_host: false,
            content_css: ["/assets/admin/gogies.css", "/assets/admin/tinymce_content.css"],
            content_style: "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'); body { font-family: 'Inter', sans-serif; font-size: 14px; color: #334155; line-height: 1.6; padding: 20px; }",
            setup: function (editor) {
                editor.on('change', function () { editor.save(); });
            }
        });
    }

    $('.tab-btn').on('click', function() {
        var target = $(this).attr('data-tab');
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').addClass('tw-hidden');
        $(target).removeClass('tw-hidden');
    });

    $('#add_new_item').on('click', function() {
        var c = parseInt($('#current_count').val()) + 1;
        var row = '<div class="tw-grid tw-grid-cols-12 tw-gap-3 tw-items-center tw-bg-white tw-p-2 tw-rounded tw-border tw-border-slate-200 item-row animated slideInDown" id="item_c' + c + '">' +
            '<div class="tw-col-span-1 tw-text-center"><button type="button" onclick="$(\'#item_c' + c + '\').remove(); recalcTotals();" class="tw-text-rose-400 hover:tw-text-rose-600"><i class="fa fa-trash"></i></button></div>' +
            '<div class="tw-col-span-5"><input type="text" name="item_' + c + '" class="tw-w-full tw-bg-slate-50 tw-border tw-border-slate-200 tw-p-1.5 tw-text-xs tw-rounded focus:tw-bg-white focus:tw-border-orange-500 tw-outline-none"></div>' +
            '<div class="tw-col-span-2"><input type="text" name="item_qty_' + c + '" class="tw-w-full tw-text-center tw-bg-slate-50 tw-border tw-border-slate-200 tw-p-1.5 tw-text-xs tw-rounded focus:tw-bg-white focus:tw-border-orange-500 tw-outline-none item-qty" value="1" onkeyup="recalcTotals()"></div>' +
            '<div class="tw-col-span-2"><input type="text" name="item_price_' + c + '" class="tw-w-full tw-text-center tw-bg-slate-50 tw-border tw-border-slate-200 tw-p-1.5 tw-text-xs tw-rounded focus:tw-bg-white focus:tw-border-orange-500 tw-outline-none item-price" value="0" onkeyup="recalcTotals()"></div>' +
            '<div class="tw-col-span-2 tw-text-right tw-text-xs tw-font-bold tw-text-slate-800 tw-pr-2"><span class="item-line-total">0.00</span></div></div>';
        $('#items').append(row);
        $('#current_count').val(c);
        recalcTotals();
    });

    $('#user').on('blur', function() {
        var email = $(this).val();
        if (!email) return;
        $('#check_user_msg').html('<div class="tw-bg-slate-50 tw-text-slate-500 tw-py-2 tw-px-3 tw-rounded tw-text-xs tw-font-bold tw-flex tw-items-center"><i class="fa fa-spinner fa-spin tw-mr-2"></i> Syncing profile...</div>');
        $.get('/admin/ajax/check-user?email=' + encodeURIComponent(email), function(data) {
            if (data.found) {
                $('#first_name').val(data.first_name || '');
                $('#last_name').val(data.last_name || '');
                $('#company').val(data.company || '');
                $('#check_user_msg').html('<div class="tw-bg-emerald-50 tw-text-emerald-600 tw-py-2 tw-px-3 tw-rounded tw-text-xs tw-font-bold tw-flex tw-items-center"><i class="fa fa-check-circle tw-mr-2"></i> Agent Verified: ' + (data.first_name || '') + '</div>');
            } else {
                 $('#check_user_msg').html('<div class="tw-bg-rose-50 tw-text-rose-600 tw-py-2 tw-px-3 tw-rounded tw-text-xs tw-font-bold tw-flex tw-items-center"><i class="fa fa-warning tw-mr-2"></i> User not found</div>');
                 $('#first_name, #last_name, #company').val('');
            }
        });
    });

    $('.ajax-load').on('click', function(e) {
        e.preventDefault();
        $('#ajax').fadeIn();
        $('#ajax_content').html('<div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-py-12"><div class="tw-w-8 tw-h-8 tw-border-[3px] tw-border-slate-100 tw-border-t-orange-600 tw-rounded-full tw-animate-spin tw-mb-4"></div><p class="tw-text-xs tw-text-slate-400">Loading...</p></div>');
        $.get($(this).attr('href'), function(html) { $('#ajax_content').html(html); });
    });

    $('form').on('submit', function() { if (typeof tinymce !== 'undefined') { tinymce.triggerSave(); } });
});
</script>
@endpush
