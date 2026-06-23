@extends('admin.layouts.app')
@section('title', 'Admin | Invoice Expenses')

@section('content')
<div class="tw-container tw-mx-auto tw-pb-20">

    @if(session('success'))
    <div class="tw-mb-6 tw-flex tw-items-center tw-gap-3 tw-px-5 tw-py-4 tw-rounded-2xl tw-bg-emerald-50 tw-border tw-border-emerald-100 tw-text-emerald-700 tw-font-semibold">
        <i class="fa fa-check-circle tw-text-xl"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- Page Header --}}
    <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-items-start md:tw-items-center tw-gap-6 tw-mb-10">
        <div>
            @php
                $booking = \App\Models\TourBooking::where('invoice_id', $invoice->id)->first();
                $guestDisplayName = '';
                if ($booking) {
                    if (!empty($booking->guest_name)) {
                        $guestDisplayName = $booking->guest_name;
                    } elseif ($booking->user) {
                        $guestDisplayName = trim($booking->user->first_name . ' ' . $booking->user->last_name);
                        if (empty($guestDisplayName) && !empty($booking->user->company)) {
                            $guestDisplayName = $booking->user->company;
                        }
                    }
                }
                $displayName = $guestDisplayName ?: html_entity_decode($invoice->desc);
            @endphp
            <div class="tw-flex tw-items-center tw-gap-3 tw-mb-1">
                <span class="tw-px-3 tw-py-1 tw-bg-slate-100 tw-text-slate-600 tw-rounded-lg tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest">Expense Management</span>
                <span class="tw-text-slate-300">/</span>
                <span class="tw-text-slate-500 tw-text-xs tw-font-medium">INV #{{ $invoice->id }}</span>
            </div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">{{ $displayName }}</h1>
            <p class="tw-text-slate-500 tw-mt-2 tw-text-sm tw-font-medium">Manage all operational costs and vendor payments for this invoice.</p>
        </div>

        <div class="tw-flex tw-flex-wrap tw-items-center tw-gap-3">
            @if($booking)
            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="tw-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2.5 tw-bg-white tw-border tw-border-slate-200 tw-text-slate-700 tw-rounded-xl tw-text-sm tw-font-bold hover:tw-bg-slate-50 tw-transition-all tw-shadow-sm tw-no-underline">
                <i class="fa fa-eye tw-text-slate-400"></i> View Booking
            </a>
            @endif

            <a href="{{ route('admin.expenses.mark-all-completed', ['invoice' => $invoiceId]) }}" onclick="return confirm('Mark all expenses as completed?');" class="tw-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2.5 tw-bg-amber-500 tw-text-white tw-rounded-xl tw-text-sm tw-font-bold hover:tw-bg-amber-600 tw-transition-all tw-shadow-md tw-shadow-amber-200 tw-no-underline">
                <i class="fa fa-check-double"></i> Mark All Complete
            </a>

            <div class="tw-relative tw-inline-block dropdown-container">
                <button type="button" class="tw-flex tw-items-center tw-gap-2 tw-px-5 tw-py-2.5 tw-bg-orange-600 tw-text-white tw-rounded-xl tw-text-sm tw-font-bold hover:tw-bg-orange-700 tw-transition-all tw-shadow-md tw-shadow-orange-200" onclick="toggleDropdown(this)">
                    <i class="fa fa-plus-circle"></i> Add New <i class="fa fa-chevron-down tw-text-[11px] tw-opacity-70"></i>
                </button>
                <div class="dropdown-menu tw-hidden tw-absolute tw-right-0 tw-mt-2 tw-w-48 tw-bg-white tw-border tw-border-slate-100 tw-rounded-2xl tw-shadow-xl tw-z-[100] tw-py-2">
                    @php
                        $addNewCountries = [71 => 'Egypt', 123 => 'Jordan', 134 => 'Lebanon', 137 => 'Libya', 160 => 'Morocco', 177 => 'Oman', 1565 => 'Palestine', 190 => 'Qatar', 203 => 'Saudi Arabia'];
                    @endphp
                    @foreach($addNewCountries as $cid => $cname)
                    <a href="#add_expense" onclick="setExpenseFilter('{{ $cid }}')" class="tw-flex tw-items-center tw-px-4 tw-py-2 tw-text-sm tw-text-slate-600 hover:tw-bg-slate-50 hover:tw-text-orange-600 tw-transition-all tw-no-underline">
                        <i class="fa fa-globe tw-mr-3 tw-text-slate-300"></i> {{ $cname }}
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="tw-relative tw-inline-block dropdown-container">
                <button type="button" class="tw-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2.5 tw-bg-white tw-border tw-border-slate-200 tw-text-slate-700 tw-rounded-xl tw-text-sm tw-font-bold hover:tw-bg-slate-50 tw-transition-all tw-shadow-sm" onclick="toggleDropdown(this)">
                    <i class="fa fa-bell"></i> Notifications <i class="fa fa-chevron-down tw-text-[11px] tw-opacity-70"></i>
                </button>
                <div class="dropdown-menu tw-hidden tw-absolute tw-right-0 tw-mt-2 tw-w-56 tw-bg-white tw-border tw-border-slate-100 tw-rounded-2xl tw-shadow-xl tw-z-[100] tw-py-2">
                    @forelse($vendors as $vid => $vname)
                        @if($vid)
                        <a href="javascript:void(0);" onclick="alert('Notification feature coming soon');" class="tw-flex tw-items-center tw-px-4 tw-py-2 tw-text-sm tw-text-slate-600 hover:tw-bg-slate-50 hover:tw-text-orange-600 tw-transition-all tw-no-underline">
                            <i class="fa fa-envelope-o tw-mr-3 tw-text-slate-300"></i> {{ $vname }}
                        </a>
                        @endif
                    @empty
                        <div class="tw-px-4 tw-py-2 tw-text-xs tw-text-slate-400">No vendors found</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Stats Cards --}}
    <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-6 tw-mb-12">
        {{-- Card 1: Total Cost (Requested to show Invoiced amount) --}}
        <div class="tw-bg-white/80 tw-backdrop-blur-md tw-border tw-border-slate-100 tw-rounded-3xl tw-p-6 tw-shadow-sm hover:tw-shadow-md tw-transition-all tw-group">
            <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4">
                <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-amber-50 tw-flex tw-items-center tw-justify-center tw-text-amber-500 tw-group-hover:tw-scale-110 tw-transition-transform">
                    <i class="fa fa-money tw-text-lg"></i>
                </div>
                <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Total Cost</span>
            </div>
            <div class="tw-flex tw-items-baseline tw-gap-2">
                <span id="stat-total-cost-display" class="tw-text-3xl tw-font-black tw-text-slate-900">{{ number_format($totalRevenue, 2) }}</span>
                <span class="tw-text-sm tw-font-bold tw-text-slate-400">JOD</span>
            </div>
        </div>

        {{-- Card 2: Total Paid --}}
        <div class="tw-bg-white/80 tw-backdrop-blur-md tw-border tw-border-slate-100 tw-rounded-3xl tw-p-6 tw-shadow-sm hover:tw-shadow-md tw-transition-all tw-group">
            <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4">
                <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-emerald-50 tw-flex tw-items-center tw-justify-center tw-text-emerald-500 tw-group-hover:tw-scale-110 tw-transition-transform">
                    <i class="fa fa-check tw-text-lg"></i>
                </div>
                <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Total Paid</span>
            </div>
            <div class="tw-flex tw-items-baseline tw-gap-2">
                <span id="stat-total-paid" class="tw-text-3xl tw-font-black tw-text-emerald-600">{{ number_format($totalPaid, 2) }}</span>
                <span class="tw-text-sm tw-font-bold tw-text-slate-400">JOD</span>
            </div>
        </div>

        {{-- Card 3: Total Unpaid --}}
        <div class="tw-bg-white/80 tw-backdrop-blur-md tw-border tw-border-slate-100 tw-rounded-3xl tw-p-6 tw-shadow-sm hover:tw-shadow-md tw-transition-all tw-group">
            <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4">
                <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-rose-50 tw-flex tw-items-center tw-justify-center tw-text-rose-500 tw-group-hover:tw-scale-110 tw-transition-transform">
                    <i class="fa fa-exclamation-circle tw-text-lg"></i>
                </div>
                <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Total Unpaid</span>
            </div>
            <div class="tw-flex tw-items-baseline tw-gap-2">
                <span id="stat-total-unpaid" class="tw-text-3xl tw-font-black tw-text-rose-600">{{ number_format($totalUnpaid, 2) }}</span>
                <span class="tw-text-sm tw-font-bold tw-text-slate-400">JOD</span>
            </div>
        </div>

        {{-- Card 4: Total Profit --}}
        <div class="tw-bg-white/80 tw-backdrop-blur-md tw-border tw-border-slate-100 tw-rounded-3xl tw-p-6 tw-shadow-sm hover:tw-shadow-md tw-transition-all tw-group">
            <div class="tw-flex tw-items-center tw-gap-4 tw-mb-4">
                <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-orange-50 tw-flex tw-items-center tw-justify-center tw-text-orange-500 tw-group-hover:tw-scale-110 tw-transition-transform">
                    <i class="fa fa-line-chart tw-text-lg"></i>
                </div>
                <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Total Profit</span>
            </div>
            <div class="tw-flex tw-items-baseline tw-gap-2">
                <span id="stat-total-profit" class="tw-text-3xl tw-font-black tw-text-orange-600">{{ number_format($totalProfit, 2) }}</span>
                <span class="tw-text-sm tw-font-bold tw-text-slate-400">JOD</span>
            </div>
            <div class="tw-mt-2 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-flex tw-items-center tw-gap-1">
                <i class="fa fa-info-circle"></i> <span id="stat-total-operational">Operational Expenses: {{ number_format($totalCost, 2) }} JOD</span>
            </div>
        </div>
    </div>



    <input type="hidden" id="expense_country_filter" value="">

    {{-- Expenses Table Section --}}
    <div class="tw-bg-white tw-rounded-[2.5rem] tw-border tw-border-slate-100 tw-shadow-sm tw-overflow-hidden tw-mb-10">
        
        {{-- Table Toolbar --}}
        <div class="tw-px-8 tw-py-6 tw-border-b tw-border-slate-50 tw-flex tw-flex-col sm:tw-flex-row tw-justify-between tw-items-center tw-gap-4">
            <div class="tw-flex tw-items-center tw-gap-3">
                <div class="tw-w-1 tw-h-6 tw-bg-orange-600 tw-rounded-full"></div>
                <h2 class="tw-text-lg tw-font-bold tw-text-slate-800">Operational Log</h2>
            </div>
            
            <div class="tw-flex tw-items-center tw-gap-4 tw-w-full sm:tw-w-auto">
                {{-- Vendor Filter Relocated --}}
                @php
                    $currentVender = request('vender');
                    $togglerLabel = 'Filter by Vendor';
                    if ($currentVender && isset($vendors[$currentVender])) {
                        $togglerLabel = $vendors[$currentVender];
                        if (strlen($togglerLabel) > 20) $togglerLabel = substr($togglerLabel, 0, 20) . '...';
                    }
                @endphp
                <div class="tw-relative dropdown-container tw-flex-1 sm:tw-flex-initial">
                    <button type="button" class="tw-w-full tw-flex tw-items-center tw-gap-3 tw-px-4 tw-py-2.5 tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-font-bold tw-text-slate-600 hover:tw-bg-slate-100 tw-transition-all" onclick="toggleDropdown(this)">
                        <i class="fa fa-filter tw-text-slate-400"></i>
                        <span>{{ $togglerLabel }}</span>
                        <i class="fa fa-chevron-down tw-text-[11px] tw-opacity-50 tw-ml-2"></i>
                    </button>
                    <div class="dropdown-menu tw-hidden tw-absolute tw-right-0 tw-mt-2 tw-w-64 tw-bg-white tw-border tw-border-slate-100 tw-rounded-2xl tw-shadow-2xl tw-z-[100] tw-py-2 tw-max-h-72 tw-overflow-y-auto">
                        <div class="tw-px-4 tw-py-2 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Select Provider</div>
                        <a href="{{ route('admin.expenses.index', ['invoice' => $invoiceId]) }}" class="tw-flex tw-items-center tw-px-4 tw-py-2.5 tw-text-sm tw-text-slate-600 hover:tw-bg-slate-50 hover:tw-text-orange-600 tw-transition-all tw-no-underline {{ !$currentVender ? 'tw-bg-orange-50 tw-text-orange-600 tw-font-bold' : '' }}">
                            <i class="fa fa-circle-o tw-mr-3 tw-opacity-30"></i> All Vendors
                        </a>
                        @foreach($vendors as $vid => $vname)
                        @if($vid)
                        <a href="{{ route('admin.expenses.index', ['invoice' => $invoiceId, 'vender' => $vid]) }}" class="tw-flex tw-items-center tw-px-4 tw-py-2.5 tw-text-sm tw-text-slate-600 hover:tw-bg-slate-50 hover:tw-text-orange-600 tw-transition-all tw-no-underline {{ $currentVender == $vid ? 'tw-bg-orange-50 tw-text-orange-600 tw-font-bold' : '' }}">
                            <i class="fa fa-building-o tw-mr-3 tw-opacity-30"></i> {{ $vname }}
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>

                {{-- Action: Export or similar (Future) --}}
                <button type="button" onclick="window.print()" class="tw-p-2.5 tw-bg-white tw-border tw-border-slate-200 tw-rounded-xl tw-text-slate-400 hover:tw-text-orange-600 hover:tw-border-orange-100 tw-transition-all" title="Print Log">
                    <i class="fa fa-print"></i>
                </button>
            </div>
        </div>

        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50">
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Description</th>
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Cost / Payment</th>
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Confirmation</th>
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Vendor & Status</th>
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Performance Dates</th>
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Region</th>
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($expenses as $expense)
                    @php
                        $serviceDesc = $expense->service ? $expense->service->description : 'N/A';
                        $venderName = '-';
                        if ($expense->venderUser) {
                            $venderName = !empty($expense->venderUser->company) ? $expense->venderUser->company : $expense->venderUser->email;
                        }
                        $statusCode = $expense->status ?? 'pen';
                        $statusLabel = $statusList[$statusCode] ?? ucfirst($statusCode);
                        $paymentLabel = ($expense->payment_status == 'c') ? 'Paid' : 'Unpaid';
                        
                        $statusClasses = [
                            'pen' => 'tw-bg-amber-50 tw-text-amber-600',
                            'con' => 'tw-bg-emerald-50 tw-text-emerald-600',
                            'com' => 'tw-bg-orange-50 tw-text-orange-600',
                            'can' => 'tw-bg-rose-50 tw-text-rose-600',
                            'inp' => 'tw-bg-blue-50 tw-text-blue-600',
                        ];
                        $badgeClass = $statusClasses[$statusCode] ?? 'tw-bg-slate-50 tw-text-slate-600';

                        $countryName = $expense->service && $expense->service->country ? ($countries[$expense->service->country] ?? '') : '';
                        $categoryName = $expense->service && $expense->service->serviceCategory ? ($expense->service->serviceCategory->name ?? '') : '';
                    @endphp
                    @php
                        // For itinerary-converted expenses, name is stored in remarks (format: "Name | Day X")
                        $rawRemarks = $expense->remarks ?? '';
                        $remarksParts = explode(' | ', $rawRemarks, 2);
                        $svcNameFromRemarks = count($remarksParts) > 1 ? $remarksParts[0] : '';
                        $remarksInfo = count($remarksParts) > 1 ? $remarksParts[1] : $rawRemarks;

                        $serviceDesc = $expense->service
                            ? $expense->service->description
                            : ($svcNameFromRemarks ?: 'Custom Service');
                    @endphp
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-px-6 tw-py-5">
                            <div class="tw-flex tw-items-center tw-gap-3 tw-mb-1">
                                <span class="tw-px-2 tw-py-0.5 tw-bg-orange-50 tw-text-orange-600 tw-rounded-md tw-text-[11px] tw-font-black">QTY: {{ $expense->qty }}</span>
                                @if(!$expense->service && $svcNameFromRemarks)
                                <span class="tw-px-2 tw-py-0.5 tw-bg-purple-50 tw-text-purple-600 tw-rounded-md tw-text-[11px] tw-font-bold">Custom</span>
                                @endif
                            </div>
                            <div class="tw-text-sm tw-font-bold tw-text-slate-900 tw-line-clamp-1">{{ $serviceDesc }}</div>
                            @if($remarksInfo)
                            <div class="tw-text-[11px] tw-text-slate-400 tw-mt-1 tw-italic tw-flex tw-items-center tw-gap-1">
                                <i class="fa fa-map-marker"></i> {{ $remarksInfo }}
                            </div>
                            @endif
                        </td>
                        <td class="tw-px-6 tw-py-5">
                            <div class="tw-text-sm tw-font-black tw-text-slate-900">{{ number_format($expense->cost, 2) }} <span class="tw-text-[11px] tw-text-slate-400">JOD</span></div>
                            <div class="tw-mt-1">
                                <span class="tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wider {{ $expense->payment_status == 'c' ? 'tw-text-emerald-500' : 'tw-text-rose-500' }}">
                                    {{ $paymentLabel }}
                                </span>
                            </div>
                        </td>
                        <td class="tw-px-6 tw-py-5">
                            <span class="tw-text-xs tw-font-medium tw-text-slate-500">{{ $expense->confirmation_number ?: '---' }}</span>
                        </td>
                        <td class="tw-px-6 tw-py-5">
                            <div class="tw-text-sm tw-font-bold tw-text-slate-700 tw-mb-2">{{ $venderName }}</div>
                            <span class="tw-inline-flex tw-px-2.5 tw-py-1 tw-rounded-lg tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wider {{ $badgeClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="tw-px-6 tw-py-5">
                            <div class="tw-flex tw-flex-col tw-gap-1">
                                <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-700">
                                    <i class="fa fa-calendar-check-o tw-text-orange-400 tw-w-4"></i> {{ $expense->service_date }}
                                </div>
                                @if($expense->service_end_date && $expense->service_end_date != '0000-00-00')
                                <div class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-medium tw-text-slate-400">
                                    <i class="fa fa-calendar-times-o tw-w-4"></i> {{ $expense->service_end_date }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="tw-px-6 tw-py-5">
                            @if($countryName)
                            <div class="tw-text-xs tw-font-bold tw-text-orange-600">{{ $countryName }}</div>
                            @endif
                            @if($categoryName)
                            <div class="tw-text-[11px] tw-font-medium tw-text-slate-400 tw-mt-0.5">{{ $categoryName }}</div>
                            @endif
                        </td>
                        <td class="tw-px-6 tw-py-5 tw-text-right">
                            <div class="tw-flex tw-items-center tw-justify-end tw-gap-1">
                                <button onclick="editExpense({{ $expense->id }});" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-text-slate-400 hover:tw-bg-orange-50 hover:tw-text-orange-600 tw-transition-all" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <a href="{{ route('admin.expenses.index', ['invoice' => $invoiceId]) }}" onclick="return confirm('Mark as confirmed?')" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-text-slate-400 hover:tw-bg-emerald-50 hover:tw-text-emerald-600 tw-transition-all" title="Confirm">
                                    <i class="fa fa-check"></i>
                                </a>
                                <button onclick="showHistory({{ $expense->id }});" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-text-slate-400 hover:tw-bg-orange-50 hover:tw-text-orange-600 tw-transition-all" title="History">
                                    <i class="fa fa-history"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.expenses.destroy', $expense->id) }}" class="tw-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-text-slate-400 hover:tw-bg-rose-50 hover:tw-text-rose-600 tw-transition-all" onclick="return confirm('Are you sure you want to delete this expense?')" title="Delete">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="tw-px-6 tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-bg-slate-50 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-slate-200 tw-text-2xl">
                                    <i class="fa fa-folder-open-o"></i>
                                </div>
                                <div class="tw-text-slate-400 tw-font-medium">No expenses recorded for this invoice yet.</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<div id="ajax"></div>
<div id="ajax2"></div>

    {{-- Add Expense Modal --}}
    <div class="modal" id="add_expense" style="display: none;">
        <div style="max-width: 1100px; width: 95%; background: #ffffff; border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.25); overflow: hidden; position: relative; margin: 30px auto;">
            <a href="#close" class="close" style="position: absolute; top: 18px; right: 22px; width: 36px; height: 36px; line-height:36px; text-align:center; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-radius: 50%; color: #fff; text-decoration: none; font-size: 18px; z-index: 30; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">&times;</a>
            
            {{-- Header --}}
            <div style="padding: 28px 32px; background: linear-gradient(135deg, #f97316, #ea580c, #c2410c); color: white; position: relative; overflow: hidden;">
                <div style="position: absolute; right: -30px; top: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                <div style="position: absolute; right: 60px; bottom: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                <div style="position: relative; z-index: 10;">
                    <h3 style="font-size: 22px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 12px; color: #fff; border:none; padding:0;">
                        <span style="display:inline-flex; width: 40px; height: 40px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-radius: 12px; align-items: center; justify-content: center;">
                            <i class="fa fa-plus-circle"></i>
                        </span>
                        Add New Operational Expense
                    </h3>
                    <p style="color: rgba(224,231,255,0.9); font-size: 13px; margin-top: 8px; margin-bottom: 0; font-weight: 500;">Select a category and service to record a new cost.</p>
                </div>
            </div>

            {{-- Body --}}
            <div style="display: flex; flex-direction: row; height: 62vh; min-height: 520px;">
                {{-- Left Panel: Category Tree --}}
                <div style="width: 38%; border-right: 1px solid #f1f5f9; background: linear-gradient(180deg, #f8fafc, #f1f5f9); padding: 20px; display: flex; flex-direction: column;">
                    {{-- Search --}}
                    <div style="margin-bottom: 16px; flex: none;">
                        <div style="position: relative;">
                            <i class="fa fa-search" style="position: absolute; left: 14px; top: 13px; color: #94a3b8; font-size: 13px;"></i>
                            <input type="text" id="category_search" placeholder="Search categories..." style="width: 100%; padding: 12px 14px 12px 38px; background: #fff; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 13px; font-weight: 600; outline: none; box-sizing: border-box; transition: all 0.2s; color: #334155;" onfocus="this.style.borderColor='#fb923c';this.style.boxShadow='0 0 0 4px rgba(251,146,60,0.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                        </div>
                        <div style="display: flex; gap: 8px; margin-top: 10px;">
                            <button type="button" onclick="collapseAll();" style="flex: 1; padding: 8px 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 10px; font-weight: 700; color: #64748b; cursor: pointer; letter-spacing: 0.5px; transition: all 0.2s;" onmouseover="this.style.background='#fff7ed';this.style.borderColor='#fed7aa';this.style.color='#ea580c'" onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0';this.style.color='#64748b'">COLLAPSE</button>
                            <button type="button" onclick="expandAll();" style="flex: 1; padding: 8px 0; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 10px; font-weight: 700; color: #64748b; cursor: pointer; letter-spacing: 0.5px; transition: all 0.2s;" onmouseover="this.style.background='#fff7ed';this.style.borderColor='#fed7aa';this.style.color='#ea580c'" onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0';this.style.color='#64748b'">EXPAND</button>
                        </div>
                    </div>

                    {{-- Tree --}}
                    <div id="category_tree" style="flex: 1; overflow-y: auto; padding-right: 5px;" class="custom-scrollbar">
                        <div style="margin-bottom: 6px;">
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 10px 12px; border-radius: 10px; transition: all 0.15s;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">
                                <input type="radio" name="category_parent" value="0" style="margin: 0; cursor: pointer; accent-color: #f97316;">
                                <i class="fa fa-globe" style="color: #f97316; font-size: 15px;"></i>
                                <span style="font-size: 14px; font-weight: 700; color: #334155;">Global Services</span>
                            </label>
                        </div>
                        @foreach($categories as $cat)
                            @include('admin.expenses._category_node', ['node' => $cat, 'depth' => 0])
                        @endforeach
                    </div>
                </div>

                {{-- Right Panel --}}
                <div style="width: 62%; background: #ffffff; padding: 30px; overflow-y: auto;" class="custom-scrollbar">
                    <div id="services-list" style="min-height: 100%; display: flex; flex-direction: column;">
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #fff7ed, #ffedd5); border-radius: 24px; display: flex; align-items: center; justify-content: center; color: #fb923c; font-size: 32px; margin-bottom: 24px; box-shadow: 0 8px 24px rgba(249,115,22,0.1);">
                                <i class="fa fa-hand-o-left"></i>
                            </div>
                            <h4 style="font-size: 20px; font-weight: 800; color: #1e293b; margin: 0 0 10px 0;">Ready to Add</h4>
                            <p style="font-size: 14px; color: #94a3b8; max-width: 280px; margin: 0; line-height: 1.6;">Select a category from the left to browse available vendor services.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Expense Modal --}}
    <div class="modal" id="edit_expense">
        <div class="tw-max-w-3xl tw-w-full tw-bg-white tw-rounded-[2rem] tw-shadow-[0_20px_50px_rgba(0,0,0,0.2)] tw-overflow-hidden tw-relative tw-border tw-border-slate-100 tw-my-12 !tw-max-h-none">
            <a href="#close" class="close tw-absolute tw-top-6 tw-right-6 tw-w-10 tw-h-10 tw-bg-white/10 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-white/60 hover:tw-bg-white/20 hover:tw-text-white tw-transition-all tw-z-30 tw-no-underline">&times;</a>
            
            <div class="tw-p-10 tw-bg-orange-600 tw-text-white tw-relative tw-overflow-hidden">
                <div class="tw-absolute -tw-right-10 -tw-top-10 tw-w-40 tw-h-40 tw-bg-white/10 tw-rounded-full tw-blur-3xl"></div>
                
                <div class="tw-relative tw-z-10">
                    <h3 class="tw-text-2xl tw-font-black tw-flex tw-items-center tw-gap-4 tw-m-0 tw-text-white !tw-border-none !tw-p-0 !tw-m-0 !tw-leading-none">
                        <div class="tw-w-10 tw-h-10 tw-bg-white/20 tw-rounded-xl tw-flex tw-items-center tw-justify-center">
                            <i class="fa fa-edit tw-text-lg"></i>
                        </div>
                        Edit Operational Cost
                    </h3>
                    <p class="tw-text-orange-100 tw-text-sm tw-mt-3 tw-font-medium tw-opacity-90">Modify the specifics of this financial record.</p>
                </div>
            </div>

            <div id="edit-expense-content" class="tw-p-8 tw-bg-white">
                <div class="tw-py-20 tw-text-center">
                    <i class="fa fa-spinner fa-spin tw-text-4xl tw-text-orange-500"></i>
                    <p class="tw-mt-4 tw-text-slate-400 tw-font-bold tw-uppercase tw-tracking-widest tw-text-[11px]">Syncing Archive...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- History Modal --}}
    <div class="modal" id="expense_history">
        <div class="tw-max-w-4xl tw-w-full tw-bg-white tw-rounded-[2rem] tw-shadow-[0_20px_50px_rgba(0,0,0,0.2)] tw-overflow-hidden tw-relative tw-border tw-border-slate-100 tw-my-12 !tw-max-h-none">
            <a href="#close" class="close tw-absolute tw-top-6 tw-right-6 tw-w-10 tw-h-10 tw-bg-white/10 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-white/60 hover:tw-bg-white/20 hover:tw-text-white tw-transition-all tw-z-30 tw-no-underline">&times;</a>
            
            <div class="tw-p-10 tw-bg-slate-900 tw-text-white tw-relative tw-overflow-hidden">
                <div class="tw-absolute -tw-right-10 -tw-top-10 tw-w-40 tw-h-40 tw-bg-orange-500/10 tw-rounded-full tw-blur-3xl"></div>
                
                <div class="tw-relative tw-z-10">
                    <h3 class="tw-text-2xl tw-font-black tw-flex tw-items-center tw-gap-4 tw-m-0 tw-text-white !tw-border-none !tw-p-0 !tw-m-0 !tw-leading-none">
                        <div class="tw-w-10 tw-h-10 tw-bg-white/10 tw-rounded-xl tw-flex tw-items-center tw-justify-center">
                            <i class="fa fa-history tw-text-lg"></i>
                        </div>
                        Cost Audit History
                    </h3>
                    <p class="tw-text-slate-400 tw-text-sm tw-mt-3 tw-font-medium">Chronological record of all modifications and updates.</p>
                </div>
            </div>

            <div id="history-content" class="tw-p-8 tw-bg-white tw-max-h-[65vh] tw-overflow-y-auto custom-scrollbar">
                <div class="tw-py-20 tw-text-center">
                    <i class="fa fa-spinner fa-spin tw-text-4xl tw-text-slate-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
 {{-- End container --}}

