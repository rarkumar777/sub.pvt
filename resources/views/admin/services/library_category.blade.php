@extends('admin.layouts.app')
@section('title', $catName . ' | Library | Admin')
@push('head')
<style>
.cat-outer { background:#f5f0e8; min-height:100vh; padding: 24px 0 80px; margin: -45px -40px -45px -40px; }
.cat-header { background:#ea580c; padding:14px 28px; display:flex; align-items:center; gap:14px; color:#fff; }
.cat-header h1 { font-size:18px; font-weight:700; margin:0; display:flex; align-items:center; gap:10px; }
.cat-search-bar { max-width:800px; margin:24px auto; padding:0 24px; }
.cat-search-input { width:100%; height:48px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:0 16px; font-size:14px; color:#1e293b; outline:none; transition:border-color .2s; box-sizing:border-box; }
.cat-search-input:focus { border-color:#ea580c; }
.cat-container { max-width:880px; margin:0 auto; padding:0 24px; }
.cat-count { font-size:12px; color:#64748b; font-weight:700; margin-bottom:16px; }
.cat-card { background:#fff; border:1px solid #e2e8f0; border-radius:4px; padding:12px 16px; display:flex; align-items:center; gap:16px; margin-bottom:12px; position:relative; }
.cat-card-thumb { width:64px; height:64px; border-radius:4px; object-fit:cover; flex-shrink:0; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#ea580c; font-size:22px; }
.cat-card-info { flex:1; min-width:0; }
.cat-card-title { color:#ea580c; font-size:14px; font-weight:700; text-decoration:none; display:block; }
.cat-card-loc { display:flex; align-items:center; gap:6px; color:#64748b; font-size:12px; margin-top:3px; }
.cat-card-dots { color:#94a3b8; font-size:20px; cursor:pointer; background:none; border:none; position:relative; }
.cat-dropdown { display:none; position:absolute; right:0; top:28px; background:#fff; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,.12); min-width:160px; z-index:100; overflow:hidden; border:1px solid rgba(0,0,0,.06); }
.cat-dropdown a { display:flex; align-items:center; gap:10px; padding:11px 16px; font-size:13px; font-weight:600; color:#555; text-decoration:none; }
.cat-dropdown a:hover { background:#f8f8f8; color:#ea580c; }
.cat-empty { text-align:center; padding:60px 20px; color:#94a3b8; }
.cat-empty i { font-size:40px; margin-bottom:12px; display:block; }
.cat-empty p { font-size:14px; font-weight:600; }
.cat-fab { position:fixed; bottom:24px; right:24px; z-index:2000; }
.cat-fab-btn { background:#ea580c; color:#fff; border:none; border-radius:30px; height:48px; display:flex; align-items:center; gap:10px; padding:0 24px; font-weight:700; font-size:13px; box-shadow:0 4px 12px rgba(0,0,0,0.2); cursor:pointer; }
/* Modal */
.cat-modal-bg { position:fixed; inset:0; background:rgba(0,0,0,.5); backdrop-filter:blur(6px); z-index:99999; display:none; align-items:center; justify-content:center; padding:20px; }
.cat-modal-box { background:#fff; border-radius:16px; max-width:1050px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 50px rgba(0,0,0,.2); }
.cat-modal-head { padding:20px 24px; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between; }
.cat-modal-head h3 { margin:0; font-size:17px; font-weight:800; color:#222; }
.cat-modal-body { padding:24px; }
/* Form styles */
.lib-cat-btn { background:#fff; border:1px solid #e2e8f0; border-radius:4px; padding:8px 16px; font-size:13px; font-weight:700; color:#1e293b; cursor:pointer; }
.lib-cat-btn.active { background:#ffedd5; border-color:#ea580c; color:#ea580c; }
/* Autocomplete dropdown */
#catArrivalDropdown { position:absolute; left:0; right:0; top:100%; background:#fff; border:1px solid #e2e8f0; border-top:none; border-radius:0 0 10px 10px; box-shadow:0 8px 24px rgba(0,0,0,.1); z-index:9999; max-height:220px; overflow-y:auto; display:none; }
</style>
@endpush

@section('content')
<div class="cat-outer">
    {{-- Header --}}
    <div class="cat-header">
        <h1><i class="fa {{ $catIcon }}"></i> {{ $catName }}</h1>
        <a href="{{ route('admin.library') }}" style="margin-left:auto; font-size:12px; font-weight:700; color:rgba(255,255,255,.8); text-decoration:none;">← Back to Library</a>
    </div>

    {{-- Search --}}
    <div class="cat-search-bar">
        <form method="GET" action="">
            <input type="hidden" name="country" value="{{ $countryId }}">
            <input type="text" name="search" class="cat-search-input" value="{{ $search }}" placeholder="Search {{ $catName }}...">
        </form>
    </div>

    <div class="cat-container">
        <div class="cat-count">{{ $services->count() }} item(s) found</div>

        @if($services->isEmpty())
            <div class="cat-empty">
                <i class="fa {{ $catIcon }}"></i>
                <p>No {{ $catName }} found.</p>
            </div>
        @else
            @foreach($services as $svc)
                @php
                    $title = $svc->description ?? $svc->descriptionL ?? '(No title)';
                    // Transport: show departure → arrival as subtitle
                    $subLocation = '';
                    if ($segment === 'transport') {
                        $dep = $svc->departure_location ?? '';
                        $arr = $svc->arrival_destination ?? $svc->arrival ?? '';
                        $subLocation = trim($dep . ($dep && $arr ? ' → ' : '') . $arr);
                    } else {
                        $subLocation = optional(optional($svc->serviceCategory)->parent)->name
                            ?? optional($svc->serviceCategory)->name ?? '';
                        if (!$subLocation && !empty($svc->arrival)) $subLocation = $svc->arrival;
                    }
                    $images = [];
                    if (!empty($svc->images)) {
                        $images = is_array($svc->images) ? $svc->images : (@unserialize($svc->images) ?: []);
                    }
                    if (empty($images) && !empty($svc->image)) {
                        $raw = @unserialize($svc->image);
                        $images = is_array($raw) ? $raw : [$svc->image];
                    }
                    $thumb = collect($images)->filter()->first();
                    if ($thumb && !str_starts_with($thumb, 'http')) $thumb = '/' . ltrim($thumb, '/');
                @endphp
                <div class="cat-card">
                    @if($thumb)
                        <img src="{{ $thumb }}" class="cat-card-thumb" alt="{{ $title }}">
                    @else
                        <div class="cat-card-thumb"><i class="fa {{ $catIcon }}"></i></div>
                    @endif
                    <div class="cat-card-info">
                        <span class="cat-card-title">{{ Str::limit($title, 80) }}</span>
                        @if($subLocation)
                            <div class="cat-card-loc"><i class="fa fa-map-marker"></i> {{ $subLocation }}</div>
                        @endif
                    </div>
                    <button class="cat-card-dots" onclick="toggleCatMenu(event, this)">⋮
                        <div class="cat-dropdown" onclick="event.stopPropagation()">
                            <a href="#" onclick="editSvc({{ $svc->id }}, '{{ $segment }}'); return false;"><i class="fa fa-pencil"></i> Edit</a>
                            <a href="#" onclick="delSvc({{ $svc->id }}, '{{ addslashes($title) }}', '{{ $segment }}'); return false;" style="color:#ef4444;"><i class="fa fa-trash"></i> Delete</a>
                        </div>
                    </button>
                </div>
            @endforeach
        @endif
    </div>
</div>

{{-- ADD Button --}}
<div class="cat-fab">
    <button class="cat-fab-btn" onclick="openAddModal()">
        <i class="fa fa-plus"></i> ADD
    </button>
</div>

{{-- Add/Edit Modal --}}
<div class="cat-modal-bg" id="catModal">
    <div class="cat-modal-box">
        <div class="cat-modal-head" id="catModalHead">
            <h3 id="catModalTitle"></h3>
            <div style="display:flex; gap:10px; align-items:center;">
                <a href="javascript:void(0)" onclick="closeCatModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none;">Cancel</a>
                <button form="catCreateForm" type="submit" id="catSubmitBtn" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer;">Create</button>
            </div>
        </div>
        <div class="cat-modal-body" id="catModalBody"></div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="cat-modal-bg" id="catDelModal">
    <div style="background:#fff; border-radius:16px; max-width:400px; width:100%; text-align:center; padding:30px; box-shadow:0 20px 50px rgba(0,0,0,.2);">
        <i class="fa fa-trash" style="font-size:32px; color:#ef4444; margin-bottom:16px; display:block;"></i>
        <h3 style="font-size:18px; font-weight:800; margin:0 0 8px;">Delete?</h3>
        <p id="catDelName" style="color:#666; font-size:14px; margin:0 0 24px;"></p>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button onclick="closeCatDel()" style="padding:10px 20px; border-radius:6px; border:1px solid #ddd; background:#fff; color:#555; font-size:13px; font-weight:600; cursor:pointer; flex:1;">Cancel</button>
            <button onclick="confirmCatDel()" style="padding:10px 20px; border-radius:6px; border:none; background:#ef4444; color:#fff; font-size:13px; font-weight:600; cursor:pointer; flex:1;"><i class="fa fa-trash"></i> Delete</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var catDelId = null, catDelType = null;
var currentCatType = '{{ $segment }}';
var currentCountryId = {{ $countryId ?? 0 }};
window.svcDt = new DataTransfer();

// ── Dropdown Menu ──
function toggleCatMenu(e, btn) {
    e.stopPropagation();
    document.querySelectorAll('.cat-dropdown').forEach(function(d) { d.style.display = 'none'; });
    var d = btn.querySelector('.cat-dropdown');
    d.style.display = d.style.display === 'block' ? 'none' : 'block';
}
document.addEventListener('click', function() {
    document.querySelectorAll('.cat-dropdown').forEach(function(d) { d.style.display = 'none'; });
});

// ── Modal Open/Close ──
function openCatModal(title, submitLabel) {
    document.getElementById('catModalTitle').textContent = title;
    document.getElementById('catSubmitBtn').textContent = submitLabel || 'Create';
    document.getElementById('catModalBody').innerHTML = '<div style="text-align:center;padding:40px"><i class="fa fa-spinner fa-spin" style="font-size:28px;color:#ea580c"></i></div>';
    document.getElementById('catModal').style.display = 'flex';
}
function closeCatModal() {
    document.getElementById('catModal').style.display = 'none';
    window.svcDt = new DataTransfer();
}

// ── ADD Button ──
function openAddModal() {
    var titles = {
        accommodation: 'Add accommodation',
        transport: 'Create transport type',
        restaurant: 'Add a restaurant',
        activity: 'Enter an activity',
        guide: 'Add a guide'
    };
    openCatModal(titles[currentCatType] || 'Add ' + currentCatType);
    document.getElementById('catModalBody').innerHTML = getTypeForm(currentCatType);
}

// ── Edit ──
function editSvc(id, svcType) {
    openCatModal('Edit', 'Save');
    document.getElementById('catSubmitBtn').style.display = 'none';
    var url = '/admin/services/' + id + '/edit?ajax=1&service_type=' + svcType;
    $.get(url, function(r) { $('#catModalBody').html(r.html); });
}

// ── Delete ──
function delSvc(id, name, svcType) {
    catDelId = id; catDelType = svcType;
    document.getElementById('catDelName').textContent = '"' + name + '"';
    document.getElementById('catDelModal').style.display = 'flex';
}
function closeCatDel() { document.getElementById('catDelModal').style.display = 'none'; catDelId = null; }
function confirmCatDel() {
    if (!catDelId) return;
    $.ajax({
        url: '/admin/services/' + catDelId,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}', _method: 'DELETE', service_type: catDelType },
        success: function() { closeCatDel(); location.reload(); },
        error: function(x) { alert('Error: ' + (x.responseJSON?.message || 'Could not delete')); }
    });
}

// ── Form Builder ──
function getTypeForm(type) {
    var catId = type==='transport'?715:type==='activity'?93:type==='restaurant'?456:type==='guide'?527:403;
    var submitUrl = '{{ route("admin.services.store") }}';

    var html = '<form id="catCreateForm" onsubmit="return submitCatForm(event)">' +
        '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
        '<input type="hidden" name="country" value="' + currentCountryId + '">' +
        '<input type="hidden" name="category" value="' + catId + '">' +
        '<input type="hidden" name="method" value="Car">' +
        '<input type="hidden" name="service_type" value="' + (type==='transport'?'transport':type) + '">';

    if (type === 'transport') {
        html += '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">' +
            langFlag('🇫🇷','fr',false)+langFlag('🇬🇧','en',true)+langFlag('🇮🇹','it',false)+langFlag('🇪🇸','es',false)+langFlag('🇩🇪','de',false)+langFlag('🇸🇪','se',false)+langFlag('🇳🇱','nl',false)+
            '</div>';
        html += '<div style="margin-bottom:16px"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Transport title</label>' +
            '<textarea name="description" required rows="2" style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;resize:vertical"></textarea></div>' +
            '<div style="margin-bottom:16px"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Method of transport</label>' +
            '<div style="display:flex;gap:10px;flex-wrap:wrap">' +
            methodBtn('Bus','fa-bus',false)+methodBtn('Airplane','fa-plane',false)+methodBtn('Car','fa-car',true)+methodBtn('Boat','fa-ship',false)+methodBtn('Train','fa-train',false)+
            '</div></div>' +
            '<div style="display:flex;gap:16px;margin-bottom:16px">' +
            '<div style="flex:1"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Departure</label><input type="text" name="departure" style="width:100%;height:38px;border:1px solid #ddd;border-radius:8px;padding:0 12px;font-size:13px;outline:none"></div>' +
            '<div style="flex:1"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Arrival</label><input type="text" name="arrival" style="width:100%;height:38px;border:1px solid #ddd;border-radius:8px;padding:0 12px;font-size:13px;outline:none"></div>' +
            '</div>' +
            '<div style="margin-bottom:16px"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Description</label>' +
            '<textarea name="notes" rows="3" style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;resize:vertical" placeholder="Add a description"></textarea></div>';

    } else if (type === 'activity' || type === 'restaurant' || type === 'guide') {
        var label = type==='restaurant'?'Restaurant name':type==='guide'?'Guide name':'Activity name';
        html += '<input type="hidden" name="cost" value="0">' +
            '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">' +
            langFlag('🇫🇷','fr',false)+langFlag('🇬🇧','en',true)+langFlag('🇮🇹','it',false)+langFlag('🇪🇸','es',false)+langFlag('🇩🇪','de',false)+langFlag('🇸🇪','se',false)+langFlag('🇳🇱','nl',false)+
            '</div>' +
            '<div style="margin-bottom:16px"><div style="display:flex;align-items:center;gap:8px;margin-bottom:6px"><span style="font-size:11px;font-weight:700;color:#555">Photos:</span></div>' +
            '<div id="svcPhotosRow" style="display:flex;gap:12px;height:120px;">' +
            '<div id="svcImageDrop" onclick="document.getElementById(\'svcImageInput\').click()" style="flex:1;min-width:100px;border:1px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:24px;cursor:pointer;"><i class="fa fa-camera"></i></div>' +
            '</div><input type="file" name="new_images[]" id="svcImageInput" accept="image/*" multiple style="display:none" onchange="previewSvcImageGrid(this)"></div>' +
            '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px;position:relative"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">' + label + '</legend>' +
            '<input type="text" name="description" required style="width:100%;height:36px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent">' +
            '</fieldset>' +
            '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px;position:relative"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Place of interest</legend>' +
            '<input type="text" id="catArrivalInput" name="arrival" autocomplete="off" oninput="catPlaceAutocomplete(this.value)" onkeydown="catPlaceKey(event)" style="width:100%;height:36px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent" placeholder="Enter destination...">' +
            '<div id="catArrivalDropdown"></div></fieldset>' +
            '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Description</legend>' +
            '<textarea name="notes" style="width:100%;min-height:120px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent" placeholder="Add a description"></textarea></fieldset>';

    } else if (type === 'accommodation') {
        html += '<input type="hidden" name="cost" value="0">' +
            '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">' +
            langFlag('🇫🇷','fr',false)+langFlag('🇬🇧','en',true)+langFlag('🇮🇹','it',false)+langFlag('🇪🇸','es',false)+langFlag('🇩🇪','de',false)+langFlag('🇸🇪','se',false)+langFlag('🇳🇱','nl',false)+
            '</div>' +
            '<div style="margin-bottom:16px"><span style="font-size:11px;font-weight:700;color:#555">Photos:</span>' +
            '<div id="svcPhotosRow" style="display:flex;gap:12px;height:120px;margin-top:6px">' +
            '<div id="svcImageDrop" onclick="document.getElementById(\'svcImageInput\').click()" style="flex:1;min-width:100px;border:1px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:24px;cursor:pointer;"><i class="fa fa-camera"></i></div>' +
            '</div><input type="file" name="new_images[]" id="svcImageInput" accept="image/*" multiple style="display:none" onchange="previewSvcImageGrid(this)"></div>' +
            '<div style="display:flex;gap:16px">' +
            '<div style="flex:1">' +
            '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px;position:relative"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Name of accommodation</legend>' +
            '<input type="text" name="description" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent"></fieldset>' +
            '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Description</legend>' +
            '<textarea name="notes" style="width:100%;min-height:120px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent" placeholder="Add a description"></textarea></fieldset>' +
            '</div>' +
            '<div style="flex:1">' +
            '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px;position:relative"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Place of interest</legend>' +
            '<input type="text" id="catArrivalInput" name="arrival" autocomplete="off" oninput="catPlaceAutocomplete(this.value)" onkeydown="catPlaceKey(event)" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent" placeholder="Enter destination...">' +
            '<div id="catArrivalDropdown"></div></fieldset>' +
            '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Category</legend>' +
            '<select name="acc_category" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555">' +
            '<option value="">Select a category</option><option value="1 Star">1 Star</option><option value="2 Star">2 Star</option>' +
            '<option value="3 Star">3 Star</option><option value="4 Star">4 Star</option><option value="5 Star">5 Star</option>' +
            '<option value="Standard">Standard</option><option value="Superior">Superior</option><option value="Luxury">Luxury</option>' +
            '</select></fieldset>' +
            '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Website</legend>' +
            '<input type="text" name="website" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent"></fieldset>' +
            '</div></div>';
    }

    html += '</form>';
    return html;
}

function langFlag(emoji, code, active) {
    var bg = active ? '#ea580c' : 'transparent';
    var border = active ? '2px solid #ea580c' : '2px solid transparent';
    return '<div class="lang-flag" data-lang="' + code + '" onclick="pickLang(this)" style="width:40px;height:32px;border-radius:6px;border:' + border + ';background:' + bg + ';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;transition:all .2s">' + emoji + '</div>';
}
function pickLang(el) {
    document.querySelectorAll('.lang-flag').forEach(function(f) { f.style.border = '2px solid transparent'; f.style.background = 'transparent'; });
    el.style.border = '2px solid #ea580c'; el.style.background = '#ea580c';
}
function methodBtn(label, icon, active) {
    var border = active ? '2px solid #ea580c' : '2px solid #ddd';
    var bg = active ? '#ffedd5' : '#fff';
    var clr = active ? '#ea580c' : '#888';
    return '<div class="method-opt" data-val="' + label + '" onclick="pickMethod(this)"><div style="width:56px;height:56px;border-radius:10px;border:' + border + ';background:' + bg + ';display:flex;align-items:center;justify-content:center;cursor:pointer"><i class="fa ' + icon + '" style="font-size:22px;color:' + clr + '"></i></div><span style="font-size:11px;color:' + clr + ';font-weight:700;margin-top:4px;display:block;text-align:center">' + label + '</span></div>';
}
function pickMethod(el) {
    document.querySelectorAll('.method-opt').forEach(function(m) {
        m.querySelector('div').style.border='2px solid #ddd'; m.querySelector('div').style.background='#fff';
        m.querySelector('i').style.color='#888'; m.querySelector('span').style.color='#888';
    });
    el.querySelector('div').style.border='2px solid #ea580c'; el.querySelector('div').style.background='#ffedd5';
    el.querySelector('i').style.color='#ea580c'; el.querySelector('span').style.color='#ea580c';
    document.querySelector('[name=method]').value = el.dataset.val;
}
function previewSvcImageGrid(input) {
    if (input.files && input.files.length > 0) {
        for (var i = 0; i < input.files.length; i++) { window.svcDt.items.add(input.files[i]); }
    }
    input.files = window.svcDt.files;
    var row = document.getElementById('svcPhotosRow');
    var addBtn = document.getElementById('svcImageDrop');
    row.querySelectorAll('.svc-photo-wrap').forEach(function(e) { e.remove(); });
    for (var i = 0; i < window.svcDt.files.length; i++) {
        (function(idx) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var div = document.createElement('div');
                div.className = 'svc-photo-wrap';
                div.style.cssText = 'position:relative;flex-shrink:0;height:100%;aspect-ratio:1.5;border-radius:4px;overflow:hidden';
                div.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover"><div style="position:absolute;top:6px;right:6px;width:24px;height:24px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer" onclick="removeImg(' + idx + ')"><i class="fa fa-trash" style="color:#555;font-size:12px"></i></div>';
                row.insertBefore(div, addBtn);
            };
            reader.readAsDataURL(window.svcDt.files[idx]);
        })(i);
    }
}
function removeImg(idx) {
    var newDt = new DataTransfer();
    for (var i = 0; i < window.svcDt.files.length; i++) { if (i !== idx) newDt.items.add(window.svcDt.files[i]); }
    window.svcDt = newDt;
    var inp = document.getElementById('svcImageInput');
    if (inp) { inp.files = newDt.files; previewSvcImageGrid(inp); }
}

// ── Form Submit ──
function submitCatForm(e) {
    e.preventDefault();
    var form = document.getElementById('catCreateForm');
    var fd = new FormData(form);
    var btn = document.getElementById('catSubmitBtn');
    btn.disabled = true; btn.textContent = 'Creating...';
    $.ajax({
        url: '{{ route("admin.services.store") }}',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function() {
            closeCatModal();
            showToast('Added successfully!');
            setTimeout(function() { location.reload(); }, 800);
        },
        error: function(x) {
            btn.disabled = false; btn.textContent = 'Create';
            showToast('Error: ' + (x.responseJSON?.message || 'Could not create'), 'error');
        }
    });
    return false;
}

// ── Toast ──
function showToast(msg, type) {
    var old = document.getElementById('catToast'); if (old) old.remove();
    var bg = type === 'error' ? '#ef4444' : '#ea580c';
    var t = document.createElement('div');
    t.id = 'catToast';
    t.style.cssText = 'position:fixed;top:24px;right:24px;z-index:999999;display:flex;align-items:center;gap:10px;padding:14px 24px;border-radius:10px;background:' + bg + ';color:#fff;font-size:14px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.18);transform:translateX(120%);transition:transform .4s ease;';
    t.innerHTML = '<i class="fa fa-check-circle" style="font-size:18px"></i> ' + msg;
    document.body.appendChild(t);
    setTimeout(function() { t.style.transform = 'translateX(0)'; }, 50);
    setTimeout(function() { t.style.transform = 'translateX(120%)'; setTimeout(function() { t.remove(); }, 500); }, 3000);
}

// ── Modal close on backdrop ──
document.getElementById('catModal').addEventListener('click', function(e) { if (e.target === this) closeCatModal(); });
document.getElementById('catDelModal').addEventListener('click', function(e) { if (e.target === this) closeCatDel(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeCatModal(); closeCatDel(); } });

// ── Place of Interest Autocomplete (Nominatim) ──
var _catPlaceTimer = null;
var _catPlaceIdx = -1;

function catEscape(v) {
    return String(v || '').replace(/[&<>"']/g, function(c) {
        return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[c];
    });
}

function catPlaceAutocomplete(query) {
    clearTimeout(_catPlaceTimer);
    _catPlaceIdx = -1;
    var dd = document.getElementById('catArrivalDropdown');
    if (!dd) return;
    if (!query || query.length < 2) { dd.style.display = 'none'; dd.innerHTML = ''; return; }
    _catPlaceTimer = setTimeout(function() {
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&addressdetails=1&limit=6&accept-language=en')
            .then(function(r) { return r.json(); })
            .then(function(results) {
                dd.innerHTML = '';
                if (!results || !results.length) { dd.style.display = 'none'; return; }
                results.forEach(function(place, idx) {
                    var addr = place.address || {};
                    var city = addr.city || addr.town || addr.village || addr.hamlet || addr.county || '';
                    var state = addr.state || '';
                    var country = addr.country || '';
                    var parts = [];
                    if (city) parts.push(city);
                    if (state && state !== city) parts.push(state);
                    var item = document.createElement('div');
                    item.style.cssText = 'display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;color:#1e293b;cursor:pointer;border-bottom:1px solid #f1f5f9;transition:background .15s;';
                    item.setAttribute('data-idx', idx);
                    item.innerHTML = '<i class="fa fa-map-marker" style="color:#9ca3af;font-size:13px;flex-shrink:0;"></i>' +
                        '<span>' +
                        (parts.length ? '<span style="font-weight:600;color:#1e293b;">' + catEscape(parts.join(', ')) + '</span> ' : '') +
                        (country ? '<span style="font-weight:700;color:#ea580c;">' + catEscape(country) + '</span>' : '') +
                        '</span>';
                    item.onmouseover = function() { this.style.background = '#fff7ed'; };
                    item.onmouseout = function() { this.style.background = (_catPlaceIdx === idx ? '#fff7ed' : ''); };
                    item.onclick = function() {
                        var label = city || state || country || place.display_name;
                        var inp = document.getElementById('catArrivalInput');
                        if (inp) inp.value = label;
                        dd.style.display = 'none';
                        dd.innerHTML = '';
                    };
                    dd.appendChild(item);
                });
                dd.style.display = 'block';
            })
            .catch(function() { dd.style.display = 'none'; });
    }, 300);
}

function catPlaceKey(event) {
    var dd = document.getElementById('catArrivalDropdown');
    var items = dd ? dd.querySelectorAll('div[data-idx]') : [];
    if (!dd || dd.style.display === 'none' || !items.length) return;
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        _catPlaceIdx = Math.min(_catPlaceIdx + 1, items.length - 1);
        items.forEach(function(el, i) { el.style.background = (i === _catPlaceIdx ? '#fff7ed' : ''); });
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        _catPlaceIdx = Math.max(_catPlaceIdx - 1, 0);
        items.forEach(function(el, i) { el.style.background = (i === _catPlaceIdx ? '#fff7ed' : ''); });
    } else if (event.key === 'Enter' && _catPlaceIdx >= 0 && items[_catPlaceIdx]) {
        event.preventDefault();
        items[_catPlaceIdx].click();
    } else if (event.key === 'Escape') {
        dd.style.display = 'none';
    }
}

document.addEventListener('click', function(e) {
    var dd = document.getElementById('catArrivalDropdown');
    var inp = document.getElementById('catArrivalInput');
    if (dd && !e.target.closest('#catArrivalDropdown') && e.target !== inp) {
        dd.style.display = 'none';
    }
});
</script>
@endpush
