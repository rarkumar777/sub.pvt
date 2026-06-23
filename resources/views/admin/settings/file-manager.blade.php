@extends('admin.layouts.app')
@section('title', 'Admin | File Manager')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">
    {{-- Filament Header & Actions --}}
    <div class="tw-flex tw-flex-col md:tw-flex-row tw-justify-between tw-items-start md:tw-items-center tw-gap-4">
        <div>
            <h1 class="tw-text-2xl tw-font-bold tw-text-slate-900 tw-tracking-tight tw-flex tw-items-center tw-gap-2">
                <i class="fa fa-folder-open-o tw-text-yellow-500"></i>
                File Manager
            </h1>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-sm tw-text-slate-500 tw-mt-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-indigo-600 tw-transition-colors">Dashboard</a>
                <i class="fa fa-chevron-right tw-text-[10px] tw-text-slate-400"></i>
                <span class="tw-font-medium tw-text-slate-700">Settings</span>
            </div>
        </div>
        <div class="tw-flex tw-gap-3">
            <button type="button" onclick="document.getElementById('new_folder_modal').style.display='flex'" class="tw-inline-flex tw-items-center tw-justify-center tw-px-4 tw-py-2 tw-text-sm tw-font-semibold tw-text-slate-700 tw-bg-white tw-border tw-border-slate-300 tw-rounded-lg hover:tw-bg-slate-50 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-indigo-500 focus:tw-ring-offset-2 tw-transition-all tw-shadow-sm">
                <i class="fa fa-folder-o tw-mr-2 tw-text-slate-400"></i> New Folder
            </button>
            <button type="button" onclick="document.getElementById('upload_modal').style.display='flex'" class="tw-inline-flex tw-items-center tw-justify-center tw-px-4 tw-py-2 tw-text-sm tw-font-semibold tw-text-white tw-bg-indigo-600 tw-border tw-border-transparent tw-rounded-lg hover:tw-bg-indigo-500 focus:tw-outline-none focus:tw-ring-2 focus:tw-ring-indigo-500 focus:tw-ring-offset-2 tw-transition-all tw-shadow-sm">
                <i class="fa fa-upload tw-mr-2"></i> Upload
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="tw-p-4 tw-rounded-lg tw-bg-emerald-50 tw-border tw-border-emerald-200 tw-flex tw-items-start tw-gap-3">
            <i class="fa fa-check-circle tw-text-emerald-500 tw-mt-0.5"></i>
            <div>
                <h3 class="tw-text-sm tw-font-medium tw-text-emerald-800">Success</h3>
                <div class="tw-mt-1 tw-text-sm tw-text-emerald-700">{{ session('success') }}</div>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="tw-p-4 tw-rounded-lg tw-bg-rose-50 tw-border tw-border-rose-200 tw-flex tw-items-start tw-gap-3">
            <i class="fa fa-times-circle tw-text-rose-500 tw-mt-0.5"></i>
            <div>
                <h3 class="tw-text-sm tw-font-medium tw-text-rose-800">Error</h3>
                <div class="tw-mt-1 tw-text-sm tw-text-rose-700">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    {{-- Filament Breadcrumb Widget --}}
    <div class="tw-bg-white tw-rounded-xl tw-border tw-border-slate-200 tw-shadow-sm tw-overflow-hidden">
        <div class="tw-px-4 tw-py-3 tw-flex tw-items-center tw-flex-wrap tw-gap-2">
            <a href="{{ route('admin.settings.file-manager') }}" class="tw-inline-flex tw-items-center tw-text-sm tw-font-medium tw-text-slate-500 hover:tw-text-indigo-600 tw-transition-colors">
                <i class="fa fa-home tw-mr-1.5"></i> Root
            </a>
            
            @if($currentDir)
                @php
                    $parts = explode('/', $currentDir);
                    $buildPath = '';
                    $parentParts = $parts;
                    array_pop($parentParts);
                    $parentDir = implode('/', $parentParts);
                @endphp
                
                <i class="fa fa-chevron-right tw-text-[10px] tw-text-slate-300 tw-mt-0.5"></i>
                <a href="{{ route('admin.settings.file-manager', ['dir' => $parentDir]) }}" class="tw-inline-flex tw-items-center tw-px-2 tw-py-1 tw-rounded-md tw-text-xs tw-font-medium tw-text-slate-600 tw-bg-slate-100 hover:tw-bg-slate-200 tw-transition-colors" title="Go up one level">
                    <i class="fa fa-level-up tw-mr-1"></i> Up
                </a>

                @foreach($parts as $index => $part)
                    @if($part)
                        @php $buildPath .= ($buildPath ? '/' : '') . $part; @endphp
                        <i class="fa fa-chevron-right tw-text-[10px] tw-text-slate-300 tw-mt-0.5"></i>
                        <a href="{{ route('admin.settings.file-manager', ['dir' => $buildPath]) }}" class="tw-inline-flex tw-items-center tw-text-sm tw-font-medium {{ $loop->last ? 'tw-text-indigo-600' : 'tw-text-slate-500 hover:tw-text-indigo-600' }} tw-transition-colors">
                            {{ $part }}
                        </a>
                    @endif
                @endforeach
            @endif
        </div>
        
        {{-- File Manager Workspace --}}
        <div class="tw-bg-slate-50 tw-border-t tw-border-slate-200 tw-p-6 tw-min-h-[400px]">
            @if(empty($folders) && empty($files))
                <div class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-py-16">
                    <div class="tw-w-16 tw-h-16 tw-rounded-full tw-bg-white tw-border tw-border-slate-200 tw-flex tw-items-center tw-justify-center tw-mb-4 tw-shadow-sm">
                        <i class="fa fa-folder-open-o tw-text-2xl tw-text-yellow-400"></i>
                    </div>
                    <h3 class="tw-text-sm tw-font-medium tw-text-slate-900">Empty directory</h3>
                    <p class="tw-text-sm tw-text-slate-500 tw-mt-1">Get started by uploading a file or creating a folder.</p>
                </div>
            @else
                <div class="tw-grid tw-grid-cols-3 sm:tw-grid-cols-4 md:tw-grid-cols-6 lg:tw-grid-cols-8 tw-gap-4">
                    
                    {{-- Folders --}}
                    @foreach($folders as $folder)
                    <div class="tw-group tw-relative tw-flex tw-flex-col tw-items-center tw-p-4 tw-bg-white tw-rounded-xl tw-border tw-border-slate-200 hover:tw-border-indigo-300 hover:tw-ring-1 hover:tw-ring-indigo-300 tw-transition-all tw-shadow-sm">
                        
                        <div class="tw-absolute tw-top-1.5 tw-right-1.5 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-opacity z-10">
                            <form method="POST" action="{{ route('admin.settings.file-manager.delete-folder') }}" onsubmit="return confirm('Delete folder {{ $folder }}?')">
                                @csrf
                                <input type="hidden" name="dir" value="{{ $currentDir }}">
                                <input type="hidden" name="folder" value="{{ $folder }}">
                                <button type="submit" class="tw-p-1.5 tw-rounded-md tw-text-slate-400 hover:tw-bg-rose-50 hover:tw-text-rose-600 tw-transition-colors" title="Delete Folder">
                                    <i class="fa fa-trash-o tw-text-[13px]"></i>
                                </button>
                            </form>
                        </div>
                        
                        <a href="{{ route('admin.settings.file-manager', ['dir' => ($currentDir ? $currentDir.'/' : '') . $folder]) }}" class="tw-flex tw-flex-col tw-items-center tw-w-full">
                            <i class="fa fa-folder tw-text-4xl tw-text-yellow-400 group-hover:tw-text-yellow-500 tw-mb-2 tw-drop-shadow-sm"></i>
                            <span class="tw-text-xs tw-font-medium tw-text-slate-700 tw-w-full tw-truncate tw-text-center" title="{{ $folder }}">{{ $folder }}</span>
                        </a>
                    </div>
                    @endforeach

                    {{-- Files --}}
                    @foreach($files as $file)
                    @php
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['jpg','jpeg','png','gif','bmp','tiff','webp','svg']);
                        $fileUrl = '/uploads/filemanager/' . ($currentDir ? $currentDir.'/' : '') . $file;
                    @endphp
                    <div class="tw-group tw-relative tw-flex tw-flex-col tw-items-center tw-p-3 tw-bg-white tw-rounded-xl tw-border tw-border-slate-200 hover:tw-border-indigo-300 hover:tw-ring-1 hover:tw-ring-indigo-300 tw-transition-all tw-shadow-sm">
                        
                        <div class="tw-absolute tw-top-1.5 tw-right-1.5 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-opacity tw-flex tw-gap-0.5 z-10 tw-bg-white/90 tw-backdrop-blur-sm tw-rounded-md tw-shadow-sm border border-slate-100">
                            <button type="button" onclick="openRename('{{ $file }}')" class="tw-p-1.5 tw-rounded-md tw-text-slate-500 hover:tw-bg-indigo-50 hover:tw-text-indigo-600 tw-transition-colors" title="Rename File">
                                <i class="fa fa-pencil tw-text-[13px]"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.settings.file-manager.delete-file') }}" onsubmit="return confirm('Delete {{ $file }}?')">
                                @csrf
                                <input type="hidden" name="dir" value="{{ $currentDir }}">
                                <input type="hidden" name="file" value="{{ $file }}">
                                <button type="submit" class="tw-p-1.5 tw-rounded-md tw-text-slate-500 hover:tw-bg-rose-50 hover:tw-text-rose-600 tw-transition-colors" title="Delete File">
                                    <i class="fa fa-trash-o tw-text-[13px]"></i>
                                </button>
                            </form>
                        </div>

                        <a href="{{ $fileUrl }}" target="_blank" class="tw-w-full tw-flex tw-flex-col tw-items-center">
                            @if($isImage)
                                <div class="tw-w-full tw-h-16 tw-mb-2">
                                    <img src="{{ $fileUrl }}" alt="{{ $file }}" class="tw-w-full tw-h-full tw-object-cover tw-rounded-lg">
                                </div>
                            @else
                                <div class="tw-w-full tw-h-16 tw-flex tw-items-center tw-justify-center tw-bg-slate-50 tw-rounded-lg tw-mb-2 tw-border tw-border-slate-100">
                                    <i class="fa fa-file-text-o tw-text-3xl tw-text-slate-300"></i>
                                </div>
                            @endif
                            <span class="tw-text-xs tw-font-medium tw-text-slate-700 tw-w-full tw-truncate tw-text-center" title="{{ $file }}">{{ $file }}</span>
                        </a>
                    </div>
                    @endforeach

                </div>
            @endif
        </div>
    </div>