<script>
var invoiceId = {{ $invoiceId }};

/**
 * Modern Dropdown Toggle
 */
function toggleDropdown(button) {
    const container = button.closest('.dropdown-container');
    const menu = container.querySelector('.dropdown-menu');
    const allMenus = document.querySelectorAll('.dropdown-menu');
    
    // Check if this menu is already open
    const isOpen = !menu.classList.contains('tw-hidden');
    
    // Close all other menus
    allMenus.forEach(m => m.classList.add('tw-hidden'));
    
    // Toggle current menu
    if (!isOpen) {
        menu.classList.remove('tw-hidden');
    }

    // Stop propagation
    if (window.event) window.event.stopPropagation();
}

// Close menus on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown-container')) {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('tw-hidden'));
    }
});

/**
 * Financial Summary Synchronization
 */
function refreshSummaryCards(totals) {
    if (!totals) return;
    
    const costEl = document.getElementById('stat-total-cost-display');
    const paidEl = document.getElementById('stat-total-paid');
    const unpaidEl = document.getElementById('stat-total-unpaid');
    const profitEl = document.getElementById('stat-total-profit');
    const operEl = document.getElementById('stat-total-operational');

    if (costEl) costEl.innerText = parseFloat(totals.totalRevenue).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    if (paidEl) paidEl.innerText = parseFloat(totals.totalPaid).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    if (unpaidEl) unpaidEl.innerText = parseFloat(totals.totalUnpaid).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    if (operEl) operEl.innerText = 'Operational Expenses: ' + parseFloat(totals.totalCost).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' JOD';
    
    if (profitEl) {
        const profitValue = parseFloat(totals.totalProfit);
        profitEl.innerText = profitValue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        profitEl.classList.remove('tw-text-rose-600');
        profitEl.classList.add('tw-text-orange-600');
    }

    // Visual feedback: brief highlight
    [costEl, paidEl, unpaidEl, profitEl].forEach(el => {
        if (el) {
            el.style.transition = 'all 0.3s ease';
            el.style.transform = 'scale(1.1)';
            setTimeout(() => el.style.transform = 'scale(1)', 300);
        }
    });
}

