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
.cat-modal-box { background:#fff; border-radius:16px; max-width:1250px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 50px rgba(0,0,0,.2); }
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
                    // Guide stores images in 'arrival_destination' column (en33_services has no 'image' column)
                    $imgSource = ($segment === 'guide') ? ($svc->arrival_destination ?? '') : ($svc->image ?? '');
                    if (!empty($svc->images)) {
                        $rawList = is_string($svc->images) ? (@unserialize($svc->images) ?: @json_decode($svc->images, true)) : $svc->images;
                        $images = is_array($rawList) ? $rawList : [];
                    }
                    if (empty($images) && !empty($imgSource)) {
                        $rawSingle = is_string($imgSource) ? (@unserialize($imgSource) ?: @json_decode($imgSource, true)) : $imgSource;
                        $images = is_array($rawSingle) ? $rawSingle : (is_string($imgSource) ? [$imgSource] : []);
                    }
                    $thumb = collect($images)->filter(function($val) { return is_string($val) && trim($val) !== ''; })->first();
                    if ($thumb) {
                        $thumb = str_replace(['public/', '/public/'], '', $thumb);
                        if (!str_starts_with($thumb, 'http')) $thumb = '/' . ltrim($thumb, '/');
                    }
                @endphp
                <div class="cat-card" onclick="editSvc({{ $svc->id }}, '{{ $segment }}')" style="cursor: pointer;">
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
var vendersData = {!! isset($venders) ? json_encode($venders) : '[]' !!};
var transCompaniesData = {!! isset($transCompanies) ? json_encode($transCompanies) : '[]' !!};
var companyMethodData = {!! isset($companyMethodData) ? json_encode($companyMethodData) : '{}' !!};
var restCategoriesData = {!! isset($restCategoriesData) ? json_encode($restCategoriesData) : '[]' !!};
var restSubServicesData = {!! isset($restSubServicesData) ? json_encode($restSubServicesData) : '[]' !!};
var allServicesData = {!! isset($services) ? json_encode($services) : '[]' !!};
var actCategoriesData = {!! isset($actCategoriesData) ? json_encode($actCategoriesData) : '[]' !!};
var guideCategoriesData = {!! isset($guideCategoriesData) ? json_encode($guideCategoriesData) : '[]' !!};
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
var originalCatModalHead = '';
function openCatModal(title, submitLabel) {
    var head = document.getElementById('catModalHead');
    if (!originalCatModalHead && head) {
        originalCatModalHead = head.innerHTML;
    } else if (originalCatModalHead && head) {
        head.innerHTML = originalCatModalHead;
    }
    var t = document.getElementById('catModalTitle');
    if (t) t.textContent = title;
    var b = document.getElementById('catSubmitBtn');
    if (b) b.textContent = submitLabel || 'Create';
    document.getElementById('catModalBody').innerHTML = '<div style="text-align:center;padding:40px"><i class="fa fa-spinner fa-spin" style="font-size:28px;color:#ea580c"></i></div>';
    document.getElementById('catModal').style.display = 'flex';
}
function closeCatModal() {
    document.getElementById('catModal').style.display = 'none';
    window.svcDt = new DataTransfer();
}
window.closeModal = function() { closeCatModal(); };

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

    setTimeout(function() {
        function updateAccommodationName(vId) {
            if (currentCatType === 'accommodation' || currentCatType === 'transport') {
                var descInput = document.querySelector('input[name="description"]');
                if (descInput) {
                    if (vId) {
                        var vName = '';
                        if (currentCatType === 'transport' && typeof transCompaniesData !== 'undefined') {
                            var tc = transCompaniesData.find(function(x) { return x.id == vId; });
                            if (tc) vName = tc.name;
                        } else if (currentCatType === 'restaurant' && typeof restCategoriesData !== 'undefined') {
                            restCategoriesData.forEach(function(group) {
                                var r = group.restaurants.find(function(x) { return x.id == vId; });
                                if (r) vName = r.name;
                            });
                        } else {
                            var v = vendersData.find(function(x) { return x.id == vId; });
                            if (v) {
                                vName = v.company ? v.company : (v.first_name + ' ' + (v.last_name || ''));
                                if (!vName.trim()) vName = v.email;
                            }
                        }
                        if (vName) {
                            descInput.value = vName;
                            var addCount = document.getElementById('transTitleCountAdd');
                            if(addCount) addCount.textContent = '(' + vName.length + '/255)';
                        }
                    } else {
                        descInput.value = '';
                        var addCount2 = document.getElementById('transTitleCountAdd');
                        if(addCount2) addCount2.textContent = '(0/255)';
                    }
                }
            }
        }

        function triggerFetch(vId) {
            var pCon = document.getElementById('propServicesContainer');
            if (!pCon || !vId) return;
            pCon.innerHTML = '<div style="text-align:center;padding:10px;"><i class="fa fa-spinner fa-spin" style="color:#ea580c"></i> Loading services...</div>';
            $.get('/admin/vendor/'+vId+'/activities', function(res) {
                pCon.innerHTML = '';
                if (res && res.length > 0) {
                    res.forEach(function(act) {
                        addPropServiceRow(act.description, act.cost, act.id);
                    });
                } else {
                    pCon.innerHTML = '<div style="font-size:12px;color:#718096;text-align:center;padding:10px;border-radius:6px;background:#f8fafc">No existing property services found for this vendor in the database.</div>';
                }
            }).fail(function() {
                pCon.innerHTML = '<div style="color:red;font-size:12px;padding:10px;">Failed to load services.</div>';
            });
        }

        window.restEditRowId = null;
        window.currentRestServices = [];

        function getActiveRestContainer() {
            var cCon = null;
            var catBody = document.getElementById("catModalBody");
            if (catBody) { cCon = catBody.querySelector("#restServicesContainer"); }
            if (!cCon) {
                var cc = document.querySelectorAll('#restServicesContainer');
                if (cc.length > 0) {
                    for(var i = 0; i < cc.length; i++) {
                        if(cc[i].offsetParent !== null) return cc[i];
                    }
                    return cc[cc.length - 1];
                }
            }
            return cCon;
        }

        function renderRestSvcTable() {
            var cCon = getActiveRestContainer();
            if (!cCon) return;
            var services = window.currentRestServices;
            if (services && services.length > 0) {
                var htmlOrig = '<table style="width:100%;border-collapse:collapse;font-size:12px;">' +
                    '<thead><tr style="border-bottom:1px solid #eee;">' +
                    '<th style="text-align:left;padding:8px 6px;color:#64748b;font-size:10px;">DESCRIPTION</th>' +
                    '<th style="text-align:left;padding:8px 6px;color:#64748b;font-size:10px;">COST</th>' +
                    '<th style="text-align:right;padding:8px 6px;color:#64748b;font-size:10px;">ACTIONS</th>' +
                    '</tr></thead><tbody>';
                services.forEach(function(act) {
                    var isEdit = (window.restEditRowId === act.id);
                    var rawDesc = act.description !== null && act.description !== undefined ? String(act.description) : '-';
                    if (isEdit) {
                        htmlOrig += '<tr style="border-bottom:1px solid #f5f5f5;">' +
                            '<td style="padding:4px 6px;"><input type="text" id="edit_rest_desc_'+act.id+'" value="' + rawDesc.replace(/"/g,'&quot;') + '" style="width:100%;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;padding:0 6px;outline:none;"></td>' +
                            '<td style="padding:4px 6px;"><input type="number" step="0.01" id="edit_rest_cost_'+act.id+'" value="' + (act.cost || 0) + '" style="width:80px;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;padding:0 6px;outline:none;"></td>' +
                            '<td style="padding:4px 6px;text-align:right;">' +
                            '<a href="javascript:void(0)" onclick="saveRestSubSvc(' + act.id + ')" style="margin-right:12px;color:#10b981;text-decoration:none;font-weight:700;">Save</a>' +
                            '<a href="javascript:void(0)" onclick="cancelRestEdit()" style="color:#64748b;text-decoration:none;font-weight:600;">Cancel</a>' +
                            '</td></tr>';
                    } else {
                        htmlOrig += '<tr style="border-bottom:1px solid #f5f5f5;">' +
                            '<td style="padding:10px 6px;color:#1e293b;font-weight:600;">' + rawDesc.replace(/</g,'&lt;') + '</td>' +
                            '<td style="padding:10px 6px;font-weight:700;color:#dc2626;">' + parseFloat(act.cost || 0).toFixed(2) + ' JOD</td>' +
                            '<td style="padding:10px 6px;text-align:right;">' +
                            '<a href="javascript:void(0)" onclick="editRestSubSvc(' + act.id + ')" style="margin-right:12px;color:#3b82f6;text-decoration:none;">Edit</a>' +
                            '<a href="javascript:void(0)" onclick="delRestSubSvc(' + act.id + ')" style="color:#ef4444;text-decoration:none;">Delete</a>' +
                            '</td></tr>';
                    }
                });
                htmlOrig += '</tbody></table>';
                cCon.innerHTML = htmlOrig;
            } else {
                cCon.innerHTML = '<div style="font-size:12px;color:#718096;text-align:center;padding:10px;border-radius:6px;background:#f8fafc;margin-top:16px;">No existing services found for this restaurant category.</div>';
            }
        }

        window.editRestSubSvc = function(id) {
            window.restEditRowId = id;
            renderRestSvcTable();
        };

        window.cancelRestEdit = function() {
            window.restEditRowId = null;
            renderRestSvcTable();
        };

        window.saveRestSubSvc = function(id) {
            var s = window.currentRestServices.find(function(x) { return x.id == id; });
            if (!s) return;
            var newDesc = document.getElementById('edit_rest_desc_' + id).value;
            var newCost = document.getElementById('edit_rest_cost_' + id).value;
            $.ajax({
                url: '/admin/services/' + id,
                type: 'POST',
                data: {
                    _method: 'PUT',
                    _token: document.querySelector('input[name="_token"]').value,
                    description: newDesc,
                    cost: parseFloat(newCost) || 0,
                    service_type: 'service'
                },
                success: function() {
                    s.description = newDesc;
                    s.cost = parseFloat(newCost) || 0;
                    window.restEditRowId = null;
                    renderRestSvcTable();
                    if(typeof showToast === 'function') showToast('Updated successfully', 'success');
                },
                error: function(x) {
                    alert('Error updating service');
                }
            });
        };

        window.delRestSubSvc = function(id) {
            if(!confirm('Are you sure you want to delete this service?')) return;
            $.ajax({
                url: '/admin/services/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: document.querySelector('input[name="_token"]').value,
                    service_type: 'service'
                },
                success: function() {
                    window.currentRestServices = window.currentRestServices.filter(function(x) { return x.id != id; });
                    var origGlobal = restSubServicesData.find(function(x) { return x.id == id; });
                    if (origGlobal) {
                        var ix = restSubServicesData.indexOf(origGlobal);
                        if(ix > -1) restSubServicesData.splice(ix, 1);
                    }
                    renderRestSvcTable();
                    if(typeof showToast === 'function') showToast('Deleted successfully', 'success');
                },
                error: function(x) {
                    alert('Error deleting service');
                }
            });
        };

        function triggerRestFetch(venderId) {
            var cCon = getActiveRestContainer();
            if (!cCon || !venderId || currentCatType !== 'restaurant') return;
            window.currentRestServices = restSubServicesData.filter(function(s) {
                return String(s.vender) === String(venderId);
            });
            renderRestSvcTable();
        }

        function triggerTransFetch(catId) {
            var tbody = document.getElementById('createTransServicesBody');
            if (!tbody || !catId || currentCatType !== 'transport') return;
            var company = companyMethodData[String(catId)];
            if (!company) { tbody.innerHTML = ''; return; }
            var allSvcs = [];
            if (company.methods && company.methods.length > 0) {
                company.methods.forEach(function(m) {
                    if (m.services) allSvcs = allSvcs.concat(m.services);
                });
            }
            if (company.directServices) allSvcs = allSvcs.concat(company.directServices);
            tbody.innerHTML = '';
            if (allSvcs.length > 0) {
                allSvcs.forEach(function(s) {
                    var td = 'padding:5px 6px;border-bottom:1px solid #f0f0f0;font-size:12px;';
                    var tr = document.createElement('tr');
                    tr.style.borderBottom = '1px solid #f0f0f0';
                    tr.innerHTML =
                        '<td style="' + td + '">' + (s.description || '-') + '</td>' +
                        '<td style="' + td + '">' + (s.transport_method || '-') + '</td>' +
                        '<td style="' + td + '">' + (s.departure_location || '-') + '</td>' +
                        '<td style="' + td + '">' + (s.arrival_destination || '-') + '</td>' +
                        '<td style="' + td + '">' + (s.length_time || '-') + '</td>' +
                        '<td style="' + td + '">' + (s.distance_km || '-') + '</td>' +
                        '<td style="' + td + 'color:#ea580c;font-weight:700;">' + parseFloat(s.cost || 0).toFixed(2) + ' JOD</td>' +
                        '<td style="' + td + '">-</td>';
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:16px;color:#718096;font-size:12px;">No existing services found for this transport company.</td></tr>';
            }
        }

        // Route a vendor value to the right auto-load function for the current type
        function dispatchVendorChange(val) {
            updateAccommodationName(val);
            if (!val) return;
            if (currentCatType === 'transport') {
                triggerTransFetch(val);
            } else if (currentCatType === 'restaurant') {
                triggerRestFetch(val);
            } else {
                triggerFetch(val);
            }
        }

        // Track whether SlimSelect successfully attached to THIS select, so the
        // native fallback below never depends on a page-wide '.ss-main' check
        // (which can be true because of unrelated SlimSelect dropdowns elsewhere
        // on the page, silently masking a failed/late init on this element).
        var slimVendorAttached = false;

        var sel = document.getElementById('modal_vender_select');
        if (sel) {
            sel.addEventListener('change', function(e) {
                if (!slimVendorAttached) {
                    dispatchVendorChange(e.target.value);
                }
            });
        }

        if (typeof SlimSelect !== 'undefined') {
            var ssVendor, ssEmail;
            try {
                if (document.getElementById('modal_vender_select')) {
                    ssVendor = new SlimSelect({
                        select: '#modal_vender_select',
                        searchPlaceholder: 'Search vendors...',
                        placeholder: currentCatType === 'transport' ? 'Select transport company...' : 'Select an owner/vender account...',
                        onChange: function (info) {
                            var val = info && info.value ? info.value : (Array.isArray(info) && info[0] ? info[0].value : null);
                            dispatchVendorChange(val);
                            if (ssEmail && ssEmail.selected() !== val) ssEmail.set(val);
                        }
                    });
                    slimVendorAttached = true;
                }
            } catch (e) { slimVendorAttached = false; }

            try {
                if (document.getElementById('modal_vender_email_select')) {
                    ssEmail = new SlimSelect({
                        select: '#modal_vender_email_select',
                        searchPlaceholder: 'Search by username/email...',
                        placeholder: 'Select username/email...',
                        onChange: function (info) {
                            var val = info && info.value ? info.value : (Array.isArray(info) && info[0] ? info[0].value : null);
                            if (ssVendor && ssVendor.selected() !== val) ssVendor.set(val);
                        }
                    });
                }
            } catch (e) {}
            try {
                if (document.getElementById('addTransMethodSelect')) {
                    new SlimSelect({ select: '#addTransMethodSelect', showSearch: false });
                }
            } catch (e) {}
        }
    }, 50);
}

// ── Edit ──
function editSvc(id, svcType) {
    openCatModal('Edit', 'Save');
    var btn = document.getElementById('catSubmitBtn');
    if (btn) btn.style.display = 'none';
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
        success: function() { closeCatDel(); reloadCatList(); },
        error: function(x) { alert('Error: ' + (x.responseJSON?.message || 'Could not delete')); }
    });
}

