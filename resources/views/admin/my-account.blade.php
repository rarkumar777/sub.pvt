@extends('admin.layouts.app')

@section('title', 'Admin | My Account')

@section('content')
<div class="row">
    <div class="sd-12 h-pad d-pad-t">
        <h2><i class="fa-user"></i> My Account</h2>
    </div>
</div>
<div class="row h-pad">
    {{-- Left Sidebar --}}
    <div class="md-3">
        <div class="bordered" style="border:1px solid #ddd;">
            <div class="align-center d-pad" style="background:#f5f5f5;">
                <img width="120" height="120" class="circle" src="{{ $user->avatar ?: asset('gogies3d/css/no_avatar.png') }}" alt="Avatar">
            </div>
            <div style="padding:0;">
                <a href="{{ route('admin.my-account') }}" class="block pad bordered-b" style="color:#333;">Edit Account</a>
                <a href="#" class="block pad bordered-b" style="color:#333;">Two Factor Authentication</a>
                <a href="#" class="block pad bordered-b" style="color:#333;">My Services</a>
                <a href="#" class="block pad" style="color:#333;">My Tours</a>
            </div>
        </div>
    </div>

    {{-- Right Content --}}
    <div class="md-9 h-pad-l">
        <h3><i class="fa-edit"></i> Edit Account</h3>
        <form action="{{ route('admin.my-account.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row h-gap-t">
                <div class="md-6 h-pad">
                    <label>First Name <span class="danger-text">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="sd-12 {{ $errors->has('first_name') ? 'tw-border-rose-500' : '' }}" required>
                    @error('first_name') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
                <div class="md-6 h-pad">
                    <label>Last Name <span class="danger-text">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="sd-12 {{ $errors->has('last_name') ? 'tw-border-rose-500' : '' }}">
                    @error('last_name') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row h-gap-t">
                <div class="md-6 h-pad">
                    <label>E-mail <span class="danger-text">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="sd-12 {{ $errors->has('email') ? 'tw-border-rose-500' : '' }}" required>
                    @error('email') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
                <div class="md-6 h-pad">
                    <label>URL</label>
                    <input type="text" name="url" value="{{ old('url', $user->url ?? '') }}" class="sd-12 {{ $errors->has('url') ? 'tw-border-rose-500' : '' }}">
                    @error('url') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row h-gap-t">
                <div class="md-6 h-pad">
                    <label>Country</label>
                    <select name="country" class="sd-12 {{ $errors->has('country') ? 'tw-border-rose-500' : '' }}">
                        <option value="">Select Country</option>
                        @if(isset($countries))
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ ($user->country ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('country') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
                <div class="md-6 h-pad">
                    <label>City</label>
                    <select name="city" class="sd-12 {{ $errors->has('city') ? 'tw-border-rose-500' : '' }}">
                        <option value="">City</option>
                        @if(isset($cities))
                            @foreach($cities as $ct)
                                <option value="{{ $ct->id }}" {{ ($user->city ?? '') == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('city') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row h-gap-t">
                <div class="md-6 h-pad">
                    <label>Company Name</label>
                    <input type="text" name="company" value="{{ old('company', $user->company ?? '') }}" class="sd-12 {{ $errors->has('company') ? 'tw-border-rose-500' : '' }}">
                    @error('company') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
                <div class="md-6 h-pad">
                    <label>Mobile</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile ?? '') }}" class="sd-12 {{ $errors->has('mobile') ? 'tw-border-rose-500' : '' }}">
                    @error('mobile') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row h-gap-t">
                <div class="md-6 h-pad">
                    <label>Telephone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $user->phone ?? '') }}" class="sd-12 {{ $errors->has('telephone') ? 'tw-border-rose-500' : '' }}">
                    @error('telephone') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
                <div class="md-6 h-pad">
                    <label>Fax</label>
                    <input type="text" name="fax" value="{{ old('fax', $user->fax ?? '') }}" class="sd-12 {{ $errors->has('fax') ? 'tw-border-rose-500' : '' }}">
                    @error('fax') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row h-gap-t">
                <div class="md-6 h-pad">
                    <label>Address</label>
                    <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}" class="sd-12 {{ $errors->has('address') ? 'tw-border-rose-500' : '' }}">
                    @error('address') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
                <div class="md-6 h-pad">
                    <label>Birth Date</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_day ?? '') }}" class="sd-12 {{ $errors->has('birth_date') ? 'tw-border-rose-500' : '' }}">
                    @error('birth_date') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row h-gap-t">
                <div class="md-6 h-pad">
                    <label>Gender</label>
                    <select name="gender" class="sd-12 {{ $errors->has('gender') ? 'tw-border-rose-500' : '' }}">
                        <option value="1" {{ ($user->gender ?? '') == 1 ? 'selected' : '' }}>Male</option>
                        <option value="2" {{ ($user->gender ?? '') == 2 ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
                <div class="md-6 h-pad">
                    <label>Avatar</label>
                    <input type="file" name="avatar" class="sd-12 {{ $errors->has('avatar') ? 'tw-border-rose-500' : '' }}" accept="image/*">
                    @error('avatar') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
            </div>

            <div class="row h-gap-t">
                <div class="md-6 h-pad">
                    <label>Password <small style="color:#999;">(leave blank to keep)</small></label>
                    <input type="password" name="password" class="sd-12 {{ $errors->has('password') ? 'tw-border-rose-500' : '' }}" placeholder="Unchanged" autocomplete="new-password">
                    @error('password') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
                <div class="md-6 h-pad">
                    <label>Retype Password <span class="danger-text">*</span></label>
                    <input type="password" name="password_confirmation" class="sd-12 {{ $errors->has('password_confirmation') ? 'tw-border-rose-500' : '' }}" placeholder="Unchanged" autocomplete="new-password">
                    @error('password_confirmation') <small style="color: #ef4444; display:block; margin-top:5px; font-weight:600; font-size:11px;"><i class="fa fa-exclamation-circle"></i> {{ $message }}</small> @enderror
                </div>
            </div>

            <div class="d-pad align-center">
                <button type="submit" class="btn blue"><i class="fa-check"></i> Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
