@extends('admin.layouts.app')

@section('title', 'Permission Denied')

@section('content')
<div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-py-20 tw-gap-6">
    <div class="tw-w-24 tw-h-24 tw-rounded-full tw-bg-rose-50 tw-flex tw-items-center tw-justify-center tw-text-rose-400">
        <i class="fa fa-lock tw-text-5xl"></i>
    </div>
    <div class="tw-text-center">
        <h2 class="tw-text-2xl tw-font-extrabold tw-text-slate-900">Access Denied</h2>
        <p class="tw-text-slate-500 tw-font-medium tw-mt-2 tw-max-w-md">You do not have permission to access this area. Please contact your administrator if you believe this is an error.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn indigo tw-mt-2">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>
</div>
@endsection
