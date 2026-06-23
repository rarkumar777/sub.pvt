@extends('admin.layouts.app')

@section('title', 'Admin | Tour Types')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    
    {{-- Header Section --}}
    <div class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4 tw-bg-white tw-p-8 tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-bold tw-text-orange-400 tw-uppercase tw-tracking-widest tw-mb-3">
                <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-600 tw-transition-colors tw-no-underline">Dashboard</a>
                <span class="tw-opacity-50">/</span>
                <span class="tw-text-slate-500">Settings</span>
            </div>
            <h1 class="tw-text-4xl tw-font-black tw-text-slate-900 tw-flex tw-items-center tw-gap-4 tw-tracking-tight">
                <div class="tw-w-14 tw-h-14 tw-bg-gradient-to-br tw-from-orange-500 tw-to-purple-600 tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-orange-200">
                    <i class="fa fa-th-large tw-text-white tw-text-2xl"></i>
                </div>
                Tour Types
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-3 tw-text-sm max-w-2xl">Define and manage multilingual tour style categories like Private or Group tours. Beautiful, material-inspired colorful cards for easy management.</p>
        </div>
        <div>
            <a href="#add_new" class="tw-inline-flex tw-items-center tw-gap-2 tw-bg-slate-900 hover:tw-bg-orange-600 tw-text-white tw-px-6 tw-py-3.5 tw-rounded-xl tw-font-bold tw-text-sm tw-whitespace-nowrap tw-transition-all tw-shadow-xl hover:tw-shadow-orange-200 hover:-tw-translate-y-0.5 tw-no-underline">
                <i class="fa fa-plus-circle tw-text-lg"></i> Add New Type
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="tw-bg-orange-50 tw-border-l-4 tw-border-orange-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-4 tw-shadow-sm">
        <div class="tw-w-10 tw-h-10 tw-bg-white tw-rounded-full tw-flex tw-items-center tw-justify-center tw-shadow-sm">
            <i class="fa fa-check tw-text-orange-500 tw-text-xl"></i>
        </div>
        <div>
            <p class="tw-text-orange-800 tw-font-black tw-text-sm tw-uppercase tw-tracking-wide">Success</p>
            <p class="tw-text-orange-600 tw-font-medium tw-text-sm">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    {{-- Types Listing Material Grid --}}
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 xl:tw-grid-cols-4 tw-gap-6 tw-mt-2">
        @forelse($types as $t)
            @php
                $bgColors = [
                    'tw-bg-orange-50 tw-text-orange-600 tw-border-orange-100',
                    'tw-bg-rose-50 tw-text-rose-600 tw-border-rose-100',
                    'tw-bg-orange-50 tw-text-orange-600 tw-border-orange-100',
                    'tw-bg-amber-50 tw-text-amber-600 tw-border-amber-100',
                    'tw-bg-purple-50 tw-text-purple-600 tw-border-purple-100',
                    'tw-bg-orange-50 tw-text-orange-600 tw-border-orange-100',
                ];
                $topBorders = [
                    'tw-bg-orange-500', 'tw-bg-rose-500', 'tw-bg-orange-500', 
                    'tw-bg-amber-500', 'tw-bg-purple-500', 'tw-bg-orange-500'
                ];
                $icons = ['fa-map-signs', 'fa-compass', 'fa-globe', 'fa-umbrella', 'fa-camera', 'fa-binoculars'];
                $themeIndex = $loop->index % 6;
                $bgClass = $bgColors[$themeIndex];
                $topBorder = $topBorders[$themeIndex];
                $icon = $icons[$themeIndex];
            @endphp
            <div class="tw-bg-white tw-flex tw-flex-col tw-justify-between tw-rounded-3xl tw-shadow-sm hover:tw-shadow-xl tw-transition-all tw-duration-300 tw-overflow-hidden tw-relative tw-border tw-border-slate-100 hover:-tw-translate-y-1">
                <div>
                    <!-- Colorful Top Bar -->
                    <div class="tw-h-1.5 tw-w-full {{ $topBorder }}"></div>
                    
                    <div class="tw-p-6">
                        <!-- Icon & ID -->
                        <div class="tw-flex tw-justify-between tw-items-start tw-mb-4">
                            <div class="tw-w-12 tw-h-12 tw-rounded-2xl {{ $bgClass }} tw-flex tw-items-center tw-justify-center tw-border group-hover:tw-scale-110 tw-transition-transform tw-duration-300">
                                <i class="fa {{ $icon }} tw-text-xl"></i>
                            </div>
                            <span class="tw-inline-flex tw-items-center tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-slate-50 tw-text-slate-400 tw-text-[10px] tw-font-black tw-border tw-border-slate-100">
                                ID: {{ $t->lang_id }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="tw-text-xl tw-font-black tw-text-slate-800 tw-leading-tight group-hover:tw-text-orange-600 tw-transition-colors">
                            {{ $t->name }}
                        </h3>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="tw-bg-slate-50/50 tw-border-t tw-border-slate-100 tw-p-4 tw-flex tw-items-center tw-justify-between tw-gap-2">
                    <a target="_blank" href="{{ url('/en/tours/?type=' . $t->lang_id) }}" class="tw-whitespace-nowrap tw-text-[11px] tw-font-bold tw-text-orange-600 tw-bg-orange-50 hover:tw-bg-orange-600 hover:tw-text-white tw-px-3 tw-py-2 tw-rounded-xl tw-border tw-border-orange-100 hover:tw-border-orange-600 tw-transition-colors tw-no-underline">
                        <i class="fa fa-external-link tw-mr-1"></i> Public View
                    </a>

                    <div class="tw-flex tw-items-center tw-gap-2">
                        <a href="javascript:void(0);" onclick="do_ajax('#ajax','{{ url('admin/tour-types/' . $t->lang_id . '/edit-ajax') }}','#edit_t'); return false;" class="tw-w-8 tw-h-8 tw-rounded-xl tw-bg-orange-50 tw-border tw-border-orange-100 tw-text-orange-500 hover:tw-bg-orange-500 hover:tw-text-white tw-flex tw-items-center tw-justify-center tw-transition-colors" title="Edit">
                            <i class="fa fa-pencil tw-text-xs"></i>
                        </a>
                        <a href="{{ route('admin.tour-types.destroy', $t->lang_id) }}" onclick="return confirm('Confirm delete type: {{ $t->name }}?');" class="tw-w-8 tw-h-8 tw-rounded-xl tw-bg-rose-50 tw-border tw-border-rose-100 tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white tw-flex tw-items-center tw-justify-center tw-transition-colors" title="Delete">
                            <i class="fa fa-trash-o tw-text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="tw-col-span-full tw-py-20 tw-text-center tw-bg-white tw-rounded-3xl tw-border tw-border-slate-100 tw-border-dashed">
                <div class="tw-w-20 tw-h-20 tw-mx-auto tw-bg-slate-50 tw-rounded-3xl tw-flex tw-items-center tw-justify-center tw-text-slate-300 tw-mb-4">
                    <i class="fa fa-th-large tw-text-4xl"></i>
                </div>
                <h3 class="tw-text-xl tw-font-black tw-text-slate-800">No tour types found</h3>
                <p class="tw-text-slate-500 tw-font-medium tw-mt-2">Define tour types like Private, Group, or Luxury to get started.</p>
                <a href="#add_new" class="tw-inline-block tw-mt-6 tw-bg-orange-50 tw-text-orange-600 tw-font-bold tw-px-6 tw-py-2.5 tw-rounded-xl hover:tw-bg-orange-600 hover:tw-text-white tw-transition-colors tw-no-underline">
                    Create First Type
                </a>
            </div>
        @endforelse
    </div>
</div>

{{-- Add New Type Modal (Cleaned) --}}
<div class="modal" id="add_new">
    <div class="tw-w-full tw-max-w-3xl !tw-p-8 sm:!tw-p-10 !tw-rounded-[2rem] tw-bg-white/95 tw-backdrop-blur-xl tw-border tw-border-slate-100 tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-mb-8">
            <div class="tw-flex tw-items-center tw-gap-4">
                <span class="tw-w-14 tw-h-14 tw-bg-gradient-to-br tw-from-orange-500 tw-to-purple-600 tw-text-white tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-orange-200">
                    <i class="fa fa-plus-circle tw-text-2xl"></i>
                </span>
                <div>
                    <h3 class="tw-text-2xl tw-font-black tw-text-slate-900 tw-mb-0 tw-tracking-tight">New Tour Type</h3>
                    <p class="tw-text-slate-500 tw-text-sm tw-font-medium tw-mt-1">Add localized names for the new type.</p>
                </div>
            </div>
            <a href="#close" class="tw-w-10 tw-h-10 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-slate-50 tw-text-slate-400 hover:tw-bg-rose-50 hover:tw-text-rose-500 tw-transition-all tw-no-underline">
                <i class="fa fa-times tw-text-lg"></i>
            </a>
        </div>
        
        <form method="POST" action="{{ route('admin.tour-types.store') }}" class="tw-flex tw-flex-col tw-gap-6">
            @csrf
            <input type="hidden" name="add_new" value="1">
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-5 tw-mb-4">
                @foreach(['en' => 'English', 'fr' => 'French', 'it' => 'Italian', 'es' => 'Spanish', 'Ar' => 'Arabic', 'ge' => 'German', 'pt' => 'Portuguese'] as $code => $label)
                <div class="tw-flex tw-flex-col tw-gap-1.5 tw-bg-slate-50/50 tw-p-3 tw-rounded-2xl tw-border tw-border-slate-100">
                    <label class="tw-text-[10px] tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider tw-ml-2">
                        {{ $label }} {!! $code == 'en' ? '<span class="tw-text-rose-500">*</span>' : '' !!}
                    </label>
                    <input type="text" name="type_{{ $code }}" {{ $code == 'en' ? 'required' : '' }} placeholder="Enter {{ strtolower($label) }} name..." class="tw-w-full tw-bg-white tw-border-none tw-shadow-sm tw-rounded-xl focus:tw-ring-2 focus:tw-ring-orange-100 !tw-py-2.5">
                </div>
                @endforeach
            </div>
            
            <div class="tw-mt-4 tw-pt-6 tw-border-t tw-border-slate-100 tw-flex tw-items-center tw-justify-end tw-gap-3">
                <a href="#close" class="tw-px-6 tw-py-3 tw-rounded-xl tw-font-bold tw-text-slate-500 hover:tw-bg-slate-100 tw-transition-colors tw-no-underline">Cancel</a>
                <button type="submit" class="tw-bg-orange-600 hover:tw-bg-orange-700 tw-text-white tw-px-8 tw-py-3 tw-rounded-xl tw-font-bold tw-shadow-lg tw-shadow-orange-200 tw-transition-all hover:-tw-translate-y-0.5">
                    Save Tour Type
                </button>
            </div>
        </form>
    </div>
</div>

<div id="ajax"></div>
@endsection
