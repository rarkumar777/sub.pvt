@extends('frontend.layout')
@section('title', 'Get a Quote | PVT')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">

    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-gray-900 mb-3">Get a Quote</h1>
        <p class="text-gray-500 max-w-xl mx-auto">Tell us about your dream trip and we'll create a custom itinerary just for you.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-8 flex items-center gap-3">
            <i data-lucide="check-circle" class="w-6 h-6 text-green-500 flex-shrink-0"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl mb-8">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2"><i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sm:p-8">
        <form method="POST" action="/{{ $lang }}/inquiry/">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Your name..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="you@@example.com" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+962..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Preferred Travel Date</label>
                    <input type="date" name="travel_date" min="{{ date('Y-m-d') }}" value="{{ old('travel_date') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Travelers</label>
                    <input type="number" name="travelers" value="{{ old('travelers', 1) }}" min="1" max="99" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                </div>
            </div>

            <div class="mt-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Message <span class="text-red-500">*</span></label>
                <textarea name="message" required rows="4" placeholder="Tell us about your dream trip..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all resize-y">{{ old('message') }}</textarea>
            </div>

            <div class="mt-5 flex items-end gap-4">
                <div class="flex-grow">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Verification <span class="text-red-500">*</span></label>
                    @php
                        $captchaCode = substr(md5(mt_rand()), 0, 4);
                        session(['inquiry_captcha_code' => $captchaCode]);
                    @endphp
                    <div class="flex items-stretch">
                        <div class="bg-blue-600 text-white font-mono font-bold text-lg px-4 flex items-center rounded-l-xl tracking-widest select-none">{{ $captchaCode }}</div>
                        <input type="text" name="captcha" required placeholder="Enter code..." class="flex-1 bg-gray-50 border border-gray-200 border-l-0 rounded-r-xl px-4 py-3 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 text-lg uppercase tracking-wide">
                    <i data-lucide="send" class="w-5 h-5"></i>
                    <span>Send Inquiry</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
