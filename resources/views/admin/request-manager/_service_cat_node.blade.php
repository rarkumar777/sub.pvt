{{-- Recursive category tree node (unprefixed Tailwind for trip planner) --}}
{{-- Only renders 2 levels: root categories + their direct children --}}
<div class="category-item mb-1" data-name="{{ strtolower($node->name) }}">
    <div class="flex items-center gap-2 group">
        <div class="flex items-center gap-1.5 p-1.5 rounded-xl hover:bg-white transition-colors w-full">
            @if(($depth ?? 0) < 1 && $node->children && $node->children->count() > 0)
                <button type="button" class="category-toggle w-8 h-8 flex items-center justify-center text-slate-400 hover:text-green-700 transition-colors" onclick="toggleChildren(this);">
                    <i class="fa fa-plus-square-o"></i>
                </button>
            @else
                <div class="w-6"></div>
            @endif
            <label class="flex items-center gap-3 cursor-pointer flex-1">
                <input type="radio" name="tp_category_radio" value="{{ $node->id }}" class="w-4 h-4 text-green-700 focus:ring-green-600 border-slate-300 cursor-pointer">
                <span class="text-sm font-bold text-slate-600 group-hover:text-green-800 transition-colors">{{ html_entity_decode($node->name) }}</span>
            </label>
        </div>
    </div>
    @if(($depth ?? 0) < 1 && $node->children && $node->children->count() > 0)
    <div class="category-children hidden ml-4 pl-4 border-l border-slate-100 mt-1">
        @foreach($node->children as $child)
            @include('admin.request-manager._service_cat_node', ['node' => $child, 'depth' => 1])
        @endforeach
    </div>
    @endif
</div>
