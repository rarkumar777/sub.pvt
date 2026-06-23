@extends('frontend.layout')

@section('title', __('Book Tour') . ': ' . ($content->title ?? 'Tour'))

@section('content')
@php
    $currSymbols = ['USD'=>'$','JOD'=>'JD','EUR'=>'€'];
    $currRates = ['USD'=>1,'JOD'=>0.709,'EUR'=>0.92];
    $sym = $currSymbols[$activeCurrency] ?? '$';
    $rate = $currRates[$activeCurrency] ?? 1;
    $displayPrice = round($tour->min_price * $rate);
@endphp
<!-- BOOKING HERO SECTION -->
<section class="relative h-[350px] flex items-center justify-center overflow-hidden -mt-[92px]">
    <div class="absolute inset-0 z-0">
        @php
            $imageSrc = !empty($tour->image) 
                ? (Str::startsWith($tour->image, ['http://', 'https://']) ? $tour->image : asset($tour->image))
                : asset('theme/pvt/video/Marvelous-Jordan-5.jpg');
        @endphp
        <img src="{{ $imageSrc }}" alt="Booking" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-[1px]"></div>
    </div>
    
    <div class="relative z-10 text-center text-white px-4">
        <nav class="flex items-center justify-center gap-2 text-white/60 text-xs font-black uppercase tracking-[0.2em] mb-6">
            <a href="/{{ $lang }}/" class="hover:text-orange-400 transition">{{ __('Portal') }}</a>
            <span>/</span>
            <span class="text-white">{{ __('Secure Booking') }}</span>
        </nav>
        <h1 class="text-4xl md:text-6xl font-black mb-4 leading-tight">{{ __('Complete Your') }} <span class="text-orange-400">{{ __('Booking') }}</span></h1>
        <p class="text-lg md:text-xl font-medium opacity-80 max-w-2xl mx-auto">{{ __('One last step to secure your unforgettable adventure in') }} {{ $startCountry->name ?? __('Jordan') }}.</p>
    </div>
</section>

