@extends('admin.layouts.app')
@section('title', 'Admin | New Booking')

@section('content')
<div class="tw-max-w-7xl tw-mx-auto tw-mb-12 tw-font-sans">
    
    {{-- Material 3 Header --}}
    <header class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4 tw-mb-8">
        <div>
            <nav class="tw-flex tw-text-xs tw-font-medium tw-text-slate-500 tw-mb-2 tw-uppercase tw-tracking-wider" aria-label="Breadcrumb">
                <ol class="tw-inline-flex tw-items-center">
                    <li><a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-600 tw-transition-colors tw-no-underline">Dashboard</a></li>
                    <li><i class="fa fa-chevron-right tw-mx-2 tw-text-[10px] tw-text-slate-400"></i></li>
                    <li><a href="{{ route('admin.bookings.index') }}" class="hover:tw-text-orange-600 tw-transition-colors tw-no-underline">Bookings</a></li>
                    <li><i class="fa fa-chevron-right tw-mx-2 tw-text-[10px] tw-text-slate-400"></i></li>
                    <li class="tw-text-orange-600 tw-font-bold">New Booking</li>
                </ol>
            </nav>
            <h1 class="tw-text-3xl tw-font-normal tw-tracking-tight tw-text-slate-900 tw-m-0">
                Add New Booking
            </h1>
        </div>
        <div class="tw-flex tw-items-center tw-gap-3">
            <a href="{{ route('admin.bookings.index') }}" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-full tw-bg-white tw-px-5 tw-py-2 tw-text-sm tw-font-medium tw-text-slate-700 tw-shadow-sm tw-border tw-border-slate-300 hover:tw-bg-slate-50 tw-transition tw-no-underline">
                <i class="fa fa-arrow-left"></i> Back
            </a>
            <button type="button" onclick="document.getElementById('booking-form').submit()" class="tw-inline-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-full tw-bg-orange-600 tw-px-6 tw-py-2 tw-text-sm tw-font-medium tw-text-white tw-shadow hover:tw-bg-orange-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-orange-500 tw-transition tw-border-0">
                <i class="fa fa-check"></i> Save
            </button>
        </div>
    </header>

    @if($errors->any())
    <div class="tw-rounded-2xl tw-bg-red-50 tw-p-4 tw-mb-8">
        <div class="tw-flex">
            <div class="tw-flex-shrink-0">
                <i class="fa fa-exclamation-circle tw-text-red-500 tw-text-lg mt-0.5"></i>
            </div>
            <div class="tw-ml-3">
                <h3 class="tw-text-sm tw-font-medium tw-text-red-800 tw-m-0">Please fix the following errors:</h3>
                <div class="tw-mt-2 tw-text-sm tw-text-red-700">
                    <ul class="tw-list-disc tw-space-y-1 tw-pl-5 tw-m-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <form id="booking-form" method="POST" action="{{ route('admin.bookings.store') }}" class="tw-flex tw-flex-col lg:tw-flex-row tw-gap-6 tw-items-start">
        @csrf

        {{-- Left Column: Core Fields --}}
        <div class="tw-flex-1 tw-flex tw-flex-col tw-gap-6 tw-w-full lg:tw-w-2/3">
            
            {{-- Material 3 Card: Client Overview --}}
            <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                <div class="tw-px-8 tw-py-6 tw-flex tw-items-center tw-gap-4 tw-border-b tw-border-slate-50">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-orange-50 tw-text-orange-600 tw-flex tw-items-center tw-justify-center">
                        <i class="fa fa-user"></i>
                    </div>
                    <div>
                        <h3 class="tw-text-lg tw-font-medium tw-text-slate-900 tw-m-0">Client Information</h3>
                        <p class="tw-text-sm tw-text-slate-500 tw-m-0">Guest details and primary contact.</p>
                    </div>
                </div>
                <div class="tw-p-8">
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-6">
                        
                        <div class="tw-col-span-2 tw-relative">
                            <input type="email" name="user_email" id="user" value="{{ $copyBooking && $copyBooking->user ? $copyBooking->user->email : old('user_email') }}" 
                                   class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer tw-transition-colors"
                                   placeholder=" ">
                            <label for="user" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-peer-focus:tw-text-orange-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3">
                                User Email <span id="check_user_msg" class="tw-ml-2"></span>
                            </label>
                        </div>

                        <div class="tw-relative">
                            <input type="text" id="first_name" value="{{ $copyBooking && $copyBooking->user ? $copyBooking->user->first_name : '' }}" disabled
                                   class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-500 tw-bg-slate-100 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-200 tw-appearance-none tw-cursor-not-allowed tw-peer" placeholder=" ">
                            <label for="first_name" class="tw-absolute tw-text-sm tw-text-slate-400 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4">First Name</label>
                        </div>

                        <div class="tw-relative">
                            <input type="text" id="last_name" value="{{ $copyBooking && $copyBooking->user ? $copyBooking->user->last_name : '' }}" disabled
                                   class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-500 tw-bg-slate-100 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-200 tw-appearance-none tw-cursor-not-allowed tw-peer" placeholder=" ">
                            <label for="last_name" class="tw-absolute tw-text-sm tw-text-slate-400 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4">Last Name</label>
                        </div>

                        <div class="tw-col-span-2 tw-relative">
                            <input type="text" id="company" value="{{ $copyBooking && $copyBooking->user ? $copyBooking->user->company : '' }}" disabled
                                   class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-500 tw-bg-slate-100 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-200 tw-appearance-none tw-cursor-not-allowed tw-peer" placeholder=" ">
                            <label for="company" class="tw-absolute tw-text-sm tw-text-slate-400 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4">Company</label>
                        </div>
                        
                        <div class="tw-col-span-2 tw-py-2"><hr class="tw-border-slate-100 tw-m-0"></div>

                        <div class="tw-col-span-2 tw-relative">
                            <input type="text" name="title" id="title" value="{{ $copyBooking && $copyBooking->invoice ? html_entity_decode($copyBooking->invoice->desc) : old('title') }}" 
                                   class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer tw-transition-colors" placeholder=" ">
                            <label for="title" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-peer-focus:tw-text-orange-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3">Tour Title</label>
                        </div>

                        <div class="tw-col-span-2 tw-relative">
                            <input type="text" name="guest_name" id="guest_name" value="{{ $copyBooking ? $copyBooking->guest_name : old('guest_name') }}" 
                                   class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer tw-transition-colors" placeholder=" ">
                            <label for="guest_name" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-peer-focus:tw-text-orange-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3">Guest / Reference Name</label>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Material 3 Card: Inclusions --}}
            <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                <div class="tw-px-8 tw-py-6 tw-flex tw-items-center tw-gap-4 tw-border-b tw-border-slate-50">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-orange-50 tw-text-orange-600 tw-flex tw-items-center tw-justify-center">
                        <i class="fa fa-list"></i>
                    </div>
                    <div>
                        <h3 class="tw-text-lg tw-font-medium tw-text-slate-900 tw-m-0">Inclusions Matrix</h3>
                        <p class="tw-text-sm tw-text-slate-500 tw-m-0">Configure included/excluded services.</p>
                    </div>
                </div>
                <div class="tw-p-8">
                    <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-4 tw-gap-6">
                        @foreach($inclusions as $inc)
                        <div class="tw-relative">
                            <select name="inclusion_{{ $inc->lang_id }}" id="inc_{{ $inc->lang_id }}" class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer m3-select">
                                <option value="disabled" selected>Disabled</option>
                                <option value="included">Included</option>
                                <option value="excluded">Excluded</option>
                            </select>
                            <label for="inc_{{ $inc->lang_id }}" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-truncate tw-max-w-[80%]" title="{{ $inc->name }}">{{ $inc->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column: Settings & Config --}}
        <div class="tw-w-full lg:tw-w-1/3 tw-flex-shrink-0 tw-flex tw-flex-col tw-gap-6">
            
            {{-- Material Logistics --}}
            <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                <div class="tw-px-8 tw-py-6 tw-flex tw-items-center tw-gap-4 tw-border-b tw-border-slate-50">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-orange-50 tw-text-orange-600 tw-flex tw-items-center tw-justify-center">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <div>
                        <h3 class="tw-text-lg tw-font-medium tw-text-slate-900 tw-m-0">Logistics</h3>
                    </div>
                </div>
                <div class="tw-p-8 tw-space-y-6">
                    <div class="tw-relative">
                        <input type="text" name="date" id="date" value="{{ $copyBooking ? $copyBooking->travel_date : old('date', date('Y-m-d')) }}" 
                               class="datepicker tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer tw-transition-colors" placeholder=" ">
                        <label for="date" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-peer-focus:tw-text-orange-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3">Departure Date</label>
                        <i class="fa fa-calendar tw-absolute tw-right-4 tw-top-4 tw-text-slate-400 tw-pointer-events-none"></i>
                    </div>

                    <div class="tw-grid tw-grid-cols-2 tw-gap-4">
                        <div class="tw-relative">
                            <input type="number" name="days" id="days" value="{{ $copyBooking ? $copyBooking->days : old('days', 1) }}" 
                                   class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer tw-transition-colors" placeholder=" ">
                            <label for="days" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-peer-focus:tw-text-orange-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3">Days</label>
                        </div>
                        <div class="tw-relative">
                            <input type="number" name="nights" id="nights" value="{{ $copyBooking ? $copyBooking->nights : old('nights', 0) }}" 
                                   class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer tw-transition-colors" placeholder=" ">
                            <label for="nights" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-peer-focus:tw-text-orange-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3">Nights</label>
                        </div>
                    </div>

                    <div class="tw-relative">
                        <select name="start_country" id="start_country" class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer m3-select">
                            @foreach($countries as $cid => $cname)
                            <option value="{{ $cid }}" {{ old('start_country', $copyBooking ? $copyBooking->start_country : 0) == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                            @endforeach
                        </select>
                        <label for="start_country" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4">Operations Country</label>
                    </div>
                </div>
            </div>

            {{-- Material Pax & Price --}}
            <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                <div class="tw-px-8 tw-py-6 tw-flex tw-items-center tw-gap-4 tw-border-b tw-border-slate-50">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-orange-50 tw-text-orange-600 tw-flex tw-items-center tw-justify-center">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>
                        <h3 class="tw-text-lg tw-font-medium tw-text-slate-900 tw-m-0">Pax & Pricing</h3>
                    </div>
                </div>
                <div class="tw-p-8 tw-space-y-6">
                    <div class="tw-grid tw-grid-cols-2 tw-gap-6">
                        <div class="tw-rounded-xl tw-bg-slate-50 tw-p-4 tw-border-b-2 tw-border-slate-200">
                            <label class="tw-block tw-text-xs tw-font-medium tw-text-slate-500 tw-mb-2">ADULTS (PAX)</label>
                            <input type="text" name="adult" inputmode="numeric" pattern="[0-9]*" value="{{ $copyBooking ? $copyBooking->adult : old('adult', 1) }}" class="tw-block tw-w-full tw-bg-transparent tw-border-0 tw-p-0 tw-text-slate-900 focus:tw-ring-0 tw-text-xl tw-font-medium tw-text-center tw-mb-2">
                            <div class="tw-relative">
                                <input type="text" name="price_adult" value="{{ old('price_adult', '0.00') }}" class="tw-block tw-w-full tw-bg-transparent tw-border-0 tw-border-b tw-border-slate-300 focus:tw-border-orange-600 tw-p-0 tw-text-orange-600 tw-font-medium tw-text-center tw-text-sm focus:tw-ring-0 pb-1" placeholder="0.00 JOD">
                            </div>
                        </div>

                        <div class="tw-rounded-xl tw-bg-slate-50 tw-p-4 tw-border-b-2 tw-border-slate-200">
                            <label class="tw-block tw-text-xs tw-font-medium tw-text-slate-500 tw-mb-2">CHILDREN (PAX)</label>
                            <input type="text" name="child" inputmode="numeric" pattern="[0-9]*" value="{{ $copyBooking ? $copyBooking->child : old('child', 0) }}" class="tw-block tw-w-full tw-bg-transparent tw-border-0 tw-p-0 tw-text-slate-900 focus:tw-ring-0 tw-text-xl tw-font-medium tw-text-center tw-mb-2">
                            <div class="tw-relative">
                                <input type="text" name="price_child" value="{{ old('price_child', '0.00') }}" class="tw-block tw-w-full tw-bg-transparent tw-border-0 tw-border-b tw-border-slate-300 focus:tw-border-orange-600 tw-p-0 tw-text-orange-600 tw-font-medium tw-text-center tw-text-sm focus:tw-ring-0 pb-1" placeholder="0.00 JOD">
                            </div>
                        </div>
                    </div>
                    
                    <div class="tw-relative">
                        <textarea name="notes" id="notes" class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer tw-transition-colors tw-min-h-[100px]" placeholder=" ">{{ $copyBooking ? $copyBooking->note : old('notes') }}</textarea>
                        <label for="notes" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4 tw-peer-focus:tw-text-orange-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3">Internal Booking Notes</label>
                    </div>
                </div>
            </div>
            
            {{-- Material Rooms --}}
            <div class="tw-bg-white tw-rounded-3xl tw-shadow-sm tw-border tw-border-slate-100 tw-overflow-hidden">
                <div class="tw-px-8 tw-py-6 tw-flex tw-items-center tw-gap-4 tw-border-b tw-border-slate-50">
                    <div class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-orange-50 tw-text-orange-600 tw-flex tw-items-center tw-justify-center">
                        <i class="fa fa-bed"></i>
                    </div>
                    <div>
                        <h3 class="tw-text-lg tw-font-medium tw-text-slate-900 tw-m-0">Rooms</h3>
                    </div>
                </div>
                <div class="tw-p-8">
                    <div class="tw-relative tw-mb-6">
                        <select name="hotel_grade" id="hotel_grade" class="tw-block tw-px-4 tw-pb-2.5 tw-pt-6 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-lg tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer m3-select">
                            @foreach($accommodations as $val => $label)
                            <option value="{{ $val }}" {{ old('hotel_grade', $copyBooking ? $copyBooking->hotel_grade : 0) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <label for="hotel_grade" class="tw-absolute tw-text-sm tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-4 tw-z-10 tw-origin-[0] tw-start-4">Accommodation Level</label>
                    </div>
                    
                    <div class="tw-grid tw-grid-cols-5 tw-gap-3">
                        @foreach(['SGL'=>'single','DBL'=>'double','TWN'=>'twin','TPL'=>'triple','QUD'=>'quad'] as $label => $name)
                        <div class="tw-relative">
                            <input type="text" name="{{ $name }}" id="{{ $name }}" inputmode="numeric" pattern="[0-9]*" value="{{ old($name, 0) }}" 
                                   class="tw-block tw-px-1 tw-pb-2 tw-pt-5 tw-w-full tw-text-sm tw-text-slate-900 tw-bg-slate-50 tw-rounded-t-md tw-border-0 tw-border-b-2 tw-border-slate-300 tw-appearance-none focus:tw-outline-none focus:tw-ring-0 focus:tw-border-orange-600 tw-peer tw-text-center tw-font-medium" placeholder=" ">
                            <label for="{{ $name }}" class="tw-absolute tw-text-[10px] tw-text-slate-500 tw-duration-300 tw-transform -tw-translate-y-3 tw-scale-75 tw-top-3 tw-z-10 tw-origin-[0] tw-left-1/2 -tw-translate-x-1/2 tw-peer-focus:tw-text-orange-600 tw-peer-placeholder-shown:tw-scale-100 tw-peer-placeholder-shown:tw-translate-y-0 tw-peer-focus:tw-scale-75 tw-peer-focus:-tw-translate-y-3">{{ $label }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Material Action Container --}}
            <div class="tw-flex tw-flex-col tw-gap-4 tw-pl-2">
                <label class="tw-flex tw-items-center tw-cursor-pointer tw-gap-3 group">
                    <div class="tw-relative tw-flex tw-items-center">
                        <input type="checkbox" name="send_invoice_note" id="send_invoice_note" value="1" class="tw-peer tw-appearance-none tw-w-5 tw-h-5 tw-border-2 tw-border-slate-400 tw-rounded tw-bg-transparent hover:tw-bg-slate-100 checked:tw-bg-orange-600 checked:tw-border-orange-600 tw-transition-all tw-cursor-pointer">
                        <i class="fa fa-check tw-absolute tw-left-1/2 tw-top-1/2 -tw-translate-x-1/2 -tw-translate-y-1/2 tw-text-white tw-text-[10px] tw-opacity-0 peer-checked:tw-opacity-100 tw-pointer-events-none tw-transition-opacity"></i>
                    </div>
                    <span class="tw-text-sm tw-font-medium tw-text-slate-700">Email invoice note to user directly</span>
                </label>
                
                <button type="submit" class="tw-mt-2 tw-w-full tw-flex tw-items-center tw-justify-center tw-gap-2 tw-rounded-full tw-bg-orange-600 tw-px-6 tw-py-4 tw-text-sm tw-font-medium tw-text-white tw-shadow-md hover:tw-shadow-lg hover:tw-bg-orange-700 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-orange-500 focus:tw-ring-offset-2 tw-transition-all tw-border-0 tw-uppercase tw-tracking-widest">
                    <i class="fa fa-save"></i> Save Booking Record
                </button>
            </div>

        </div>
    </form>
</div>

<style>
    /* Absolute Material Stylings */
    .datepicker { cursor: pointer; }
    .m3-select { 
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); 
        background-position: right 0.75rem center; 
        background-repeat: no-repeat; 
        background-size: 1.5em 1.5em; 
        padding-right: 2.5rem; 
    }
</style>

<script>
    var userField = document.getElementById('user');
    if (userField) {
        userField.addEventListener('blur', function() {
            var email = this.value;
            if (email) {
                const msgEl = document.getElementById('check_user_msg');
                msgEl.innerHTML = '<i class="fa fa-circle-o-notch fa-spin tw-text-orange-500"></i>';
                fetch('/admin/ajax/check-user?email=' + encodeURIComponent(email))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.found) {
                            document.getElementById('first_name').value = data.first_name || '';
                            document.getElementById('last_name').value = data.last_name || '';
                            document.getElementById('company').value = data.company || '';
                            msgEl.innerHTML = '<span class="tw-text-emerald-600 tw-font-bold tw-text-[10px] tw-uppercase"><i class="fa fa-check"></i> Found</span>';
                        } else {
                            msgEl.innerHTML = '<span class="tw-text-red-500 tw-font-bold tw-text-[10px] tw-uppercase"><i class="fa fa-times"></i> Guest</span>';
                        }
                    }).catch(function() { msgEl.innerHTML = ''; });
            }
        });
    }

    document.querySelectorAll('.datepicker').forEach(function(el) { el.removeAttribute('readonly'); });
</script>
@endsection
