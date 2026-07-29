@extends('admin.layouts.app')
@section('title', 'Admin | Top Nav')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Header --}}
    <div class="tw-flex tw-justify-between tw-items-center">
        <div>
            <h1 class="tw-text-3xl tw-font-extrabold tw-text-slate-900 tw-tracking-tight">
                Top <span class="tw-text-indigo-600">Navigation</span>
            </h1>
            <p class="tw-text-slate-500 tw-font-medium tw-mt-1">Drag and drop to reorder menu items</p>
        </div>
        <div class="tw-flex tw-gap-3">
            <a href="#" class="btn indigo" onclick="save_menu(); return false;"><i class="fa fa-check"></i> Save Order</a>
            <a href="#add_new_link" class="btn indigo"><i class="fa fa-plus"></i> Add New</a>
        </div>
    </div>

    <div id="ajax"></div>

    {{-- Nestable Nav Tree --}}
    <div class="box">
        <div class="dd" id="nestable">
            @php
            if (!function_exists('renderNavTree')) {
                function renderNavTree($items, $parentId = 0) {
                    $html = '';
                    if (isset($items[$parentId])) {
                        $html .= '<ol class="' . ($parentId == 0 ? 'dd-list' : '') . '">';
                        foreach ($items[$parentId] as $item) {
                            $html .= '<li class="dd-item" id="' . ($item->lang_id ?? $item->id) . '">';
                            $html .= '<div class="absolute top right h-gap">';
                            $html .= '<a href="' . route('admin.nav.delete', $item->lang_id ?? $item->id) . '" onclick="return confirm(\'Are you sure?\')" class="btn red h-pad small"><i class="fa-close"></i></a> ';
                            $html .= '<a href="' . route('admin.nav.edit', $item->lang_id ?? $item->id) . '" class="btn green h-pad small"><i class="fa-edit"></i></a>';
                            $html .= '</div>';
                            $html .= '<div class="dd-handle"><i class="' . ($item->icon ?? '') . '"></i> ' . ($item->label ?? $item->title) . '</div>';
                            $html .= renderNavTree($items, $item->lang_id ?? $item->id);
                            $html .= '</li>';
                        }
                        $html .= '</ol>';
                    }
                    return $html;
                }
            }
            @endphp
            {!! renderNavTree($navItems) !!}
        </div>
    </div>
</div>

{{-- Add New Link Modal --}}
<div class="modal" id="add_new_link">
    <div class="tw-bg-white tw-rounded-3xl tw-p-0 tw-overflow-hidden !tw-w-[550px] !tw-max-w-[95vw] tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-8 tw-py-6 tw-bg-slate-900 tw-text-white">
            <h3 class="tw-text-lg tw-font-bold tw-flex tw-items-center tw-gap-3 !tw-m-0">
                <i class="fa fa-plus-circle tw-text-emerald-400"></i> Add Navigation Item
            </h3>
            <a href="#close" class="tw-text-white/60 hover:tw-text-white tw-transition-colors tw-text-2xl tw-no-underline">&times;</a>
        </div>
        <form action="{{ route('admin.nav.store') }}" method="POST" class="tw-p-8 tw-max-h-[70vh] tw-overflow-y-auto">
            @csrf
            @foreach($langs as $l)
            <div class="tw-mb-4 tw-pb-4 tw-border-b tw-border-slate-100">
                <div class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider tw-mb-3">{{ strtoupper($l) }}</div>
                <div class="tw-space-y-3">
                    <div class="tw-grid tw-grid-cols-3 tw-items-center tw-gap-3">
                        <label class="tw-text-xs tw-font-semibold tw-text-slate-600">Label</label>
                        <input type="text" name="link_label{{ $l }}" class="tw-col-span-2">
                    </div>
                    <div class="tw-grid tw-grid-cols-3 tw-items-center tw-gap-3">
                        <label class="tw-text-xs tw-font-semibold tw-text-slate-600">URL</label>
                        <input type="text" name="link_url{{ $l }}" class="tw-col-span-2">
                    </div>
                </div>
            </div>
            @endforeach

            <div class="tw-space-y-4 tw-mt-4">
                <div class="tw-grid tw-grid-cols-3 tw-items-center tw-gap-3">
                    <label class="tw-text-xs tw-font-semibold tw-text-slate-600">Icon</label>
                    <input type="text" name="selected_icon" placeholder="fa-icon" class="tw-col-span-2">
                </div>
                <div class="tw-grid tw-grid-cols-3 tw-items-center tw-gap-3">
                    <label class="tw-text-xs tw-font-semibold tw-text-slate-600">Target</label>
                    <select name="link_target" class="tw-col-span-2">
                        <option value="_self">Open in same window</option>
                        <option value="_blank">Open in new window</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn indigo tw-w-full tw-mt-8"><i class="fa fa-check"></i> Save</button>
        </form>
    </div>
