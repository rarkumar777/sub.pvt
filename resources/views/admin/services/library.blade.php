@extends('admin.layouts.app')
@section('title', 'Library | Admin')
@push('head')
<style>
.lib-outer{background:#f5f0e8; min-height:100vh; padding-top:24px; padding-bottom:80px; margin: -45px -40px -45px -40px;}
.lib-header{background:#ea580c; padding:12px 24px; display:flex; align-items:center; justify-content:space-between; color:#fff; position:sticky; top:0; z-index:1000;}
.lib-header h1{font-size:18px; font-weight:700; margin:0; display:flex; align-items:center; gap:12px;}

.lib-nav-search-bar{max-width:800px; width:100%; margin:0 auto 24px auto; display:flex; gap:12px; align-items:center; padding:0 24px;}
.lib-search-container{position:relative; flex:1;}
.lib-search-input{width:100%; height:48px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:0 16px !important; font-size:14px; color:#1e293b; outline:none; transition:border-color 0.2s; box-sizing:border-box !important;}
.lib-search-input:focus{border-color:#ea580c;}

.lib-lang-select{background:#fff; border:1px solid #e2e8f0; border-radius:8px; height:48px; display:flex; align-items:center; gap:8px; padding:0 12px; cursor:pointer; min-width:140px;}
.lib-lang-select img{width:20px; height:14px; object-fit:cover; border-radius:2px;}
.lib-lang-select span{font-size:14px; font-weight:600; color:#1e293b;}

.lib-container{max-width:1000px; margin:0 auto; padding:0 24px;}

.lib-section{margin-bottom:48px;}
.lib-section-header{display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;}
.lib-section-title{display:flex; align-items:center; gap:12px; font-size:16px; font-weight:700; color:#1e293b;}
.lib-section-title i{font-size:24px;}
.lib-see-more{color:#ea580c; font-size:13px; font-weight:700; text-decoration:none;}

/* DAY CARDS (Banner style) */
.day-banner-card{position:relative; width:100%; height:110px; border-radius:4px; margin-bottom:12px; cursor:pointer; background:#ccc;}
.day-banner-bg{position:absolute; inset:0; background-size:cover; background-position:center; border-radius:4px; filter:brightness(0.65); transition:transform 0.3s; overflow:hidden;}
.day-banner-card:hover .day-banner-bg{transform:scale(1.03);}
.day-banner-overlay{position:relative; z-index:2; height:100%; padding:16px 24px; display:flex; flex-direction:column; justify-content:center; color:#fff;}
.day-banner-loc{font-size:11px; font-weight:600; display:flex; align-items:center; gap:6px; margin-bottom:4px; opacity:0.9;}
.day-banner-title{font-size:18px; font-weight:700;}
.day-banner-dots{position:absolute; top:12px; right:12px; z-index:3; color:#fff; font-size:18px; cursor:pointer; background:none; border:none;}

/* ACCOMMODATION CARDS (Evaneos style) */
.accom-card-ev{background:#fff; border:1px solid #e2e8f0; border-radius:4px; padding:12px 16px; display:flex; align-items:center; gap:16px; margin-bottom:12px; position:relative;}
.accom-thumb{width:64px; height:64px; border-radius:4px; object-fit:cover; flex-shrink:0;}
.accom-info-ev{flex:1; min-width:0;}
.accom-title-row{display:flex; align-items:center; gap:8px; margin-bottom:2px;}
.accom-title-row i{color:#ea580c; font-size:16px;}
.accom-title-ev{color:#ea580c; font-size:14px; font-weight:700; text-decoration:none;}
.accom-loc-row{display:flex; align-items:center; gap:6px; color:#64748b; font-size:12px; margin-bottom:4px;}
.accom-desc-snippet{font-size:12px; color:#64748b; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
.accom-dots-ev{color:#94a3b8; font-size:18px; cursor:pointer; background:none; border:none;}

/* Dropdown */
.lib-dropdown{display:none; position:absolute; right:16px; top:100%; margin-top:-8px; background:#fff; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.12); min-width:170px; z-index:100; overflow:hidden; border:1px solid rgba(0,0,0,.06);}
.lib-dropdown a{display:flex; align-items:center; gap:10px; padding:11px 16px; font-size:13px; font-weight:600; color:#555; text-decoration:none;}
.lib-dropdown a:hover{background:#f8f8f8; color:#ea580c;}
.lib-dropdown .del:hover{background:#fef2f2; color:#ef4444;}
.lib-dropdown .divider{height:1px; background:#f0f0f0;}

/* Floating Add Button */
.fab-ev{position:fixed; bottom:24px; right:24px; z-index:2000;}
.fab-btn-ev{background:#ea580c; color:#fff; border:none; border-radius:30px; height:48px; display:flex; align-items:center; gap:10px; padding:0 24px; font-weight:700; font-size:13px; box-shadow:0 4px 12px rgba(0,0,0,0.2); cursor:pointer;}
.fab-btn-ev img{width:20px; height:14px; object-fit:cover; border-radius:2px;}

/* Category Buttons */
.lib-cat-btn { background:#fff; border:1px solid #e2e8f0; border-radius:4px; padding:8px 16px; font-size:13px; font-weight:700; color:#1e293b; cursor:pointer; transition:all 0.2s; display:flex; align-items:center; justify-content:center; }
.lib-cat-btn:hover { border-color:#ea580c; color:#ea580c; box-shadow:0 2px 4px rgba(0,0,0,0.05); }
.lib-cat-btn.active { background:#ffedd5; border-color:#ea580c; color:#ea580c; }

.lib-modal-bg{position:fixed; inset:0; background:rgba(0,0,0,.5); backdrop-filter:blur(6px); z-index:99999; display:none; align-items:center; justify-content:center; padding:20px;}
.lib-modal-box{background:#fff; border-radius:16px; max-width:1050px; width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 50px rgba(0,0,0,.2);}
.lib-modal-body{padding:24px;}
.lib-modal-head{padding:20px 24px; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between;}
.lib-modal-head h3{margin:0; font-size:17px; font-weight:800; color:#222;}
.lib-modal-close{width:32px; height:32px; border-radius:8px; border:none; background:#f5f5f5; cursor:pointer; font-size:14px; color:#999; display:flex; align-items:center; justify-content:center;}
</style>
@endpush

@section('content')
<div class="lib-outer">

    {{-- Search Bar --}}
    <div class="lib-nav-search-bar">
        <div class="lib-search-container">
            <input type="text" class="lib-search-input" id="libSearch" placeholder="Search in my library..." oninput="debounceSearch()">
        </div>
        <div class="lib-lang-select">
            <img src="https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg" alt="UK">
            <span>English</span>
            <i class="fa fa-caret-down" style="color:#94a3b8; font-size:12px;"></i>
        </div>
    </div>

    <div class="lib-container">
        <div style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
            <button onclick="toggleCatBtn(93, this)" class="lib-cat-btn" id="catBtn_93">Activity</button>
            <button onclick="toggleCatBtn(715, this)" class="lib-cat-btn" id="catBtn_715">Transport</button>
            <button onclick="toggleCatBtn(403, this)" class="lib-cat-btn" id="catBtn_403">Accommodation</button>
            <button onclick="toggleCatBtn(456, this)" class="lib-cat-btn" id="catBtn_456">Restaurant</button>
            <button onclick="toggleCatBtn(527, this)" class="lib-cat-btn" id="catBtn_527">Guide</button>
            <button onclick="loadDaysTab(this)" class="lib-cat-btn" id="catBtn_days">Days</button>
        </div>

        <div id="activeFilterChip" style="display:none; margin-bottom:20px;"></div>
        
        <div id="libItems">
            @include('admin.services._library_items')
        </div>
    </div>
</div>

{{-- FAB --}}
<div class="fab-ev">
    <div class="lib-fab-menu" id="fabMenu" style="display:none; flex-direction:column; gap:10px; margin-bottom:15px; align-items:flex-end;">
        <div class="lib-fab-opt" style="display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="openTypeModal('days')">
            <span style="background:#333; color:#fff; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:700;">Days</span>
            <div style="width:40px; height:40px; border-radius:50%; background:#555; color:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);"><i class="fa fa-calendar"></i></div>
        </div>
        <div class="lib-fab-opt" style="display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="openTypeModal('accommodation')">
            <span style="background:#333; color:#fff; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:700;">Accommodation</span>
            <div style="width:40px; height:40px; border-radius:50%; background:#0891b2; color:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);"><i class="fa fa-bed"></i></div>
        </div>
        <div class="lib-fab-opt" style="display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="openTypeModal('hotel')">
            <span style="background:#333; color:#fff; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:700;">Hotel</span>
            <div style="width:40px; height:40px; border-radius:50%; background:#0e7490; color:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);"><i class="fa fa-building"></i></div>
        </div>
        <div class="lib-fab-opt" style="display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="openTypeModal('activity')">
            <span style="background:#333; color:#fff; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:700;">Activity</span>
            <div style="width:40px; height:40px; border-radius:50%; background:#ea580c; color:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);"><i class="fa fa-binoculars"></i></div>
        </div>
        <div class="lib-fab-opt" style="display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="openTypeModal('restaurant')">
            <span style="background:#333; color:#fff; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:700;">Restaurant</span>
            <div style="width:40px; height:40px; border-radius:50%; background:#dc2626; color:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);"><i class="fa fa-cutlery"></i></div>
        </div>
        <div class="lib-fab-opt" style="display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="openTypeModal('guide')">
            <span style="background:#333; color:#fff; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:700;">Guide</span>
            <div style="width:40px; height:40px; border-radius:50%; background:#d97706; color:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);"><i class="fa fa-user-o"></i></div>
        </div>
        <div class="lib-fab-opt" style="display:flex; align-items:center; gap:10px; cursor:pointer;" onclick="openTypeModal('transport')">
            <span style="background:#333; color:#fff; padding:4px 10px; border-radius:4px; font-size:12px; font-weight:700;">Transport</span>
            <div style="width:40px; height:40px; border-radius:50%; background:#7c3aed; color:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);"><i class="fa fa-car"></i></div>
        </div>
    </div>
    <button class="fab-btn-ev" id="fabBtn" onclick="toggleFab()">
        <i class="fa fa-plus" id="fabIcon"></i> ADD 
        <img src="https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg" alt="UK">
    </button>
</div>

{{-- Edit/Add Modal --}}
<div class="lib-modal-bg" id="libModal">
    <div class="lib-modal-box" style="max-width:1100px;">
        <div class="lib-modal-head" id="libModalHead"><h3 id="libModalTitle"></h3><button class="lib-modal-close" onclick="closeModal()"><i class="fa fa-times"></i></button></div>
        <div class="lib-modal-body" id="libModalBody"></div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="lib-modal-bg" id="delModal">
    <div class="lib-modal-box" style="max-width:400px;text-align:center;padding:24px;">
        <div class="lib-confirm">
            <div class="lib-confirm-icon" style="margin-bottom:16px;"><i class="fa fa-trash" style="font-size:32px;color:#ef4444"></i></div>
            <h3 style="font-size:18px;font-weight:800;color:#222;margin:0 0 8px">Delete Service?</h3>
            <p id="delName" style="color:#666;font-size:14px;margin:0 0 24px;word-break:break-word;"></p>
            <div class="lib-confirm-btns" style="display:flex;gap:12px;justify-content:center;">
                <button class="btn-cancel" onclick="closeDelModal()" style="padding:10px 20px;border-radius:6px;border:1px solid #ddd;background:#fff;color:#555;font-size:13px;font-weight:600;cursor:pointer;flex:1;">Cancel</button>
                <button class="btn-del" onclick="confirmDel()" style="padding:10px 20px;border-radius:6px;border:none;background:#ef4444;color:#fff;font-size:13px;font-weight:600;cursor:pointer;flex:1;"><i class="fa fa-trash"></i> Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var countryId={{ $countryId ?: 0 }},catFilter='',searchTmr=null,delId=null,fabOpen=false;

function updateHotelList(star) {
    var tableWrap = document.getElementById('vendorServicesTableWrap');
    
    if(!star) {
        if(tableWrap) tableWrap.innerHTML = '';
        return;
    }

    // Immediately load ALL services for this star category
    loadVendorServicesByStar(star);
}

function loadVendorServicesByStar(star) {
    var tableWrap = document.getElementById('vendorServicesTableWrap');
    if(!tableWrap) return;

    tableWrap.innerHTML = '<div style="padding:20px; text-align:center; color:#999; font-size:12px;">Loading services...</div>';

    fetch('/admin/library/vendor-services-table/0?star=' + encodeURIComponent(star) + '&country_id=' + countryId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            tableWrap.innerHTML = data.html || '<div style="padding:20px; text-align:center; color:#999; font-size:12px;">No services found.</div>';
        })
        .catch(function(err) {
            tableWrap.innerHTML = '<div style="padding:20px; text-align:center; color:#ef4444; font-size:12px;">Failed to load services.</div>';
        });
}

function filterVendorTable(vendorId) {
    var rows = document.querySelectorAll('.svc-row');
    rows.forEach(function(row) {
        if(!vendorId || row.getAttribute('data-vendor') == vendorId) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function addServiceToCategory() {
    var existing = document.getElementById('addServiceFormWrap');
    if(existing) { existing.style.display = existing.style.display === 'none' ? 'block' : 'none'; return; }
    
    var tableWrap = document.getElementById('vendorServicesTableWrap');
    if(!tableWrap) return;

    // Build vendor options from the master vendor list
    var vendorSelect = document.getElementById('masterVendorList');
    var vendorOpts = '<option value="">-- Select Provider --</option>';
    if(vendorSelect) {
        for(var i=0; i < vendorSelect.options.length; i++) {
            vendorOpts += '<option value="'+vendorSelect.options[i].value+'">'+vendorSelect.options[i].text+'</option>';
        }
    }

    var formHtml = '<div id="addServiceFormWrap" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:20px; margin-bottom:16px;">';
    formHtml += '<div style="font-size:13px; font-weight:700; color:#1e293b; margin-bottom:12px;"><i class="fa fa-plus-circle" style="color:#ea580c;"></i> Add New Service</div>';
    formHtml += '<div style="display:flex; gap:12px; margin-bottom:12px;">';
    formHtml += '<div style="flex:2;"><label style="font-size:11px; color:#64748b; margin-bottom:4px; display:block;">Description</label><input type="text" id="newSvcDesc" placeholder="e.g. Double room on HB" style="width:100%; height:36px; border:1px solid #ddd; border-radius:6px; padding:0 10px; font-size:13px; outline:none;"></div>';
    formHtml += '<div style="flex:1;"><label style="font-size:11px; color:#64748b; margin-bottom:4px; display:block;">Cost (JOD)</label><input type="number" id="newSvcCost" step="0.01" placeholder="0.00" style="width:100%; height:36px; border:1px solid #ddd; border-radius:6px; padding:0 10px; font-size:13px; outline:none;"></div>';
    formHtml += '</div>';
    formHtml += '<div style="margin-bottom:12px;"><label style="font-size:11px; color:#64748b; margin-bottom:4px; display:block;">Provider / Hotel</label><select id="newSvcVender" style="width:100%; height:36px; border:1px solid #ddd; border-radius:6px; padding:0 10px; font-size:13px; outline:none; background:#fff;">'+vendorOpts+'</select></div>';
    formHtml += '<div style="display:flex; gap:8px; justify-content:flex-end;">';
    formHtml += '<button type="button" onclick="document.getElementById(\'addServiceFormWrap\').style.display=\'none\'" style="padding:8px 16px; border-radius:6px; border:1px solid #ddd; background:#fff; color:#64748b; font-size:12px; font-weight:600; cursor:pointer;">Cancel</button>';
    formHtml += '<button type="button" onclick="submitNewService()" style="padding:8px 16px; border-radius:6px; border:none; background:#ea580c; color:#fff; font-size:12px; font-weight:700; cursor:pointer;">Save Service</button>';
    formHtml += '</div></div>';

    tableWrap.insertAdjacentHTML('afterbegin', formHtml);
}

function submitNewService() {
    var desc = document.getElementById('newSvcDesc').value.trim();
    var cost = document.getElementById('newSvcCost').value;
    var vender = document.getElementById('newSvcVender').value;
    if(!desc) { alert('Please enter a description'); return; }
    if(!vender) { alert('Please select a provider/hotel'); return; }

    var catSelect = document.querySelector('[name=acc_category]');
    var star = catSelect ? catSelect.value : '';
    var categoryIdInput = document.getElementById('currentCategoryId');
    var catId = categoryIdInput ? categoryIdInput.value : 0;

    $.post('/admin/services', {
        _token: '{{ csrf_token() }}',
        description: desc,
        cost: cost || 0,
        vender: vender,
        country: countryId,
        category: catId,
        star: star
    }, function(r) {
        document.getElementById('addServiceFormWrap').remove();
        if(catId > 0) {
            // Edit modal: Refresh the specific hotel's services
            fetch('/admin/library/vendor-services-table/' + catId + '?country_id=' + countryId)
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    var tableWrap = document.getElementById('vendorServicesTableWrap');
                    if(tableWrap && data.html) tableWrap.innerHTML = data.html;
                });
        } else if(star) {
            // Add modal: Refresh by star rating
            loadVendorServicesByStar(star);
        }
    }).fail(function() {
        alert('Failed to save service');
    });
}

function loadVendorServices(categoryId) {
    var tableWrap = document.getElementById('vendorServicesTableWrap');
    if(!tableWrap) return;

    if(!categoryId) {
        // If "All Vendors" selected, reload by star
        var catSelect = document.querySelector('[name=acc_category]');
        if(catSelect && catSelect.value) {
            loadVendorServicesByStar(catSelect.value);
        } else {
            tableWrap.innerHTML = '';
        }
        return;
    }

    tableWrap.innerHTML = '<div style="padding:20px; text-align:center; color:#999; font-size:12px;">Loading vendor services...</div>';

    fetch('/admin/library/vendor-services-table/' + encodeURIComponent(categoryId))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            tableWrap.innerHTML = data.html || '';
        })
        .catch(function(err) {
            tableWrap.innerHTML = '<div style="padding:20px; text-align:center; color:#ef4444; font-size:12px;">Failed to load services.</div>';
        });
}

function debounceSearch(){clearTimeout(searchTmr);searchTmr=setTimeout(loadLib, 400);}

// "See more" filter — shows active chip like Evaneos
function seeMoreFilter(catId, catName){
    catFilter=catId;
    var chip=document.getElementById('activeFilterChip');
    chip.style.display='inline-flex';
    chip.innerHTML='<div class="lib-active-filter">'+catName+' <span class="x" onclick="clearFilter()">✕</span></div>';
    
    // Also sync the top buttons if applicable
    document.querySelectorAll('.lib-cat-btn').forEach(b => b.classList.remove('active'));
    var activeBtn = document.getElementById('catBtn_'+catId);
    if(activeBtn) activeBtn.classList.add('active');

    loadLib();
}
function clearFilter(){
    catFilter='';
    document.getElementById('activeFilterChip').style.display='none';
    document.getElementById('activeFilterChip').innerHTML='';
    document.querySelectorAll('.lib-cat-btn').forEach(b => b.classList.remove('active'));
    loadLib();
}

function toggleCatBtn(catId, btnEl) {
    if (catFilter == catId) {
        clearFilter(); // deselect if already selected
    } else {
        catFilter = catId;
        document.querySelectorAll('.lib-cat-btn').forEach(b => b.classList.remove('active'));
        btnEl.classList.add('active');
        document.getElementById('activeFilterChip').style.display='none';
        loadLib();
    }
}
function loadDaysTab(btnEl) {
    catFilter = null;
    document.querySelectorAll('.lib-cat-btn').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');
    document.getElementById('activeFilterChip').style.display='none';
    var s = document.getElementById('libSearch').value;
    document.getElementById('libItems').style.opacity='.4';
    $.get('{{ route("admin.library.days") }}', {search:s}, function(r){
        document.getElementById('libItems').innerHTML = r.html;
        document.getElementById('libItems').style.opacity='1';
    });
}

function loadLib(){
    var s=document.getElementById('libSearch').value;
    document.getElementById('libItems').style.opacity='.4';
    $.get('{{ route("admin.library.filter") }}', {country:countryId, search:s, category:catFilter}, function(r){
        document.getElementById('libItems').innerHTML=r.html;
        document.getElementById('libItems').style.opacity='1';
    });
}

function toggleMenu(btn){
    document.querySelectorAll('.lib-dropdown').forEach(function(m){if(m!==btn.nextElementSibling)m.style.display='none'});
    var m=btn.nextElementSibling; m.style.display=m.style.display==='block'?'none':'block';
}
document.addEventListener('click', function(e){if(!e.target.closest('.svc-dots')&&!e.target.closest('.day-card-dots')&&!e.target.closest('.lib-dropdown'))document.querySelectorAll('.lib-dropdown').forEach(function(m){m.style.display='none'})});

function editSvc(id, svcType){
    closeMenus(); showModal('Edit Service');
    var url = '/admin/services/'+id+'/edit?ajax=1';
    if(svcType) url += '&service_type=' + svcType;
    $.get(url, function(r){$('#libModalBody').html(r.html)});
}

function editCat(id, e){
    if(e) e.stopPropagation();
    closeMenus(); showModal('Modify category');
    $.get('/admin/services-category/'+id+'/edit', function(r){$('#libModalBody').html(r.html)});
}

function openSeasons(id, svcType){
    closeMenus(); showModal('Manage Seasons');
    var url = '/admin/services/'+id+'/seasons';
    if(svcType) url += '?service_type=' + svcType;
    $.ajax({url:url, type:'GET', dataType:'json',
        success:function(r){
            document.getElementById('libModalBody').innerHTML=r.html;
            bindSeasons(r.service_id, svcType);
        },
        error:function(x){
            document.getElementById('libModalBody').innerHTML='<p style="color:red;padding:20px;font-weight:700;">Error loading seasons: '+(x.responseJSON&&x.responseJSON.message?x.responseJSON.message:x.statusText)+'</p>';
        }
    });
}
function bindSeasons(sid, svcType){
    var typeParam = svcType ? '&service_type=' + svcType : '';
    $(document).off('click','#add_season_btn').on('click','#add_season_btn', function(){
        $.post('/admin/services/'+sid+'/seasons', {_token:'{{ csrf_token() }}', date_from:$('#season_from').val(), date_to:$('#season_to').val(), cost:$('#season_cost').val(), service_type:svcType||''}, function(){openSeasons(sid, svcType)});
    });
    $(document).off('click','.edit-season-btn').on('click','.edit-season-btn', function(){
        var row = $(this).closest('tr');
        row.find('.season-val').hide();
        row.find('.season-edit-input').show();
        row.find('.edit-season-btn').hide();
        row.find('.save-season-btn').show();
    });
    $(document).off('click','.save-season-btn').on('click','.save-season-btn', function(){
        var row = $(this).closest('tr');
        var id = $(this).data('id');
        var inputs = row.find('.season-edit-input');
        $.ajax({
            url: '/admin/services/seasons/'+id,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                date_from: $(inputs[0]).val(),
                date_to:   $(inputs[1]).val(),
                cost:      $(inputs[2]).val()
            },
            success: function(){ openSeasons(sid, svcType); },
            error: function(){ alert('Error saving season.'); }
        });
    });
    $(document).off('click','.del-season-btn').on('click','.del-season-btn', function(){
        if(!confirm('Delete?'))return;
        $.ajax({url:'/admin/services/seasons/'+$(this).data('id'), method:'DELETE', data:{_token:'{{ csrf_token() }}'}, success:function(){openSeasons(sid, svcType)}});
    });
}
function delSvc(id,name,svcType){closeMenus(); delId=id; window.delSvcType=svcType||''; document.getElementById('delName').textContent='"' + name + '"'; document.getElementById('delModal').style.display='flex';}
function confirmDel(){
    if(!delId)return;
    var data = {_token:'{{ csrf_token() }}', _method: 'DELETE'};
    if(window.delSvcType) data.service_type = window.delSvcType;
    $.ajax({
        url:'/admin/services/'+delId, 
        type:'POST', 
        data:data, 
        success:function(){
            closeDelModal(); 
            loadLib();
        },
        error:function(x){
            alert('Error: ' + (x.responseJSON?.message || 'Could not delete'));
        }
    });
}
function closeDelModal(){document.getElementById('delModal').style.display='none'; delId=null;}

function showModal(t){document.getElementById('libModalHead').innerHTML='<h3 id="libModalTitle">'+t+'</h3><button class="lib-modal-close" onclick="closeModal()"><i class="fa fa-times"></i></button>'; document.getElementById('libModalBody').innerHTML='<div style="text-align:center;padding:30px"><i class="fa fa-spinner fa-spin" style="font-size:24px;color:#ea580c"></i></div>'; document.getElementById('libModal').style.display='flex';}
function closeModal(){document.getElementById('libModal').style.display='none'; document.getElementById('libModalBody').innerHTML=''; document.getElementById('libModalHead').innerHTML='<h3 id="libModalTitle"></h3><button class="lib-modal-close" onclick="closeModal()"><i class="fa fa-times"></i></button>'; var d=document.getElementById('libAccDropdownFixed'); if(d){d.style.display='none';d.innerHTML='';} }
function closeMenus(){document.querySelectorAll('.lib-dropdown').forEach(function(m){m.style.display='none'});}

var currentModalType='';
function openTypeModal(type){
    window.svcDt = new DataTransfer();
    currentModalType=type;
    if(fabOpen)toggleFab();
    var titles={transport:'Create transport type', activity:'Enter an activity', accommodation:'Add accommodation', hotel:'Add a hotel', days:'Create another day', restaurant:'Add a restaurant'};
    var hdr=document.getElementById('libModalHead');
    hdr.innerHTML='<h3>'+titles[type]+'</h3><div style="display:flex;gap:10px;align-items:center"><a href="javascript:void(0)" onclick="closeModal()" style="font-size:13px;font-weight:700;color:#ea580c;text-decoration:none">Cancel</a><button form="typeCreateForm" type="submit" style="padding:8px 18px;border-radius:8px;border:none;background:#ea580c;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Create</button></div>';
    document.getElementById('libModal').style.display='flex';
    document.getElementById('libModalBody').innerHTML=getTypeForm(type);
}

function getTypeForm(type){
    var catId=type==='transport'?715:type==='activity'?93:type==='days'?204:type==='restaurant'?456:type==='hotel'?404:403;
    var html='<form id="typeCreateForm" onsubmit="return submitTypeForm(event)">'+
    '<input type="hidden" name="_token" value="{{ csrf_token() }}">'+
    '<input type="hidden" name="country" value="'+countryId+'">'+
    '<input type="hidden" name="category" value="'+catId+'">'+
    '<input type="hidden" name="method" value="Car">'+
    '<input type="hidden" name="service_type" value="'+(type==='transport'?'transport':type)+'">';

    if(type==='transport'){
        html+='<div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">'+
        langFlag('🇫🇷','fr',false)+langFlag('🇬🇧','en',true)+langFlag('🇮🇹','it',false)+langFlag('🇪🇸','es',false)+langFlag('🇩🇪','de',false)+langFlag('🇸🇪','se',false)+langFlag('🇳🇱','nl',false)+
        '</div>';
        html+='<div style="display:flex;gap:28px"><div style="flex:1"><div style="margin-bottom:20px"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:10px;display:block">Method of transport</label><div style="display:flex;gap:10px;flex-wrap:wrap">'+
        methodBtn('Bus','fa-bus',false)+methodBtn('Airplane','fa-plane',false)+methodBtn('Car','fa-car',true)+methodBtn('Boat','fa-ship',false)+methodBtn('Train','fa-train',false)+
        '</div></div><div style="margin-bottom:16px;position:relative"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Transport title</label><textarea name="description" id="titleArea" maxlength="255" required rows="2" oninput="document.getElementById(\'charCnt\').textContent=this.value.length" style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;resize:vertical"></textarea><span id="charCnt" style="position:absolute;right:10px;bottom:8px;font-size:11px;color:#bbb">0</span><span style="position:absolute;right:30px;bottom:8px;font-size:11px;color:#bbb">/255</span></div><div style="margin-bottom:16px"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Description</label><textarea name="notes" rows="4" style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px 14px;font-size:14px;outline:none;resize:vertical" placeholder="Add a description"></textarea></div></div><div style="flex:1"><div style="margin-bottom:16px"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Place of interest</label><fieldset id="depFieldset" style="border:1px solid #ddd;border-radius:8px;padding:12px 14px;margin-bottom:12px"><legend style="font-size:11px;color:#999;padding:0 4px">Departure locations</legend><div id="depPlaceholder" onclick="showDestInput(\'dep\')" style="display:flex;align-items:center;gap:8px;color:#ea580c;font-size:13px;font-weight:600;cursor:pointer"><i class="fa fa-map-marker" style="color:#ea580c"></i> Add a destination</div><input type="text" name="departure" id="depInput" style="display:none;width:100%;height:36px;border:1px solid #ea580c;border-radius:6px;padding:0 10px;font-size:13px;outline:none" placeholder="Enter departure location..."></fieldset><fieldset id="arrFieldset" style="border:1px solid #ddd;border-radius:8px;padding:12px 14px"><legend style="font-size:11px;color:#999;padding:0 4px">Arrival destination</legend><div id="arrPlaceholder" onclick="showDestInput(\'arr\')" style="display:flex;align-items:center;gap:8px;color:#ea580c;font-size:13px;font-weight:600;cursor:pointer"><i class="fa fa-map-marker" style="color:#ea580c"></i> Add a destination</div><input type="text" name="arrival" id="arrInput" style="display:none;width:100%;height:36px;border:1px solid #ea580c;border-radius:6px;padding:0 10px;font-size:13px;outline:none" placeholder="Enter arrival destination..."></fieldset></div><div style="display:flex;gap:12px;margin-top:16px"><div style="flex:1"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Length</label><input type="text" name="length_time" value="00:00" style="width:100%;height:38px;border:1px solid #ddd;border-radius:8px;padding:0 12px;font-size:13px;outline:none"></div><div style="flex:1"><label style="font-size:13px;font-weight:700;color:#555;margin-bottom:6px;display:block">Distance (km)</label><input type="text" name="distance" style="width:100%;height:38px;border:1px solid #ddd;border-radius:8px;padding:0 12px;font-size:13px;outline:none"></div></div></div></div>';
    } else if(type==='activity' || type==='restaurant' || type==='guide') {
        html+='<input type="hidden" name="cost" value="0"><div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">'+
        langFlag('🇫🇷','fr',false)+langFlag('🇬🇧','en',true)+langFlag('🇮🇹','it',false)+langFlag('🇪🇸','es',false)+langFlag('🇩🇪','de',false)+langFlag('🇸🇪','se',false)+langFlag('🇳🇱','nl',false)+
        '</div><div style="margin-bottom:16px;"><div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;"><span style="font-size:11px;font-weight:700;color:#555;">Photos:</span><a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a></div><div id="svcPhotosRow" style="display:flex;gap:12px;height:120px;"><div id="svcImageDrop" onclick="document.getElementById(\'svcImageInput\').click()" style="flex:1;min-width:100px;border:1px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:24px;cursor:pointer;"><i class="fa fa-camera"></i></div></div><input type="file" name="new_images[]" id="svcImageInput" accept="image/*" multiple style="display:none" onchange="previewSvcImageGrid(this)"></div>';
        var label = type==='restaurant'?'Restaurant name':type==='guide'?'Guide name':'Activity name';
        html+='<fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;position:relative;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">'+label+'</legend><input type="text" name="description" required style="width:100%;height:32px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;"><div style="position:absolute;right:0;bottom:-18px;font-size:10px;color:#bbb;">(0/255)</div></fieldset><fieldset style="width:100%;border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;position:relative;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend><div id="svcPlaceholder" onclick="showDestInput(\'svc\')" style="height:32px;display:flex;align-items:center;padding:0 12px;color:#ea580c;font-size:13px;font-weight:700;cursor:pointer;"><i class="fa fa-map-marker" style="margin-right:6px;"></i> Add a destination</div><input type="text" name="arrival" id="svcInput" style="display:none;width:100%;height:32px;border:none;background:transparent;padding:0 12px;font-size:13px;outline:none" placeholder="Enter destination..." autocomplete="off" oninput="libSvcAutocomplete(this.value)"><div id="svcArrivalDropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #ddd;border-radius:6px;box-shadow:0 4px 12px rgba(0,0,0,0.1);z-index:999;max-height:200px;overflow-y:auto;margin-top:4px;"></div></fieldset><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend><textarea name="notes" style="width:100%;min-height:250px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description"></textarea></fieldset>';
    } else if(type==='accommodation' || type==='hotel') {
        var nameLabel = type==='hotel' ? 'Hotel name' : 'Name of accommodation';
        html+='<input type="hidden" name="cost" value="0"><div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">'+
        langFlag('🇫🇷','fr',false)+langFlag('🇬🇧','en',true)+langFlag('🇮🇹','it',false)+langFlag('🇪🇸','es',false)+langFlag('🇩🇪','de',false)+langFlag('🇸🇪','se',false)+langFlag('🇳🇱','nl',false)+
        '</div><div style="margin-bottom:16px;"><div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;"><span style="font-size:11px;font-weight:700;color:#555;">Photos:</span><a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a></div><div id="svcPhotosRow" style="display:flex;gap:12px;height:120px;"><div id="svcImageDrop" onclick="document.getElementById(\'svcImageInput\').click()" style="flex:1;min-width:100px;border:1px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:24px;cursor:pointer;"><i class="fa fa-camera"></i></div></div><input type="file" name="new_images[]" id="svcImageInput" accept="image/*" multiple style="display:none" onchange="previewSvcImageGrid(this)"></div><div style="display:flex;gap:16px;margin-bottom:16px;"><div style="flex:1;"><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;position:relative;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">'+nameLabel+'</legend><input type="text" name="description" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;"><div style="position:absolute;right:0;bottom:-18px;font-size:10px;color:#bbb;">(0/255)</div></fieldset><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend><textarea name="notes" style="width:100%;min-height:160px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description"></textarea></fieldset></div><div style="flex:1;"><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Place of interest</legend><div id="accPlaceholder" onclick="showDestInput(\'acc\')" style="height:40px;display:flex;align-items:center;padding:0 12px;color:#ea580c;font-size:13px;font-weight:700;cursor:pointer;"><i class="fa fa-map-marker" style="margin-right:6px;color:#ea580c;"></i> Add a destination</div><input type="text" name="arrival" id="accInput" style="display:none;width:100%;height:40px;border:none;background:transparent;padding:0 12px;font-size:13px;outline:none" placeholder="Enter destination..."></fieldset><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Category</legend><select name="acc_category" onchange="updateHotelList(this.value)" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;"><option value="">Select a category</option><option value="1 Star">1 Star</option><option value="2 Star">2 Star</option><option value="3 Star">3 Star</option><option value="4 Star">4 Star</option><option value="5 Star">5 Star</option><option value="Standard">Standard</option><option value="Superior">Superior</option><option value="Luxury">Luxury</option></select></fieldset><fieldset id="hotelSelectWrap" style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0 0 16px 0;display:none;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Select Hotel</legend><select name="category_parent" id="hotelSelect" onchange="if(this.value && !document.querySelector(\'[name=description]\').value) document.querySelector(\'[name=description]\').value=this.options[this.selectedIndex].text" style="width:100%;height:40px;border:none;outline:none;padding:0 8px;font-size:13px;background:transparent;color:#555;"><option value="">Select a hotel</option></select></fieldset><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Website</legend><input type="text" name="website" style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;" placeholder=""></fieldset></div></div>';
    } else if(type==='days'){
        html+='<input type="hidden" name="cost" value="0"><div style="display:flex;gap:8px;margin-bottom:22px;align-items:center">'+
        langFlag('🇫🇷','fr',false)+langFlag('🇬🇧','en',true)+langFlag('🇮🇹','it',false)+langFlag('🇪🇸','es',false)+langFlag('🇩🇪','de',false)+langFlag('🇸🇪','se',false)+langFlag('🇳🇱','nl',false)+
        '</div><div style="margin-bottom:16px;"><div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;"><span style="font-size:11px;font-weight:700;color:#555;">Photos:</span><a href="#" onclick="return false;" style="font-size:11px;font-weight:700;color:#ea580c;text-decoration:none;">How to choose the right photos?</a></div><div id="svcPhotosRow" style="display:flex;gap:12px;height:120px;"><div id="svcImageDrop" onclick="document.getElementById(\'svcImageInput\').click()" style="flex:1;min-width:100px;border:1px dashed #ccc;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:24px;cursor:pointer;"><i class="fa fa-camera"></i></div></div><input type="file" name="new_images[]" id="svcImageInput" accept="image/*" multiple style="display:none" onchange="previewSvcImageGrid(this)"></div><div style="display:flex;gap:12px;margin-bottom:20px;"><fieldset style="flex:1;border:1px solid #ddd;border-radius:4px;padding:0;margin:0;position:relative;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Day title</legend><input type="text" name="description" required style="width:100%;height:40px;border:none;outline:none;padding:0 12px;font-size:13px;background:transparent;"><div style="position:absolute;right:0;bottom:-18px;font-size:10px;color:#bbb;">(0/255)</div></fieldset><fieldset style="flex:0.6;border:1px solid #ddd;border-radius:4px;padding:0;margin:0;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Site(s)</legend><div id="dayPlaceholder" onclick="showDestInput(\'day\')" style="height:40px;display:flex;align-items:center;padding:0 12px;color:#ea580c;font-size:13px;font-weight:700;cursor:pointer;"><i class="fa fa-map-marker" style="margin-right:6px;color:#ea580c;"></i> Add a destination</div><input type="text" name="arrival" id="dayInput" style="display:none;width:100%;height:40px;border:none;background:transparent;padding:0 12px;font-size:13px;outline:none" placeholder="Enter destination..."></fieldset></div><fieldset style="border:1px solid #ddd;border-radius:4px;padding:0;margin:0;margin-bottom:16px;"><legend style="font-size:10px;color:#999;margin-left:10px;padding:0 4px;">Description</legend><textarea name="notes" style="width:100%;min-height:200px;border:none;outline:none;padding:8px 12px;font-size:13px;resize:vertical;background:transparent;" placeholder="Add a description"></textarea></fieldset>';
    }
    html+='</form>';
    return html;
}

function methodBtn(label, icon, active){
    var border=active?'2px solid #ea580c':'2px solid #ddd';
    var bg=active?'#ffedd5':'#fff';
    var clr=active?'#ea580c':'#888';
    var fw=active?'700':'400';
    return '<div class="method-opt" data-val="'+label+'" onclick="pickMethod(this)"><div style="width:56px;height:56px;border-radius:10px;border:'+border+';background:'+bg+';display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s"><i class="fa '+icon+'" style="font-size:22px;color:'+clr+'"></i></div><span style="font-size:11px;color:'+clr+';font-weight:'+fw+';margin-top:4px;display:block;text-align:center">'+label+'</span></div>';
}

function langFlag(emoji, code, active){
    var bg=active?'#ea580c':'transparent';
    var border=active?'2px solid #ea580c':'2px solid transparent';
    return '<div class="lang-flag" data-lang="'+code+'" onclick="pickLang(this)" style="width:40px;height:32px;border-radius:6px;border:'+border+';background:'+bg+';display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:20px;transition:all .2s">'+emoji+'</div>';
}

function pickLang(el){
    document.querySelectorAll('.lang-flag').forEach(function(f){f.style.border='2px solid transparent'; f.style.background='transparent';});
    el.style.border='2px solid #ea580c'; el.style.background='#ea580c';
}

function showDestInput(type){
    var ph=document.getElementById(type+'Placeholder');
    var inp=document.getElementById(type+'Input');
    if(ph && inp){ph.style.display='none'; inp.style.display='block'; inp.focus();}
}

function pickMethod(el){
    document.querySelectorAll('.method-opt').forEach(function(m){
        m.querySelector('div').style.border='2px solid #ddd';
        m.querySelector('div').style.background='#fff';
        m.querySelector('i').style.color='#888';
        m.querySelector('span').style.color='#888';
        m.querySelector('span').style.fontWeight='400';
    });
    el.querySelector('div').style.border='2px solid #ea580c';
    el.querySelector('div').style.background='#ffedd5';
    el.querySelector('i').style.color='#ea580c';
    el.querySelector('span').style.color='#ea580c';
    el.querySelector('span').style.fontWeight='700';
    document.querySelector('[name=method]').value=el.dataset.val;
}

function showToast(msg, type){
    var old=document.getElementById('libToast');
    if(old)old.remove();
    var bg=type==='success'?'#ea580c':'#dc3545';
    var icon=type==='success'?'fa-check-circle':'fa-exclamation-circle';
    var t=document.createElement('div');
    t.id='libToast';
    t.style.cssText='position:fixed;top:24px;right:24px;z-index:99999;display:flex;align-items:center;gap:10px;padding:14px 24px;border-radius:10px;background:'+bg+';color:#fff;font-size:14px;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.18);transform:translateX(120%);transition:transform .4s ease;';
    t.innerHTML='<i class="fa '+icon+'" style="font-size:18px"></i> '+msg;
    document.body.appendChild(t);
    setTimeout(function(){t.style.transform='translateX(0)';},50);
    setTimeout(function(){t.style.transform='translateX(120%)'; setTimeout(function(){t.remove()},500)},3000);
}

window.svcDt = new DataTransfer();

function previewSvcImageGrid(input) {
    if (input.files && input.files.length > 0) {
        for(let i=0; i<input.files.length; i++) {
            window.svcDt.items.add(input.files[i]);
        }
    }
    input.files = window.svcDt.files;
    renderSvcImageGrid(input);
}

function renderSvcImageGrid(input) {
    var row = document.getElementById('svcPhotosRow');
    var addBtn = document.getElementById('svcImageDrop');
    
    var existing = row.querySelectorAll('.svc-photo-wrap');
    existing.forEach(e => e.remove());
    
    for(let i=0; i<window.svcDt.files.length; i++) {
        let file = window.svcDt.files[i];
        let reader = new FileReader();
        reader.onload = function(e){
            let div = document.createElement('div');
            div.className = 'svc-photo-wrap';
            div.style.cssText = 'position:relative; flex-shrink:0; height:100%; aspect-ratio:1.5; border-radius:4px; overflow:hidden;';
            div.innerHTML = '<img src="' + e.target.result + '" style="width:100%; height:100%; border-radius:4px; object-fit:cover;">' + 
                            '<div class="del-btn" style="position:absolute;top:6px;right:6px;width:24px;height:24px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 4px rgba(0,0,0,0.2);cursor:pointer;"><i class="fa fa-trash" style="color:#555;font-size:12px;"></i></div>';
            
            div.querySelector('.del-btn').onclick = function(event) {
                event.stopPropagation();
                let newDt = new DataTransfer();
                for(let f=0; f<window.svcDt.files.length; f++) {
                    if(f !== i) newDt.items.add(window.svcDt.files[f]);
                }
                window.svcDt = newDt;
                input.files = window.svcDt.files;
                renderSvcImageGrid(input);
            };
            
            row.insertBefore(div, addBtn);
        };
        reader.readAsDataURL(file);
    }
}

function addAccImages(input) {
    if(input.files && input.files.length > 0){
        var row = document.getElementById('catPhotosRow') || document.getElementById('accPhotosRow');
        if(!row) return;
        var addBtn = row.lastElementChild;
        
        for(var i=0; i<input.files.length; i++){
            var reader = new FileReader();
            reader.onload = function(e){
                var div = document.createElement('div');
                div.className = 'acc-photo-wrap';
                div.style.cssText = 'position:relative;flex-shrink:0;height:104px;';
                div.innerHTML = '<img src="' + e.target.result + '" style="height:100%;border-radius:4px;object-fit:cover;">' +
                                '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
                row.insertBefore(div, addBtn);
            };
            reader.readAsDataURL(input.files[i]);
        }
    }
}

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
                                '<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
                row.insertBefore(div, addBtn);
            };
            reader.readAsDataURL(input.files[i]);
        }
    }
}

function submitTypeForm(e){
    e.preventDefault();
    var form=document.getElementById('typeCreateForm');
    var fd=new FormData(form);
    var btn=$('button[form="typeCreateForm"]');
    btn.prop('disabled',true).text('Creating...');
    var submitUrl=currentModalType==='days'?'{{ route("admin.canned-days.store-ajax") }}':'{{ route("admin.services.store") }}';
    $.ajax({
        url:submitUrl,
        type:'POST',
        data:fd,
        processData:false,
        contentType:false,
        success:function(r){closeModal(); loadLib(); showToast('Service created successfully!','success');},
        error:function(x){btn.prop('disabled',false).text('Create'); showToast('Error: '+(x.responseJSON?.message||'Could not create'),'error');}
    });
    return false;
}

window.editServiceSubmit=function(id){
    $.ajax({url:'/admin/services/'+id, method:'PUT', data:$('#edit_service_form').serialize()+'&_token={{ csrf_token() }}', success:function(){closeModal(); loadLib()}});
};
window.submitEditAcc=function(id){
    var form=document.getElementById('editAccForm');
    var fd=new FormData(form);
    fd.append('_method','PUT');
    fd.append('_token','{{ csrf_token() }}');
    fd.append('service_type','accommodation');
    $.ajax({url:'/admin/services/'+id, type:'POST', data:fd, processData:false, contentType:false, success:function(){closeModal(); loadLib(); showToast('Accommodation updated!','success');}, error:function(x){showToast('Error: '+(x.responseJSON?.message||'Could not update'),'error');}});
};
window.submitEditTransport=function(id){
    var form=document.getElementById('editTransForm');
    var fd=new FormData(form);
    fd.append('_method','PUT');
    fd.append('_token','{{ csrf_token() }}');
    fd.append('service_type','transport');
    $.ajax({url:'/admin/services/'+id, type:'POST', data:fd, processData:false, contentType:false, success:function(){closeModal(); loadLib(); showToast('Transport updated!','success');}, error:function(x){showToast('Error: '+(x.responseJSON?.message||'Could not update'),'error');}});
};
window.submitEditRestaurant=function(id){
    var form=document.getElementById('editRestForm');
    var fd=new FormData(form);
    fd.append('_method','PUT');
    fd.append('_token','{{ csrf_token() }}');
    fd.append('service_type','restaurant');
    $.ajax({url:'/admin/services/'+id, type:'POST', data:fd, processData:false, contentType:false, success:function(){closeModal(); loadLib(); showToast('Restaurant updated!','success');}, error:function(x){showToast('Error: '+(x.responseJSON?.message||'Could not update'),'error');}});
};
window.selectTransMethod=function(radio){
    var labels=radio.closest('div').querySelectorAll('label');
    labels.forEach(function(l){l.style.border='1px solid #ddd'; l.style.background='#fff'; l.querySelector('i').style.color='#888'; l.querySelector('span').style.color='#888';});
    var lbl=radio.closest('label');
    lbl.style.border='2px solid #ea580c'; lbl.style.background='#ffedd5'; lbl.querySelector('i').style.color='#ea580c'; lbl.querySelector('span').style.color='#ea580c';
};
window.submitEditActivity=function(id){
    var form=document.getElementById('editActForm');
    var fd=new FormData(form);
    fd.append('_method','PUT');
    fd.append('_token','{{ csrf_token() }}');
    fd.append('service_type','activity');
    $.ajax({url:'/admin/services/'+id, type:'POST', data:fd, processData:false, contentType:false, success:function(){closeModal(); loadLib(); showToast('Activity updated!','success');}, error:function(x){showToast('Error: '+(x.responseJSON?.message||'Could not update'),'error');}});
};
window.submitEditAccSection=function(id){
    var form=document.getElementById('editActSecForm');
    var fd=new FormData(form);
    fd.append('_method','PUT');
    fd.append('_token','{{ csrf_token() }}');
    fd.append('service_type','accommodation');
    $.ajax({url:'/admin/services/'+id, type:'POST', data:fd, processData:false, contentType:false, success:function(){closeModal(); loadLib(); showToast('Hotel updated!','success');}, error:function(x){showToast('Error: '+(x.responseJSON?.message||'Could not update'),'error');}});
};
window.submitEditTransSection=function(id){
    var form=document.getElementById('editTransSecForm');
    var fd=new FormData(form);
    fd.append('_method','PUT');
    fd.append('_token','{{ csrf_token() }}');
    fd.append('service_type','accommodation');
    $.ajax({url:'/admin/services/'+id, type:'POST', data:fd, processData:false, contentType:false, success:function(){closeModal(); loadLib(); showToast('Hotel updated!','success');}, error:function(x){showToast('Error: '+(x.responseJSON?.message||'Could not update'),'error');}});
};
window.addTransSecImages=function(input){
    var row=document.getElementById('transSecPhotosRow');
    for(var i=0;i<input.files.length;i++){
        var f=input.files[i], r=new FileReader();
        r.onload=function(e){
            var d=document.createElement('div');
            d.className='acc-photo-wrap';
            d.style='position:relative;flex-shrink:0;height:104px;';
            d.innerHTML='<img src="'+e.target.result+'" style="height:100%;border-radius:4px;object-fit:cover;"><button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            row.insertBefore(d, row.lastElementChild);
        };
        r.readAsDataURL(f);
    }
};
window.submitEditRestSection=function(id){
    var form=document.getElementById('editRestSecForm');
    var fd=new FormData(form);
    fd.append('_method','PUT');
    fd.append('_token','{{ csrf_token() }}');
    fd.append('service_type','accommodation');
    $.ajax({url:'/admin/services/'+id, type:'POST', data:fd, processData:false, contentType:false, success:function(){closeModal(); loadLib(); showToast('Hotel updated!','success');}, error:function(x){showToast('Error: '+(x.responseJSON?.message||'Could not update'),'error');}});
};
window.addRestSecImages=function(input){
    var row=document.getElementById('restSecPhotosRow');
    for(var i=0;i<input.files.length;i++){
        var f=input.files[i], r=new FileReader();
        r.onload=function(e){
            var d=document.createElement('div');
            d.className='acc-photo-wrap';
            d.style='position:relative;flex-shrink:0;height:104px;';
            d.innerHTML='<img src="'+e.target.result+'" style="height:100%;border-radius:4px;object-fit:cover;"><button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:2px;right:2px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.6);color:#fff;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
            row.insertBefore(d, row.lastElementChild);
        };
        r.readAsDataURL(f);
    }
};
window.deleteActivityRow=function(actId, hotelId){
    if(!confirm('Delete this activity?')) return;
    $.ajax({
        url:'/admin/services/'+actId,
        type:'POST',
        data:{_token:'{{ csrf_token() }}', _method:'DELETE', service_type:'activity'},
        success:function(){
            showToast('Activity deleted!','success');
            editSvc(hotelId,'activity_section');
        },
        error:function(x){
            showToast('Error: '+(x.responseJSON?.message||'Could not delete'),'error');
        }
    });
};
// ── Day card menu (always defined, not AJAX-dependent) ──
window.toggleLibDayMenu=function(e,id){
    e.preventDefault(); e.stopPropagation();
    var m=document.getElementById('lib_day_menu_'+id);
    var open=m&&!m.classList.contains('open');
    window.closeLibDayMenus();
    if(m&&open) m.classList.add('open');
};
window.closeLibDayMenus=function(){
    document.querySelectorAll('.ev-card-menu.open').forEach(function(m){m.classList.remove('open');});
};
document.addEventListener('click',function(){ window.closeLibDayMenus(); });

window.editDay=function(id){
    closeMenus();
    window.closeLibDayMenus();
    window._dayDt = new DataTransfer();
    showModal('Modify day');
    $.ajax({
        url: '/admin/canned-days/'+id+'/edit-ajax',
        type: 'GET',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        success: function(r){
            $('#libModalBody').html(r.html);
        },
        error: function(xhr){
            $('#libModalBody').html('<p style="color:red;padding:20px;font-weight:700;">Error loading day ('+xhr.status+'). Please try again.</p>');
        }
    });
};
window.deleteDay=function(id, title){
    if(!confirm('Delete day "'+title+'"?')) return;
    $.ajax({url:'/admin/canned-days/'+id+'/delete', type:'GET', success:function(){
        loadDaysTab(document.getElementById('catBtn_days'));
        showToast('Day deleted!','success');
    }, error:function(){showToast('Could not delete','error');}});
};
window.submitEditDay=function(id){
    // Sync all Quill editors before collecting form data
    if(window._dayQuills){
        Object.keys(window._dayQuills).forEach(function(L){
            var q=window._dayQuills[L];
            var h=document.getElementById('quillHidden_'+L);
            if(h&&q) h.value=q.root.innerHTML;
        });
    }
    var form=document.getElementById('editDayForm');
    if(!form){return;}
    var fd=new FormData(form);
    fd.append('_token','{{ csrf_token() }}');
    $.ajax({url:'/admin/canned-days/'+id+'/update-ajax', type:'POST', data:fd, processData:false, contentType:false,
        success:function(){ location.reload(); },
        error:function(x){ showToast('Error: '+(x.responseJSON&&x.responseJSON.message?x.responseJSON.message:'Could not update'),'error'); }
    });
};
window.addDaySiteTag=function(){
    var name=prompt('Enter destination name:');
    if(!name) return;
    var tags=document.getElementById('daySiteTags');
    var tag=document.createElement('div');
    tag.style='display:inline-flex;align-items:center;gap:4px;background:#f0f0f0;border-radius:16px;padding:4px 10px;font-size:12px;font-weight:600;color:#333;';
    tag.innerHTML='<i class="fa fa-map-marker" style="color:#aaa;font-size:10px;"></i> '+name+'<span onclick="this.parentElement.remove()" style="cursor:pointer;color:#999;margin-left:4px;font-size:14px;">✕</span>';
    tags.appendChild(tag);
};
window.addDayImages=function(input){
    if(!input.files||!input.files.length) return;
    if(!window._dayDt) window._dayDt = new DataTransfer();
    var newFiles = [];
    for(var i=0; i<input.files.length; i++){
        window._dayDt.items.add(input.files[i]);
        newFiles.push(input.files[i]);
    }
    input.files = window._dayDt.files;
    var row=document.getElementById('dayPhotosRow');
    if(!row) return;
    var addBtn=row.lastElementChild;
    for(var i=0; i<newFiles.length; i++){
        (function(file){
            var reader=new FileReader();
            reader.onload=function(e){
                var div=document.createElement('div');
                div.style='position:relative;flex-shrink:0;height:88px;';
                div.innerHTML='<img src="'+e.target.result+'" style="height:100%;border-radius:8px;object-fit:cover;min-width:88px;">'
                    +'<button type="button" onclick="this.parentElement.remove()" style="position:absolute;top:3px;right:3px;width:20px;height:20px;border-radius:50%;border:none;background:rgba(0,0,0,0.65);color:#fff;font-size:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;">✕</button>';
                row.insertBefore(div,addBtn);
            };
            reader.readAsDataURL(file);
        })(newFiles[i]);
    }
};
window.addServiceSubmit=function(){$.post('{{ route("admin.services.store") }}', $('#add_service_form').serialize(), function(){closeModal(); loadLib()});};

window.categorySubmit=function(editId){
    var url=editId?'/admin/services-category/'+editId+'/update':'{{ route("admin.services.store-category") }}';
    var formId = editId ? 'edit_category_form' : 'add_category_form';
    var formElement = document.getElementById(formId);
    var fd = new FormData(formElement);
    fd.append('_token', '{{ csrf_token() }}');
    
    $.ajax({
        url: url,
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(r){
            if(r.success){closeModal(); location.reload()}
            else{alert(r.message||'Error')}
        },
        error: function(){
            alert('Error');
        }
    });
};

// ---- Place Of Interest Autocomplete (Modify Accommodation modal) ----
var libAccTimer = null;
var libAccActiveIdx = -1;

function qtpEscapeHtmlLib(value) {
    return String(value || '').replace(/[&<>"']/g, function(ch) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[ch];
    });
}

function libAccAutocomplete(query) {
    clearTimeout(libAccTimer);
    libAccActiveIdx = -1;
    var dropdown = document.getElementById('editAccArrivalDropdown');
    if (!dropdown) return;
    
    if (!query || query.length < 2) { 
        dropdown.style.display = 'none'; 
        dropdown.innerHTML = ''; 
        return; 
    }
    
    libAccTimer = setTimeout(function() {
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&addressdetails=1&limit=6&accept-language=en')
            .then(function(r) { return r.json(); })
            .then(function(results) {
                dropdown.innerHTML = '';
                if (!results || !results.length) { dropdown.style.display = 'none'; return; }
                
                results.forEach(function(place, idx) {
                    var addr = place.address || {};
                    var city = addr.city || addr.town || addr.village || addr.hamlet || addr.county || '';
                    var state = addr.state || '';
                    var country = addr.country || '';
                    
                    var displayParts = [];
                    if (city) displayParts.push(city);
                    if (state && state !== city) displayParts.push(state);
                    
                    var btn = document.createElement('div');
                    btn.style.cssText = 'display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;color:#1e293b;cursor:pointer;border-bottom:1px solid #f1f5f9;transition:all 0.2s;';
                    btn.setAttribute('data-idx', idx);
                    
                    var html = '<i class="fa fa-map-marker" style="color:#9ca3af;font-size:13px;flex-shrink:0;"></i> ';
                    html += '<span style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">';
                    if (displayParts.length) html += '<span style="font-weight:600;color:#1e293b;">' + qtpEscapeHtmlLib(displayParts.join(', ')) + '</span> ';
                    if (country) html += '<span style="font-weight:700;color:#ea580c;">' + qtpEscapeHtmlLib(country) + '</span>';
                    html += '</span>';
                    
                    btn.innerHTML = html;
                    
                    btn.onmouseover = function() { this.style.background='#f0fdf8'; };
                    btn.onmouseout = function() { this.style.background=(libAccActiveIdx === idx ? '#f0fdf8' : ''); };
                    btn.onclick = function() {
                        var label = city || state || country || place.display_name;
                        var inp2 = document.getElementById('editAccArrivalInput');
                        if (inp2) inp2.value = label;
                        dropdown.style.display = 'none';
                        dropdown.innerHTML = '';
                    };
                    dropdown.appendChild(btn);
                });
                dropdown.style.display = 'block';
            })
            .catch(function() { dropdown.style.display = 'none'; });
    }, 300);
}

function libAccInputKey(event) {
    var dropdown = document.getElementById('editAccArrivalDropdown');
    var items = dropdown ? dropdown.querySelectorAll('div[data-idx]') : [];
    if (!dropdown || dropdown.style.display === 'none' || items.length === 0) return;

    if (event.key === 'ArrowDown') {
        event.preventDefault();
        libAccActiveIdx = Math.min(libAccActiveIdx + 1, items.length - 1);
        items.forEach(function(el, i) { el.style.background = (i === libAccActiveIdx ? '#f0fdf8' : ''); });
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        libAccActiveIdx = Math.max(libAccActiveIdx - 1, 0);
        items.forEach(function(el, i) { el.style.background = (i === libAccActiveIdx ? '#f0fdf8' : ''); });
    } else if (event.key === 'Enter') {
        if (libAccActiveIdx >= 0 && items[libAccActiveIdx]) {
            event.preventDefault();
            items[libAccActiveIdx].click();
        }
    } else if (event.key === 'Escape') {
        dropdown.style.display = 'none';
    }
}

document.addEventListener('click', function(e) {
    var dropdown = document.getElementById('editAccArrivalDropdown');
    var inp = document.getElementById('editAccArrivalInput');
    if (dropdown && !e.target.closest('#editAccArrivalDropdown') && e.target !== inp) {
        dropdown.style.display = 'none';
    }
    
    var svcDropdown = document.getElementById('svcArrivalDropdown');
    var svcInp = document.getElementById('svcInput');
    if (svcDropdown && !e.target.closest('#svcArrivalDropdown') && e.target !== svcInp) {
        svcDropdown.style.display = 'none';
    }
});

var libSvcTimer = null;
var libSvcActiveIdx = -1;

function libSvcAutocomplete(query) {
    clearTimeout(libSvcTimer);
    libSvcActiveIdx = -1;
    var dropdown = document.getElementById('svcArrivalDropdown');
    if (!dropdown) return;
    
    if (!query || query.length < 2) { 
        dropdown.style.display = 'none'; 
        dropdown.innerHTML = ''; 
        return; 
    }
    
    libSvcTimer = setTimeout(function() {
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&addressdetails=1&limit=6&accept-language=en')
            .then(function(r) { return r.json(); })
            .then(function(results) {
                dropdown.innerHTML = '';
                if (!results || !results.length) { dropdown.style.display = 'none'; return; }
                
                results.forEach(function(place, idx) {
                    var addr = place.address || {};
                    var city = addr.city || addr.town || addr.village || addr.hamlet || addr.county || '';
                    var state = addr.state || '';
                    var country = addr.country || '';
                    
                    var displayParts = [];
                    if (city) displayParts.push(city);
                    if (state && state !== city) displayParts.push(state);
                    
                    var btn = document.createElement('div');
                    btn.style.cssText = 'display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:13px;color:#1e293b;cursor:pointer;border-bottom:1px solid #f1f5f9;transition:all 0.2s;';
                    btn.setAttribute('data-idx', idx);
                    
                    var html = '<i class="fa fa-map-marker" style="color:#9ca3af;font-size:13px;flex-shrink:0;"></i> ';
                    html += '<span style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">';
                    if (displayParts.length) html += '<span style="font-weight:600;color:#1e293b;">' + qtpEscapeHtmlLib(displayParts.join(', ')) + '</span> ';
                    if (country) html += '<span style="font-weight:700;color:#ea580c;">' + qtpEscapeHtmlLib(country) + '</span>';
                    html += '</span>';
                    
                    btn.innerHTML = html;
                    
                    btn.onmouseover = function() { this.style.background='#f0fdf8'; };
                    btn.onmouseout = function() { this.style.background=(libSvcActiveIdx === idx ? '#f0fdf8' : ''); };
                    btn.onclick = function() {
                        var label = city || state || country || place.display_name;
                        var inp2 = document.getElementById('svcInput');
                        if (inp2) inp2.value = label;
                        dropdown.style.display = 'none';
                        dropdown.innerHTML = '';
                    };
                    dropdown.appendChild(btn);
                });
                dropdown.style.display = 'block';
            })
            .catch(function(e){ console.error('Autocomplete Error:', e); });
    }, 400);
}
// ---- End Place Of Interest Autocomplete ----

function toggleFab(){
    fabOpen=!fabOpen;
    var m=document.getElementById('fabMenu'), i=document.getElementById('fabIcon'), btn=document.getElementById('fabBtn');
    if(fabOpen){
        m.style.display='flex'; i.style.transform='rotate(45deg)';
        btn.innerHTML='<i class="fa fa-times" id="fabIcon" style="transition:transform .3s"></i> CLOSE <img src="https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg" alt="UK">';
        var bd=document.createElement('div'); bd.id='fabBD'; bd.style.cssText='position:fixed;inset:0;z-index:999;background:rgba(0,0,0,.15)'; bd.onclick=toggleFab; document.body.appendChild(bd);
    }else{
        m.style.display='none';
        btn.innerHTML='<i class="fa fa-plus" id="fabIcon" style="transition:transform .3s"></i> ADD <img src="https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg" alt="UK">';
        var bd=document.getElementById('fabBD'); if(bd)bd.remove();
    }
}

document.getElementById('libModal').addEventListener('click', function(e){if(e.target===this)closeModal()});
document.getElementById('delModal').addEventListener('click', function(e){if(e.target===this)closeDelModal()});
document.addEventListener('keydown', function(e){if(e.key==='Escape'){closeModal(); closeDelModal(); if(fabOpen)toggleFab();}});
</script>
@endpush
