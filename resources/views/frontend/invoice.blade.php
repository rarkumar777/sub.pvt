@extends('frontend.layout')
@section('title', 'Invoice #' . $invoice->id)

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 sm:px-6 lg:px-8 font-sans text-slate-800">
    <div class="max-w-4xl mx-auto">
        
        {{-- Payment Success/Error Messages --}}
        @if(session('payment_success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-6 py-4 rounded-xl mb-6 shadow-sm flex items-center gap-3">
                <i class="fa fa-check-circle text-emerald-500 text-xl"></i>
                <span class="font-medium">{{ session('payment_success') }}</span>
            </div>
        @endif
        @if(session('payment_error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-6 py-4 rounded-xl mb-6 shadow-sm flex items-center gap-3">
                <i class="fa fa-times-circle text-rose-500 text-xl"></i>
                <span class="font-medium">{{ session('payment_error') }}</span>
            </div>
        @endif

        {{-- Main Invoice Box --}}
        <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-slate-100">
            
            {{-- Header Section --}}
            <div class="px-8 py-8 sm:p-12 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white relative overflow-hidden">
                <div class="flex items-center gap-6 z-10 w-full sm:w-auto mb-6 sm:mb-0">
                    <img src="/PVtravels.jpg" alt="PV Travels" class="w-32 h-auto object-contain">
                    <div class="hidden sm:block h-12 w-px bg-slate-200"></div>
                    <div>
                        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">INVOICE</h2>
                        <div class="text-slate-500 font-medium text-sm mt-1">#INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
                
                {{-- Status Pill --}}
                <div class="z-10 w-full sm:w-auto text-left sm:text-right">
                    @php
                        $badgeClasses = [
                            'success' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'error' => 'bg-rose-100 text-rose-700 border-rose-200',
                            'warning' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'info' => 'bg-blue-100 text-blue-700 border-blue-200',
                        ];
                        $badgeClass = $badgeClasses[$status['class'] ?? 'info'] ?? 'bg-slate-100 text-slate-700';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-bold border uppercase tracking-wider shadow-sm {{ $badgeClass }}">
                        @if($status['class'] == 'success')
                            <i class="fa fa-check-circle"></i>
                        @elseif($status['class'] == 'warning')
                            <i class="fa fa-clock-o"></i>
                        @else
                            <i class="fa fa-info-circle"></i>
                        @endif
                        {{ $status['label'] }}
                    </span>
                </div>
                
                {{-- Background Deco --}}
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-60 z-0"></div>
            </div>

            {{-- Info & Dates Section --}}
            <div class="px-8 py-8 sm:p-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    {{-- From --}}
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Billed From</div>
                        <div class="text-slate-900 font-bold mb-1">{{ $companyName }}</div>
                        <div class="text-sm text-slate-500 leading-relaxed">
                            {!! nl2br(e($companyAddress)) !!}<br>
                            <a href="mailto:{{ $companyEmail }}" class="text-indigo-600 hover:underline">{{ $companyEmail }}</a><br>
                            <span class="mt-2 inline-block">T: {{ $companyPhone }}</span><br>
                            F: {{ $companyFax }}
                        </div>
                    </div>

                    {{-- To --}}
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Billed To</div>
                        @if($customer)
                            @php
                                $custName = trim(($customer->last_name ?? '') . ' ' . ($customer->first_name ?? ''));
                                if(empty($custName)) $custName = $customer->name ?? '';
                            @endphp
                            <div class="text-slate-900 font-bold mb-1 capitalize">{{ $custName }}</div>
                            <div class="text-sm text-slate-500 leading-relaxed">
                                @if(!empty($customer->address))
                                    <div>{!! nl2br(e($customer->address)) !!}</div>
                                @endif
                                @if(!empty($customer->email))
                                    <a href="mailto:{{ $customer->email }}" class="text-indigo-600 hover:underline">{{ $customer->email }}</a><br>
                                @endif
                                @if(!empty($customer->phone))
                                    <span class="mt-2 inline-block">T: {{ $customer->phone }}</span>
                                @endif
                                @if(!empty($customer->fax))
                                    <br>F: {{ $customer->fax }}
                                @endif
                            </div>
                        @else
                            <div class="text-slate-900 font-bold mb-1">Guest</div>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div class="md:text-right">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 shadow-sm inline-block w-full md:w-auto text-left md:text-right">
                            <table class="w-full text-sm">
                                <tr>
                                    <td class="font-semibold text-slate-500 py-1 pr-4">Invoice No</td>
                                    <td class="font-bold text-slate-900 py-1">INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold text-slate-500 py-1 pr-4">Issue Date</td>
                                    <td class="font-bold text-slate-900 py-1">{{ date('M d, Y', strtotime($invoice->date)) }}</td>
                                </tr>
                                <tr>
                                    <td class="font-semibold text-slate-500 py-1 pr-4">Due Date</td>
                                    <td class="font-bold text-rose-600 py-1">{{ date('M d, Y', strtotime($invoice->due_to_date)) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Description Banner --}}
                <div class="mt-12 mb-8 border-l-4 border-amber-500 bg-amber-50 rounded-r-xl p-5 shadow-sm">
                    <h3 class="text-amber-700 font-bold text-lg flex items-center gap-3">
                        <i class="fa fa-map-marker text-amber-500"></i> {{ $invoice->desc }}
                    </h3>
                </div>

                {{-- Items Table --}}
                <div class="mt-8 border border-slate-200 rounded-2xl overflow-hidden shadow-sm overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider font-bold border-b border-slate-200">
                                <th class="px-6 py-4">Description</th>
                                <th class="px-6 py-4 text-center">Qty</th>
                                <th class="px-6 py-4 text-right">Unit Price</th>
                                <th class="px-6 py-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($items as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $item['name'] ?? '' }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600 text-center">{{ $item['qty'] ?? 1 }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-600 text-right">{{ number_format($item['price'] ?? 0, 2) }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-slate-900 text-right">{{ number_format($item['total'] ?? 0, 2) }} <span class="text-xs text-slate-400 font-normal">{{ $activeCurrency }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals Area --}}
                <div class="mt-8 flex flex-col md:flex-row justify-between items-end md:items-start tracking-tight">
                    
                    {{-- Discount Notes --}}
                    <div class="w-full md:w-1/2 mb-6 md:mb-0">
                        @if($discount > 0)
                            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-emerald-800 text-sm shadow-sm inline-block">
                                <div class="font-bold flex items-center gap-2 mb-1"><i class="fa fa-tag"></i> Discount Applied</div>
                                <div>{{ $invoice->discount_description ?? 'Special Discount' }}</div>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Balances --}}
                    <div class="w-full md:w-1/3 space-y-3 shrink-0">
                        @if($discount > 0)
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-semibold text-slate-500">Discount</span>
                                <span class="font-bold text-emerald-600">-{{ number_format($discount, 2) }} <span class="text-[10px] text-emerald-400">{{ $activeCurrency }}</span></span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-semibold text-slate-500">Subtotal</span>
                            <span class="font-bold text-slate-700">{{ number_format($total, 2) }} <span class="text-[10px] text-slate-400">{{ $activeCurrency }}</span></span>
                        </div>
                        
                        @if($tax > 0)
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-semibold text-slate-500">Tax</span>
                                <span class="font-bold text-slate-700">{{ number_format($tax, 2) }} <span class="text-[10px] text-slate-400">{{ $activeCurrency }}</span></span>
                            </div>
                        @endif
                        
                        <div class="border-t border-slate-200 mt-3 pt-3"></div>
                        
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-extrabold text-slate-900 uppercase">Grand Total</span>
                            <span class="font-extrabold text-indigo-700">{{ number_format($grandTotal, 2) }} <span class="text-xs text-indigo-400 align-baseline">{{ $activeCurrency }}</span></span>
                        </div>

                        @if(($invoice->total_paid ?? 0) > 0)
                            <div class="flex justify-between items-center text-sm mt-3 px-3 py-2 bg-emerald-50 rounded-lg">
                                <span class="font-bold text-emerald-700">Amount Paid</span>
                                <span class="font-bold text-emerald-700">{{ number_format($invoice->total_paid, 2) }} <span class="text-xs">{{ $activeCurrency }}</span></span>
                            </div>
                            
                            @if(($invoice->total_paid ?? 0) < ($invoice->total ?? 0))
                                <div class="flex justify-between items-center text-base mt-2 px-3 py-2 bg-rose-50 rounded-lg border border-rose-100">
                                    <span class="font-bold text-rose-800">Amount Due</span>
                                    <span class="font-bold text-rose-800">{{ number_format(($invoice->total ?? 0) - ($invoice->total_paid ?? 0), 2) }} <span class="text-xs">{{ $activeCurrency }}</span></span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
            
            {{-- Payment Area --}}
            @if($invoice->status == 'u' || $invoice->status == 'pp')
            <div class="bg-indigo-50/50 border-t border-indigo-100 px-8 py-8 sm:p-12 print-hide">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h4 class="text-lg font-extrabold text-indigo-900 flex items-center gap-2 tracking-tight">
                            <i class="fa fa-lock text-indigo-500"></i> Secure Payment
                        </h4>
                        <p class="text-sm text-indigo-700 mt-1 font-medium">Complete your payment securely via Visa or MasterCard.</p>
                        
                        @if(config('app.env') === 'local' && env('PAYTABS_TEST_MODE'))
                            <div class="mt-3 bg-amber-100 border border-amber-300 text-amber-800 text-xs px-3 py-2 rounded font-bold inline-flex items-center gap-2">
                                <i class="fa fa-warning px-1"></i> SANDBOX MODE
                            </div>
                        @endif
                    </div>
                    
                    <div class="w-full md:w-auto">
                        <form method="POST" action="{{ route('frontend.payment.initiate', ['lang' => $lang, 'id' => $invoice->id]) }}" id="payment-form" class="bg-white p-4 rounded-2xl shadow-sm border border-indigo-100 flex flex-col sm:flex-row items-center gap-6">
                            @csrf
                            <label class="flex items-center gap-3 cursor-pointer px-2 py-1 rounded-lg hover:bg-slate-50 transition-colors">
                                <input type="radio" name="payment_method" value="visa" checked class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 cursor-pointer">
                                <div class="flex items-center gap-1.5 opacity-90">
                                    <div class="bg-blue-900 text-white font-extrabold px-3 py-1 rounded text-sm tracking-wider italic">VISA</div>
                                    <i class="fa fa-cc-mastercard text-[32px] text-rose-600"></i>
                                </div>
                            </label>
                            
                            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-[0_4px_10px_rgba(79,70,229,0.3)] hover:shadow-[0_6px_15px_rgba(79,70,229,0.4)] transition-all focus:outline-none focus:ring-4 focus:ring-indigo-500/50 flex items-center justify-center gap-2 text-base" id="pay-now-btn">
                                <i class="fa fa-credit-card"></i> Pay Now
                            </button>
                        </form>
                        
                        @if(config('app.env') === 'local' && env('PAYTABS_TEST_MODE'))
                            <div class="mt-4 text-right">
                                <a href="/{{ $lang }}/invoice/{{ $invoice->id }}/simulate-success" class="text-[11px] uppercase tracking-wider font-bold text-slate-400 hover:text-slate-600 transition-colors">
                                    <i class="fa fa-bug"></i> Simulate Success
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            {{-- Print Button (Hidden in print) --}}
            <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 text-center print-hide">
                <button onclick="window.print()" class="text-sm font-bold text-slate-400 hover:text-indigo-600 transition-colors inline-flex items-center gap-2">
                    <i class="fa fa-print"></i> Print Invoice
                </button>
            </div>
        </div>
        
    </div>
</div>

<style>
    @media print {
        @page { margin: 0; size: auto; }
        body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .print-hide { display: none !important; }
        body, html { background: white !important; padding: 0 !important; margin: 0 !important; }
        .shadow-xl { shadow: none !important; box-shadow: none !important; border: none !important; border-radius: 0 !important; }
        #navbar-placeholder, #fixed-nav, #footer { display: none !important; }
        .bg-slate-50 { background: white !important; }
        .max-w-4xl { max-width: 100% !important; margin: 0 !important; }
    }
</style>

<script>
    window.addEventListener('DOMContentLoaded', function () {
        // Keep header and footer hidden so invoice takes full screen if that was the intended behavior
        var fixedNav = document.getElementById('fixed-nav');
        var navPlaceholder = document.getElementById('navbar-placeholder');
        var footer = document.getElementById('footer');
        
        if(fixedNav) fixedNav.style.display = 'none';
        if(navPlaceholder) navPlaceholder.style.display = 'none';
        if(footer) footer.style.display = 'none';

        // Prevent double-click and handle button reset
        var payForm = document.getElementById('payment-form');
        var payBtn = document.getElementById('pay-now-btn');

        if(payForm && payBtn) {
            payForm.addEventListener('submit', function() {
                payBtn.disabled = true;
                payBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
                payBtn.classList.add('opacity-75', 'cursor-not-allowed');
            });

            // Reset button if page reloads with error (failsafe)
            @if(session('payment_error'))
                payBtn.disabled = false;
                payBtn.innerHTML = '<i class="fa fa-credit-card"></i> Pay Now';
                payBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            @endif
        }
    });
</script>
@endsection