</div>

{{-- Nestable CSS (required for drag-and-drop functionality) --}}
<style type="text/css">
.dd { position: relative; display: block; margin: 0; padding: 0px 10px; list-style: none; font-size: 13px; line-height: 20px; min-height:180px; }
.dd-list { display: block; position: relative; margin: 0; padding: 0; list-style: none; }
.dd-list .dd-list { padding-left: 30px; }
.dd-collapsed .dd-list { display: none; }
.dd-item, .dd-empty, .dd-placeholder { display: block; position: relative; margin: 0; padding: 0; min-height: 20px; font-size: 13px; line-height: 20px; }
.dd-handle { display: block; height: 30px; margin: 5px 0; padding: 5px 10px; color: #333; text-decoration: none; font-weight: bold; border: 1px solid #ccc; cursor:move;
    background: #fafafa; background: linear-gradient(top, #fafafa 0%, #eee 100%); border-radius: 3px; box-sizing: border-box; }
.dd-handle:hover { color: #2ea8e5; background: #fff; }
.dd-item > button { display: block; position: relative; cursor: pointer; float: left; width: 25px; height: 20px; margin: 5px 0; padding: 0; text-indent: 100%; white-space: nowrap; overflow: hidden; border: 0; background: transparent; font-size: 12px; line-height: 1; text-align: center; font-weight: bold; }
.dd-item > button:before { content: '+'; display: block; position: absolute; width: 100%; text-align: center; text-indent: 0; }
.dd-item > button[data-action="collapse"]:before { content: '-'; }
.dd-placeholder, .dd-empty { margin: 5px 0; padding: 0; min-height: 30px; background: #B7DBFF; border: 1px dashed #666; box-sizing: border-box; }
.dd-empty { border: 1px dashed #bbb; min-height: 100px; background-color: #e5e5e5; }
.dd-dragel { position: absolute; pointer-events: none; z-index: 1000; }
.dd-dragel > .dd-item .dd-handle { margin-top: 0; }
.dd-dragel .dd-handle { box-shadow: 2px 4px 6px 0 rgba(0,0,0,.1); }
.dd-hover > .dd-handle { background: #2ea8e5 !important; }
</style>

@endsection

@push('scripts')
<script src="{{ asset('assets/admin/js/jquery.nestable.js') }}"></script>
<script>
    $('#nestable').nestable({
        group: 1
    });

    function save_menu(){
        var datax = '';
        var order = 0;
        $('.dd-item').each(function() {
            var parent = $(this).parents('li').attr('id'); 
            if (parent == undefined){ parent = 0; }
            order = order + 1;    
            var id = $(this).attr('id');
            datax += 'items:' + order + ',' + parent + ',' + id;
        });
        
        $('#ajax').html('<div class="tw-bg-blue-50 tw-border-l-4 tw-border-blue-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3"><i class="fa fa-spinner fa-spin tw-text-blue-500"></i><span class="tw-text-blue-800 tw-font-bold tw-text-sm">Updating menu order...</span></div>');
        
        $.ajax({
            method: "POST",
            url: "{{ route('admin.nav.save-order') }}",
            data: {
                _token: "{{ csrf_token() }}",
                items: datax
            }
        })
        .done(function( html ) {
            $('#ajax').html('<div class="tw-bg-emerald-50 tw-border-l-4 tw-border-emerald-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3"><i class="fa fa-check-circle tw-text-emerald-500"></i><span class="tw-text-emerald-800 tw-font-bold tw-text-sm">Menu order saved!</span></div>');
        })
        .fail(function() {
            $('#ajax').html('<div class="tw-bg-rose-50 tw-border-l-4 tw-border-rose-500 tw-p-4 tw-rounded-xl tw-flex tw-items-center tw-gap-3"><i class="fa fa-exclamation-circle tw-text-rose-500"></i><span class="tw-text-rose-800 tw-font-bold tw-text-sm">Error updating menu</span></div>');
        });
    }
</script>
@endpush
