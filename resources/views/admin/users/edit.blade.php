@extends('admin.layouts.app')

@section('title', 'Admin | Edit User')

@section('content')

<div class="tw-flex tw-justify-between tw-items-center tw-mb-10">
    <div>
        <h1 class="tw-flex tw-items-center tw-gap-4">
            <span class="tw-w-12 tw-h-12 tw-bg-orange-50 tw-text-orange-500 tw-rounded-2xl tw-flex tw-items-center tw-justify-center">
                <i class="fa fa-edit"></i>
            </span>
            Edit User: {{ $user->first_name }}
        </h1>
        <p class="subtitle">Modify the account details and configuration for this user.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn red">
        <i class="fa fa-times"></i> Cancel
    </a>
</div>

<form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="tw-space-y-8">
    <div class="box !tw-p-10">
        
        <!-- SECTION 1: IDENTITY -->
        <div class="form-section">
            <div class="form-section-header">
                <i class="fa fa-id-card"></i>
                <h3>Identity & Profile</h3>
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
                <div>
                    <label>First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" placeholder="e.g. John" style="@error('first_name') border-color: #ef4444 !important; background-color: #fef2f2 !important; @enderror">
                    @error('first_name')<span class="tw-text-red-500 tw-text-xs tw-mt-1 tw-block tw-font-semibold">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" placeholder="e.g. Doe">
                </div>
                <div>
                    <label>Company Name</label>
                    <input type="text" name="company" value="{{ old('company', $user->company) }}" placeholder="Organization or Company">
                </div>
                <div class="md:tw-col-span-3">
                    <label>Profile Avatar</label>
                    <div class="tw-flex tw-items-center tw-gap-4">
                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name) . '&color=FFFFFF&background=f97316' }}" class="tw-w-14 tw-h-14 tw-rounded-2xl tw-object-cover tw-border-2 tw-border-slate-50 tw-shadow-sm">
                        <input type="file" name="avatar" class="!tw-py-8 !tw-h-auto !tw-border-dashed tw-bg-slate-50/50 tw-flex-1">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: CONTACT & LOCATION -->
        <div class="form-section">
            <div class="form-section-header">
                <i class="fa fa-envelope"></i>
                <h3>Contact & Location</h3>
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
                <div>
                    <label>E-mail Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="john@example.com" style="@error('email') border-color: #ef4444 !important; background-color: #fef2f2 !important; @enderror">
                    @error('email')<span class="tw-text-red-500 tw-text-xs tw-mt-1 tw-block tw-font-semibold">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label>Website URL</label>
                    <input type="text" name="url" value="{{ old('url', $user->url) }}" placeholder="https://example.com">
                </div>
                <div>
                    <label>Mobile Number</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="+1 (555) 000-0000">
                </div>
                <div class="md:tw-col-span-2">
                    <label>Physical Address</label>
                    <input type="text" name="address" value="{{ old('address', $user->address) }}" placeholder="Full street address">
                </div>
                <div>
                    <label>Gender</label>
                    <select name="gender">
                        <option value="0" {{ $user->gender == 0 ? 'selected' : '' }}>Male</option>
                        <option value="1" {{ $user->gender == 1 ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                <div>
                    <label>Country</label>
                    <select name="country" id="country" onchange="do_ajax('#city','{{ url('/') }}/ajax/cities.php?input=city&c='+document.getElementById('country').value,'');">
                        <option value="">Select Country</option>
                        @foreach($countries as $cid => $cname)
                        <option value="{{ $cid }}" {{ $user->country == $cid ? 'selected' : '' }}>{{ $cname }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>City</label>
                    <div id="city">
                        <input type="text" name="city" value="{{ old('city', $user->city) }}" placeholder="Enter City">
                    </div>
                </div>
                <div>
                    <label>Date of Birth</label>
                    <input type="text" name="birth_day" value="{{ old('birth_day', $user->birth_day) }}" class="datepicker" data-disable-dates="future" data-view-mode="years">
                </div>
            </div>
        </div>

        <!-- SECTION 3: SECURITY & ROLE -->
        <div class="form-section !tw-mb-0">
            <div class="form-section-header">
                <i class="fa fa-shield"></i>
                <h3>Account Security & Role</h3>
            </div>
            <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6">
                <div>
                    <label>Role / User Group</label>
                    <select name="user_group">
                        @foreach($userGroups as $ug)
                        <option value="{{ $ug }}" {{ $user->user_group == $ug ? 'selected' : '' }}>{{ $ug }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>New Password (Optional)</label>
                    <input type="password" name="password" placeholder="••••••••" style="@error('password') border-color: #ef4444 !important; background-color: #fef2f2 !important; @enderror">
                    @error('password')<span class="tw-text-red-500 tw-text-xs tw-mt-1 tw-block tw-font-semibold">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label>Confirm Password</label>
                    <input type="password" name="retype_password" placeholder="••••••••" style="@error('password') border-color: #ef4444 !important; background-color: #fef2f2 !important; @enderror">
                </div>
            </div>
        </div>

        <!-- ACTIONS -->
        <div class="tw-mt-12 tw-pt-8 tw-border-t tw-border-slate-100 tw-flex tw-justify-end">
            <button type="submit" class="btn orange !tw-px-10 !tw-h-14 tw-text-base">
                <i class="fa fa-save"></i> Save Changes
            </button>
        </div>

    </div>
</div>


</form>
@endsection