/**
 * Category Tree Logic
 */
function toggleChildren(el) {
    const container = el.closest('.category-item');
    const children = container.querySelector('.category-children');
    const icon = el.querySelector('i');
    
    if (children) {
        if (children.classList.contains('tw-hidden')) {
            children.classList.remove('tw-hidden');
            icon.className = 'fa fa-minus-square-o';
        } else {
            children.classList.add('tw-hidden');
            icon.className = 'fa fa-plus-square-o';
        }
    }
}

function collapseAll() {
    document.querySelectorAll('.category-children').forEach(el => el.classList.add('tw-hidden'));
    document.querySelectorAll('.category-toggle i').forEach(el => el.className = 'fa fa-plus-square-o');
}

function expandAll() {
    document.querySelectorAll('.category-children').forEach(el => el.classList.remove('tw-hidden'));
    document.querySelectorAll('.category-toggle i').forEach(el => el.className = 'fa fa-minus-square-o');
}

/**
 * Service & Expense Logic
 */
function setExpenseFilter(cid) {
    document.getElementById('expense_country_filter').value = cid;
    window.location.hash = 'add_expense';
}

// Search operational categories
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('category_search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.category-item').forEach(item => {
                const name = item.getAttribute('data-name');
                if (name && name.includes(query)) {
                    item.classList.remove('tw-hidden');
                    // Ensure parents are visible
                    let current = item.closest('.category-children');
                    while (current) {
                        current.classList.remove('tw-hidden');
                        current = current.parentElement.closest('.category-children');
                    }
                } else if (!query) {
                    item.classList.remove('tw-hidden');
                } else {
                    item.classList.add('tw-hidden');
                }
            });
        });
    }

    // Category click listener
    document.querySelectorAll('input[name=category_parent]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value != "0") loadServices(this.value);
        });
    });
});

