@extends('frontend.layout')
@section('title', 'Edit Account')

@section('content')
<div id="main-contents">
    <div class="full-wdith grey pvt-pad-tb">
        <div class="wrap" style="max-width: 1200px;">
            <div class="row cell">
                
                {{-- Left Sidebar Account Menu --}}
                <div class="md-3" style="padding-right: 20px;">
                    <div class="white shadow-box" style="border-radius: 8px; overflow: hidden; margin-bottom: 30px;">
                        <div style="background: #a44b11; color: #fff; padding: 15px 20px; font-size: 18px; font-weight: bold; text-transform: uppercase;">
                            <i class="fa-user-circle-o"></i> My Account
                        </div>
                        <ul style="list-style:none; padding:10px 0; margin:0;">
                            <li>
                                <a href="/{{ $lang }}/users/account/my-bookings/" style="display:block; padding:12px 20px; color:#444; font-weight:600; text-decoration:none; border-left:4px solid transparent; transition:all 0.3s;" onmouseover="this.style.background='#f9f9f9'; this.style.color='#eb9950';" onmouseout="this.style.background='transparent'; this.style.color='#444';">
                                    <i class="fa-list-ul" style="width:25px; color:#bbb;"></i> My Bookings
                                </a>
                            </li>
                            <li>
                                <a href="/{{ $lang }}/users/account/my-messages/" style="display:block; padding:12px 20px; color:#444; font-weight:600; text-decoration:none; border-left:4px solid transparent; transition:all 0.3s;" onmouseover="this.style.background='#f9f9f9'; this.style.color='#eb9950';" onmouseout="this.style.background='transparent'; this.style.color='#444';">
                                    <i class="fa-comments" style="width:25px; color:#bbb;"></i> My Messages
                                </a>
                            </li>
                            <li>
                                <a href="/{{ $lang }}/users/account/edit-account/" style="display:block; padding:12px 20px; color:#eb9950; font-weight:600; text-decoration:none; background:#fff5eb; border-left:4px solid #eb9950;">
                                    <i class="fa-edit" style="width:25px; color:#eb9950;"></i> Edit Account
                                </a>
                            </li>
                            <li style="border-top:1px solid #eee; margin-top:10px; padding-top:10px;">
                                <a href="/{{ $lang }}/users/logout/" style="display:block; padding:12px 20px; color:#d9534f; font-weight:600; text-decoration:none; border-left:4px solid transparent; transition:all 0.3s;" onmouseover="this.style.background='#fdf3f2';" onmouseout="this.style.background='transparent';">
                                    <i class="fa-sign-out" style="width:25px;"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="md-9">
                    <div class="white shadow-box" style="margin-bottom:30px; min-height: 500px; border-radius: 8px; overflow: hidden;">

                        <div class="pvt-orange" style="padding: 15px 20px;">
                            <h2 style="font-size:22px; color:#fff; margin:0; text-transform: uppercase; font-weight: bold;">
                                <i class="fa-edit"></i> Edit Account
                            </h2>
                        </div>

                    @if(session('success'))
                        <div class="sd-12 pad" style="background:#dff0d8; color:#3c763d; border:1px solid #d6e9c6; margin-bottom:8px; border-radius:3px;">
                            <i class="fa-check"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        @foreach($errors->all() as $error)
                            <div class="sd-12 pad" style="background:#f2dede; color:#a94442; border:1px solid #ebccd1; margin-bottom:8px; border-radius:3px;">
                                <i class="fa-close"></i> {{ $error }}
                            </div>
                        @endforeach
                    @endif

                    <div class="sd-12 pad">
                        <form method="POST" action="/{{ $lang }}/users/account/edit-account/" enctype="multipart/form-data">
                            @csrf

                            {{-- First Name / Last Name --}}
                            <div class="row cell">
                                <div class="md-2"><label>First Name</label></div>
                                <div class="md-4">
                                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="full-width" required>
                                </div>
                                <div class="md-2"><label>Last Name</label></div>
                                <div class="md-4">
                                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="full-width" required>
                                </div>
                            </div>

                            {{-- Email / URL --}}
                            <div class="row cell">
                                <div class="md-2"><label>E-mail</label></div>
                                <div class="md-4">
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="full-width" required>
                                </div>
                                <div class="md-2"><label>URL</label></div>
                                <div class="md-4">
                                    <input type="text" name="url" value="{{ old('url', $user->url) }}" class="full-width">
                                </div>
                            </div>

                            {{-- Country / City --}}
                            <div class="row cell">
                                <div class="md-2"><label>Country</label></div>
                                <div class="md-4">
                                    <select name="country" class="full-width">
                                        @foreach($countries as $c)
                                            <option value="{{ $c->name }}" {{ (old('country', $user->country) == $c->name) ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md-2"><label>City</label></div>
                                <div class="md-4">
                                    <input type="text" name="city" value="{{ old('city', $user->city) }}" class="full-width">
                                </div>
                            </div>

                            {{-- Company / Mobile --}}
                            <div class="row cell">
                                <div class="md-2"><label>Company Name</label></div>
                                <div class="md-4">
                                    <input type="text" name="company" value="{{ old('company', $user->company) }}" class="full-width">
                                </div>
                                <div class="md-2"><label>Mobile</label></div>
                                <div class="md-4">
                                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" class="full-width">
                                </div>
                            </div>

                            {{-- Telephone / Fax --}}
                            <div class="row cell">
                                <div class="md-2"><label>Telephone</label></div>
                                <div class="md-4">
                                    <input type="text" name="telephone" value="{{ old('telephone', $user->phone) }}" class="full-width">
                                </div>
                                <div class="md-2"><label>Fax</label></div>
                                <div class="md-4">
                                    <input type="text" name="fax" value="{{ old('fax', $user->fax) }}" class="full-width">
                                </div>
                            </div>

                            {{-- Address / Birth Date --}}
                            <div class="row cell">
                                <div class="md-2"><label>Address</label></div>
                                <div class="md-4">
                                    <input type="text" name="address" value="{{ old('address', $user->address) }}" class="full-width">
                                </div>
                                <div class="md-2"><label>Birth Date</label></div>
                                <div class="md-4">
                                    <input type="date" name="birth_day" value="{{ old('birth_day', $user->birth_day) }}" class="full-width">
                                </div>
                            </div>

                            {{-- Gender / Avatar --}}
                            <div class="row cell">
                                <div class="md-2"><label>Gender</label></div>
                                <div class="md-4">
                                    <select name="gender" class="full-width">
                                        <option value="male" {{ (old('gender', $user->gender) == 'male') ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ (old('gender', $user->gender) == 'female') ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div class="md-2"><label>Avatar</label></div>
                                <div class="md-4">
                                    @if(!empty($user->avatar))
                                        <img src="/{{ $user->avatar }}" style="height:40px; margin-bottom:5px; border-radius:3px;"><br>
                                    @endif
                                    <input type="file" name="avatar" accept="image/*">
                                </div>
                            </div>

                            <hr style="border-color:#eee; margin:15px 0;">

                            {{-- Password --}}
                            <div class="row cell">
                                <div class="md-2"><label>Password</label></div>
                                <div class="md-4">
                                    <input type="password" name="password" class="full-width" placeholder="Unchanged">
                                </div>
                                <div class="md-2"><label>Retype Password</label></div>
                                <div class="md-4">
                                    <input type="password" name="retype_password" class="full-width" placeholder="Unchanged">
                                </div>
                            </div>

                            <hr style="border-color:#eee; margin:15px 0;">

                            {{-- Submit --}}
                            <div class="row cell" style="text-align:center;">
                                <button type="submit" class="btn blue" style="padding:10px 35px; font-size:16px;">
                                    <i class="fa-check"></i> Save
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
