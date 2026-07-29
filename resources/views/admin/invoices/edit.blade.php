@extends('admin.layouts.app')

@section('title', 'Admin | Edit Invoice #' . $invoice->id)

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumbs & Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-mb-2">
                <a href="{{ route('admin.invoices.index') }}" class="hover:tw-text-orange-600 tw-transition-colors">Invoices</a>
                <i class="fa fa-angle-right"></i>
                <span class="tw-text-orange-900">Edit Invoice #{{ $invoice->id }}</span>
            </div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-orange-900 tw-tracking-tight">Edit <span class="tw-text-orange-600">Invoice</span></h1>
        </div>
        <div class="tw-flex tw-gap-3">
            <a href="{{ route('admin.invoices.expenses', $invoice->id) }}" class="btn amber !tw-px-6">
                <i class="fa fa-usd"></i> Manage Expenses
            </a>
            <a href="{{ route('admin.invoices.index') }}" class="btn red !tw-px-6">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(!empty($invoice->module))
    <div class="tw-bg-amber-50 tw-border-l-4 tw-border-amber-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-info-circle tw-text-amber-500 tw-text-xl"></i>
        <p class="tw-text-amber-800 tw-font-bold tw-text-sm">Editing restricted: This invoice is managed by the {{ ucfirst($invoice->module) }} module.</p>
    </div>
    @endif

    @if(session('success'))
    <div class="tw-bg-orange-50 tw-border-l-4 tw-border-orange-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-orange-500 tw-text-xl"></i>
        <p class="tw-text-orange-800 tw-font-bold tw-text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.invoices.update', $invoice->id) }}" id="edit_invoice_form" class="tw-flex tw-flex-col tw-gap-8">
        @csrf
        @method('PUT')

        <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-8">
            {{-- Left Column: Client & Details --}}
            <div class="lg:tw-col-span-2 tw-flex tw-flex-col tw-gap-8">
                {{-- Basic Information Card --}}
                <div class="box">
                    <div class="tw-flex tw-items-center tw-gap-3 tw-mb-8">
                        <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-orange-50 tw-text-orange-600 tw-flex tw-items-center tw-justify-center">
                            <i class="fa fa-user"></i>
                        </div>
                        <h3 class="tw-text-lg tw-font-bold tw-text-slate-800">Client Info & Settings</h3>
                    </div>

                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label>User E-mail</label>
                            <input type="text" name="user" value="{{ $invoice->user ? $invoice->user->email : '' }}" placeholder="Required..." {{ !empty($invoice->module) ? 'disabled' : '' }}>
                        </div>

                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label>Description</label>
                            <input type="text" name="desc" value="{{ $invoice->desc }}" placeholder="Summary of services..." {{ !empty($invoice->module) ? 'disabled' : '' }}>
                        </div>

                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label>Invoice Date</label>
                            <div class="tw-relative">
                                <i class="fa fa-calendar tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400"></i>
                                <input type="text" name="date" value="{{ $invoice->date }}" class="datepicker !tw-pl-11" {{ !empty($invoice->module) ? 'disabled' : '' }}>
                            </div>
                        </div>

                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label>Due Date</label>
                            <div class="tw-relative">
                                <i class="fa fa-calendar tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400"></i>
                                <input type="text" name="due_to_date" value="{{ $invoice->due_to_date }}" class="datepicker !tw-pl-11" {{ !empty($invoice->module) ? 'disabled' : '' }}>
                            </div>
                        </div>

                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label>Notify User via Email?</label>
                            <select name="notify_user" {{ !empty($invoice->module) ? 'disabled' : '' }}>
                                <option value="0">Do not send notification</option>
                                <option value="1">Send invoice note to user</option>
                            </select>
                        </div>

                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label>Status Classification</label>
                            <select name="status" {{ !empty($invoice->module) ? 'disabled' : '' }}>
                                <option value="u" {{ $invoice->status == 'u' ? 'selected' : '' }}>Unpaid</option>
                                <option value="pa" {{ $invoice->status == 'pa' ? 'selected' : '' }}>Partly Payment</option>
                                <option value="c" {{ $invoice->status == 'c' ? 'selected' : '' }}>Paid (Completed)</option>
                                <option value="ca" {{ $invoice->status == 'ca' ? 'selected' : '' }}>Cancelled</option>
                                <option value="r" {{ $invoice->status == 'r' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Invoice Items Card --}}
                <div class="box !tw-p-0 !tw-overflow-hidden">
                    <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <div class="tw-flex tw-items-center tw-gap-3">
                            <div class="tw-w-9 tw-h-9 tw-rounded-lg tw-bg-orange-600 tw-text-white tw-flex tw-items-center tw-justify-center tw-text-sm">
                                <i class="fa fa-list"></i>
                            </div>
                            <h3 class="tw-text-base tw-font-bold tw-text-slate-800">Invoice Line Items</h3>
                        </div>
                        @if(empty($invoice->module))
                        <button type="button" id="add_new_item" class="tw-text-xs tw-font-black tw-text-orange-600 hover:tw-text-orange-800 tw-flex tw-items-center tw-gap-1.5 tw-transition-colors">
                            <i class="fa fa-plus-circle"></i> ADD NEW ITEM
                        </button>
                        @endif
                    </div>

                    <div id="items_container" class="tw-flex tw-flex-col tw-divide-y tw-divide-slate-50">
                        @php
                        $items = [];
                        if (!empty($invoice->items)) {
                            if (is_string($invoice->items)) { $items = @unserialize($invoice->items) ?: []; }
                            elseif (is_array($invoice->items)) { $items = $invoice->items; }
                        }
                        $itemTotal = 0; $c = 0;
                        @endphp

                        @foreach($items as $item)
                        @php
                        $c++;
                        $lineTotal = ($item['qty'] ?? 0) * ($item['price'] ?? 0);
                        $itemTotal += $lineTotal;
                        @endphp
                        <div class="tw-p-6 tw-grid tw-grid-cols-12 tw-gap-4 tw-items-center group" id="item_c{{ $c }}">
                            <div class="tw-col-span-12 md:tw-col-span-6">
                                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-mb-1 md:tw-hidden">Item Description</label>
                                <input type="text" name="item_{{ $c }}" value="{{ $item['name'] ?? '' }}" placeholder="Item name..." class="!tw-h-11">
                            </div>
                            <div class="tw-col-span-4 md:tw-col-span-2">
                                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-mb-1 md:tw-hidden">Qty</label>
                                <input type="number" name="item_qty_{{ $c }}" value="{{ $item['qty'] ?? '' }}" class="!tw-text-center !tw-h-11" placeholder="1">
                            </div>
                            <div class="tw-col-span-5 md:tw-col-span-3">
                                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-mb-1 md:tw-hidden">Price (JOD)</label>
                                <input type="number" step="0.01" name="item_price_{{ $c }}" value="{{ $item['price'] ?? '' }}" class="!tw-text-right !tw-h-11" placeholder="0.00">
                            </div>
                            <div class="tw-col-span-3 md:tw-col-span-1 tw-flex tw-justify-end">
                                @if(empty($invoice->module))
                                <button type="button" onclick="$('#item_c{{ $c }}').remove();" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-text-rose-400 hover:tw-text-rose-600 hover:tw-bg-rose-50 tw-transition-all">
                                    <i class="fa fa-times"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="current_count" id="current_count" value="{{ $c }}">
                    
                    @if(empty($items))
                    <div id="no_items_msg" class="tw-p-12 tw-text-center">
                        <p class="tw-text-slate-400 tw-text-sm">No items added yet. Click "Add New Item" to start billing.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Right Column: Financial Summary --}}
            <div class="tw-flex tw-flex-col tw-gap-8">
                {{-- Totals Summary Card --}}
                <div class="tw-bg-orange-900 tw-rounded-3xl tw-p-8 tw-text-white tw-shadow-2xl tw-relative tw-overflow-hidden">
                    <div class="tw-absolute tw-top-0 tw-right-0 tw-w-32 tw-h-32 tw-bg-orange-500/10 tw-rounded-full -tw-mr-16 -tw-mt-16"></div>
                    
                    <h3 class="tw-text-lg tw-font-bold tw-mb-8 tw-flex tw-items-center tw-gap-3">
                        <i class="fa fa-calculator tw-text-orange-400"></i> Payment Summary
                    </h3>

                    @php
                    $discountVal = str_replace('%', '', $invoice->discount ?? '0');
                    if (strpos($invoice->discount ?? '', '%') !== false) {
                        $discountCalc = ($discountVal / 100) * $itemTotal;
                    } else {
                        $discountCalc = floatval($discountVal);
                    }
                    $taxCalc = (floatval($invoice->tax) / 100) * ($itemTotal - $discountCalc);
                    $grandTotal = ($itemTotal - $discountCalc) + $taxCalc;
                    @endphp

                    <div class="tw-flex tw-flex-col tw-gap-5">
                        <div class="tw-flex tw-justify-between tw-items-center tw-pb-4 tw-border-b tw-border-white/5">
                            <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Subtotal</span>
                            <span class="tw-text-sm tw-font-bold">{{ number_format($itemTotal, 2) }} <span class="tw-text-[11px] tw-opacity-60">JOD</span></span>
                        </div>
                        <div class="tw-flex tw-justify-between tw-items-center tw-pb-4 tw-border-b tw-border-white/5">
                            <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Discount</span>
                            <span class="tw-text-sm tw-font-bold tw-text-rose-400">-{{ number_format($discountCalc, 2) }} <span class="tw-text-[11px] tw-opacity-60">JOD</span></span>
                        </div>
                        <div class="tw-flex tw-justify-between tw-items-center tw-pb-4 tw-border-b tw-border-white/5">
                            <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Tax ({{ $invoice->tax }}%)</span>
                            <span class="tw-text-sm tw-font-bold tw-text-orange-400">+{{ number_format($taxCalc, 2) }} <span class="tw-text-[11px] tw-opacity-60">JOD</span></span>
                        </div>
                        <div class="tw-flex tw-justify-between tw-items-center tw-pt-4">
                            <span class="tw-text-sm tw-font-black tw-text-white tw-uppercase tw-tracking-tighter">Grand Total</span>
                            <span class="tw-text-2xl tw-font-black tw-text-orange-400">{{ number_format($grandTotal, 2) }} <span class="tw-text-xs tw-opacity-60">JOD</span></span>
                        </div>
                    </div>

                    @if($invoice->total_paid > 0)
                    <div class="tw-mt-8 tw-p-4 tw-bg-white/5 tw-rounded-2xl tw-border tw-border-white/10 tw-flex tw-justify-between tw-items-center">
                        <span class="tw-text-xs tw-font-bold tw-text-slate-400">Total Paid to Date</span>
                        <span class="tw-text-sm tw-font-bold tw-text-orange-400">{{ number_format($invoice->total_paid, 2) }} JOD</span>
                    </div>
                    @endif
                </div>

                {{-- Tax & Discount Settings Card --}}
                <div class="box">
                    <h3 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-mb-6">Global Adjustments</h3>
                    
                    <div class="tw-flex tw-flex-col tw-gap-6">
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label>Tax Rate (%)</label>
                            <input type="number" name="tax" value="{{ $invoice->tax }}" placeholder="0" {{ !empty($invoice->module) ? 'disabled' : '' }}>
                        </div>
                        
                        <div class="tw-flex tw-flex-col tw-gap-2">
                            <label>Partly Payment Requirement</label>
                            <input type="number" step="0.01" name="partly_payment" value="{{ $invoice->partly_payment }}" placeholder="0.00" {{ !empty($invoice->module) ? 'disabled' : '' }}>
                        </div>

                        <div class="tw-pt-4 tw-border-t tw-border-slate-50">
                            <label class="tw-text-orange-600">Discount Configuration</label>
                            <div class="tw-space-y-4 tw-mt-2">
                                <input type="text" name="discount_description" value="{{ $invoice->discount_description }}" placeholder="Discount reason..." class="!tw-text-xs" {{ !empty($invoice->module) ? 'disabled' : '' }}>
                                <div class="tw-grid tw-grid-cols-2 tw-gap-2">
                                    <select name="discount_type" class="!tw-text-xs" {{ !empty($invoice->module) ? 'disabled' : '' }}>
                                        <option value="" {{ strpos($invoice->discount ?? '', '%') === false ? 'selected' : '' }}>Fixed (JOD)</option>
                                        <option value="%" {{ strpos($invoice->discount ?? '', '%') !== false ? 'selected' : '' }}>Percent (%)</option>
                                    </select>
                                    <input type="number" step="0.01" name="discount_amount" value="{{ str_replace('%', '', $invoice->discount ?? '0') }}" placeholder="Amount" class="!tw-text-xs" {{ !empty($invoice->module) ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(empty($invoice->module))
                <button type="submit" class="btn amber !tw-w-full !tw-py-5 tw-shadow-xl">
                    <i class="fa fa-save tw-text-xl"></i> &nbsp; Save Invoice Updates
                </button>
                @endif
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
$('#add_new_item').on('click', function(){
    $('#no_items_msg').hide();
    var c = $('#current_count').val()*1;
    c = c + 1;
    var rowHtml = `
        <div class="tw-p-6 tw-grid tw-grid-cols-12 tw-gap-4 tw-items-center tw-bg-orange-50/30 tw-animate-pulse" id="item_c${c}">
            <div class="tw-col-span-12 md:tw-col-span-6">
                <input type="text" name="item_${c}" value="" placeholder="New item description..." class="!tw-h-11">
            </div>
            <div class="tw-col-span-4 md:tw-col-span-2">
                <input type="number" name="item_qty_${c}" value="1" class="!tw-text-center !tw-h-11">
            </div>
            <div class="tw-col-span-5 md:tw-col-span-3">
                <input type="number" step="0.01" name="item_price_${c}" value="0.00" class="!tw-text-right !tw-h-11">
            </div>
            <div class="tw-col-span-3 md:tw-col-span-1 tw-flex tw-justify-end">
                <button type="button" onclick="$('#item_c${c}').remove();" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-text-rose-400 hover:tw-text-rose-600 hover:tw-bg-rose-50 tw-transition-all">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    $('#items_container').append(rowHtml);
    $('#current_count').val(c);
    
    // Remove pulse after 1s
    setTimeout(() => {
        $(`#item_c${c}`).removeClass('tw-animate-pulse');
    }, 1000);
});
</script>
@endpush
<div id="ajax"></div>
@endsection
