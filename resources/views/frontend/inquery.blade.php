@extends('frontend.layout')
@section('title', ($lang == 'fr' ? 'Personnaliser le Tour' : 'Customize Tour') . ' | ' . ($content->title ?? 'Tour'))

@php
    // Translations
    $t = [
        'en' => [
            'customize_tour' => 'Customize the Tour',
            'step1' => 'Customize Trip Details',
            'step2' => 'Send Your Modifications',
            'step3' => 'We Will Reply Quickly',
            'name' => 'Name...',
            'email' => 'E-mail...',
            'phone' => 'Telephone...',
            'date' => 'Date...',
            'hotel_cat' => 'Hotel Category',
            'select_hotel' => 'Select Hotel',
            'no_hotel' => 'No Hotel',
            'adult' => 'Adult:',
            'child' => 'Child:',
            'baby' => 'Baby:',
            'rooms' => 'Hotel Rooms',
            'single' => 'Single:',
            'double' => 'Double:',
            'triple' => 'Triple:',
            'days' => 'Days:',
            'nights' => 'Nights:',
            'robot' => 'Robot Verification',
            'code_hint' => 'code from the left blue box',
            'send' => 'Send',
            'inclusions' => 'Inclusions',
        ],
        'fr' => [
            'customize_tour' => 'Personnaliser le Tour',
            'step1' => 'Personnalisez les Détails du Voyage',
            'step2' => 'Envoyez vos Modifications',
            'step3' => 'Nous Vous Répondrons Rapidement',
            'name' => 'Nom...',
            'email' => 'E-mail...',
            'phone' => 'Téléphone...',
            'date' => 'Date...',
            'hotel_cat' => "Catégorie d'Hôtel",
            'select_hotel' => "Catégorie d'Hôtel",
            'no_hotel' => 'Pas d\'hôtel',
            'adult' => 'Adulte :',
            'child' => 'Enfant :',
            'baby' => 'Bébé :',
            'rooms' => "Chambres d'Hôtel",
            'single' => 'Célibataire:',
            'double' => 'Double:',
            'triple' => 'Tripler:',
            'days' => 'Jours:',
            'nights' => 'Nuits:',
            'robot' => 'Robot Verification',
            'code_hint' => 'code from the left blue box',
            'send' => 'Envoyer',
            'inclusions' => 'Inclusions',
        ],
    ];
    $tr = $t[$lang] ?? $t['en'];
@endphp

