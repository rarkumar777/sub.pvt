@extends('admin.layouts.app')
@section('title', 'Admin | Customer Ledger')
@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumbs & Header --}}
    <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-items-start md:tw-items-center tw-gap-4">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-black tw-text-orange-500 tw-uppercase tw-tracking-[0.2em] tw-mb-2">
                <span class="tw-text-slate-400">Management</span>
                <i class="fa fa-angle-right tw-text-slate-300"></i>
                <span class="tw-text-orange-900">Customers Ledger</span>
            </div>
            <h1 class="tw-text-4xl tw-font-black tw-text-orange-900 tw-tracking-tight tw-flex tw-items-center tw-gap-3">
                <span class="tw-w-1.5 tw-h-8 tw-bg-orange-600 tw-rounded-full"></span>
                Customer <span class="tw-text-orange-600">Accounts</span>
            </h1>
            <p class="tw-text-sm tw-text-slate-500 tw-font-medium tw-mt-1">Manage and monitor balances for partners, agencies, and direct clients.</p>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="tw-bg-white tw-rounded-xl tw-border-t-4 tw-border-t-orange-500 tw-border tw-border-slate-200 tw-shadow-[0_8px_30px_rgb(0,0,0,0.04)] tw-mb-6">
        <div class="tw-px-6 tw-py-5 tw-border-b tw-border-slate-100">
            <h3 class="tw-text-sm tw-font-black tw-text-orange-900 tw-uppercase tw-tracking-widest tw-flex tw-items-center">
                <i class="fa fa-filter tw-mr-2 tw-text-orange-500 tw-text-lg"></i> Filter Records
            </h3>
        </div>
        <div class="tw-p-6">
            <form action="{{ url()->current() }}" method="GET" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-6 tw-items-end">
                
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label class="tw-text-[11px] tw-font-bold tw-text-orange-600 tw-uppercase tw-tracking-wider">Country Vector</label>
                    <select name="country" class="tw-w-full tw-h-11 tw-px-3 tw-text-sm tw-font-semibold tw-text-slate-700 tw-bg-slate-50 tw-border tw-border-slate-200 focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-100 tw-rounded-xl tw-shadow-sm outline-none tw-transition-all">
                        <option value="">Consolidated View</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->lang_id }}" {{ request('country') == $c->lang_id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="tw-flex tw-flex-col tw-gap-2 lg:tw-col-span-1">
                    <label class="tw-text-[11px] tw-font-bold tw-text-orange-600 tw-uppercase tw-tracking-wider">Branch/Agency Name</label>
                    <input type="text" name="company" value="{{ request('company') }}" placeholder="Search company..." class="tw-w-full tw-h-11 tw-px-4 tw-text-sm tw-font-semibold tw-text-slate-700 tw-bg-slate-50 tw-border tw-border-slate-200 focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-100 tw-rounded-xl tw-shadow-sm outline-none tw-transition-all">
                </div>

                <div class="tw-flex tw-flex-col tw-gap-2 lg:tw-col-span-1">
                    <label class="tw-text-[11px] tw-font-bold tw-text-orange-600 tw-uppercase tw-tracking-wider">Email Details</label>
                    <input type="text" name="email" value="{{ request('email') }}" placeholder="Contact Email..." class="tw-w-full tw-h-11 tw-px-4 tw-text-sm tw-font-semibold tw-text-slate-700 tw-bg-slate-50 tw-border tw-border-slate-200 focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-100 tw-rounded-xl tw-shadow-sm outline-none tw-transition-all">
                </div>

                <div class="tw-flex tw-items-center tw-justify-end tw-gap-3 lg:tw-col-span-1">
                    <a href="{{ url()->current() }}" class="tw-h-11 tw-px-5 tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 tw-text-sm tw-font-bold hover:tw-bg-rose-500 hover:tw-text-white tw-flex tw-items-center tw-justify-center tw-transition-all outline-none">
                        Reset
                    </a>
                    <button type="submit" class="tw-h-11 tw-px-6 tw-rounded-xl tw-bg-orange-600 tw-text-white tw-text-sm tw-font-bold hover:tw-bg-orange-700 tw-shadow-lg tw-shadow-orange-600/30 tw-flex tw-items-center tw-justify-center tw-transition-all outline-none">
                        <i class="fa fa-filter tw-mr-2"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    @php
        $themeGradients = [
            'tw-from-orange-500 tw-to-orange-600',
            'tw-from-orange-400 tw-to-teal-600',
            'tw-from-rose-400 tw-to-red-500',
            'tw-from-violet-500 tw-to-purple-600',
            'tw-from-amber-400 tw-to-orange-500',
            'tw-from-cyan-400 tw-to-orange-500',
            'tw-from-fuchsia-400 tw-to-pink-600'
        ];
    @endphp

    {{-- Main Table --}}
    <div class="tw-bg-white tw-rounded-xl tw-overflow-hidden tw-shadow-[0_8px_30px_rgb(0,0,0,0.04)] tw-border tw-border-slate-200">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50 tw-border-b tw-border-slate-200">
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Account Name</th>
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Type / Contact</th>
                        <th class="tw-px-6 tw-py-4 tw-text-right tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Outstanding Balance</th>
                        <th class="tw-px-6 tw-py-4 tw-text-right tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest tw-w-0 tw-whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-100">
                    {{-- Direct Customers Group (Miscellaneous) --}}
                    @if($miscStats)
                    <tr class="hover:tw-bg-orange-50/30 tw-transition-colors tw-bg-orange-50/10">
                        <td class="tw-px-6 tw-py-4">
                            <div class="tw-flex tw-items-center tw-gap-4">
                                <div class="tw-relative tw-shrink-0">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-gradient-to-br tw-from-slate-600 tw-to-slate-800 tw-text-white tw-flex tw-items-center tw-justify-center tw-font-black tw-text-[13px] tw-shadow-md">
                                        <i class="fa fa-users"></i>
                                    </div>
                                </div>
                                <div class="tw-flex tw-flex-col tw-justify-center">
                                    <span class="tw-text-sm tw-font-black tw-text-slate-800">Direct Customers</span>
                                    <div class="tw-flex tw-items-center tw-text-xs tw-text-slate-500 tw-mt-0.5">
                                        <span class="tw-font-medium">{{ $miscStats->total_clients }} Clients</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="tw-px-6 tw-py-4">
                            <div class="tw-flex tw-flex-col">
                                <span class="tw-text-[13px] tw-text-orange-900 tw-font-bold"><i class="fa fa-folder-open tw-text-slate-400 tw-mr-1"></i> Miscellaneous Accounts</span>
                            </div>
                        </td>
                        <td class="tw-px-6 tw-py-4 tw-text-right">
                            @php $miscBal = $miscStats->total_billed - $miscStats->total_paid; @endphp
                            @if($miscBal > 0)
                                <span class="tw-inline-flex tw-flex-col tw-items-end tw-justify-center">
                                    <span class="tw-text-sm tw-font-black tw-text-rose-600">{{ number_format($miscBal, 2) }}</span>
                                    <span class="tw-text-[9px] tw-font-black tw-text-rose-400 tw-uppercase tw-tracking-widest tw-bg-rose-50 tw-px-2 tw-py-0.5 tw-rounded tw-mt-1">JOD Pending</span>
                                </span>
                            @else
                                <span class="tw-inline-flex tw-flex-col tw-items-end tw-justify-center">
                                    <span class="tw-text-sm tw-font-black tw-text-orange-500">0.00</span>
                                    <span class="tw-text-[9px] tw-font-black tw-text-orange-400 tw-uppercase tw-tracking-widest tw-bg-orange-50 tw-px-2 tw-py-0.5 tw-rounded tw-mt-1">Cleared</span>
                                </span>
                            @endif
                        </td>
                        <td class="tw-px-6 tw-py-4 tw-text-right tw-whitespace-nowrap">
                            <button type="button" onclick="openCustomerAccount('misc');" class="tw-px-4 tw-py-2 tw-rounded-lg tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-text-[11px] tw-font-black tw-uppercase tw-tracking-wider tw-transition-colors outline-none tw-shadow-sm tw-border tw-border-orange-100">
                                View Ledger
                            </button>
                        </td>
                    </tr>
                    @endif

                    {{-- Partner Agencies --}}
                    @forelse($partners as $p)
                    @php
                        $gradClass = $themeGradients[$p->id % count($themeGradients)];
                        $initials = strtoupper(substr(trim($p->company ?: $p->first_name), 0, 2));
                    @endphp
                    <tr class="hover:tw-bg-slate-50 tw-transition-colors">
                        <td class="tw-px-6 tw-py-4">
                            <div class="tw-flex tw-items-center tw-gap-4">
                                <div class="tw-relative tw-shrink-0">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-gradient-to-br {{ $gradClass }} tw-text-white tw-flex tw-items-center tw-justify-center tw-font-black tw-text-[13px] tw-shadow-md">
                                        {{ $initials ?: '--' }}
                                    </div>
                                </div>
                                <div class="tw-flex tw-flex-col tw-justify-center">
                                    <span class="tw-text-sm tw-font-black tw-text-slate-800">{{ html_entity_decode($p->company ?: ($p->first_name . ' ' . $p->last_name), ENT_QUOTES, 'UTF-8') }}</span>
                                    <div class="tw-flex tw-items-center tw-text-xs tw-text-slate-500 tw-mt-0.5">
                                        <span class="tw-font-bold tw-text-orange-400">#{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        <span class="tw-mx-2 tw-text-slate-300">&bull;</span>
                                        <span class="tw-font-medium">{{ $p->first_name }} {{ $p->last_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="tw-px-6 tw-py-4">
                            <div class="tw-flex tw-flex-col">
                                <span class="tw-text-[13px] tw-text-orange-900 tw-font-bold"><i class="fa fa-envelope tw-text-orange-400 tw-mr-1"></i> {{ $p->email }}</span>
                                <div class="tw-flex tw-items-center tw-text-[11px] tw-font-semibold tw-text-slate-500 tw-mt-1">
                                    <i class="fa fa-map-marker tw-text-rose-400 tw-mr-1"></i>
                                    <span>{{ $p->city ?: 'HQ' }}, {{ $p->country ?: 'Global' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="tw-px-6 tw-py-4 tw-text-right">
                            @php $bal = $p->total_billed - $p->total_paid; @endphp
                            @if($bal > 0)
                                <span class="tw-inline-flex tw-flex-col tw-items-end tw-justify-center">
                                    <span class="tw-text-sm tw-font-black tw-text-rose-600">{{ number_format($bal, 2) }}</span>
                                    <span class="tw-text-[9px] tw-font-black tw-text-rose-400 tw-uppercase tw-tracking-widest tw-bg-rose-50 tw-px-2 tw-py-0.5 tw-rounded tw-mt-1">JOD Pending</span>
                                </span>
                            @else
                                <span class="tw-inline-flex tw-flex-col tw-items-end tw-justify-center">
                                    <span class="tw-text-sm tw-font-black tw-text-orange-500">0.00</span>
                                    <span class="tw-text-[9px] tw-font-black tw-text-orange-400 tw-uppercase tw-tracking-widest tw-bg-orange-50 tw-px-2 tw-py-0.5 tw-rounded tw-mt-1">Cleared</span>
                                </span>
                            @endif
                        </td>
                        <td class="tw-px-6 tw-py-4 tw-text-right tw-whitespace-nowrap">
                            <button type="button" onclick="openCustomerAccount({{ $p->id }});" class="tw-px-4 tw-py-2 tw-rounded-lg tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-text-[11px] tw-font-black tw-uppercase tw-tracking-wider tw-transition-colors outline-none tw-shadow-sm tw-border tw-border-orange-100">
                                View Ledger
                            </button>
                        </td>
                    </tr>
                    @empty
                    @if(!$miscStats)
                    <tr>
                        <td colspan="4" class="tw-py-12 tw-text-center">
                            <i class="fa fa-users tw-text-5xl tw-text-orange-100 tw-mb-3"></i>
                            <h3 class="tw-mt-2 tw-text-sm tw-font-black tw-text-slate-800 tw-uppercase tw-tracking-widest">No accounts found</h3>
                            <p class="tw-mt-1 tw-text-sm tw-font-medium tw-text-slate-500">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                    @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="tw-flex tw-justify-center tw-mt-4">
        <div class="tw-bg-white tw-px-6 tw-py-3 tw-rounded-xl tw-shadow-sm tw-border tw-border-slate-200">
            {{ $partners->links() }}
        </div>
    </div>
</div>

<!-- Customer Account Modal -->
<div id="customer_account_modal" class="modal">
    <div class="tw-max-w-7xl tw-w-[95%] !tw-p-0 !tw-bg-transparent">
        <div class="tw-relative">
            <div class="modal-close tw-absolute tw-top-6 tw-right-8 tw-z-50">
                <a href="#close" class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-white/10 tw-text-white hover:tw-bg-rose-500 tw-transition-all tw-flex tw-items-center tw-justify-center tw-backdrop-blur-md">
                    <i class="fa fa-times"></i>
                </a>
            </div>
            <div id="customer_account_content" class="tw-bg-white tw-rounded-3xl tw-overflow-hidden tw-shadow-2xl"></div>
        </div>
    </div>
</div>

<script>
function openCustomerAccount(id) {
    $("#customer_account_content").html('<div class="d-pad align-center tw-py-20"><i class="fa-spinner fa-spin fa-3x tw-text-orange-500"></i></div>');
    window.location.hash = 'customer_account_modal';
    
    $.ajax({
        url: "{{ url('admin/customers/ledger') }}/" + id,
        type: "GET",
        success: function(response) {
            $("#customer_account_content").html(response.html);
        },
        error: function() {
            $("#customer_account_content").html('<div class="d-pad align-center red-text tw-py-10">Error loading account details.</div>');
        }
    });
}

function applyCustomerAccountFilter(id) {
    var formData = $("#customer_account_filter").serialize();
    $("#customer_account_content").html('<div class="d-pad align-center tw-py-20"><i class="fa-spinner fa-spin fa-3x tw-text-orange-500"></i></div>');
    
    $.ajax({
        url: "{{ url('admin/customers/ledger') }}/" + id + "?" + formData,
        type: "GET",
        success: function(response) {
            $("#customer_account_content").html(response.html);
        },
        error: function() {
            $("#customer_account_content").html('<div class="d-pad align-center red-text tw-py-10">Error loading account details.</div>');
        }
    });
}
</script>
@endsection
