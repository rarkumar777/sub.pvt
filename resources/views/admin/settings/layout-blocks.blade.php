@extends('admin.layouts.app')
@section('title', 'Admin | ' . $name . ' - Blocks')
@section('content')
<div class="breadcrumb pad-t">
    <a class="active"><i class="fa-server"></i> CMS</a>
    <a href="{{ route('admin.settings.layouts') }}" class="green"><i class="fa-sliders"></i> Layouts</a>
    <a class="active">{{ $name }} > <i class="fa-list-alt"></i> Blocks</a>
</div>
<div class="sd-12 relative">
    <h1><i class="fa-list-alt"></i> {{ $name }} (Blocks)</h1>
    <a href="{{ route('admin.customblocks.index') }}" class="btn blue absolute top h-gap right"><i class="fa-th"></i> <span class="hide-sd">Custom Blocks</span></a>
</div>

@if(session('success'))
<div class="row"><div class="sd-12 green-bg white pad">{{ session('success') }}</div></div>
@endif

<style type="text/css">
    body.dragging, body.dragging * { cursor: move !important; }
    .blocks_container, .left_blocks, .right_blocks, .center_top_blocks, .center_bottom_blocks {
        border: 1px dashed #999; padding: 5px; width: 98%; list-style: none; margin: 0; min-height: 100px;
    }
    .left_blocks, .right_blocks { min-height: 300px; }
    .blocks_container { width: 100%; border-color: #333; }
    #trash {
        height: 42px; background: #F4BCBD; border: 1px dashed #999; padding: 0; width: 100%; 
        list-style: none; margin-top: 8px; position: relative;
    }
    #trash ul {
        position: absolute; top: 2px; left: 13px; background: none; cursor: default; 
        padding: 0; margin: 0; border: none; color: #fff; font-size: 12px; font-weight: bold;
    }
    .blocks_container li, .left_blocks li, .right_blocks li, .center_top_blocks li, .center_bottom_blocks li, .trash li {
        cursor: move; width: 100%; height: 35px; padding: 8px; margin-top: 2px; background-color: #eee;
    }
    .dragged { position: absolute; opacity: 0.8; max-width: 350px; border: 1px dashed #999; z-index: 2000; }
</style>

<div class="bordered">
    <div class="row">
        {{-- Left Sidebar --}}
        <div class="md-3" style="padding:10px;">
            <div class="dropdown blue">
                <button class="btn blue small" id="toggler"><i class="fa-plus"></i> Add New</button>
                <ul style="display:none; position:absolute; background:#fff; border:1px solid #ddd; z-index:100; list-style:none; padding:0; margin:0; width:150px;">
                    <li style="padding:5px 10px; border-bottom:1px solid #eee;"><a href="#" onclick="loadBlocks('tours'); return false;">Tours Booking</a></li>
                    <li style="padding:5px 10px; border-bottom:1px solid #eee;"><a href="#" onclick="loadBlocks('media'); return false;">Media Galleries ??</a></li>
                    <li style="padding:5px 10px;"><a href="#" onclick="loadBlocks('custom'); return false;">Custom Blocks</a></li>
                </ul>
            </div>
            <ul id="blocks_container" class="blocks_container" style="margin:10px 0;"></ul>

            <div class="trash" id="trash">
                <ul><i class="fa-trash-o fa-2x"></i> Drop blocks here to delete</ul>
            </div>

            <div class="h-pad gap-t">
                <label for="slider"><strong>Manage slider for this layout</strong></label>
                <select id="slider" class="full-width">
                    <option value="0">Disable</option>
                    @foreach($sliders as $s)
                    <option value="{{ $s->id }}" {{ $sliderId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn green d-gap-t full-width" onclick="saveBlocks('{{ $name }}')"><i class="fa-check"></i> Save</button>
        </div>

        {{-- Right Main Area --}}
        <div class="md-9" style="padding:10px;">
            @if($colType == '3')
            <table width="100%">
                <tr align="center" valign="top">
                    <td width="25%" valign="top">
                        <table width="100%" border="0"><tr><td align="center">Left Side</td></tr>
                        <tr><td align="center"><ul class="left_blocks" id="left_blocks">
                            @foreach($leftSide as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                        </ul></td></tr></table>
                    </td>
                    <td width="50%" valign="top">
                        <table width="100%" border="0">
                            <tr><td align="center">Center top</td></tr>
                            <tr><td align="center"><ul class="center_top_blocks" id="center_top_blocks">
                                @foreach($centerTop as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                            </ul></td></tr>
                            <tr><td align="center" bgcolor="#F6EA83" height="60">Web Site Contents</td></tr>
                            <tr><td align="center">Center bottom blocks</td></tr>
                            <tr><td align="center"><ul class="center_bottom_blocks" id="center_bottom_blocks">
                                @foreach($centerBottom as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                            </ul></td></tr>
                        </table>
                    </td>
                    <td width="25%" valign="top">
                        <table width="100%" border="0"><tr><td align="center">Right Side</td></tr>
                        <tr><td align="center"><ul class="right_blocks" id="right_blocks">
                            @foreach($rightSide as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                        </ul></td></tr></table>
                    </td>
                </tr>
            </table>
            @elseif($colType == '2l')
            <table width="100%">
                <tr align="center" valign="top">
                    <td width="30%" valign="top">
                        <table width="100%" border="0"><tr><td align="center">Left Side</td></tr>
                        <tr><td align="center"><ul class="left_blocks" id="left_blocks">
                            @foreach($leftSide as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                        </ul></td></tr></table>
                    </td>
                    <td width="70%" valign="top">
                        <table width="100%" border="0">
                            <tr><td align="center">Center top</td></tr>
                            <tr><td align="center"><ul class="center_top_blocks" id="center_top_blocks">
                                @foreach($centerTop as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                            </ul></td></tr>
                            <tr><td align="center" bgcolor="#F6EA83" height="120"><h2>Web Site Contents</h2></td></tr>
                            <tr><td align="center">Center bottom blocks</td></tr>
                            <tr><td align="center"><ul class="center_bottom_blocks" id="center_bottom_blocks">
                                @foreach($centerBottom as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                            </ul></td></tr>
                        </table>
                    </td>
                </tr>
            </table>
            <ul class="hidden" id="right_blocks" style="display:none;"></ul>
            @elseif($colType == '2r')
            <table width="100%">
                <tr align="center" valign="top">
                    <td width="70%" valign="top">
                        <table width="100%" border="0">
                            <tr><td align="center">Center top</td></tr>
                            <tr><td align="center"><ul class="center_top_blocks" id="center_top_blocks">
                                @foreach($centerTop as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                            </ul></td></tr>
                            <tr><td align="center" bgcolor="#F6EA83" height="120"><h2>Web Site Contents</h2></td></tr>
                            <tr><td align="center">Center bottom blocks</td></tr>
                            <tr><td align="center"><ul class="center_bottom_blocks" id="center_bottom_blocks">
                                @foreach($centerBottom as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                            </ul></td></tr>
                        </table>
                    </td>
                    <td width="30%" valign="top">
                        <table width="100%" border="0"><tr><td align="center">Right Side</td></tr>
                        <tr><td align="center"><ul class="right_blocks" id="right_blocks">
                            @foreach($rightSide as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                        </ul></td></tr></table>
                    </td>
                </tr>
            </table>
            <ul class="hidden" id="left_blocks" style="display:none;"></ul>
            @else
            <table width="100%">
                <tr align="center" valign="top">
                    <td valign="top">
                        <table width="100%" border="0">
                            <tr><td align="center">Center top</td></tr>
                            <tr><td align="center"><ul class="center_top_blocks" id="center_top_blocks">
                                @foreach($centerTop as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                            </ul></td></tr>
                            <tr><td align="center" bgcolor="#F6EA83" height="120"><h2>Web Site Contents</h2></td></tr>
                            <tr><td align="center">Center bottom blocks</td></tr>
                            <tr><td align="center"><ul class="center_bottom_blocks" id="center_bottom_blocks">
                                @foreach($centerBottom as $k)<li id="{{ $k[0] }}$-${{ $k[1] }}$-${{ $k[2] }}" title="{{ $k[3] ?? '' }}">{{ $k[2] }}</li>@endforeach
                            </ul></td></tr>
                        </table>
                    </td>
                </tr>
            </table>
            <ul class="hidden" id="left_blocks" style="display:none;"></ul>
            <ul class="hidden" id="right_blocks" style="display:none;"></ul>
            @endif
        </div>
    </div>
</div>

<div id="loading"></div>

@push('scripts')
<script src="{{ asset('admin-assets/js/jquery-sortable-min.js') }}"></script>
<script>
$(function() {
    // Source container (cloning)
    $("ul.blocks_container").sortable({
        group: 'blocks',
        drop: false,
        onDrop: function ($item, container, _super) {
            var clonedItem = $('<li/>').css({height: 0});
            $item.before(clonedItem);
            clonedItem.animate({height: $item.height()});
            $item.animate(clonedItem.position(), function () {
                clonedItem.detach();
                _super($item, container);
                clearTrash();
            });
        },
        onDragStart: function ($item, container, _super) {
            if (!container.options.drop) {
                $item.clone().insertAfter($item);
            }
            _super($item, container);
        }
    });

    // Destination containers
    $("ul.left_blocks, ul.center_top_blocks, ul.center_bottom_blocks, ul.right_blocks").sortable({
        group: 'blocks',
        onDrop: function($item, container, _super) {
            _super($item, container);
            clearTrash();
        }
    });

    // Trash container
    $("div.trash").sortable({
        group: 'blocks',
        onDrop: function ($item, container, _super) {
            $item.remove();
            clearTrash();
        }
    });

    // Dropdown toggle
    $(document).on('click', '#toggler', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $dropdown = $(this).closest('.dropdown').find('ul');
        $('.dropdown ul').not($dropdown).hide(); // Hide others
        $dropdown.toggle();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown ul').hide();
        }
    });
});

function loadBlocks(mod) {
    $('.dropdown ul').hide();
    $('#loading').show();
    fetch('{{ route("admin.layouts.blocks.get-blocks") }}?mod=' + mod)
        .then(r => r.text())
        .then(html => {
            document.getElementById('blocks_container').innerHTML = html;
            $('#loading').hide();
        });
}

function clearTrash() {
    document.getElementById('trash').innerHTML = '<ul><i class="fa-trash-o fa-2x"></i> Drop blocks here to delete</ul>';
}

function saveBlocks(layoutName) {
    var data = { layout: layoutName, slider: document.getElementById('slider').value };
    var zones = ['center_top_blocks', 'center_bottom_blocks', 'left_blocks', 'right_blocks'];
    
    zones.forEach(function(id) {
        var el = document.getElementById(id);
        var items = [];
        $(el).find('li').each(function() {
            items.push($(this).attr('id'));
        });
        data[id] = items;
    });

    fetch('{{ route("admin.layouts.blocks.save", $name) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    }).then(r => r.json()).then(d => {
        if(d.success) { alert('Saved!'); location.reload(); }
        else { alert('Error: ' + d.message); }
    }).catch(e => alert('Error: ' + e));
}
</script>
@endpush
@endsection
