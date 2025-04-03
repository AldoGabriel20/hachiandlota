@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Settings</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Settings</div>
                    </li>
                </ul>
            </div>
            <div class="wg-box">
                <form class="form-new-product form-style-1" method="POST" action="{{ route('admin.setting.update') }}">
                    @if(Session::has('status'))
                        <p class="alert alert-success">{{ Session::get('status') }}</p>
                    @endif
                    @csrf
                    @method('PUT')
                    <fieldset class="name">
                        <div class="body-title">Email <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Email" name="email" tabindex="0"
                            value="{{ old('email', $setting?->email) }}" aria-required="true" required="">
                    </fieldset>
                    @error('email') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title">Phone <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Phone" name="phone" tabindex="0"
                            value="{{ old('phone', $setting?->phone) }}" aria-required="true" required="">
                    </fieldset>
                    @error('phone') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title">Phone Second</div>
                        <input class="flex-grow" type="text" placeholder="Phone Second" name="phone_second" tabindex="0"
                            value="{{ old('phone_second', $setting?->phone_second) }}">
                    </fieldset>
                    @error('phone_second') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title">Address <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Address" name="address" tabindex="0"
                            value="{{ old('address', $setting?->address) }}" aria-required="true" required="">
                    </fieldset>
                    @error('address') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title">Map</div>
                        <input class="flex-grow" type="text" placeholder="Map" name="map" tabindex="0"
                            value="{{ old('map', $setting?->map) }}">
                    </fieldset>
                    @error('map') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title">Twitter</div>
                        <input class="flex-grow" type="text" placeholder="Twitter" name="twitter" tabindex="0"
                            value="{{ old('twitter', $setting?->twitter) }}">
                    </fieldset>
                    @error('twitter') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title">Instagram</div>
                        <input class="flex-grow" type="text" placeholder="Instagram" name="instagram" tabindex="0"
                            value="{{ old('instagram', $setting?->instagram) }}">
                    </fieldset>
                    @error('instagram') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title">Youtube</div>
                        <input class="flex-grow" type="text" placeholder="Youtube" name="youtube" tabindex="0"
                            value="{{ old('youtube', $setting?->youtube) }}">
                    </fieldset>
                    @error('youtube') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title">Pinterest</div>
                        <input class="flex-grow" type="text" placeholder="Pinterest" name="pinterest" tabindex="0"
                            value="{{ old('pinterest', $setting?->pinterest) }}">
                    </fieldset>
                    @error('pinterest') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror
                    <fieldset class="name">
                        <div class="body-title">Facebook</div>
                        <input class="flex-grow" type="text" placeholder="Facebook" name="facebook" tabindex="0"
                            value="{{ old('facebook', $setting?->facebook) }}">
                    </fieldset>
                    @error('facebook') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror

                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection