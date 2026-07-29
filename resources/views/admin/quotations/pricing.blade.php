@extends('admin.layouts.app')

@section('title', 'Admin | Quotation Pricing')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    @include('admin.quotations._nav')

    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Quotation <span class="tw-text-orange-600">Pricing</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Define profit margins and commission formulas per customer type</p>
        </div>
        <a href="#add_pricing" class="btn orange !tw-px-6">
            <i class="fa fa-plus"></i> Add New Category
        </a>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-xl"></i>
        <p class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 tw-gap-6">
        @foreach($pricingList as $item)
        @php
            $isAgency = ($item->customer_type ?? 'direct') == 'agency';
            $isPercent = $item->type == 'p';
            if ($isPercent) {
                $formula = $isAgency
                    ? 'Price = Cost × (1 + '.$item->value.'%) + '.$item->commission.'% Commission'
                    : 'Price = Cost × (1 + '.$item->value.'%)';
            } else {
                $formula = $isAgency
                    ? 'Price = Cost + '.$item->value.' JOD + '.$item->commission.'% Commission'
                    : 'Price = Cost + '.$item->value.' JOD';
            }
        @endphp
        <div class="box !tw-p-6 group hover:tw-border-orange-100 tw-transition-all tw-duration-300">
            <div class="tw-flex tw-justify-between tw-items-start tw-mb-4">
                <div class="tw-flex tw-items-center tw-gap-3">
                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-flex tw-items-center tw-justify-center {{ $isAgency ? 'tw-bg-amber-50 tw-text-amber-500' : 'tw-bg-orange-50 tw-text-orange-500' }}">
                        <i class="fa {{ $isAgency ? 'fa-handshake-o' : 'fa-user' }} tw-text-lg"></i>
                    </div>
                    <div>
                        <span class="tw-text-[10px] tw-font-black tw-uppercase tw-tracking-widest {{ $isAgency ? 'tw-text-amber-500' : 'tw-text-orange-500' }}">
                            {{ $isAgency ? 'Agency / Partner' : 'Direct Customer' }}
                        </span>
                    </div>
                </div>
                <div class="tw-flex tw-gap-2">
                    <button onclick="openEditPricing({{ $item->id }})" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-transition-all" title="Edit">
                        <i class="fa fa-edit"></i>
                    </button>
                    <a href="{{ route('admin.quotation-pricing.destroy', $item->id) }}" onclick="return confirm('Delete this pricing category?');" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all" title="Delete">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>

            <h3 class="tw-text-lg tw-font-bold tw-text-slate-900 tw-mb-1">{{ $item->description }}</h3>

            {{-- Formula Box --}}
            <div class="tw-bg-slate-900 tw-text-emerald-400 tw-text-[11px] tw-font-mono tw-font-bold tw-px-4 tw-py-3 tw-rounded-xl tw-mb-4 tw-mt-3">
                <i class="fa fa-calculator tw-mr-1 tw-text-slate-400"></i> {{ $formula }}
            </div>

            <div class="tw-flex tw-flex-col tw-gap-3">
                <div class="tw-flex tw-items-center tw-justify-between tw-p-3 tw-bg-slate-50 tw-rounded-xl tw-border tw-border-slate-100">
                    <div class="tw-flex tw-flex-col">
                        <span class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Pricing Model</span>
                        <span class="tw-text-xs tw-font-bold tw-text-slate-700">
                            {{ $isPercent ? 'Percentage Based' : 'Fixed Per Person' }}
                        </span>
                    </div>
                    <span class="tw-text-lg tw-font-black {{ $isPercent ? 'tw-text-orange-600' : 'tw-text-emerald-600' }}">
                        {{ $item->value }}{{ $isPercent ? '%' : ' JOD' }}
                    </span>
                </div>

                <div class="tw-flex tw-gap-3">
                    <div class="tw-flex-1 tw-flex tw-flex-col tw-gap-1 tw-p-3 tw-bg-white tw-border tw-border-slate-100 tw-rounded-xl">
                        <span class="tw-text-[10px] tw-font-black tw-text-slate-400 tw-uppercase">Min Profit</span>
                        <span class="tw-text-sm tw-font-bold tw-text-slate-700">{{ number_format($item->min_profit, 2) }} <small class="tw-text-[11px] tw-text-slate-400">JOD</small></span>
                    </div>
                    @if($isAgency)
                    <div class="tw-flex-1 tw-flex tw-flex-col tw-gap-1 tw-p-3 tw-bg-amber-50 tw-border tw-border-amber-100 tw-rounded-xl">
                        <span class="tw-text-[10px] tw-font-black tw-text-amber-500 tw-uppercase">Commission</span>
                        <span class="tw-text-sm tw-font-bold tw-text-amber-700">{{ $item->commission }}%</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Add New Pricing Modal --}}
