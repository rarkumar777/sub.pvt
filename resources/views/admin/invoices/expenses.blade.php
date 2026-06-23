@extends('admin.layouts.app')

@section('title', 'Admin | Invoice #' . $invoice->id . ' Expenses')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumbs & Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-mb-2">
                <a href="{{ route('admin.invoices.index') }}" class="hover:tw-text-orange-600 tw-transition-colors">Invoices</a>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="hover:tw-text-orange-600 tw-transition-colors">Invoice #{{ $invoice->id }}</a>
                <i class="fa fa-angle-right"></i>
                <span class="tw-text-orange-900">Expenses</span>
            </div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-orange-900 tw-tracking-tight">Invoice <span class="tw-text-orange-600">Expenses</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Manage operational costs associated with Invoice #{{ $invoice->id }}</p>
        </div>
        <div class="tw-flex tw-gap-3">
            <a href="#add_expense" class="btn amber !tw-px-6">
                <i class="fa fa-plus-circle"></i> Add New Expense
            </a>
            <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn red !tw-px-6">
                <i class="fa fa-arrow-left"></i> Back to Invoice
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="tw-bg-orange-50 tw-border-l-4 tw-border-orange-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-orange-500 tw-text-xl"></i>
        <p class="tw-text-orange-800 tw-font-bold tw-text-sm">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Expenses Listing Card --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Description & Context</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Timeline</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Cost</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Statuses</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($invoice->expenses as $expense)
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-flex-col">
                                <span class="tw-text-sm tw-font-bold tw-text-slate-700">{{ $expense->remarks }}</span>
                                <div class="tw-flex tw-items-center tw-gap-2 tw-mt-1">
                                    <span class="tw-text-[11px] tw-text-slate-400 tw-font-medium">Added by: {{ $expense->addedByUser->first_name ?? 'System' }}</span>
                                    @if($expense->service)
                                    <span class="tw-text-[11px] tw-px-1.5 tw-py-0.5 tw-bg-orange-50 tw-text-orange-600 tw-rounded tw-font-black tw-uppercase">{{ $expense->service->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-flex-col">
                                <span class="tw-text-xs tw-font-bold tw-text-slate-600">{{ $expense->service_date }}</span>
                                @if($expense->service_time)
                                <span class="tw-text-[11px] tw-text-slate-400 tw-mt-1"><i class="fa fa-clock-o"></i> {{ $expense->service_time }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <span class="tw-text-sm tw-font-black tw-text-orange-900">{{ number_format($expense->cost, 2) }} <span class="tw-text-[11px] tw-opacity-50">JOD</span></span>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-flex-col tw-gap-1.5">
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <span class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-w-14">Payment:</span>
                                    @php
                                        $pStatusClass = 'tw-bg-slate-100 tw-text-slate-600';
                                        if($expense->payment_status == 'completed' || $expense->payment_status == 'confirmed') $pStatusClass = 'tw-bg-orange-50 tw-text-orange-600 tw-border-orange-100';
                                        elseif($expense->payment_status == 'pending') $pStatusClass = 'tw-bg-amber-50 tw-text-amber-600 tw-border-amber-100';
                                        elseif($expense->payment_status == 'cancelled') $pStatusClass = 'tw-bg-rose-50 tw-text-rose-600 tw-border-rose-100';
                                    @endphp
                                    <span class="tw-px-2 tw-py-0.5 {{ $pStatusClass }} tw-text-[11px] tw-font-black tw-rounded tw-border tw-uppercase">{{ $expense->payment_status ?: 'N/A' }}</span>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <span class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-w-14">Overall:</span>
                                    @php
                                        $sStatusClass = 'tw-bg-slate-100 tw-text-slate-600';
                                        if($expense->status == 'completed' || $expense->status == 'confirmed') $sStatusClass = 'tw-bg-orange-50 tw-text-orange-600 tw-border-orange-100';
                                        elseif($expense->status == 'pending') $sStatusClass = 'tw-bg-amber-50 tw-text-amber-600 tw-border-amber-100';
                                        elseif($expense->status == 'cancelled') $sStatusClass = 'tw-bg-rose-50 tw-text-rose-600 tw-border-rose-100';
                                    @endphp
                                    <span class="tw-px-2 tw-py-0.5 {{ $sStatusClass }} tw-text-[11px] tw-font-black tw-rounded tw-border tw-uppercase">{{ $expense->status ?: 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-text-right">
                            <div class="tw-flex tw-justify-end tw-items-center tw-gap-2">
                                <a href="javascript:void(0);" onclick="do_ajax('#ajax','{{ route('admin.invoices.expenses.edit-form', [$invoice->id, $expense->id]) }}','#edit_expense');" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-amber-100 tw-text-amber-600 hover:tw-bg-amber-500 hover:tw-text-white tw-transition-all" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.invoices.expenses.delete', [$invoice->id, $expense->id]) }}" onclick="return confirm('Delete this expense?');" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all" title="Delete">
                                    <i class="fa fa-trash-o"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-rounded-full tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-300">
                                    <i class="fa fa-money tw-text-3xl"></i>
                                </div>
                                <div class="tw-max-w-[200px]">
                                    <p class="tw-text-slate-600 tw-font-bold">No Expenses Logged</p>
                                    <p class="tw-text-slate-400 tw-text-xs tw-mt-1">Operational costs for this invoice will appear here.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add New Expense Modal --}}
<div class="modal" id="add_expense">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[500px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-orange-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-orange-400"></i> New Expense
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        
        <form method="POST" action="{{ route('admin.invoices.expenses.store', $invoice->id) }}" class="tw-p-8 tw-flex tw-flex-col tw-gap-6">
            @csrf
            
            <div class="tw-flex tw-flex-col tw-gap-2">
                <label>Description / Remarks</label>
                <textarea name="description" placeholder="What was this cost for?" required rows="3"></textarea>
            </div>

            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Service Date</label>
                    <input type="text" name="date" class="datepicker" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Cost (JOD)</label>
                    <input type="number" step="0.01" name="cost" placeholder="0.00" required>
                </div>
            </div>

            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Payment Status</label>
                    <select name="payment_status">
                        <option value="">Select...</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Overall Status</label>
                    <select name="status">
                        <option value="">Select...</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="tw-pt-6 tw-border-t tw-border-slate-50">
                <button type="submit" class="btn amber !tw-w-full !tw-py-4">
                    <i class="fa fa-check-circle"></i> Save Expense Record
                </button>
            </div>
        </form>
    </div>
</div>

<div id="ajax"></div>
@endsection
