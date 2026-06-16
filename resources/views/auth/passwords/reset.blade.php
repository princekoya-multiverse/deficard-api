@extends('front.layout')

@section('content')
    <main>
        <section class="home-banner">
            <div class="container">
                <div class="row align-items-center flex-wrap-reverse flex-md-wrap">
                    <div class="col-md-6">
                        <div class="banner-content">
                            <h1>Reset Your Password</h1>
                            <p class="fst-italic">
                                NE card embraces emerging technologies like blockchain, AI,
                                and IoT that can streamline processes, enhance security, and
                                offer innovative solutions for transactions.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="banner-figure">
                            <img src="{{ asset('frontend_assets/assets/images/Home-Page/Card-Icon.png') }}"
                                alt="Card-Icon" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <div class="modal ne-modal fade" id="updatePasswordModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="text-center mb-4">
                        <img src="{{ asset('frontend_assets/assets/images/Home-Page/Header-Logo.png') }}" logo />
                        <h4>Update Password</h4>
                        <p>
                            Enter your email and new password
                        </p>
                    </div>
                    <form method="POST" action="{{ route('password.update') }}" class="login-form">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" />
                            @error('email')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="register_pass">Password</label>
                            <input type="password" class="form-control password" id="register_pass" name="password">
                            <div class="btn-eye showPassword">
                                <img src="{{ asset('frontend_assets/assets/images/icons/eye.png') }}" class="normal">
                                <img src="{{ asset('frontend_assets/assets/images/icons/hide.png') }}" class="hover d-none">
                            </div>
                            @error('password')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="register_pass">Confirm Password</label>
                            <input type="password" class="form-control password" id="register_pass"
                                name="password_confirmation">
                            <div class="btn-eye showPasswordConfirm">
                                <img src="{{ asset('frontend_assets/assets/images/icons/eye.png') }}" class="normalConfirm">
                                <img src="{{ asset('frontend_assets/assets/images/icons/hide.png') }}" class="hoverConfirm d-none">
                            </div>
                            @error('password_confirmation')
                                <span class="text-danger" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">
                                <span>Update Password</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#updatePasswordModal').modal("show");
        })
    </script>
@endsection