<div class="modal" id="add_pricing">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[520px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-orange-400"></i> New Pricing Category
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        
        <form method="POST" action="{{ route('admin.quotation-pricing.store') }}" class="tw-p-8 tw-flex tw-flex-col tw-gap-5">
            @csrf
            <div class="tw-flex tw-flex-col tw-gap-2">
                <label>Description <span class="tw-text-slate-400 tw-text-xs">(e.g. Evaneos, Direct Client)</span></label>
                <input type="text" name="description" placeholder="e.g. Evaneos" maxlength="150" required>
            </div>
            
            <div class="tw-flex tw-flex-col tw-gap-2">
                <label>Customer Type</label>
                <select name="customer_type" required>
                    <option value="">Select type...</option>
                    <option value="direct">Direct Customer — simpler formula (no commission)</option>
                    <option value="agency">Agency / Partner — includes commission on top</option>
                </select>
            </div>

            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Pricing Strategy</label>
                    <select name="type" required>
                        <option value="">Select...</option>
                        <option value="p">Percentage (%) — multiply cost</option>
                        <option value="f">Fixed Amount (JOD) — add fixed margin</option>
                    </select>
                </div>
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Margin / Value</label>
                    <input type="number" name="value" step="0.01" placeholder="e.g. 20 or 70" required>
                </div>
            </div>

            <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Min Profit (per person JOD)</label>
                    <input type="number" name="min_profit" step="0.01" placeholder="e.g. 50" required>
                </div>
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label>Agent Commission (%) <span class="tw-text-slate-400 tw-text-xs">agency only</span></label>
                    <input type="number" name="commission" step="0.01" placeholder="0.00">
                </div>
            </div>

            {{-- Live formula preview --}}
            <div id="formula_preview" class="tw-bg-slate-900 tw-text-emerald-400 tw-text-[11px] tw-font-mono tw-font-bold tw-px-4 tw-py-3 tw-rounded-xl tw-hidden">
                <i class="fa fa-calculator tw-mr-1 tw-text-slate-400"></i> <span id="formula_text">—</span>
            </div>
            
            <div class="tw-pt-4">
                <button type="submit" class="btn orange !tw-w-full !tw-py-4">
                    <i class="fa fa-check-circle"></i> Create Pricing Category
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Pricing Modal --}}
<div class="modal" id="edit_pricing">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[520px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-edit tw-text-orange-400"></i> Edit Pricing
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <div id="edit_pricing_content" class="tw-p-8">
            <div class="tw-flex tw-flex-col tw-items-center tw-py-12 tw-gap-4">
                <i class="fa fa-spinner fa-spin tw-text-4xl tw-text-orange-500"></i>
                <span class="tw-text-sm tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Loading Details...</span>
            </div>
        </div>
    </div>
</div>

<script>
function openEditPricing(id) {
    document.getElementById('edit_pricing_content').innerHTML = `
        <div class="tw-flex tw-flex-col tw-items-center tw-py-12 tw-gap-4">
            <i class="fa fa-spinner fa-spin tw-text-4xl tw-text-orange-500"></i>
            <span class="tw-text-sm tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Loading...</span>
        </div>
    `;
    window.location.hash = 'edit_pricing';
    fetch('{{ url("admin/quotation-pricing") }}/' + id + '/edit')
        .then(r => r.json())
        .then(data => {
            if(data.html) {
                document.getElementById('edit_pricing_content').innerHTML = data.html;
            }
        })
        .catch(err => {
            document.getElementById('edit_pricing_content').innerHTML = `
                <div class="tw-bg-rose-50 tw-text-rose-600 tw-p-4 tw-rounded-xl tw-text-xs tw-font-bold">
                    Error loading data. Please try again.
                </div>
            `;
        });
}

// Live formula preview in Add modal
function updateFormulaPreview() {
    var type = document.querySelector('select[name="type"]') ? document.querySelector('select[name="type"]').value : '';
    var value = document.querySelector('input[name="value"]') ? document.querySelector('input[name="value"]').value : '';
    var comm = document.querySelector('input[name="commission"]') ? document.querySelector('input[name="commission"]').value : '0';
    var ctype = document.querySelector('select[name="customer_type"]') ? document.querySelector('select[name="customer_type"]').value : '';
    var preview = document.getElementById('formula_preview');
    var text = document.getElementById('formula_text');
    if (!type || !value || !ctype) { if(preview) preview.classList.add('tw-hidden'); return; }
    var formula = '';
    if (type === 'p') {
        formula = ctype === 'agency'
            ? 'Price = Cost × (1 + ' + value + '%) + ' + (comm || 0) + '% Commission'
            : 'Price = Cost × (1 + ' + value + '%)';
    } else {
        formula = ctype === 'agency'
            ? 'Price = Cost + ' + value + ' JOD + ' + (comm || 0) + '% Commission'
            : 'Price = Cost + ' + value + ' JOD';
    }
    if(text) text.textContent = formula;
    if(preview) preview.classList.remove('tw-hidden');
}

document.addEventListener('change', updateFormulaPreview);
document.addEventListener('input', updateFormulaPreview);
</script>

@if($errors->any())
<script>window.location.hash='add_pricing';</script>
@endif
@endsection
