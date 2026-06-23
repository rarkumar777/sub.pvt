@extends('admin.layouts.app')

@section('title', 'Admin | User Groups')

@section('content')
<div class="tw-flex tw-flex-col tw-gap-6">
    
    {{-- Header Area --}}
    <div class="tw-flex tw-flex-col sm:tw-flex-row sm:tw-items-center tw-justify-between tw-gap-4">
        <div>
            <div class="tw-flex tw-items-center tw-gap-2 tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:tw-text-orange-500 tw-transition-colors tw-no-underline">Admin</a>
                <span class="tw-opacity-50">/</span>
                <span>User Access</span>
                <span class="tw-opacity-50">/</span>
                <span class="tw-text-slate-600">User Groups</span>
            </div>
            <h1 class="tw-text-3xl tw-font-black tw-text-slate-900 tw-flex tw-items-center tw-gap-3">
                <div class="tw-w-10 tw-h-10 tw-bg-orange-500 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-orange-200">
                    <i class="fa fa-users tw-text-white tw-text-base"></i>
                </div>
                User Access Groups
            </h1>
            <p class="subtitle">Manage membership levels, pricing, and access permissions for your users.</p>
        </div>
        <div>
            <a href="#addnew" onclick="edit_group('','','','','email',0,'add_new');" class="btn orange tw-shadow-premium">
                <i class="fa fa-plus-circle"></i> Add New Group
            </a>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="box !tw-p-0 tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/80 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Group Name</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Pricing & Validity</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Activation</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-center">Sign-up Status</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($userGroups as $name => $group)
                    @if(empty($name)) @continue @endif
                    <tr class="hover:tw-bg-orange-50/30 tw-transition-colors">
                        {{-- Group Name --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-font-bold tw-text-slate-900 tw-text-sm">{{ $name }}</div>
                            <div class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mt-1">Identifier Tag</div>
                        </td>
                        
                        {{-- Pricing --}}
                        <td class="tw-py-4 tw-px-6">
                            @if($group['price'] == 0)
                                <span class="tw-inline-flex tw-items-center tw-px-2 tw-py-0.5 tw-rounded-md tw-bg-emerald-50 tw-text-emerald-600 tw-text-[10px] tw-font-black tw-uppercase tw-tracking-widest">
                                    Free
                                </span>
                            @else
                                <div class="tw-font-bold tw-text-orange-600 tw-text-sm">${{ number_format($group['price'], 2) }}</div>
                            @endif
                            <div class="tw-text-xs tw-text-slate-500 tw-font-semibold tw-mt-1.5 tw-flex tw-items-center tw-gap-1.5">
                                <i class="fa fa-clock-o tw-text-slate-300"></i>
                                {{ $group['valid'] == 0 ? 'Lifetime Access' : $group['valid'] . ' Days' }}
                            </div>
                        </td>

                        {{-- Activation --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                @php
                                    $actIcon = 'fa-check-circle';
                                    $actTheme = 'emerald';
                                    if($group['activate_by'] == 'email') { $actIcon = 'fa-envelope'; $actTheme = 'amber'; }
                                    elseif($group['activate_by'] == 'admin') { $actIcon = 'fa-shield'; $actTheme = 'orange'; }
                                @endphp
                                <div class="tw-w-8 tw-h-8 tw-rounded-xl tw-bg-{{ $actTheme }}-50 tw-text-{{ $actTheme }}-600 tw-flex tw-items-center tw-justify-center tw-border tw-border-{{ $actTheme }}-100">
                                    <i class="fa {{ $actIcon }} tw-text-[13px]"></i>
                                </div>
                                <span class="tw-text-sm tw-font-bold tw-text-slate-700 tw-capitalize">{{ $group['activate_by'] }}</span>
                            </div>
                        </td>

                        {{-- Sign-up --}}
                        <td class="tw-py-4 tw-px-6 tw-text-center">
                            @if($group['allowed'] == 1)
                                <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-emerald-50 tw-text-emerald-600 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-border tw-border-emerald-100">
                                    <span class="tw-w-1.5 tw-h-1.5 tw-bg-emerald-500 tw-rounded-full"></span> Enabled
                                </span>
                            @else
                                <span class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-2.5 tw-py-1 tw-rounded-lg tw-bg-slate-50 tw-text-slate-500 tw-text-[11px] tw-font-black tw-uppercase tw-tracking-widest tw-border tw-border-slate-100">
                                    <span class="tw-w-1.5 tw-h-1.5 tw-bg-slate-300 tw-rounded-full"></span> Disabled
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-flex tw-items-center tw-justify-end tw-gap-2">
                                <a href="{{ route('admin.user-groups.fields', $name) }}" 
                                   class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-amber-50 tw-text-amber-600 hover:tw-bg-amber-500 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-amber-100 hover:tw-border-amber-500" 
                                   title="Edit Custom Fields">
                                    <i class="fa fa-th-list tw-text-xs"></i>
                                </a>
                                
                                <a href="#addnew" 
                                   onclick="edit_group('{{ $name }}','{{ $group['valid'] }}','{{ $group['price'] }}','{{ $group['valid'] }}','{{ $group['activate_by'] }}',{{ $group['allowed'] }},'edit');"
                                   class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-orange-100 hover:tw-border-orange-600" 
                                   title="Edit Group">
                                    <i class="fa fa-pencil tw-text-xs"></i>
                                </a>
                                
                                <a href="{{ route('admin.user-groups.index', ['del' => $name]) }}" 
                                   onclick="return confirm('Are you sure you want to delete this group?');" 
                                   class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-rose-100 hover:tw-border-rose-600" 
                                   title="Delete Group">
                                    <i class="fa fa-trash-o tw-text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="tw-py-20 tw-text-center">
                            <div class="tw-flex tw-flex-col tw-items-center tw-gap-3">
                                <div class="tw-w-16 tw-h-16 tw-rounded-2xl tw-bg-slate-50 tw-flex tw-items-center tw-justify-center tw-text-slate-300">
                                    <i class="fa fa-users tw-text-3xl"></i>
                                </div>
                                <div>
                                    <p class="tw-text-slate-600 tw-font-bold tw-text-base">No groups found</p>
                                    <p class="tw-text-slate-400 tw-text-xs tw-mt-1">Add a new user group to get started.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div class="modal" id="addnew">
    <div class="tw-w-full tw-max-w-2xl !tw-p-8 sm:!tw-p-10 !tw-rounded-3xl tw-bg-white/95 tw-backdrop-blur-xl tw-border tw-border-slate-100 tw-shadow-2xl">
        <div class="tw-flex tw-items-center tw-justify-between tw-mb-8">
            <div class="tw-flex tw-items-center tw-gap-4">
                <span class="tw-w-12 tw-h-12 tw-bg-orange-50 tw-text-orange-500 tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-shadow-sm tw-border tw-border-orange-100">
                    <i class="fa fa-plus-circle tw-text-xl" id="modal-icon"></i>
                </span>
                <div>
                    <h3 class="tw-text-2xl tw-font-black tw-text-slate-900 tw-mb-0" id="modal-title">New Group</h3>
                    <p class="tw-text-slate-400 tw-text-sm tw-mt-1 tw-font-medium">Define group properties and accessibility.</p>
                </div>
            </div>
            <a href="#close" title="Close" class="tw-w-10 tw-h-10 tw-flex tw-items-center tw-justify-center tw-rounded-xl tw-bg-slate-50 tw-text-slate-400 hover:tw-bg-rose-50 hover:tw-text-rose-500 tw-transition-all tw-no-underline">
                <i class="fa fa-times tw-text-lg"></i>
            </a>
        </div>

        <form method="POST" action="{{ route('admin.user-groups.store') }}" name="add_new">
            @csrf
            <div class="tw-space-y-6">
                <div>
                    <label class="tw-text-sm tw-font-bold tw-text-slate-700 tw-mb-2">Unique Group Name <span class="tw-text-rose-500">*</span></label>
                    <input type="text" name="group_name" id="group_name" maxlength="50" placeholder="e.g. VIP Members" required class="tw-w-full tw-bg-slate-50 focus:tw-bg-white">
                </div>

                <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 tw-gap-6 tw-border-t tw-border-slate-100 tw-pt-6">
                    <div>
                        <label class="tw-text-sm tw-font-bold tw-text-slate-700 tw-mb-2">Membership Type</label>
                        <select name="group_type" id="group_type" onchange="set_price_input();" class="tw-w-full tw-bg-slate-50 focus:tw-bg-white">
                            <option value="0">Free Membership</option>
                            <option value="1">Paid Membership</option>
                        </select>
                    </div>
                    <div>
                        <label class="tw-text-sm tw-font-bold tw-text-slate-700 tw-mb-2">Activate Group By</label>
                        <select name="group_activate" id="group_activate" class="tw-w-full tw-bg-slate-50 focus:tw-bg-white">
                            <option value="auto">Instant (Sign up)</option>
                            <option value="email" selected>Email Verification</option>
                            <option value="admin">Manual Approval</option>
                        </select>
                    </div>
                </div>

                <div class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 tw-gap-6">
                    <div>
                        <label class="tw-text-sm tw-font-bold tw-text-slate-700 tw-mb-2">Price ($)</label>
                        <div class="tw-relative">
                            <span class="tw-absolute tw-left-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400 tw-font-bold">$</span>
                            <input type="text" name="group_price" id="group_price" maxlength="50" value="0" disabled class="tw-w-full !tw-pl-8 disabled:tw-opacity-60 disabled:tw-bg-slate-50 tw-transition-all tw-bg-slate-50 focus:tw-bg-white">
                        </div>
                        <input type="hidden" name="group_price_hidden" id="group_price_hidden" value="0">
                    </div>
                    <div>
                        <label class="tw-text-sm tw-font-bold tw-text-slate-700 tw-mb-2">Validity Limit</label>
                        <div class="tw-relative">
                            <input type="text" name="group_cycle" id="group_cycle" maxlength="50" value="0" placeholder="0 for lifetime" disabled class="tw-w-full !tw-pr-16 disabled:tw-opacity-60 disabled:tw-bg-slate-50 tw-transition-all tw-bg-slate-50 focus:tw-bg-white">
                            <span class="tw-absolute tw-right-4 tw-top-1/2 -tw-translate-y-1/2 tw-text-slate-400 tw-text-xs tw-font-bold">DAYS</span>
                        </div>
                        <input type="hidden" name="group_cycle_hidden" id="group_cycle_hidden" value="0">
                    </div>
                </div>

                <style>
                    .m3-toggle { position: relative; display: inline-block; width: 48px; height: 26px; flex-shrink: 0; }
                    .m3-toggle input { opacity: 0; width: 0; height: 0; }
                    .m3-toggle .m3-slider {
                        position: absolute; cursor: pointer; inset: 0;
                        background: #cbd5e1; border-radius: 999px;
                        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    }
                    .m3-toggle .m3-slider::before {
                        content: ''; position: absolute;
                        width: 20px; height: 20px; left: 3px; bottom: 3px;
                        background: white; border-radius: 50%;
                        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                    }
                    .m3-toggle input:checked + .m3-slider { background: #f97316; }
                    .m3-toggle input:checked + .m3-slider::before { transform: translateX(22px); }
                </style>
                <div class="tw-bg-orange-50/50 tw-p-5 tw-rounded-2xl tw-border tw-border-orange-100">
                    <label class="tw-flex tw-items-center tw-justify-between tw-cursor-pointer tw-mb-0">
                        <div class="tw-flex tw-flex-col">
                            <span class="tw-text-sm tw-font-bold tw-text-slate-900">Public Sign-up Visibility</span>
                            <span class="tw-text-xs tw-text-slate-500 tw-font-medium tw-mt-1">Allow users to discover and join this group during registration.</span>
                        </div>
                        <label class="m3-toggle">
                            <input type="checkbox" name="in_use" id="in_use" value="1">
                            <span class="m3-slider"></span>
                        </label>
                    </label>
                </div>
            </div>

            <input type="hidden" name="action" id="action" value="add_new">
            <input type="hidden" name="edit_name" id="edit_name" value="">
            
            <div class="tw-mt-8 tw-pt-6 tw-border-t tw-border-slate-100 tw-flex tw-items-center tw-justify-end tw-gap-4">
                <a href="#close" class="btn tw-bg-slate-100 tw-text-slate-600 hover:tw-bg-slate-200 !tw-px-6 !tw-py-3">Cancel</a>
                <button type="submit" name="save" id="save" class="btn orange !tw-px-8 !tw-py-3">
                    <i class="fa fa-check"></i> <span>Create Group</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function set_price_input(){
    var el = document.getElementById('group_type').value;
    var group_price = document.getElementById("group_price");
    var group_cycle = document.getElementById("group_cycle");
    if (el == 1){ 
        group_price.disabled = false; group_price.value = ""; 
        group_cycle.disabled = false; group_cycle.value = ""; 
        group_price.style.cursor = "text"; group_cycle.style.cursor = "text";
    }
    if (el == 0){ 
        group_price.disabled = true; group_price.value = 0; 
        group_cycle.disabled = true; group_cycle.value = 0; 
        group_price.style.cursor = "not-allowed"; group_cycle.style.cursor = "not-allowed";
    }
    // Sync hidden fields
    document.getElementById('group_price_hidden').value = group_price.value;
    document.getElementById('group_cycle_hidden').value = group_cycle.value;
}
function edit_group(name, valid, price, valid2, activate, allowed, action){
    if (price == 0){
        document.getElementById('group_type').selectedIndex = 0;
    } else {
        document.getElementById('group_type').selectedIndex = 1;
    }
    set_price_input();
    document.getElementById('group_name').value = name;
    // Use readOnly instead of disabled so value is submitted
    document.getElementById('group_name').readOnly = (action != 'add_new');
    if (action != 'add_new') {
        document.getElementById('group_name').classList.add('tw-opacity-60');
    } else {
        document.getElementById('group_name').classList.remove('tw-opacity-60');
    }
    
    document.getElementById('group_price').value = price;
    document.getElementById('group_cycle').value = valid;
    document.getElementById('group_price_hidden').value = price;
    document.getElementById('group_cycle_hidden').value = valid;
    document.getElementById('group_activate').value = activate;
    
    if (action == 'add_new'){
        document.getElementById('modal-title').innerText = 'New Group';
        document.getElementById('modal-icon').className = 'fa fa-users tw-text-xl';
        document.getElementById('save').innerHTML = '<i class="fa fa-plus-circle"></i> Create Group';
        document.getElementById('action').value = 'add_new';
    } else {
        document.getElementById('modal-title').innerText = 'Edit Group';
        document.getElementById('modal-icon').className = 'fa fa-pencil tw-text-xl';
        document.getElementById('save').innerHTML = '<i class="fa fa-check"></i> Save Changes';
        document.getElementById('action').value = 'edit';
        document.getElementById('edit_name').value = name;
    }
    if (allowed == 1){ document.getElementById('in_use').checked = true; }
    else { document.getElementById('in_use').checked = false; }
}

// Before form submit, enable all disabled inputs and sync hidden fields
document.querySelector('form[name="add_new"]').addEventListener('submit', function() {
    var price = document.getElementById('group_price');
    var cycle = document.getElementById('group_cycle');
    // Sync hidden values with visible values
    document.getElementById('group_price_hidden').value = price.value;
    document.getElementById('group_cycle_hidden').value = cycle.value;
    // Enable disabled fields so they submit
    price.disabled = false;
    cycle.disabled = false;
});

// Also sync hidden fields on input change
document.getElementById('group_price').addEventListener('input', function() {
    document.getElementById('group_price_hidden').value = this.value;
});
document.getElementById('group_cycle').addEventListener('input', function() {
    document.getElementById('group_cycle_hidden').value = this.value;
});
</script>
@endsection
