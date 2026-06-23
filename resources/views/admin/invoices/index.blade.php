@extends('admin.layouts.app')

@section('title', 'Admin | Invoices')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Header Section --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Invoices <span class="tw-text-orange-600">Management</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Track and manage client billing and payments</p>
        </div>
        <a href="{{ route('admin.invoices.create') }}" class="btn orange !tw-px-6">
            <i class="fa fa-plus"></i> Add New Invoice
        </a>
    </div>

    {{-- Filter Card --}}
    <div class="box">
        <form method="get" action="{{ route('admin.invoices.index') }}" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 lg:tw-grid-cols-4 tw-gap-4">
            <div class="tw-flex tw-flex-col tw-gap-2">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Module</label>
                <select name="module">
                    <option value="">- All Modules</option>
                    <option value="tours" {{ request('module')=='tours' ? 'selected' : '' }}>Tours</option>
                    <option value="invoices" {{ request('module')=='invoices' ? 'selected' : '' }}>General Invoices</option>
                </select>
            </div>

            <div class="tw-flex tw-flex-col tw-gap-2">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Payment Status</label>
                <select name="status">
                    <option value="">- All Statuses</option>
                    <option value="c" {{ request('status')=='c' ? 'selected' : '' }}>Paid</option>
                    <option value="pa" {{ request('status')=='pa' ? 'selected' : '' }}>Partly Paid</option>
                    <option value="u" {{ request('status')=='u' ? 'selected' : '' }}>Unpaid</option>
                    <option value="ca" {{ request('status')=='ca' ? 'selected' : '' }}>Cancelled</option>
                    <option value="r" {{ request('status')=='r' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <div class="tw-flex tw-flex-col tw-gap-2">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Invoice # / Title</label>
                <div class="tw-grid tw-grid-cols-3 tw-gap-2">
                    <input type="text" name="id" class="tw-col-span-1" placeholder="ID" value="{{ request('id') }}">
                    <input type="text" name="title" class="tw-col-span-2" placeholder="Title/Desc" value="{{ request('title') }}">
                </div>
            </div>

            <div class="tw-flex tw-flex-col tw-gap-2">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">User E-mail</label>
                <input type="text" name="user_email" placeholder="example@pvt.jo" value="{{ request('user_email') }}">
            </div>

            <div class="tw-flex tw-flex-col tw-gap-2">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Date range</label>
                <div class="tw-flex tw-items-center tw-gap-2">
                    <input type="text" name="from_date" class="datepicker !tw-text-xs" placeholder="From" value="{{ request('from_date') }}">
                    <span class="tw-text-slate-300">to</span>
                    <input type="text" name="to_date" class="datepicker !tw-text-xs" placeholder="To" value="{{ request('to_date') }}">
                </div>
            </div>

            <div class="tw-flex tw-flex-col tw-gap-2">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Due Date range</label>
                <div class="tw-flex tw-items-center tw-gap-2">
                    <input type="text" name="from_due_to_date" class="datepicker !tw-text-xs" placeholder="From" value="{{ request('from_due_to_date') }}">
                    <span class="tw-text-slate-300">to</span>
                    <input type="text" name="to_due_to_date" class="datepicker !tw-text-xs" placeholder="To" value="{{ request('to_due_to_date') }}">
                </div>
            </div>

            <div class="tw-col-span-full tw-flex tw-justify-end tw-items-end tw-gap-3 tw-mt-4">
                <a href="{{ route('admin.invoices.index') }}" class="btn red !tw-px-6">
                    <i class="fa fa-trash-o"></i> Clear Filters
                </a>
                <button type="submit" class="btn orange !tw-px-8">
                    <i class="fa fa-search"></i> Search Invoices
                </button>
            </div>
        </form>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-xl"></i>
        <p class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Invoices Table Card --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Invoice Details</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Client Info</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Title & Status</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Financials</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($invoices as $invoice)
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-flex-col">
                                <a href="{{ url('en/invoice/' . $invoice->id) }}" target="_blank" style="color: #ea580c !important;" class="tw-text-sm tw-font-bold hover:tw-underline">
                                    #{{ $invoice->id }}
                                </a>
                                <div class="tw-flex tw-items-center tw-gap-1.5 tw-mt-1">
                                    <i class="fa fa-calendar-o tw-text-[11px] tw-text-slate-400"></i>
                                    <span class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-tighter">Due: {{ $invoice->due_to_date }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-slate-100 tw-flex tw-items-center tw-justify-center tw-text-slate-400">
                                    <i class="fa fa-user-circle-o"></i>
                                </div>
                                <div class="tw-flex tw-flex-col">
                                    @if($invoice->user_id)
                                    <a href="{{ route('admin.users.edit', $invoice->user_id) }}" style="color: #ea580c !important;" class="tw-text-xs tw-font-bold hover:tw-underline" target="_blank">{{ $invoice->user->email ?? 'User: '.$invoice->user_id }}</a>
                                    @else
                                    <span class="tw-text-xs tw-font-bold tw-text-slate-700">Guest</span>
                                    @endif
                                    @if($invoice->sent_count > 0)
                                    <span class="tw-text-[11px] tw-text-emerald-500 tw-font-medium">{{ $invoice->sent_count }} Notifications Sent</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-flex-col tw-gap-2">
                                <span class="tw-text-xs tw-text-slate-600 tw-font-medium tw-leading-relaxed tw-max-w-[250px] tw-truncate">{{ str_replace('Booking > ', '', $invoice->desc) }}</span>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    @if($invoice->status == 'c')
                                    <span class="tw-px-2 tw-py-0.5 tw-bg-emerald-50 tw-text-emerald-600 tw-text-[11px] tw-font-black tw-rounded tw-border tw-border-emerald-100 tw-uppercase">Paid</span>
                                    @elseif($invoice->status == 'u')
                                    <span class="tw-px-2 tw-py-0.5 tw-bg-rose-50 tw-text-rose-600 tw-text-[11px] tw-font-black tw-rounded tw-border tw-border-rose-100 tw-uppercase">Unpaid</span>
                                    @elseif($invoice->status == 'pa')
                                    <span class="tw-px-2 tw-py-0.5 tw-bg-amber-50 tw-text-amber-600 tw-text-[11px] tw-font-black tw-rounded tw-border tw-border-amber-100 tw-uppercase">Partly</span>
                                    @elseif($invoice->status == 'ca')
                                    <span class="tw-px-2 tw-py-0.5 tw-bg-slate-100 tw-text-slate-600 tw-text-[11px] tw-font-black tw-rounded tw-border tw-border-slate-200 tw-uppercase">Cancelled</span>
                                    @elseif($invoice->status == 'r')
                                    <span class="tw-px-2 tw-py-0.5 tw-bg-blue-50 tw-text-blue-600 tw-text-[11px] tw-font-black tw-rounded tw-border tw-border-blue-100 tw-uppercase">Refunded</span>
                                    @else
                                    <span class="tw-px-2 tw-py-0.5 tw-bg-slate-50 tw-text-slate-500 tw-text-[11px] tw-font-black tw-rounded tw-border tw-border-slate-200 tw-uppercase">{{ $invoice->status }}</span>
                                    @endif

                                    <a href="javascript:void(0);" onclick="if(confirm('Send Invoice note to user?')){window.location='{{ route('admin.invoices.send', $invoice->id) }}';}" class="tw-text-[11px] tw-font-bold tw-text-orange-500 hover:tw-text-orange-700 tw-transition-colors">
                                        <i class="fa fa-paper-plane-o"></i> Send Note
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-align-middle">
                            <div class="tw-flex tw-flex-col tw-gap-1.5 tw-w-full">
                                <div class="tw-flex tw-justify-between tw-items-center tw-bg-orange-50/70 tw-py-1 tw-px-2 tw-rounded tw-border tw-border-orange-100/50" style="background-color: #fff7ed !important; border-color: #ffedd5 !important;">
                                    <span class="tw-text-[9px] tw-font-extrabold tw-text-orange-400 tw-uppercase tw-tracking-wider" style="color: #ea580c !important;">Total</span>
                                    <span class="tw-text-[13px] tw-font-bold tw-text-orange-900" style="color: #7c2d12 !important;">{{ number_format($invoice->total, 2) }} <span class="tw-text-[9px] tw-font-semibold">JOD</span></span>
                                </div>
                                @if($invoice->total_paid > 0)
                                <div class="tw-flex tw-justify-between tw-items-center tw-bg-emerald-50/70 tw-py-1 tw-px-2 tw-rounded tw-border tw-border-emerald-100/50" style="background-color: #ecfdf5 !important; border-color: #d1fae5 !important;">
                                    <span class="tw-text-[9px] tw-font-extrabold tw-text-emerald-500 tw-uppercase tw-tracking-wider" style="color: #10b981 !important;">Paid</span>
                                    <span class="tw-text-[12px] tw-font-bold tw-text-emerald-700" style="color: #047857 !important;">-{{ number_format($invoice->total_paid, 2) }} <span class="tw-text-[9px] tw-font-semibold">JOD</span></span>
                                </div>
                                <div class="tw-flex tw-justify-between tw-items-center tw-bg-rose-50/70 tw-py-1 tw-px-2 tw-rounded tw-border tw-border-rose-100/50" style="background-color: #fff1f2 !important; border-color: #ffe4e6 !important;">
                                    <span class="tw-text-[9px] tw-font-extrabold tw-text-rose-500 tw-uppercase tw-tracking-wider" style="color: #f43f5e !important;">Due</span>
                                    <span class="tw-text-[12px] tw-font-bold tw-text-rose-700" style="color: #be123c !important;">{{ number_format($invoice->total - $invoice->total_paid, 2) }} <span class="tw-text-[9px] tw-font-semibold">JOD</span></span>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-text-right">
                            <div class="tw-flex tw-justify-end tw-items-center tw-gap-2">
                                <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-amber-100 tw-text-amber-600 hover:tw-bg-amber-500 hover:tw-text-white tw-transition-all" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="javascript:void(0);" onclick="do_ajax('#ajax','{{ route('admin.invoices.transactions', $invoice->id) }}','#transations');" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-blue-50 tw-text-blue-600 hover:tw-bg-blue-500 hover:tw-text-white tw-transition-all" title="Transactions">
                                    <i class="fa fa-refresh"></i>
                                </a>
                                <a href="{{ route('admin.invoices.expenses', $invoice->id) }}" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-emerald-50 tw-text-emerald-600 hover:tw-bg-emerald-500 hover:tw-text-white tw-transition-all" title="Expenses">
                                    <i class="fa fa-usd"></i>
                                </a>
                                <a href="javascript:void(0);" onclick="if(confirm('Delete this invoice?')){ window.location='{{ route('admin.invoices.destroy', $invoice->id) }}'; }" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all" title="Delete">
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
                                    <i class="fa fa-file-text-o tw-text-3xl"></i>
                                </div>
                                <div class="tw-max-w-[200px]">
                                    <p class="tw-text-slate-600 tw-font-bold">No Invoices Found</p>
                                    <p class="tw-text-slate-400 tw-text-xs tw-mt-1">Try adjusting your filters or create a new invoice.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($invoices->hasPages())
    <div class="tw-mt-8 tw-bg-white tw-p-4 tw-rounded-2xl tw-border tw-border-slate-100 tw-shadow-sm">
        {{ $invoices->appends(request()->query())->links() }}
    </div>
    @endif

    <div id="ajax"></div>
</div>
@endsection
