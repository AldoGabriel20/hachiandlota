@extends('layouts.app')
@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="my-account container">
            <h2 class="page-title">Address</h2>
            <div class="row">
                <div class="col-lg-3">
                    @include('user.account-nav')
                </div>
                <div class="col-lg-9">
                    <div class="page-content my-account__address">
                        <div class="row">
                            <div class="col-6">
                                <p class="notice">The following addresses will be used on the checkout page by default.</p>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('user.addresses') }}" class="btn btn-sm btn-danger">Back</a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-5">
                                    <div class="card-header">
                                        <h5>Add New Address</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('user.address.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="name"
                                                            value="{{ old('name') }}">
                                                        <label for="name">Full Name *</label>
                                                        <span class="text-danger"></span>
                                                    </div>
                                                </div>
                                                @error('name') <span
                                                class="alert alert-danger text-center">{{ $message }}</span> @enderror
                                                <div class="col-md-6">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="phone"
                                                            value="{{ old('phone') }}">
                                                        <label for="phone">Phone Number *</label>
                                                        <span class="text-danger"></span>
                                                    </div>
                                                </div>
                                                @error('phone') <span
                                                class="alert alert-danger text-center">{{ $message }}</span> @enderror
                                                <div class="col-md-4">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="zip"
                                                            value="{{ old('zip') }}">
                                                        <label for="zip">Pincode *</label>
                                                        <span class="text-danger"></span>
                                                    </div>
                                                </div>
                                                @error('zip') <span
                                                class="alert alert-danger text-center">{{ $message }}</span> @enderror
                                                <div class="col-md-4">
                                                    <div class="form-floating mt-3 mb-3">
                                                        <input type="text" class="form-control" name="state"
                                                            value="{{ old('state') }}">
                                                        <label for="state">State *</label>
                                                        <span class="text-danger"></span>
                                                    </div>
                                                </div>
                                                @error('state') <span
                                                class="alert alert-danger text-center">{{ $message }}</span> @enderror
                                                <div class="col-md-4">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="city"
                                                            value="{{ old('city') }}">
                                                        <label for="city">Town / City *</label>
                                                        <span class="text-danger"></span>
                                                    </div>
                                                </div>
                                                @error('city') <span
                                                class="alert alert-danger text-center">{{ $message }}</span> @enderror
                                                <div class="col-md-6">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="address"
                                                            value="{{ old('address') }}">
                                                        <label for="address">House no, Building Name *</label>
                                                        <span class="text-danger"></span>
                                                    </div>
                                                </div>
                                                @error('address') <span
                                                class="alert alert-danger text-center">{{ $message }}</span> @enderror
                                                <div class="col-md-6">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="locality"
                                                            value="{{ old('locality') }}">
                                                        <label for="locality">Road Name, Area, Colony *</label>
                                                        <span class="text-danger"></span>
                                                    </div>
                                                </div>
                                                @error('locality') <span
                                                class="alert alert-danger text-center">{{ $message }}</span> @enderror
                                                <div class="col-md-12">
                                                    <div class="form-floating my-3">
                                                        <input type="text" class="form-control" name="landmark"
                                                            value="{{ old('landmark') }}">
                                                        <label for="landmark">Landmark *</label>
                                                        <span class="text-danger"></span>
                                                    </div>
                                                </div>
                                                @error('landmark') <span
                                                class="alert alert-danger text-center">{{ $message }}</span> @enderror
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            id="isdefault" name="isdefault">
                                                        <label class="form-check-label" for="isdefault">
                                                            Make as Default address
                                                        </label>
                                                    </div>
                                                </div>
                                                @error('isdefault') <span
                                                class="alert alert-danger text-center">{{ $message }}</span> @enderror
                                                <div class="col-md-12 text-right">
                                                    <button type="submit" class="btn btn-success">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection