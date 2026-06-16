@extends('front.layout')

@section('content')
    <main>
        <section class="home-banner">
            <div class="container">
                <div class="d-flex flex-lg-nowrap justify-content-center flex-wrap">
                    <div class="banner-figure left-figure wow fadeInLeft d-none d-lg-block" data-wow-duration="2s">
                        <img src="{{ url('frontend_assets/assets/images/Home-Page/Black-Card-Banner.png') }}" alt="Card-Icon" />
                    </div>
                    <div class="col-lg-6 col-md-10">
                        <div class="banner-content text-center">
                            <h1 class="wow fadeInRight" data-wow-duration="1s">
                                The Key to Seamless Transactions
                            </h1>
                            <p class="wow fadeInRight" data-wow-duration="2s">
                                NE card embraces emerging technologies like blockchain, AI,
                                and IoT that can streamline processes, enhance security, and
                                offer innovative solutions for transactions.
                            </p>
                            <a href="#" @if(!auth()->check()) data-bs-toggle="modal" data-bs-target="#signInModal" @endif
                                class="btn btn-primary wow fadeInUp" data-wow-duration="2s">
                                <span>Get Started</span>
                            </a>
                            <div class="d-flex justify-content-center gap-2 mt-4">
                                <button class="btn app-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px"
                                        viewBox="0 0 24 24">
                                        <path fill="#fff"
                                            d="m22.018 13.298l-3.919 2.218l-3.515-3.493l3.543-3.521l3.891 2.202a1.49 1.49 0 0 1 0 2.594M1.337.924a1.5 1.5 0 0 0-.112.568v21.017c0 .217.045.419.124.6l11.155-11.087zm12.207 10.065l3.258-3.238L3.45.195a1.47 1.47 0 0 0-.946-.179zm0 2.067l-11 10.933c.298.036.612-.016.906-.183l13.324-7.54z" />
                                    </svg>
                                    <div class="text-left">
                                        <span class="text-uppercase">Get It On</span>
                                        <p>Google Play</p>
                                    </div>
                                </button>
                                <button class="btn app-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px"
                                        viewBox="0 0 24 24">
                                        <path fill="#fff"
                                            d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47c-1.34.03-1.77-.79-3.29-.79c-1.53 0-2 .77-3.27.82c-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51c1.28-.02 2.5.87 3.29.87c.78 0 2.26-1.07 3.81-.91c.65.03 2.47.26 3.64 1.98c-.09.06-2.17 1.28-2.15 3.81c.03 3.02 2.65 4.03 2.68 4.04c-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5c.13 1.17-.34 2.35-1.04 3.19c-.69.85-1.83 1.51-2.95 1.42c-.15-1.15.41-2.35 1.05-3.11" />
                                    </svg>
                                    <div class="text-left">
                                        <span>Download on the</span>
                                        <p>App Store</p>
                                    </div>
                                </button>
                            </div>
                            <div class="text-center mt-3">
                                <p class="coming-soon">Coming Soon...</p>
                            </div>
                        </div>
                    </div>
                    <div class="banner-figure right-figure wow fadeInLeft d-none d-lg-block" data-wow-duration="2s">
                        <img src="{{ url('frontend_assets/assets/images/Home-Page/Golden-Card-Banner.png') }}" alt="Card-Icon" />
                    </div>
                    <div class="banner-figure right-figure wow fadeInLeft d-lg-none" data-wow-duration="2s">
                        <img src="{{ url('frontend_assets/assets/images/Home-Page/mobile-Card-Banner.png') }}" alt="Card-Icon" />
                    </div>
                </div>
            </div>
        </section>
        <section class="ne-card">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="ne-card-figure text-center wow fadeInLeft" data-wow-duration="3s">
                            <img src="{{ url('frontend_assets/assets/images/Home-Page/Girl.png') }}" alt="girl" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ne-card-content">
                            <h2 class="ne-heading wow fadeInUp" data-wow-duration="1s">
                                NE Card
                            </h2>
                            <p class="wow fadeInUp" data-wow-duration="2s">
                                <b>We've revolutionized transactions with innovative, secure
                                    cards providing unparalleled flexibility & peace of mind.</b>
                            </p>
                            <p class="wow fadeInUp" data-wow-duration="3s">
                                Seamlessly handle everyday purchases, travel, & business
                                expenses with cutting-edge technology & robust security.
                                Empowering customers and foster inclusion, shaping a
                                convenient, rewarding, & transformative payment future.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="ne-payment-solutions">
            <div class="container">
                <div class="payement-cards">
                    <img src="{{ url('frontend_assets/assets/images/Home-Page/payment-solutions.png') }}" alt="payement cards" />
                </div>
                <div class="text-center">
                    <h2 class="ne-heading wow fadeInUp" data-wow-duration="2s">
                        NE Payment Solutions
                    </h2>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="single-payment wow fadeInUp" data-wow-duration="2s">
                            Powered by Visa Card
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-payment wow fadeInUp" data-wow-duration="2s">
                            Regulated & Secured
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-payment wow fadeInUp" data-wow-duration="2s">
                            No Credit Check
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-payment wow fadeInUp" data-wow-duration="2s">
                            No Monthly Fees
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-payment wow fadeInUp" data-wow-duration="2s">
                            Pin & Chip Payment Service
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-payment wow fadeInUp" data-wow-duration="2s">
                            24/7 Cash Withdrawals
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-payment wow fadeInUp" data-wow-duration="2s">
                            Usable Round the Globe
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-payment wow fadeInUp" data-wow-duration="2s">
                            Friendly User Panel
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-payment wow fadeInUp" data-wow-duration="2s">
                            Customer Service 24/7
                        </div>
                    </div>
                </div>
                <div class="text-center my-3">
                    <a href="#" @if(!auth()->check()) data-bs-toggle="modal" data-bs-target="#signUpModal" @endif
                        class="btn btn-primary wow fadeInUp" data-wow-duration="2s">
                        <span>Register Now</span>
                    </a>
                </div>
                <div class="d-flex justify-content-center payment-cards wow fadeInUp" data-wow-duration="2s">
                    <p class="coming-soon">It Works >></p>
                    <img src="{{ url('frontend_assets/assets/images/Home-Page/payment-cards.png') }}" alt="payment cards" />
                </div>
            </div>
        </section>
        <section class="testimonials">
            <div class="container">
                <div class="test-figure text-center">
                    <img src="{{ url('frontend_assets/assets/images/Home-Page/testimonial-figure.png') }}" alt="girl" class="mx-auto" />
                </div>
                <div class="text-center">
                    <h2 class="ne-heading wow fadeInUp" data-wow-duration="2s">
                        See Why Our Customers Love Us
                    </h2>
                </div>
            </div>
            <div class="testimonial-slider slick-slider slick-column-10 slick-dot-style slick-arrow-style wow fadeInUp"
                data-wow-duration="2s">
                @foreach($testimonials as $te)
                <div class="slide">
                    <div class="single-slider">
                        <p>
                            {{ $te['description']??'' }}
                        </p>
                        <h6>{{ $te['name']??'' }}</h6>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        <section class="ne-card-setup">
            <div class="child">
                <div class="container">
                    <div class="text-center">
                        <h2 class="ne-heading wow fadeInUp" data-wow-duration="2s">
                            Setup with these Easy Steps
                        </h2>
                    </div>
                    <div class="ne-card-steps-wrapper wow fadeInUp" data-wow-duration="2s">
                        <ul>
                            <li>
                                <div class="icon wow fadeInDown" data-wow-duration="2s">
                                    <img src="{{ url('frontend_assets/assets/images/Home-Page/Create-Account-Icon.png') }}" alt="account icon" />
                                </div>
                                <div class="ring"><span></span></div>
                                <div class="text-center steps-content">
                                    <h4 class="wow fadeInUp" data-wow-duration="3s">
                                        Create an Account
                                    </h4>
                                    <p class="wow fadeInUp" data-wow-duration="4s">
                                        Empowering Your Journey, One Account at a Time
                                    </p>
                                    <a href="#" class="wow fadeInUp" data-wow-duration="5s">Click Here</a>
                                </div>
                            </li>
                            <li>
                                <div class="icon wow fadeInDown" data-wow-duration="2s">
                                    <img src="{{ url('frontend_assets/assets/images/Home-Page/KYC-Account-Icon.png') }}" alt="KYC account icon" />
                                </div>
                                <div class="ring"><span></span></div>
                                <div class="text-center steps-content">
                                    <h4 class="wow fadeInUp" data-wow-duration="3s">
                                        KYC Your Account
                                    </h4>
                                    <p class="wow fadeInUp" data-wow-duration="4s">
                                        KYC Your Account: Trust, Security, Confidence
                                    </p>
                                    <a href="#" class="wow fadeInUp" data-wow-duration="5s">Click Here</a>
                                </div>
                            </li>
                            <li>
                                <div class="icon wow fadeInDown" data-wow-duration="2s">
                                    <img src="{{ url('frontend_assets/assets/images/Home-Page/Order-Card-Icon.png') }}" alt="Order icon" />
                                </div>
                                <div class="ring"><span></span></div>
                                <div class="text-center steps-content">
                                    <h4 class="wow fadeInUp" data-wow-duration="3s">
                                        Order Your Card
                                    </h4>
                                    <p class="wow fadeInUp" data-wow-duration="4s">
                                        Unlock Convenience, Order Your Card  Today!
                                    </p>
                                    <a href="#" class="wow fadeInUp" data-wow-duration="5s">Click Here</a>
                                </div>
                            </li>
                            <li>
                                <div class="icon wow fadeInDown" data-wow-duration="2s">
                                    <img src="{{ url('frontend_assets/assets/images/Home-Page/Activate-Card-Icon.png') }}" alt="Activate icon" />
                                </div>
                                <div class="ring"><span></span></div>
                                <div class="text-center steps-content">
                                    <h4 class="wow fadeInUp" data-wow-duration="3s">
                                        Activate Your Card
                                    </h4>
                                    <p class="wow fadeInUp" data-wow-duration="4s">
                                        Activate Your Card, Unleash Seamless Transactions.
                                    </p>
                                    <a href="#" class="wow fadeInUp" data-wow-duration="5s">Click Here</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="row justify-content-center wow fadeInUp" data-wow-duration="2s">
                        <div class="col-lg-7">
                            <div class="text-center mt-5">
                                <div class="card-figure text-center">
                                    {{--  <img src="{{ url('frontend_assets/assets/images/Home-Page/Card-Icon.png') }}" alt="card" /> --}}
                                </div>
                                <div class="mt-5">
                                    <h2 class="ne-heading mt-5">Join 1,000 + Happy Customers</h2>
                                    <p>
                                        NE card embraces emerging technologies like blockchain, AI, and IoT that can
                                        streamline processes, enhance security, and offer innovative solutions for
                                        transactions.
                                    </p>
                                </div>
                                <a href="#" class="btn btn-primary wow fadeInUp" data-wow-duration="2s">
                                    <span>Get Started</span>
                                </a>
                                <div class="d-flex justify-content-center gap-2 mt-4">
                                    <button class="btn app-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px"
                                            viewBox="0 0 24 24">
                                            <path fill="#fff"
                                                d="m22.018 13.298l-3.919 2.218l-3.515-3.493l3.543-3.521l3.891 2.202a1.49 1.49 0 0 1 0 2.594M1.337.924a1.5 1.5 0 0 0-.112.568v21.017c0 .217.045.419.124.6l11.155-11.087zm12.207 10.065l3.258-3.238L3.45.195a1.47 1.47 0 0 0-.946-.179zm0 2.067l-11 10.933c.298.036.612-.016.906-.183l13.324-7.54z" />
                                        </svg>
                                        <div class="text-left">
                                            <span class="text-uppercase">Get It On</span>
                                            <p>Google Play</p>
                                        </div>
                                    </button>
                                    <button class="btn app-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px"
                                            viewBox="0 0 24 24">
                                            <path fill="#fff"
                                                d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47c-1.34.03-1.77-.79-3.29-.79c-1.53 0-2 .77-3.27.82c-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51c1.28-.02 2.5.87 3.29.87c.78 0 2.26-1.07 3.81-.91c.65.03 2.47.26 3.64 1.98c-.09.06-2.17 1.28-2.15 3.81c.03 3.02 2.65 4.03 2.68 4.04c-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5c.13 1.17-.34 2.35-1.04 3.19c-.69.85-1.83 1.51-2.95 1.42c-.15-1.15.41-2.35 1.05-3.11" />
                                        </svg>
                                        <div class="text-left">
                                            <span>Download on the</span>
                                            <p>App Store</p>
                                        </div>
                                    </button>
                                </div>
                                <div class="text-center mt-3">
                                    <p class="coming-soon">Coming Soon...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
