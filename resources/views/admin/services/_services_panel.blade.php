{{-- Category Header --}}
<div class="tw-flex tw-flex-col md:tw-flex-row tw-items-start md:tw-items-center tw-justify-between tw-gap-4 tw-p-6 tw-bg-indigo-50/80 tw-rounded-t-[2rem] tw-border-b tw-border-indigo-100/50">
    <div class="tw-flex tw-items-center tw-gap-3 tw-flex-wrap">
        <div class="tw-w-12 tw-h-12 tw-rounded-2xl tw-bg-white tw-text-indigo-600 tw-flex tw-items-center tw-justify-center tw-shadow-sm">
            <i class="fa fa-cubes tw-text-lg"></i>
        </div>
        <span class="tw-text-[15px] tw-font-black tw-text-indigo-950">
            @if($breadcrumb)<span class="tw-text-indigo-400 tw-font-semibold">{{ $breadcrumb }}</span> <i class="fa fa-angle-right tw-mx-1 tw-text-indigo-300"></i> @endif{{ $category->name }}
        </span>
        <div class="tw-flex tw-gap-1 tw-ml-2">
            <a href="javascript:void(0);" onclick="openEditCategoryModal({{ $category->id }});" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-[0.5rem] tw-bg-white tw-text-indigo-500 hover:tw-bg-indigo-500 hover:tw-text-white tw-border tw-border-indigo-100 tw-transition-all tw-shadow-sm" title="Edit Category"><i class="fa fa-pencil tw-text-[10px]"></i></a>
            <a href="javascript:void(0);" onclick="deleteCategory({{ $category->id }});" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-[0.5rem] tw-bg-white tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white tw-border tw-border-rose-100 tw-transition-all tw-shadow-sm" title="Delete Category"><i class="fa fa-trash tw-text-[10px]"></i></a>
        </div>
    </div>
    <a href="javascript:void(0);" onclick="openAddServiceModal({{ $categoryId }}, {{ $countryId }});" class="btn indigo !tw-px-5 !tw-py-2.5 !tw-text-[13px] !tw-rounded-xl !tw-font-bold">
        <i class="fa fa-plus-circle tw-mr-1"></i> Add Service
    </a>
</div>

