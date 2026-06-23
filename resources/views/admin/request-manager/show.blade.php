@extends('admin.layouts.app')
@section('title', 'Request #' . $tripRequest->id)
@section('content')
<style>
*{box-sizing:border-box;}
.rm-detail{display:flex;gap:0;min-height:calc(100vh - 140px);margin:-20px;background:#fff;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;}
.rm-left{flex:0 0 48%;max-width:48%;border-right:1px solid #e0e0e0;overflow-y:auto;max-height:calc(100vh - 140px);background:#fff;}
.rm-right{flex:1;display:flex;flex-direction:column;overflow:hidden;max-height:calc(100vh - 140px);background:#fff;}
.rm-topbar{display:flex;align-items:center;gap:0;padding:0 16px;border-bottom:1px solid #e0e0e0;background:#fff;height:48px;}
.rm-topbar a.tb-link{color:#333;text-decoration:none;font-size:13px;font-weight:500;display:flex;align-items:center;gap:5px;padding:12px 14px;border-bottom:2px solid transparent;height:100%;transition:all .15s;}
.rm-topbar a.tb-link:hover{color:#f97316}
.rm-topbar a.tb-link.active{color:#f97316;border-bottom-color:#f97316;font-weight:600;}
.rm-topbar a.tb-back{color:#f97316;text-decoration:none;font-size:13px;font-weight:600;display:flex;align-items:center;gap:4px;padding:12px 14px 12px 0;margin-right:8px;}
.rm-topbar a.tb-back:hover{text-decoration:underline}
.rm-topbar .tb-more{color:#999;cursor:pointer;padding:0 8px;font-size:16px;}
.rm-topbar .tb-actions{margin-left:auto;display:flex;align-items:center;gap:12px;}
.rm-topbar .tb-actions a.tb-attach{color:#333;font-size:13px;font-weight:500;text-decoration:none;display:flex;align-items:center;gap:5px;}
.rm-topbar .tb-actions a.tb-attach:hover{color:#f97316}
.rm-topbar .btn-call{background:#f97316;color:#fff;padding:7px 18px;border-radius:4px;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:5px;border:none;cursor:pointer;}
.rm-topbar .btn-call:hover{background:#ea580c}
.rm-topbar .btn-call-dd{background:#ea580c;color:#fff;padding:7px 8px;border-radius:0 4px 4px 0;border:none;border-left:1px solid rgba(255,255,255,.3);cursor:pointer;font-size:10px;}
.rm-info{padding:24px 28px;}
.traveler-name{font-size:18px;font-weight:700;color:#1a1a1a;margin-bottom:20px;display:flex;align-items:center;gap:8px;}
.traveler-name .dot{width:10px;height:10px;border-radius:50%;background:#e8b445;flex-shrink:0;}
.info-row{display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;font-size:13px;line-height:1.4;}
.info-row .ir-icon{color:#999;width:16px;text-align:center;margin-top:1px;font-size:12px;}
.info-row .ir-label{color:#888;min-width:110px;font-weight:400;}
.info-row .ir-value{color:#1a1a1a;font-weight:500;flex:1;}
select.stage-select{padding:3px 10px;border:1px solid #ccc;border-radius:3px;font-size:12px;font-weight:500;background:#fff;cursor:pointer;color:#333;outline:none;appearance:auto;}
select.stage-select:focus{border-color:#f97316}
.stage-dot{display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:4px;}
.stage-dot-green{background:#f97316;}
.stage-dot-yellow{background:#e8b445;}
.next-action-badge{color:#e8b445;font-weight:600;font-size:12px;padding:3px 0;}
.info-section{margin-top:20px;border-top:1px solid #eee;padding-top:18px;}
.info-section h4{font-size:12px;font-weight:700;color:#333;margin-bottom:14px;text-transform:uppercase;letter-spacing:.8px;}
.chat-area{flex:1;overflow-y:auto;padding:20px 24px;display:flex;flex-direction:column;gap:8px;background:#fff;}
.chat-date{text-align:center;font-size:11px;color:#999;margin:12px 0 4px;font-weight:500;}
.msg{max-width:80%;padding:12px 16px;font-size:13px;line-height:1.6;position:relative;}
.msg-traveler{background:#fff;border:1px solid #e8e8e8;align-self:flex-start;border-radius:2px 12px 12px 12px;}
.msg-agent{background:#fff7ed;align-self:flex-end;border-radius:12px 2px 12px 12px;border:none;}
.msg .msg-meta{font-size:11px;color:#999;margin-top:6px;display:flex;align-items:center;gap:4px;justify-content:flex-end;}
.msg .msg-meta .sender{font-weight:500;color:#666;}
.msg .msg-meta .read-receipt{color:#f97316;font-size:10px;}
.chat-composer{border-top:1px solid #e0e0e0;padding:0;background:#fff;}
.composer-toolbar{display:flex;align-items:center;gap:2px;padding:8px 16px 0;border-bottom:1px solid #f0f0f0;}
.composer-toolbar button{background:none;border:none;font-size:14px;color:#666;cursor:pointer;padding:6px 8px;border-radius:3px;font-weight:600;}
.composer-toolbar button:hover{background:#f5f5f5;color:#333}
.composer-text{padding:8px 16px;}
.composer-text textarea{width:100%;padding:8px 0;border:none;font-size:13px;resize:none;min-height:36px;max-height:100px;font-family:inherit;outline:none;color:#333;}
.composer-bottom{display:flex;align-items:center;justify-content:space-between;padding:8px 16px;border-top:1px solid #f0f0f0;}
.composer-bottom-left{display:flex;gap:12px;}
.composer-bottom-left button{background:none;border:none;font-size:16px;color:#bbb;cursor:pointer;padding:4px;}
.composer-bottom-left button:hover{color:#666}
.composer-bottom-right{display:flex;align-items:center;gap:8px;}
.btn-send{background:none;border:1px solid #ddd;color:#bbb;padding:6px 16px;border-radius:4px;font-size:12px;font-weight:600;cursor:pointer;}
.btn-send:hover,.btn-send.has-text{background:#f97316;color:#fff;border-color:#f97316}
.editable{cursor:pointer;border-bottom:1px dashed transparent;transition:all .15s;padding:2px 4px;margin:-2px -4px;border-radius:3px;}
.editable:hover{background:#fff7ed;border-bottom-color:#f97316;}
.edit-inline{display:none;align-items:center;gap:6px;}
.edit-inline.active{display:flex;}
.edit-inline input,.edit-inline textarea,.edit-inline select{border:1px solid #f97316;border-radius:4px;padding:4px 8px;font-size:13px;font-family:inherit;outline:none;color:#333;}
.edit-inline input:focus,.edit-inline textarea:focus,.edit-inline select:focus{box-shadow:0 0 0 2px rgba(249,115,22,.15);}
.edit-inline .edit-save{background:#f97316;color:#fff;border:none;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:600;cursor:pointer;}
.edit-inline .edit-save:hover{background:#ea580c;}
.edit-inline .edit-cancel{background:none;border:1px solid #ddd;color:#888;padding:4px 10px;border-radius:4px;font-size:11px;font-weight:600;cursor:pointer;}
.edit-inline .edit-cancel:hover{background:#f5f5f5;}
.ir-value .display-val{display:inline;}
</style>

<div class="rm-detail">
    <div class="rm-left">
        <div class="rm-topbar">
            <a href="{{ route('admin.request-manager') }}" class="tb-back"><i class="fa fa-arrow-left"></i> Back</a>
            <a href="javascript:void(0)" onclick="showDetailTab('discussion')" class="tb-link detail-tab active" data-tab="discussion"><i class="fa fa-comments-o"></i> Discussion</a>
            <a href="/admin/request-manager/{{ $tripRequest->id }}/trip-planner" class="tb-link"><i class="fa fa-plane"></i> Itinerary</a>
            <a href="javascript:void(0)" onclick="showDetailTab('contract')" class="tb-link detail-tab" data-tab="contract"><i class="fa fa-eur"></i> Contract</a>
            <a href="javascript:void(0)" onclick="showDetailTab('booking')" class="tb-link detail-tab" data-tab="booking"><i class="fa fa-calendar-check-o"></i> Booking</a>
            <span class="tb-more"><i class="fa fa-ellipsis-v"></i></span>
        </div>
        <div class="rm-info">
            <div class="traveler-name" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <span class="dot"></span>
                <span class="display-val editable" id="disp-traveler-name" onclick="toggleNameEdit()" style="cursor:pointer;">{{ $tripRequest->first_name }} {{ strtoupper($tripRequest->last_name ?? '') }}</span>
                <span id="edit-traveler-name" style="display:none; align-items:center; gap:6px; flex-wrap:wrap;">
                    <input type="text" id="fld-first_name" value="{{ $tripRequest->first_name }}" style="width:120px; padding:4px 8px; border:1px solid #e2e8f0; border-radius:6px; font-size:14px;" placeholder="First name">
                    <input type="text" id="fld-last_name" value="{{ $tripRequest->last_name }}" style="width:120px; padding:4px 8px; border:1px solid #e2e8f0; border-radius:6px; font-size:14px;" placeholder="Last name">
                    <button class="edit-save" onclick="saveFullName()">✓</button>
                    <button class="edit-cancel" onclick="cancelNameEdit()">✕</button>
                </span>
            </div>

            <div class="info-row">
                <span class="ir-icon"><i class="fa fa-bar-chart"></i></span>
                <span class="ir-label">Sales Stage</span>
                <span class="ir-value">
                    <select class="stage-select" onchange="updateStage(this.value)">
                        @php
                        $allStages = [
                            'new_request'=>'New Request','discovery'=>'Discovery','itinerary_creation'=>'First Itinerary',
                            'fine_tuning'=>'Fine Tuning','validation'=>'Itinerary Validated','postponed'=>'⏸ Postponed',
                            'payment_received'=>'Payment Received',
                            'pre_trip'=>'Pre-Trip','trip_in_progress'=>'Trip in Progress','post_trip'=>'Post-Trip',
                            'completed'=>'✔ Completed','canceled'=>'✖ Canceled','lost'=>'✖ Lost'
                        ];
                        @endphp
                        @foreach($allStages as $stKey => $stLabel)
                        <option value="{{ $stKey }}" {{ $tripRequest->pipeline_stage==$stKey?'selected':'' }}>• {{ $stLabel }}</option>
                        @endforeach
                    </select>
                </span>
            </div>
            <div class="info-row">
                <span class="ir-icon"><i class="fa fa-bolt"></i></span>
                <span class="ir-label">Next action</span>
                <span class="ir-value">
                    <span class="next-action-badge">Reply to traveler</span>
                    <span style="margin-left:8px;font-size:12px;color:#888;font-weight:400;"><i class="fa fa-calendar-o"></i> {{ $tripRequest->updated_at->format('d M Y') }}</span>
                </span>
            </div>
            <div class="info-row">
                <span class="ir-icon"><i class="fa fa-money"></i></span>
                <span class="ir-label">Budget</span>
                <span class="ir-value">
                    <span class="display-val editable" onclick="toggleEdit('budget')">${{ number_format($tripRequest->ideal_budget ?? 0) }}{{ $tripRequest->max_budget ? ' - $'.number_format($tripRequest->max_budget) : '' }}</span>
                    <span class="edit-inline" id="edit-budget">
                        <input type="number" id="fld-ideal_budget" value="{{ $tripRequest->ideal_budget ?? 0 }}" style="width:80px;" placeholder="Min">
                        <span>-</span>
                        <input type="number" id="fld-max_budget" value="{{ $tripRequest->max_budget ?? 0 }}" style="width:80px;" placeholder="Max">
                        <button class="edit-save" onclick="saveBudget()">✓</button>
                        <button class="edit-cancel" onclick="cancelEdit('budget')">✕</button>
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="ir-icon"><i class="fa fa-sticky-note-o"></i></span>
                <span class="ir-label">Notes</span>
                <span class="ir-value">
                    <span class="display-val editable" onclick="toggleEdit('notes')" style="color:#999;font-weight:400;">{{ $tripRequest->notes ?: '(empty)' }}</span>
                    <span class="edit-inline" id="edit-notes" style="flex-direction:column;align-items:stretch;">
                        <textarea id="fld-notes" rows="3" style="width:100%;min-width:250px;">{{ $tripRequest->notes }}</textarea>
                        <span style="display:flex;gap:6px;justify-content:flex-end;margin-top:4px;">
                            <button class="edit-save" onclick="saveField('notes')">✓</button>
                            <button class="edit-cancel" onclick="cancelEdit('notes')">✕</button>
                        </span>
                    </span>
                </span>
            </div>

            <div class="info-section">
                <h4>Information</h4>
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-user-circle-o"></i></span>
                    <span class="ir-label">Managed by</span>
                    <span class="ir-value">
                        <span class="display-val editable" onclick="toggleEdit('assigned_to')"><i class="fa fa-circle" style="color:#f97316;font-size:8px;margin-right:4px;"></i> {{ $user->name ?? 'Admin' }}</span>
                        <span class="edit-inline" id="edit-assigned_to">
                            <select id="fld-assigned_to" style="min-width:180px;">
                                @foreach(\App\Models\User::whereIn('user_group', ['admin','pv travels team'])->limit(20)->get() as $u)
                                <option value="{{ $u->id }}" {{ ($tripRequest->assigned_to ?? auth()->id()) == $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }}</option>
                                @endforeach
                            </select>
                            <button class="edit-save" onclick="saveField('assigned_to')">✓</button>
                            <button class="edit-cancel" onclick="cancelEdit('assigned_to')">✕</button>
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-phone"></i></span>
                    <span class="ir-label">Phone number</span>
                    <span class="ir-value">
                        <span class="display-val editable" onclick="toggleEdit('phone')">{{ $tripRequest->phone ?: 'N/A' }}</span>
                        <span class="edit-inline" id="edit-phone">
                            <input type="text" id="fld-phone" value="{{ $tripRequest->phone }}" style="width:160px;" placeholder="Phone number">
                            <button class="edit-save" onclick="saveField('phone')">✓</button>
                            <button class="edit-cancel" onclick="cancelEdit('phone')">✕</button>
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-users"></i></span>
                    <span class="ir-label">Travelers</span>
                    <span class="ir-value">
                        <span class="display-val editable" onclick="toggleEdit('adults')">{{ ($tripRequest->adults ?? 0) + ($tripRequest->children ?? 0) }} PAX</span>
                        <span class="edit-inline" id="edit-adults">
                            <input type="number" id="fld-adults" value="{{ $tripRequest->adults ?? 0 }}" style="width:80px;" placeholder="Adults" min="0">
                            <button class="edit-save" onclick="saveField('adults')">✓</button>
                            <button class="edit-cancel" onclick="cancelEdit('adults')">✕</button>
                        </span>
                    </span>
                </div>
                @php $cAges = $tripRequest->children_ages; if(is_string($cAges)) $cAges = json_decode($cAges, true); @endphp
                @if($tripRequest->children > 0 && $cAges && count($cAges))
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-child"></i></span>
                    <span class="ir-label">Children ages</span>
                    <span class="ir-value">{{ implode(', ', array_map(fn($a) => $a.' yrs', $cAges)) }}</span>
                </div>
                @endif
                @if($tripRequest->group_type)
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-sitemap"></i></span>
                    <span class="ir-label">Group type</span>
                    <span class="ir-value">{{ $tripRequest->group_type }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-calendar"></i></span>
                    <span class="ir-label">Travel dates</span>
                    <span class="ir-value">
                        @if($tripRequest->departure_date && $tripRequest->return_date)
                            @php
                            $dep = \Carbon\Carbon::parse($tripRequest->departure_date);
                            $ret = \Carbon\Carbon::parse($tripRequest->return_date);
                            $days = $dep->diffInDays($ret);
                            @endphp
                            <span class="display-val editable" onclick="toggleEdit('departure_date')">{{ $dep->format('d M Y') }} → {{ $ret->format('d M Y') }} ({{ $days }} days)</span>
                        @else
                            <span class="display-val editable" onclick="toggleEdit('departure_date')">Flexible</span>
                        @endif
                        <span class="edit-inline" id="edit-departure_date">
                            <input type="date" id="fld-departure_date" value="{{ $tripRequest->departure_date ? \Carbon\Carbon::parse($tripRequest->departure_date)->format('Y-m-d') : '' }}" style="width:145px;">
                            <input type="date" id="fld-return_date" value="{{ $tripRequest->return_date ? \Carbon\Carbon::parse($tripRequest->return_date)->format('Y-m-d') : '' }}" style="width:145px;">
                            <button class="edit-save" onclick="saveTravelDates()">✓</button>
                            <button class="edit-cancel" onclick="cancelEdit('departure_date')">✕</button>
                        </span>
                    </span>
                </div>
                @php
                $countryFlags = ['US'=>'🇺🇸','UK'=>'🇬🇧','FR'=>'🇫🇷','DE'=>'🇩🇪','IN'=>'🇮🇳','JO'=>'🇯🇴','AE'=>'🇦🇪','SA'=>'🇸🇦','CA'=>'🇨🇦','AU'=>'🇦🇺'];
                $cCode = $tripRequest->country ?? '';
                $cFlag = $countryFlags[$cCode] ?? '🌍';
                @endphp
                @if($cCode)
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-flag"></i></span>
                    <span class="ir-label">Market</span>
                    <span class="ir-value">
                        <span class="display-val editable" onclick="toggleEdit('country')">{{ $cFlag }} {{ $cCode }}</span>
                        <span class="edit-inline" id="edit-country">
                            <input type="text" id="fld-country" value="{{ $cCode }}" style="width:80px;" placeholder="e.g. JO">
                            <button class="edit-save" onclick="saveField('country')">✓</button>
                            <button class="edit-cancel" onclick="cancelEdit('country')">✕</button>
                        </span>
                    </span>
                </div>
                @endif
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-map-marker"></i></span>
                    <span class="ir-label">Destination</span>
                    <span class="ir-value">
                        <span class="display-val editable" onclick="toggleEdit('destination')">{{ $tripRequest->destination ?? 'Jordan' }}</span>
                        <span class="edit-inline" id="edit-destination" style="position:relative; flex-wrap:wrap; gap:4px; align-items:center;">
                            <span style="position:relative; display:inline-block;">
                                <input type="text" id="fld-destination" value="{{ $tripRequest->destination ?? 'Jordan' }}" style="width:180px;" placeholder="Type a location..." autocomplete="off" oninput="destAutocomplete(this.value)">
                                <ul id="dest-suggestions" style="display:none; position:absolute; top:100%; left:0; z-index:9999; background:#fff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,.12); margin:4px 0 0 0; padding:4px 0; min-width:220px; max-height:200px; overflow-y:auto; list-style:none;"></ul>
                            </span>
                            <button class="edit-save" onclick="saveField('destination')">✓</button>
                            <button class="edit-cancel" onclick="cancelEdit('destination')">✕</button>
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-tag"></i></span>
                    <span class="ir-label">Profile</span>
                    <span class="ir-value">
                        <span class="display-val editable" onclick="toggleEdit('participant_type')">{{ ucfirst(str_replace('_',' ',$tripRequest->participant_type ?? 'N/A')) }}</span>
                        <span class="edit-inline" id="edit-participant_type">
                            <select id="fld-participant_type" style="min-width:140px;">
                                <option value="alone" {{ ($tripRequest->participant_type ?? '') == 'alone' ? 'selected' : '' }}>Alone</option>
                                <option value="couple" {{ ($tripRequest->participant_type ?? '') == 'couple' ? 'selected' : '' }}>Couple</option>
                                <option value="family" {{ ($tripRequest->participant_type ?? '') == 'family' ? 'selected' : '' }}>Family</option>
                                <option value="group" {{ ($tripRequest->participant_type ?? '') == 'group' ? 'selected' : '' }}>Group</option>
                                <option value="friends" {{ ($tripRequest->participant_type ?? '') == 'friends' ? 'selected' : '' }}>Friends</option>
                            </select>
                            <button class="edit-save" onclick="saveField('participant_type')">✓</button>
                            <button class="edit-cancel" onclick="cancelEdit('participant_type')">✕</button>
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-envelope"></i></span>
                    <span class="ir-label">Email</span>
                    <span class="ir-value">
                        <span class="display-val editable" onclick="toggleEdit('email')">{{ $tripRequest->email }}</span>
                        <span class="edit-inline" id="edit-email">
                            <input type="email" id="fld-email" value="{{ $tripRequest->email }}" style="width:200px;" placeholder="Email">
                            <button class="edit-save" onclick="saveField('email')">✓</button>
                            <button class="edit-cancel" onclick="cancelEdit('email')">✕</button>
                        </span>
                    </span>
                </div>
            </div>

            <div class="info-section">
                <h4>Initial Request</h4>
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-check"></i></span>
                    <span class="ir-label">Received on</span>
                    <span class="ir-value">{{ $tripRequest->created_at->format('d M Y \a\t H:i') }}</span>
                </div>

                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-compass"></i></span>
                    <span class="ir-label">Project</span>
                    <span class="ir-value">{{ ucwords(str_replace('_',' ',$tripRequest->project_stage ?? 'N/A')) }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-bed"></i></span>
                    <span class="ir-label">Accommodation</span>
                    <span class="ir-value">
                        @php
                        $accom = $tripRequest->accommodation_prefs;
                        if(is_string($accom)) { $accom = json_decode($accom, true); }
                        @endphp
                        {{ is_array($accom) ? implode(', ', $accom) : ($accom ?? 'N/A') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-user"></i></span>
                    <span class="ir-label">Guided</span>
                    <span class="ir-value">{{ ucwords(str_replace(['-','_'],' ',$tripRequest->guide_type ?? 'N/A')) }}</span>
                </div>
                @php
                $gLangs = $tripRequest->guide_languages;
                if(is_string($gLangs)) { $gLangs = json_decode($gLangs, true); }
                @endphp
                @if($gLangs)
                <div class="info-row">
                    <span class="ir-icon"><i class="fa fa-language"></i></span>
                    <span class="ir-label">Guide language</span>
                    <span class="ir-value">{{ is_array($gLangs) ? implode(', ', $gLangs) : $gLangs }}</span>
                </div>
                @endif
                @if($tripRequest->travel_plan)
                <div style="margin-top:12px;">
                    <div style="font-size:12px;font-weight:700;color:#888;margin-bottom:4px;">Description</div>
                    <div style="font-size:13px;color:#333;background:#fafaf8;padding:12px;border-radius:6px;line-height:1.6;">{{ $tripRequest->travel_plan }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="rm-right">
        <div class="rm-topbar" style="justify-content:flex-end;">
            <div class="tb-actions">
                <a href="#" class="tb-attach"><i class="fa fa-paperclip"></i> Attachments</a>
                <div style="display:flex;">
                    <a href="tel:{{ $tripRequest->phone }}" class="btn-call"><i class="fa fa-phone"></i> Call</a>
                    <button class="btn-call-dd"><i class="fa fa-caret-down"></i></button>
                </div>
            </div>
        </div>

        {{-- DISCUSSION TAB --}}
        <div class="detail-panel" id="panelDiscussion">
            <div class="chat-area" id="chatArea">
                @if($tripRequest->messages->count() == 0)
                <div id="chatEmptyState" style="text-align:center;padding:40px;color:#aaa;">
                    <i class="fa fa-comments-o" style="font-size:36px;opacity:.4;margin-bottom:8px;display:block;"></i>
                    <p style="font-size:13px;">No messages yet. Start the conversation!</p>
                </div>
                @else
                    @php $lastDate = null; @endphp
                    @foreach($tripRequest->messages as $msg)
                        @php $msgDate = $msg->created_at->format('d F Y'); @endphp
                        @if($msgDate !== $lastDate)
                            <div class="chat-date">{{ $msgDate }}</div>
                            @php $lastDate = $msgDate; @endphp
                        @endif
                        <div class="msg {{ $msg->sender_type === 'agent' ? 'msg-agent' : 'msg-traveler' }}">
                            {!! nl2br(e($msg->message)) !!}
                            @if($msg->attachment)
                            <div style="margin-top:6px;">
                                @php $ext = strtolower(pathinfo($msg->attachment, PATHINFO_EXTENSION)); @endphp
                                @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <a href="{{ $msg->attachment }}" target="_blank"><img src="{{ $msg->attachment }}" style="max-width:200px;max-height:150px;border-radius:6px;border:1px solid #ddd;"></a>
                                @else
                                <a href="{{ $msg->attachment }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:rgba(0,0,0,.05);border-radius:4px;font-size:12px;color:#f97316;text-decoration:none;">
                                    <i class="fa fa-file-o"></i> {{ basename($msg->attachment) }}
                                </a>
                                @endif
                            </div>
                            @endif
                            <div class="msg-meta">
                                <span>{{ $msg->created_at->format('H:i') }}</span>
                                <span>·</span>
                                <span class="sender">{{ $msg->sender_name }}</span>
                                @if($msg->sender_type === 'agent')<span class="read-receipt">✓✓</span>@endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="chat-composer">
                <div class="composer-toolbar">
                    <button type="button" title="Bold"><b>B</b></button>
                    <button type="button" title="Italic"><i>I</i></button>
                    <button type="button" title="Underline"><u>U</u></button>
                    <button type="button" title="Strikethrough"><s>S</s></button>
                </div>
                <div class="composer-text">
                    <textarea id="msgInput" placeholder="Type your message..." rows="2"></textarea>
                </div>
                <div class="composer-bottom">
                    <div class="composer-bottom-left">
                        <input type="file" id="fileInput" style="display:none;" onchange="handleFileSelect(this)">
                        <button type="button" title="Attach file" onclick="document.getElementById('fileInput').click()"><i class="fa fa-paperclip"></i></button>
                        <button type="button" title="Attach document" onclick="document.getElementById('fileInput').click()"><i class="fa fa-file-o"></i></button>
                        <button type="button" title="Emoji"><i class="fa fa-smile-o"></i></button>
                        <button type="button" title="Template"><i class="fa fa-font"></i></button>
                    </div>
                    <div class="composer-bottom-right">
                        <span id="attachLabel" style="font-size:11px;color:#f97316;display:none;"></span>
                        <button class="btn-send" id="btnSend" onclick="sendMsg()">Send</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ITINERARY TAB --}}
        <div class="detail-panel" id="panelItinerary" style="display:none;overflow:hidden;">
            @include('admin.request-manager.partials.itinerary-tab')
        </div>

        {{-- CONTRACT TAB --}}
        <div class="detail-panel" id="panelContract" style="display:none;overflow-y:auto;">
            <div style="text-align:center;padding:60px 20px;color:#aaa;">
                <i class="fa fa-file-text-o" style="font-size:48px;opacity:.3;margin-bottom:12px;display:block;"></i>
                <h3 style="color:#555;font-size:16px;margin-bottom:8px;">Contract Management</h3>
                <p style="font-size:13px;">Create and manage travel contracts for this request.</p>
                @php
                    $contractDays = 1;
                    $contractNights = 0;
                    if($tripRequest->departure_date && $tripRequest->return_date) {
                        $d1 = \Carbon\Carbon::parse($tripRequest->departure_date);
                        $d2 = \Carbon\Carbon::parse($tripRequest->return_date);
                        $contractDays = max(1, $d1->diffInDays($d2) + 1);
                        $contractNights = max(0, $contractDays - 1);
                    }
                    $contractPax = ($tripRequest->adults ?? 0) + ($tripRequest->children ?? 0);
                    if($contractPax < 1) $contractPax = 1;
                @endphp
                <a href="{{ route('admin.quotations.create', [
                    'customer_name' => ($tripRequest->first_name ?? '') . ' ' . ($tripRequest->last_name ?? ''),
                    'email' => $tripRequest->email ?? '',
                    'phone' => $tripRequest->phone ?? '',
                    'description' => 'Trip to Jordan - Request #' . $tripRequest->id,
                    'ref_number' => 'REQ-' . $tripRequest->id,
                    'travel_date' => $tripRequest->departure_date ?? date('Y-m-d'),
                    'days' => $contractDays,
                    'nights' => $contractNights,
                    'travelers_number' => $contractPax,
                    'trip_request_id' => $tripRequest->id,
                ]) }}" style="margin-top:16px;background:#f97316;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-block;">
                    <i class="fa fa-plus"></i> Create Contract
                </a>
            </div>
        </div>

        {{-- BOOKING TAB --}}
        <div class="detail-panel" id="panelBooking" style="display:none;overflow-y:auto;">
            <div style="padding:24px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <h3 style="color:#1a1a1a;font-size:16px;font-weight:700;margin:0;"><i class="fa fa-calendar-check-o" style="color:#f97316;margin-right:8px;"></i>Booking Management</h3>
                    <a href="{{ route('admin.bookings.create') }}" style="background:#f97316;color:#fff;padding:8px 20px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:6px;">
                        <i class="fa fa-plus"></i> Create Booking
                    </a>
                </div>

                {{-- Quick Info --}}
                <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:14px 18px;margin-bottom:20px;font-size:13px;color:#c2410c;">
                    <i class="fa fa-info-circle" style="margin-right:6px;"></i>
                    Traveler: <strong>{{ $tripRequest->first_name }} {{ $tripRequest->last_name }}</strong>
                    @if($tripRequest->email) &nbsp;·&nbsp; <strong>{{ $tripRequest->email }}</strong> @endif
                    @if($tripRequest->departure_date) &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($tripRequest->departure_date)->format('d M Y') }} @endif
                </div>

                {{-- Existing Bookings --}}
                @php
                    $relatedBookings = [];
                    if ($tripRequest->email) {
                        $bUser = \App\Models\User::where('email', $tripRequest->email)->first();
                        if ($bUser) {
                            $relatedBookings = \App\Models\TourBooking::with(['invoice'])
                                ->where('user_id', $bUser->id)
                                ->orderBy('id', 'desc')
                                ->limit(20)
                                ->get();
                        }
                    }
                @endphp

                @if(count($relatedBookings) > 0)
                <div style="margin-bottom:16px;">
                    <h4 style="font-size:12px;font-weight:700;color:#333;text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px;">Existing Bookings ({{ count($relatedBookings) }})</h4>
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($relatedBookings as $rb)
                        <a href="{{ route('admin.bookings.edit', $rb->id) }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#fff;border:1px solid #e0e0e0;border-radius:8px;text-decoration:none;color:#333;transition:all .15s;" onmouseover="this.style.borderColor='#f97316';this.style.background='#fff7ed'" onmouseout="this.style.borderColor='#e0e0e0';this.style.background='#fff'">
                            <div style="width:36px;height:36px;background:#fff7ed;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#f97316;font-weight:800;font-size:12px;">
                                #{{ $rb->id }}
                            </div>
                            <div style="flex:1;">
                                <div style="font-size:13px;font-weight:600;color:#1a1a1a;">{{ $rb->invoice->desc ?? 'Booking #'.$rb->id }}</div>
                                <div style="font-size:11px;color:#888;margin-top:2px;">
                                    {{ $rb->travel_date && $rb->travel_date != '0000-00-00' ? date('d M Y', strtotime($rb->travel_date)) : 'No date' }}
                                    · {{ $rb->adult + $rb->child + $rb->infant }} PAX
                                    · {{ $rb->days }}D/{{ $rb->nights }}N
                                </div>
                            </div>
                            <div>
                                @php
                                    $bStatusMap = ['pen'=>['Pending','#f59e0b'],'con'=>['Confirmed','#10b981'],'can'=>['Cancelled','#ef4444'],'com'=>['Completed','#10b981']];
                                    $bStatus = $bStatusMap[$rb->trip_status] ?? ['N/A','#94a3b8'];
                                @endphp
                                <span style="font-size:10px;font-weight:700;color:{{ $bStatus[1] }};text-transform:uppercase;letter-spacing:.5px;">{{ $bStatus[0] }}</span>
                            </div>
                            <div style="color:#f97316;font-size:14px;"><i class="fa fa-chevron-right"></i></div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @else
                <div style="text-align:center;padding:40px 20px;color:#aaa;">
                    <i class="fa fa-calendar-o" style="font-size:48px;opacity:.3;margin-bottom:12px;display:block;"></i>
                    <h3 style="color:#555;font-size:16px;margin-bottom:8px;">No Bookings Yet</h3>
                    <p style="font-size:13px;max-width:300px;margin:0 auto;">Create a booking for this traveler to manage their reservation, invoicing, and traveler details.</p>
                    <a href="{{ route('admin.bookings.create') }}" style="display:inline-flex;align-items:center;gap:6px;margin-top:16px;background:#f97316;color:#fff;border:none;padding:10px 24px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="fa fa-plus"></i> Create Booking
                    </a>
                </div>
                @endif

                {{-- Quick Link to All Bookings --}}
                <div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px;text-align:center;">
                    <a href="{{ route('admin.bookings.index') }}" style="font-size:12px;color:#f97316;font-weight:600;text-decoration:none;"><i class="fa fa-list"></i> View All Bookings</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detail-panel{flex:1;display:flex;flex-direction:column;overflow:hidden;}
</style>

<script>
// Tab switching
function showDetailTab(tab){
    document.querySelectorAll('.detail-panel').forEach(function(p){p.style.display='none';});
    document.querySelectorAll('.detail-tab').forEach(function(t){t.classList.remove('active');});
    var map={discussion:'panelDiscussion',itinerary:'panelItinerary',contract:'panelContract',booking:'panelBooking'};
    var panel=document.getElementById(map[tab]);
    if(panel){panel.style.display='flex';}
    var activeTab=document.querySelector('.detail-tab[data-tab="'+tab+'"]');
    if(activeTab) activeTab.classList.add('active');
}

// Stage update
function updateStage(stage){
    fetch('/admin/request-manager/{{ $tripRequest->id }}/stage',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({stage:stage})}).then(function(r){return r.json()}).then(function(d){if(d.success){showToast('Stage updated');}});
}

// Inline edit
function toggleEdit(field){
    var editEl=document.getElementById('edit-'+field);
    var dispEl=editEl.previousElementSibling;
    if(editEl){editEl.classList.add('active');}
    if(dispEl){dispEl.style.display='none';}
}
function cancelEdit(field){
    var editEl=document.getElementById('edit-'+field);
    var dispEl=editEl.previousElementSibling;
    if(editEl){editEl.classList.remove('active');}
    if(dispEl){dispEl.style.display='inline';}
}
function saveField(field){
    var val=document.getElementById('fld-'+field).value;
    fetch('/admin/request-manager/{{ $tripRequest->id }}/update-field',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({field:field,value:val})}).then(function(r){return r.json()}).then(function(d){
        if(d.success){
            var editEl=document.getElementById('edit-'+field);
            var dispEl=editEl.previousElementSibling;
            if(field==='assigned_to'){
                var sel=document.getElementById('fld-assigned_to');
                dispEl.innerHTML='<i class="fa fa-circle" style="color:#f97316;font-size:8px;margin-right:4px;"></i> '+sel.options[sel.selectedIndex].text;
            } else if(field==='notes'){
                dispEl.textContent=val||'(empty)';
            } else {
                dispEl.textContent=val||'N/A';
            }
            cancelEdit(field);
            showToast(field.replace('_',' ')+' updated');
        }
    });
}
function saveBudget(){
    var min=document.getElementById('fld-ideal_budget').value;
    var max=document.getElementById('fld-max_budget').value;
    Promise.all([
        fetch('/admin/request-manager/{{ $tripRequest->id }}/update-field',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({field:'ideal_budget',value:min})}),
        fetch('/admin/request-manager/{{ $tripRequest->id }}/update-field',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({field:'max_budget',value:max})})
    ]).then(function(){
        var editEl=document.getElementById('edit-budget');
        var dispEl=editEl.previousElementSibling;
        var txt='$'+Number(min).toLocaleString();
        if(max && Number(max)>0) txt+=' - $'+Number(max).toLocaleString();
        dispEl.textContent=txt;
        cancelEdit('budget');
        showToast('Budget updated');
    });
}
function saveTravelDates(){
    var dep=document.getElementById('fld-departure_date').value;
    var ret=document.getElementById('fld-return_date').value;
    Promise.all([
        fetch('/admin/request-manager/{{ $tripRequest->id }}/update-field',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({field:'departure_date',value:dep})}),
        fetch('/admin/request-manager/{{ $tripRequest->id }}/update-field',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({field:'return_date',value:ret})})
    ]).then(function(){
        var editEl=document.getElementById('edit-departure_date');
        var dispEl=editEl.previousElementSibling;
        if(dep && ret){
            var d1=new Date(dep), d2=new Date(ret);
            var days=Math.round((d2-d1)/(1000*60*60*24));
            var fmt=function(d){return d.getDate().toString().padStart(2,'0')+' '+['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][d.getMonth()]+' '+d.getFullYear();};
            dispEl.textContent=fmt(d1)+' → '+fmt(d2)+' ('+days+' days)';
        } else {
            dispEl.textContent='Flexible';
        }
        cancelEdit('departure_date');
        showToast('Travel dates updated');
    });
}
// Traveller Name Edit
function toggleNameEdit(){
    document.getElementById('disp-traveler-name').style.display='none';
    document.getElementById('edit-traveler-name').style.display='inline-flex';
}
function cancelNameEdit(){
    document.getElementById('edit-traveler-name').style.display='none';
    document.getElementById('disp-traveler-name').style.display='inline';
}
function saveFullName(){
    var fn=document.getElementById('fld-first_name').value.trim();
    var ln=document.getElementById('fld-last_name').value.trim();
    Promise.all([
        fetch('/admin/request-manager/{{ $tripRequest->id }}/update-field',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({field:'first_name',value:fn})}),
        fetch('/admin/request-manager/{{ $tripRequest->id }}/update-field',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({field:'last_name',value:ln})})
    ]).then(function(){
        document.getElementById('disp-traveler-name').textContent=fn+' '+ln.toUpperCase();
        cancelNameEdit();
        showToast('Name updated');
    });
}
// Destination Autocomplete
var destTimer = null;
function destAutocomplete(val){
    var ul = document.getElementById('dest-suggestions');
    clearTimeout(destTimer);
    if(!val || val.length < 2){ ul.style.display='none'; ul.innerHTML=''; return; }
    destTimer = setTimeout(function(){
        fetch('https://nominatim.openstreetmap.org/search?format=json&limit=6&q='+encodeURIComponent(val), {headers:{'Accept-Language':'en'}})
        .then(function(r){ return r.json(); })
        .then(function(data){
            ul.innerHTML='';
            if(!data || !data.length){ ul.style.display='none'; return; }
            data.forEach(function(item){
                var li = document.createElement('li');
                li.textContent = item.display_name;
                li.style.cssText = 'padding:8px 14px; font-size:13px; cursor:pointer; color:#1e293b; border-bottom:1px solid #f1f5f9; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;';
                li.onmouseenter = function(){ this.style.background='#fff7ed'; this.style.color='#f97316'; };
                li.onmouseleave = function(){ this.style.background=''; this.style.color='#1e293b'; };
                li.onclick = function(){
                    document.getElementById('fld-destination').value = item.display_name;
                    ul.style.display='none'; ul.innerHTML='';
                };
                ul.appendChild(li);
            });
            ul.style.display='block';
        })
        .catch(function(){ ul.style.display='none'; });
    }, 350);
}
document.addEventListener('click', function(e){
    var ul = document.getElementById('dest-suggestions');
    if(ul && !ul.contains(e.target) && e.target.id !== 'fld-destination'){ ul.style.display='none'; }
});
// Chat
var selectedFile = null;
function handleFileSelect(input){
    if(input.files && input.files[0]){
        selectedFile = input.files[0];
        var label = document.getElementById('attachLabel');
        label.textContent = '📎 ' + selectedFile.name;
        label.style.display = 'inline';
        document.getElementById('btnSend').classList.add('has-text');
    }
}
function sendMsg(){
    var input=document.getElementById('msgInput');
    var msg=input.value.trim();
    if(!msg && !selectedFile) return;

    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    if(msg) formData.append('message', msg);
    if(selectedFile) formData.append('attachment', selectedFile);

    fetch('/admin/request-manager/{{ $tripRequest->id }}/message',{
        method:'POST',
        body: formData
    }).then(function(r){return r.json()}).then(function(d){
        if(d.success){
            var area=document.getElementById('chatArea');
            var empty=document.getElementById('chatEmptyState');if(empty)empty.remove();
            var div=document.createElement('div');div.className='msg msg-agent';
            var content = msg || '';
            if(d.attachment){
                var ext = d.attachment.split('.').pop().toLowerCase();
                if(['jpg','jpeg','png','gif','webp'].indexOf(ext) >= 0){
                    content += '<div style="margin-top:6px;"><a href="'+d.attachment+'" target="_blank"><img src="'+d.attachment+'" style="max-width:200px;max-height:150px;border-radius:6px;border:1px solid #ddd;"></a></div>';
                } else {
                    content += '<div style="margin-top:6px;"><a href="'+d.attachment+'" target="_blank" style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:rgba(0,0,0,.05);border-radius:4px;font-size:12px;color:#f97316;text-decoration:none;"><i class="fa fa-file-o"></i> '+(d.filename||'File')+'</a></div>';
                }
            }
            div.innerHTML=content+'<div class="msg-meta"><span>'+new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})+'</span><span>·</span><span class="sender">{{ $user->name ?? "Admin" }}</span><span class="read-receipt">✓✓</span></div>';
            area.appendChild(div);area.scrollTop=area.scrollHeight;
            input.value='';
            selectedFile = null;
            document.getElementById('fileInput').value = '';
            document.getElementById('attachLabel').style.display = 'none';
            document.getElementById('btnSend').classList.remove('has-text');
        }
    });
}
document.getElementById('msgInput').addEventListener('keydown',function(e){if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendMsg();}});
document.getElementById('msgInput').addEventListener('input',function(){
    var btn=document.getElementById('btnSend');
    if(this.value.trim()){btn.classList.add('has-text');}else{btn.classList.remove('has-text');}
});
document.getElementById('chatArea').scrollTop=document.getElementById('chatArea').scrollHeight;

// Auto-refresh polling for messages
setInterval(function() {
    // Only poll if Discussion tab is active
    var activeTab = document.querySelector('.detail-tab[data-tab="discussion"]');
    if (activeTab && activeTab.classList.contains('active')) {
        fetch(window.location.href)
            .then(function(r) { return r.text(); })
            .then(function(html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newChatArea = doc.getElementById('chatArea');
                var oldChatArea = document.getElementById('chatArea');
                
                if (newChatArea && oldChatArea) {
                    var newMsgs = newChatArea.querySelectorAll('.msg');
                    var oldMsgs = oldChatArea.querySelectorAll('.msg');
                    
                    if (newChatArea.innerHTML !== oldChatArea.innerHTML) {
                        var isAtBottom = oldChatArea.scrollHeight - oldChatArea.scrollTop <= oldChatArea.clientHeight + 50;
                        oldChatArea.innerHTML = newChatArea.innerHTML;
                        if (isAtBottom) {
                            oldChatArea.scrollTop = oldChatArea.scrollHeight;
                        }
                    }
                }
            })
            .catch(function(err) { console.error('Polling error', err); });
    }
}, 5000);

// Trip Planner landing/editor toggle
function openTripEditor(){
    document.getElementById('tpLanding').style.display='none';
    document.getElementById('tpEditor').style.display='flex';
}
function backToLanding(){
    document.getElementById('tpEditor').style.display='none';
    document.getElementById('tpLanding').style.display='block';
}

// Itinerary inner tabs (My quote / Day by day / Price)
function showItinTab(tab, el){
    document.querySelectorAll('.itin-tab-content').forEach(function(c){c.style.display='none';});
    document.querySelectorAll('.tp-editor-tabs a').forEach(function(a){a.classList.remove('active');});
    var map={personalization:'itinPersonalization',dayByDay:'itinDayByDay',pricing:'itinPricing'};
    var target=document.getElementById(map[tab]);
    if(target) target.style.display = tab==='dayByDay' ? 'flex' : 'block';
    el.classList.add('active');
}

// Itinerary CRUD
var itineraryId = {{ $tripRequest->latestItinerary->id ?? 'null' }};
var csrfToken = '{{ csrf_token() }}';
var requestId = {{ $tripRequest->id }};

function saveItinerary(){
    var data={title:document.getElementById('itinTitle').value,traveler_surname:document.getElementById('itinSurname').value,language:document.getElementById('itinLang').value,arrival_date:document.getElementById('itinArrival').value,cover_photo:document.getElementById('itinCover').value};
    var url='/admin/request-manager/'+requestId+'/itinerary'+(itineraryId?'/'+itineraryId:'');
    fetch(url,{method:itineraryId?'PUT':'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify(data)}).then(function(r){return r.json()}).then(function(d){if(d.success){itineraryId=d.id;showToast('Itinerary saved');}});
}

function addDay(){
    if(!itineraryId){showToast('Save personalization first');return;}
    var detail=document.getElementById('dayDetail');
    var empty=document.getElementById('dayEmptyState');
    if(detail) detail.style.display='block';
    if(empty) empty.style.display='none';
    document.getElementById('editDayId').value='';
    document.getElementById('dayTitle').value='';
    document.getElementById('dayDest').value='';
    document.getElementById('dayDesc').value='';
    document.getElementById('dayBreakfast').checked=false;
    document.getElementById('dayLunch').checked=false;
    document.getElementById('dayDinner').checked=false;
    document.getElementById('dayAccomName').value='';
    document.getElementById('dayAccomCat').value='';
    if(document.getElementById('dayAccomStars')) document.getElementById('dayAccomStars').value='';
    document.getElementById('dayAccomDesc').value='';
    // Deselect all sidebar cards
    document.querySelectorAll('.tp-day-card').forEach(function(c){c.classList.remove('active');});
}

function selectDay(dayId, cardEl){
    var detail=document.getElementById('dayDetail');
    var empty=document.getElementById('dayEmptyState');
    if(detail) detail.style.display='block';
    if(empty) empty.style.display='none';
    // Highlight sidebar card
    document.querySelectorAll('.tp-day-card').forEach(function(c){c.classList.remove('active');});
    if(cardEl) cardEl.classList.add('active');
    fetch('/admin/request-manager/'+requestId+'/itinerary/'+itineraryId+'/day/'+dayId,{headers:{'X-CSRF-TOKEN':csrfToken}}).then(function(r){return r.json()}).then(function(d){
        document.getElementById('editDayId').value=d.id;
        document.getElementById('dayTitle').value=d.title||'';
        document.getElementById('dayDest').value=d.destinations||'';
        document.getElementById('dayDesc').value=d.description||'';
        document.getElementById('dayBreakfast').checked=d.breakfast;
        document.getElementById('dayLunch').checked=d.lunch;
        document.getElementById('dayDinner').checked=d.dinner;
        document.getElementById('dayAccomName').value=d.accommodation_name||'';
        document.getElementById('dayAccomCat').value=d.accommodation_category||'';
        document.getElementById('dayAccomDesc').value=d.accommodation_description||'';
        // Update badge
        var badge=document.getElementById('dayBadgeNum');
        if(badge) badge.textContent=d.day_number||'';
    });
}

function saveDay(){
    if(!itineraryId){showToast('Save personalization first');return;}
    var dayId=document.getElementById('editDayId').value;
    var data={title:document.getElementById('dayTitle').value,destinations:document.getElementById('dayDest').value,description:document.getElementById('dayDesc').value,breakfast:document.getElementById('dayBreakfast').checked,lunch:document.getElementById('dayLunch').checked,dinner:document.getElementById('dayDinner').checked,accommodation_name:document.getElementById('dayAccomName').value,accommodation_category:document.getElementById('dayAccomCat').value,accommodation_description:document.getElementById('dayAccomDesc').value};
    var url='/admin/request-manager/'+requestId+'/itinerary/'+itineraryId+'/day'+(dayId?'/'+dayId:'');
    fetch(url,{method:dayId?'PUT':'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify(data)}).then(function(r){return r.json()}).then(function(d){if(d.success){showToast('Day saved');location.reload();}});
}

function savePricing(){
    if(!itineraryId){showToast('Save personalization first');return;}
    var data={price_per_person:document.getElementById('itinPPP').value,num_travelers:document.getElementById('itinNumTrav').value,price_includes:document.getElementById('itinIncludes').value,price_excludes:document.getElementById('itinExcludes').value,booking_conditions:document.getElementById('itinConditions').value};
    fetch('/admin/request-manager/'+requestId+'/itinerary/'+itineraryId,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify(data)}).then(function(r){return r.json()}).then(function(d){
        if(d.success){
            showToast('Pricing saved');
            if(d.group_total){
                document.getElementById('itinTotal').value=d.group_total;
                var disp=document.getElementById('itinTotalDisplay');
                if(disp) disp.textContent=parseFloat(d.group_total).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
            }
        }
    });
}

// Auto-calc total
var itinPPPEl = document.getElementById('itinPPP');
var itinNumTravEl = document.getElementById('itinNumTrav');
if (itinPPPEl) {
    itinPPPEl.addEventListener('input',function(){
        var ppp=parseFloat(this.value)||0;var num=parseInt(document.getElementById('itinNumTrav').value)||1;
        var total=(ppp*num).toFixed(2);
        document.getElementById('itinTotal').value=total;
        var disp=document.getElementById('itinTotalDisplay');
        if(disp) disp.textContent=parseFloat(total).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
    });
}
if (itinNumTravEl) {
    itinNumTravEl.addEventListener('input',function(){
        var num=parseInt(this.value)||1;var ppp=parseFloat(document.getElementById('itinPPP').value)||0;
        var total=(ppp*num).toFixed(2);
        document.getElementById('itinTotal').value=total;
        var disp=document.getElementById('itinTotalDisplay');
        if(disp) disp.textContent=parseFloat(total).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
    });
}

function showToast(msg){var t=document.createElement('div');t.textContent=msg;t.style.cssText='position:fixed;bottom:20px;right:20px;background:#f97316;color:#fff;padding:10px 20px;border-radius:6px;font-size:13px;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,.15);';document.body.appendChild(t);setTimeout(function(){t.remove()},2000);}
</script>
@endsection
