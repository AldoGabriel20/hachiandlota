@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <!-- main-content-wrap -->
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Edit User Role</h3>
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
                        <div class="text-tiny">Edit User Role</div>
                    </li>
                </ul>
            </div>
            <!-- new-category -->
            <div class="wg-box">
                <form class="form-new-product form-style-1" action="{{ route('admin.user.update.role') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $selected_user->id }}" />
                    <fieldset class="name">
                        <div class="body-title">Username</div>
                        <input class="flex-grow" type="text" placeholder="Name" name="name" tabindex="0"
                            value="{{ $selected_user->name }}" readonly>
                    </fieldset>

                    <fieldset class="utype">
                        <div class="body-title">Role<span class="tf-color-1">*</span></div>
                        <div class="select">
                            <select class="" name="utype" required="">
                                <option value="ADM" {{ $selected_user->utype == "ADM" ? "selected" : "" }}>Admin</option>
                                <option value="USR" {{ $selected_user->utype == "USR" ? "selected" : "" }}>User</option>
                            </select>
                        </div>
                    </fieldset>
                    @error('utype') <span class="alert alert-danger text-center">{{ $message }}</span> @enderror

                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">Save</button>
                    </div>
                </form>
            </div>
            <!-- /new-category -->
        </div>
        <!-- /main-content-wrap -->
    </div>
@endsection