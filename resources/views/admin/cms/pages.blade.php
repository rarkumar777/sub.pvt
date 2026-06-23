@extends('admin.layouts.app')
@section('title', 'Admin | CMS Pages')

@section('content')
<div class="tw-flex tw-items-center tw-justify-between tw-mb-8">
    <div>
        <h1 class="tw-text-3xl tw-font-extrabold tw-text-orange-900 tw-tracking-tight">CMS Pages</h1>
        <p class="tw-text-slate-500 tw-mt-1 tw-font-medium">Manage your website's static pages and content variants.</p>
    </div>
    <a href="{{ route('admin.pages.create') }}" class="btn orange">
        <i class="fa fa-plus"></i> Add New Page
    </a>
</div>

<form method="GET" action="{{ route('admin.pages.index') }}" class="box tw-flex tw-flex-col sm:tw-flex-row tw-items-center tw-gap-4 tw-p-5 tw-mb-8">
    <div class="tw-flex-1 tw-w-full tw-relative">
        <i class="fa fa-search tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID or Page Title..." class="tw-w-full tw-pr-4 tw-py-3 tw-bg-slate-50/50 tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-font-medium tw-text-slate-700 focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500 focus:tw-border-orange-500 tw-outline-none tw-transition-all" style="padding-left: 3rem !important; box-shadow: none;">
    </div>
    
    <div class="tw-w-full sm:tw-w-56 tw-relative">
        <select name="status" class="tw-w-full tw-px-4 tw-py-3 tw-bg-slate-50/50 tw-border tw-border-slate-200 tw-rounded-xl tw-text-sm tw-font-medium tw-text-slate-700 focus:tw-bg-white focus:tw-ring-2 focus:tw-ring-orange-500 focus:tw-border-orange-500 tw-outline-none tw-transition-all tw-appearance-none tw-cursor-pointer" style="box-shadow: none;">
            <option value="">All Statuses</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
        <i class="fa fa-chevron-down tw-absolute tw-right-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400 tw-pointer-events-none tw-text-[10px]"></i>
    </div>
    
    <div class="tw-flex tw-gap-3 tw-w-full sm:tw-w-auto">
        <button type="submit" class="btn orange !tw-py-3 tw-flex-1 sm:tw-flex-none tw-font-semibold tw-shadow-sm"><i class="fa fa-filter tw-mr-1.5"></i> Filter</button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.pages.index') }}" class="btn tw-bg-slate-100 tw-text-slate-600 hover:tw-bg-slate-200 hover:tw-text-slate-800 !tw-py-3 tw-flex-1 sm:tw-flex-none tw-text-center tw-no-underline tw-font-semibold tw-transition-colors">Clear</a>
        @endif
    </div>
</form>

<div class="box tw-p-0 tw-overflow-hidden">
    <div class="tw-overflow-x-auto">
        <table class="tw-w-full tw-text-left tw-border-collapse">
            <thead>
                <tr class="tw-bg-slate-50/50 tw-border-b tw-border-slate-100">
                    <th class="tw-py-4 tw-px-6 tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider"># ID</th>
                    <th class="tw-py-4 tw-px-6 tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider">Page Title</th>
                    <th class="tw-py-4 tw-px-6 tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider">Slug / URL</th>
                    <th class="tw-py-4 tw-px-6 tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider">Status</th>
                    <th class="tw-py-4 tw-px-6 tw-text-xs tw-font-bold tw-text-slate-500 tw-uppercase tw-tracking-wider tw-text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="tw-divide-y tw-divide-slate-100">
                @forelse($pages as $page)
                <tr class="tw-group hover:tw-bg-slate-50/20 tw-transition-all">
                    <td class="tw-py-4 tw-px-6 tw-text-sm tw-text-slate-400 tw-font-medium">#{{ $page->id }}</td>
                    <td class="tw-py-4 tw-px-6">
                        <div class="tw-text-sm tw-font-bold tw-text-orange-900">{{ $page->title }}</div>
                        <div class="tw-text-[11px] tw-text-slate-400 tw-mt-0.5 tw-font-medium">{{ $page->name }}</div>
                    </td>
                    <td class="tw-py-4 tw-px-6">
                        <code class="tw-text-[11px] tw-bg-slate-100 tw-px-2 tw-py-1 tw-rounded tw-text-slate-600 tw-font-bold">
                            {{ $page->slug ?? $page->url ?? '-' }}
                        </code>
                    </td>
                    <td class="tw-py-4 tw-px-6">
                        <span class="status-toggle badge {{ ($page->published == 1 || $page->status == 1) ? '!tw-bg-orange-50 !tw-text-orange-500' : 'danger' }} tw-cursor-pointer tw-transition-all active:tw-scale-95" 
                              data-url="{{ route('admin.pages.toggle-status', $page->id) }}" 
                              id="status-{{ $page->id }}">
                            <i class="fa {{ ($page->published == 1 || $page->status == 1) ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ ($page->published == 1 || $page->status == 1) ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="tw-py-4 tw-px-6 tw-text-right">
                        <div class="tw-flex tw-items-center tw-justify-end tw-gap-2">
                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-transition-all" title="Edit Page">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.pages.destroy', $page->id) }}" class="tw-inline" onsubmit="return confirm('Delete this page?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="tw-w-9 tw-h-9 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-red-50 tw-text-red-500 hover:tw-bg-red-500 hover:tw-text-white tw-transition-all tw-border-none tw-cursor-pointer">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="tw-py-20 tw-text-center">
                        <div class="tw-flex tw-flex-col tw-items-center">
                            <i class="fa fa-file-text-o tw-text-5xl tw-text-slate-200 tw-mb-4"></i>
                            <div class="tw-text-slate-500 tw-font-bold">No pages found</div>
                            <div class="tw-text-slate-400 tw-text-sm">Start by creating your first content page.</div>
                            <a href="{{ route('admin.pages.create') }}" class="btn orange tw-mt-6">
                                <i class="fa fa-plus"></i> Add New Page
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="tw-mt-8">
    {{ $pages->links() }}
</div>

@push('scripts')
<script>
$(document).on('click', '.status-toggle', function() {
    var $el = $(this);
    var url = $el.data('url');
    
    $el.css('opacity', '0.5').css('pointer-events', 'none');
    
    $.ajax({
        url: url,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                var icon = response.status == 1 ? '<i class="fa fa-check-circle"></i> ' : '<i class="fa fa-times-circle"></i> ';
                $el.html(icon + response.label)
                   .removeClass('!tw-bg-orange-50 !tw-text-orange-500 danger')
                   .addClass(response.status == 1 ? '!tw-bg-orange-50 !tw-text-orange-500' : 'danger');
            }
        },
        complete: function() {
            $el.css('opacity', '1').css('pointer-events', 'auto');
        }
    });
});
</script>
@endpush
@endsection
