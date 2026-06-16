<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('frontend_assets/assets/images/favicon.png') }}" />
    <title>NE Card</title>
    <link href="{{ url('frontend_assets/assets/css/bootstrap.min.css') }}?v=1" rel="stylesheet" type="text/css" />
    <link href="{{ url('frontend_assets/assets/css/font-awesome.min.css') }}?v=1" rel="stylesheet" type="text/css" />
    <link href="{{ url('frontend_assets/assets/css/animate.css') }}?v=1" rel="stylesheet" type="text/css" />
    <link href="{{ url('frontend_assets/assets/css/slick.min.css') }}?v=1" rel="stylesheet" type="text/css" />
    <link href="{{ url('frontend_assets/assets/css/style.css') }}?v=1" rel="stylesheet" type="text/css" />
    <script src="{{ url('frontend_assets/assets/js/jquery-3.6.0.min.js') }}?v=1"></script>
    <link href="{{ url('frontend_assets/assets/css/custom.css') }}?v=1" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @yield('head_scripts')
</head>

<body>
    <header class="header-area header-wide sticky">
        <div class="container position-relative">
            <div class="main-header d-lg-flex justify-content-between">
                <a href="/" class="logo">
                    <img src="{{ url('frontend_assets/assets/images/Home-Page/Header-Logo.png') }}" alt="logo" />
                </a>
                <div class="d-flex gap-xl-3 gap-2 align-items-center">
                    @if (auth()->check())
                    <div class="dropdown">
                        <div class="user-profile gap-2 d-flex align-items-center" id="profileDD"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="icon">
                                <i class="fa fa-user"></i>
                            </div>
                            {{ auth()->user()->name }}
                            <i class="fa fa-ellipsis-v"></i>
                        </div>
                        <ul class="dropdown-menu profile-dropdown dropdown-menu-right" aria-labelledby="profileDD">
                            <li>
                                <a href="{{ route('front.ne_card',  ['step' => 6]) }}">MY Account</a>
                            </li>
                            <li>
                                <a href="{{ route('front.ne_card', ['step' => 2]) }}">Card Payment</a>
                            </li>
                            <li>
                                <a href="{{ route('front.ne_card', ['step' => 5]) }}">KYC Verification</a>
                            </li>
                            <li>
                                <a href="{{ route('front.ne_card', ['step' => 3]) }}">Card Activation</a>
                            </li>
                            <li>
                                <a href="{{ route('front.ne_card', ['step' => 1]) }}">Card Load</a>
                            </li>
                            <li>
                                <a href="{{ route('front.ne_card', ['step' => 4]) }}">Card Transactions</a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}">Logout</a>
                            </li>
                        </ul>
                    </div>
                    @else
                        <button class="btn btn-default" data-bs-toggle="modal" data-bs-target="#signInModal">
                            <span> <i class="fa fa-user"></i>Sign In </span>
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#signUpModal">
                            <span> <i class="fa fa-sign-in"></i> Sign Up </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </header>
    {{-- main content start here  --}}
    @yield('content')
    <div class="support-ticket d-none">
        <div class="support-card-header px-3 py-3 d-flex justify-content-between">
            <p class="m-0 p-0">Support Ticket</p>
            <i class="fa-solid fa-xmark close-support-ticket" style="color: #fff;font-size: 24px;"></i>
        </div>
        <div class="support-card-body px-3">
            <div class="d-flex align-items-center justify-content-center h-100 w-100"
                style="max-width: 400px; margin: auto">
                @if(auth()->check())
                <form action="{{ route('support_ticket.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="text-center1">
                        <div class="form-group my-3">
                            <h6>Message</h6>
                            <textarea class="form-control tx_id" name="message" rows="4"
                                style="border: 2px dashed #585123 !important;font-size:14px;"></textarea>
                        </div>
                        {{--
                        <div class="file-input-box">
                            <div class="wrapper-file-input">
                                <label for="file_new_5" class="w-100">
                                    <div class="input-box" id="openFileInputBtn">
                                        <p id="file-input-button-x" class="file-input-button small" data-id="x">
                                            <i class="fa fa-plus"></i>
                                            Upload file (optional)
                                        </p>

                                        <input type="file" id="file_new_5" class="file-input visually-hidden"
                                            name="file" data-id="x" />
                                    </div>
                                </label>
                                <small>Upload supported file (Max 15MB)</small>
                                <p id="file-list-x"></p>
                            </div>
                        </div>
                        --}}
                        <button class="btn btn-primary float-end" style="width: 100px">
                            <span>Submit</span>
                        </button>
                    </div>
                </form>
                @else
                <p class="mt-3 text-primary">Please sign in your account first.</p>
                @endif
            </div>
        </div>
    </div>
    <footer id="footer">
        <div class="text-center">
            <a href="#" class="footer-logo wow fadeInUp" data-wow-duration="2s">
                <img src="{{ url('frontend_assets/assets/images/Home-Page/Footer-Logo.png') }}" alt="footer logo" />
            </a>
        </div>
        <div class="footer-widgets wow fadeInDown" data-wow-duration="2s">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="single-widget">
                            <div class="icon">
                                <img src="{{ url('frontend_assets/assets/images/Home-Page/Support-Icon-01.png') }}" />
                            </div>
                            <div class="content">
                                <h6>Customer Support</h6>
                                <a href="mailto:support@necard.io">Support@necard.io</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-widget">{{--
                            <div class="icon">
                                <img src="{{ url('frontend_assets/assets/images/Home-Page/Location-Icon-01.png') }}" />
                            </div>
                            <div class="content">
                                <h6>Address</h6>
                                <p>11720 Voyageur Way #8, Richmond, BCV6X 3G9, Canada</p>
                            </div>
                            --}}
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="social-widget">
                            <a href="#" class="fb-icon">
                                <img src="{{ url('frontend_assets/assets/images/icons/Facebook-Icon.png') }}" class="normal" />
                                <img src="{{ url('frontend_assets/assets/images/icons/Facebook-Hover-Icon.png') }}" class="hover" />
                            </a>
                            <a href="#" class="insta-icon">
                                <img src="{{ url('frontend_assets/assets/images/icons/Instagram-Icon.png') }}" class="normal" />
                                <img src="{{ url('frontend_assets/assets/images/icons/Instagram-Hover-Icon.png') }}" class="hover" />
                            </a>
                            <a href="#" class="twitter-icon">
                                <img src="{{ url('frontend_assets/assets/images/icons/Twitter-Icon.png') }}" class="normal" />
                                <img src="{{ url('frontend_assets/assets/images/icons/Twitter-Hover-Icon.png') }}" class="hover" />
                            </a>
                            <a href="#" class="yt-icon">
                                <img src="{{ url('frontend_assets/assets/images/icons/Youtube-Icon.png') }}" class="normal" />
                                <img src="{{ url('frontend_assets/assets/images/icons/Youtube-Hover-Icon.png') }}" class="hover" />
                            </a>
                            <a href="#" class="tt-icon">
                                <img src="{{ url('frontend_assets/assets/images/icons/Tiktok-Icon.png') }}" class="hover" />
                                <img src="{{ url('frontend_assets/assets/images/icons/Tiktok-Hover-Icon.png') }}" class="normal" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 d-flex justify-content-md-start justify-content-center">
                        <p>
                            Copyright © 2023 - {{ date('Y') }} <span>necard.io</span>. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="d-flex justify-content-md-end justify-content-center">
                            <a href="{{ route('terms.conditions') }}">Terms of Services</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <div class="modal ne-modal fade" id="signInModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="text-center mb-4">
                        <img src="{{ url('frontend_assets/assets/images/Home-Page/Header-Logo.png ')}}" logo />
                        <h4>Login</h4>
                    </div>
                    <form class="login-form" action="{{ route('login') }}" method="POST">
                    @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" />
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" />
                            <div class="btn-eye" id="showPassword">
                                <img src="{{ url('frontend_assets/assets/images/icons/eye.png') }}" class="normal" />
                                <img src="{{ url('frontend_assets/assets/images/icons/hide.png') }}" class="hover d-none" />
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="#" class="forget-password" data-bs-dismiss="modal" data-bs-toggle="modal"
                                data-bs-target="#forgotPasswordModal">
                                Forgot Password?
                            </a>
                        </div>
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="{{ config('app.recaptcha.site_key') }}"></div>
                            @error('g-recaptcha-response')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">
                                <span>Login</span>
                            </button>
                        </div>
                        <div class="text-center">
                            <p>
                                New to this Site?
                                <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal"
                                    data-bs-target="#signUpModal">
                                    Sign Up
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal ne-modal fade" id="signUpModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="text-center mb-4">
                        <img src="{{ url('frontend_assets/assets/images/Home-Page/Header-Logo.png') }}" logo />
                        <h4>Sign Up</h4>
                    </div>
                    <form class="login-form" action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="fName">First Name</label>
                            <input type="text" class="form-control" id="fName" name="first_name" />
                            @error('first_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="lName">Last Name</label>
                            <input type="text" class="form-control" id="lName" name="last_name" />
                            @error('last_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" />
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="register_pass">Password <span class="small text-muted">(Min 8 chracters)</span></label>
                            <input type="password" class="form-control" id="register_pass" name="password" />
                            <div class="btn-eye showPassword">
                                <img src="{{ url('frontend_assets/assets/images/icons/eye.png') }}" class="normal" />
                                <img src="{{ url('frontend_assets/assets/images/icons/hide.png') }}" class="hover d-none" />
                            </div>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="register_pass_conf">Confirm Password</label>
                            <input type="password" class="form-control" id="register_pass_conf" name="password_confirmation" />
                            <div class="btn-eye showPasswordConfirm">
                                <img src="{{ url('frontend_assets/assets/images/icons/eye.png') }}" class="normalConfirm" />
                                <img src="{{ url('frontend_assets/assets/images/icons/hide.png') }}" class="hoverConfirm d-none" />
                            </div>
                            @error('password_confirmation')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="g-recaptcha" data-sitekey="{{ config('app.recaptcha.site_key') }}"></div>
                            @error('g-recaptcha-response')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">
                                <span>Sign Up</span>
                            </button>
                        </div>
                        <div class="text-center">
                            <p>
                                Already a member?
                                <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal"
                                    data-bs-target="#signInModal">
                                    Login
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal ne-modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="text-center mb-4">
                        <img src="{{ url('frontend_assets/assets/images/Home-Page/Header-Logo.png') }}" logo />
                        <h4>Reset Password</h4>
                        <p>
                            Enter your login email and we'll send you al link to reset your
                            password
                        </p>
                    </div>
                    <form class="login-form" action="{{ route('password.email') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" />
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">
                                <span>Reset Password</span>
                            </button>
                        </div>
                        <div class="text-center">
                            <a href="#" class="forget-password mb-0 mt-3" data-bs-dismiss="modal"
                                data-bs-toggle="modal" data-bs-target="#signInModal">
                                Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="scroll-top">
        <i class="fa fa-angle-up"></i>
    </div>
    <div class="comments-icon">
        <i class="fa fa-comments"></i>
    </div>
    <script src="{{ url('frontend_assets/assets/js/bootstrap.bundle.min.js')}}?v=1"></script>
    <script src="{{ url('frontend_assets/assets/js/wow.min.js') }}?v=1"></script>
    <script src="{{ url('frontend_assets/assets/js/slick.min.js') }}?v=1"></script>
    <script src="{{ url('frontend_assets/assets/js/main.js') }}?v=1"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://kit.fontawesome.com/3caf391076.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"
        integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        @if (Session::has('message'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.success("{{ str_replace([chr(10),chr(13)], '', session('message')) }}");
        @endif
        @if (Session::has('error'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.error("{{ str_replace([chr(10),chr(13)], '', session('error')) }}");
        @endif
        @if (Session::has('info'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.info("{{ str_replace([chr(10),chr(13)], '', session('info')) }}");
        @endif
        @if (Session::has('warning'))
            toastr.options = {
                "closeButton": true,
                "progressBar": true
            }
            toastr.warning("{{ str_replace([chr(10),chr(13)], '', session('warning')) }}");
        @endif
        @if (Session::has('register_check'))
            $('#signUpModal').modal("show")
        @endif
        @if (Session::has('login_check'))
            $('#signInModal').modal("show")
        @endif

        $('.support-ticket').hide();
        $('.comments-icon').on('click', function() {
            $('.support-ticket').removeClass('d-none');
            $('.support-ticket').show("slide", {
                direction: "right"
            }, 500);
        });
        $('.close-support-ticket').on('click', function() {
            $('.support-ticket').hide("slide", {
                direction: "right"
            }, 500);
        });
    });
</script>
@yield('foot_scripts')
</body>
</html>
