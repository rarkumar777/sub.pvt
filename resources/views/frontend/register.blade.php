@extends('frontend.layout')
@section('title', 'Create My Account')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<style>
    /* ============================================
       REGISTER PAGE — Scoped Field Styles
       All rules scoped inside #register-card
    ============================================ */

    /* Text/Email/Password/Date inputs */
    #register-card .reg-input {
        display: block !important;
        width: 100% !important;
        height: 38px !important;
        padding: 8px 12px !important;
        font-size: 14px !important;
        font-family: inherit !important;
        color: #1f2937 !important;
        background-color: #ffffff !important;
        border: 1.5px solid #d1d5db !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        box-sizing: border-box !important;
        outline: none !important;
        transition: border-color 0.2s, box-shadow 0.2s !important;
        -webkit-appearance: none !important;
        appearance: none !important;
        margin: 0 !important;
        line-height: 1.5 !important;
    }

    #register-card .reg-input:focus {
        border-color: #a44b11 !important;
        box-shadow: 0 0 0 3px rgba(164, 75, 17, 0.15) !important;
        background-color: #ffffff !important;
    }

    /* Native select */
    #register-card .reg-select {
        display: block !important;
        width: 100% !important;
        height: 38px !important;
        padding: 8px 12px !important;
        font-size: 14px !important;
        font-family: inherit !important;
        color: #1f2937 !important;
        background-color: #ffffff !important;
        border: 1.5px solid #d1d5db !important;
        border-radius: 8px !important;
        box-shadow: none !important;
        box-sizing: border-box !important;
        outline: none !important;
        -webkit-appearance: none !important;
        appearance: none !important;
        cursor: pointer !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        padding-right: 36px !important;
    }
    #register-card .reg-select:focus {
        border-color: #a44b11 !important;
        box-shadow: 0 0 0 3px rgba(164, 75, 17, 0.15) !important;
    }

    /* File input */
    #register-card .reg-file {
        display: block !important;
        width: 100% !important;
        padding: 7px 12px !important;
        font-size: 14px !important;
        color: #374151 !important;
        background-color: #ffffff !important;
        border: 1.5px solid #d1d5db !important;
        border-radius: 8px !important;
        box-sizing: border-box !important;
        cursor: pointer !important;
        margin: 0 !important;
    }

    /* Labels */
    #register-card label.reg-label {
        display: block !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #374151 !important;
        margin-bottom: 4px !important;
        font-family: inherit !important;
    }

    /* Section divider title */
    #register-card .reg-section-title {
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        color: #9ca3af !important;
        border-bottom: 1px solid #e5e7eb !important;
        padding-bottom: 4px !important;
        margin-bottom: 2px !important;
    }

    /* Captcha */
    #register-card .captcha-box {
        display: flex !important;
        border: 1.5px solid #d1d5db !important;
        border-radius: 8px !important;
        overflow: hidden !important;
        height: 38px !important;
    }
    #register-card .captcha-code {
        background: #1d4ed8 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 17px !important;
        font-style: italic !important;
        letter-spacing: 3px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 20px !important;
        min-width: 90px !important;
        user-select: none !important;
        flex-shrink: 0 !important;
    }
    #register-card .captcha-input {
        flex: 1 !important;
        border: none !important;
        outline: none !important;
        padding: 0 14px !important;
        font-size: 14px !important;
        color: #1f2937 !important;
        background: #f9fafb !important;
        height: 100% !important;
        box-shadow: none !important;
    }
    #register-card .captcha-box:focus-within {
        border-color: #a44b11 !important;
        box-shadow: 0 0 0 3px rgba(164, 75, 17, 0.15) !important;
    }

    /* Submit Button */
    #register-card .reg-submit-btn {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        width: 100% !important;
        height: 44px !important;
        background: #a44b11 !important;
        color: #ffffff !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        border: none !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        box-shadow: 0 4px 14px rgba(164, 75, 17, 0.25) !important;
        transition: background-color 0.2s, box-shadow 0.2s !important;
        letter-spacing: 0.02em !important;
    }
    #register-card .reg-submit-btn:hover {
        background: #8b3f0e !important;
        box-shadow: 0 6px 20px rgba(164, 75, 17, 0.35) !important;
    }

    /* Error Alert */
    #register-card .reg-error {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        background: #fef2f2 !important;
        border: 1px solid #fecaca !important;
        color: #b91c1c !important;
        border-radius: 8px !important;
        padding: 10px 14px !important;
        font-size: 13px !important;
        margin-bottom: 8px !important;
    }

    /* Field groups inside grid */
    #register-card .reg-field-group {
        display: flex !important;
        flex-direction: column !important;
    }

    /* Unified 2-column grid for all form fields */
    #register-card .reg-unified-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 12px 16px !important;
    }
    /* Items that must span both columns */
    #register-card .reg-span-full {
        grid-column: 1 / -1 !important;
    }
    @media (max-width: 580px) {
        #register-card .reg-unified-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

{{-- Helpers for field visibility --}}
@php
    $gf = $groupFields ?? [];

    // Check if field should be shown (not disabled)
    $isVisible = function($key) use ($gf) {
        $val = $gf[$key] ?? 'a';
        return $val === 'a' || $val === 'r';
    };

    // Check if field is required
    $isRequired = function($key) use ($gf) {
        return ($gf[$key] ?? 'a') === 'r';
    };

    // ── Build sections dynamically ──
    // Each section has a title and an ordered list of field definitions.
    // 'pair' means the field prefers to sit side-by-side with another field.
    // 'full' means the field always takes full width.

    $sections = [];

    // --- Personal Information ---
    $personalFields = [];
    if ($isVisible('first_name')) $personalFields[] = ['key' => 'first_name', 'prefer' => 'pair'];
    if ($isVisible('last_name'))  $personalFields[] = ['key' => 'last_name',  'prefer' => 'pair'];
    if ($isVisible('email'))      $personalFields[] = ['key' => 'email',      'prefer' => 'full'];
    if (count($personalFields))   $sections[] = ['title' => 'Personal Information', 'fields' => $personalFields];

    // --- Location ---
    $locationFields = [];
    if ($isVisible('country')) $locationFields[] = ['key' => 'country', 'prefer' => 'pair'];
    if ($isVisible('city'))    $locationFields[] = ['key' => 'city',    'prefer' => 'pair'];
    if ($isVisible('address')) $locationFields[] = ['key' => 'address', 'prefer' => 'full'];
    if (count($locationFields)) $sections[] = ['title' => 'Location', 'fields' => $locationFields];

    // --- Contact Details ---
    $contactFields = [];
    if ($isVisible('mobile'))  $contactFields[] = ['key' => 'mobile',  'prefer' => 'pair'];
    if ($isVisible('phone'))   $contactFields[] = ['key' => 'phone',   'prefer' => 'pair'];
    if ($isVisible('fax'))     $contactFields[] = ['key' => 'fax',     'prefer' => 'pair'];
    if ($isVisible('url'))     $contactFields[] = ['key' => 'url',     'prefer' => 'pair'];
    if ($isVisible('company')) $contactFields[] = ['key' => 'company', 'prefer' => 'full'];
    if (count($contactFields)) $sections[] = ['title' => 'Contact Details', 'fields' => $contactFields];

    // --- Additional Info ---
    $additionalFields = [];
    if ($isVisible('birth_day')) $additionalFields[] = ['key' => 'birth_day', 'prefer' => 'pair'];
    if ($isVisible('gender'))   $additionalFields[] = ['key' => 'gender',    'prefer' => 'pair'];
    if ($isVisible('avatar'))   $additionalFields[] = ['key' => 'avatar',    'prefer' => 'full'];
    if (count($additionalFields)) $sections[] = ['title' => 'Additional Info', 'fields' => $additionalFields];

    // ── Smart layout: assign column classes ──
    // Walk each section's fields and assign 'col_class':
    //   - 'full' preference → always full-col
    //   - 'pair' preference → pair up with next 'pair' field; if no partner → full-col
    foreach ($sections as &$sec) {
        $arranged = [];
        $i = 0;
        $fields = $sec['fields'];
        $count = count($fields);

        while ($i < $count) {
            $f = $fields[$i];
            if ($f['prefer'] === 'full') {
                $f['col_class'] = 'reg-span-full';
                $arranged[] = $f;
                $i++;
            } else {
                // Look for a pair partner (next 'pair' field)
                $partner = null;
                if ($i + 1 < $count && $fields[$i + 1]['prefer'] === 'pair') {
                    $partner = $fields[$i + 1];
                }
                if ($partner) {
                    $f['col_class'] = '';
                    $partner['col_class'] = '';
                    $arranged[] = $f;
                    $arranged[] = $partner;
                    $i += 2;
                } else {
                    // No partner → full width
                    $f['col_class'] = 'reg-span-full';
                    $arranged[] = $f;
                    $i++;
                }
            }
        }
        $sec['fields'] = $arranged;
    }
    unset($sec);

    // ── Field HTML renderers ──
    $fieldHtml = [
        'first_name' => function() use ($isRequired) {
            $req = $isRequired('first_name') || true;
            $star = $req ? '<span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">First Name '.$star.'</label><input type="text" name="first_name" value="'.e(old('first_name')).'" '.($req ? 'required' : '').' class="reg-input" placeholder="Enter first name">';
        },
        'last_name' => function() use ($isRequired) {
            $req = $isRequired('last_name') || true;
            $star = $req ? '<span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">Last Name '.$star.'</label><input type="text" name="last_name" value="'.e(old('last_name')).'" '.($req ? 'required' : '').' class="reg-input" placeholder="Enter last name">';
        },
        'email' => function() use ($isRequired) {
            $req = $isRequired('email') || true;
            $star = $req ? '<span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">E-mail Address '.$star.'</label><input type="email" name="email" value="'.e(old('email')).'" '.($req ? 'required' : '').' class="reg-input" placeholder="you@example.com">';
        },
        'country' => function() use ($isRequired, $countries) {
            $req = $isRequired('country');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            $reqAttr = $req ? 'required' : '';
            $opts = '<option value="0">-- Select Country --</option>';
            foreach ($countries as $c) {
                $sel = old('country') == $c->id ? 'selected' : '';
                $opts .= '<option value="'.$c->id.'" '.$sel.'>'.e($c->name).'</option>';
            }
            return '<label class="reg-label">Country'.$star.'</label><select name="country" data-advance="false" class="reg-select" '.$reqAttr.'>'.$opts.'</select>';
        },
        'city' => function() use ($isRequired) {
            $req = $isRequired('city');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">City'.$star.'</label><input type="text" name="city" value="'.e(old('city')).'" class="reg-input" placeholder="Enter your city" '.($req ? 'required' : '').'>';
        },
        'address' => function() use ($isRequired) {
            $req = $isRequired('address');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">Address'.$star.'</label><input type="text" name="address" value="'.e(old('address')).'" class="reg-input" placeholder="Enter your full address" '.($req ? 'required' : '').'>';
        },
        'mobile' => function() use ($isRequired) {
            $req = $isRequired('mobile');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">Mobile'.$star.'</label><input type="text" name="mobile" value="'.e(old('mobile')).'" class="reg-input" placeholder="+1 234 567 8900" '.($req ? 'required' : '').'>';
        },
        'phone' => function() use ($isRequired) {
            $req = $isRequired('phone');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">Telephone'.$star.'</label><input type="text" name="telephone" value="'.e(old('telephone')).'" class="reg-input" placeholder="Telephone number" '.($req ? 'required' : '').'>';
        },
        'fax' => function() use ($isRequired) {
            $req = $isRequired('fax');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">Fax'.$star.'</label><input type="text" name="fax" value="'.e(old('fax')).'" class="reg-input" placeholder="Fax number" '.($req ? 'required' : '').'>';
        },
        'url' => function() use ($isRequired) {
            $req = $isRequired('url');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">Website URL'.$star.'</label><input type="text" name="url" value="'.e(old('url')).'" class="reg-input" placeholder="https://yourwebsite.com" '.($req ? 'required' : '').'>';
        },
        'company' => function() use ($isRequired) {
            $req = $isRequired('company');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">Company'.$star.'</label><input type="text" name="company" value="'.e(old('company')).'" class="reg-input" placeholder="Your company name" '.($req ? 'required' : '').'>';
        },
        'birth_day' => function() use ($isRequired) {
            $req = $isRequired('birth_day');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">Birth Date'.$star.'</label><input type="date" name="birth_day" value="'.e(old('birth_day')).'" class="reg-input" '.($req ? 'required' : '').'>';
        },
        'gender' => function() use ($isRequired) {
            $req = $isRequired('gender');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            $reqAttr = $req ? 'required' : '';
            $selM = old('gender', '1') == '1' ? 'selected' : '';
            $selF = old('gender') == '0' ? 'selected' : '';
            return '<label class="reg-label">Gender'.$star.'</label><select name="gender" data-advance="false" class="reg-select" '.$reqAttr.'><option value="1" '.$selM.'>Male</option><option value="0" '.$selF.'>Female</option></select>';
        },
        'avatar' => function() use ($isRequired) {
            $req = $isRequired('avatar');
            $star = $req ? ' <span style="color:#ef4444;">*</span>' : '';
            return '<label class="reg-label">Profile Avatar'.$star.'</label><input type="file" name="avatar" accept="image/*" class="reg-file" '.($req ? 'required' : '').'>';
        },
    ];
@endphp

{{-- Modal Overlay --}}
<style>
    #reg-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 41, 0.75);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        animation: regFadeIn 0.25s ease;
    }
    @keyframes regFadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    #reg-modal-box {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 24px 80px rgba(0,0,0,0.35);
        width: 100%;
        max-width: 820px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        animation: regSlideUp 0.28s ease;
    }
    @keyframes regSlideUp {
        from { transform: translateY(32px); opacity: 0; }
        to   { transform: translateY(0);   opacity: 1; }
    }
    #reg-modal-close {
        position: absolute;
        top: 14px;
        right: 16px;
        width: 32px;
        height: 32px;
        background: rgba(255,255,255,0.12);
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        z-index: 10;
        transition: background 0.2s;
    }
    #reg-modal-close:hover { background: rgba(255,255,255,0.25); }
    #reg-modal-box::-webkit-scrollbar { width: 5px; }
    #reg-modal-box::-webkit-scrollbar-track { background: #f1f1f1; }
    #reg-modal-box::-webkit-scrollbar-thumb { background: #a44b11; border-radius: 4px; }
