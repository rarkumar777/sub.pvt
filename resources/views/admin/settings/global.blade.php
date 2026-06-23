@extends('admin.layouts.app')

@section('title', 'Admin | Global Settings')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">
    @include('admin.settings._nav')
    
    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="tw-bg-emerald-50 tw-border tw-border-emerald-200 tw-text-emerald-700 tw-px-6 tw-py-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3 tw-text-sm tw-font-semibold">
        <i class="fa fa-check-circle tw-text-emerald-500"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="tw-bg-red-50 tw-border tw-border-red-200 tw-text-red-700 tw-px-6 tw-py-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3 tw-text-sm tw-font-semibold">
        <i class="fa fa-exclamation-circle tw-text-red-500"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <h1 class="tw-text-2xl tw-font-bold tw-text-slate-800 tw-flex tw-items-center tw-gap-3">
        <i class="fa fa-cog tw-text-slate-500"></i> Global
    </h1>

    <form method="POST" action="{{ route('admin.settings.global.update') }}">
    @csrf

    <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
        <div class="tw-divide-y tw-divide-slate-50">
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">System Email</label>
                <input type="text" name="systemmail" value="{{ $settings['system_mail'] ?? '' }}" placeholder="admin@example.com" class="tw-w-full">
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Trun off Website front end</label>
                <select name="turnoffwebsite" class="tw-w-full">
                    <option value="on" {{ ($settings['WEB_SITE_OFFLINE'] ?? '') == 'on' ? 'selected' : '' }}>Yes</option>
                    <option value="off" {{ ($settings['WEB_SITE_OFFLINE'] ?? '') == 'off' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Time zone</label>
                <select name="time_zone" class="tw-w-full">
                    @foreach($timezones as $tz)
                    <option value="{{ $tz }}" {{ ($settings['time_zone'] ?? '') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Default theme</label>
                <select name="defaulttheme" class="tw-w-full">
                    @foreach($themes as $t)
                    <option value="{{ $t }}" {{ ($settings['theme'] ?? '') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Default layout</label>
                <select name="defaultlayout" class="tw-w-full">
                    @foreach($layouts as $lk => $lv)
                    <option value="{{ $lk }}" {{ ($settings['defaultlayout'] ?? '') == $lk ? 'selected' : '' }}>{{ $lk }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Default Language</label>
                <select name="defaultlang" class="tw-w-full">
                    @foreach($langs as $l)
                    <option value="{{ $l }}" {{ ($settings['lang'] ?? '') == $l ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Default Language-&gt;Administrations</label>
                <select name="defaultlangadmin" class="tw-w-full">
                    @foreach($langs as $l)
                    <option value="{{ $l }}" {{ ($settings['admin_lang'] ?? '') == $l ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Debug Mode</label>
                <select name="debug" class="tw-w-full">
                    <option value="on" {{ ($settings['debug'] ?? '') == 'on' ? 'selected' : '' }}>On</option>
                    <option value="off" {{ ($settings['debug'] ?? '') == 'off' ? 'selected' : '' }}>Off</option>
                </select>
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Recored per page</label>
                <input type="text" name="recored_per_page" value="{{ $settings['recored_per_page'] ?? 100 }}" class="tw-w-full">
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Administrations &gt; Default Currency</label>
                <select name="default_currency" class="tw-w-full">
                    @foreach($currencies as $cid => $cname)
                    <option value="{{ $cid }}" {{ ($settings['currency'] ?? '') == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Checkout &gt; Default Currency</label>
                <select name="check_out_currency" class="tw-w-full">
                    @foreach($currencies as $cid => $cname)
                    <option value="{{ $cid }}" {{ ($settings['check_out_currency'] ?? '') == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Front end &gt; Default Currency</label>
                <select name="front_currency" class="tw-w-full">
                    @foreach($currencies as $cid => $cname)
                    <option value="{{ $cid }}" {{ ($settings['front_currency'] ?? '') == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Default user group</label>
                <select name="def_user_group" class="tw-w-full">
                    @foreach($userGroups as $ug)
                    <option value="{{ $ug }}" {{ ($settings['def_user_group'] ?? '') == $ug ? 'selected' : '' }}>{{ $ug }}</option>
                    @endforeach
                </select>
            </div>
            
        </div>
    </div>

    {{-- SMTP mailer setting --}}
    <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
        <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
            <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">SMTP mailer setting</h3>
        </div>
        <div class="tw-divide-y tw-divide-slate-50">
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Server</label>
                <input type="text" name="smtp_server" value="{{ $settings['smtp_server'] ?? '' }}" placeholder="mail.example.com" class="tw-w-full">
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Port</label>
                <input type="text" name="smtp_port" value="{{ $settings['smtp_port'] ?? '' }}" placeholder="25" class="tw-w-full">
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">User</label>
                <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] ?? '' }}" placeholder="user@example.com" class="tw-w-full">
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Password</label>
                <input type="text" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}" class="tw-w-full">
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Secure</label>
                <select name="smtp_secure" class="tw-w-full">
                    <option value="none" {{ ($settings['smtp_secure'] ?? '') == 'none' ? 'selected' : '' }}>none</option>
                    <option value="ssl" {{ ($settings['smtp_secure'] ?? '') == 'ssl' ? 'selected' : '' }}>ssl</option>
                    <option value="tls" {{ ($settings['smtp_secure'] ?? '') == 'tls' ? 'selected' : '' }}>tls</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Google analytics ID --}}
    <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
        <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
            <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Google analytics ID</h3>
        </div>
        <div class="tw-divide-y tw-divide-slate-50">
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Google analytics ID</label>
                <input type="text" name="google_analytics" value="{{ $settings['google_analytics'] ?? '' }}" placeholder="UA-XXXXXXXXX-X" class="tw-w-full">
            </div>
        </div>
    </div>

    {{-- Facebook Login --}}
    <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
        <div class="tw-px-6 tw-py-4 tw-bg-slate-50 tw-border-b tw-border-slate-200">
            <h3 class="tw-text-[15px] tw-font-bold tw-text-slate-800 !tw-m-0">Facebook Login</h3>
        </div>
        <div class="tw-divide-y tw-divide-slate-50">
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Status</label>
                <select name="fb_status" class="tw-w-full">
                    <option value="0" {{ ($settings['fb_status'] ?? '') == '0' ? 'selected' : '' }}>Disable</option>
                    <option value="1" {{ ($settings['fb_status'] ?? '') == '1' ? 'selected' : '' }}>Enable</option>
                </select>
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">App ID</label>
                <input type="text" name="fb_app_id" value="{{ $settings['fb_app_id'] ?? '' }}" class="tw-w-full">
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">App secret</label>
                <input type="text" name="fb_app_secret" value="{{ $settings['fb_app_secret'] ?? '' }}" class="tw-w-full">
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4 hover:tw-bg-slate-50/50 tw-transition-colors">
                <label class="tw-text-[13px] tw-font-semibold tw-text-slate-700">Graph version</label>
                <input type="text" name="fb_graph_version" value="{{ $settings['fb_graph_version'] ?? '' }}" placeholder="v2.8" class="tw-w-full">
            </div>
        </div>
        <div class="tw-px-6 tw-py-4 tw-bg-amber-50 tw-border-t tw-border-amber-200">
            <p class="tw-text-[13px] tw-text-amber-700 !tw-m-0">
                <i class="fa fa-exclamation-triangle"></i> you must add the bellow link to facebook Valid OAuth redirect URIs , you have to add alink to each language on your website
            </p>
            <div class="tw-flex tw-flex-wrap tw-gap-2 tw-mt-2">
                @foreach($langs as $l)
                <code class="tw-text-[12px] tw-bg-white tw-px-3 tw-py-1.5 tw-rounded tw-border tw-border-amber-200 tw-text-amber-800">https://pvt.jo/{{ $l }}/users/login/</code>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <button type="submit" name="save" class="btn blue tw-w-full !tw-py-4 !tw-text-base">
        <i class="fa fa-save"></i> Save Settings
    </button>
    
    </form>
</div>
@endsection