function loadServices(categoryId, venderFilter) {
    const servicesList = document.getElementById('services-list');
    servicesList.innerHTML = '<div class="tw-flex-1 tw-flex tw-items-center tw-justify-center"><i class="fa fa-spinner fa-spin tw-text-3xl tw-text-orange-500"></i></div>';

    let url = `/admin/expenses/services?category=${categoryId}`;
    if (venderFilter) url += `&vender=${venderFilter}`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            let html = `
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-6">
                    <div>
                        <h4 class="tw-text-lg tw-font-black tw-text-slate-900">${data.categoryName}</h4>
                        <p class="tw-text-xs tw-text-slate-400 tw-font-bold tw-uppercase tw-tracking-wider tw-mt-0.5">Available Services</p>
                    </div>
                </div>

                <div class="tw-flex tw-items-center tw-gap-2 tw-p-3 tw-bg-slate-50 tw-rounded-2xl tw-mb-6">
                    <i class="fa fa-filter tw-text-slate-300 tw-ml-2"></i>
                    <select id="service_vender_filter" onchange="loadServices(${categoryId}, this.value);" class="tw-flex-1 tw-bg-transparent tw-border-none tw-text-xs tw-font-bold tw-text-slate-600 focus:tw-ring-0">
                        <option value="">All Vendors</option>
                        ${Object.entries(data.vendors || {}).map(([vid, vname]) => `
                            <option value="${vid}" ${venderFilter == vid ? 'selected' : ''}>${vname}</option>
                        `).join('')}
                    </select>
                </div>

                <div class="tw-space-y-3 tw-max-h-[350px] tw-overflow-y-auto tw-pr-2 custom-scrollbar">`;

            if (data.services && data.services.length > 0) {
                data.services.forEach(s => {
                    html += `
                        <div class="tw-group tw-flex tw-items-center tw-justify-between tw-p-4 tw-bg-white tw-border tw-border-slate-100 tw-rounded-2xl hover:tw-border-orange-200 hover:tw-shadow-md tw-transition-all">
                            <div class="tw-flex-1">
                                <div class="tw-text-sm tw-font-bold tw-text-slate-800">${s.description}</div>
                                <div class="tw-text-[11px] tw-text-slate-400 tw-mt-1 tw-font-medium">${s.vender_name || 'Generic Vendor'}</div>
                            </div>
                            <div class="tw-text-right tw-px-6">
                                <div class="tw-text-sm tw-font-black tw-text-orange-600">${s.cost} <small class="tw-text-[11px] tw-text-slate-400 tw-font-bold">JOD</small></div>
                            </div>
                            <button onclick="selectService(${s.id});" class="tw-px-4 tw-py-2 tw-bg-orange-50 tw-text-orange-600 tw-rounded-xl tw-text-xs tw-font-bold hover:tw-bg-orange-600 hover:tw-text-white tw-transition-all">
                                SELECT
                            </button>
                        </div>`;
                });
            } else {
                html += `
                    <div class="tw-py-12 tw-text-center">
                        <div class="tw-text-slate-300 tw-mb-3"><i class="fa fa-info-circle fa-2x"></i></div>
                        <p class="tw-text-sm tw-font-medium tw-text-slate-400">No specific services found for this vendor/category.</p>
                    </div>`;
            }

            html += `</div>`;
            servicesList.innerHTML = html;
        })
        .catch(err => {
            servicesList.innerHTML = `<div class="tw-p-4 tw-bg-rose-50 tw-text-rose-600 tw-text-sm tw-rounded-xl">Error: ${err.message}</div>`;
        });
}

