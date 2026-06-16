@extends('admin.layout')

@section('content')
<div class="col-sm-6">
    <div class="page-title-box">
        <h4>Change Password</h4>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Change Password</li>
            </ol>
    </div>
</div>
<div class="card custom-card">
    <div class="card-header">
        <div class="card-title">
            Update your password.
        </div>
    </div>
    <div class="card-body">
        <div class="row  mt-4">
            <div class="col-md-12">
                <form method="POST" action="{{ route('admin.password.change') }}" class="">
                    @csrf
                    <div class="row mb-3">
                        <label for="old_password" class="col-md-4 col-form-label text-md-end">{{ __('Current Password') }}</label>
                        <div class="col-md-6 position-relative">
                            <input id="old_password" type="password" name="old_password" class="form-control @error('old_password') is-invalid @enderror" required autocomplete="off">
                            @error('old_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('New Password') }}</label>
                        <div class="col-md-6 position-relative">
                            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="off">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="password_confirmation" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                        <div class="col-md-6 position-relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required autocomplete="off">
                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3 justify-content-center">
                        <div class="col-sm-4">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
