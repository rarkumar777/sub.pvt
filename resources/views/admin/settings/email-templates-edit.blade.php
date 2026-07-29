@extends('admin.layouts.app')
@section('title', 'Admin | Edit Email Template')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    <div class="tw-flex tw-justify-between tw-items-center">
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
            <span class="tw-w-12 tw-h-12 tw-bg-indigo-50 tw-text-indigo-600 tw-rounded-2xl tw-inline-flex tw-items-center tw-justify-center tw-mr-2">
                <i class="fa fa-envelope-o"></i>
            </span>
            Edit <span class="tw-text-indigo-600">Template</span>
        </h1>
        <a href="{{ route('admin.settings.email-templates', $mod) }}" class="btn red !tw-py-2 !tw-px-4 tw-shadow-md tw-shadow-rose-100">
            <i class="fa fa-times"></i> cancel
        </a>
    </div>

    <div class="box !tw-p-0 !tw-overflow-hidden">
        <div class="tw-bg-slate-50 tw-px-8 tw-py-6 tw-border-b tw-border-slate-100">
            <h3 class="tw-text-lg tw-font-bold tw-text-slate-800 !tw-m-0">{{ $templateTitle }}</h3>
            <p class="tw-text-sm tw-text-slate-500 tw-mt-1">Edit the subject and body of this email template</p>
        </div>

        <div class="tw-p-8">
            <form action="{{ route('admin.settings.email-templates.update', [$mod, $key]) }}" method="POST">
                @csrf
                <div class="tw-mb-6">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Subject</label>
                    <input type="text" name="subject" value="{{ old('subject', $template['subject'] ?? '') }}" class="tw-w-full" required>
                    @error('subject')
                        <p class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="tw-mb-6">
                    <label class="tw-text-sm tw-font-semibold tw-text-slate-700 tw-mb-2 tw-block">Message Body</label>
                    <textarea name="body" class="editor" rows="15">{{ old('body', $template['body'] ?? '') }}</textarea>
                    @error('body')
                        <p class="tw-text-red-500 tw-text-xs tw-mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="tw-pt-4 tw-border-t tw-border-slate-100 tw-flex tw-justify-end">
                    <button type="submit" class="btn blue !tw-px-8 !tw-py-3 tw-shadow-xl tw-shadow-blue-500/20">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    if(typeof CKEDITOR !== 'undefined') {
        const editors = document.querySelectorAll('.editor');
        editors.forEach(el => {
            CKEDITOR.replace(el, {
                height: 400
            });
        });
    }
</script>
@endpush