function selectService(serviceId) {
    const servicesList = document.getElementById('services-list');
    
    fetch(`/admin/expenses/service-detail?service=${serviceId}`)
        .then(r => r.json())
        .then(data => {
            let html = `
                <div class="tw-mb-8">
                    <button onclick="loadServices(${data.category_id});" class="tw-text-xs tw-font-bold tw-text-slate-400 hover:tw-text-orange-600 tw-flex tw-items-center tw-gap-2 tw-mb-6 tw-transition-colors">
                        <i class="fa fa-arrow-left"></i> BACK TO SERVICES
                    </button>
                    
                    <div class="tw-p-6 tw-bg-slate-50 tw-rounded-3xl tw-border tw-border-slate-100">
                        <div class="tw-flex tw-items-start tw-gap-4">
                            <div class="tw-w-12 tw-h-12 tw-bg-white tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-text-orange-500 tw-shadow-sm">
                                <i class="fa fa-tag tw-text-xl"></i>
                            </div>
                            <div>
                                <h4 class="tw-text-lg tw-font-black tw-text-slate-900">${data.description}</h4>
                                <p class="tw-text-xs tw-font-bold tw-text-orange-600 tw-uppercase tw-tracking-widest tw-mt-1">${data.vender_name || 'Standard Vendor'}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="expense_form" class="tw-space-y-6">
                    <input type="hidden" name="invoice_id" value="${invoiceId}">
                    <input type="hidden" name="service_id" value="${data.id}">

                    <div class="tw-grid tw-grid-cols-2 tw-gap-6">
                        <div class="tw-space-y-2">
                            <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Performance Start</label>
                            <input type="date" name="date" class="tw-w-full tw-px-4 tw-py-3 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded-2xl tw-text-sm focus:tw-ring-4 focus:tw-ring-orange-500/10 focus:tw-bg-white tw-transition-all" required>
                        </div>
                        <div class="tw-space-y-2">
                            <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Performance End</label>
                            <input type="date" name="end_date" class="tw-w-full tw-px-4 tw-py-3 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded-2xl tw-text-sm focus:tw-ring-4 focus:tw-ring-orange-500/10 focus:tw-bg-white tw-transition-all">
                        </div>
                    </div>

                    <div class="tw-grid tw-grid-cols-2 tw-gap-6">
                        <div class="tw-space-y-2">
                            <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Unit Cost (JOD)</label>
                            <input type="number" name="cost" value="${data.cost}" step="0.01" class="tw-w-full tw-px-4 tw-py-3 tw-bg-orange-50/50 tw-border tw-border-orange-100 tw-rounded-2xl tw-text-sm tw-font-black tw-text-orange-700 focus:tw-ring-4 focus:tw-ring-orange-500/10 focus:tw-bg-white tw-transition-all" required>
                        </div>
                        <div class="tw-space-y-2">
                            <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Start Time</label>
                            <input type="time" name="time" class="tw-w-full tw-px-4 tw-py-3 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded-2xl tw-text-sm focus:tw-ring-4 focus:tw-ring-orange-500/10 focus:tw-bg-white tw-transition-all">
                        </div>
                    </div>

                    <div class="tw-grid tw-grid-cols-3 tw-gap-6">
                        <div class="tw-space-y-2">
                            <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Quantity</label>
                            <input type="number" name="qty" value="1" min="1" class="tw-w-full tw-px-4 tw-py-3 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded-2xl tw-text-sm focus:tw-ring-4 focus:tw-ring-orange-500/10 focus:tw-bg-white tw-transition-all">
                        </div>
                        <div class="tw-space-y-2">
                            <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Duration</label>
                            <input type="number" name="duration" value="1" min="1" class="tw-w-full tw-px-4 tw-py-3 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded-2xl tw-text-sm focus:tw-ring-4 focus:tw-ring-orange-500/10 focus:tw-bg-white tw-transition-all">
                        </div>
                        <div class="tw-space-y-2">
                            <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Status</label>
                            <select name="status" class="tw-w-full tw-px-4 tw-py-3 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded-2xl tw-text-sm focus:tw-ring-4 focus:tw-ring-orange-500/10 focus:tw-bg-white tw-transition-all tw-font-bold">
                                <option value="pen">Pending</option>
                                <option value="con" selected>Confirmed</option>
                                <option value="com">Completed</option>
                                <option value="can">Cancelled</option>
                                <option value="inp">In Process</option>
                            </select>
                        </div>
                    </div>

                    <div class="tw-space-y-2">
                        <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">Remarks / Internal Notes</label>
                        <textarea name="remarks" class="tw-w-full tw-px-4 tw-py-3 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded-2xl tw-text-sm focus:tw-ring-4 focus:tw-ring-orange-500/10 focus:tw-bg-white tw-transition-all tw-h-20"></textarea>
                    </div>

                    <div class="tw-pt-4">
                        <button type="button" onclick="saveExpense();" class="tw-w-full tw-py-4 tw-bg-orange-600 tw-text-white tw-rounded-2xl tw-text-sm tw-font-black tw-shadow-xl tw-shadow-orange-200 hover:tw-bg-orange-700 hover:tw-scale-[1.02] tw-transition-all tw-flex tw-items-center tw-justify-center tw-gap-3">
                            <i class="fa fa-check-circle"></i> CONFIRM & SAVE EXPENSE
                        </button>
                    </div>
                </form>`;

            servicesList.innerHTML = html;
        })
        .catch(err => { console.error(err); });
}

