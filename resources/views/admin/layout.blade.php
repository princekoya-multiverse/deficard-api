<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>NE CARD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" href="{{ asset('dashboard_assets/assets/images/brand-logos/favicon.ico') }}" type="image/x-icon"> --}}
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body data-sidebar="dark"> <!-- <body data-layout="horizontal" data-topbar="colored"> -->
    <!-- Begin page -->
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="{{ url('admin/dashboard') }}" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="{{ asset('frontend_assets/assets/images/favicon.png') }}"
                                    alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset('frontend_assets/assets/images/Home-Page/Header-Logo.png') }}"
                                    alt="" height="40">
                            </span>
                        </a>
                        <a href="{{ url('admin/dashboard') }}" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="{{ asset('frontend_assets/assets/images/favicon.png') }}"
                                    alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset('frontend_assets/assets/images/Home-Page/Header-Logo.png') }}"
                                    alt="" height="40">
                            </span>
                        </a>
                    </div>
                    <button type="button"
                        class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn">
                        <i class="mdi mdi-menu"></i>
                    </button>
                </div>

                <div class="d-flex w-100">
                    <div class="page-title-box">
                        <ol class="breadcrumb m-0 mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            @foreach($breadcrumb ?? [] as $name => $route)
                                <li class="breadcrumb-item">
                                    <a href="{{ route($route) }}">{{ $name }}</a>
                                </li>
                            @endforeach
                            <li class="breadcrumb-item active">{{ $title ?? '' }}</li>
                        </ol>
                        <h3 class="m-0">{{ $title ?? '' }}</h3>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="dropdown d-none d-lg-inline-block">
                        <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                            <i class="mdi mdi-fullscreen font-size-24"></i>
                        </button>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user"
                                src="{{ asset('assets/images/users/user-4.jpg') }}" alt="Header Avatar">
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="{{ route('admin.password.change') }}"><i
                                    class="mdi mdi-lock-open-outline font-size-17 text-muted align-middle me-1"></i>
                                Change Password</a>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"><i
                                    class="mdi mdi-power font-size-17 text-muted align-middle me-1 text-danger"></i>
                                Logout</a>
                        </div>
                    </div>
                    {{--
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                            <i class="mdi mdi-spin mdi-cog"></i>
                        </button>
                    </div>
                    --}}
                </div>
            </div>
        </header>
        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title">Main</li>

                        <li class=" @if (request()->route()->getName() == 'admin.dashboard') mm-active @endif">
                            <a href="{{ route('admin.dashboard') }}"
                                class="waves-effect  @if (request()->route()->getName() == 'admin.dashboard') active @endif">
                                <i class="mdi mdi-view-dashboard"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class=" @if (request()->route()->getName() == 'admin.users') mm-active @endif">
                            <a href="{{ route('admin.users') }}"
                                class="waves-effect  @if (request()->route()->getName() == 'admin.users') active @endif">
                                <i class="mdi mdi-account"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        <li class=" @if (request()->route()->getName() == 'admin.card') mm-active @endif">
                            <a href="{{ route('admin.card') }}"
                                class="waves-effect  @if (request()->route()->getName() == 'admin.card') active @endif">
                                <i class="mdi mdi-card"></i>
                                <span>Card Applications</span>
                            </a>
                        </li>
                        <li class=" @if (request()->route()->getName() == 'admin.kyc') mm-active @endif">
                            <a href="{{ route('admin.kyc') }}"
                                class="waves-effect  @if (request()->route()->getName() == 'admin.kyc') active @endif">
                                <i class="mdi mdi-layers"></i>
                                <span>Kyc Applications</span>
                            </a>
                        </li>
                        <li class=" @if (request()->route()->getName() == 'admin.card.activation') mm-active @endif">
                            <a href="{{ route('admin.card.activation') }}"
                                class="waves-effect  @if (request()->route()->getName() == 'admin.card.activation') active @endif">
                                <i class="mdi mdi-credit-card-multiple"></i>
                                <span>Card Activations</span>
                            </a>
                        </li>
                        <li class=" @if (request()->route()->getName() == 'admin.card.load') mm-active @endif">
                            <a href="{{ route('admin.card.load') }}"
                                class="waves-effect  @if (request()->route()->getName() == 'admin.card.load') active @endif">
                                <i class="mdi mdi-credit-card"></i>
                                <span>Card Load</span>
                            </a>
                        </li>
                        <li class=" @if (request()->route()->getName() == 'admin.kyc.payment') mm-active @endif">
                            <a href="{{ route('admin.kyc.payment') }}"
                                class="waves-effect  @if (request()->route()->getName() == 'admin.kyc.payment') active @endif">
                                <i class="mdi mdi-cash"></i>
                                <span>Kyc Payments</span>
                            </a>
                        </li>
                        <li class=" @if (request()->route()->getName() == 'admin.support_ticket') mm-active @endif">
                            <a href="{{ route('admin.support_ticket') }}"
                                class="waves-effect  @if (request()->route()->getName() == 'admin.support_ticket') active @endif">
                                <i class="mdi mdi-email"></i>
                                <span>Support Tickets</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    @yield('content')

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            Copyright © {{ date('Y') }} All rights reserved.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    <div class="right-bar">
        <div data-simplebar class="h-100">
            <div class="rightbar-title d-flex align-items-center px-3 py-4">
                <h5 class="m-0 me-2">Settings</h5>
                <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                    <i class="mdi mdi-close noti-icon"></i>
                </a>
            </div>
            <!-- Settings -->
            <hr class="mt-0" />
            <div class="px-4 py-2">
                <h6 class="mb-3">Select Custome Colors</h6>
                <div class="form-check form-check-inline">
                    <input class="form-check-input theme-color" type="radio" name="theme-mode" id="theme-default"
                        value="default" onchange="document.documentElement.setAttribute('data-theme-mode', 'default')"
                        checked>
                    <label class="form-check-label" for="theme-default">Default</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input theme-color" type="radio" name="theme-mode" id="theme-red"
                        value="red" onchange="document.documentElement.setAttribute('data-theme-mode', 'red')">
                    <label class="form-check-label" for="theme-red">Red</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input theme-color" type="radio" name="theme-mode" id="theme-teal"
                        value="teal" onchange="document.documentElement.setAttribute('data-theme-mode', 'teal')">
                    <label class="form-check-label" for="theme-teal">Teal</label>
                </div>
            </div>


            <h6 class="text-center mb-0 mt-3">Choose Layouts</h6>

            <div class="p-4">
                <div class="mb-2">
                    <img src="{{ asset('assets/images/layouts/layout-1.jpg') }}" class="img-thumbnail"
                        alt="">
                </div>
                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input theme-choice" id="light-mode-switch" checked />
                    <label class="form-check-label" for="light-mode-switch">Light Mode</label>
                </div>

                <div class="mb-2">
                    <img src="{{ asset('assets/images/layouts/layout-2.jpg') }}" class="img-thumbnail"
                        alt="">
                </div>
                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input theme-choice" id="dark-mode-switch"
                        data-bsStyle="assets/css/bootstrap-dark.min.css') }}"
                        data-appStyle="assets/css/app-dark.min.html" />
                    <label class="form-check-label" for="dark-mode-switch">Dark Mode</label>
                </div>

                <div class="mb-2">
                    <img src="{{ asset('assets/images/layouts/layout-3.jpg') }}" class="img-thumbnail"
                        alt="">
                </div>
                <div class="form-check form-switch mb-5">
                    <input type="checkbox" class="form-check-input theme-choice" id="rtl-mode-switch"
                        data-appStyle="assets/css/app-rtl.min.css') }}" />
                    <label class="form-check-label" for="rtl-mode-switch">RTL Mode</label>
                </div>


            </div>

        </div> <!-- end slimscroll-menu-->
    </div>
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('assets/libs/morris.js/morris.min.js') }}"></script>
    <script src="{{ asset('assets/libs/raphael/raphael.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @yield('foot_scripts')
    <script type="text/javascript">
    $(function() {
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
    });
    </script>
</body>

</html>