</div>

{{-- Filament Styled Modals --}}

{{-- New Folder Modal --}}
<div id="new_folder_modal" style="display:none;" class="tw-fixed tw-inset-0 tw-z-[9999] tw-bg-slate-900/50 tw-backdrop-blur-sm tw-items-center tw-justify-center tw-p-4">
    <div class="tw-bg-white tw-w-full tw-max-w-md tw-rounded-xl tw-shadow-xl tw-overflow-hidden tw-relative">
        <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-200 tw-flex tw-items-center tw-justify-between">
            <h3 class="tw-text-base tw-font-semibold tw-text-slate-900 tw-flex tw-items-center">
                <i class="fa fa-folder-o tw-mr-2 tw-text-yellow-500"></i> Create folder
            </h3>
            <button onclick="document.getElementById('new_folder_modal').style.display='none'" class="tw-text-slate-400 hover:tw-text-slate-600 tw-transition-colors"><i class="fa fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.settings.file-manager.new-folder') }}" class="tw-p-6">
            @csrf
            <input type="hidden" name="dir" value="{{ $currentDir }}">
            <div class="tw-mb-6">
                <label class="tw-block tw-text-sm tw-font-medium tw-text-slate-700 tw-mb-1">Folder name</label>
                <input type="text" name="folder_name" required placeholder="e.g., Reports" class="tw-w-full tw-h-10 tw-px-3 tw-bg-white tw-border tw-border-slate-300 tw-rounded-lg tw-text-sm tw-text-slate-900 focus:tw-ring-1 focus:tw-ring-indigo-500 focus:tw-border-indigo-500 tw-transition-shadow outline-none">
            </div>
            <div class="tw-flex tw-justify-end tw-gap-3">
                <button type="button" onclick="document.getElementById('new_folder_modal').style.display='none'" class="tw-px-4 tw-py-2 tw-rounded-lg tw-bg-white tw-border tw-border-slate-300 tw-text-sm tw-font-medium tw-text-slate-700 hover:tw-bg-slate-50 tw-transition-colors">Cancel</button>
                <button type="submit" class="tw-px-4 tw-py-2 tw-rounded-lg tw-bg-indigo-600 tw-text-white tw-text-sm tw-font-medium hover:tw-bg-indigo-500 tw-shadow-sm tw-transition-colors">Create</button>
            </div>
        </form>
    </div>
