@extends('admin.layouts.app')
@section('title', 'Admin | Countries')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    @include('admin.settings._nav')
    
    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
                <span class="tw-w-12 tw-h-12 tw-bg-emerald-50 tw-text-emerald-600 tw-rounded-2xl tw-inline-flex tw-items-center tw-justify-center tw-mr-2">
                    <i class="fa fa-globe"></i>
                </span>
                Manage <span class="tw-text-indigo-600">Countries</span>
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Configure available countries and their cities</p>
        </div>
        <button type="button" onclick="document.getElementById('add_country_modal').style.display='flex';" class="btn indigo">
            <i class="fa fa-plus"></i> Add Country
        </button>
    </div>

    {{-- Countries List --}}
    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Country Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @foreach($countries as $country)
                    <tr class="tw-group hover:tw-bg-slate-50/50 tw-transition-colors" id="country_row_{{ $country->id }}">
                        <td class="tw-py-5 tw-px-6">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <div class="tw-w-9 tw-h-9 tw-rounded-xl tw-bg-emerald-50 tw-text-emerald-600 tw-flex tw-items-center tw-justify-center tw-text-xs tw-font-bold">
                                    <i class="fa fa-globe"></i>
                                </div>
                                <span class="tw-font-bold tw-text-slate-900 tw-text-sm" id="country_name_{{ $country->id }}">{{ $country->name }}</span>
                            </div>
                        </td>
                        <td class="tw-py-5 tw-px-6 tw-text-right">
                            <div class="tw-flex tw-justify-end tw-items-center tw-gap-2">
                                <a href="{{ route('admin.settings.cities') }}?country={{ $country->id }}" class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-3 tw-py-1.5 tw-rounded-xl tw-bg-emerald-50 tw-text-emerald-600 tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-wider hover:tw-bg-emerald-600 hover:tw-text-white tw-transition-all tw-no-underline">
                                    <i class="fa fa-building-o"></i> Cities
                                </a>
                                <button type="button" onclick="editCountry({{ $country->id }}, '{{ addslashes($country->name) }}')" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-indigo-50 tw-text-indigo-600 hover:tw-bg-indigo-600 hover:tw-text-white tw-transition-all tw-border-none tw-cursor-pointer" title="Edit">
                                    <i class="fa fa-edit tw-text-xs"></i>
                                </button>
                                <form action="{{ route('admin.settings.countries.delete', $country->id) }}" method="POST" class="tw-inline" onsubmit="return confirm('Delete {{ addslashes($country->name) }}?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-border-none tw-cursor-pointer" title="Delete">
                                        <i class="fa fa-trash tw-text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($countries->hasPages())
    <div class="tw-flex tw-justify-center">
        {{ $countries->links() }}
    </div>
    @endif
</div>

{{-- Add Country Modal --}}
<div id="add_country_modal" class="tw-fixed tw-inset-0 tw-bg-black/40 tw-z-50 tw-items-center tw-justify-center tw-backdrop-blur-sm" style="display:none;">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden tw-w-[450px] tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-emerald-400"></i> Add New Country
            </h3>
            <a href="javascript:void(0);" onclick="document.getElementById('add_country_modal').style.display='none';" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.settings.countries.store') }}" class="tw-p-8">
            @csrf
            <div class="tw-mb-6">
                <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Country Name</label>
                <input type="text" name="name" required placeholder="Enter country name">
            </div>
            <div class="tw-flex tw-justify-end tw-gap-3">
                <a href="javascript:void(0);" onclick="document.getElementById('add_country_modal').style.display='none';" class="btn red">Cancel</a>
                <button type="submit" class="btn indigo"><i class="fa fa-check"></i> Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Country Modal --}}
<div id="edit_country_modal" class="tw-fixed tw-inset-0 tw-bg-black/40 tw-z-50 tw-items-center tw-justify-center tw-backdrop-blur-sm" style="display:none;">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden tw-w-[450px] tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-edit tw-text-indigo-400"></i> Edit Country
            </h3>
            <a href="javascript:void(0);" onclick="document.getElementById('edit_country_modal').style.display='none';" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form method="POST" id="edit_country_form" class="tw-p-8">
            @csrf @method('PUT')
            <div class="tw-mb-6">
                <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Country Name</label>
                <input type="text" name="name" id="edit_country_name" required>
            </div>
            <div class="tw-flex tw-justify-end tw-gap-3">
                <a href="javascript:void(0);" onclick="document.getElementById('edit_country_modal').style.display='none';" class="btn red">Cancel</a>
                <button type="submit" class="btn indigo"><i class="fa fa-check"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editCountry(id, name) {
    document.getElementById('edit_country_name').value = name;
    document.getElementById('edit_country_form').action = '{{ url("admin/settings/countries") }}/' + id;
    document.getElementById('edit_country_modal').style.display = 'flex';
}
</script>
@endpush