<!-- BOOKING FORM SECTION -->
<section class="max-w-6xl mx-auto px-4 py-16 -mt-20 relative z-20">
    <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden">
        
        <div class="flex flex-col lg:flex-row">
            <!-- Left Side: Form Area -->
            <div class="lg:w-2/3 p-8 md:p-12">

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-8 flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-semibold text-sm">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl mb-8 flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-semibold text-sm">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl mb-8">
                        @foreach($errors->all() as $error)
                            <p class="font-semibold text-sm flex items-center gap-2"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div id="js-errors" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl mb-8 hidden"></div>
                <form id="bookingForm" action="/{{ $lang }}/tours/book_tour/{{ $tour->id }}/" method="POST" class="space-y-8" onsubmit="return validateBooking()">
                    @csrf
                    
                    <!-- PERSONAL INFORMATION -->
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Personal Information') }}</h2>
                        <div class="h-px flex-1 bg-gray-200"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Full Name') }} <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <input type="text" name="guest_name" value="{{ $user ? ($user->first_name . ' ' . $user->last_name) : '' }}" required placeholder="{{ __('John Doe') }}" class="w-full border border-gray-300 rounded-xl pl-12 pr-4 py-3.5 text-sm font-medium text-gray-800 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none placeholder:text-gray-400" {{ isset($isUser) && $isUser ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Email Address') }} <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <input type="email" name="email" value="{{ $user->email ?? '' }}" required placeholder="{{ __('john@example.com') }}" class="w-full border border-gray-300 rounded-xl pl-12 pr-4 py-3.5 text-sm font-medium text-gray-800 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none placeholder:text-gray-400" {{ isset($isUser) && $isUser ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Phone Number') }} <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <input type="tel" name="phone" value="{{ $user->phone ?? '' }}" required placeholder="{{ __('+962 77X XXX XXX') }}" class="w-full border border-gray-300 rounded-xl pl-12 pr-4 py-3.5 text-sm font-medium text-gray-800 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none placeholder:text-gray-400">
                            </div>
                        </div>
                    </div>

                    <!-- TRAVEL DETAILS -->
                    <div class="flex items-center gap-4 pt-4 mb-2">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Travel Details') }}</h2>
                        <div class="h-px flex-1 bg-gray-200"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Proposed Travel Date') }} <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <input type="date" name="travel_date" min="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded-xl pl-12 pr-4 py-3.5 text-sm font-medium text-gray-800 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Hotel Standard') }} <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <select name="hotel_grade" required class="w-full border border-gray-300 rounded-xl pl-12 pr-10 py-3.5 text-sm font-medium text-gray-800 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none appearance-none cursor-pointer">
                                    @foreach($availableGrades as $gradeValue => $gradeLabel)
                                    <option value="{{ $gradeValue }}" {{ $gradeValue == $priceBase ? 'selected' : '' }}>{{ __($gradeLabel) }}</option>
                                    @endforeach
                                </select>
                                <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- TRAVELERS COUNT -->
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Adults') }}</label>
                            <input type="number" name="adults" value="1" min="1" class="w-full border border-gray-300 rounded-xl px-4 py-3.5 text-center text-base font-black text-orange-600 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Children') }}</label>
                            <input type="number" name="children" value="0" min="0" class="w-full border border-gray-300 rounded-xl px-4 py-3.5 text-center text-base font-black text-orange-600 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Infants') }}</label>
                            <input type="number" name="infants" value="0" min="0" class="w-full border border-gray-300 rounded-xl px-4 py-3.5 text-center text-base font-black text-orange-600 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none">
                        </div>
                    </div>

                    <!-- ROOM PREFERENCES -->
                    <div class="flex items-center gap-4 pt-4 mb-2">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Room Preferences') }}</h2>
                        <div class="h-px flex-1 bg-gray-200"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Double Rooms') }}</label>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <input type="number" name="rooms_double" value="0" min="0" class="w-full border border-gray-300 rounded-xl pl-12 pr-4 py-3.5 text-sm font-medium text-gray-800 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Single Rooms') }}</label>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <input type="number" name="rooms_single" value="0" min="0" class="w-full border border-gray-300 rounded-xl pl-12 pr-4 py-3.5 text-sm font-medium text-gray-800 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Triple Rooms') }}</label>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <input type="number" name="rooms_triple" value="0" min="0" class="w-full border border-gray-300 rounded-xl pl-12 pr-4 py-3.5 text-sm font-medium text-gray-800 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- SPECIAL REQUESTS -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ __('Special Requests / Private Notes') }}</label>
                        <textarea name="note" rows="4" placeholder="{{ __('Tell us more about your preferences, allergies, or special occasions...') }}" class="w-full border border-gray-300 rounded-xl px-5 py-4 text-sm font-medium text-gray-800 bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all outline-none placeholder:text-gray-400 resize-none"></textarea>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl flex items-center justify-center gap-3 shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 transition-all text-base uppercase tracking-wider">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>{{ __('Secure Reservation') }}</span>
                        </button>
                    </div>
                </form>

                <script>
                function validateBooking() {
                    var errs = [];
                    var f = document.getElementById('bookingForm');
                    var errBox = document.getElementById('js-errors');

                    // Reset previous highlights
                    f.querySelectorAll('.border-red-400').forEach(function(el) { el.classList.remove('border-red-400'); });

                    var name = f.querySelector('[name=guest_name]');
                    if (!name.value.trim()) { errs.push('{{ __('Full Name is required.') }}'); name.classList.add('border-red-400'); }

                    var email = f.querySelector('[name=email]');
                    if (!email.value.trim()) { errs.push('{{ __('Email Address is required.') }}'); email.classList.add('border-red-400'); }
                    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) { errs.push('{{ __('Please enter a valid email address.') }}'); email.classList.add('border-red-400'); }

                    var phone = f.querySelector('[name=phone]');
                    if (!phone.value.trim()) { errs.push('{{ __('Phone number is required.') }}'); phone.classList.add('border-red-400'); }

                    var date = f.querySelector('[name=travel_date]');
                    if (!date.value) { errs.push('{{ __('Travel date is required.') }}'); date.classList.add('border-red-400'); }
                    else if (new Date(date.value) <= new Date()) { errs.push('{{ __('Travel date must be a future date.') }}'); date.classList.add('border-red-400'); }

                    var adults = parseInt(f.querySelector('[name=adults]').value) || 0;
                    if (adults < 1) { errs.push('{{ __('At least 1 adult is required.') }}'); f.querySelector('[name=adults]').classList.add('border-red-400'); }

                    var rd = parseInt(f.querySelector('[name=rooms_double]').value) || 0;
                    var rs = parseInt(f.querySelector('[name=rooms_single]').value) || 0;
                    var rt = parseInt(f.querySelector('[name=rooms_triple]').value) || 0;
                    if (rd + rs + rt < 1) {
                        errs.push('{{ __('Please select at least one room.') }}');
                        f.querySelector('[name=rooms_double]').classList.add('border-red-400');
                        f.querySelector('[name=rooms_single]').classList.add('border-red-400');
                        f.querySelector('[name=rooms_triple]').classList.add('border-red-400');
                    }

                    if (errs.length > 0) {
                        errBox.innerHTML = errs.map(function(e) { return '<p class="font-semibold text-sm flex items-center gap-2 mb-1"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>' + e + '</p>'; }).join('');
                        errBox.classList.remove('hidden');
                        errBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        return false;
                    }
                    errBox.classList.add('hidden');
                    return true;
                }
                </script>
            </div>

            <!-- Right Side: Booking Summary -->
            <div class="lg:w-1/3 bg-gray-50 p-8 border-l border-gray-200 flex flex-col">
                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ __('Summary') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('Review your selected tour package.') }}</p>
                </div>
                
                <div class="space-y-6 flex-1">
                    <!-- Tour Info Card -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Selected Experience') }}</p>
                        <h4 class="text-lg font-bold text-gray-900 leading-snug mb-4">{!! htmlspecialchars_decode($content->title ?? $tour->title ?? '') !!}</h4>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 p-3 rounded-xl">
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">{{ __('Duration') }}</p>
                                <p class="text-sm font-bold text-gray-900">{{ $tour->days }} {{ __('Days') }}</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-xl">
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">{{ __('Tour ID') }}</p>
                                <p class="text-sm font-bold text-gray-900">#{{ $tour->id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- PRICE BOX -->
                    <div class="bg-orange-600 p-6 rounded-2xl shadow-lg">
                        <p class="text-[10px] font-bold text-white/60 uppercase tracking-widest mb-2 text-center">{{ __('Price Starts From') }}</p>
                        <div class="flex items-center justify-center gap-2">
                            <span class="text-4xl font-black text-white leading-none">{{ $sym }}{{ number_format($displayPrice, 0) }}</span>
                            <span class="text-xs font-bold text-white/70">/ {{ __('person') }}</span>
                        </div>
                    </div>

                    <!-- Trust Signals -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-white transition-colors">
                            <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ __('Secure Transaction') }}</p>
                                <p class="text-[11px] text-gray-500">{{ __('256-bit encrypted checkout') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-white transition-colors">
                            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ __('Free Cancellation') }}</p>
                                <p class="text-[11px] text-gray-500">{{ __('Cancel up to 48h before') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-white transition-colors">
                            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ __('24/7 Support') }}</p>
                                <p class="text-[11px] text-gray-500">{{ __('Always here to help') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <img src="{{ asset('Pvtnew1.png') }}" alt="PV Travels" class="h-8 opacity-50 mx-auto grayscale">
                    <p class="text-[10px] font-bold text-gray-400 mt-3 uppercase tracking-widest">{{ __('Authorized Agency') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HELP CENTER -->
<div class="max-w-6xl mx-auto px-4 mt-8 pb-16">
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl p-8 md:p-10 text-white shadow-xl">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="flex-1 text-center md:text-left">
                <p class="text-xs font-bold text-orange-400 uppercase tracking-widest mb-2">{{ __('Need Help?') }}</p>
                <h3 class="text-2xl md:text-3xl font-bold mb-2">{{ __('Dedicated Support') }}</h3>
                <p class="text-gray-400 text-sm">{{ __('Our local travel experts are available around the clock.') }}</p>
            </div>
            <a href="tel:+962797682220" class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-4 rounded-xl text-center text-lg font-bold shadow-lg transition-all hover:-translate-y-0.5">
                +962 7976 82220
            </a>
        </div>
    </div>
</div>
<style>
/* ── Booking Form Input Enhancements ── */
.lg\:w-2\/3 input[type="text"],
.lg\:w-2\/3 input[type="email"],
.lg\:w-2\/3 input[type="tel"],
.lg\:w-2\/3 input[type="date"],
.lg\:w-2\/3 input[type="number"],
.lg\:w-2\/3 select,
.lg\:w-2\/3 textarea {
    background-color: #ffffff !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 0.85rem !important;
    font-size: 0.95rem !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    padding-top: 0.875rem !important;
    padding-bottom: 0.875rem !important;
    padding-left: 1rem !important;
    padding-right: 1rem !important;
}

/* Specific padding for fields with icons on the left */
.lg\:w-2\/3 .relative input[type="text"],
.lg\:w-2\/3 .relative input[type="email"],
.lg\:w-2\/3 .relative input[type="tel"],
.lg\:w-2\/3 .relative input[type="date"],
.lg\:w-2\/3 .relative input[type="number"],
.lg\:w-2\/3 .relative select {
    padding-left: 3.25rem !important;
}

.lg\:w-2\/3 input:hover,
.lg\:w-2\/3 select:hover,
.lg\:w-2\/3 textarea:hover {
    border-color: #9ca3af !important;
    background-color: #fff !important;
}

.lg\:w-2\/3 input:focus,
.lg\:w-2\/3 select:focus,
.lg\:w-2\/3 textarea:focus {
    border-color: #f97316 !important;
    background-color: #fff !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15), 0 1px 3px rgba(0,0,0,0.04) !important;
}

/* Icon separator line */
.lg\:w-2\/3 .relative svg:first-child {
    color: #6b7280 !important;
    opacity: 0.7;
}

.lg\:w-2\/3 .relative:has(input:focus) svg:first-child,
.lg\:w-2\/3 .relative:has(select:focus) svg:first-child {
    color: #f97316 !important;
    opacity: 1;
}

/* Number inputs: hide default spinner, add custom styling */
.lg\:w-2\/3 input[type="number"] {
    -moz-appearance: textfield;
    font-weight: 700 !important;
    font-size: 16px !important;
}

.lg\:w-2\/3 input[type="number"]::-webkit-inner-spin-button,
.lg\:w-2\/3 input[type="number"]::-webkit-outer-spin-button {
    opacity: 1;
    height: 36px;
}

/* Select dropdown enhancement */
.lg\:w-2\/3 select {
    cursor: pointer !important;
}

/* Textarea enhancement */
.lg\:w-2\/3 textarea {
    min-height: 100px;
    line-height: 1.6;
}

/* Labels */
.lg\:w-2\/3 label {
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 11px !important;
    letter-spacing: 0.08em !important;
}

/* Readonly inputs */
.lg\:w-2\/3 input[readonly] {
    background-color: #f1f5f9 !important;
    color: #64748b !important;
    cursor: not-allowed;
}

/* Hide native date picker icon */
.lg\:w-2\/3 input[type="date"]::-webkit-calendar-picker-indicator {
    background: transparent;
    bottom: 0;
    color: transparent;
    cursor: pointer;
    height: auto;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
    width: auto;
}

/* Hide native select arrow more aggressively */
.lg\:w-2\/3 select {
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-image: none !important;
    padding-right: 3rem !important; /* Clear custom arrow */
}

/* Perfecting separators */
.lg\:w-2\/3 h2 + div {
    background-color: #e2e8f0 !important;
    height: 1px !important;
    margin-left: 1rem;
}
</style>
@endsection
