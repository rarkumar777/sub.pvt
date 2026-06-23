@extends('admin.layouts.app')
@section('title', 'Admin | Company Profile')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    @include('admin.settings._nav')
    
    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
                <span class="tw-w-12 tw-h-12 tw-bg-indigo-50 tw-text-indigo-600 tw-rounded-2xl tw-inline-flex tw-items-center tw-justify-center tw-mr-2">
                    <i class="fa fa-building"></i>
                </span>
                Company <span class="tw-text-indigo-600">Profile</span>
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Manage your organization's contact details and branding</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.company-profile.update') }}" enctype="multipart/form-data">
        @csrf

        {{-- Contact Information --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-8 tw-py-5 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                <h3 class="tw-text-sm tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                    <i class="fa fa-phone tw-text-emerald-500"></i> Contact Information
                </h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Telephone</label>
                    <input type="text" name="telephone" value="{{ $profile['telephone'] ?? '' }}" placeholder="+962 6 000 0000">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Fax</label>
                    <input type="text" name="fax" value="{{ $profile['fax'] ?? '' }}" placeholder="+962 6 000 0000">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">E-mail</label>
                    <input type="email" name="email" value="{{ $profile['email'] ?? '' }}" placeholder="info@company.com">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Company National Number</label>
                    <input type="text" name="national_number" value="{{ $profile['national_number'] ?? '' }}">
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Google Map Code</label>
                    <input type="text" name="google_map" value="{{ $profile['google_map'] ?? '' }}" placeholder="Embed code or coordinates">
                </div>
            </div>
        </div>

        {{-- Branding --}}
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-8 tw-py-5 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                <h3 class="tw-text-sm tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                    <i class="fa fa-image tw-text-violet-500"></i> Branding
                </h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Logo</label>
                    <div class="tw-flex tw-items-center tw-gap-4">
                        @if(isset($profile['logo']) && $profile['logo'])
                            <div class="tw-h-12 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded tw-flex tw-items-center tw-justify-center tw-px-2">
                                <img src="{{ asset('uploads/' . $profile['logo']) }}" alt="Logo" class="tw-h-8 tw-w-auto tw-object-contain">
                            </div>
                        @else
                            <div class="tw-h-12 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded tw-flex tw-items-center tw-justify-center tw-px-2">
                                <img src="{{ asset('Pvtnew1.png') }}" alt="Default Logo" class="tw-h-8 tw-w-auto tw-object-contain">
                            </div>
                        @endif
                        <input type="file" name="logo" class="tw-text-sm tw-text-slate-600">
                    </div>
                </div>
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Favicon</label>
                    <div class="tw-flex tw-items-center tw-gap-4">
                        @if(isset($profile['fav_icon']) && $profile['fav_icon'])
                            <div class="tw-h-12 tw-w-12 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded tw-flex tw-items-center tw-justify-center tw-overflow-hidden">
                                <img src="{{ asset('uploads/' . $profile['fav_icon']) }}" alt="Favicon" class="tw-h-6 tw-w-6 tw-object-contain">
                            </div>
                        @else
                            <div class="tw-h-12 tw-w-12 tw-bg-slate-50 tw-border tw-border-slate-100 tw-rounded tw-flex tw-items-center tw-justify-center tw-overflow-hidden">
                                <img src="{{ asset('favpvt1.png') }}" alt="Default Favicon" class="tw-h-6 tw-w-6 tw-object-contain">
                            </div>
                        @endif
                        <input type="file" name="fav_icon" class="tw-text-sm tw-text-slate-600">
                    </div>
                </div>
            </div>
        </div>

        {{-- Per-language sections --}}
        @if(isset($profile['langs']))
        @foreach($profile['langs'] as $lang => $langData)
        <div class="box !tw-p-0 !tw-overflow-hidden tw-mb-6">
            <div class="tw-px-8 tw-py-5 tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                <h3 class="tw-text-sm tw-font-bold tw-text-slate-700 tw-uppercase tw-tracking-wider tw-flex tw-items-center tw-gap-2 !tw-m-0">
                    <i class="fa fa-language tw-text-blue-500"></i> {{ strtoupper($lang) }} Content
                </h3>
            </div>
            <div class="tw-divide-y tw-divide-slate-50">
                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-items-center tw-px-8 tw-py-5 tw-gap-4">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700">Company Name ({{ $lang }})</label>
                    <input type="text" name="langs[{{ $lang }}][name]" value="{{ $langData['name'] ?? '' }}">
                </div>
                <div class="tw-px-8 tw-py-5">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-3 tw-block">Address ({{ $lang }})</label>
                    <textarea name="langs[{{ $lang }}][address]" class="tinymce" rows="5">{!! isset($langData['address']) ? html_entity_decode($langData['address']) : '' !!}</textarea>
                </div>
                <div class="tw-px-8 tw-py-5">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-3 tw-block">Opening Hours ({{ $lang }})</label>
                    <textarea name="langs[{{ $lang }}][opening_hours]" class="tinymce" rows="5">{!! isset($langData['opening_hours']) ? html_entity_decode($langData['opening_hours']) : '' !!}</textarea>
                </div>
                <div class="tw-px-8 tw-py-5">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-3 tw-block">Mail Signature ({{ $lang }})</label>
                    <textarea name="langs[{{ $lang }}][mail_signature]" class="tinymce" rows="5">{!! isset($langData['mail_signature']) ? html_entity_decode($langData['mail_signature']) : '' !!}</textarea>
                </div>
            </div>
        </div>
        @endforeach
        @endif

        {{-- Save Button --}}
        <button type="submit" class="btn indigo tw-w-full !tw-py-4 !tw-text-base tw-shadow-lg tw-shadow-indigo-100">
            <i class="fa fa-check-circle"></i> Save Company Profile
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/admin/tinymce/tinymce.min.js') }}"></script>
<script>
tinymce.init({
    selector: 'textarea.tinymce',
    height: 150,
    menubar: true,
    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
    toolbar: 'bold italic underline strikethrough | formatselect | fontsizeselect fontselect | alignleft aligncenter alignright alignjustify | cut copy paste | undo redo | bullist numlist | outdent indent | link unlink image media | table | forecolor backcolor | removeformat | code',
    content_style: 'body { font-family: Roboto, sans-serif; font-size: 11pt; }'
});
</script>
@endpush