function saveExpense() {
    const form = document.getElementById('expense_form');
    const formData = new FormData(form);
    const servicesList = document.getElementById('services-list');

    fetch('/admin/expenses/store', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            servicesList.innerHTML = `
                <div class="tw-flex-1 tw-flex tw-flex-col tw-items-center tw-justify-center tw-text-center">
                    <div class="tw-w-20 tw-h-20 tw-bg-emerald-50 tw-text-emerald-500 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-3xl tw-mb-6">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <h4 class="tw-text-xl tw-font-black tw-text-slate-900">Expense Saved!</h4>
                    <p class="tw-text-sm tw-text-slate-400 tw-mt-2">${data.message}</p>
                    <a href="/admin/expenses?invoice=${invoiceId}" class="tw-mt-8 tw-px-8 tw-py-3 tw-bg-slate-900 tw-text-white tw-rounded-xl tw-text-sm tw-font-bold tw-no-underline hover:tw-bg-slate-800 tw-transition-colors">Refresh Interface</a>
                </div>`;
            refreshSummaryCards(data.totals);
        } else {
            alert(data.message || 'Validation error, please check the form.');
        }
    })
    .catch(err => { alert('Network Error: ' + err.message); });
}

function editExpense(expenseId) {
    window.location.hash = 'edit_expense';
    const content = document.getElementById('edit-expense-content');
    content.innerHTML = '<div class="tw-py-20 tw-text-center"><i class="fa fa-spinner fa-spin tw-text-4xl tw-text-orange-500"></i></div>';

    fetch(`/admin/expenses/${expenseId}/edit`)
        .then(r => r.json())
        .then(data => {
            const statusOptions = { 'pen': 'Pending', 'con': 'Confirmed', 'com': 'Completed', 'can': 'Cancelled', 'inp': 'In Process' };
            let html = `
                <div id="edit_ajax_msg" class="tw-mb-4"></div>
                <form id="edit_expense_form">
                    <input type="hidden" name="expense_id" value="${data.id}">
                    
                    {{-- Scrollable Body --}}
                    <div class="tw-max-h-[60vh] tw-overflow-y-auto tw-px-1 tw-py-2 custom-scrollbar">
                        <div class="tw-p-5 tw-bg-orange-50/30 tw-rounded-2xl tw-border tw-border-orange-100/50 tw-mb-6 tw-flex tw-items-center tw-gap-4">
                            <div class="tw-w-12 tw-h-12 tw-bg-white tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-text-orange-600 tw-shadow-sm">
                                <i class="fa fa-info-circle tw-text-xl"></i>
                            </div>
                            <div>
                                <div class="tw-text-[11px] tw-font-black tw-text-orange-400 tw-uppercase tw-tracking-widest">Service Item Details</div>
                                <div class="tw-text-sm tw-font-bold tw-text-slate-900">${data.description}</div>
                                <div class="tw-text-xs tw-text-orange-500 tw-font-bold tw-mt-0.5">${data.vender_name || 'Global Provider'}</div>
                            </div>
                        </div>

                        <div class="tw-space-y-4">
                            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                                <div class="tw-space-y-1.5">
                                    <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Performance Start</label>
                                    <div class="tw-relative">
                                        <input type="date" name="date" class="tw-w-full tw-px-4 tw-py-2.5 tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-font-medium focus:tw-ring-2 focus:tw-ring-orange-500/20" value="${data.service_date || ''}" required>
                                    </div>
                                </div>
                                <div class="tw-space-y-1.5">
                                    <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Performance End</label>
                                    <div class="tw-relative">
                                        <input type="date" name="end_date" class="tw-w-full tw-px-4 tw-py-2.5 tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-font-medium focus:tw-ring-2 focus:tw-ring-orange-500/20" value="${data.service_end_date || ''}">
                                    </div>
                                </div>
                            </div>

                            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                                <div class="tw-space-y-1.5">
                                    <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Total Net Cost (JOD)</label>
                                    <div class="tw-relative">
                                        <input type="number" id="edit_cost" name="cost" value="${data.cost}" step="0.01" class="tw-w-full tw-px-4 tw-py-2.5 tw-bg-orange-50/50 tw-border tw-border-orange-200 tw-rounded-xl tw-text-sm tw-font-black tw-text-orange-700 focus:tw-ring-2 focus:tw-ring-orange-500/20" required>
                                    </div>
                                </div>
                                <div class="tw-space-y-1.5">
                                    <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Service Time</label>
                                    <div class="tw-relative">
                                        <input type="time" name="time" value="${data.service_time || ''}" class="tw-w-full tw-px-4 tw-py-2.5 tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-font-medium focus:tw-ring-2 focus:tw-ring-slate-500/10">
                                    </div>
                                </div>
                            </div>

                            <div class="tw-grid tw-grid-cols-3 tw-gap-4">
                                <div class="tw-space-y-1.5">
                                    <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Qty</label>
                                    <input type="number" name="qty" value="${data.qty}" min="1" class="tw-w-full tw-px-4 tw-py-2.5 tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-font-bold">
                                </div>
                                <div class="tw-space-y-1.5">
                                    <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Duration</label>
                                    <input type="number" name="duration" value="${data.duration}" min="1" class="tw-w-full tw-px-4 tw-py-2.5 tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-font-bold">
                                </div>
                                <div class="tw-space-y-1.5">
                                    <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Progress</label>
                                    <select name="status" class="tw-w-full tw-px-4 tw-py-2.5 tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-font-bold tw-appearance-none">
                                        ${Object.entries(statusOptions).map(([key, label]) => `
                                            <option value="${key}" ${data.status == key ? 'selected' : ''}>${label}</option>
                                        `).join('')}
                                    </select>
                                </div>
                            </div>

                            <div class="tw-space-y-1.5">
                                <label class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest tw-ml-1">Administrative Remarks</label>
                                <textarea name="remarks" placeholder="Optional notes regarding this cost item..." class="tw-w-full tw-px-4 tw-py-3 tw-bg-slate-50 tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-h-24 tw-resize-none">${data.remarks || ''}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Fixed Footer --}}
                    <div class="tw-pt-6 tw-mt-4 tw-border-t tw-border-slate-100 tw-flex tw-gap-4">
                        <a href="#close" class="tw-flex-1 tw-py-3.5 tw-bg-slate-100 tw-text-slate-600 tw-rounded-xl tw-text-[11px] tw-font-black tw-text-center tw-no-underline tw-transition-all hover:tw-bg-slate-200 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-justify-center">Cancel Changes</a>
                        <button type="button" onclick="updateExpense(${data.id});" class="tw-flex-[2] tw-py-3.5 tw-bg-orange-600 tw-text-white tw-rounded-xl tw-text-[11px] tw-font-black tw-shadow-xl tw-shadow-orange-200 hover:tw-bg-orange-700 hover:tw-translate-y-[-2px] tw-transition-all tw-uppercase tw-tracking-wider">
                            Save Updated Record
                        </button>
                    </div>
                </form>`;

            content.innerHTML = html;
        });
}

