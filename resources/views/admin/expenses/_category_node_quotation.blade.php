{{-- Recursive category tree node for quotation expense modal --}}
<div class="modal-category-item" data-name="{{ strtolower($node->name) }}">
    @if($node->children && $node->children->count() > 0)
    <div class="row">
        <span class="modal-category-toggle" onclick="modalToggleChildren(this);" style="cursor:pointer;">
            <i class="fa-plus-square-o"></i>
        </span>
        <label><input type="radio" name="modal_category_parent" value="{{ $node->id }}"> {{ html_entity_decode($node->name) }}</label>
    </div>
    <div class="modal-category-children" style="display:none; padding-left:20px;">
        @foreach($node->children as $child)
            @include('admin.expenses._category_node_quotation', ['node' => $child, 'depth' => $depth + 1])
        @endforeach
    </div>
    @else
    <div class="row">
        <label><input type="radio" name="modal_category_parent" value="{{ $node->id }}"> {{ html_entity_decode($node->name) }}</label>
    </div>
    @endif
</div>