@section('content')
<div class="body-wrap">
<div class="wrap pad" style="max-width:1095px; margin:auto; padding: 20px 10px;">

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Page Title --}}
    <h1 style="font-size:24px; font-weight:bold; margin:0 0 15px 0; color:#333;">
        <i class="fa-edit" style="color:#c77b2e;"></i> {{ $tr['customize_tour'] }}
    </h1>

    {{-- 3-Step Progress Indicator --}}
    <div style="background:#f5f0e8; border-radius:8px; padding:15px 20px; margin-bottom:20px; display:flex; justify-content:space-around; align-items:center; flex-wrap:wrap; gap:10px;">
        <div style="text-align:center; flex:1;">
            <div style="margin-bottom:5px;"><i class="fa-edit" style="font-size:20px; color:#c77b2e;"></i></div>
            <div style="font-size:12px; font-weight:600; color:#c77b2e;">{{ $tr['step1'] }}</div>
        </div>
        <div style="text-align:center; flex:1;">
            <div style="margin-bottom:5px;"><i class="fa-paper-plane" style="font-size:20px; color:#4a90a4;"></i></div>
            <div style="font-size:12px; font-weight:600; color:#4a90a4;">{{ $tr['step2'] }}</div>
        </div>
        <div style="text-align:center; flex:1;">
            <div style="margin-bottom:5px;"><i class="fa-comments" style="font-size:20px; color:#c77b2e;"></i></div>
            <div style="font-size:12px; font-weight:600; color:#c77b2e;">{{ $tr['step3'] }}</div>
        </div>
    </div>

    <form method="post" action="/{{ $lang }}/tours/inquery/{{ $tour->id }}/" id="inqueryForm">
        @csrf

        {{-- Two-Column Layout --}}
        <div style="display:flex; gap:20px; flex-wrap:wrap;">

            {{-- LEFT COLUMN: Tour Title + TinyMCE Editor --}}
            <div style="flex:1; min-width:300px;">
                <h2 style="font-size:20px; font-weight:bold; margin:0 0 12px 0; color:#333;">
                    <i class="fa-th" style="color:#c77b2e;"></i> {{ $content->title ?? 'Tour' }}
                </h2>

                <textarea name="desc" id="inqueryEditor" class="tinymce" style="width:100%; min-height:300px;">{!! $content->itinerary ?? '' !!}</textarea>
            </div>

            {{-- RIGHT COLUMN: Sidebar Form --}}
            <div style="width:260px; flex-shrink:0;">
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <input type="text" name="name" placeholder="{{ $tr['name'] }}" required
                           style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:4px; font-size:13px; box-sizing:border-box;">

                    <input type="email" name="email" placeholder="{{ $tr['email'] }}" required
                           style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:4px; font-size:13px; box-sizing:border-box;">

                    <input type="text" name="telephone" placeholder="{{ $tr['phone'] }}"
                           style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:4px; font-size:13px; box-sizing:border-box;">

                    <div style="position:relative;">
                        <input type="text" name="date" placeholder="{{ $tr['date'] }}" class="datepicker" data-disable-dates="past" required
                               style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:4px; font-size:13px; box-sizing:border-box;">
                        <i class="fa-calendar" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#c77b2e; pointer-events:none;"></i>
                    </div>

                    {{-- Hotel Grade --}}
                    <select name="hotel_grade" style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:4px; font-size:13px; box-sizing:border-box; background:#fff;">
                        <option value="0">{{ $tr['select_hotel'] }}</option>
                        <option value="0">{{ $tr['no_hotel'] }}</option>
                        <option value="1">1 ★</option>
                        <option value="2">2 ★</option>
                        <option value="3">3 ★</option>
                        <option value="4">4 ★</option>
                        <option value="5">5 ★</option>
                    </select>

                    {{-- Participants --}}
                    <div style="display:flex; gap:8px; align-items:center;">
                        <div style="flex:1; text-align:center;">
                            <label style="font-size:11px; font-weight:600; color:#555; display:block; margin-bottom:3px;">{{ $tr['adult'] }}</label>
                            <input type="number" name="adult" value="1" min="1" max="99"
                                   style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:center; box-sizing:border-box;">
                        </div>
                        <div style="flex:1; text-align:center;">
                            <label style="font-size:11px; font-weight:600; color:#555; display:block; margin-bottom:3px;">{{ $tr['child'] }}</label>
                            <input type="number" name="child" value="0" min="0" max="99"
                                   style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:center; box-sizing:border-box;">
                        </div>
                        <div style="flex:1; text-align:center;">
                            <label style="font-size:11px; font-weight:600; color:#555; display:block; margin-bottom:3px;">{{ $tr['baby'] }}</label>
                            <input type="number" name="infant" value="0" min="0" max="99"
                                   style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:center; box-sizing:border-box;">
                        </div>
                    </div>

                    {{-- Hotel Rooms --}}
                    <div style="margin-top:5px;">
                        <label style="font-size:12px; font-weight:700; color:#333; display:block; margin-bottom:5px;">{{ $tr['rooms'] }}</label>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <div style="flex:1; text-align:center;">
                                <label style="font-size:10px; font-weight:600; color:#555; display:block; margin-bottom:3px;">{{ $tr['single'] }}</label>
                                <input type="number" name="hotel_room_single" value="0" min="0" max="50"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:center; box-sizing:border-box;">
                            </div>
                            <div style="flex:1; text-align:center;">
                                <label style="font-size:10px; font-weight:600; color:#555; display:block; margin-bottom:3px;">{{ $tr['double'] }}</label>
                                <input type="number" name="hotel_room_double" value="0" min="0" max="50"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:center; box-sizing:border-box;">
                            </div>
                            <div style="flex:1; text-align:center;">
                                <label style="font-size:10px; font-weight:600; color:#555; display:block; margin-bottom:3px;">{{ $tr['triple'] }}</label>
                                <input type="number" name="hotel_room_triple" value="0" min="0" max="50"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:center; box-sizing:border-box;">
                            </div>
                        </div>
                    </div>

                    {{-- Days / Nights --}}
                    <div style="display:flex; gap:8px; align-items:center; margin-top:5px;">
                        <div style="flex:1; text-align:center;">
                            <label style="font-size:11px; font-weight:600; color:#555; display:block; margin-bottom:3px;">{{ $tr['days'] }}</label>
                            <input type="number" name="days" value="{{ $tour->days ?? 1 }}" min="1" max="60"
                                   style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:center; box-sizing:border-box;">
                        </div>
                        <div style="flex:1; text-align:center;">
                            <label style="font-size:11px; font-weight:600; color:#555; display:block; margin-bottom:3px;">{{ $tr['nights'] }}</label>
                            <input type="number" name="nights" value="{{ $tour->nights ?? 0 }}" min="0" max="60"
                                   style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:center; box-sizing:border-box;">
                        </div>
                    </div>

                    {{-- Robot Verification --}}
                    <div style="margin-top:8px;">
                        <label style="font-size:12px; font-weight:700; color:#333; display:block; margin-bottom:5px;">{{ $tr['robot'] }}</label>
                        @php
                            $captchaCode = substr(md5(mt_rand()), 0, 4);
                            session(['tour_captcha_code' => $captchaCode]);
                        @endphp
                        <div style="display:flex; align-items:stretch;">
                            <div style="background:#369; color:#fff; font-family:monospace; font-weight:bold; font-size:18px; padding:8px 12px; border-radius:4px 0 0 4px; display:flex; align-items:center; user-select:none; letter-spacing:3px;">{{ $captchaCode }}</div>
                            <input type="text" name="captcha" placeholder="{{ $tr['code_hint'] }}" required
                                   style="flex:1; padding:8px 10px; border:1px solid #ddd; border-left:none; border-radius:0 4px 4px 0; font-size:12px; box-sizing:border-box;">
                        </div>
                    </div>

                    {{-- Send Button --}}
                    <button type="submit" onclick="if(typeof tinymce !== 'undefined') tinymce.triggerSave();"
                            style="width:100%; padding:12px; background:#c77b2e; color:#fff; border:none; border-radius:4px; font-size:14px; font-weight:bold; cursor:pointer; margin-top:5px; display:flex; align-items:center; justify-content:center; gap:6px;">
                        <i class="fa-paper-plane"></i> {{ $tr['send'] }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Inclusions Section --}}
        @if(isset($incItems) && $incItems->count() > 0)
        <div style="margin-top:25px; padding-top:15px; border-top:2px solid #eee;">
            <h3 style="font-size:18px; font-weight:bold; color:#333; margin:0 0 12px 0;">
                <span style="color:#4CAF50;">✔</span> {{ $tr['inclusions'] }}
            </h3>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:6px 30px;">
                @foreach($incItems as $inc)
                <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#444; cursor:pointer; padding:4px 0;">
                    <input type="checkbox" name="inclusions[]" value="{{ $inc->id }}"
                           {{ in_array($inc->id, $tourInc) ? 'checked' : '' }}
                           style="width:16px; height:16px; accent-color:#c77b2e;">
                    {{ $inc->name }}
                </label>
                @endforeach
            </div>
        </div>
        @endif

    </form>

</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="/assets/admin/tinymce/tinymce.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#inqueryEditor',
            plugins: ["advlist autolink link image lists charmap hr anchor pagebreak",
                       "searchreplace wordcount visualblocks visualchars code fullscreen",
                       "media nonbreaking table contextmenu directionality emoticons textcolor paste"],
            toolbar1: "bold italic underline strikethrough | formatselect | alignleft aligncenter alignright alignjustify | bullist numlist | link image media | code fullscreen",
            menubar: false,
            toolbar_items_size: 'small',
            height: 350,
            content_style: "@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap'); body { font-family: 'Inter', sans-serif; font-size: 14px; padding: 15px; line-height: 1.6; }"
        });
    }
});
</script>
@endpush
