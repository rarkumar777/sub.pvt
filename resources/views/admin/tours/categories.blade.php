@extends('admin.layouts.app')

@section('title', 'Admin | Tour Categories')

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
                <div class="tw-w-14 tw-h-14 tw-bg-gradient-to-br tw-from-amber-400 tw-to-orange-500 tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-amber-200">
                    <i class="fa fa-folder-open-o tw-text-white tw-text-2xl"></i>
                </div>
                Tour Categories
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-3 tw-text-sm max-w-2xl">Manage multilingual tour classification groups. Beautiful, material-inspired colorful cards for easy management.</p>
        </div>
        <div>
            <a href="#addnew" class="tw-inline-flex tw-items-center tw-gap-2 tw-bg-slate-900 hover:tw-bg-amber-500 tw-text-white tw-px-6 tw-py-3.5 tw-rounded-xl tw-font-bold tw-text-sm tw-whitespace-nowrap tw-transition-all tw-shadow-xl hover:tw-shadow-amber-200 hover:-tw-translate-y-0.5 tw-no-underline">
                <i class="fa fa-plus-circle tw-text-lg"></i> Add New Category
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

    {{-- Categories Listing Material Grid --}}
    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-3 xl:tw-grid-cols-4 tw-gap-6 tw-mt-2">
        @forelse($categories as $c)
            @php
                $bgColors = [
                    'tw-bg-amber-50 tw-text-amber-600 tw-border-amber-100',
                    'tw-bg-orange-50 tw-text-orange-600 tw-border-orange-100',
                    'tw-bg-rose-50 tw-text-rose-600 tw-border-rose-100',
                    'tw-bg-orange-50 tw-text-orange-600 tw-border-orange-100',
                    'tw-bg-purple-50 tw-text-purple-600 tw-border-purple-100',
                    'tw-bg-orange-50 tw-text-orange-600 tw-border-orange-100',
                ];
                $topBorders = [
                    'tw-bg-amber-500', 'tw-bg-orange-500', 'tw-bg-rose-500', 
                    'tw-bg-orange-500', 'tw-bg-purple-500', 'tw-bg-orange-500'
                ];
                $icons = ['fa-folder', 'fa-folder-open', 'fa-bookmark', 'fa-tags', 'fa-archive', 'fa-briefcase'];
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
                                ID: {{ $c->lang_id }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="tw-text-xl tw-font-black tw-text-slate-800 tw-leading-tight group-hover:tw-text-amber-600 tw-transition-colors">
                            {{ $c->name }}
                        </h3>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="tw-bg-slate-50/50 tw-border-t tw-border-slate-100 tw-p-4 tw-flex tw-items-center tw-justify-between tw-gap-2">
                    <a target="_blank" href="{{ url('/en/tours/?category=' . $c->lang_id) }}" class="tw-whitespace-nowrap tw-text-[11px] tw-font-bold tw-text-orange-600 tw-bg-orange-50 hover:tw-bg-orange-600 hover:tw-text-white tw-px-3 tw-py-2 tw-rounded-xl tw-border tw-border-orange-100 hover:tw-border-orange-600 tw-transition-colors tw-no-underline">
                        <i class="fa fa-external-link tw-mr-1"></i> Public View
                    </a>

                    <div class="tw-flex tw-items-center tw-gap-2">
                        <a href="javascript:void(0);" onclick="do_ajax('#ajax','{{ url('admin/tour-categories/' . $c->lang_id . '/edit-ajax') }}','#edit_t'); return false;" class="tw-w-8 tw-h-8 tw-rounded-xl tw-bg-orange-50 tw-border tw-border-orange-100 tw-text-orange-500 hover:tw-bg-orange-500 hover:tw-text-white tw-flex tw-items-center tw-justify-center tw-transition-colors" title="Edit">
                            <i class="fa fa-pencil tw-text-xs"></i>
                        </a>
                        <a href="{{ route('admin.tour-categories.destroy', $c->lang_id) }}" onclick="return confirm('Confirm delete cat: {{ $c->name }}?');" class="tw-w-8 tw-h-8 tw-rounded-xl tw-bg-rose-50 tw-border tw-border-rose-100 tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white tw-flex tw-items-center tw-justify-center tw-transition-colors" title="Delete">
                            <i class="fa fa-trash-o tw-text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="tw-col-span-full tw-py-20 tw-text-center tw-bg-white tw-rounded-3xl tw-border tw-border-slate-100 tw-border-dashed">
                <div class="tw-w-20 tw-h-20 tw-mx-auto tw-bg-slate-50 tw-rounded-3xl tw-flex tw-items-center tw-justify-center tw-text-slate-300 tw-mb-4">
                    <i class="fa fa-folder-open-o tw-text-4xl"></i>
                </div>
                <h3 class="tw-text-xl tw-font-black tw-text-slate-800">No tour categories found</h3>
                <p class="tw-text-slate-500 tw-font-medium tw-mt-2">Create your first category to group tours.</p>
                <a href="#addnew" class="tw-inline-block tw-mt-6 tw-bg-amber-50 tw-text-amber-600 tw-font-bold tw-px-6 tw-py-2.5 tw-rounded-xl hover:tw-bg-amber-500 hover:tw-text-white tw-transition-colors tw-no-underline">
                    Create First Category
                </a>
            </div>
        @endforelse
    </div>
</div>

{{-- Add New Category Modal (Cleaned) --}}
<div class="modal" id="addnew">
    <div class="tw-w-full tw-max-w-3xl !tw-p-8 sm:!tw-p-10 !tw-rounded-[2rem] tw-bg-white/95 tw-backdrop-blur-xl tw-border tw-border-slate-100 tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-mb-8">
            <div class="tw-flex tw-items-center tw-gap-4">
                <span class="tw-w-14 tw-h-14 tw-bg-gradient-to-br tw-from-amber-400 tw-to-orange-500 tw-text-white tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-amber-200">
                    <i class="fa fa-plus-circle tw-text-2xl"></i>
                </span>
                <div>
                    <h3 class="tw-text-2xl tw-font-black tw-text-slate-900 tw-mb-0 tw-tracking-tight">New Category</h3>
                    <p class="tw-text-slate-500 tw-text-sm tw-font-medium tw-mt-1">Add localized names for the new category.</p>
                </div>
            </div>
            <a href="#close" class="tw-w-10 tw-h-10 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-slate-50 tw-text-slate-400 hover:tw-bg-rose-50 hover:tw-text-rose-500 tw-transition-all tw-no-underline">
                <i class="fa fa-times tw-text-lg"></i>
            </a>
        </div>
        
        <form method="POST" action="{{ route('admin.tour-categories.store') }}" class="tw-flex tw-flex-col tw-gap-6">
            @csrf
            <input type="hidden" name="add_new" value="1">
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-5 tw-mb-4">
                @foreach(['en' => 'English', 'fr' => 'French', 'it' => 'Italian', 'es' => 'Spanish', 'Ar' => 'Arabic', 'ge' => 'German', 'pt' => 'Portuguese'] as $code => $label)
                <div class="tw-flex tw-flex-col tw-gap-1.5 tw-p-3 tw-rounded-2xl tw-border {{ $errors->has('category_'.$code) ? 'tw-bg-rose-50 tw-border-rose-200' : 'tw-bg-slate-50/50 tw-border-slate-100' }}">
                    <label class="tw-text-[10px] tw-font-bold tw-uppercase tw-tracking-wider tw-ml-2 {{ $errors->has('category_'.$code) ? 'tw-text-rose-500' : 'tw-text-slate-500' }}">
                        {{ $label }} {!! $code == 'en' ? '<span class="tw-text-rose-500">*</span>' : '' !!}
                    </label>
                    <input type="text" name="category_{{ $code }}" value="{{ old('category_'.$code) }}" placeholder="Enter {{ strtolower($label) }} name..." class="tw-w-full tw-bg-white tw-shadow-sm tw-rounded-xl !tw-py-2.5 {{ $errors->has('category_'.$code) ? 'tw-border tw-border-rose-300 focus:tw-ring-2 focus:tw-ring-rose-100' : 'tw-border-none focus:tw-ring-2 focus:tw-ring-amber-100' }}">
                    @error('category_'.$code)
                        <span class="tw-text-xs tw-font-bold tw-text-rose-500 tw-ml-2 tw-mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @endforeach
            </div>
            
            <div class="tw-mt-4 tw-pt-6 tw-border-t tw-border-slate-100 tw-flex tw-items-center tw-justify-end tw-gap-3">
                <a href="#close" class="tw-px-6 tw-py-3 tw-rounded-xl tw-font-bold tw-text-slate-500 hover:tw-bg-slate-100 tw-transition-colors tw-no-underline">Cancel</a>
                <button type="submit" class="tw-bg-amber-500 hover:tw-bg-amber-600 tw-text-white tw-px-8 tw-py-3 tw-rounded-xl tw-font-bold tw-shadow-lg tw-shadow-amber-200 tw-transition-all hover:-tw-translate-y-0.5">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<div id="ajax"></div>

@if($errors->any() && old('add_new'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.location.hash = '#addnew';
    });
</script>
@endif

@endsection