</style>

<div id="reg-modal-overlay">
    <div id="reg-modal-box" id="register-card">

        {{-- Modal Header --}}
        <div style="background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 18px 18px 0 0; padding: 18px 24px; position:relative;">
            <button id="reg-modal-close" onclick="window.location.href='/{{ $lang }}/'" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <h2 style="font-size:20px; font-weight:800; color:#fff; margin:0; display:flex; align-items:center; gap:10px;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:20px;height:20px;flex-shrink:0;color:#fff;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Create My Account
            </h2>
            <p style="color:rgba(255,255,255,0.85); font-size:13px; margin:4px 0 0 0;">Fill in the details below to get started.</p>
        </div>

        {{-- Modal Body --}}
        <div id="register-card" style="padding: 20px 24px;">

            {{-- Errors --}}
            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="reg-error">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;flex-shrink:0;" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $error }}
                    </div>
                @endforeach
            @endif

            <form method="POST" action="/{{ $lang }}/users/register/" enctype="multipart/form-data">
                @csrf

                {{-- Single unified 2-column grid for ALL fields --}}
                <div class="reg-unified-grid">

                    {{-- Dynamic sections --}}
                    @foreach($sections as $sIdx => $section)
                        <div class="reg-section-title reg-span-full" @if($sIdx === 0) style="margin-top:0;" @endif>{{ $section['title'] }}</div>
                        @foreach($section['fields'] as $field)
                            <div class="reg-field-group {{ $field['col_class'] ?? '' }}">
                                {!! $fieldHtml[$field['key']]() !!}
                            </div>
                        @endforeach
                    @endforeach

                    {{-- Security section --}}
                    <div class="reg-section-title reg-span-full">Security</div>
                    <div class="reg-field-group">
                        <label class="reg-label">Password <span style="color:#ef4444;">*</span></label>
                        <div style="display:flex;border:1.5px solid #d1d5db;border-radius:8px;overflow:hidden;transition:border-color .2s;" id="regPwdWrap1">
                            <input type="password" name="password" id="reg_password" required placeholder="Create a password" style="flex:1;border:none;outline:none;padding:8px 12px;font-size:14px;font-family:inherit;background:transparent;min-width:0;" onfocus="document.getElementById('regPwdWrap1').style.borderColor='#a44b11'" onblur="document.getElementById('regPwdWrap1').style.borderColor='#d1d5db'">
                            <button type="button" onclick="toggleRegPwd('reg_password','regEye1')" style="border:none;background:none;padding:0 12px;cursor:pointer;color:#9ca3af;font-size:15px;flex-shrink:0;" id="regEye1"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="reg-field-group">
                        <label class="reg-label">Retype Password <span style="color:#ef4444;">*</span></label>
                        <div style="display:flex;border:1.5px solid #d1d5db;border-radius:8px;overflow:hidden;transition:border-color .2s;" id="regPwdWrap2">
                            <input type="password" name="retype_password" id="reg_password_confirm" required placeholder="Confirm your password" style="flex:1;border:none;outline:none;padding:8px 12px;font-size:14px;font-family:inherit;background:transparent;min-width:0;" onfocus="document.getElementById('regPwdWrap2').style.borderColor='#a44b11'" onblur="document.getElementById('regPwdWrap2').style.borderColor='#d1d5db'">
                            <button type="button" onclick="toggleRegPwd('reg_password_confirm','regEye2')" style="border:none;background:none;padding:0 12px;cursor:pointer;color:#9ca3af;font-size:15px;flex-shrink:0;" id="regEye2"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>

                    {{-- CAPTCHA section --}}
                    <div class="reg-section-title reg-span-full">Robot Verification</div>
                    <div class="reg-field-group">
                        <label class="reg-label">Enter the code shown in the blue box <span style="color:#ef4444;">*</span></label>
                        <div class="captcha-box">
                            <div class="captcha-code">{{ $captchaCode }}</div>
                            <input type="text" name="captcha" class="captcha-input" placeholder="Type the code here..." required>
                        </div>
                    </div>
                    <div class="reg-field-group" style="display:flex; align-items:flex-end; padding-bottom:2px;">
                        <div style="font-size:12px; color:#6b7280;">Type the characters exactly as shown in the blue box.</div>
                    </div>

                    {{-- Sign in link + Submit --}}
                    <div class="reg-span-full" style="margin-top:4px;">
                        <div style="text-align:center; margin-bottom:10px;">
                            <span style="font-size:13px; color:#6b7280;">Already have an account?</span>
                            <a href="/{{ $lang }}/users/login/" style="font-size:13px; font-weight:700; color:#a44b11; margin-left:4px; text-decoration:none;">Sign In</a>
                        </div>
                        <button type="submit" class="reg-submit-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;flex-shrink:0;" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Create My Account
                        </button>
                    </div>

                </div>{{-- end reg-unified-grid --}}

            </form>
        </div>

    </div>
</div>
<script>
function toggleRegPwd(fieldId, btnId){
    var inp = document.getElementById(fieldId);
    var ico = document.getElementById(btnId).querySelector('i');
    if(inp.type === 'password'){ inp.type = 'text'; ico.className = 'fa fa-eye-slash'; }
    else { inp.type = 'password'; ico.className = 'fa fa-eye'; }
}
</script>
@endsection
