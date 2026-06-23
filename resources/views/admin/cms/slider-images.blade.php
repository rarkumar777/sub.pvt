@extends('admin.layouts.app')
@section('title', 'Admin | Slider Images')

@section('content')
<div class="tw-font-sans tw-max-w-screen-2xl tw-mx-auto tw-pb-16">

    {{-- Material 3 Header & Breadcrumb --}}
    <header class="tw-mb-10">
        <nav class="tw-flex tw-text-xs tw-font-medium tw-text-slate-500 tw-mb-3 tw-uppercase tw-tracking-wider" aria-label="Breadcrumb">
            <ol class="tw-inline-flex tw-items-center">
                <li><a href="{{ route('admin.dashboard') }}" class="hover:tw-text-indigo-600 tw-transition-colors tw-no-underline">Dashboard</a></li>
                <li><i class="fa fa-chevron-right tw-mx-2 tw-text-[10px] tw-text-slate-300"></i></li>
                <li><a href="{{ route('admin.sliders.index') }}" class="hover:tw-text-indigo-600 tw-transition-colors tw-no-underline">Sliders</a></li>
                <li><i class="fa fa-chevron-right tw-mx-2 tw-text-[10px] tw-text-slate-300"></i></li>
                <li class="tw-text-indigo-600 tw-font-bold">{{ $slider->name }} Images</li>
            </ol>
        </nav>
        <div class="tw-flex tw-flex-col sm:tw-flex-row tw-justify-between tw-items-start sm:tw-items-end tw-gap-4">
            <div>
                <h1 class="tw-text-3xl tw-font-normal tw-tracking-tight tw-text-slate-900 tw-m-0">
                    <span class="tw-font-bold">{{ $slider->name }}</span>
                </h1>
                <p class="tw-text-sm tw-text-slate-500 tw-mt-1.5 tw-mb-0">Manage and upload images for this slider.</p>
            </div>
            <a href="#add_img" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-full tw-bg-indigo-600 tw-px-8 tw-py-2.5 tw-text-sm tw-font-medium tw-text-white tw-shadow-sm hover:tw-bg-indigo-700 hover:tw-shadow-md tw-transition-all tw-flex-shrink-0 tw-no-underline">
                <i class="fa fa-plus"></i> Add Image
            </a>
        </div>
    </header>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-rounded-3xl tw-p-5 tw-flex tw-items-center tw-gap-4 tw-mb-8">
        <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-emerald-500 tw-text-white tw-flex tw-items-center tw-justify-center tw-flex-shrink-0">
            <i class="fa fa-check"></i>
        </div>
        <p class="tw-text-emerald-800 tw-font-medium tw-text-sm tw-m-0">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Material 3 Image Grid --}}
    <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-3 xl:tw-grid-cols-4 tw-gap-8">
        @forelse($images as $img)
        <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden hover:tw-shadow-lg tw-transition-shadow tw-duration-300 tw-group tw-flex tw-flex-col">
            <div class="tw-aspect-[4/3] tw-bg-slate-100 tw-overflow-hidden tw-relative">
                @php
                    $ext = strtolower(pathinfo($img->image, PATHINFO_EXTENSION));
                @endphp
                @if(in_array($ext, ['mp4', 'webm', 'ogg']))
                    <video class="tw-w-full tw-h-full tw-object-cover group-hover:tw-scale-105 tw-transition-transform tw-duration-500" controls muted>
                        <source src="{{ asset('uploads/sliders/' . $img->image) }}" type="video/{{ $ext === 'ogg' ? 'ogg' : ($ext === 'webm' ? 'webm' : 'mp4') }}">
                    </video>
                    <div class="tw-absolute tw-top-3 tw-left-3 tw-px-2 tw-py-1 tw-rounded-lg tw-bg-slate-900/70 tw-backdrop-blur-sm tw-text-white tw-text-[10px] tw-font-bold tw-uppercase tw-tracking-wider">VIDEO</div>
                @else
                    <img src="{{ asset('uploads/sliders/' . $img->image) }}" alt="" class="tw-w-full tw-h-full tw-object-cover group-hover:tw-scale-105 tw-transition-transform tw-duration-500">
                @endif
            </div>
            <div class="tw-flex tw-justify-between tw-items-center tw-px-6 tw-py-4 tw-bg-white tw-border-t tw-border-slate-50 tw-flex-1">
                <div class="tw-text-xs tw-text-slate-400 tw-font-medium tw-truncate tw-pr-2">ID: {{ $img->id }}</div>
                <div class="tw-flex tw-gap-2">
                    <a href="#edit_img_{{ $img->id }}" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-slate-50 tw-text-slate-500 hover:tw-bg-amber-500 hover:tw-text-white tw-transition-all tw-no-underline tw-duration-300 tw-shadow-sm hover:tw-shadow-amber-200" title="Edit Configuration">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a href="{{ route('admin.sliders.images.delete.get', [$sliderId, $img->id]) }}" onclick="return confirm('WARNING: Are you sure you want to permanently delete this media file?')" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-slate-50 tw-text-slate-500 hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all tw-no-underline tw-duration-300 tw-shadow-sm hover:tw-shadow-rose-200" title="Delete Media">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="tw-col-span-full">
            <div class="tw-bg-white tw-rounded-3xl tw-border tw-border-dashed tw-border-slate-200 tw-p-20 tw-text-center tw-flex tw-flex-col tw-items-center">
                <div class="tw-w-20 tw-h-20 tw-rounded-full tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-300 tw-mb-4">
                    <i class="fa fa-images tw-text-4xl"></i>
                </div>
                <h3 class="tw-text-lg tw-font-medium tw-text-slate-900 tw-m-0">No Media Found</h3>
                <p class="tw-text-slate-500 tw-mt-2 tw-max-w-sm">This slider is currently empty. Click "Add Image" to upload photos or videos.</p>
                <a href="#add_img" class="tw-mt-6 tw-inline-flex tw-items-center tw-gap-2 tw-rounded-full tw-bg-indigo-50 tw-px-6 tw-py-2.5 tw-text-sm tw-font-bold tw-text-indigo-600 hover:tw-bg-indigo-100 tw-transition-colors tw-no-underline">
                    <i class="fa fa-plus"></i> Add Media
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
/* Custom Material Floating Inputs for Modals */
.m3-input {
    @apply tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-200 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-indigo-600 tw-peer tw-transition-colors;
}
.m3-label {
    @apply tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-peer-focus:tw-text-indigo-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3;
}
</style>

