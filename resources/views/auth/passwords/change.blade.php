@extends('front.layout')

@section('content')
<main>
    <div class="ne-card-tabs">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link custom-tab" href="{{ route('front.ne_card', ['step' => 1]) }}">Card Load</a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab" href="{{ route('front.ne_card', ['step' => 2]) }}">Card Payment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab" href="{{ route('front.ne_card', ['step' => 3]) }}">Card Activation</a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab" href="{{ route('front.ne_card', ['step' => 4]) }}">KYC Verification</a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab active" href="{{ route('front.ne_card', ['step' => 5]) }}">MY Account</a>
            </li>
        </ul>
        <div class="container">
            <div class="tab-content" id="myTabContent">
                <div class="row bb-1">
                    <div class="col-sm-12">
                        <h4 class="mb-1">Change Password</h4>
                        <p>{{ __('Please confirm your password before continuing.') }}</p>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-9 col-lg-8">
                        <form method="POST" action="{{ route('password.change') }}" class="ne-form pt-5">
                            @csrf
                            <div class="row mb-3">
                                <label for="old_password" class="col-md-4 col-form-label text-md-end">{{ __('Current Password') }}</label>
                                <div class="col-md-6 position-relative">
                                    <input id="old_password" type="password" name="old_password" class="form-control @error('old_password') is-invalid @enderror" required autocomplete="off">
                                    <div class="btn-eye showOldPassword">
                                        <img src="{{ asset('frontend_assets/assets/images/icons/eye.png') }}" class="normalOld">
                                        <img src="{{ asset('frontend_assets/assets/images/icons/hide.png') }}" class="hoverOld d-none">
                                    </div>
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
                                    <div class="btn-eye showPassword">
                                        <img src="{{ asset('frontend_assets/assets/images/icons/eye.png') }}" class="normal">
                                        <img src="{{ asset('frontend_assets/assets/images/icons/hide.png') }}" class="hover d-none">
                                    </div>
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
                                    <div class="btn-eye showPasswordConfirm">
                                        <img src="{{ asset('frontend_assets/assets/images/icons/eye.png') }}" class="normalConfirm">
                                        <img src="{{ asset('frontend_assets/assets/images/icons/hide.png') }}" class="hoverConfirm d-none">
                                    </div>
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-12 text-center">
                                    <button class="btn btn-primary" type="submit"><span>Change Password</span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<style>
.ne-form .form-control {
    padding-right: 2em;
}
.ne-form .btn-eye {
  background-color: transparent;
  position: absolute;
  right: 25px;
  bottom: 15px;
  cursor: pointer;
}
</style>
@endsection
