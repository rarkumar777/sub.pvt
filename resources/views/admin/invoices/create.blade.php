@extends('admin.layouts.app')

@section('title', 'Admin | Create Invoice')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumbs & Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-mb-2">
                <a href="{{ route('admin.invoices.index') }}" class="hover:tw-text-orange-600 tw-transition-colors">Invoices</a>
                <i class="fa fa-angle-right"></i>
                <span class="tw-text-slate-900">Create New</span>
            </div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Create <span class="tw-text-orange-600">Invoice</span></h1>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="btn red !tw-px-6">
            <i class="fa fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if($errors->any())
    <div class="tw-flex tw-flex-col tw-gap-2">
        @foreach($errors->all() as $error)
        <div class="tw-bg-rose-50 tw-border-l-4 tw-border-rose-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3">
            <i class="fa fa-warning tw-text-rose-500"></i>
            <p class="tw-text-rose-800 tw-font-bold tw-text-sm">{{ $error }}</p>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Main Form Card --}}
    <div class="box !tw-p-8">
        <form method="POST" action="{{ route('admin.invoices.store') }}" class="tw-flex tw-flex-col tw-gap-8">
            @csrf
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-8">
                {{-- Client Selection --}}
                <div class="tw-flex tw-flex-col tw-gap-3">
                    <div class="tw-flex tw-items-center tw-gap-2">
                        <div class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-orange-50 tw-text-orange-600 tw-flex tw-items-center tw-justify-center tw-text-xs">
                            <i class="fa fa-user"></i>
                        </div>
                        <label class="tw-text-sm tw-font-bold tw-text-slate-700">Select Client / User</label>
                    </div>
                    <select name="user_id" required>
                        <option value="">Choose a user...</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    <p class="tw-text-[11px] tw-text-slate-400 tw-font-medium">The invoice will be associated with this user account.</p>
                </div>

                {{-- Invoice Description --}}
                <div class="tw-flex tw-flex-col tw-gap-3">
                    <div class="tw-flex tw-items-center tw-gap-2">
                        <div class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-blue-50 tw-text-blue-600 tw-flex tw-items-center tw-justify-center tw-text-xs">
                            <i class="fa fa-file-text-o"></i>
                        </div>
                        <label class="tw-text-sm tw-font-bold tw-text-slate-700">Short Description</label>
                    </div>
                    <input type="text" name="desc" placeholder="e.g. Tour Package Payment" required>
                    <p class="tw-text-[11px] tw-text-slate-400 tw-font-medium">A brief summary of what this invoice is for.</p>
                </div>
            </div>

            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-8">
                {{-- Invoice Date --}}
                <div class="tw-flex tw-flex-col tw-gap-3">
                    <div class="tw-flex tw-items-center tw-gap-2">
                        <div class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-emerald-50 tw-text-emerald-600 tw-flex tw-items-center tw-justify-center tw-text-xs">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <label class="tw-text-sm tw-font-bold tw-text-slate-700">Invoice Date</label>
                    </div>
                    <div class="tw-relative">
                        <i class="fa fa-calendar tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400"></i>
                        <input type="text" name="date" class="datepicker !tw-pl-12" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                {{-- Due Date --}}
                <div class="tw-flex tw-flex-col tw-gap-3">
                    <div class="tw-flex tw-items-center tw-gap-2">
                        <div class="tw-w-8 tw-h-8 tw-rounded-lg tw-bg-rose-50 tw-text-rose-600 tw-flex tw-items-center tw-justify-center tw-text-xs">
                            <i class="fa fa-hourglass-end"></i>
                        </div>
                        <label class="tw-text-sm tw-font-bold tw-text-slate-700">Due Date</label>
                    </div>
                    <div class="tw-relative">
                        <i class="fa fa-calendar tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400"></i>
                        <input type="text" name="due_to_date" class="datepicker !tw-pl-12" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                    </div>
                </div>
            </div>

            <div class="tw-pt-8 tw-border-t tw-border-slate-50 tw-flex tw-justify-end">
                <button type="submit" class="btn amber !tw-px-10 !tw-py-4">
                    <i class="fa fa-check-circle"></i> Create Invoice & Continue to Items
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
