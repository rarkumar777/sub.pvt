@extends('admin.layouts.app')

@section('title', 'Admin | Quotations')

@section('content')
<style>
.qt-wrap{background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.06);padding:20px;}
.qt-tabs{display:flex;gap:0;border-bottom:2px solid #eee;margin:0 -20px;padding:0 20px;}
.qt-tab{padding:12px 20px;font-size:13px;font-weight:600;color:#888;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;transition:all .2s;}
.qt-tab:hover{color:#ea580c}
.qt-tab.active{color:#ea580c;border-bottom-color:#ea580c;}
.qt-tab .badge{background:#ea580c;color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;margin-left:4px;}
.qt-right-actions{margin-left:auto;display:flex;align-items:center;gap:12px;padding:12px 0;}
.qt-right-actions a,.qt-right-actions button{font-size:12px;color:#ea580c;text-decoration:none;font-weight:600;background:none;border:none;cursor:pointer;}
.qt-right-actions a:hover{text-decoration:underline}
.qt-tab-content{padding:20px 0;display:none;}
.qt-tab-content.active{display:block;}
.qt-kanban{display:flex;gap:12px;overflow-x:auto;overflow-y:visible;padding-bottom:160px;}
.qt-kanban-col{flex:1;min-width:200px;background:#f3f4f6;border-radius:10px;padding:12px;min-height:400px;overflow:visible;}
.qt-kanban-col h5{font-size:16px;font-weight:700;color:#555;margin:0 0 12px 0;}
.qt-card{background:#fff;border-radius:8px;padding:16px 12px 16px 16px;margin-bottom:12px;box-shadow:0 2px 4px rgba(0,0,0,.1);cursor:pointer;transition:all .2s;border-left:3px solid transparent;position:relative;}
.qt-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.12);transform:translateY(-1px);}
.qt-card.action-open{z-index:9999;}
.qt-card.dragging{opacity:.4;transform:rotate(2deg);}
.qt-kanban-col.drag-over{background:#e6f4f0!important;border:2px dashed #ea580c;}
.qt-card .qc-action{font-size:10px;font-weight:600;color:#e8b445;margin-bottom:4px;}
.qt-card .qc-name{font-size:13px;font-weight:700;color:#333;}
.qt-card .qc-detail{font-size:11px;color:#888;margin-top:6px;display:flex;flex-direction:column;gap:3px;}
.qt-card .qc-detail span{display:flex;align-items:center;gap:5px;}
.qt-card .qc-detail i{font-size:10px;color:#aaa;width:12px;}
.qt-card .qc-price{margin-top:8px;font-size:12px;font-weight:800;color:#ea580c;}
.qt-card .qc-badges{display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;}
.qt-card .qc-badges span{font-size:9px;font-weight:600;padding:2px 6px;border-radius:4px;}
.qt-search{display:flex;gap:8px;align-items:center;margin-bottom:16px;}
.qt-search input{flex:1;padding:8px 14px;border:1px solid #ddd;border-radius:6px;font-size:13px;outline:none;}
.qt-search input:focus{border-color:#ea580c;box-shadow:0 0 0 2px rgba(234,88,12,.1);}
.qt-search button{padding:8px 16px;border-radius:6px;border:none;font-size:12px;font-weight:600;cursor:pointer;}
/* List view */
.qt-list-row{display:flex;align-items:center;gap:16px;padding:12px 16px;border-radius:8px;background:#fff;margin-bottom:8px;border:1px solid #eee;transition:all .2s;cursor:pointer;text-decoration:none;color:inherit;}
.qt-list-row:hover{border-color:#ea580c;box-shadow:0 2px 8px rgba(0,0,0,.06);}
.qt-newbtn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#ea580c;color:#fff;border-radius:6px;border:none;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;}
.qt-newbtn:hover{background:#c2410c;color:#fff;text-decoration:none;}
.qt-action-wrap{position:absolute;right:10px;top:10px;z-index:10000;}
.qt-action-btn{display:inline-flex;align-items:center;gap:4px;height:24px;padding:0 8px;border:0!important;outline:0!important;border-radius:3px;background:#f97316;color:#fff;font-size:11px;font-weight:700;line-height:24px;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.08);}
.qt-action-btn:hover{background:#ea580c;}
.qt-action-btn:focus{outline:0!important;box-shadow:0 0 0 2px rgba(249,115,22,.18);}
.qt-action-menu{position:absolute;right:0;top:28px;display:none;width:168px;background:#fff;border:1px solid #cfcfcf;border-radius:2px;box-shadow:0 4px 12px rgba(0,0,0,.18);overflow:hidden;z-index:10001;}
.qt-action-menu.open{display:block;}
.qt-action-menu a,.qt-action-menu button{width:100%;min-height:34px;display:flex;align-items:center;gap:7px;padding:8px 10px;border:0;border-bottom:1px solid #eee;background:#fff;color:#444;font-size:12px;font-weight:500;line-height:16px;text-decoration:none;text-align:left;cursor:pointer;border-radius:0!important;box-shadow:none!important;}
.qt-action-menu a:last-child,.qt-action-menu button:last-child{border-bottom:0;}
.qt-action-menu a:hover,.qt-action-menu button:hover{background:#f7f7f7;color:#ea580c;text-decoration:none;}
.qt-action-menu .qt-danger{color:#c0392b;}
.qt-action-menu form{margin:0;}
</style>

<div class="qt-wrap">
    {{-- Navigation Tabs (Sub-pages) --}}
    @include('admin.quotations._nav')

    <div style="display:flex;align-items:center;justify-content:space-between;margin:16px 0 20px;">
        <div>
            <h1 style="font-size:22px;font-weight:800;color:#1a1a1a;margin:0;">Quotations</h1>
            <p style="font-size:12px;color:#888;margin:4px 0 0;">Manage and track your tour quotations</p>
        </div>
        @if(auth()->user()->hasPermission('tours_add_quotation'))
        <a href="{{ route('admin.quotations.create') }}" class="qt-newbtn"><i class="fa fa-plus"></i> New Quotation</a>
        @endif
    </div>

    @php
        $draftCount = $quotations->where('status','draft')->count();
        $sentCount = $quotations->where('status','sent')->count();
        $acceptedCount = $quotations->where('status','accepted')->count();
        $rejectedCount = $quotations->where('status','rejected')->count();
    @endphp

    <div class="qt-tabs">
        <div class="qt-tab active" onclick="showQtTab('pipeline',this)">Pipeline <span class="badge">{{ $quotations->count() }}</span></div>
        <div class="qt-tab" onclick="showQtTab('list',this)">List View</div>
        <div class="qt-tab" onclick="showQtTab('archive',this)">Archive</div>
        <div class="qt-right-actions">
            <form method="get" action="{{ route('admin.quotations.index') }}" style="display:flex;gap:6px;margin:0;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search quotations..." style="padding:6px 12px;border:1px solid #ddd;border-radius:4px;font-size:12px;width:180px;">
                <button type="submit" style="padding:6px 10px;background:#f5f5f5;border:1px solid #ddd;border-radius:4px;cursor:pointer;font-size:11px;"><i class="fa fa-search"></i></button>
                @if(request('search'))
                <a href="{{ route('admin.quotations.index') }}" style="padding:6px 10px;background:#fee;border:1px solid #fcc;border-radius:4px;font-size:11px;color:#c33;"><i class="fa fa-times"></i></a>
                @endif
            </form>
        </div>
    </div>

    {{-- PIPELINE TAB --}}
    <div class="qt-tab-content active" id="qtPipelineTab">
        <div class="qt-kanban" id="qtKanbanBoard">
            @php
            $stages = [
                'draft' => ['label'=>'Draft','color'=>'#f59e0b','bg'=>'#fffbeb'],
                'sent' => ['label'=>'Sent','color'=>'#3b82f6','bg'=>'#eff6ff'],
                'accepted' => ['label'=>'Accepted','color'=>'#10b981','bg'=>'#ecfdf5'],
                'rejected' => ['label'=>'Rejected','color'=>'#ef4444','bg'=>'#fef2f2'],
            ];
            @endphp
            @foreach($stages as $stKey => $stInfo)
            <div class="qt-kanban-col" data-status="{{ $stKey }}">
                <h5 style="display:flex;align-items:center;gap:8px;">
                    <span style="width:10px;height:10px;border-radius:50%;background:{{ $stInfo['color'] }};display:inline-block;"></span>
                    {{ $stInfo['label'] }}
                    <span style="font-size:11px;color:#aaa;font-weight:400;">({{ $quotations->where('status',$stKey)->count() }})</span>
                </h5>
                @foreach($quotations->where('status', $stKey) as $q)
                @php
                    $perPerson = $q->travelers_number > 0 ? $q->total / $q->travelers_number : 0;
                    $linkedBk = \App\Models\TourBooking::where('quotation_id', $q->id)->first();
                @endphp
                <div class="qt-card" draggable="true" data-id="{{ $q->id }}" data-href="{{ route('admin.quotations.edit', $q->id) }}">
                    <div class="qt-action-wrap" onclick="event.stopPropagation();" draggable="false">
                        <button type="button" class="qt-action-btn" onclick="toggleQuotationActions(event, {{ $q->id }})">Action <i class="fa fa-caret-down"></i></button>
                        <div class="qt-action-menu" id="qt_action_menu_{{ $q->id }}">
                            <a href="{{ route('admin.quotations.edit', $q->id) }}"><i class="fa fa-edit"></i> edit</a>
                            <button type="button" onclick="openSendModal({{ $q->id }}); closeQuotationActions();"><i class="fa fa-envelope"></i> Send</button>
                            <a href="{{ route('admin.quotations.copy', $q->id) }}"><i class="fa fa-copy"></i> Copy</a>
                            @if(!$linkedBk)
                            <form method="POST" action="{{ route('admin.quotations.validate', $q->id) }}" onsubmit="return confirm('This will create a Booking and Invoice from this quotation. Continue?');">
                                @csrf
                                <button type="submit"><i class="fa fa-undo"></i> Convert To Booking</button>
                            </form>
                            @endif
                            <a href="/{{ $q->lang ?: 'en' }}/tours/quotation/{{ $q->id }}/" target="_blank"><i class="fa fa-eye"></i> View</a>
                            <form method="POST" action="{{ route('admin.quotations.destroy', $q->id) }}" onsubmit="return confirm('Delete this quotation?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="qt-danger"><i class="fa fa-trash"></i> delete</button>
                            </form>
                        </div>
                    </div>
                    <div class="qc-action">
                        @if($linkedBk)
                            <span style="color:#10b981;">✓ Booked #{{ $linkedBk->id }}</span>
                        @else
                            Review quotation
                        @endif
                    </div>
                    <div class="qc-name">{{ $q->customer_name }}</div>
                    <div class="qc-detail">
                        <span><i class="fa fa-map-marker"></i> {{ $q->description ?: 'Jordan' }}</span>
                        <span><i class="fa fa-calendar"></i> {{ $q->travel_date }}</span>
                        <span><i class="fa fa-users"></i> {{ $q->travelers_number }} PAX</span>
                        @if($q->total > 0)<span><i class="fa fa-money"></i> ${{ number_format($q->total, 2) }}</span>@endif
                    </div>
                    <div class="qc-badges">
                        <span style="background:#ecfdf5;color:#059669;">{{ $q->days }}D / {{ $q->nights }}N</span>
                        <span style="background:#eff6ff;color:#2563eb;">#{{ $q->ref_number }}</span>
                        <span style="background:#f3e8ff;color:#7c3aed;">{{ strtoupper($q->lang) }}</span>
                    </div>
                    @if($q->total > 0)
                    <div class="qc-price">${{ number_format($q->total, 2) }} <span style="font-weight:400;color:#aaa;font-size:10px;">/ ${{ number_format($perPerson, 2) }} pp</span></div>
                    @else
                    <div class="qc-price" style="color:#f59e0b;">⚠ Calculate pricing</div>
                    @endif
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>

    {{-- LIST VIEW TAB --}}
    <div class="qt-tab-content" id="qtListTab">
        @forelse($quotations->whereNotIn('status',['rejected']) as $q)
        @php
            $perPerson = $q->travelers_number > 0 ? $q->total / $q->travelers_number : 0;
            $stColors = ['draft'=>'background:#fffbeb;color:#b45309;','sent'=>'background:#eff6ff;color:#1d4ed8;','accepted'=>'background:#ecfdf5;color:#047857;','rejected'=>'background:#fef2f2;color:#b91c1c;'];
            $addedBy = $q->addedByUser;
        @endphp
        <a class="qt-list-row" href="{{ route('admin.quotations.edit', $q->id) }}">
            <div style="width:8px;height:8px;border-radius:50;background:{{ $q->total > 0 ? '#10b981' : '#f59e0b' }};flex-shrink:0;"></div>
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:700;color:#333;">{{ $q->customer_name }}</div>
                <div style="font-size:11px;color:#888;">{{ $q->email }}</div>
            </div>
            <div style="flex:1;">
                <div style="font-size:12px;color:#555;">{{ $q->description ?: '-' }}</div>
                <div style="font-size:10px;color:#aaa;margin-top:2px;">{{ $q->travel_date }} · {{ $q->days }}D/{{ $q->nights }}N · #{{ $q->ref_number }}</div>
            </div>
            <div style="width:100px;text-align:right;">
                @if($q->total > 0)
                <div style="font-size:13px;font-weight:700;color:#ea580c;">${{ number_format($q->total, 2) }}</div>
                <div style="font-size:10px;color:#888;">${{ number_format($perPerson, 2) }}/pp</div>
                @else
                <span style="font-size:10px;font-weight:600;padding:3px 8px;border-radius:4px;background:#fffbeb;color:#b45309;">Calculate</span>
                @endif
            </div>
            <div style="width:70px;text-align:center;">
                <span style="font-size:10px;font-weight:600;padding:3px 8px;border-radius:4px;{{ $stColors[$q->status] ?? $stColors['draft'] }}">{{ ucfirst($q->status) }}</span>
            </div>
            <div style="width:60px;text-align:right;">
                <span style="font-size:10px;color:#888;">{{ $addedBy ? $addedBy->first_name : 'System' }}</span>
            </div>
        </a>
        @empty
        <div style="text-align:center;padding:60px;color:#888;">
            <i class="fa fa-folder-open-o" style="font-size:48px;opacity:.3;display:block;margin-bottom:12px;"></i>
            <p style="font-size:14px;font-weight:600;">No quotations found</p>
        </div>
        @endforelse
    </div>

    {{-- ARCHIVE TAB --}}
    <div class="qt-tab-content" id="qtArchiveTab" style="background:#3f464d;margin:-20px;padding:20px;border-radius:0 0 12px 12px;min-height:50vh;">
        <div class="qt-kanban">
            <div class="qt-kanban-col" style="background:transparent;padding:0;">
                <h5 style="color:#fff;font-size:18px;">Accepted</h5>
                @foreach($quotations->where('status','accepted') as $q)
                <div class="qt-card" onclick="window.location='{{ route('admin.quotations.edit', $q->id) }}'">
                    <div class="qc-name" style="display:flex;align-items:center;gap:6px;">
                        <span>✅</span> <strong>{{ $q->customer_name }}</strong>
                    </div>
                    <div class="qc-detail" style="margin-top:8px;">
                        <span><i class="fa fa-calendar"></i> {{ $q->travel_date }}</span>
                        <span><i class="fa fa-users"></i> {{ $q->travelers_number }} PAX</span>
                        <span><i class="fa fa-money"></i> ${{ number_format($q->total, 2) }}</span>
                    </div>
                </div>
                @endforeach
                @if($quotations->where('status','accepted')->count() == 0)
                <div style="color:#999;font-size:12px;padding:20px;text-align:center;">No accepted quotations yet</div>
                @endif
            </div>
            <div class="qt-kanban-col" style="background:transparent;padding:0;">
                <h5 style="color:#fff;font-size:18px;">Rejected</h5>
                @foreach($quotations->where('status','rejected') as $q)
                <div class="qt-card" onclick="window.location='{{ route('admin.quotations.edit', $q->id) }}'">
                    <div class="qc-name" style="display:flex;align-items:center;gap:6px;">
                        <span>❌</span> <strong>{{ $q->customer_name }}</strong>
                    </div>
                    <div class="qc-detail" style="margin-top:8px;">
                        <span><i class="fa fa-calendar"></i> {{ $q->travel_date }}</span>
                        <span><i class="fa fa-users"></i> {{ $q->travelers_number }} PAX</span>
                    </div>
                </div>
                @endforeach
                @if($quotations->where('status','rejected')->count() == 0)
                <div style="color:#999;font-size:12px;padding:20px;text-align:center;">No rejected quotations yet</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div id="ajax"></div>

{{-- Send Notifications Modal --}}
<div class="modal" id="send_quotation">
    <div class="tw-w-full tw-max-w-3xl !tw-p-0 !tw-rounded-xl tw-bg-white tw-shadow-sm tw-ring-1 tw-ring-gray-950/5 tw-overflow-hidden">
        <div class="tw-flex tw-items-center tw-justify-between tw-px-6 tw-py-4 tw-border-b tw-border-gray-200">
            <h3 class="tw-text-base tw-font-semibold tw-text-gray-950 tw-m-0">Send Quotation Notification</h3>
            <a href="#close" class="tw-text-gray-400 hover:tw-text-gray-500 tw-transition-colors tw-no-underline"><i class="fa fa-times tw-text-lg"></i></a>
        </div>
        <div id="send-modal-content" class="tw-p-6">
            <div class="tw-flex tw-flex-col tw-items-center tw-py-12 tw-gap-4">
                <i class="fa fa-circle-o-notch fa-spin tw-text-2xl tw-text-primary-600"></i>
                <span class="tw-text-sm tw-text-gray-500">Preparing Template...</span>
            </div>
        </div>
    </div>
</div>

<script>
function showQtTab(tab,el){
    document.querySelectorAll('.qt-tab').forEach(function(t){t.classList.remove('active')});
    el.classList.add('active');
    document.querySelectorAll('.qt-tab-content').forEach(function(c){c.classList.remove('active')});
    var map={pipeline:'qtPipelineTab',list:'qtListTab',archive:'qtArchiveTab'};
    document.getElementById(map[tab]).classList.add('active');
}

function toggleQuotationActions(event, id) {
    event.preventDefault();
    event.stopPropagation();
    var menu = document.getElementById('qt_action_menu_' + id);
    var shouldOpen = menu && !menu.classList.contains('open');
    closeQuotationActions();
    if (menu && shouldOpen) {
        menu.classList.add('open');
        var card = menu.closest('.qt-card');
        if (card) {
            card.classList.add('action-open');
        }
    }
}

function closeQuotationActions() {
    document.querySelectorAll('.qt-action-menu.open').forEach(function(menu) {
        menu.classList.remove('open');
    });
    document.querySelectorAll('.qt-card.action-open').forEach(function(card) {
        card.classList.remove('action-open');
    });
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.qt-action-wrap')) {
        closeQuotationActions();
    }
});

document.addEventListener('submit', function(e) {
    if (e.target.closest('.qt-action-menu')) {
        e.stopPropagation();
    }
}, true);

// Drag & Drop for Kanban
document.addEventListener('DOMContentLoaded',function(){
    var wasDragged = false;

    document.querySelectorAll('.qt-card[draggable="true"]').forEach(function(card){
        card.addEventListener('dragstart', function(e){
            wasDragged = true;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', card.dataset.id);
            setTimeout(function(){ card.classList.add('dragging'); }, 0);
        });
        card.addEventListener('dragend', function(){
            card.classList.remove('dragging');
            document.querySelectorAll('.qt-kanban-col').forEach(function(c){ c.classList.remove('drag-over'); });
            setTimeout(function(){ wasDragged = false; }, 300);
        });
        card.addEventListener('click', function(e){
            if(wasDragged){ e.preventDefault(); return; }
            if(e.target.closest('.qt-action-wrap')){ return; }
            if(card.dataset.href) window.location = card.dataset.href;
        });
    });

    document.querySelectorAll('.qt-kanban-col[data-status]').forEach(function(col){
        col.addEventListener('dragover', function(e){
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            col.classList.add('drag-over');
        });
        col.addEventListener('dragleave', function(e){
            if(!col.contains(e.relatedTarget)) col.classList.remove('drag-over');
        });
        col.addEventListener('drop', function(e){
            e.preventDefault();
            col.classList.remove('drag-over');
            var id = e.dataTransfer.getData('text/plain');
            if(!id) return;
            var card = document.querySelector('.qt-card[data-id="'+id+'"]');
            if(card){
                card.classList.remove('dragging');
                col.appendChild(card);
                // Update status via AJAX
                fetch('/admin/quotations/'+id+'/update-status',{
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                    body:JSON.stringify({status:col.dataset.status})
                }).then(function(r){
                    if(r.ok){
                        card.style.borderLeftColor = '#10b981';
                        setTimeout(function(){ card.style.borderLeftColor = 'transparent'; }, 1500);
                    }
                });
            }
        });
    });
});

function openSendModal(qId) {
    window.location.hash = 'send_quotation';
    document.getElementById('send-modal-content').innerHTML = '<div class="tw-flex tw-flex-col tw-items-center tw-py-12 tw-gap-4"><i class="fa fa-circle-o-notch fa-spin tw-text-2xl"></i><span class="tw-text-sm tw-text-gray-500">Loading...</span></div>';
    fetch('/admin/quotations/' + qId + '/send-modal')
        .then(function(r) { return r.json(); })
        .then(function(data) { document.getElementById('send-modal-content').innerHTML = data.html; })
        .catch(function(err) { document.getElementById('send-modal-content').innerHTML = '<div style="text-align:center;padding:20px;color:red;">Error loading data</div>'; });
}

function sendQuotationMail(qId) {
    var subject = document.getElementById('send_subject').value;
    var qmsg = document.getElementById('mail_msg').innerHTML;
    if (!subject) { alert('Please enter a subject'); return; }
    var formData = new FormData();
    formData.append('subject', subject);
    formData.append('qmsg', qmsg);
    formData.append('_token', '{{ csrf_token() }}');
    fetch('/admin/quotations/' + qId + '/send', { method: 'POST', body: formData })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            document.getElementById('send-modal-content').innerHTML = '<div style="text-align:center;padding:40px;"><div style="width:48px;height:48px;border-radius:50%;background:#ecfdf5;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;"><i class="fa fa-check" style="color:#10b981;font-size:20px;"></i></div><h3 style="font-size:16px;font-weight:700;margin:0 0 8px;">Sent Successfully</h3><p style="color:#888;font-size:13px;">'+data.message+'</p><a href="#close" style="display:inline-block;margin-top:12px;padding:8px 20px;background:#ea580c;color:#fff;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">Close</a></div>';
        } else { alert(data.message || 'Error'); }
    })
    .catch(function(err) { alert('Error: ' + err.message); });
}
</script>
@endsection