{{-- Edit Image Modals --}}
@foreach($images as $img)
@php
    $contents = $imageContents[$img->id] ?? ['price' => '', 'langs' => []];
    $langs = ['en', 'fr', 'it', 'es', 'ar', 'ge', 'pt'];
@endphp
<div class="modal" id="edit_img_{{ $img->id }}">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[800px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-white tw-border-b tw-border-slate-100 tw-sticky tw-top-0 tw-z-20">
            <h3 class="tw-text-xl tw-font-semibold tw-text-slate-900 tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-amber-100 tw-text-amber-600 tw-flex tw-items-center tw-justify-center tw-text-sm">
                    <i class="fa fa-pencil"></i>
                </div>
                Edit Media Data & Captions
            </h3>
            <a href="#close" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-slate-100 tw-text-slate-500 hover:tw-bg-slate-200 tw-transition-colors tw-text-lg tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.sliders.images.update', [$sliderId, $img->id]) }}" enctype="multipart/form-data" class="tw-p-8 tw-max-h-[75vh] tw-overflow-y-auto">
            @csrf @method('PUT')

            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-x-10 tw-gap-y-12 tw-mb-8">
                @foreach($langs as $k)
                @php
                    $langData = $contents['langs'][$k] ?? ['text' => '', 'text2' => '', 'text3' => '', 'link' => ''];
                @endphp
                <div class="tw-bg-slate-50/50 tw-rounded-2xl tw-p-6 tw-border tw-border-slate-100 tw-shadow-sm">
                    <div class="tw-flex tw-items-center tw-gap-3 tw-mb-5">
                        <span class="tw-px-2.5 tw-py-1 tw-rounded-md tw-bg-indigo-100 tw-text-indigo-700 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest">{{ $k }}</span>
                        <div class="tw-h-px tw-flex-1 tw-bg-slate-200"></div>
                    </div>
                    <div class="tw-space-y-4">
                        <div class="tw-relative">
                            <input type="text" name="edit_{{ $k }}" id="e_c1_{{ $img->id }}_{{ $k }}" value="{{ $langData['text'] }}" placeholder=" " class="m3-input">
                            <label for="e_c1_{{ $img->id }}_{{ $k }}" class="m3-label">Caption 1</label>
                        </div>
                        <div class="tw-relative">
                            <input type="text" name="edit_2{{ $k }}" id="e_c2_{{ $img->id }}_{{ $k }}" value="{{ $langData['text2'] }}" placeholder=" " class="m3-input">
                            <label for="e_c2_{{ $img->id }}_{{ $k }}" class="m3-label">Caption 2</label>
                        </div>
                        <div class="tw-relative">
                            <input type="text" name="edit_3{{ $k }}" id="e_c3_{{ $img->id }}_{{ $k }}" value="{{ $langData['text3'] ?? '' }}" placeholder=" " class="m3-input">
                            <label for="e_c3_{{ $img->id }}_{{ $k }}" class="m3-label">Caption 3</label>
                        </div>
                        <div class="tw-relative">
                            <input type="text" name="link_{{ $k }}" id="e_link_{{ $img->id }}_{{ $k }}" value="{{ $langData['link'] }}" placeholder=" " class="m3-input">
                            <label for="e_link_{{ $img->id }}_{{ $k }}" class="m3-label">URL / CTA Link</label>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="tw-bg-slate-50 tw-rounded-2xl tw-p-6 tw-border tw-border-slate-200 tw-mb-6">
                <h4 class="tw-text-sm tw-font-bold tw-text-slate-900 tw-mb-5 tw-flex tw-items-center tw-gap-2">
                    <i class="fa fa-cog tw-text-slate-400"></i> General Settings
                </h4>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6 tw-items-start">
                    <div class="tw-relative">
                        <input type="text" name="price" id="e_price_{{ $img->id }}" value="{{ $contents['price'] ?? '' }}" placeholder=" " class="m3-input">
                        <label for="e_price_{{ $img->id }}" class="m3-label">Override Price (Optional)</label>
                    </div>
                    <div>
                        <label class="tw-text-xs tw-font-semibold tw-text-slate-500 tw-mb-2 tw-block">Replace Media File</label>
                        <input type="file" name="change_image" class="tw-block tw-w-full tw-text-sm tw-text-slate-500 file:tw-mr-4 file:tw-py-2 file:tw-px-4 file:tw-rounded-full file:tw-border-0 file:tw-text-xs file:tw-font-semibold file:tw-bg-indigo-50 file:tw-text-indigo-700 hover:file:tw-bg-indigo-100 tw-transition-colors">
                    </div>
                </div>
            </div>
            
            <div class="tw-sticky tw-bottom-0 tw-bg-white tw-pt-4 tw-border-t tw-border-slate-100 tw-flex tw-justify-end tw-gap-3">
                <a href="#close" class="tw-inline-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-slate-100 tw-px-8 tw-py-3 tw-text-sm tw-font-medium tw-text-slate-700 hover:tw-bg-slate-200 tw-transition-colors tw-no-underline">Cancel</a>
                <button type="submit" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-full tw-bg-indigo-600 tw-px-10 tw-py-3 tw-text-sm tw-font-medium tw-text-white tw-shadow-md hover:tw-bg-indigo-700 tw-transition-colors tw-border-none tw-cursor-pointer">
                    <i class="fa fa-check"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- Add Image Modal --}}