function reloadCatList() {
    $.get(location.href, function(res) {
        var newList = $(res).find('.cat-container').html();
        if (newList) {
            $('.cat-container').html(newList);
        }
    });
}

// ── Form Builder ──
function getTypeForm(type) {
    var catId = type==='transport'?715:type==='activity'?93:type==='restaurant'?456:type==='guide'?527:403;
    var submitUrl = '{{ route("admin.services.store") }}';

    var html = '<form id="catCreateForm" onsubmit="return submitCatForm(event)">' +
        '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
        '<input type="hidden" name="country" value="' + currentCountryId + '">' +
        (type !== 'transport' ? '<input type="hidden" name="category" value="' + catId + '">' : '') +
        '<input type="hidden" name="method" value="Car">' +
        '<input type="hidden" name="service_type" value="' + (type==='transport'?'transport':type) + '">';

    var venderOpts = '<option value="">Select an owner/vender account...</option>';
    vendersData.forEach(function(v) {
        var vName = v.company ? v.company : (v.first_name + ' ' + (v.last_name || ''));
        if (!vName.trim()) vName = v.email;
        venderOpts += '<option value="'+v.id+'">'+vName.replace(/"/g, '&quot;')+'</option>';
    });
    var emailOpts = '<option value="">Select username/email...</option>';
    vendersData.forEach(function(v) {
        emailOpts += '<option value="'+v.id+'">'+(v.email || '').replace(/"/g, '&quot;')+'</option>';
    });

    // Vendor selection block removed as requested

    if (type === 'transport') {
        var transCompanyOpts = '<option value="">Select transport company...</option>';
        if (typeof transCompaniesData !== 'undefined') {
            transCompaniesData.forEach(function(c) {
                transCompanyOpts += '<option value="' + c.id + '">' + c.name.replace(/"/g, '&quot;') + '</option>';
            });
        }

        // Row: Flags + Vendor dropdown (inline) + Vendor Price  — same as Modify modal
        html += '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center;">';
        html += langFlag('🇫🇷','fr',false)+langFlag('🇬🇧','en',true)+langFlag('🇮🇹','it',false)+langFlag('🇪🇸','es',false)+langFlag('🇩🇪','de',false)+langFlag('🇸🇪','se',false)+langFlag('🇳🇱','nl',false);
        html += '<div style="margin-left:auto;display:flex;gap:16px;align-items:center;background:#f8f9fa;border:1px solid #e9ecef;border-radius:6px;padding:6px 14px;font-size:12px;width:75%;">';
        html += '<div style="flex:1;"><select id="modal_vender_select" name="category" style="width:100%;height:30px;border:1px solid #ddd;border-radius:4px;outline:none;">' + transCompanyOpts + '</select></div>';
        html += '<span style="color:#ccc;">|</span>';
        html += '<span style="white-space:nowrap;"><strong>Vendor Price:</strong> <input type="number" name="cost" value="0" step="0.01" style="width:70px;height:24px;border:1px solid #ddd;border-radius:4px;padding:2px 6px;outline:none;"> <span style="color:#ea580c;font-weight:700;">JOD</span></span>';
        html += '</div></div>';

        // Photos
        html += '<div style="margin-bottom:16px"><div style="display:flex;align-items:center;gap:8px;margin-bottom:6px"><span style="font-size:11px;font-weight:700;color:#555">Photos: <span style="color:#ea580c;font-weight:400;">How to choose the right photos?</span></span></div>' +
            '<div id="svcPhotosRow" style="display:flex;gap:12px;height:120px;">' +
            '<div id="svcImageDrop" onclick="document.getElementById(\'svcImageInput\').click()" style="flex-shrink:0;width:100px;height:104px;border:2px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:28px;cursor:pointer;">+</div>' +
            '</div><input type="file" name="new_images[]" id="svcImageInput" accept="image/*" multiple style="display:none" onchange="previewSvcImageGrid(this)"></div>';

        // Transport Title
        html += '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 4px 0;position:relative;">';
        html += '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Transport Title</legend>';
        html += '<input type="text" name="description" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" oninput="document.getElementById(\'transTitleCountAdd\').textContent=\'(\'+this.value.length+\'/255)\'">';
        html += '<div id="transTitleCountAdd" style="position:absolute;right:10px;bottom:12px;font-size:10px;color:#aaa;">(0/255)</div>';
        html += '</fieldset>';

        // Description
        html += '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px;">';
        html += '<legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend>';
        html += '<textarea name="notes" style="width:100%;min-height:140px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description"></textarea>';
        html += '</fieldset>';

        // Services table — same columns as Modify modal
        html += '<div style="border-top:1px solid #e2e8f0;padding-top:12px;">';
        html += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">';
        html += '<span style="color:#10b981;font-size:12px;font-weight:800;letter-spacing:1px;text-transform:uppercase;">🚗 SUB-SERVICES LIST</span>';
        html += '<button type="button" onclick="addCreateTransServiceRow()" style="background:#10b981;border:none;color:#fff;border-radius:6px;padding:6px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Service Row</button>';
        html += '</div>';
        html += '<table style="width:100%;border-collapse:collapse;font-size:12px;">';
        html += '<thead><tr style="border-bottom:2px solid #e2e8f0;">';
        html += '<th style="text-align:left;padding:8px 6px;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;">DESCRIPTION</th>';
        html += '<th style="text-align:left;padding:8px 6px;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;">METHOD OF TRANSPORT</th>';
        html += '<th style="text-align:left;padding:8px 6px;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;">DEPARTURE LOCATION</th>';
        html += '<th style="text-align:left;padding:8px 6px;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;">ARRIVAL LOCATION</th>';
        html += '<th style="text-align:left;padding:8px 6px;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;">LENGTH</th>';
        html += '<th style="text-align:left;padding:8px 6px;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;">DISTANCE KM</th>';
        html += '<th style="text-align:left;padding:8px 6px;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;">COST</th>';
        html += '<th style="text-align:left;padding:8px 6px;font-size:11px;font-weight:700;color:#555;text-transform:uppercase;">ACTIONS</th>';
        html += '</tr></thead>';
        html += '<tbody id="createTransServicesBody"></tbody>';
        html += '</table>';
        html += '</div>';



    } else if (type === 'restaurant') {
        var restSelect = '<option value="">Select a vendor...</option>';
        if (typeof restCategoriesData !== 'undefined') {
            restCategoriesData.forEach(function(vendor) {
                restSelect += '<option value="' + vendor.id + '">' + vendor.name.replace(/"/g, '&quot;') + '</option>';
            });
        }

        html += '<div style="display:flex;gap:16px;margin-bottom:16px"><div style="flex:1"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Select Vendor</label>' +
                '<select id="modal_vender_select" name="vender" onchange="triggerRestFetch(this.value)" style="width:100%;height:40px;border:1px solid #ddd;border-radius:8px;padding:0 12px;font-size:13px;outline:none;background:#fff">' + restSelect + '</select></div></div>';

        html += '<input type="hidden" name="cost" value="0">' +
            '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">' +
            langFlag('🇫🇷','fr',false)+langFlag('🇬🇧','en',true)+langFlag('🇮🇹','it',false)+langFlag('🇪🇸','es',false)+langFlag('🇩🇪','de',false)+langFlag('🇸🇪','se',false)+langFlag('🇳🇱','nl',false)+
            '</div>' +
            '<div style="margin-bottom:16px"><div style="display:flex;align-items:center;gap:8px;margin-bottom:6px"><span style="font-size:11px;font-weight:700;color:#555">Photos:</span></div>' +
            '<div id="svcPhotosRow" style="display:flex;gap:12px;height:120px;">' +
            '<div id="svcImageDrop" onclick="document.getElementById(\'svcImageInput\').click()" style="flex:1;min-width:100px;border:1px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:24px;cursor:pointer;"><i class="fa fa-camera"></i></div>' +
            '</div><input type="file" name="new_images[]" id="svcImageInput" accept="image/*" multiple style="display:none" onchange="previewSvcImageGrid(this)"></div>' +
            '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px;position:relative"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Restaurant name</legend>' +
            '<input type="text" name="description" required style="width:100%;height:36px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent">' +
            '</fieldset>' +
            '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px;position:relative"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Place of interest</legend>' +
            '<input type="text" id="catArrivalInput" name="arrival" autocomplete="off" oninput="catPlaceAutocomplete(this.value)" onkeydown="catPlaceKey(event)" style="width:100%;height:36px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent" placeholder="Enter destination...">' +
            '<div id="catArrivalDropdown"></div></fieldset>' +
            '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Description</legend>' +
            '<textarea name="notes" style="width:100%;min-height:120px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent" placeholder="Add a description"></textarea></fieldset>' +
            '<div style="margin-top:20px;border-top:1px solid #e2e8f0;padding-top:16px;">' +
            '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">' +
            '<span style="font-size:10px;font-weight:800;color:#ea580c;letter-spacing:1px;text-transform:uppercase;">🍽️ EXISTING SERVICES LIST</span>' +
            '<button type="button" onclick="addCreateRestServiceRow()" style="background:#ea580c;border:none;color:#fff;border-radius:6px;padding:6px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Service Row</button>' +
            '</div>' +
            '<table style="width:100%;border-collapse:collapse;margin-bottom:10px;">' +
            '<tbody id="createRestServicesBody"></tbody>' +
            '</table>' +
            '</div>' +
            '<div id="restServicesContainer"></div>';

    } else if (type === 'activity' || type === 'guide') {
        var label = type === 'guide' ? 'Guide name' : 'Activity name';

        if (type === 'guide') {
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
        } else {
            // Activity specific layout as requested
            html += '<input type="hidden" name="cost" value="0">' +
                '<div style="display:flex;gap:16px;margin-bottom:16px;">' +
                '<div style="flex:1;"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Select Vendor</label>' +
                '<select name="vender" id="actVenderSelect" onchange="loadActCategoryServices(this.value)" required style="width:100%;height:40px;border:1px solid #ddd;border-radius:8px;padding:0 12px;font-size:13px;outline:none;background:#fff"><option value="">Select a vendor...</option>' + (function(){ var opts=''; actCategoriesData.forEach(function(a){ opts += '<option value="'+a.id+'">'+a.name.replace(/"/g,'&quot;')+'</option>'; }); return opts; })() + '</select></div>' +
                '</div>' +
                '<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">' +
                langFlag('🇫🇷','fr',false)+langFlag('🇬🇧','en',true)+langFlag('🇮🇹','it',false)+langFlag('🇪🇸','es',false)+langFlag('🇩🇪','de',false)+langFlag('🇸🇪','se',false)+langFlag('🇳🇱','nl',false)+
                '</div>' +
                '<div style="margin-bottom:16px"><div style="display:flex;align-items:center;gap:8px;margin-bottom:6px"><span style="font-size:11px;font-weight:700;color:#555">Photos:</span></div>' +
                '<div id="svcPhotosRow" style="display:flex;gap:12px;height:120px;">' +
                '<div id="svcImageDrop" onclick="document.getElementById(\'svcImageInput\').click()" style="flex:1;min-width:100px;border:1px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:24px;cursor:pointer;"><i class="fa fa-camera"></i></div>' +
                '</div><input type="file" name="new_images[]" id="svcImageInput" accept="image/*" multiple style="display:none" onchange="previewSvcImageGrid(this)"></div>' +
                '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px;position:relative"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Active Name</legend>' +
                '<input type="text" name="description" id="actNameInput" required style="width:100%;height:36px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent">' +
                '</fieldset>' +
                '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Information</legend>' +
                '<textarea name="notes" style="width:100%;min-height:80px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent" placeholder="Add information about this Activity"></textarea></fieldset>' +
                '<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px;position:relative"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Location</legend>' +
                '<input type="text" id="catArrivalInput" name="arrival" autocomplete="off" oninput="catPlaceAutocomplete(this.value)" onkeydown="catPlaceKey(event)" style="width:100%;height:36px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent" placeholder="Enter destination...">' +
                '<div id="catArrivalDropdown"></div></fieldset>' +
                '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Activity category</legend>' +
                '<select name="acc_category" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555">' +
                '<option value="">Select a category</option><option value="1 Star">1 Star</option><option value="2 Star">2 Star</option>' +
                '<option value="3 Star">3 Star</option><option value="4 Star">4 Star</option><option value="5 Star">5 Star</option>' +
                '</select></fieldset>' +
                '<div style="margin-top:20px;border-top:1px solid #e2e8f0;padding-top:16px;">' +
                '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">' +
                '<span style="color:#ea580c;font-size:12px;font-weight:800;letter-spacing:1px;text-transform:uppercase;">📝 ACTIVITY SERVICES</span>' +
                '<button type="button" onclick="addCreateActServiceRow()" style="background:#ea580c;border:none;color:#fff;border-radius:6px;padding:6px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Service Row</button>' +
                '</div>' +
                '<div id="actServicesContainer"></div>' +
                '</div>';
        }

    } else if (type === 'accommodation') {
        html += '<div style="display:flex;gap:16px;margin-bottom:16px"><div style="flex:1"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Select Vendor</label>' +
                '<select id="modal_vender_select" name="vender" style="width:100%;height:40px;border:1px solid #ddd;border-radius:8px;padding:0 12px;font-size:13px;outline:none;background:#fff">' + venderOpts + '</select></div></div>';
        
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
            '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Accommodation Type</legend>' +
            '<select name="acc_type" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555">' +
            '<option value="">Select a type</option><option value="Hotel">Hotel</option><option value="Guesthouse">Guesthouse</option>' +
            '<option value="Hostel">Hostel</option><option value="Resort">Resort</option><option value="Apartment">Apartment</option>' +
            '<option value="Camp">Camp</option><option value="Eco-lodge">Eco-lodge</option><option value="Riad">Riad</option><option value="Villa">Villa</option>' +
            '</select></fieldset>' +
            '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Category</legend>' +
            '<select name="acc_category" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555">' +
            '<option value="">Select a category</option><option value="1 Star">1 Star</option><option value="2 Star">2 Star</option>' +
            '<option value="3 Star">3 Star</option><option value="4 Star">4 Star</option><option value="5 Star">5 Star</option>' +
            '<option value="Standard">Standard</option><option value="Superior">Superior</option><option value="Luxury">Luxury</option>' +
            '</select></fieldset>' +
            '<fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px">Website</legend>' +
            '<input type="text" name="website" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent"></fieldset>' +
            '</div></div>' +
            '<div style="margin-top:20px;border-top:1px solid #e2e8f0;padding-top:16px;">' +
            '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">' +
            '<span style="color:#e53e3e;font-size:12px;font-weight:800;letter-spacing:1px;">🏃 PROPERTY SERVICES</span>' +
            '<button type="button" onclick="addPropServiceRow()" style="background:#ea580c;border:none;color:#fff;border-radius:6px;padding:6px 12px;font-size:11px;font-weight:700;cursor:pointer;"><i class="fa fa-plus"></i> Add Service Row</button>' +
            '</div>' +
            '<div id="propServicesContainer"></div>' +
            '</div>';
    }

    html += '</form>';
    return html;
}

var _propSvcIdx = 0;
var _actSvcIdx = 0;
// Tracks a known-valid category for the currently selected vendor (taken from
// their first existing service), used as the default for brand-new blank rows
// added via "+ Add Service Row", so newly created services actually persist
// under a real activity-type category instead of the generic container one.
var _actCurrentDefaultCategory = '';

function loadActCategoryServices(venderId) {
    var c = document.getElementById('actServicesContainer');
    if (!c) return;
    c.innerHTML = ''; // clear old rows
    _actSvcIdx = 0;
    _actCurrentDefaultCategory = '';
    if (!venderId) return;
    var found = null;
    actCategoriesData.forEach(function(a) {
        if (String(a.id) === String(venderId)) found = a;
    });

    if (!found || !found.services || found.services.length === 0) return;
    if (found.services[0].category) _actCurrentDefaultCategory = found.services[0].category;
    found.services.forEach(function(svc) {
        addCreateActServiceRow(svc.description || '', svc.cost != null ? svc.cost : '0.00', svc.category || '');
    });
}

function addCreateActServiceRow(descVal, costVal, catVal) {
    descVal = descVal || '';
    costVal = costVal != null ? costVal : '0.00';
    catVal = catVal || _actCurrentDefaultCategory || '';
    var c = document.getElementById('actServicesContainer');
    if (!c) return;
    var row = document.createElement('div');
    row.style.cssText = "display:flex;gap:10px;align-items:flex-end;margin-bottom:12px;padding:10px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;";
    row.innerHTML =
        '<input type="hidden" name="sub_services[' + _actSvcIdx + '][category]" value="' + catVal + '">' +
        '<div style="flex:2;"><label style="font-size:11px;color:#555;font-weight:700;display:block;margin-bottom:4px;">Small description (offers)</label>' +
        '<input type="text" name="sub_services[' + _actSvcIdx + '][description]" value="' + descVal.replace(/"/g, '&quot;') + '" style="width:100%;height:32px;border:1px solid #ccc;border-radius:4px;padding:0 8px;font-size:12px;outline:none;" required></div>' +
        '<div style="flex:1;"><label style="font-size:11px;color:#555;font-weight:700;display:block;margin-bottom:4px;">Price (JOD)</label>' +
        '<input type="number" step="0.01" name="sub_services[' + _actSvcIdx + '][cost]" value="' + costVal + '" style="width:100%;height:32px;border:1px solid #ccc;border-radius:4px;padding:0 8px;font-size:12px;outline:none;" required></div>' +
        '<div style="flex-shrink:0;"><button type="button" onclick="this.parentNode.parentNode.remove()" style="height:32px;padding:0 12px;background:#ef4444;color:#fff;border:none;border-radius:4px;font-size:12px;cursor:pointer;"><i class="fa fa-trash"></i></button></div>';
    c.appendChild(row);
    _actSvcIdx++;
}

function updateEditActivityServices(catId) {
    var c = document.getElementById('editActServicesTbody');
    if (!c) return;
    c.innerHTML = '';
    if (!catId) return;
    var found = null;
    actCategoriesData.forEach(function(a) {
        if (String(a.id) === String(catId)) found = a;
    });
    if (!found || !found.services || found.services.length === 0) return;

    var html = '';
    found.services.forEach(function(svc) {
        var actDesc = svc.description || '-';
        var cost = parseFloat(svc.cost || 0).toFixed(2);

        actDesc = actDesc.replace(/</g, '&lt;').replace(/>/g, '&gt;');

        html += '<tr style="border-bottom:1px solid #f5f5f5;">' +
            '<td style="padding:10px 6px;color:#1e293b;font-weight:600;">' + actDesc + '</td>' +
            '<td style="padding:10px 6px;font-weight:700;color:#ea580c;white-space:nowrap;">' + cost + ' JOD</td>' +
            '</tr>';
    });
    c.innerHTML = html;
}

window.toggleActSubAddForm = function() {
    var f = document.getElementById("actSubAddSvcForm");
    if (f) {
        f.style.display = (f.style.display === "none") ? "block" : "none";
    }
};

window.quickAddActSubEdit = function(cat, token, country, vender) {
    // The vendor select's value is a real VENDOR id (used to group services by
    // vendor) - it must never be sent as `category`. The activity's own category
    // (`cat`, e.g. the generic "*PVT services" root) also isn't a valid category
    // for individual services - real services live under specific activity-type
    // categories (Jeep Tour, Lunch, Horse Ride, etc). So we reuse the category of
    // an existing sibling service for this vendor, which is guaranteed valid;
    // only fall back to `cat` if this vendor has no existing services yet.
    var sel = document.getElementById("editActCategorySelect");
    var selectedVendor = sel && sel.value ? sel.value : vender;
    var desc = document.getElementById("newActDescEdit").value;
    var cost = document.getElementById("newActCostEdit").value || 0;
    if(!desc) { alert("Please enter description"); return; }

    var targetCategory = cat;
    var found = null;
    if (typeof actCategoriesData !== "undefined") {
        actCategoriesData.forEach(function(a) {
            if(String(a.id) === String(selectedVendor)) found = a;
        });
        if (found && found.services && found.services.length > 0 && found.services[0].category) {
            targetCategory = found.services[0].category;
        }
    }

    var btn = event.target;
    btn.innerHTML = "Saving...";
    btn.disabled = true;
    $.ajax({
        url: "/admin/services",
        type: "POST",
        data: {
            _token: token,
            service_type: "service",
            description: desc,
            cost: cost,
            category: targetCategory,
            country: country,
            vender: selectedVendor
        },
        success: function(res) {
            btn.innerHTML = "Save"; btn.disabled = false;

            if (found) {
                if(!found.services) found.services = [];
                // Using temporary ID, because backend doesn't return the model
                found.services.unshift({
                    id: 'temp_' + Date.now(),
                    description: desc,
                    cost: cost,
                    category: targetCategory,
                    vender: selectedVendor
                });
            }
            document.getElementById("newActDescEdit").value = "";
            document.getElementById("newActCostEdit").value = "0";

            if (typeof updateEditActivityServices === "function") updateEditActivityServices(selectedVendor);
            if (typeof showToast === "function") showToast("Service added!", "success");
            toggleActSubAddForm();
        },
        error: function(x) {
            btn.innerHTML = "Save"; btn.disabled = false;
            alert("Error saving service");
        }
    });
};

function addActImages(input) {
    if(input.files && input.files.length > 0){
        var row = document.getElementById('actPhotosRow');
        if(!row) return;
        var addBtn = row.lastElementChild;

        for(var i=0; i<input.files.length; i++){
            var reader = new FileReader();
            reader.onload = function(e){
                var div = document.createElement('div');
                div.style.cssText = 'position:relative;flex-shrink:0;height:104px;';
                div.innerHTML = '<img src="' + e.target.result + '" style="height:100%;border-radius:4px;object-fit:cover;">' +
                                '<input type="hidden" name="existing_images[]" value="">' +
                                '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">?</button>';
                row.insertBefore(div, addBtn);
            };
            reader.readAsDataURL(input.files[i]);
        }
    }
}

window.submitEditActivity = function(id) {
    var form = document.getElementById("editActForm");
    var fd = new FormData(form);
    fd.append("_method", "PUT");
    fd.append("_token", "{{ csrf_token() }}");
    fd.append("service_type", "activity");

    var btn = form.querySelector("button[type=submit]");
    if (btn) { btn.disabled = true; btn.innerText = "Saving..."; }

    $.ajax({
        url: "/admin/services/" + id,
        type: "POST",
        data: fd,
        processData: false,
        contentType: false,
        success: function() {
            if (typeof closeCatModal === "function") closeCatModal();
            if (typeof showToast === "function") showToast("Activity updated!", "success");
            location.reload();
        },
        error: function(x) {
            if (btn) { btn.disabled = false; btn.innerText = "Save"; }
            alert("Error: " + (x.responseJSON?.message || "Could not update"));
        }
    });
};

function addPropServiceRow(descVal, costVal, idVal) {
    descVal = descVal || '';
    costVal = costVal != null ? costVal : '0.00';
    idVal = idVal || '';
    var c = document.getElementById('propServicesContainer');
    if (!c) return;
    _propSvcIdx++;
    var row = document.createElement('div');
    row.style.cssText = "display:flex;gap:10px;align-items:flex-end;margin-bottom:12px;padding:10px;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;";
    var cleanDesc = descVal.toString().replace(/"/g, '&quot;');
    row.innerHTML = '<div style="flex:2;min-width:140px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Description</label>' +
        '<input type="text" name="prop_desc[]" value="' + cleanDesc + '" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;" placeholder="e.g. Single Room" required></div>' +
        '<div style="flex:1;min-width:80px;"><label style="font-size:10px;font-weight:700;color:#718096;display:block;margin-bottom:4px;">Cost (JOD)</label>' +
        '<input type="number" name="prop_cost[]" value="' + costVal + '" step="0.01" style="width:100%;height:36px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;font-size:12px;" placeholder="0.00"></div>' +
        '<input type="hidden" name="prop_id[]" value="' + idVal + '">' +
        '<input type="hidden" name="prop_type[]" value="">' +
        '<input type="hidden" name="prop_cat[]" value="">' +
        '<button type="button" onclick="this.parentElement.remove()" style="height:36px;background:#fff5f5;border:1px solid #fc8181;color:#e53e3e;border-radius:6px;padding:0 12px;font-size:12px;cursor:pointer;margin-bottom:0;"><i class="fa fa-trash"></i></button>';
    c.appendChild(row);
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

// ── Create Transport Service Rows ──
var _createTransRowIdx = 0;
function addCreateTransServiceRow() {
    var tbody = document.getElementById('createTransServicesBody');
    if (!tbody) return;
    var idx = _createTransRowIdx++;
    var methodOpts = ['Car with driver','Van with driver','Bus with driver','Car','Van','Bus','Airplane','Boat','Train'];
    var methodSel = '<select name="sub_services['+idx+'][transport_method]" style="width:100%;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;outline:none;">';
    methodOpts.forEach(function(m){ methodSel += '<option value="'+m+'">'+m+'</option>'; });
    methodSel += '</select>';

    var tr = document.createElement('tr');
    tr.id = 'createTransRow_'+idx;
    tr.style.borderBottom = '1px solid #f0f0f0';
    tr.innerHTML =
        '<td style="padding:6px 4px;"><input type="text" name="sub_services['+idx+'][description]" placeholder="Description" style="width:100%;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;padding:0 6px;outline:none;"></td>' +
        '<td style="padding:6px 4px;">'+methodSel+'</td>' +
        '<td style="padding:6px 4px;position:relative;"><input autocomplete="off" type="text" id="CT_dep_'+idx+'" name="sub_services['+idx+'][departure_location]" placeholder="Departure" style="width:100%;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;padding:0 6px;outline:none;" oninput="if(typeof transPlaceAutocomplete !== \'undefined\') transPlaceAutocomplete(this.value, \'CT_dep_dd_'+idx+'\', \'CT_dep_'+idx+'\')"><div id="CT_dep_dd_'+idx+'" style="display:none;position:absolute;left:4px;right:4px;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.15);max-height:220px;overflow-y:auto;text-align:left;"></div></td>' +
        '<td style="padding:6px 4px;position:relative;"><input autocomplete="off" type="text" id="CT_arr_'+idx+'" name="sub_services['+idx+'][arrival_destination]" placeholder="Arrival" style="width:100%;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;padding:0 6px;outline:none;" oninput="if(typeof transPlaceAutocomplete !== \'undefined\') transPlaceAutocomplete(this.value, \'CT_arr_dd_'+idx+'\', \'CT_arr_'+idx+'\')"><div id="CT_arr_dd_'+idx+'" style="display:none;position:absolute;left:4px;right:4px;top:100%;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:0 0 8px 8px;box-shadow:0 8px 20px rgba(0,0,0,.15);max-height:220px;overflow-y:auto;text-align:left;"></div></td>' +
        '<td style="padding:6px 4px;"><input type="text" name="sub_services['+idx+'][length_time]" placeholder="0:00" style="width:70px;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;padding:0 6px;outline:none;"></td>' +
        '<td style="padding:6px 4px;"><input type="number" name="sub_services['+idx+'][distance_km]" placeholder="0" style="width:70px;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;padding:0 6px;outline:none;"></td>' +
        '<td style="padding:6px 4px;"><input type="number" step="0.01" name="sub_services['+idx+'][cost]" value="0" style="width:70px;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;padding:0 6px;outline:none;"></td>' +
        '<td style="padding:6px 4px;"><a href="#" onclick="removeCreateTransServiceRow('+idx+'); return false;" style="color:#ef4444;font-size:12px;font-weight:700;text-decoration:none;"><i class="fa fa-trash"></i> Delete</a></td>';
    tbody.appendChild(tr);
}
function removeCreateTransServiceRow(idx) {
    var tr = document.getElementById('createTransRow_'+idx);
    if (tr) tr.remove();
}

// ── Create Restaurant Service Rows ──
var _createRestRowIdx = 0;
function addCreateRestServiceRow() {
    var tbody = document.getElementById('createRestServicesBody');
    if (!tbody) return;
    var idx = _createRestRowIdx++;
    var tr = document.createElement('tr');
    tr.id = 'createRestRow_'+idx;
    tr.style.borderBottom = '1px solid #f0f0f0';
    tr.innerHTML =
        '<td style="padding:6px 4px;"><input type="text" name="sub_services['+idx+'][description]" placeholder="Description" style="width:100%;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;padding:0 6px;outline:none;"></td>' +
        '<td style="padding:6px 4px;"><input type="number" step="0.01" name="sub_services['+idx+'][cost]" value="0" style="width:100px;height:28px;border:1px solid #ddd;border-radius:4px;font-size:12px;padding:0 6px;outline:none;"></td>' +
        '<td style="padding:6px 4px;text-align:right;"><a href="#" onclick="removeCreateRestServiceRow('+idx+'); return false;" style="color:#ef4444;font-size:12px;font-weight:700;text-decoration:none;"><i class="fa fa-trash"></i> Delete</a></td>';
    tbody.appendChild(tr);
}
function removeCreateRestServiceRow(idx) {
    var tr = document.getElementById('createRestRow_'+idx);
    if (tr) tr.remove();
}


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
            reloadCatList();
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

function transPlaceAutocomplete(query, ddId, inpId) {
    if (window._transTimer) clearTimeout(window._transTimer);
    var dd = document.getElementById(ddId);
    if (!dd) return;
    if (!query || query.length < 2) { dd.style.display = 'none'; dd.innerHTML = ''; return; }
    window._transTimer = setTimeout(function() {
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
                    item.innerHTML = '<i class="fa fa-map-marker" style="color:#9ca3af;font-size:13px;flex-shrink:0;"></i>' +
                        '<span>' +
                        (parts.length ? '<span style="font-weight:600;color:#1e293b;">' + catEscape(parts.join(', ')) + '</span> ' : '') +
                        (country ? '<span style="font-weight:700;color:#ea580c;">' + catEscape(country) + '</span>' : '') +
                        '</span>';
                    item.onmouseover = function() { this.style.background = '#fff7ed'; };
                    item.onmouseout = function() { this.style.background = ''; };
                    item.onclick = function() {
                        var label = city || state || country || place.display_name;
                        var inp = document.getElementById(inpId);
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

    var tDep = document.getElementById('editTransDepDD');
    if (tDep && !e.target.closest('#editTransDepDD') && e.target.id !== 'editTransDepInp') tDep.style.display = 'none';

    var tArr = document.getElementById('editTransArrDD');
    if (tArr && !e.target.closest('#editTransArrDD') && e.target.id !== 'editTransArrInp') tArr.style.display = 'none';

    var editAccDD = document.getElementById('editAccArrivalDropdown');
    var editAccInp = document.getElementById('editAccArrivalInput');
    if (editAccDD && !e.target.closest('#editAccArrivalDropdown') && e.target !== editAccInp) {
        editAccDD.style.display = 'none';
    }
});

var _libAccTimer = null;
var _libAccIdx = -1;

function libAccAutocomplete(query) {
    if (_libAccTimer) clearTimeout(_libAccTimer);
    _libAccIdx = -1;
    var dd = document.getElementById('editAccArrivalDropdown');
    if (!dd) return;
    if (!query || query.length < 2) { 
        dd.style.display = 'none'; 
        dd.innerHTML = ''; 
        return; 
    }
    
    _libAccTimer = setTimeout(function() {
        var headers = new Headers();
        headers.append('Accept-Language', 'en');
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&addressdetails=1&limit=6', {headers: headers})
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
                    
                    var html = '<i class="fa fa-map-marker" style="color:#9ca3af;font-size:13px;flex-shrink:0;"></i> ' +
                        '<span>' +
                        (parts.length ? '<span style="font-weight:600;color:#1e293b;">' + catEscape(parts.join(', ')) + '</span> ' : '') +
                        (country ? '<span style="font-weight:700;color:#ea580c;">' + catEscape(country) + '</span>' : '') +
                        '</span>';
                    
                    item.innerHTML = html;
                    
                    item.onmouseover = function() { this.style.background = '#fff7ed'; };
                    item.onmouseout = function() { this.style.background = (_libAccIdx === idx ? '#fff7ed' : ''); };
                    item.onclick = function() {
                        var label = city || state || country || place.display_name;
                        var inp2 = document.getElementById('editAccArrivalInput');
                        if (inp2) inp2.value = label;
                        dd.style.display = 'none';
                        dd.innerHTML = '';
                    };
                    dd.appendChild(item);
                });
                dd.style.display = 'block';
            })
            .catch(function(e){ console.error('Autocomplete Error:', e); });
    }, 300);
}

function libAccInputKey(event) {
    var dd = document.getElementById('editAccArrivalDropdown');
    var items = dd ? dd.querySelectorAll('div[data-idx]') : [];
    if (!dd || dd.style.display === 'none' || items.length === 0) return;

    if (event.key === 'ArrowDown') {
        event.preventDefault();
        _libAccIdx = Math.min(_libAccIdx + 1, items.length - 1);
        items.forEach(function(el, i) { el.style.background = (i === _libAccIdx ? '#fff7ed' : ''); });
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        _libAccIdx = Math.max(_libAccIdx - 1, 0);
        items.forEach(function(el, i) { el.style.background = (i === _libAccIdx ? '#fff7ed' : ''); });
    } else if (event.key === 'Enter') {
        if (_libAccIdx >= 0 && items[_libAccIdx]) {
            event.preventDefault();
            items[_libAccIdx].click();
        }
    } else if (event.key === 'Escape') {
        dd.style.display = 'none';
    }
}
</script>
@endpush
