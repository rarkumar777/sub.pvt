@extends('frontend.layout')

@section('title', 'Contact Us | PV Travels')
@section('meta_desc', 'Get in touch with PV Travels. Call us, email us or visit our office in Petra, Jordan.')

@section('content')
<!-- HERO -->
<section class="relative h-[320px] flex items-center justify-center overflow-hidden -mt-[92px]">
    <div class="absolute inset-0 z-0">
        <div class="w-full h-full bg-gradient-to-br from-gray-900 via-amber-950 to-gray-900"></div>
        <div class="absolute inset-0 opacity-20" style="background-image:url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.15\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    <div class="relative z-10 text-center text-white px-4">
        <nav class="flex items-center justify-center gap-2 text-white/60 text-xs font-black uppercase tracking-[0.2em] mb-5">
            <a href="/{{ $lang }}/" class="hover:text-amber-400 transition">Home</a>
            <span>/</span>
            <span class="text-white">Contact Us</span>
        </nav>
        <h1 class="text-5xl md:text-6xl font-black mb-3 leading-tight">
            Get In <span class="text-amber-400">Touch</span>
        </h1>
        <p class="text-lg text-white/70 font-medium">We'd love to hear from you. Our team is always here to help.</p>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="max-w-6xl mx-auto px-4 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- LEFT: Contact Info Cards -->
        <div class="space-y-6">

            <!-- Phone -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-7 flex items-start gap-5 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-13 h-13 bg-amber-100 rounded-2xl flex items-center justify-center shrink-0 p-3">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Telephone</p>
                    <a href="tel:+96277996601" class="text-lg font-black text-gray-900 hover:text-amber-600 transition" dir="ltr">+962 77996601</a>
                </div>
            </div>

            <!-- Fax -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-7 flex items-start gap-5 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-13 h-13 bg-blue-100 rounded-2xl flex items-center justify-center shrink-0 p-3">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">France (Local)</p>
                    <a href="tel:+33177624237" class="text-lg font-black text-gray-900 hover:text-blue-600 transition" dir="ltr">+33177624237</a>
                </div>
            </div>

            <!-- Email -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-7 flex items-start gap-5 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-13 h-13 bg-emerald-100 rounded-2xl flex items-center justify-center shrink-0 p-3">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Email</p>
                    <a href="mailto:info@pvt.jo" class="text-lg font-black text-gray-900 hover:text-emerald-600 transition">info@pvt.jo</a>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-7 flex items-start gap-5 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div class="w-13 h-13 bg-rose-100 rounded-2xl flex items-center justify-center shrink-0 p-3">
                    <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Address</p>
                    <p class="text-base font-bold text-gray-900 leading-snug">P.O.Box 43 Petra<br>71810 Jordan</p>
                </div>
            </div>

            <!-- Opening Hours -->
            <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl p-7 text-white shadow-lg">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-6 h-6 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs font-black uppercase tracking-widest text-white/80">Opening Hours</p>
                </div>
                <p class="font-black text-base">Monday to Saturday</p>
                <p class="text-white/80 font-semibold text-sm mt-1">10:00 AM — 10:00 PM</p>
            </div>
        </div>

        <!-- RIGHT: Contact Form + Map -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Success / Error Messages -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-semibold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl shadow-sm">
                    @foreach($errors->all() as $error)
                        <p class="font-semibold text-sm flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-10">
                <div class="mb-8">
                    <h2 class="text-3xl font-black text-gray-900">Send Us a Message</h2>
                    <p class="text-gray-500 text-sm mt-1">Fill in the form below and we'll get back to you within 24 hours.</p>
                </div>

                <form action="{{ route('frontend.contact-us.submit', ['lang' => $lang]) }}" method="POST" class="space-y-6" autocomplete="off">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Full Name <span class="text-red-400">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="John Doe" autocomplete="off"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-medium text-gray-800 bg-gray-50 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all outline-none placeholder:text-gray-400">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Email Address <span class="text-red-400">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com" autocomplete="off"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-medium text-gray-800 bg-gray-50 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all outline-none placeholder:text-gray-400">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+962 77X XXX XXX" autocomplete="off"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-medium text-gray-800 bg-gray-50 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all outline-none placeholder:text-gray-400">
                        </div>

                        <!-- Subject -->
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Subject</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" placeholder="How can we help?" autocomplete="off"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3.5 text-sm font-medium text-gray-800 bg-gray-50 focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all outline-none placeholder:text-gray-400">
                        </div>
                    </div>

                    <!-- Message with Quill Editor -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Message <span class="text-red-400">*</span></label>
                        <!-- Actual textarea (hidden, used for form submission) -->
                        <textarea id="contact_message" name="message" style="display:none;">{{ old('message') }}</textarea>
                        <!-- Quill editor visible container -->
                        <div id="quill_editor" style="min-height:200px; border:1px solid #e5e7eb; border-radius:0.75rem; background:#f9fafb; font-size:14px;"></div>
                        @error('message')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quill CSS + JS -->
                    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
                    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
                    <script>
                        // Wait for Quill to load
                        (function initQuill() {
                            if (typeof Quill === 'undefined') {
                                setTimeout(initQuill, 100);
                                return;
                            }

                            var quill = new Quill('#quill_editor', {
                                theme: 'snow',
                                placeholder: 'Tell us about your travel plans or how we can assist you...',
                                modules: {
                                    toolbar: [
                                        ['bold', 'italic', 'underline'],
                                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                        ['link'],
                                        ['clean']
                                    ]
                                }
                            });

                            // Restore old value if exists
                            var oldVal = document.getElementById('contact_message').value;
                            if (oldVal && oldVal.trim() !== '') {
                                quill.root.innerHTML = oldVal;
                            }

                            // Sync Quill to textarea on every keystroke
                            quill.on('text-change', function() {
                                document.getElementById('contact_message').value = quill.root.innerHTML;
                            });

                            // Final sync on form submit
                            document.querySelector('form').addEventListener('submit', function(e) {
                                var content = quill.root.innerHTML;
                                var text = quill.getText().trim();

                                if (!text || text === '') {
                                    e.preventDefault();
                                    alert('Please write a message before sending.');
                                    return false;
                                }
                                document.getElementById('contact_message').value = content;
                            });
                        })();
                    </script>

                    <!-- Submit -->
                    <button type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black py-4 rounded-2xl flex items-center justify-center gap-3 shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 hover:-translate-y-1 active:translate-y-0 transition-all duration-300 text-base uppercase tracking-wider">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Google Map -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-black text-gray-900">Our Location</h3>
                        <p class="text-sm text-gray-500">P.O.Box 43, Petra 71810, Jordan</p>
                    </div>
                    <a href="https://maps.google.com/?q=PV+Travels+Petra+Jordan" target="_blank" rel="noopener noreferrer"
                        class="flex items-center gap-2 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-sm px-4 py-2 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Open in Maps
                    </a>
                </div>
                <div class="h-72">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3432.4978571254657!2d35.47553!3d30.32849!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzDCsDE5JzQyLjYiTiAzNcKwMjgnMzIuMSJF!5e0!3m2!1sen!2sjo!4v1234567890"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
