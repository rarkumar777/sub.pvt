@extends('admin.layouts.app')
@section('title', 'Admin | Venders')
@section('content')
<div class="tw-flex tw-flex-col tw-gap-8">
    {{-- Breadcrumbs & Header --}}
    <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-items-start md:tw-items-center tw-gap-4">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-[11px] tw-font-black tw-text-orange-500 tw-uppercase tw-tracking-[0.2em] tw-mb-2">
                <a href="{{ route('admin.services.index') }}" class="hover:tw-text-orange-700 tw-transition-colors">Services</a>
                <i class="fa fa-angle-right tw-text-slate-300"></i>
                <span class="tw-text-slate-400">Management</span>
                <i class="fa fa-angle-right tw-text-slate-300"></i>
                <span class="tw-text-orange-900">Venders</span>
            </div>
            <h1 class="tw-text-4xl tw-font-black tw-text-orange-900 tw-tracking-tight tw-flex tw-items-center tw-gap-3">
                <span class="tw-w-1.5 tw-h-8 tw-bg-orange-600 tw-rounded-full"></span>
                Service <span class="tw-text-orange-600">Venders</span>
            </h1>
            <p class="tw-text-sm tw-text-slate-500 tw-font-medium tw-mt-1">Manage and monitor all service providers and their accounts.</p>
        </div>
        <div class="tw-flex tw-gap-3">
            <a href="{{ route('admin.services.index') }}" class="btn red !tw-px-8 !tw-py-4 tw-shadow-xl tw-shadow-rose-500/10">
                <i class="fa fa-arrow-left"></i> Back to Services
            </a>
        </div>
    </div>

    {{-- Filament Filter Section (Colorful Variant) --}}
    <div class="tw-bg-white tw-rounded-xl tw-border-t-4 tw-border-t-orange-500 tw-border tw-border-slate-200 tw-shadow-[0_8px_30px_rgb(0,0,0,0.04)] tw-mb-6">
        <div class="tw-px-6 tw-py-5 tw-border-b tw-border-slate-100">
            <h3 class="tw-text-sm tw-font-black tw-text-orange-900 tw-uppercase tw-tracking-widest tw-flex tw-items-center">
                <i class="fa fa-filter tw-mr-2 tw-text-orange-500 tw-text-lg"></i> Filter Records
            </h3>
        </div>
        <div class="tw-p-6">
            <form action="{{ url()->current() }}" method="GET" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 lg:tw-grid-cols-4 tw-gap-6 tw-items-end">
                
                <div class="tw-flex tw-flex-col tw-gap-2">
                    <label class="tw-text-[11px] tw-font-bold tw-text-orange-600 tw-uppercase tw-tracking-wider">Country Vector</label>
                    <select name="country" class="tw-w-full tw-h-11 tw-px-3 tw-text-sm tw-font-semibold tw-text-slate-700 tw-bg-slate-50 tw-border tw-border-slate-200 focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-100 tw-rounded-xl tw-shadow-sm outline-none tw-transition-all">
                        <option value="">Consolidated View</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->lang_id }}" {{ request('country') == $c->lang_id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="tw-flex tw-flex-col tw-gap-2 lg:tw-col-span-1">
                    <label class="tw-text-[11px] tw-font-bold tw-text-orange-600 tw-uppercase tw-tracking-wider">Corporation Name</label>
                    <input type="text" name="company" value="{{ request('company') }}" placeholder="Search company..." class="tw-w-full tw-h-11 tw-px-4 tw-text-sm tw-font-semibold tw-text-slate-700 tw-bg-slate-50 tw-border tw-border-slate-200 focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-100 tw-rounded-xl tw-shadow-sm outline-none tw-transition-all">
                </div>

                <div class="tw-flex tw-flex-col tw-gap-2 lg:tw-col-span-1">
                    <label class="tw-text-[11px] tw-font-bold tw-text-orange-600 tw-uppercase tw-tracking-wider">Email Details</label>
                    <input type="text" name="email" value="{{ request('email') }}" placeholder="Vendor Email..." class="tw-w-full tw-h-11 tw-px-4 tw-text-sm tw-font-semibold tw-text-slate-700 tw-bg-slate-50 tw-border tw-border-slate-200 focus:tw-border-orange-500 focus:tw-ring-2 focus:tw-ring-orange-100 tw-rounded-xl tw-shadow-sm outline-none tw-transition-all">
                </div>

                <div class="tw-flex tw-items-center tw-justify-end tw-gap-3 lg:tw-col-span-1">
                    <a href="{{ url()->current() }}" class="tw-h-11 tw-px-5 tw-rounded-xl tw-bg-rose-50 tw-text-rose-600 tw-text-sm tw-font-bold hover:tw-bg-rose-500 hover:tw-text-white tw-flex tw-items-center tw-justify-center tw-transition-all outline-none">
                        Reset
                    </a>
                    <button type="submit" class="tw-h-11 tw-px-6 tw-rounded-xl tw-bg-orange-600 tw-text-white tw-text-sm tw-font-bold hover:tw-bg-orange-700 tw-shadow-lg tw-shadow-orange-600/30 tw-flex tw-items-center tw-justify-center tw-transition-all outline-none">
                        <i class="fa fa-white tw-mr-2"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    @php
        $themeGradients = [
            'tw-from-orange-500 tw-to-orange-600',
            'tw-from-orange-400 tw-to-teal-600',
            'tw-from-rose-400 tw-to-red-500',
            'tw-from-violet-500 tw-to-purple-600',
            'tw-from-amber-400 tw-to-orange-500',
            'tw-from-cyan-400 tw-to-orange-500',
            'tw-from-fuchsia-400 tw-to-pink-600'
        ];
    @endphp

    {{-- Strict Filament PHP Table (Colorful Variant) --}}
    <div class="tw-bg-white tw-rounded-xl tw-overflow-hidden tw-shadow-[0_8px_30px_rgb(0,0,0,0.04)] tw-border tw-border-slate-200">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50 tw-border-b tw-border-slate-200">
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Service Provider</th>
                        <th class="tw-px-6 tw-py-4 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Corporation</th>
                        <th class="tw-px-6 tw-py-4 tw-text-right tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Balance Exposure</th>
                        <th class="tw-px-6 tw-py-4 tw-text-right tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest tw-w-0 tw-whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-100">
                    @forelse($venders as $v)
                    @php
                        $gradClass = $themeGradients[$v->id % count($themeGradients)];
                        $initials = strtoupper(substr(trim($v->first_name), 0, 1)) . strtoupper(substr(trim($v->last_name), 0, 1));
                    @endphp
                    <tr class="hover:tw-bg-orange-50/30 tw-transition-colors">
                        <td class="tw-px-6 tw-py-4">
                            <div class="tw-flex tw-items-center tw-gap-4">
                                <div class="tw-relative tw-shrink-0">
                                    <div class="tw-w-10 tw-h-10 tw-rounded-xl tw-bg-gradient-to-br {{ $gradClass }} tw-text-white tw-flex tw-items-center tw-justify-center tw-font-black tw-text-[13px] tw-shadow-md">
                                        {{ $initials ?: '--' }}
                                    </div>
                                    <div class="tw-absolute -tw-bottom-1 -tw-right-1 tw-w-3.5 tw-h-3.5 tw-bg-orange-500 tw-border-2 tw-border-white tw-rounded-full"></div>
                                </div>
                                <div class="tw-flex tw-flex-col tw-justify-center">
                                    <span class="tw-text-sm tw-font-black tw-text-slate-800">{{ html_entity_decode($v->first_name, ENT_QUOTES, 'UTF-8') }} {{ html_entity_decode($v->last_name, ENT_QUOTES, 'UTF-8') }}</span>
                                    <div class="tw-flex tw-items-center tw-text-xs tw-text-slate-500 tw-mt-0.5">
                                        <span class="tw-font-bold tw-text-orange-400">#{{ str_pad($v->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        <span class="tw-mx-2 tw-text-slate-300">&bull;</span>
                                        <span class="tw-font-medium">{{ $v->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="tw-px-6 tw-py-4">
                            <div class="tw-flex tw-flex-col">
                                <span class="tw-text-[13px] tw-text-orange-900 tw-font-bold"><i class="fa fa-briefcase tw-text-orange-400 tw-mr-1"></i> {{ html_entity_decode($v->company, ENT_QUOTES, 'UTF-8') ?: 'Private Independent' }}</span>
                                <div class="tw-flex tw-items-center tw-text-[11px] tw-font-semibold tw-text-slate-500 tw-mt-1">
                                    <i class="fa fa-map-marker tw-text-rose-400 tw-mr-1"></i>
                                    <span>{{ $v->city ?: 'Local HQ' }}, {{ $v->country ?: 'Global' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="tw-px-6 tw-py-4 tw-text-right">
                            @php $bal = $v->venderBalance->balance ?? 0; @endphp
                            @if($bal > 0)
                                <span class="tw-inline-flex tw-flex-col tw-items-end tw-justify-center">
                                    <span class="tw-text-sm tw-font-black tw-text-rose-600">{{ number_format($bal, 2) }}</span>
                                    <span class="tw-text-[9px] tw-font-black tw-text-rose-400 tw-uppercase tw-tracking-widest tw-bg-rose-50 tw-px-2 tw-py-0.5 tw-rounded tw-mt-1">JOD Balance</span>
                                </span>
                            @else
                                <span class="tw-inline-flex tw-flex-col tw-items-end tw-justify-center">
                                    <span class="tw-text-sm tw-font-black tw-text-orange-500">0.00</span>
                                    <span class="tw-text-[9px] tw-font-black tw-text-orange-400 tw-uppercase tw-tracking-widest tw-bg-orange-50 tw-px-2 tw-py-0.5 tw-rounded tw-mt-1">JOD Balance</span>
                                </span>
                            @endif
                        </td>
                        <td class="tw-px-6 tw-py-4 tw-text-right tw-whitespace-nowrap">
                            <div class="tw-flex tw-justify-end tw-gap-2">
                                <button type="button" onclick="openVenderAccount({{ $v->id }});" class="tw-px-3 tw-py-1.5 tw-rounded-lg tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-text-[11px] tw-font-black tw-uppercase tw-tracking-wider tw-transition-colors outline-none tw-shadow-sm">
                                    Account
                                </button>
                                <button type="button" onclick="openVenderDescription({{ $v->id }});" class="tw-px-3 tw-py-1.5 tw-rounded-lg tw-bg-amber-50 tw-text-amber-600 hover:tw-bg-amber-500 hover:tw-text-white tw-text-[11px] tw-font-black tw-uppercase tw-tracking-wider tw-transition-colors outline-none tw-shadow-sm">
                                    Profile
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="tw-py-12 tw-text-center">
                            <i class="fa fa-users tw-text-5xl tw-text-orange-100 tw-mb-3"></i>
                            <h3 class="tw-mt-2 tw-text-sm tw-font-black tw-text-slate-800 tw-uppercase tw-tracking-widest">No vendors found</h3>
                            <p class="tw-mt-1 tw-text-sm tw-font-medium tw-text-slate-500">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="tw-flex tw-justify-center tw-mt-4">
        <div class="tw-bg-white tw-px-6 tw-py-3 tw-rounded-xl tw-shadow-sm tw-border tw-border-slate-200">
            {{ $venders->links() }}
        </div>
    </div>
</div>

<!-- Vender Account Modal -->
<div id="vender_account" class="modal">
    <div class="tw-max-w-7xl tw-w-[95%] !tw-p-0 !tw-bg-transparent">
        <div class="tw-relative">
            <div class="modal-close tw-absolute tw-top-6 tw-right-8 tw-z-50">
                <a href="#close" class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-white/10 tw-text-white hover:tw-bg-rose-500 tw-transition-all tw-flex tw-items-center tw-justify-center tw-backdrop-blur-md">
                    <i class="fa fa-times"></i>
                </a>
            </div>
            <div id="vender_account_content" class="tw-bg-white tw-rounded-3xl tw-overflow-hidden tw-shadow-2xl"></div>
        </div>
    </div>
</div>

<!-- Vender Description Modal -->
<div id="vender_description" class="modal">
    <div class="tw-max-w-5xl tw-w-[90%] !tw-p-0 !tw-bg-transparent">
        <div class="tw-relative">
            <div class="modal-close tw-absolute tw-top-6 tw-right-8 tw-z-50">
                <a href="#close" class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-white/10 tw-text-white hover:tw-bg-rose-500 tw-transition-all tw-flex tw-items-center tw-justify-center tw-backdrop-blur-md">
                    <i class="fa fa-times"></i>
                </a>
            </div>
            <div id="vender_description_content" class="tw-bg-white tw-rounded-3xl tw-overflow-hidden tw-shadow-2xl"></div>
        </div>
    </div>
</div>

<!-- Vender Images File Manager Modal -->
<div id="vender_images_modal" class="modal">
    <div class="tw-max-w-6xl tw-w-[95%] !tw-p-0 !tw-bg-transparent">
        <div class="tw-relative">
            <div class="modal-close tw-absolute tw-top-6 tw-right-8 tw-z-50">
                <a href="#close" class="tw-w-10 tw-h-10 tw-rounded-full tw-bg-white/10 tw-text-white hover:tw-bg-rose-500 tw-transition-all tw-flex tw-items-center tw-justify-center tw-backdrop-blur-md">
                    <i class="fa fa-times"></i>
                </a>
            </div>
            
            <div class="tw-bg-white tw-rounded-[3rem] tw-overflow-hidden tw-shadow-[0_40px_100px_rgba(0,0,0,0.2)] tw-flex tw-flex-col tw-min-h-[700px]">
                <div class="tw-px-10 tw-py-10 tw-bg-orange-900 tw-flex tw-justify-between tw-items-center tw-relative tw-overflow-hidden">
                    <div class="tw-absolute tw-top-0 tw-right-0 tw-w-96 tw-h-96 tw-bg-orange-500/10 tw-rounded-full -tw-mr-32 -tw-mt-32 tw-blur-3xl"></div>
                    <div class="tw-flex tw-items-center tw-gap-5 tw-relative tw-z-10">
                        <div class="tw-w-14 tw-h-14 tw-rounded-2xl tw-bg-white/10 tw-text-white tw-flex tw-items-center tw-justify-center tw-backdrop-blur-md tw-border tw-border-white/10 tw-shadow-xl">
                            <i class="fa fa-folder-open tw-text-xl"></i>
                        </div>
                        <div>
                            <h3 class="tw-text-2xl tw-font-black tw-text-white tw-tracking-tight">Global Assets <span class="tw-text-white/30 tw-font-light">Discovery</span></h3>
                            <p class="tw-text-[11px] tw-text-slate-400 tw-font-black tw-uppercase tw-tracking-[0.2em] tw-mt-1">Manage and select vendor portfolio resources</p>
                        </div>
                    </div>
                </div>

                <div id="file_manager_toolbar" class="tw-px-10 tw-py-6 tw-bg-slate-50 tw-border-b tw-border-slate-100 tw-flex tw-justify-between tw-items-center">
                    <div id="file_manager_breadcrumbs" class="tw-flex tw-items-center tw-gap-3 tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest">
                        <!-- Breadcrumbs injected here -->
                    </div>
                    <div class="tw-flex tw-gap-4">
                        <button class="tw-px-6 tw-py-3 tw-rounded-xl tw-bg-white tw-text-slate-600 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-border tw-border-slate-200 hover:tw-bg-slate-100 tw-transition-all tw-flex tw-items-center tw-gap-2" onclick="createFileManagerFolder()"><i class="fa fa-plus-circle tw-text-orange-500"></i> New Folder</button>
                        <button class="tw-px-6 tw-py-3 tw-rounded-xl tw-bg-orange-500 tw-text-white tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-shadow-xl tw-shadow-orange-500/20 hover:tw-scale-105 tw-transition-transform tw-flex tw-items-center tw-gap-2" onclick="$('#fm_upload_input').click()"><i class="fa fa-cloud-upload"></i> Upload Asset</button>
                        <input type="file" id="fm_upload_input" style="display:none" onchange="uploadFileManagerFile(this)">
                    </div>
                </div>

                <div id="file_manager_content" class="tw-p-10 tw-overflow-y-auto tw-flex-1 tw-max-h-[600px] tw-bg-white">
                    <!-- Content injected here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fm-item { text-align: center; cursor: pointer; padding: 20px; border-radius: 24px; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); border: 2px solid transparent; }
    .fm-item:hover { background: #f8fafc; border-color: #e2e8f0; transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
    .fm-icon { font-size: 48px; margin-bottom: 12px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05)); }
    .fm-icon.folder { color: #f59e0b; }
    .fm-icon.file { color: #ea580c; }
    .fm-name { font-size: 11px; font-weight: 800; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%; display: block; text-transform: uppercase; letter-spacing: 0.05em; }
    .fm-img-prev { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 20px; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: transform 0.5s; }
    .fm-item:hover .fm-img-prev { transform: scale(1.05); }
    #file_manager_breadcrumbs a { color: #ea580c; font-weight: 900; }
    #file_manager_breadcrumbs .fa-angle-right { opacity: 0.3; font-size: 10px; }
</style>

<script>
function openVenderAccount(venderId) {
    $("#vender_account_content").html('<div class="d-pad align-center"><i class="fa-spinner fa-spin fa-3x"></i></div>');
    window.location.hash = 'vender_account';
    
    $.ajax({
        url: "{{ url('admin/services-venders') }}/" + venderId + "/account",
        type: "GET",
        success: function(response) {
            $("#vender_account_content").html(response.html);
        },
        error: function() {
            $("#vender_account_content").html('<div class="d-pad align-center red-text">Error loading account details.</div>');
        }
    });
}

function openVenderDescription(venderId) {
    $("#vender_description_content").html('<div class="d-pad align-center"><i class="fa-spinner fa-spin fa-3x"></i></div>');
    window.location.hash = 'vender_description';
    
    $.ajax({
        url: "{{ url('admin/services-venders') }}/" + venderId + "/description",
        type: "GET",
        success: function(response) {
            $("#vender_description_content").html(response.html);
        },
        error: function() {
            $("#vender_description_content").html('<div class="d-pad align-center red-text">Error loading description.</div>');
        }
    });
}

function addVenderImage() {
    window.location.hash = 'vender_images_modal';
    fetchFileManagerContent('');
}

var currentFMDir = '';

function fetchFileManagerContent(dir) {
    currentFMDir = dir;
    $("#file_manager_content").html('<div class="align-center d-pad"><i class="fa-spinner fa-spin fa-3x"></i></div>');
    
    $.ajax({
        url: "{{ route('admin.ajax.file-manager-browse') }}",
        type: "GET",
        data: { dir: dir },
        success: function(res) {
            renderFileManager(res);
        },
        error: function() {
            $("#file_manager_content").html('<div class="align-center d-pad red-text">Error loading files.</div>');
        }
    });
}

function renderFileManager(data) {
    var html = '<div class="tw-grid tw-grid-cols-2 sm:tw-grid-cols-4 md:tw-grid-cols-6 tw-gap-4">';
    
    // Render Folders
    data.folders.forEach(function(folder) {
        var path = (data.current_dir ? data.current_dir + '/' : '') + folder;
        html += '<div class="fm-item" onclick="fetchFileManagerContent(\'' + path + '\')">';
        html += '    <div class="fm-icon folder"><i class="fa fa-folder"></i></div>';
        html += '    <span class="fm-name">' + folder + '</span>';
        html += '</div>';
    });
    
    // Render Files
    data.files.forEach(function(file) {
        html += '<div class="fm-item" onclick="selectFileManagerImage(\'' + file.url + '\')">';
        html += '    <img src="' + file.url + '" class="fm-img-prev">';
        html += '    <span class="fm-name">' + file.name + '</span>';
        html += '</div>';
    });
    
    if (data.folders.length === 0 && data.files.length === 0) {
        html += '<div class="sd-12 align-center grey-text d-pad">Directory is empty</div>';
    }
    
    html += '</div>';
    $("#file_manager_content").html(html);
    
    // Render Breadcrumbs
    var bc = '<a href="javascript:void(0)" onclick="fetchFileManagerContent(\'\')"><i class="fa-home"></i></a>';
    if (data.current_dir) {
        var parts = data.current_dir.split('/');
        var cumulativePath = '';
        parts.forEach(function(part) {
            cumulativePath += (cumulativePath ? '/' : '') + part;
            bc += ' <i class="fa-angle-right"></i> <a href="javascript:void(0)" onclick="fetchFileManagerContent(\'' + cumulativePath + '\')">' + part + '</a>';
        });
    }
    $("#file_manager_breadcrumbs").html(bc);
}

function selectFileManagerImage(url) {
    var html = '<div class="tw-relative tw-aspect-square tw-rounded-2xl tw-overflow-hidden tw-border tw-border-slate-100 tw-shadow-sm group vender-image-item">';
    html += '<img src="' + url + '" class="tw-w-full tw-h-full tw-object-cover tw-transition-transform tw-duration-500 group-hover:tw-scale-110">';
    html += '<input type="hidden" name="images[]" value="' + url + '">';
    html += '<div class="tw-absolute tw-inset-0 tw-bg-orange-900/40 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-opacity tw-flex tw-items-center tw-justify-center">';
    html += '    <button type="button" onclick="$(this).closest(\'.vender-image-item\').remove();" class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-rose-500 tw-text-white tw-flex tw-items-center tw-justify-center tw-shadow-lg"><i class="fa fa-times"></i></button>';
    html += '</div></div>';
    $("#vender_images_container").append(html);
    window.location.hash = 'vender_description';
}

function createFileManagerFolder() {
    var name = prompt("Enter folder name:");
    if (!name) return;
    
    $.ajax({
        url: "{{ route('admin.ajax.file-manager-create-folder') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}", dir: currentFMDir, folder_name: name },
        success: function(res) {
            if (res.success) fetchFileManagerContent(currentFMDir);
            else alert(res.message);
        }
    });
}

function uploadFileManagerFile(input) {
    if (!input.files.length) return;
    
    var formData = new FormData();
    formData.append('file', input.files[0]);
    formData.append('dir', currentFMDir);
    formData.append('_token', "{{ csrf_token() }}");
    
    var $btn = $(".btn.orange.small");
    $btn.prop('disabled', true).html('<i class="fa-spinner fa-spin"></i> Uploading...');
    
    $.ajax({
        url: "{{ route('admin.ajax.file-manager-upload') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa-upload"></i> Upload');
            if (res.success) fetchFileManagerContent(currentFMDir);
            else alert(res.message);
        },
        error: function() {
            $btn.prop('disabled', false).html('<i class="fa-upload"></i> Upload');
            alert("Upload failed.");
        }
    });
}

function saveVenderDescription(venderId) {
    var formData = $("#vender_desc_form").serialize();
    var $btn = $("#vender_desc_form").find("button[onclick^='saveVenderDescription']");
    $btn.prop('disabled', true).html('<i class="fa-spinner fa-spin"></i> Saving...');
    
    $.ajax({
        url: "{{ url('admin/services-venders') }}/" + venderId + "/description",
        type: "POST",
        data: formData,
        success: function(response) {
            alert("Detail saved successfully.");
            $btn.prop('disabled', false).html('<i class="fa-check"></i> Save');
        },
        error: function() {
            alert("Error saving details.");
            $btn.prop('disabled', false).html('<i class="fa-check"></i> Save');
        }
    });
}
</script>
@endsection
