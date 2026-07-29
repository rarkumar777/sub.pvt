{{-- Modernized recursive category tree node --}}
<div class="category-item tw-mb-1" data-name="{{ strtolower($node->name) }}">
    <div class="tw-flex tw-items-center tw-gap-2 tw-group">
        <div class="tw-flex tw-items-center tw-gap-1.5 tw-p-1.5 tw-rounded-xl hover:tw-bg-white tw-transition-colors tw-w-full">
            @if($node->children && $node->children->count() > 0)
                <button type="button" class="category-toggle tw-w-8 tw-h-8 tw-flex tw-items-center tw-justify-center tw-text-slate-400 hover:tw-text-indigo-600 tw-transition-colors" onclick="toggleChildren(this);">
                    <i class="fa fa-plus-square-o"></i>
                </button>
            @else
                <div class="tw-w-6"></div>
            @endif
            
            <label class="tw-flex tw-items-center tw-gap-3 tw-cursor-pointer tw-flex-1">
                <input type="radio" name="category_parent" value="{{ $node->id }}" class="tw-w-4 tw-h-4 tw-text-indigo-600 focus:tw-ring-indigo-500 tw-border-slate-300">
                <span class="tw-text-sm tw-font-bold tw-text-slate-600 group-hover:tw-text-indigo-700 tw-transition-colors">{{ html_entity_decode($node->name) }}</span>
            </label>
        </div>
    </div>

    @if($node->children && $node->children->count() > 0)
    <div class="category-children tw-hidden tw-ml-4 tw-pl-4 tw-border-l tw-border-slate-100 tw-mt-1">
        @foreach($node->children as $child)
            @include('admin.expenses._category_node', ['node' => $child, 'depth' => ($depth ?? 0) + 1])
        @endforeach
    </div>
    @endif
</div>