{{-- Hotel/Category Details Section --}}
<div class="tw-p-6 tw-bg-white tw-border-x tw-border-b tw-border-slate-100 tw-flex tw-flex-col tw-gap-6">
    <div class="tw-flex tw-flex-col tw-justify-center">
        <h2 class="tw-text-2xl tw-font-black tw-text-slate-800 tw-mb-2 tw-tracking-tight">{{ $category->name }}</h2>
        <div class="tw-flex tw-items-center tw-gap-4 tw-mb-3">
            @if($category->arrival)
            <p class="tw-text-[13px] tw-text-slate-500 tw-font-bold tw-m-0"><i class="fa fa-map-marker tw-mr-1.5 tw-text-indigo-400"></i>{{ $category->arrival }}</p>
            @endif
            @if($category->website)
            <p class="tw-text-[13px] tw-text-slate-500 tw-font-bold tw-m-0"><i class="fa fa-globe tw-mr-1.5 tw-text-indigo-400"></i><a href="{{ str_starts_with($category->website, 'http') ? $category->website : 'https://'.$category->website }}" target="_blank" class="tw-text-indigo-600 hover:tw-text-indigo-800 tw-no-underline hover:tw-underline">{{ $category->website }}</a></p>
            @endif
        </div>
        <div class="tw-text-[14px] tw-text-slate-600 tw-leading-relaxed tw-font-medium tw-mb-2">
            @if($category->description)
                {!! nl2br(htmlspecialchars($category->description)) !!}
            @else
                <p class="tw-text-slate-400 tw-italic tw-text-[13px] tw-m-0">No detailed description available. Click the edit icon to add information.</p>
            @endif
        </div>
    </div>

    <div class="tw-flex tw-flex-wrap tw-gap-4">
        @php
            $images = [];
            if(!empty($category->image)) {
                $decoded = @json_decode($category->image, true);
                if(is_array($decoded) && count($decoded) > 0) {
                    $images = $decoded;
                } else {
                    $images = [$category->image];
                }
            }
        @endphp
        
        @if(count($images) > 0)
            @foreach($images as $img)
                @php $imgUrl = str_starts_with($img, 'http') ? $img : asset(ltrim($img, '/')); @endphp
                <div class="tw-w-48 tw-h-32 tw-rounded-2xl tw-bg-slate-50 tw-overflow-hidden tw-flex-shrink-0 tw-shadow-sm tw-border tw-border-slate-100 tw-relative group">
                    <img src="{{ $imgUrl }}" class="tw-w-full tw-h-full tw-object-cover" alt="Category Image">
                    <div class="tw-absolute tw-inset-0 tw-bg-indigo-900/40 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-opacity tw-flex tw-items-center tw-justify-center cursor-pointer" onclick="openEditCategoryModal({{ $category->id }});" title="Edit details to manage images">
                        <i class="fa fa-pencil tw-text-white tw-text-xl"></i>
                    </div>
                </div>
            @endforeach
        @else
            <div class="tw-w-48 tw-h-32 tw-rounded-2xl tw-bg-slate-50 tw-overflow-hidden tw-flex-shrink-0 tw-shadow-sm tw-border tw-border-slate-100 tw-relative group">
                <div class="tw-w-full tw-h-full tw-flex tw-flex-col tw-items-center tw-justify-center tw-text-slate-300 tw-bg-gradient-to-br tw-from-slate-50 tw-to-slate-100">
                    <i class="fa fa-image tw-text-4xl tw-mb-2"></i>
                    <span class="tw-text-[10px] tw-font-bold tw-uppercase tw-tracking-widest">No Image</span>
                </div>
                <div class="tw-absolute tw-inset-0 tw-bg-indigo-900/40 tw-opacity-0 group-hover:tw-opacity-100 tw-transition-opacity tw-flex tw-items-center tw-justify-center cursor-pointer" onclick="openEditCategoryModal({{ $category->id }});" title="Edit details to add image">
                    <i class="fa fa-pencil tw-text-white tw-text-xl"></i>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Vender filter --}}
<div class="tw-px-6 tw-py-4 tw-bg-white tw-border-x tw-border-slate-100 tw-flex tw-items-center tw-justify-between">
    <span class="tw-text-[11px] tw-font-black tw-text-slate-400 tw-uppercase tw-tracking-widest"><i class="fa fa-list tw-mr-1"></i> Services List</span>
    <div class="tw-relative">
        <i class="fa fa-filter tw-absolute tw-left-3 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400 tw-text-xs"></i>
        <select name="vender" class="get_vender !tw-w-64 !tw-pl-8 !tw-pr-8 !tw-py-2 !tw-text-[13px] !tw-font-semibold !tw-text-slate-700 !tw-rounded-xl tw-border tw-border-slate-200 focus:tw-border-indigo-400 focus:tw-ring focus:tw-ring-indigo-100 tw-bg-slate-50 hover:tw-bg-slate-100 tw-transition-colors tw-cursor-pointer tw-appearance-none">
            <option value="0">All Vendors</option>
            @foreach($venderList as $vid => $vname)
            <option value="{{ $vid }}" {{ $venderId == $vid ? 'selected' : '' }}>{{ $vname }}</option>
            @endforeach
        </select>
        <i class="fa fa-angle-down tw-absolute tw-right-3 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-500 tw-pointer-events-none"></i>
    </div>
</div>

