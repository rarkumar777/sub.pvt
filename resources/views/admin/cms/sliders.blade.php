@extends('admin.layouts.app')
@section('title', 'Admin | CMS Sliders')

@section('content')
<div class="tw-font-sans tw-max-w-screen-xl tw-mx-auto tw-pb-16">

    {{-- Material 3 Header --}}
    <header class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4 tw-mb-8">
        <div>
            <nav class="tw-flex tw-text-xs tw-font-medium tw-text-slate-500 tw-mb-2 tw-uppercase tw-tracking-wider" aria-label="Breadcrumb">
                <ol class="tw-inline-flex tw-items-center">
                    <li><a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-600 tw-transition-colors tw-no-underline">Dashboard</a></li>
                    <li><i class="fa fa-chevron-right tw-mx-2 tw-text-[10px] tw-text-slate-300"></i></li>
                    <li class="tw-text-orange-600 tw-font-bold">Image Sliders</li>
                </ol>
            </nav>
            <h1 class="tw-text-3xl tw-font-normal tw-tracking-tight tw-text-orange-900 tw-m-0">
                Image Sliders
            </h1>
            <p class="tw-text-sm tw-text-slate-500 tw-mt-1 tw-mb-0">Manage homepage and page image carousels.</p>
        </div>
        <div>
            <button type="button" onclick="document.getElementById('add-slider-modal').style.display='flex';" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-full tw-bg-orange-600 tw-px-6 tw-py-2.5 tw-text-sm tw-font-medium tw-text-white tw-shadow-sm hover:tw-bg-orange-700 tw-transition-colors tw-border-none tw-cursor-pointer">
                <i class="fa fa-plus"></i> Create Slider
            </button>
        </div>
    </header>

    @if(session('success'))
    <div class="tw-bg-orange-50 tw-rounded-2xl tw-p-4 tw-flex tw-items-center tw-gap-3 tw-mb-8">
        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-orange-500 tw-text-white tw-flex tw-items-center tw-justify-center tw-flex-shrink-0 tw-text-xs">
            <i class="fa fa-check"></i>
        </div>
        <p class="tw-text-orange-800 tw-font-medium tw-text-sm tw-m-0">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Edit Slider Form (shown when editing) --}}
    @if(isset($slider))
    <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-p-6 tw-mb-8 tw-animate-[slideDown_0.3s_ease]">
        <style>@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }</style>
        <div class="tw-flex tw-items-center tw-gap-3 tw-mb-4">
            <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-amber-100 tw-text-amber-600 tw-flex tw-items-center tw-justify-center">
                <i class="fa fa-pencil"></i>
            </div>
            <h3 class="tw-text-base tw-font-medium tw-text-orange-900 tw-m-0">Edit Slider Configuration</h3>
        </div>
        
        <form method="POST" action="{{ route('admin.sliders.update', $slider->id) }}" class="tw-flex tw-flex-col sm:tw-flex-row tw-items-end tw-gap-4">
            @csrf @method('PUT')
            <div class="tw-flex-1 tw-w-full">
                <div class="tw-relative">
                    <input type="text" name="name" id="edit_name" value="{{ $slider->name }}" placeholder=" " required
                        class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-orange-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-amber-500 tw-peer tw-transition-colors">
                    <label for="edit_name" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-peer-focus:tw-text-amber-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3">Slider Title</label>
                </div>
            </div>
            <div class="tw-flex tw-gap-2">
                <a href="{{ route('admin.sliders.index') }}" class="tw-inline-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-slate-100 tw-px-6 tw-py-2.5 tw-text-sm tw-font-medium tw-text-slate-700 hover:tw-bg-slate-200 tw-transition-colors tw-no-underline">Cancel</a>
                <button type="submit" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-full tw-bg-amber-500 tw-px-6 tw-py-2.5 tw-text-sm tw-font-medium tw-text-white tw-shadow-sm hover:tw-bg-amber-600 tw-transition-colors tw-border-none tw-cursor-pointer">
                    <i class="fa fa-check"></i> Update
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Material 3 Data Table Card --}}
    <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-8 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Slider Details</th>
                        <th class="tw-py-4 tw-px-8 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($sliders as $s)
                    <tr class="hover:tw-bg-slate-50/50 tw-transition-colors">
                        <td class="tw-py-4 tw-px-8">
                            <div class="tw-flex tw-items-center tw-gap-4">
                                <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-orange-50 tw-text-orange-600 tw-flex tw-items-center tw-justify-center tw-flex-shrink-0 tw-shadow-sm">
                                    <i class="fa fa-image"></i>
                                </div>
                                <div>
                                    <span class="tw-font-medium tw-text-orange-900 tw-text-sm tw-block">{{ $s->name }}</span>
                                    <span class="tw-text-xs tw-text-slate-400 tw-font-medium">ID: {{ $s->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="tw-py-4 tw-px-8 tw-text-right">
                            <div class="tw-flex tw-justify-end tw-items-center tw-gap-3">
                                
                                {{-- Colorful Manage Images Pill Button --}}
                                <a href="{{ route('admin.sliders.images', $s->id) }}" class="tw-inline-flex tw-items-center tw-gap-2 tw-px-4 tw-py-1.5 tw-rounded-full tw-bg-orange-50 tw-text-orange-600 tw-text-xs tw-font-bold tw-uppercase tw-tracking-wide hover:tw-bg-orange-600 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-orange-100 hover:tw-border-orange-600 tw-shadow-sm">
                                    <i class="fa fa-picture-o"></i> Edit Images
                                </a>
                                
                                <div class="tw-w-[1px] tw-h-5 tw-bg-slate-200"></div>

                                {{-- Circular Action Buttons --}}
                                <a href="{{ route('admin.sliders.edit', $s->id) }}" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-amber-50 tw-text-amber-600 hover:tw-bg-amber-500 hover:tw-text-white tw-transition-colors tw-no-underline" title="Edit Slider Name">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.sliders.delete', $s->id) }}" class="tw-inline" onsubmit="return confirm('WARNING: This will delete the slider and all attached images. Proceed?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-colors tw-border-none tw-cursor-pointer" title="Delete Slider">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-rounded-full tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-300">
                                    <i class="fa fa-images tw-text-3xl"></i>
                                </div>
                                <div>
                                    <p class="tw-text-orange-900 tw-font-medium tw-text-sm tw-m-0">No Sliders Found</p>
                                    <p class="tw-text-slate-500 tw-text-xs tw-mt-1 tw-m-0">Create a slider to start managing homepage and page images.</p>
                                </div>
                                <button type="button" onclick="document.getElementById('add-slider-modal').style.display='flex';" class="tw-inline-flex tw-items-center tw-gap-2 tw-rounded-full tw-bg-orange-50 tw-text-orange-600 tw-px-4 tw-py-2 tw-text-xs tw-font-bold tw-uppercase tw-tracking-wide hover:tw-bg-orange-100 tw-transition-colors tw-mt-2 tw-border-none tw-cursor-pointer">
                                    <i class="fa fa-plus"></i> Add First Slider
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sliders->hasPages())
        <div class="tw-px-8 tw-py-4 tw-border-t tw-border-slate-100 tw-bg-slate-50/50">
            {{ $sliders->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Add New Slider Material 3 Modal --}}
<div id="add-slider-modal" class="tw-fixed tw-inset-0 tw-bg-orange-900/40 tw-z-50 tw-items-center tw-justify-center tw-backdrop-blur-sm" style="display:none;">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden tw-w-[450px] tw-max-w-[95vw] tw-shadow-2xl tw-animate-[slideDown_0.2s_ease]">
        <div class="tw-pt-8 tw-px-8 tw-pb-6">
            <div class="tw-w-12 tw-h-12 tw-rounded-2xl tw-bg-orange-50 tw-text-orange-600 tw-flex tw-items-center tw-justify-center tw-text-xl tw-mb-4">
                <i class="fa fa-plus-circle"></i>
            </div>
            <h3 class="tw-text-xl tw-font-semibold tw-text-orange-900 tw-m-0">Create New Slider</h3>
            <p class="tw-text-sm tw-text-slate-500 tw-mt-1 tw-mb-0">Enter a descriptive title for this image carousel.</p>
        </div>
        
        <form method="POST" action="{{ route('admin.sliders.store') }}" class="tw-px-8 tw-pb-8">
            @csrf
            <div class="tw-mb-8">
                <div class="tw-relative">
                    <input type="text" name="name" id="new_name" placeholder=" " required autofocus
                        class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-orange-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer tw-transition-colors">
                    <label for="new_name" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-peer-focus:tw-text-orange-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3">Slider Title</label>
                </div>
            </div>
            <div class="tw-flex tw-justify-end tw-gap-3">
                <button type="button" onclick="document.getElementById('add-slider-modal').style.display='none';" class="tw-inline-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-slate-100 tw-px-6 tw-py-2.5 tw-text-sm tw-font-medium tw-text-slate-700 hover:tw-bg-slate-200 tw-transition-colors tw-border-none tw-cursor-pointer">Cancel</button>
                <button type="submit" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-full tw-bg-orange-600 tw-px-6 tw-py-2.5 tw-text-sm tw-font-medium tw-text-white tw-shadow-sm hover:tw-bg-orange-700 tw-transition-colors tw-border-none tw-cursor-pointer">
                    <i class="fa fa-check"></i> Create
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
