@extends('admin.layouts.app')

@section('title', 'Admin | Payment Gateways')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
                <span class="tw-w-12 tw-h-12 tw-bg-blue-50 tw-text-blue-600 tw-rounded-2xl tw-inline-flex tw-items-center tw-justify-center tw-mr-2">
                    <i class="fa fa-credit-card"></i>
                </span>
                Payment <span class="tw-text-indigo-600">Gateways</span>
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure and manage payment processing integrations</p>
        </div>
    </div>

    {{-- Gateways List --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-bold tw-text-slate-700">Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-bold tw-text-slate-700">Status</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-bold tw-text-slate-700">Action</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-bold tw-text-slate-700">Edit</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @foreach($gateways as $key => $status)
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-800">{{ $gatewayNames[$key] ?? $key }}</td>
                        <td class="tw-py-4 tw-px-6">
                            @if($status === 1)
                                <i class="fa fa-check tw-text-emerald-500 tw-font-bold tw-text-lg" title="Active"></i>
                            @else
                                <i class="fa fa-times tw-text-rose-600 tw-font-bold tw-text-lg" title="Disabled"></i>
                            @endif
                        </td>
                        <td class="tw-py-4 tw-px-6">
                            @if($status === 1)
                                <a href="{{ route('admin.settings.payments') }}?disable={{ $key }}" class="btn red tw-text-xs !tw-py-1.5 !tw-px-4 tw-shadow-md tw-shadow-rose-100">
                                    <i class="fa fa-times"></i> Disable
                                </a>
                            @else
                                <a href="{{ route('admin.settings.payments') }}?enable={{ $key }}" class="btn green tw-text-xs !tw-py-1.5 !tw-px-4 tw-shadow-md tw-shadow-emerald-100">
                                    <i class="fa fa-check"></i> Enable
                                </a>
                            @endif
                        </td>
                        <td class="tw-py-4 tw-px-6">
                            <a class="btn blue tw-text-xs !tw-py-1.5 !tw-px-4 tw-shadow-md tw-shadow-blue-100" href="#modal_{{ $key }}">
                                <i class="fa fa-edit"></i> edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Modal CSS */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 50;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal:target {
        display: flex;
    }
</style>

{{-- Migs Modal --}}
<div class="modal" id="modal_migs">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[600px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-credit-card tw-text-indigo-400"></i> MIGS Configuration
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.settings.payments.update', 'migs') }}" class="tw-p-8">
            @csrf
            <div class="tw-space-y-5">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Merchant ID</label>
                    <input type="text" name="mid" value="{{ $configs['migs']['mid'] ?? '' }}">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Access Code</label>
                    <input type="text" name="access_code" value="{{ $configs['migs']['access_code'] ?? '' }}">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Secret Hash</label>
                    <input type="text" name="secret_hash" value="{{ $configs['migs']['secret_hash'] ?? '' }}">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Extra Fee (%)</label>
                    <input type="text" name="extra_fee" value="{{ $configs['migs']['handle_fee'] ?? '' }}" placeholder="0">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Test Mode</label>
                    <label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer">
                        <input type="checkbox" name="test_mode" value="1" {{ ($configs['migs']['test_mode'] ?? 0) == 1 ? 'checked' : '' }} class="tw-w-4 tw-h-4 tw-rounded tw-border-slate-300 tw-text-indigo-600">
                        <span class="tw-text-xs tw-text-slate-500">Enable sandbox testing</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save MIGS Settings</button>
        </form>
    </div>
</div>

{{-- Paypal Modal --}}
<div class="modal" id="modal_paypal">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[600px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-paypal tw-text-blue-400"></i> PayPal Configuration
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.settings.payments.update', 'paypal') }}" class="tw-p-8">
            @csrf
            <div class="tw-space-y-5">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">PayPal ID</label>
                    <input type="text" name="payee_id" value="{{ $configs['paypal']['id'] ?? '' }}">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Sandbox Mode</label>
                    <label class="tw-flex tw-items-center tw-gap-2 tw-cursor-pointer">
                        <input type="checkbox" name="sand_box" value="1" {{ ($configs['paypal']['sand_box'] ?? 0) == 1 ? 'checked' : '' }} class="tw-w-4 tw-h-4 tw-rounded tw-border-slate-300 tw-text-indigo-600">
                        <span class="tw-text-xs tw-text-slate-500">Enable sandbox testing</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save PayPal Settings</button>
        </form>
    </div>
</div>

{{-- Offline Payments Modal --}}
<div class="modal" id="modal_offline_payments">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[700px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-university tw-text-amber-400"></i> Offline Payments
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.settings.payments.update', 'offline_payments') }}" class="tw-p-8">
            @csrf
            <div class="tw-space-y-5">
                @foreach($langs as $L)
                <div>
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Offline Payment Details ({{ strtoupper($L) }})</label>
                    <textarea name="details{{ $L }}" rows="4" class="tw-w-full">{{ $offlineDetails[$L] ?? '' }}</textarea>
                </div>
                @endforeach
            </div>
            <button type="submit" class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save Offline Settings</button>
        </form>
    </div>
</div>

{{-- PayTabs Modal --}}
<div class="modal" id="modal_paytabs">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[600px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-credit-card tw-text-emerald-400"></i> PayTabs Configuration
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.settings.payments.update', 'paytabs') }}" class="tw-p-8">
            @csrf
            <div class="tw-space-y-5">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Profile ID</label>
                    <input type="text" name="profile_id" value="{{ $configs['paytabs']['profile_id'] ?? '' }}">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Server Key</label>
                    <input type="text" name="server_key" value="{{ $configs['paytabs']['server_key'] ?? '' }}">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Client Key</label>
                    <input type="text" name="client_key" value="{{ $configs['paytabs']['client_key'] ?? '' }}">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">URL</label>
                    <input type="text" name="url" value="{{ $configs['paytabs']['url'] ?? '' }}">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Extra Fee (%)</label>
                    <input type="text" name="extra_fee" value="{{ $configs['paytabs']['handle_fee'] ?? '' }}" placeholder="0">
                </div>
            </div>
            <button type="submit" class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save PayTabs Settings</button>
        </form>
    </div>
</div>

@endsection
