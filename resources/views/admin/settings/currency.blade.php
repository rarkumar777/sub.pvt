@extends('admin.layouts.app')
@section('title', 'Admin | Currency')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    @include('admin.settings._nav')
    
    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
                <span class="tw-w-12 tw-h-12 tw-bg-amber-50 tw-text-amber-600 tw-rounded-2xl tw-inline-flex tw-items-center tw-justify-center tw-mr-2">
                    <i class="fa fa-money"></i>
                </span>
                Manage <span class="tw-text-indigo-600">Currencies</span>
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure exchange rates and currency display</p>
        </div>
        <button type="button" onclick="document.getElementById('add_currency_modal').style.display='flex';" class="btn indigo">
            <i class="fa fa-plus"></i> Add Currency
        </button>
    </div>

    {{-- Currency Table --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Currency</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Symbol</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Rate</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @foreach($currencies as $currency)
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-amber-50 tw-text-amber-600 tw-flex tw-items-center tw-justify-center tw-text-xs tw-font-bold">
                                    <i class="fa fa-money"></i>
                                </div>
                                <span class="tw-font-bold tw-text-slate-900 tw-text-sm">{{ $currency->name }}</span>
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <code class="tw-text-xs tw-bg-slate-100 tw-px-2.5 tw-py-1 tw-rounded-lg tw-text-slate-600 tw-font-bold">{{ $currency->symbol }}</code>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <span class="tw-text-sm tw-font-bold tw-text-indigo-600">{{ number_format($currency->rate, 3) }}</span>
                            @if($currency->rate == 1.000)
                                <span class="tw-ml-2 tw-px-2 tw-py-0.5 tw-rounded-full tw-bg-emerald-50 tw-text-emerald-600 tw-text-[11px] tw-font-bold">Base</span>
                            @endif
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-text-right">
                            <div class="tw-flex tw-justify-end tw-items-center tw-gap-2">
                                <button type="button" onclick="editCurrency({{ $currency->id }}, '{{ addslashes($currency->name) }}', '{{ addslashes($currency->symbol) }}', '{{ $currency->rate }}')" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-600 hover:tw-bg-indigo-600 hover:tw-text-white tw-transition-all tw-border-none tw-cursor-pointer" title="Edit">
                                    <i class="fa fa-edit tw-text-xs"></i>
                                </button>
                                @if($currency->rate != 1.000)
                                <form action="{{ route('admin.settings.currency.delete', $currency->id) }}" method="POST" class="tw-inline" onsubmit="return confirm('Delete {{ addslashes($currency->name) }}?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-border-none tw-cursor-pointer" title="Delete">
                                        <i class="fa fa-trash tw-text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Currency Modal --}}
<div id="add_currency_modal" class="tw-fixed tw-inset-0 tw-bg-black/40 tw-z-50 tw-items-center tw-justify-center tw-backdrop-blur-sm" style="display:none;">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden tw-w-[500px] tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-emerald-400"></i> Add New Currency
            </h3>
            <a href="javascript:void(0);" onclick="document.getElementById('add_currency_modal').style.display='none';" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.settings.currency.store') }}" class="tw-p-8">
            @csrf
            <div class="tw-space-y-5">
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Name</label>
                    <input type="text" name="name" required placeholder="e.g. US Dollar">
                </div>
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Symbol</label>
                    <input type="text" name="symbol" required placeholder="e.g. USD">
                </div>
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Exchange Rate</label>
                    <input type="text" name="rate" required placeholder="e.g. 1.413">
                </div>
            </div>
            <div class="tw-flex tw-justify-end tw-gap-3 tw-mt-8">
                <a href="javascript:void(0);" onclick="document.getElementById('add_currency_modal').style.display='none';" class="btn red">Cancel</a>
                <button type="submit" class="btn indigo"><i class="fa fa-check"></i> Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Currency Modal --}}
<div id="edit_currency_modal" class="tw-fixed tw-inset-0 tw-bg-black/40 tw-z-50 tw-items-center tw-justify-center tw-backdrop-blur-sm" style="display:none;">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden tw-w-[500px] tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-edit tw-text-indigo-400"></i> Edit Currency
            </h3>
            <a href="javascript:void(0);" onclick="document.getElementById('edit_currency_modal').style.display='none';" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" id="edit_currency_form" class="tw-p-8">
            @csrf @method('PUT')
            <div class="tw-space-y-5">
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Name</label>
                    <input type="text" name="name" id="edit_currency_name" required>
                </div>
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Symbol</label>
                    <input type="text" name="symbol" id="edit_currency_symbol" required>
                </div>
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Exchange Rate</label>
                    <input type="text" name="rate" id="edit_currency_rate" required>
                </div>
            </div>
            <div class="tw-flex tw-justify-end tw-gap-3 tw-mt-8">
                <a href="javascript:void(0);" onclick="document.getElementById('edit_currency_modal').style.display='none';" class="btn red">Cancel</a>
                <button type="submit" class="btn indigo"><i class="fa fa-check"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editCurrency(id, name, symbol, rate) {
    document.getElementById('edit_currency_name').value = name;
    document.getElementById('edit_currency_symbol').value = symbol;
    document.getElementById('edit_currency_rate').value = rate;
    document.getElementById('edit_currency_form').action = '{{ url("admin/settings/currency") }}/' + id;
    document.getElementById('edit_currency_modal').style.display = 'flex';
}
</script>
@endpush