{{-- Services table --}}
<div class="tw-overflow-x-auto tw-bg-white tw-border tw-border-t-0 tw-border-b-0 tw-border-slate-100 tw-rounded-b-[2rem]">
    <table class="tw-w-full tw-text-left tw-border-collapse">
        <thead>
            <tr class="tw-bg-slate-50/80 tw-border-y tw-border-slate-100">
                <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Description</th>
                <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Cost</th>
                <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest">Vendor</th>
                <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-black tw-text-slate-500 tw-uppercase tw-tracking-widest tw-text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="tw-divide-y tw-divide-slate-50">
            @forelse($services as $service)
            <tr class="hover:tw-bg-slate-50/80 tw-transition-colors tw-group">
                <td class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-bold tw-text-slate-700">{{ $service->description }}</td>
                <td class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-black tw-text-emerald-600">{{ number_format($service->cost, 2) }} <span class="tw-text-[10px] tw-text-emerald-500/70 tw-uppercase">JOD</span></td>
                <td class="tw-py-4 tw-px-6 tw-text-[13px] tw-font-semibold tw-text-slate-500">
                    @if($service->venderUser)
                        {{ !empty($service->venderUser->company) ? $service->venderUser->company : $service->venderUser->email }}
                    @else
                        <span class="tw-px-2 tw-py-1 tw-rounded-md tw-bg-slate-100 tw-text-[10px] tw-font-bold tw-text-slate-400">N/A</span>
                    @endif
                </td>
                <td class="tw-py-4 tw-px-6 tw-text-right">
                    <div class="tw-flex tw-items-center tw-justify-end tw-gap-1.5 opacity-80 group-hover:opacity-100 transition-opacity">
                        <a href="javascript:void(0);" class="tw-px-3 tw-py-1.5 tw-rounded-lg tw-bg-amber-50 tw-text-amber-600 hover:tw-bg-amber-500 hover:tw-text-white tw-text-[11px] tw-font-bold tw-transition-all tw-no-underline seasons-btn" data-id="{{ $service->id }}"><i class="fa fa-calendar-o tw-mr-1"></i> Seasons</a>
                        <a href="javascript:void(0);" class="tw-w-7 tw-h-7 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-indigo-50 tw-text-indigo-500 hover:tw-bg-indigo-500 hover:tw-text-white tw-transition-all tw-no-underline edit-service-btn" data-id="{{ $service->id }}"><i class="fa fa-edit tw-text-xs"></i></a>
                        <a href="javascript:void(0);" class="tw-w-7 tw-h-7 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-rose-50 tw-text-rose-500 hover:tw-bg-rose-500 hover:tw-text-white tw-transition-all tw-no-underline del-service-btn" data-id="{{ $service->id }}" data-desc="{{ $service->description }}"><i class="fa fa-trash tw-text-xs"></i></a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="tw-py-16 tw-text-center">
                    <div class="tw-inline-flex tw-items-center tw-justify-center tw-w-16 tw-h-16 tw-rounded-full tw-bg-slate-50 tw-text-slate-300 tw-mb-4"><i class="fa fa-inbox tw-text-2xl"></i></div>
                    <div class="tw-text-slate-400 tw-text-[13px] tw-font-bold tw-uppercase tw-tracking-widest">No services available</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($services->hasPages())
<div class="tw-flex tw-items-center tw-justify-center tw-gap-1A tw-pt-5">
    @if($services->onFirstPage())
        <span class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-slate-50 tw-text-slate-300 tw-text-xs"><i class="fa fa-angle-double-left"></i></span>
    @else
        <a href="{{ $services->currentPage() - 1 }}" data-page="true" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-indigo-50 tw-text-indigo-600 hover:tw-bg-indigo-100 tw-transition-all tw-no-underline tw-text-xs"><i class="fa fa-angle-double-left"></i></a>
    @endif

    @foreach($services->getUrlRange(1, $services->lastPage()) as $page => $url)
        @if($page == $services->currentPage())
            <span class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-indigo-600 tw-text-white tw-text-xs tw-font-bold">{{ $page }}</span>
        @else
            <a href="{{ $page }}" data-page="true" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-white tw-text-slate-600 hover:tw-bg-indigo-50 tw-border tw-border-slate-100 tw-transition-all tw-no-underline tw-text-xs tw-font-bold">{{ $page }}</a>
        @endif
    @endforeach

    @if($services->hasMorePages())
        <a href="{{ $services->currentPage() + 1 }}" data-page="true" class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-indigo-50 tw-text-indigo-600 hover:tw-bg-indigo-100 tw-transition-all tw-no-underline tw-text-xs"><i class="fa fa-angle-double-right"></i></a>
    @else
        <span class="tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-rounded-lg tw-bg-slate-50 tw-text-slate-300 tw-text-xs"><i class="fa fa-angle-double-right"></i></span>
    @endif
</div>
@endif

<div id="ajax"></div>