</div>

{{-- Upload Modal --}}
<div id="upload_modal" style="display:none;" class="tw-fixed tw-inset-0 tw-z-[9999] tw-bg-slate-900/50 tw-backdrop-blur-sm tw-items-center tw-justify-center tw-p-4">
    <div class="tw-bg-white tw-w-full tw-max-w-md tw-rounded-xl tw-shadow-xl tw-overflow-hidden tw-relative">
        <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-200 tw-flex tw-items-center tw-justify-between">
            <h3 class="tw-text-base tw-font-semibold tw-text-slate-900 tw-flex tw-items-center">
                <i class="fa fa-upload tw-mr-2 tw-text-slate-400"></i> Upload file
            </h3>
            <button onclick="document.getElementById('upload_modal').style.display='none'" class="tw-text-slate-400 hover:tw-text-slate-600 tw-transition-colors"><i class="fa fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.settings.file-manager.upload') }}" enctype="multipart/form-data" class="tw-p-6">
            @csrf
            <input type="hidden" name="dir" value="{{ $currentDir }}">
            <div class="tw-mb-6">
                <label class="tw-block tw-text-sm tw-font-medium tw-text-slate-700 tw-mb-1">Select file</label>
                <div class="tw-flex tw-justify-center tw-px-6 tw-py-8 tw-border-2 tw-border-slate-300 tw-border-dashed tw-rounded-lg hover:tw-border-indigo-400 tw-transition-colors tw-bg-slate-50 tw-relative">
                    <div class="tw-space-y-1 tw-text-center">
                        <i class="fa fa-file-image-o tw-mx-auto tw-h-12 tw-w-12 tw-text-slate-300"></i>
                        <div class="tw-flex tw-text-sm tw-text-slate-600 tw-justify-center">
                            <span class="tw-relative tw-cursor-pointer tw-bg-white tw-rounded-md tw-font-medium tw-text-indigo-600 hover:tw-text-indigo-500 focus-within:tw-outline-none">
                                <span>Upload a file</span>
                                <input name="file" type="file" required class="tw-absolute tw-inset-0 tw-w-full tw-h-full tw-opacity-0 tw-cursor-pointer">
                            </span>
                            <p class="tw-pl-1">or drag and drop</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tw-flex tw-justify-end tw-gap-3">
                <button type="button" onclick="document.getElementById('upload_modal').style.display='none'" class="tw-px-4 tw-py-2 tw-rounded-lg tw-bg-white tw-border tw-border-slate-300 tw-text-sm tw-font-medium tw-text-slate-700 hover:tw-bg-slate-50 tw-transition-colors">Cancel</button>
                <button type="submit" class="tw-px-4 tw-py-2 tw-rounded-lg tw-bg-indigo-600 tw-text-white tw-text-sm tw-font-medium hover:tw-bg-indigo-500 tw-shadow-sm tw-transition-colors">Upload</button>
            </div>
        </form>
    </div>
