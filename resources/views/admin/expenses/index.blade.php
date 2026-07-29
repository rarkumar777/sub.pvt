@extends('admin.layouts.app')
@section('title', 'Admin | Expenses')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">
    
    {{-- Header --}}
    <div class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4">
        <div>
            <h1 class="tw-text-3xl tw-font-black tw-text-orange-900 tw-flex tw-items-center tw-gap-3">
                <div class="tw-w-10 tw-h-10 tw-bg-rose-500 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-rose-200">
                    <i class="fa fa-money tw-text-white tw-text-base"></i>
                </div>
                Global Expenses
            </h1>
            <p class="subtitle">Showing operational cost records matching your specific filters.</p>
        </div>
        <div>
            <a href="{{ route('admin.expenses.mark-all-completed') }}" class="btn tw-bg-orange-500 tw-text-white tw-shadow-sm hover:tw-bg-orange-600 tw-font-bold tw-no-underline" onclick="return confirm('Mark all visible expenses as completed?');">
                <i class="fa fa-check-circle tw-mr-1"></i> Mark All Completed
            </a>
        </div>
    </div>

    {{-- Filter Widget --}}
    <div class="box !tw-p-5 !tw-mb-0">
        <form method="get" action="{{ route('admin.expenses.index') }}" class="tw-flex tw-flex-wrap tw-items-end tw-gap-3">
            <div class="tw-flex-1 tw-min-w-[200px]">
                <input type="text" name="search" placeholder="Search description, vendor, remarks..." value="{{ request('search') }}" class="tw-w-full">
            </div>
            <div>
                <select name="status" class="tw-min-w-[130px]">
                    <option value="">All Statuses</option>
                    @foreach($statusesList as $sKey => $sLabel)
                        <option value="{{ $sKey }}" {{ request('status') == $sKey ? 'selected' : '' }}>{{ $sLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="country" class="tw-min-w-[130px]">
                    <option value="">All Countries</option>
                    @foreach($countriesList as $c)
                        <option value="{{ $c->lang_id }}" {{ request('country') == $c->lang_id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="vender" class="tw-min-w-[130px]">
                    <option value="">All Vendors</option>
                    @foreach($vendorsList as $v)
                        <option value="{{ $v->id }}" {{ request('vender') == $v->id ? 'selected' : '' }}>{{ $v->first_name }} {{ $v->last_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="tw-flex tw-gap-2">
                <button type="submit" class="btn" style="background:#f59e0b;color:white;">
                    <i class="fa fa-filter tw-mr-1"></i> Apply
                </button>
                <a href="{{ route('admin.expenses.index') }}" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-white tw-border tw-border-slate-200 tw-text-slate-400 hover:tw-text-slate-600 tw-transition-colors tw-no-underline" title="Clear Filters">
                    <i class="fa fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Main Data Table --}}
    <div class="box !tw-p-0 tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/80 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Description</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Cost/Pay</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Vendor/Status</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Timeline</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Added By</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($expenses as $exp)
                    @php
                        $sLabels = ['pen'=>'Pending','inp'=>'Progress','com'=>'Completed','con'=>'Confirmed','can'=>'Cancelled'];
                        $sColors = ['pen'=>'tw-text-amber-600','inp'=>'tw-text-orange-600','com'=>'tw-text-orange-600','con'=>'tw-text-orange-600','can'=>'tw-text-rose-600'];
                        $countryName = '';
                        if ($exp->service && $exp->service->serviceCategory) {
                            $cid = $exp->service->serviceCategory->country_id ?? null;
                            if ($cid && isset($countriesLookup[$cid])) { $countryName = $countriesLookup[$cid]; }
                        }
                    @endphp
                    <tr class="hover:tw-bg-orange-50/30 tw-transition-colors">
                        {{-- DESCRIPTION --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-flex tw-items-start tw-gap-3">
                                <span class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-1 tw-rounded-md tw-text-[10px] tw-font-black tw-uppercase tw-shrink-0" style="background:#f59e0b;color:white;">QTY {{ $exp->qty ?: 1 }}</span>
                                <div>
                                    <div class="tw-text-sm tw-font-semibold tw-text-slate-800">{{ optional($exp->service)->description ?: $exp->remarks }}</div>
                                    <div class="tw-flex tw-items-center tw-gap-2 tw-mt-1.5">
                                        @if($countryName)
                                        <span class="tw-inline-flex tw-items-center tw-px-2 tw-py-0.5 tw-rounded tw-text-[10px] tw-font-bold" style="background:#fef3c7;color:#d97706;">{{ $countryName }}</span>
                                        @endif
                                        <span class="tw-text-xs tw-text-slate-400 tw-font-mono">#{{ $exp->id }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        {{-- COST/PAY --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-font-bold tw-text-slate-800">{{ number_format($exp->cost, 2) }} JOD</div>
                            <div class="tw-text-xs tw-font-semibold tw-mt-1 {{ $exp->payment_status == 'c' ? 'tw-text-orange-500' : 'tw-text-rose-500' }}">
                                {{ $exp->payment_status == 'c' ? 'Paid' : 'Unpaid' }}
                            </div>
                        </td>
                        {{-- VENDOR/STATUS --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-text-sm tw-font-semibold tw-text-slate-700">{{ optional($exp->venderUser)->company ?: optional($exp->venderUser)->full_name }}</div>
                            <div class="tw-text-xs tw-font-semibold tw-mt-1 {{ $sColors[$exp->status] ?? 'tw-text-slate-400' }}">
                                {{ $sLabels[$exp->status] ?? $exp->status }}
                            </div>
                        </td>
                        {{-- TIMELINE --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-text-sm tw-text-slate-600 tw-font-medium">
                                {{ $exp->service_date ? date('d M, y', strtotime($exp->service_date)) : ($exp->time ? date('d M, y', $exp->time) : 'N/A') }}
                            </div>
                        </td>
                        {{-- ADDED BY --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-text-sm tw-font-semibold tw-text-slate-700">{{ optional($exp->addedByUser)->full_name ?: 'System' }}</div>
                            <div class="tw-text-xs tw-text-slate-400 tw-mt-0.5">N/A</div>
                        </td>
                        {{-- ACTIONS --}}
                        <td class="tw-py-4 tw-px-4">
                            <div class="tw-flex tw-items-center tw-justify-end tw-gap-1.5">
                                <a href="{{ route('admin.expenses.edit', $exp->id) }}" class="action-btn tw-bg-orange-50 tw-text-orange-500 hover:tw-bg-orange-500 hover:tw-text-white hover:tw-shadow-lg hover:tw-shadow-orange-200" title="Edit Expense">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                @if(auth()->user()->hasPermission('delete_expense'))
                                <form action="{{ route('admin.expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Delete this expense?');" class="tw-m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn tw-bg-rose-50 tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white hover:tw-shadow-lg hover:tw-shadow-rose-200" title="Delete Expense">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </form>
                                @endif
                                @if($exp->invoice_id)
                                <a href="{{ route('admin.expenses.index', ['invoice' => $exp->invoice_id]) }}" class="action-btn tw-bg-orange-50 tw-text-orange-500 hover:tw-bg-orange-500 hover:tw-text-white hover:tw-shadow-lg hover:tw-shadow-orange-200" title="View Invoice Expenses">
                                    <i class="fa fa-list-alt"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-300">
                                    <i class="fa fa-money tw-text-3xl"></i>
                                </div>
                                <div>
                                    <p class="tw-text-slate-600 tw-font-bold tw-text-base">No expenses recorded</p>
                                    <p class="tw-text-slate-400 tw-text-xs tw-mt-1">No matching expense records found.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($expenses->hasPages())
        <div class="tw-px-6 tw-py-4 tw-border-t tw-border-slate-100 tw-bg-slate-50/30">
            {{ $expenses->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* Action Buttons */
    .action-btn {
        width: 34px; height: 34px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 10px; font-size: 13px;
        text-decoration: none; border: none; cursor: pointer;
        transition: all 0.25s ease;
    }
    /* Pagination Overrides */
    .pagination { display: flex; list-style: none; gap: 6px; justify-content: flex-end; align-items: center; margin: 0; padding: 0; }
    .pagination li { display: inline-block; }
    .pagination li span, .pagination li a {
        display: flex; align-items: center; justify-content: center;
        min-width: 36px; height: 36px; border-radius: 10px; font-size: 13px; font-weight: 700;
        text-decoration: none; border: 1px solid #f1f5f9; background: #fff; color: #475569;
        padding: 0 10px; transition: all 0.2s;
    }
    .pagination li.active span {
        background: var(--brand-primary); color: #fff; border-color: var(--brand-primary);
        box-shadow: 0 4px 10px rgba(234, 88, 12, 0.25);
    }
    .pagination li a:hover { background: #f8fafc; border-color: #e2e8f0; color: var(--brand-primary); }
    .pagination li.disabled span { opacity: 0.4; cursor: not-allowed; }
</style>
@endsection
