@extends('admin.layouts.app')

@section('title', 'Admin | Users')

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
                <span class="tw-text-slate-600">Manage Users</span>
            </div>
            <h1 class="tw-text-3xl tw-font-black tw-text-slate-900 tw-flex tw-items-center tw-gap-3">
                <div class="tw-w-10 tw-h-10 tw-bg-orange-500 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-shadow-lg tw-shadow-orange-200">
                    <i class="fa fa-users tw-text-white tw-text-base"></i>
                </div>
                Manage Users
            </h1>
            <p class="subtitle">Search, filter, and manage administrative and client accounts across the platform.</p>
        </div>
        <div>
            <a href="{{ route('admin.users.create') }}" class="btn orange tw-shadow-premium">
                <i class="fa fa-plus-circle"></i> Add New User
            </a>
        </div>
    </div>

    {{-- Filter Widget --}}
    <div class="box !tw-p-5 !tw-mb-0">
        <div class="tw-flex tw-items-center tw-gap-2 tw-text-slate-400 tw-mb-4">
            <i class="fa fa-filter tw-text-sm"></i>
            <span class="tw-text-[11px] tw-font-bold tw-uppercase tw-tracking-widest">Filters</span>
        </div>
        <form method="get" action="{{ route('admin.users.index') }}" class="tw-grid tw-grid-cols-1 md:tw-grid-cols-5 tw-gap-4 tw-items-end">
            
            <div class="tw-flex tw-flex-col tw-gap-1.5">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-0">Country</label>
                <select name="country">
                    <option value="">All Countries</option>
                    @foreach($countries as $cid => $cname)
                    <option value="{{ $cid }}" {{ request('country') == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                    @endforeach
                </select>
            </div>

            <div class="tw-flex tw-flex-col tw-gap-1.5">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-0">User Group</label>
                <select name="user_group">
                    <option value="">All Groups</option>
                    @foreach($userGroups as $ug)
                    <option value="{{ $ug }}" {{ request('user_group') == $ug ? 'selected' : '' }}>{{ $ug }}</option>
                    @endforeach
                </select>
            </div>

            <div class="tw-flex tw-flex-col tw-gap-1.5">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-0">Search Name</label>
                <input type="text" name="first_name" placeholder="Enter name..." value="{{ request('first_name') }}">
            </div>

            <div class="tw-flex tw-flex-col tw-gap-1.5">
                <label class="tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-mb-0">Email Address</label>
                <input type="email" name="email" placeholder="Enter email..." value="{{ request('email') }}">
            </div>

            <div class="tw-flex tw-gap-2 tw-items-end">
                <button type="submit" class="btn orange tw-flex-1">
                    <i class="fa fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn red !tw-px-3" title="Reset Filters">
                    <i class="fa fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Main Table Section --}}
    <div class="box !tw-p-0 tw-overflow-hidden">
        <div class="tw-overflow-x-auto">
            <table class="tw-w-full tw-text-left tw-border-collapse">
                <thead>
                    <tr class="tw-bg-slate-50/80 tw-border-b tw-border-slate-100">
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">User Details</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Contact Info</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest">Affiliation</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-center">Status</th>
                        <th class="tw-py-4 tw-px-6 tw-text-[11px] tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-widest tw-text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="tw-divide-y tw-divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:tw-bg-orange-50/30 tw-transition-colors">
                        {{-- User Identity --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-flex tw-items-center tw-gap-3">
                                <div class="tw-relative">
                                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name) . '&color=FFFFFF&background=f97316' }}" 
                                         class="tw-w-10 tw-h-10 tw-rounded-xl tw-object-cover tw-border-2 tw-border-slate-100 tw-shadow-sm"
                                         alt="{{ $user->first_name }}">
                                </div>
                                <div>
                                    <div class="tw-font-bold tw-text-slate-900 tw-text-sm">{{ $user->first_name }} {{ $user->last_name }}</div>
                                    <div class="tw-mt-1">
                                        <span class="tw-inline-flex tw-items-center tw-px-2 tw-py-0.5 tw-rounded-md tw-bg-orange-50 tw-text-orange-600 tw-text-[10px] tw-font-black tw-uppercase tw-tracking-widest">
                                            {{ $user->user_group }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Contact Info --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-flex tw-flex-col tw-gap-1">
                                <span class="tw-text-sm tw-text-slate-700 tw-font-medium tw-flex tw-items-center tw-gap-1.5">
                                    <i class="fa fa-envelope-o tw-text-xs tw-text-slate-300"></i> {{ $user->email }}
                                </span>
                                @if($user->mobile)
                                <span class="tw-text-xs tw-text-slate-500 tw-flex tw-items-center tw-gap-1.5">
                                    <i class="fa fa-phone tw-text-xs tw-text-slate-300"></i> {{ $user->mobile }}
                                </span>
                                @endif
                            </div>
                        </td>

                        {{-- Company --}}
                        <td class="tw-py-4 tw-px-6">
                            @if($user->company)
                                <div class="tw-text-sm tw-text-slate-700 tw-font-semibold">{{ $user->company }}</div>
                            @else
                                <span class="tw-text-sm tw-text-slate-400 tw-italic tw-font-medium">Unspecified</span>
                            @endif
                        </td>

                        {{-- Status Toggle --}}
                        <td class="tw-py-4 tw-px-6 tw-text-center">
                            <label class="tw-inline-flex tw-items-center tw-cursor-pointer tw-mb-0">
                                <input type="checkbox" class="tw-sr-only md-switch" {{ $user->status == 1 ? 'checked' : '' }}
                                       onchange="user_activator(this.checked ? 'a' : 'd', '{{ $user->id }}', '{{ $user->email }}');">
                                <div class="switch-track tw-relative tw-w-11 tw-h-6 tw-bg-slate-200 tw-rounded-full tw-transition-colors">
                                    <div class="switch-thumb tw-absolute tw-top-0.5 tw-left-0.5 tw-w-5 tw-h-5 tw-bg-white tw-shadow-md tw-rounded-full tw-transition-all"></div>
                                </div>
                            </label>
                        </td>

                        {{-- Actions --}}
                        <td class="tw-py-4 tw-px-6">
                            <div class="tw-flex tw-items-center tw-justify-end tw-gap-2">
                                <a href="{{ route('admin.users.permissions', $user->id) }}" 
                                   class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-orange-50 tw-text-orange-500 hover:tw-bg-orange-500 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-orange-100 hover:tw-border-orange-500"
                                   title="Roles & Permissions">
                                    <i class="fa fa-shield tw-text-xs"></i>
                                </a>
                                
                                <a href="{{ route('admin.users.edit', $user->id) }}" 
                                   class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-orange-50 tw-text-orange-600 hover:tw-bg-orange-600 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-orange-100 hover:tw-border-orange-600" 
                                   title="Edit Profile">
                                    <i class="fa fa-pencil tw-text-xs"></i>
                                </a>
                                
                                <a href="{{ route('admin.users.index', ['del' => $user->id]) }}" 
                                   onclick="return confirm('Archive user {{ $user->first_name }}?');" 
                                   class="tw-w-9 tw-h-9 tw-rounded-xl tw-flex tw-items-center tw-justify-center tw-bg-rose-50 tw-text-rose-600 hover:tw-bg-rose-600 hover:tw-text-white tw-transition-all tw-no-underline tw-border tw-border-rose-100 hover:tw-border-rose-600" 
                                   title="Delete User">
                                    <i class="fa fa-trash tw-text-xs"></i>
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
                                    <p class="tw-text-slate-600 tw-font-bold tw-text-base">No users found</p>
                                    <p class="tw-text-slate-400 tw-text-xs tw-mt-1">No results match your current filter settings.</p>
                                </div>
                                <a href="{{ route('admin.users.index') }}" class="btn red !tw-text-xs !tw-py-2 !tw-px-4 tw-mt-2">
                                    <i class="fa fa-refresh"></i> Clear Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if($users->hasPages())
        <div class="tw-px-6 tw-py-4 tw-border-t tw-border-slate-100 tw-bg-slate-50/30">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* Material Switch Styles */
    .md-switch:checked + .switch-track {
        background-color: #f97316 !important;
    }
    .md-switch:checked + .switch-track .switch-thumb {
        transform: translateX(20px);
        background-color: #ffffff !important;
        box-shadow: 0 2px 4px rgba(249, 115, 22, 0.4) !important;
    }
    
    /* Pagination Overrides */
    .pagination { display: flex; list-style: none; gap: 6px; justify-content: flex-end; align-items: center; margin: 0; padding: 0; }
    .pagination li { display: inline-block; }
    .pagination li span, .pagination li a {
        display: flex; align-items: center; justify-content: center;
        min-width: 36px; height: 36px; border-radius: 10px; font-size: 13px; font-weight: 700;
        text-decoration: none; border: 1px solid #f1f5f9; background: #fff; color: #475569;
        padding: 0 10px; transition: all 0.2s;
    }
    .pagination li.active span {
        background: var(--brand-primary); color: #fff; border-color: var(--brand-primary);
        box-shadow: 0 4px 10px rgba(249, 115, 22, 0.25);
    }
    .pagination li a:hover { background: #f8fafc; border-color: #e2e8f0; color: var(--brand-primary); }
    .pagination li.disabled span { opacity: 0.4; cursor: not-allowed; }
</style>

@push('scripts')
<script>
/**
 * Modern Status Activator
 * @param {string} action 'a' for activate, 'd' for deactivate
 * @param {string|number} id User ID
 * @param {string} email User Email for confirmation context
 */
function user_activator(action, id, email){
    const isActive = (action === 'a');
    const msg = isActive ? `Enable account access for ${email}?` : `Revoke account access for ${email}?`;
    
    if (confirm(msg)) {
        $.ajax({
            url: "{{ url('admin/users') }}/" + id + "/toggle-status?action=" + action,
            method: 'GET',
            success: function(response){
                location.reload();
            },
            error: function(xhr){
                alert('An error occurred while updating status. Please try again.');
                location.reload();
            }
        });
    } else {
        location.reload();
    }
}
</script>
@endpush

@endsection