<div class="modal" id="add_img">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[800px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-white tw-border-b tw-border-slate-100 tw-sticky tw-top-0 tw-z-20">
            <h3 class="tw-text-xl tw-font-semibold tw-text-slate-900 tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-indigo-100 tw-text-indigo-600 tw-flex tw-items-center tw-justify-center tw-text-sm">
                    <i class="fa fa-plus"></i>
                </div>
                Upload New Media
            </h3>
            <a href="#close" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-slate-100 tw-text-slate-500 hover:tw-bg-slate-200 tw-transition-colors tw-text-lg tw-no-underline">&times;</a>
        </div>
        <form method="POST" action="{{ route('admin.sliders.images.store', $sliderId) }}" enctype="multipart/form-data" class="tw-p-8 tw-max-h-[75vh] tw-overflow-y-auto">
            @csrf

            <div class="tw-bg-indigo-50/50 tw-rounded-2xl tw-p-6 tw-border tw-border-indigo-100 tw-mb-8">
                <h4 class="tw-text-sm tw-font-bold tw-text-indigo-900 tw-mb-5 tw-flex tw-items-center tw-gap-2">
                    <i class="fa fa-upload tw-text-indigo-400"></i> Media Source
                </h4>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6 tw-items-start">
                    <div>
                        <label class="tw-text-xs tw-font-semibold tw-text-slate-600 tw-mb-2 tw-block">Upload File (Image/Video)</label>
                        <input type="file" name="image_file" class="tw-block tw-w-full tw-text-sm tw-text-slate-600 file:tw-mr-4 file:tw-py-2.5 file:tw-px-6 file:tw-rounded-full file:tw-border-0 file:tw-text-sm file:tw-font-bold file:tw-bg-indigo-600 file:tw-text-white hover:file:tw-bg-indigo-700 tw-transition-colors tw-cursor-pointer">
                    </div>
                    <div class="tw-relative tw-mt-6">
                        <input type="text" name="image" id="a_img_name" placeholder=" " class="m3-input">
                        <label for="a_img_name" class="m3-label">Or specific filename (e.g. 14492.jpg)</label>
                    </div>
                    <div class="tw-relative tw-col-span-full md:tw-col-span-1">
                        <input type="text" name="price" id="a_price" placeholder=" " class="m3-input">
                        <label for="a_price" class="m3-label">Price Data (Optional)</label>
                    </div>
                </div>
            </div>
            
            <h4 class="tw-text-sm tw-font-bold tw-text-slate-900 tw-mb-5 tw-flex tw-items-center tw-gap-2 tw-mt-4">
                <i class="fa fa-language tw-text-slate-400"></i> Localized Captions
            </h4>

            @php
                $langs = ['en', 'fr', 'it', 'es', 'ar', 'ge', 'pt'];
            @endphp
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-x-10 tw-gap-y-8 tw-mb-4">
                @foreach($langs as $k)
                <div class="tw-bg-slate-50/50 tw-rounded-2xl tw-p-6 tw-border tw-border-slate-100 tw-shadow-sm">
                    <div class="tw-flex tw-items-center tw-gap-3 tw-mb-5">
                        <span class="tw-px-2.5 tw-py-1 tw-rounded-md tw-bg-indigo-100 tw-text-indigo-700 tw-text-xs tw-font-bold tw-uppercase tw-tracking-widest">{{ $k }}</span>
                        <div class="tw-h-px tw-flex-1 tw-bg-slate-200"></div>
                    </div>
                    <div class="tw-space-y-4">
                        <div class="tw-relative">
                            <input type="text" name="edit_{{ $k }}" id="a_c1_{{ $k }}" placeholder=" " class="m3-input">
                            <label for="a_c1_{{ $k }}" class="m3-label">Caption 1</label>
                        </div>
                        <div class="tw-relative">
                            <input type="text" name="edit_2{{ $k }}" id="a_c2_{{ $k }}" placeholder=" " class="m3-input">
                            <label for="a_c2_{{ $k }}" class="m3-label">Caption 2</label>
                        </div>
                        <div class="tw-relative">
                            <input type="text" name="edit_3{{ $k }}" id="a_c3_{{ $k }}" placeholder=" " class="m3-input">
                            <label for="a_c3_{{ $k }}" class="m3-label">Caption 3</label>
                        </div>
                        <div class="tw-relative">
                            <input type="text" name="link_{{ $k }}" id="a_link_{{ $k }}" placeholder=" " class="m3-input">
                            <label for="a_link_{{ $k }}" class="m3-label">URL / CTA Link</label>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="tw-sticky tw-bottom-0 tw-bg-white tw-pt-4 tw-border-t tw-border-slate-100 tw-flex tw-justify-end tw-gap-3">
                <a href="#close" class="tw-inline-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-slate-100 tw-px-8 tw-py-3 tw-text-sm tw-font-medium tw-text-slate-700 hover:tw-bg-slate-200 tw-transition-colors tw-no-underline">Cancel</a>
                <button type="submit" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-full tw-bg-indigo-600 tw-px-10 tw-py-3 tw-text-sm tw-font-medium tw-text-white tw-shadow-md hover:tw-bg-indigo-700 tw-transition-colors tw-border-none tw-cursor-pointer">
                    <i class="fa fa-cloud-upload"></i> Upload & Save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
