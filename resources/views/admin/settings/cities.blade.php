@extends('admin.layouts.app')
@section('title', 'Admin | Cities')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">Manage <span class="tw-text-indigo-600">Cities</span></h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure cities for countries</p>
        </div>
        <a href="#add_city" class="btn indigo"><i class="fa fa-plus"></i> Add City</a>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-check-circle tw-text-emerald-500 tw-text-lg"></i>
        <span class="tw-text-emerald-800 tw-font-bold tw-text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-w-16">#</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Country</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($cities as $city)
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-400">{{ $city->id }}</td>
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-font-bold tw-text-slate-900">{{ $city->name }}</td>
                        <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-500">{{ $city->country->name ?? '-' }}</td>
                        <td class="tw-py-4 tw-px-6 tw-text-right">
                            <a href="#" class="tw-w-9 tw-h-9 tw-inline-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-600 hover:tw-bg-indigo-600 hover:tw-text-white tw-transition-all tw-no-underline"><i class="fa fa-edit tw-text-xs"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="tw-py-16 tw-text-center tw-text-slate-400 tw-text-sm tw-font-bold">No cities found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($cities->hasPages())<div class="tw-flex tw-justify-center">{{ $cities->links() }}</div>@endif
</div>

<div class="modal" id="add_city">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[450px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0"><i class="fa fa-plus-circle tw-text-emerald-400"></i> Add City</h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.settings.cities.store') }}" class="tw-p-8">@csrf
            <div class="tw-space-y-5">
                <div><label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Name</label><input type="text" name="name" required placeholder="City name"></div>
                <div><label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Country ID</label><input type="number" name="country_id" placeholder="Country ID"></div>
            </div>
            <button type="submit" class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save</button>
        </form>
    </div>
</div>
@endsection