function updateExpense(expenseId) {
    const form = document.getElementById('edit_expense_form');
    const formData = new FormData(form);
    formData.append('_method', 'PUT');

    fetch(`/admin/expenses/${expenseId}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('edit-expense-content').innerHTML = `
                <div class="tw-py-12 tw-text-center">
                    <div class="tw-w-20 tw-h-20 tw-bg-emerald-50 tw-text-emerald-500 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-3xl tw-mb-6 tw-mx-auto">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <h4 class="tw-text-xl tw-font-black tw-text-slate-900">Updated Successfully</h4>
                    <p class="tw-text-sm tw-text-slate-400 tw-mt-2">The operational cost record was modified.</p>
                    <a href="/admin/expenses?invoice=${invoiceId}" class="tw-mt-8 tw-inline-block tw-px-8 tw-py-3 tw-bg-slate-900 tw-text-white tw-rounded-xl tw-text-sm tw-font-bold tw-no-underline">Close & Refresh</a>
                </div>`;
            refreshSummaryCards(data.totals);
        } else {
            alert('Error updating: ' + (data.message || 'Check your input.'));
        }
    });
}

function showHistory(expenseId) {
    window.location.hash = 'expense_history';
    const content = document.getElementById('history-content');
    content.innerHTML = '<div class="tw-py-20 tw-text-center"><i class="fa fa-spinner fa-spin tw-text-4xl tw-text-slate-300"></i></div>';

    fetch(`/admin/expenses/${expenseId}/history`)
        .then(r => r.json())
        .then(data => {
            content.innerHTML = data.html;
        })
        .catch(err => {
            content.innerHTML = '<div class="tw-p-4 tw-bg-rose-50 tw-text-rose-600 tw-rounded-xl text-center">Failed to load history archive.</div>';
        });
}
</script>

@endsection
