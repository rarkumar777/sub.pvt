@extends('admin.layouts.app')

@section('title', 'Admin | Tour Settings')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    
    {{-- Header Section --}}
    <div class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4 tw-bg-white tw-p-8 tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-bold tw-text-indigo-400 tw-uppercase tw-tracking-widest tw-mb-3">
                <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-indigo-600 tw-transition-colors tw-no-underline">Dashboard</a>
                <span class="tw-opacity-50">/</span>
                <span class="tw-text-slate-500">Settings</span>
            </div>
            <h1 class="tw-text-4xl tw-font-black tw-text-slate-900 tw-flex tw-items-center tw-gap-4 tw-tracking-tight">
                <div class="tw-w-14 tw-h-14 tw-bg-gradient-to-br tw-from-orange-400 tw-to-orange-600 tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-orange-200">
                    <i class="fa fa-cog tw-text-white tw-text-2xl"></i>
                </div>
                Tour Global Settings
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-3 tw-text-sm max-w-2xl">Global configuration for tour modules, pricing tax structures, and frontend display logic. Changes here apply system-wide.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-4 tw-shadow-sm">
        <div class="tw-w-10 tw-h-10 tw-bg-white tw-rounded-full tw-flex tw-items-center tw-justify-center tw-shadow-sm">
            <i class="fa fa-check tw-text-emerald-500 tw-text-xl"></i>
        </div>
        <div>
            <p class="tw-text-emerald-800 tw-font-black tw-text-sm tw-uppercase tw-tracking-wide">Success</p>
            <p class="tw-text-emerald-600 tw-font-medium tw-text-sm">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="tw-flex tw-flex-col tw-gap-3">
        @foreach($errors->all() as $error)
        <div class="tw-bg-rose-50 tw-border-l-4 tw-border-rose-500 tw-p-5 tw-rounded-2xl tw-flex tw-items-center tw-gap-4 tw-shadow-sm">
            <div class="tw-w-10 tw-h-10 tw-bg-white tw-rounded-full tw-flex tw-items-center tw-justify-center tw-shadow-sm">
                <i class="fa fa-warning tw-text-rose-500 tw-text-xl"></i>
            </div>
            <div>
                <p class="tw-text-rose-800 tw-font-black tw-text-sm tw-uppercase tw-tracking-wide">Error Detected</p>
                <p class="tw-text-rose-600 tw-font-medium tw-text-sm">{{ $error }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-8 tw-mt-2">
        {{-- Main Settings Form --}}
        <div class="lg:tw-col-span-2">
            <div class="tw-bg-white tw-rounded-[2rem] tw-shadow-sm tw-border tw-border-slate-100 tw-p-8 sm:tw-p-10 tw-h-full">
                <form method="POST" action="{{ route('admin.tour-settings.save') }}" class="tw-flex tw-flex-col tw-gap-10">
                    @csrf
                    
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-8">
                        {{-- Tax Setting --}}
                        <div class="tw-flex tw-flex-col tw-gap-3">
                            <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-flex tw-items-center tw-gap-2">
                                <span class="tw-w-7 tw-h-7 tw-rounded-lg tw-bg-indigo-50 tw-flex tw-items-center tw-justify-center">
                                    <i class="fa fa-percent tw-text-indigo-500"></i>
                                </span>
                                Global Tax Rate
                            </label>
                            <div class="tw-relative group">
                                <input type="text" name="tax" value="{{ $settings['tax'] ?? '0' }}" class="tw-w-full !tw-pl-5 !tw-pr-12 !tw-py-4 tw-bg-slate-50 tw-border-none tw-shadow-sm tw-rounded-2xl focus:tw-ring-2 focus:tw-ring-indigo-100 focus:tw-bg-white tw-text-lg tw-font-black tw-text-slate-700 tw-transition-all" placeholder="0">
                                <span class="tw-absolute tw-right-5 tw-top-1/2 -tw-translate-y-1/2 tw-text-lg tw-font-black tw-text-slate-300 group-focus-within:tw-text-indigo-400 tw-transition-colors">%</span>
                            </div>
                            <p class="tw-text-xs tw-text-slate-400 tw-font-medium tw-ml-2">Applied automatically to all tour pricing calculations.</p>
                        </div>

                        {{-- Pagination Setting --}}
                        <div class="tw-flex tw-flex-col tw-gap-3">
                            <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-flex tw-items-center tw-gap-2">
                                <span class="tw-w-7 tw-h-7 tw-rounded-lg tw-bg-blue-50 tw-flex tw-items-center tw-justify-center">
                                    <i class="fa fa-list-ol tw-text-blue-500"></i>
                                </span>
                                Latest Tours Count
                            </label>
                            <input type="text" name="latest_tours_number" value="{{ $settings['latest_tours_number'] ?? '8' }}" class="tw-w-full !tw-px-5 !tw-py-4 tw-bg-slate-50 tw-border-none tw-shadow-sm tw-rounded-2xl focus:tw-ring-2 focus:tw-ring-blue-100 focus:tw-bg-white tw-text-lg tw-font-black tw-text-slate-700 tw-transition-all" placeholder="8">
                            <p class="tw-text-xs tw-text-slate-400 tw-font-medium tw-ml-2">Number of items to show in the "Latest Tours" section.</p>
                        </div>
                    </div>

                    {{-- Icon Setting --}}
                    <div class="tw-flex tw-flex-col tw-gap-3 tw-bg-slate-50/50 tw-p-6 tw-rounded-3xl tw-border tw-border-slate-100">
                        <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-flex tw-items-center tw-gap-2">
                            <span class="tw-w-7 tw-h-7 tw-rounded-lg tw-bg-amber-50 tw-flex tw-items-center tw-justify-center">
                                <i class="fa fa-star tw-text-amber-500"></i>
                            </span>
                            Default Rating Symbol
                        </label>
                        <div class="tw-flex tw-items-center tw-gap-5">
                            <div class="tw-w-16 tw-h-16 tw-bg-white tw-rounded-2xl tw-shadow-md tw-flex tw-items-center tw-justify-center tw-text-2xl tw-text-amber-400 tw-border tw-border-slate-100">
                                <i class="{{ $settings['rate_icon'] ?? 'fa-star' }} tw-drop-shadow-sm" id="icon-preview"></i>
                            </div>
                            <div class="tw-flex-1">
                                <select name="selected_icon" id="selected_icon" class="tw-w-full !tw-bg-white tw-border-none tw-shadow-sm tw-rounded-xl focus:tw-ring-2 focus:tw-ring-amber-200 !tw-py-4 tw-text-base tw-font-bold tw-text-slate-700">
                                    @php
                                    $icons = ['fa-star','fa-circle','fa-heart','fa-diamond','fa-square','fa-certificate','fa-trophy','fa-flag','fa-bolt','fa-fire'];
                                    $current = $settings['rate_icon'] ?? 'fa-star';
                                    @endphp
                                    @foreach($icons as $icon)
                                    <option value="{{ $icon }}" {{ $current == $icon ? 'selected' : '' }}>{{ ucfirst(str_replace('fa-', '', $icon)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p class="tw-text-xs tw-text-slate-400 tw-font-medium tw-ml-2 tw-mt-2">Choose the visual symbol for quality ratings across the public site.</p>
                    </div>

                    <div class="tw-pt-8 tw-border-t tw-border-slate-100 tw-flex tw-justify-end">
                        <button type="submit" class="tw-bg-orange-600 hover:tw-bg-orange-700 tw-text-white tw-px-10 tw-py-4 tw-rounded-2xl tw-font-black tw-text-sm tw-shadow-xl hover:tw-shadow-orange-200 hover:-tw-translate-y-0.5 tw-transition-all tw-flex tw-items-center tw-gap-3">
                            <i class="fa fa-save tw-text-lg"></i> Save Global Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Help Card --}}
        <div class="tw-flex tw-flex-col">
            <div class="tw-bg-gradient-to-br tw-from-orange-500 tw-to-orange-600 tw-text-white tw-rounded-[2rem] tw-p-10 tw-shadow-2xl tw-h-full tw-relative tw-overflow-hidden">
                <!-- Decorative background elements -->
                <div class="tw-absolute -tw-top-20 -tw-right-20 tw-w-40 tw-h-40 tw-bg-indigo-500/20 tw-rounded-full tw-blur-3xl"></div>
                <div class="tw-absolute -tw-bottom-20 -tw-left-20 tw-w-40 tw-h-40 tw-bg-blue-500/20 tw-rounded-full tw-blur-3xl"></div>

                <div class="tw-relative tw-z-10">
                    <div class="tw-w-14 tw-h-14 tw-bg-indigo-500/20 tw-border tw-border-indigo-400/30 tw-text-indigo-300 tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-mb-8 tw-shadow-inner">
                        <i class="fa fa-lightbulb-o tw-text-2xl"></i>
                    </div>
                    
                    <h3 class="tw-text-2xl tw-font-black tw-mb-2">Configuration Tips</h3>
                    <p class="tw-text-slate-400 tw-text-sm tw-font-medium tw-mb-8">Useful information regarding these global variables.</p>
                    
                    <ul class="tw-flex tw-flex-col tw-gap-6 tw-text-slate-300 tw-text-sm tw-font-medium tw-list-none !tw-p-0">
                        <li class="tw-flex tw-items-start tw-gap-4">
                            <div class="tw-mt-1 tw-w-6 tw-h-6 tw-rounded-full tw-bg-emerald-500/20 tw-flex tw-items-center tw-justify-center tw-shrink-0">
                                <i class="fa fa-check tw-text-emerald-400 tw-text-[10px]"></i>
                            </div>
                            <span class="tw-leading-relaxed">Tax changes are applied immediately to all newly calculated quotes.</span>
                        </li>
                        <li class="tw-flex tw-items-start tw-gap-4">
                            <div class="tw-mt-1 tw-w-6 tw-h-6 tw-rounded-full tw-bg-emerald-500/20 tw-flex tw-items-center tw-justify-center tw-shrink-0">
                                <i class="fa fa-check tw-text-emerald-400 tw-text-[10px]"></i>
                            </div>
                            <span class="tw-leading-relaxed">The "Latest Tours" number affects the homepage and frontend dashboards display count limit.</span>
                        </li>
                        <li class="tw-flex tw-items-start tw-gap-4">
                            <div class="tw-mt-1 tw-w-6 tw-h-6 tw-rounded-full tw-bg-emerald-500/20 tw-flex tw-items-center tw-justify-center tw-shrink-0">
                                <i class="fa fa-check tw-text-emerald-400 tw-text-[10px]"></i>
                            </div>
                            <span class="tw-leading-relaxed">The Rating symbol changes the icon used universally for tour scoring (Stars, Diamonds, etc).</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('selected_icon').addEventListener('change', function() {
        document.getElementById('icon-preview').className = this.value + ' tw-drop-shadow-sm';
    });
</script>
@endsection