</div>

{{-- Rename Modal --}}
<div id="rename_modal" style="display:none;" class="tw-fixed tw-inset-0 tw-z-[9999] tw-bg-slate-900/50 tw-backdrop-blur-sm tw-items-center tw-justify-center tw-p-4">
    <div class="tw-bg-white tw-w-full tw-max-w-md tw-rounded-xl tw-shadow-xl tw-overflow-hidden tw-relative">
        <div class="tw-px-6 tw-py-4 tw-border-b tw-border-slate-200 tw-flex tw-items-center tw-justify-between">
            <h3 class="tw-text-base tw-font-semibold tw-text-slate-900 tw-flex tw-items-center">
                <i class="fa fa-pencil tw-mr-2 tw-text-slate-400"></i> Rename file
            </h3>
            <button onclick="document.getElementById('rename_modal').style.display='none'" class="tw-text-slate-400 hover:tw-text-slate-600 tw-transition-colors"><i class="fa fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.settings.file-manager.rename') }}" class="tw-p-6">
            @csrf
            <input type="hidden" name="dir" value="{{ $currentDir }}">
            <input type="hidden" name="old_name" id="rename_old_name">
            <div class="tw-mb-6">
                <label class="tw-block tw-text-sm tw-font-medium tw-text-slate-700 tw-mb-1">New name</label>
                <input type="text" name="new_name" id="rename_new_name" required class="tw-w-full tw-h-10 tw-px-3 tw-bg-white tw-border tw-border-slate-300 tw-rounded-lg tw-text-sm tw-text-slate-900 focus:tw-ring-1 focus:tw-ring-indigo-500 focus:tw-border-indigo-500 tw-transition-shadow outline-none">
            </div>
            <div class="tw-flex tw-justify-end tw-gap-3">
                <button type="button" onclick="document.getElementById('rename_modal').style.display='none'" class="tw-px-4 tw-py-2 tw-rounded-lg tw-bg-white tw-border tw-border-slate-300 tw-text-sm tw-font-medium tw-text-slate-700 hover:tw-bg-slate-50 tw-transition-colors">Cancel</button>
                <button type="submit" class="tw-px-4 tw-py-2 tw-rounded-lg tw-bg-indigo-600 tw-text-white tw-text-sm tw-font-medium hover:tw-bg-indigo-500 tw-shadow-sm tw-transition-colors">Rename</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRename(name) {
    document.getElementById('rename_old_name').value = name;
    document.getElementById('rename_new_name').value = name;
    document.getElementById('rename_modal').style.display = 'flex';
}
</script>
@endsection
