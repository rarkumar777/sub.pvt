@extends('admin.layouts.app')
@section('title', 'Request Manager')
@section('content')
<style>
.rm-tabs{display:flex;gap:0;margin:0 -20px;border-bottom:2px solid #eee;padding:0 20px;background:#fff;}
.rm-tab{padding:12px 20px;font-size:13px;font-weight:600;color:#888;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;transition:all .2s;}
.rm-tab:hover{color:#f97316}
.rm-tab.active{color:#f97316;border-bottom-color:#f97316;}
.rm-tab .badge{background:#f97316;color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;margin-left:4px;}
.rm-right-actions{margin-left:auto;display:flex;align-items:center;gap:16px;padding:12px 0;}
.rm-right-actions a{font-size:12px;color:#f97316;text-decoration:none;font-weight:600;}
.rm-right-actions a:hover{text-decoration:underline}
.tab-content{padding:20px 0;display:none;}
.tab-content.active{display:block;}
.hello{font-size:28px;font-weight:800;color:#1a1a1a;margin-bottom:24px;}
.inbox-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:30px;}
.inbox-card{background:#fff7ed;border-radius:12px;padding:20px;text-align:center;min-height:200px;display:flex;flex-direction:column;justify-content:space-between;cursor:pointer;transition:all .2s;border:2px solid transparent;}
.inbox-card:hover{border-color:#f97316;box-shadow:0 4px 16px rgba(249,115,22,.12);transform:translateY(-2px);}
.inbox-card.active-filter{border-color:#f97316;background:#fff7ed;}
.inbox-card h4{font-size:14px;font-weight:700;color:#333;text-align:left;}
.inbox-card .ic-empty{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;}
.inbox-card .ic-empty i{font-size:32px;color:#f97316;opacity:.5;}
.inbox-card .ic-empty p{font-size:12px;color:#888;max-width:180px;}
.inbox-card .ic-empty .label{font-size:13px;font-weight:600;color:#555;}
.kanban{display:flex;gap:12px;overflow-x:auto;padding-bottom:12px;}
.kanban-col{flex:1;min-width:180px;background:#f3f4f6;border-radius:10px;padding:12px;min-height:400px;}
.kanban-col h5{font-size:16px;font-weight:700;color:#555;margin-bottom:12px;}
.kanban-card{background:#fff;border-radius:8px;padding:16px;margin-bottom:12px;box-shadow:0 2px 4px rgba(0,0,0,.1);cursor:pointer;transition:all .2s;border-left:3px solid transparent;}
.kanban-card.completed-card{border-left:3px solid transparent;}
.kanban-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.12);transform:translateY(-1px)}
.kanban-card .kc-action{font-size:10px;font-weight:600;color:#f97316;margin-bottom:4px;}
.kanban-card .kc-name{font-size:13px;font-weight:600;color:#333;}
.kanban-card .kc-detail{font-size:11px;color:#888;margin-top:4px;display:flex;flex-direction:column;gap:2px;}
.kanban-card .kc-detail span{display:flex;align-items:center;gap:4px;}
.kanban-card .kc-detail i{font-size:10px;color:#aaa;width:12px;}
.kanban-card.dragging{opacity:.4;transform:rotate(2deg);}
.kanban-col.drag-over{background:#fff7ed!important;border:2px dashed #f97316;}
.req-list{margin-top:20px;}
.req-row{display:flex;align-items:center;gap:16px;padding:12px 16px;border-radius:8px;background:#fff;margin-bottom:8px;border:1px solid #eee;transition:all .2s;cursor:pointer;text-decoration:none;color:inherit;}
.req-row:hover{border-color:#f97316;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.req-row .dot-unread{width:8px;height:8px;border-radius:50%;background:#f97316;flex-shrink:0;}
.req-row .dot-read{width:8px;height:8px;border-radius:50%;background:#ccc;flex-shrink:0;}
.req-row .rr-name{font-size:14px;font-weight:600;color:#333;flex:1;}
.req-row .rr-email{font-size:12px;color:#888;flex:1;}
.req-row .rr-stage{font-size:11px;font-weight:600;padding:4px 10px;border-radius:12px;background:#fff7ed;color:#f97316;}
.req-row .rr-date{font-size:11px;color:#aaa;}
.filters-panel{position:fixed;right:0;top:60px;width:260px;height:calc(100vh - 60px);background:#fff;border-left:1px solid #eee;padding:20px;overflow-y:auto;z-index:100;box-shadow:-4px 0 20px rgba(0,0,0,.06);display:none;}
.filters-panel.open{display:block}
.filters-panel h3{font-size:16px;font-weight:700;color:#f97316;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;}
.filters-panel h3 .close-filters{cursor:pointer;font-size:18px;color:#aaa;}
.filter-section{margin-bottom:16px;}
.filter-section label{font-size:12px;font-weight:600;color:#555;margin-bottom:6px;display:block;}
.filter-section .fc{display:flex;align-items:center;gap:6px;font-size:12px;color:#333;margin-bottom:4px;cursor:pointer;}
.filter-section .fc input{accent-color:#f97316;}
.filter-section select{width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:4px;font-size:12px;}
.todo-empty{text-align:center;padding:60px;color:#888;}
.todo-empty i{font-size:48px;opacity:.3;margin-bottom:12px;display:block;}
.todo-subtab.active{color:#f97316!important;border-bottom-color:#f97316!important;}
.todo-subtab:hover{color:#f97316;}
</style>

<div style="background:#fff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,.06);padding:20px;overflow:hidden;">
    <div class="rm-tabs">
        <div class="rm-tab active" onclick="showTab('inbox',this)">Inbox <span class="badge">{{ $newRequests }}</span></div>
        <div class="rm-tab" onclick="showTab('pipeline',this)">Pipeline</div>
        <div class="rm-tab" onclick="showTab('todo',this)">To Do</div>
        <div class="rm-tab todo-hide" onclick="showTab('sales',this)">Sales</div>
        <div class="rm-tab todo-hide" onclick="showTab('operation',this)">Operation</div>
        <div class="rm-tab todo-hide" onclick="showTab('archive',this)">Archive</div>
        <div class="rm-tab todo-show" onclick="showTodoSub('today',this)" style="display:none;">Today</div>
        <div class="rm-tab todo-show" onclick="showTodoSub('tomorrow',this)" style="display:none;">Tomorrow</div>
        <div class="rm-tab todo-show" onclick="showTodoSub('next5',this)" style="display:none;">Next 5 Days</div>
        <div class="rm-right-actions">
            <a href="javascript:void(0)" onclick="toggleFilters()">Filters</a>
        </div>
    </div>

    <!-- INBOX -->
    <div class="tab-content active" id="inboxTab">
        <div class="hello">Hello {{ $user->name ?? 'Admin' }},</div>
        @php
            // Count important notifications: recent paid invoices in last 7 days
            $importantCount = 0;
            try {
                $importantCount = \App\Models\Invoice::where('status', 'p')
                    ->where('date', '>=', now()->subDays(7)->format('Y-m-d'))
                    ->count();
            } catch(\Exception $e) {
                $importantCount = 0;
            }
        @endphp
        <div class="inbox-cards">
            <div class="inbox-card" onclick="filterRequests('unread')" id="card-unread">
                <h4>Messages awaiting response ({{ $awaitingResponse }})</h4>
                <div class="ic-empty">
                    @if($awaitingResponse == 0)
                    <i class="fa fa-envelope-open-o"></i>
                    <p class="label">All travelers have received a reply</p>
                    <p>Kudos for your quick response!</p>
                    @else
                    <i class="fa fa-envelope"></i>
                    <p class="label">{{ $awaitingResponse }} messages waiting</p>
                    @endif
                </div>
            </div>
            <div class="inbox-card" onclick="filterRequests('new')" id="card-new">
                <h4>New requests ({{ $newRequests }})</h4>
                <div class="ic-empty">
                    @if($newRequests == 0)
                    <i class="fa fa-check-circle"></i>
                    <p class="label">Completed</p>
                    <p>Your travelers are in good hands.</p>
                    @else
                    <i class="fa fa-plus-circle"></i>
                    <p class="label">{{ $newRequests }} new requests</p>
                    @endif
                </div>
            </div>
            <div class="inbox-card" onclick="filterRequests('notifications')" id="card-notif">
                <h4>Important notifications ({{ $importantCount }})</h4>
                <div class="ic-empty">
                    @if($importantCount == 0)
                    <i class="fa fa-bell-o"></i>
                    <p class="label">You are up to date!</p>
                    <p>Important notifications such as traveler payments received will be displayed here.</p>
                    @else
                    <i class="fa fa-bell"></i>
                    <p class="label">{{ $importantCount }} new notification{{ $importantCount > 1 ? 's' : '' }}</p>
                    <p>Traveler payments received</p>
                    @endif
                </div>
            </div>
        </div>
        @if($requests->count() > 0)
        <h4 style="font-size:16px;font-weight:700;margin-bottom:12px;" id="req-list-title">All Requests</h4>
        <div class="req-list" id="reqList">
            @foreach($requests as $req)
            <a class="req-row" href="{{ route('admin.request-manager.show', $req->id) }}" data-stage="{{ $req->pipeline_stage }}" data-read="{{ $req->is_read }}">
                <div class="{{ $req->is_read ? 'dot-read' : 'dot-unread' }}"></div>
                <div class="rr-name">{{ $req->first_name }} {{ $req->last_name }}</div>
                <div class="rr-email">{{ $req->email }}</div>
                <div class="rr-stage">{{ ucwords(str_replace('_', ' ', $req->pipeline_stage)) }}</div>
                <div class="rr-date">{{ $req->created_at->format('M d, Y') }}</div>
            </a>
            @endforeach
        </div>
        @endif
    </div>

    <!-- PIPELINE -->
    <div class="tab-content" id="pipelineTab">
        <div class="kanban" id="kanbanBoard">
            @php
            $salesStages = ['new_request'=>'New Request','discovery'=>'Discovery','itinerary_creation'=>'First Itinerary Creation','fine_tuning'=>'Fine Tuning','validation'=>'Itinerary Validated','postponed'=>'Postponed'];
            @endphp
            @foreach($salesStages as $key => $label)
            <div class="kanban-col" data-stage="{{ $key }}">
                <h5>{{ $label }}</h5>
                @foreach($requests->where('pipeline_stage', $key) as $req)
                <div class="kanban-card" draggable="true" data-id="{{ $req->id }}" data-href="{{ route('admin.request-manager.show', $req->id) }}">
                    <div class="kc-action">Reply to traveler</div>
                    <div class="kc-name">{{ $req->first_name }} {{ $req->last_name }}</div>
                    <div class="kc-detail">
                        <span><i class="fa fa-map-marker"></i> Jordan</span>
                        <span><i class="fa fa-calendar"></i> {{ $req->created_at->format('d M Y') }}</span>
                        <span><i class="fa fa-users"></i> {{ ($req->adults ?? 0) + ($req->children ?? 0) }} PAX</span>
                        @if($req->ideal_budget)<span><i class="fa fa-money"></i> ${{ number_format($req->ideal_budget) }}</span>@endif
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>

    <!-- TO DO -->
    <div class="tab-content" id="todoTab">

        @php
            $today = \Carbon\Carbon::today();
            $tomorrow = \Carbon\Carbon::tomorrow();
            $next5end = \Carbon\Carbon::today()->addDays(5);

            $todoToday = $requests->filter(function($r) use ($today){
                return $r->updated_at->isToday() && !in_array($r->pipeline_stage, ['completed','canceled','lost']);
            });
            $todoTomorrow = $requests->filter(function($r) use ($tomorrow){
                return $r->departure_date && \Carbon\Carbon::parse($r->departure_date)->isTomorrow() && !in_array($r->pipeline_stage, ['completed','canceled','lost']);
            });
            $todoNext5 = $requests->filter(function($r) use ($today, $next5end){
                if(!$r->departure_date) return false;
                $dep = \Carbon\Carbon::parse($r->departure_date);
                return $dep->gte($today) && $dep->lte($next5end) && !in_array($r->pipeline_stage, ['completed','canceled','lost']);
            });
        @endphp

        {{-- TODAY --}}
        <div class="todo-sub-content" id="todoToday">
            <div style="font-size:15px;font-weight:700;color:#1a1a1a;margin-bottom:14px;">{{ $today->format('l j F') }}</div>
            @forelse($todoToday as $req)
            <a href="{{ route('admin.request-manager.show', $req->id) }}" style="display:flex;align-items:center;gap:16px;padding:12px 16px;border-radius:8px;background:#fff;margin-bottom:8px;border:1px solid #eee;text-decoration:none;color:inherit;transition:all .2s;">
                <div style="flex:1;">
                    <div style="font-size:12px;color:#f97316;font-weight:600;">Reply to traveler</div>
                    <div style="font-size:11px;color:#888;margin-top:2px;">{{ ucwords(str_replace('_',' ',$req->pipeline_stage)) }}</div>
                </div>
                <div style="font-size:13px;font-weight:600;color:#333;flex:1;">{{ $req->first_name }} {{ strtoupper($req->last_name) }}</div>
                <div style="font-size:11px;background:#fff7ed;color:#f97316;padding:4px 10px;border-radius:12px;font-weight:600;">{{ $req->created_at->format('d M') }}</div>
            </a>
            @empty
            <div class="todo-empty"><i class="fa fa-check-circle"></i><p style="font-size:14px;font-weight:600;">No tasks for today</p></div>
            @endforelse
        </div>

        {{-- TOMORROW --}}
        <div class="todo-sub-content" id="todoTomorrow" style="display:none;">
            <div style="font-size:15px;font-weight:700;color:#1a1a1a;margin-bottom:14px;">{{ $tomorrow->format('l j F') }}</div>
            @forelse($todoTomorrow as $req)
            <a href="{{ route('admin.request-manager.show', $req->id) }}" style="display:flex;align-items:center;gap:16px;padding:12px 16px;border-radius:8px;background:#fff;margin-bottom:8px;border:1px solid #eee;text-decoration:none;color:inherit;transition:all .2s;">
                <div style="flex:1;">
                    <div style="font-size:12px;color:#f97316;font-weight:600;">Departure tomorrow</div>
                    <div style="font-size:11px;color:#888;margin-top:2px;">{{ ucwords(str_replace('_',' ',$req->pipeline_stage)) }}</div>
                </div>
                <div style="font-size:13px;font-weight:600;color:#333;flex:1;">{{ $req->first_name }} {{ strtoupper($req->last_name) }}</div>
                <div style="font-size:11px;background:#fff7ed;color:#f97316;padding:4px 10px;border-radius:12px;font-weight:600;">{{ \Carbon\Carbon::parse($req->departure_date)->format('d M') }}</div>
            </a>
            @empty
            <div class="todo-empty"><i class="fa fa-check-circle"></i><p style="font-size:14px;font-weight:600;">No tasks for tomorrow</p></div>
            @endforelse
        </div>

        {{-- NEXT 5 DAYS --}}
        <div class="todo-sub-content" id="todoNext5" style="display:none;">
            <div style="font-size:15px;font-weight:700;color:#1a1a1a;margin-bottom:14px;">{{ $today->format('d M') }} – {{ $next5end->format('d M Y') }}</div>
            @forelse($todoNext5 as $req)
            <a href="{{ route('admin.request-manager.show', $req->id) }}" style="display:flex;align-items:center;gap:16px;padding:12px 16px;border-radius:8px;background:#fff;margin-bottom:8px;border:1px solid #eee;text-decoration:none;color:inherit;transition:all .2s;">
                <div style="flex:1;">
                    <div style="font-size:12px;color:#f97316;font-weight:600;">Upcoming departure</div>
                    <div style="font-size:11px;color:#888;margin-top:2px;">{{ ucwords(str_replace('_',' ',$req->pipeline_stage)) }}</div>
                </div>
                <div style="font-size:13px;font-weight:600;color:#333;flex:1;">{{ $req->first_name }} {{ strtoupper($req->last_name) }}</div>
                <div style="font-size:11px;background:#fff7ed;color:#f97316;padding:4px 10px;border-radius:12px;font-weight:600;">{{ \Carbon\Carbon::parse($req->departure_date)->format('d M') }}</div>
            </a>
            @empty
            <div class="todo-empty"><i class="fa fa-check-circle"></i><p style="font-size:14px;font-weight:600;">No upcoming tasks</p></div>
            @endforelse
        </div>
    </div>

    <!-- SALES -->
    <div class="tab-content" id="salesTab">
        <div class="kanban" id="salesKanban">
            @php
            $salesStages2 = ['new_request'=>'New Request','discovery'=>'Discovery','itinerary_creation'=>'First Itinerary Creation','fine_tuning'=>'Fine Tuning','validation'=>'Itinerary Validated','postponed'=>'Postponed'];
            @endphp
            @foreach($salesStages2 as $key => $label)
            <div class="kanban-col" data-stage="{{ $key }}">
                <h5>{{ $label }}</h5>
                @foreach($requests->where('pipeline_stage', $key) as $req)
                <div class="kanban-card" draggable="true" data-id="{{ $req->id }}" data-href="{{ route('admin.request-manager.show', $req->id) }}">
                    <div class="kc-action">Reply to traveler</div>
                    <div class="kc-name">{{ $req->first_name }} {{ $req->last_name }}</div>
                    <div class="kc-detail">
                        <span><i class="fa fa-map-marker"></i> Jordan</span>
                        <span><i class="fa fa-calendar"></i> {{ $req->created_at->format('d M Y') }}</span>
                        <span><i class="fa fa-users"></i> {{ ($req->adults ?? 0) + ($req->children ?? 0) }} PAX</span>
                        @if($req->ideal_budget)<span><i class="fa fa-money"></i> ${{ number_format($req->ideal_budget) }}</span>@endif
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>

    <!-- OPERATION -->
    <div class="tab-content" id="operationTab">
        <div class="kanban">
            @php
            $opStages = ['payment_received'=>'Payment Received','deferred_trip'=>'Deferred Trip','pre_trip'=>'Pre-Trip','trip_in_progress'=>'Trip in Progress','post_trip'=>'Post-Trip'];
            @endphp
            @foreach($opStages as $key => $label)
            <div class="kanban-col">
                <h5>{{ $label }}</h5>
                @foreach($requests->where('pipeline_stage', $key) as $req)
                <div class="kanban-card" onclick="window.location='{{ route('admin.request-manager.show', $req->id) }}'">
                    <div class="kc-action">Reply to traveler</div>
                    <div class="kc-name">{{ $req->first_name }} {{ $req->last_name }}</div>
                    <div class="kc-detail">
                        <span><i class="fa fa-map-marker"></i> Jordan</span>
                        <span><i class="fa fa-calendar"></i> {{ $req->created_at->format('d M Y') }}</span>
                        <span><i class="fa fa-users"></i> {{ ($req->adults ?? 0) + ($req->children ?? 0) }} PAX</span>
                        @if($req->ideal_budget)<span><i class="fa fa-money"></i> ${{ number_format($req->ideal_budget) }}</span>@endif
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>

    <!-- ARCHIVE -->
    <div class="tab-content" id="archiveTab">
        <div class="kanban" id="archiveKanban">
            @php
            $archiveStages = ['completed'=>'Completed','canceled'=>'Cancelled','lost'=>'Lost'];
            @endphp
            @foreach($archiveStages as $key => $label)
            <div class="kanban-col" style="max-width:220px;">
                <h5>{{ $label }}</h5>
                @foreach($requests->where('pipeline_stage', $key) as $req)
                <div class="kanban-card" onclick="window.location='{{ route('admin.request-manager.show', $req->id) }}'">
                    <div class="kc-action">Reply to traveler</div>
                    <div class="kc-name">{{ $req->first_name }} {{ $req->last_name }}</div>
                    <div class="kc-detail">
                        <span><i class="fa fa-map-marker"></i> Jordan</span>
                        <span><i class="fa fa-calendar"></i> {{ $req->created_at->format('d M Y') }}</span>
                        <span><i class="fa fa-users"></i> {{ ($req->adults ?? 0) + ($req->children ?? 0) }} PAX</span>
                        @if($req->ideal_budget)<span><i class="fa fa-money"></i> ${{ number_format($req->ideal_budget) }}</span>@endif
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- FILTERS PANEL -->
<div class="filters-panel" id="filtersPanel">
    <h3>Filters <span class="close-filters" onclick="toggleFilters()">✕</span></h3>
    <div class="filter-section">
        <label>Routing criteria</label>
        <label style="font-weight:700;margin-top:6px;">Agents</label>
        <div class="fc"><input type="checkbox" checked> {{ $user->name ?? 'Admin' }}</div>
    </div>
    <hr style="border:none;border-top:1px solid #eee;margin:12px 0;">
    <div class="filter-section">
        <label>Market</label>
        <select><option>All Markets</option><option>Jordan</option></select>
    </div>
    <hr style="border:none;border-top:1px solid #eee;margin:12px 0;">
    <div class="filter-section">
        <label>Actions</label>
        <label style="font-weight:700;margin-top:6px;">Type of Action</label>
        <select><option>All Actions</option><option>Reply to traveler</option></select>
    </div>
    <div class="filter-section" style="margin-top:8px;">
        <div class="fc"><input type="checkbox"> To Reply</div>
    </div>
</div>

<script>
var currentFilter = null;

function filterRequests(type) {
    var rows = document.querySelectorAll('#reqList .req-row');
    var cards = document.querySelectorAll('.inbox-card');
    var title = document.getElementById('req-list-title');

    // Toggle: if same filter clicked again, clear it
    if (currentFilter === type) {
        currentFilter = null;
        cards.forEach(function(c){ c.classList.remove('active-filter'); });
        rows.forEach(function(r){ r.style.display = ''; });
        if(title) title.textContent = 'All Requests';
        return;
    }

    currentFilter = type;
    cards.forEach(function(c){ c.classList.remove('active-filter'); });

    if (type === 'new') {
        document.getElementById('card-new').classList.add('active-filter');
        if(title) title.textContent = 'New Requests';
        rows.forEach(function(r) {
            r.style.display = (r.dataset.stage === 'new_request') ? '' : 'none';
        });
    } else if (type === 'unread') {
        document.getElementById('card-unread').classList.add('active-filter');
        if(title) title.textContent = 'Unread Messages';
        rows.forEach(function(r) {
            r.style.display = (r.dataset.read === '0') ? '' : 'none';
        });
    } else if (type === 'notifications') {
        document.getElementById('card-notif').classList.add('active-filter');
        if(title) title.textContent = 'Important Notifications';
        // Show all for now (notifications are cross-cutting)
        rows.forEach(function(r){ r.style.display = ''; });
    }
}

function showTab(tab,el){
    document.querySelectorAll('.rm-tab').forEach(function(t){t.classList.remove('active')});
    el.classList.add('active');
    document.querySelectorAll('.tab-content').forEach(function(c){c.classList.remove('active')});
    var map={inbox:'inboxTab',pipeline:'pipelineTab',todo:'todoTab',sales:'salesTab',operation:'operationTab',archive:'archiveTab'};
    document.getElementById(map[tab]).classList.add('active');
    if(tab==='pipeline'){
        document.querySelectorAll('.rm-tab').forEach(function(t){
            if(t.textContent.trim()==='Sales') t.classList.add('active');
        });
    }
    // Toggle todo sub-tabs visibility
    if(tab==='todo'){
        document.querySelectorAll('.todo-hide').forEach(function(t){t.style.display='none';});
        document.querySelectorAll('.todo-show').forEach(function(t){t.style.display='';t.classList.remove('active');});
        // Activate To Do + Today by default
        document.querySelectorAll('.todo-show')[0].classList.add('active');
        document.querySelectorAll('.todo-sub-content').forEach(function(c){c.style.display='none';});
        document.getElementById('todoToday').style.display='block';
    } else {
        document.querySelectorAll('.todo-hide').forEach(function(t){t.style.display='';});
        document.querySelectorAll('.todo-show').forEach(function(t){t.style.display='none';});
    }
    // Clear card filters when switching tabs
    currentFilter = null;
    document.querySelectorAll('.inbox-card').forEach(function(c){ c.classList.remove('active-filter'); });
}
function toggleFilters(){document.getElementById('filtersPanel').classList.toggle('open');}
function showTodoSub(sub,el){
    document.querySelectorAll('.todo-show').forEach(function(t){t.classList.remove('active');});
    el.classList.add('active');
    document.querySelectorAll('.todo-sub-content').forEach(function(c){c.style.display='none';});
    var map={today:'todoToday',tomorrow:'todoTomorrow',next5:'todoNext5'};
    document.getElementById(map[sub]).style.display='block';
}

function archiveRequest(id, stage, btn){
    if(!confirm('Mark this request as '+stage+'?')) return;
    btn.disabled=true;
    btn.innerHTML='<i class="fa fa-spinner fa-spin"></i>';
    fetch('/admin/request-manager/'+id+'/stage',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:JSON.stringify({stage:stage})
    }).then(function(r){
        if(r.ok){window.location.reload();}
        else{btn.disabled=false;btn.innerHTML=stage==='completed'?'<i class="fa fa-check"></i> Complete':'<i class="fa fa-times"></i> Cancel';alert('Failed to update stage');}
    }).catch(function(){btn.disabled=false;alert('Network error');});
}

document.addEventListener('DOMContentLoaded',function(){
    var wasDragged = false;

    // --- CARDS ---
    document.querySelectorAll('.kanban-card[draggable="true"]').forEach(function(card){
        // Start drag
        card.addEventListener('dragstart', function(e){
            wasDragged = true;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', card.dataset.id);
            setTimeout(function(){ card.classList.add('dragging'); }, 0);
        });

        // End drag
        card.addEventListener('dragend', function(){
            card.classList.remove('dragging');
            document.querySelectorAll('.kanban-col').forEach(function(c){
                c.classList.remove('drag-over');
            });
            // Keep wasDragged true for 300ms to block click
            setTimeout(function(){ wasDragged = false; }, 300);
        });

        // Click to navigate (only if NOT dragged)
        card.addEventListener('click', function(e){
            if(wasDragged){
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            if(card.dataset.href){
                window.location = card.dataset.href;
            }
        });
    });

    // --- COLUMNS ---
    document.querySelectorAll('.kanban-col[data-stage]').forEach(function(col){
        col.addEventListener('dragover', function(e){
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            col.classList.add('drag-over');
        });

        col.addEventListener('dragleave', function(e){
            // Only remove if leaving the column itself
            if(!col.contains(e.relatedTarget)){
                col.classList.remove('drag-over');
            }
        });

        col.addEventListener('drop', function(e){
            e.preventDefault();
            e.stopPropagation();
            col.classList.remove('drag-over');
            var id = e.dataTransfer.getData('text/plain');
            if(!id) return;
            // Find the card across ALL kanban boards (pipeline + sales)
            var cards = document.querySelectorAll('.kanban-card[data-id="'+id+'"]');
            cards.forEach(function(card){
                card.classList.remove('dragging');
            });
            // Move the dragged card into this column
            if(cards.length > 0){
                col.appendChild(cards[0]);
                // Save to database
                fetch('/admin/request-manager/'+id+'/stage',{
                    method:'POST',
                    headers:{
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN':'{{ csrf_token() }}'
                    },
                    body:JSON.stringify({stage:col.dataset.stage})
                }).then(function(r){
                    if(r.ok){
                        // Flash orange to confirm
                        cards[0].style.borderLeftColor = '#f97316';
                        setTimeout(function(){ cards[0].style.borderLeftColor = 'transparent'; }, 1000);
                    } else {
                        alert('Failed to update stage');
                    }
                });
            }
        });
    });
});
</script>
@endsection
