@extends('front.layout')

@section('content')
<main>
    <div class="ne-card-tabs">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link custom-tab @if (request()->step == 6) active @endif" id="myAccount-tab"
                    data-bs-toggle="tab" data-step="6" href="#myAccount" role="tab" aria-controls="myAccount"
                    aria-selected="@if (request()->step == 6) true @else false @endif">Step 1: Account</a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab @if (request()->step == 2) active @endif" id="cardPayment-tab"
                    data-bs-toggle="tab" data-step="2" href="#cardPayment" role="tab" aria-controls="cardPayment"
                    aria-selected="@if (request()->step == 2) true @else false @endif">Step 2: Card Payment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab @if (request()->step == 5) active @endif" id="kycVerification-tab"
                    data-bs-toggle="tab" data-step="5" href="#kycVerification" role="tab"
                    aria-controls="kycVerification"
                    aria-selected="@if (request()->step == 5) true @else false @endif">Step 3: KYC Verification</a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab @if (request()->step == 3) active @endif" id="cardActivation-tab"
                    data-bs-toggle="tab" data-step="3" href="#cardActivation" role="tab"
                    aria-controls="cardActivation"
                    aria-selected="@if (request()->step == 3) true @else false @endif">Step 4: Card Activation</a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab @if (request()->step == 1) active @endif" id="cardLoad-tab"
                    data-bs-toggle="tab" data-step="1" href="#cardLoad" role="tab" aria-controls="cardLoad"
                    aria-selected="@if (request()->step == 1) true @else false @endif">Step 5: Card Load</a>
            </li>
            <li class="nav-item">
                <a class="nav-link custom-tab @if (request()->step == 4) active @endif" id="cardTransactions-tab"
                    data-bs-toggle="tab" data-step="4" href="#cardTransactions" role="tab"
                    aria-controls="cardTransactions"
                    aria-selected="@if (request()->step == 4) true @else false @endif">Card Transactions</a>
            </li>
        </ul>
        <div class="container">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade @if (request()->step == 1) show active @endif" id="cardLoad"
                    role="tabpanel" aria-labelledby="cardLoad-tab">
                    <div class="row">
                        <div class="col-md-8 col-lg-8">
                            <h4>Load Your NE Card Payment Card with USDT TRC20:</h4>
                            <h6>
                                To add funds to your NE Card Payment Card, simply follow
                                these easy steps:
                            </h6>
                            <ul>
                                <li>
                                    Scan the QRCode: Scan the provided QRCode using a
                                    compatible TRC20 wallet or application. Enter the amount
                                    you wish to load onto your card, ensuring it aligns with
                                    your available balance.
                                </li>
                                <li>
                                    Send TRC20 Amount: Send the specified TRC20 amount to the
                                    provided wallet address. Double-check the wallet address
                                    before proceeding to ensure accurate delivery of funds.
                                    Please note that any transaction fees associated with the
                                    TRC20 transfer will be the sender's responsibility.
                                </li>
                                <li>
                                    Upload Proof of USDT Sent: After completing the TRC20
                                    transfer, upload a clear photo or screenshot showing proof
                                    of the USDT transaction. This step helps us verify the
                                    transaction and process your card load efficiently. Ensure
                                    that the uploaded proof clearly displays the transaction
                                    details, including the sender's address, transaction hash,
                                    and transferred amount.
                                </li>
                                <li>
                                    Card Loading Timeframe: Once you have submitted the proof
                                    of USDT sent, our team will review and process your
                                    request. Please allow 24-48 hours for your card to be
                                    loaded with the specified amount. Rest assured, we are
                                    working diligently to ensure a prompt and accurate loading
                                    process.
                                </li>
                                <li>
                                    Receive Confirmation: You will receive a notification
                                    message as soon as your NE Payment Card has
                                    been successfully funded. This message will include the
                                    updated card balance and confirm the completion of the
                                    loading process.
                                </li>
                                <li>
                                    Receive Confirmation: You will receive a notification
                                    message as soon as your NE Payment Card has
                                    been successfully funded. This message will include the
                                    updated card balance and confirm the completion of the
                                    loading process.
                                </li>
                            </ul>
                            <p>
                                If you have any questions or require assistance during the
                                loading process, our dedicated support team is here to help.
                                We value your trust in NE Card and look forward to providing
                                you with a seamless and secure payment experience. Load your
                                NE Card Payment Card today and enjoy the convenience and
                                flexibility it offers. Welcome to a new era of digital
                                payments! Please note that loading times and procedures may
                                vary depending on network conditions and transaction
                                verification requirements.
                            </p>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            @if(isset($gateway_address) && !empty($gateway_address) && trim($gateway_address)!='')
                            <div class="qr-code" style="max-width: none">
                                <div class="mb-4 text-center"><img src="{{ $qr_code_path }}" class="mb-0 px-lg-5" /></div>
                                <p class="mt-3 text-center" style="overflow: visible">
                                    <a href="#{{ $gateway_address }}" class="text-decoration-none"
                                        data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Copied"
                                        id="copyLink">
                                        <span class="text-primary">{{ $gateway_address }}</span>
                                    </a>
                                </p>
                                <div class="text-center text-secondary small fst-italic">
                                    Click the text to copy the address.
                                </div>
                                {{--
                                <h6>Upload Proof of USDT</h6>
                                <div class="file-input-box">
                                    {{--
                                    <form action="{{ route('card.load') }}" method="post"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="wrapper-file-input">
                                            <label for="file_new_3" class="w-100">
                                                <div class="input-box" id="openFileInputBtn">
                                                    <p id="file-input-button-x" class="file-input-button"
                                                        data-id="x">
                                                        <i class="fa fa-plus"></i>
                                                        Upload file
                                                    </p>

                                                    <input type="file" id="file_new_3"
                                                        class="file-input visually-hidden" name="file"
                                                        data-id="x" />
                                                </div>
                                            </label>
                                            <div class="form-group my-3">
                                                <h6>TXID</h6>
                                                <input type="text" class="form-control tx_id" name="tx_id"
                                                    style="border: 2px dashed #585123 !important;"
                                                    value="{{ old('tx_id') }}">
                                                <input type="text" class="form-control tx_id" name="type"
                                                    value="load" hidden="">
                                            </div>
                                            <div class="form-group my-3">
                                                <h6>Cards</h6>
                                                <select name="card_id" id="" class="form-control tx_id"
                                                    name="card_id" style="border: 2px dashed #585123 !important;">
                                                    @foreach ($cards as $card)
                                                        <option value="{{ $card->id }}">{{ $card->number }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <small>Upload supported file (Max 15MB)</small>
                                            <p id="file-list-x"></p>
                                            <button class="btn btn-primary" style="width: 200px">
                                                <span>Submit</span>
                                            </button>
                                        </div>

                                    </form>

                                    <div class="wrapper-file-section">
                                        <div class="selected-files" id="selectedFiles" style="display: none">
                                            <h5>Selected Files</h5>
                                            <ul class="file-list"></ul>
                                        </div>
                                    </div>
                                </div>
                                --}}
                            </div>
                            @else
                            <div class="qr-code" style="max-width: none">
                                <h6 class="pt-5 fw-normal"><i class="fa fa-exclamation-triangle"></i> Please activate your card.</h6>
                            </div>
                            @endif
                        </div>
                        @if (count($card_datas) > 0)
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table" id="cardLoadTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Type</th>
                                                <th>Address</td>
                                                <th>Card To Load</th>
                                                <th>Amount</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $card_load_serial = 1;
                                            @endphp
                                            @foreach ($card_datas as $card_data)
                                                <tr>
                                                    <td>{{ $card_load_serial }}</td>
                                                    <td>{{ ucfirst($card_data->type) }}</td>
                                                    <td>@if($card_data->type == 'USDT' && $card_data->tx_id != 'Card Load')
                                                            {{ trim($card_data->trans_address)!='' ? $card_data->trans_address : $card_data->card?->number }}
                                                            <div class="text-secondary fst-italic pb-3"><span class="small">TXID:</span> {{ $card_data->tx_id }}</div>
                                                        @else
                                                            <div class="pb-3">{{ $card_data->tx_id }}</div>
                                                        @endif
                                                    </td>
                                                    <td>@if($card_data->type == 'USDT' && $card_data->trans_loaded == '1')
                                                            {{ $card_data->card?->number }}
                                                        @endif
                                                        @if($card_data->type == 'USDT' && $card_data->trans_loaded != '1')
                                                            <select name="card_id" class="form-select selectCard" name="card_id" data-id="{{ $card_data->id }}">
                                                                <option value="">-- Select --</option>
                                                                @foreach ($cards as $card)
                                                                    <option value="{{ $card->id }}" {{ $card->id == $card_data->card_id ? 'selected' : '' }}>{{ $card->number }}</option>
                                                                @endforeach
                                                            </select>
                                                            <div class="alert m-0 p-1"></div>
                                                        @endif
                                                    </td>
                                                    <td>@if($card_data->type == 'USDT')
                                                            <strong>{{ number_format(floatval($card_data->trans_amount) - floatval($card_data->trans_fee),2) }} USDT</strong>
                                                        @else
                                                            @if($card_data->file)
                                                            <img style="height: 75px;width: 75px;"
                                                                src="{{ asset('uploads/payment_files/' . $card_data->file) }}"
                                                                alt="">
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td> {{ $card_data->updated_at->format('d/m/Y') }}</td>
                                                    <td>{{ $card_data->status }}</td>
                                                </tr>
                                                @php
                                                    $card_load_serial++;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="tab-pane fade @if (request()->step == 2) show active @endif" id="cardPayment"
                    role="tabpanel" aria-labelledby="cardPayment-tab">
                    <h4>Welcome!</h4>
                    <p>
                        We're thrilled to have you on board, taking the first step
                        towards a seamless & secure payment experience. With our
                        state-of-the-art payment card, you can enjoy a world of
                        convenience, flexibility, and peace of mind. Let us guide you
                        through the simple process of obtaining your very own NE Card
                        Payment Card.
                    </p>
                    <h6>Get Started with NE Card Payment Card:</h6>
                    <ul>
                        <li>
                            Card Cost: For a one-time fee of {{ config('app.usdt-gateway-card-onetime-fee') }} USD, you'll gain access
                            to a range of exclusive benefits and features that will
                            revolutionize the way you handle your finances. Experience the
                            power of our cutting-edge payment technology by investing in
                            your NE Card Payment Card.
                        </li>
                        <li>
                            KYC Verification: We prioritize the security and
                            safety of our users. To ensure a seamless and trustworthy
                            experience, we require a Know Your Customer (KYC) verification
                            process. This helps us confirm your identity, protecting you
                            and others from potential fraud.
                        </li>
                        <li>
                            Load Fee: 10% (on the loaded amount). When adding funds to your
                            NE Card Payment Card, a load fee will be applied to the loaded
                            amount. This fee covers the cost of processing and managing
                            your transactions, ensuring the highest level of service and
                            security.
                        </li>
                    </ul>
                    <h5>Expected Delivery Time</h5>
                    <p>
                        Approximately 15 working days Once the KYC verification process
                        and payment verification are completed, we will initiate the
                        card production and mailing process. The estimated delivery time
                        for your card to reach your designated address is approximately
                        15 working days. We appreciate your patience during this period.
                        **Express Post Available: Exact pricing details can be obtained
                        from our customer support team.
                    </p>
                    <h5>Lost or Stolen Cards</h5>
                    <p>
                        In the unfortunate event of a lost or stolen card, it is
                        essential to take immediate action to protect your funds and
                        personal information. Please report any lost or stolen cards
                        immediately through our chat service or by emailing
                        <a href="mailto:support@necard.io">support@necard.io</a>. Our dedicated support team will guide
                        you
                        through the necessary steps to secure your account and issue a
                        replacement card. Replacement Card Fee: Should you require a
                        replacement card due to loss or theft, a replacement fee of $150
                        will apply. Additionally, the cost of mailing the replacement
                        card will be included. Our team will provide you with the exact
                        mailing cost based on your location.
                    </p>
                    <p>
                        At NE Card, we believe in transparency and putting our users'
                        needs first. Rest assured that the fees associated with
                        obtaining and activating your payment card go towards
                        maintaining the highest level of security, technological
                        innovation, and exceptional customer support. Obtain your NE
                        Card Payment Card today and experience the future of payments.
                        Together, we're redefining the way you spend, save, and manage
                        your finances.
                    </p>
                    <p>
                        If you have any questions or need assistance during the process,
                        our dedicated support team is here to help. Welcome to the NE
                        Card family!
                    </p>
                    <p>
                        <b><i>Note:</i></b> The fees mentioned above are accurate as of
                        the publication date and are subject to change without notice.
                    </p>
                    <a name="CardPaymentSelect"></a>

                    <div class="payment-plan mt-5">
                        <div class="row">
                            @if (count($card_payments) > 0)
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Transaction Details</th>
                                                    <th>File</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $card_payment_serial = 1;
                                                @endphp
                                                @foreach ($card_payments as $card_payment)
                                                    <tr>
                                                        <td scope="row">{{ $card_payment_serial }}</td>
                                                        <td>{{ $card_payment->tx_id }}</td>
                                                        <td>@if($card_payment->file)
                                                            <img style="height: 50px;width: 50px;"
                                                                src="{{ asset('uploads/payment_files/' . $card_payment->file) }}">
                                                            @endif
                                                        </td>
                                                        <td> {{ $card_payment->updated_at->format('d/m/Y') }}</td>
                                                        <td>{{ $card_payment->status }}</td>
                                                    </tr>
                                                    @php
                                                        $card_payment_serial++;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    {{-- @dump($pending_card_payment) --}}
                    @if(!isset($pending_card_payment) || empty($pending_card_payment))
                        {{-- if there any previous cards --}}
                        @if (count($card_payments) > 0)
                            <a href="#" class="btn btn-primary" onclick="javascript: jQuery(this).next().toggleClass('d-none'); return false;">Order New Card</a>
                        @endif
                        <div class="{{ (count($card_payments) > 0) ? 'd-none' : '' }}">
                            <h5 class="mt-4">Choose Your Card</h5>
                            <div class="row mt-5">
                                <div class="col-sm-12 col-md-8 offset-md-1">
                                    <div class="d-flex justify-content-between" id="cardPurchase">
                                        {{--
                                        <a href="#" class="btn btn-default border rounded-4" data-url="{{ route('necard.purchase', ['type' => 'Visa']) }}" data-cardtype="Visa card">
                                            <div class="d-flex flex-column text-center pt-3">
                                                <div class="h5 text-primary">Visa Card</div>
                                                <img style="max-width: 300px"
                                                    src="{{ asset('frontend_assets/assets/images/Visa-Black-Card-Icon.png') }}" />
                                            </div>
                                        </a>
                                        --}}
                                        <a href="#" class="btn btn-default border rounded-4" data-url="{{ route('necard.purchase', ['type' => 'Mastercard']) }}" data-cardtype="Mastercard">
                                            <div class="d-flex flex-column text-center pt-3">
                                                <div class="h5 text-primary">Master Card</div>
                                                <img style="max-width: 300px"
                                                    src="{{ asset('frontend_assets/assets/images/Mastercard-Black-Card-Icon.png') }}" />
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                {{--
                                <div class="col-md-6 col-lg-3">
                                    <div class="text-center qr-code d-none">
                                        <img
                                            src="{{ asset('frontend_assets/assets/images/Card-Payment/QR-Code-2.png') }}" />
                                        <p class="choco-clr">
                                            Scan the QR Code through your Trust Wallet or the link
                                            below
                                        </p>
                                        &nbsp;
                                        <button class="btn btn-primary d-none">
                                            <span>Let's Go</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-7 offset-1">
                                    <div class="d-flex align-items-center justify-content-center h-100 w-100">
                                        <form action="{{ route('payment.store') }}" method="POST"
                                            enctype="multipart/form-data" class="w-75">
                                            @csrf
                                            <div class="text-center">
                                                <h6>Please upload proof of payment with TXID</h6>
                                                <div class="form-group my-3">
                                                    <input type="text" class="form-control tx_id" name="tx_id"
                                                        style="border: 2px dashed #585123 !important;"
                                                        value="{{ old('tx_id') }}" required  />
                                                    <input type="text" class="form-control tx_id" name="type"
                                                        value="card" hidden />
                                                    <input type="text" name="step" value="2" hidden />
                                                </div>
                                                <div class="file-input-box">
                                                    <div class="wrapper-file-input">
                                                        <label for="file_new" class="w-100">
                                                            <div class="input-box" id="openFileInputBtn">
                                                                <p id="file-input-button-x" class="file-input-button"
                                                                    data-id="x">
                                                                    <i class="fa fa-plus"></i>
                                                                    Upload file
                                                                </p>

                                                                <input type="file" id="file_new"
                                                                    class="file-input visually-hidden" name="file"
                                                                    data-id="x" />
                                                            </div>
                                                        </label>
                                                        <small>Upload supported image file JPG or PNG (Max 5MB)</small>
                                                        <p id="file-list-x"></p>
                                                    </div>
                                                </div>
                                                <button class="btn btn-primary" style="width: 200px">
                                                    <span>Submit</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                --}}
                            </div>
                        </div>
                    @else
                        <h4>Pending Card Payment</h4>
                        <p class="">
                            Scan the QR Code through your phone camera external exchange wallet app.
                        </p>
                        <div class="row">
                            <div class="col-sm-6 col-md-10 offset-md-1">
                                <div class="row mt-4">
                                    <div class="col-4">
                                        <div class="h5 text-primary text-center">Your Selected Card</div>
                                        <img style="max-width: 300px"
                                            src="{{ asset('frontend_assets/assets/images/'.($pending_card_payment->card_type).'-Black-Card-Icon.png') }}" />
                                    </div>
                                    <div class="col-8 text-center">
                                        <h6 class="">Please verify the address is a TRC20 USDT</h6>
                                        <div class="qr-code" style="max-width: none">
                                            <div class="mb-4 text-center"><img src="{{ $card_payment_qr_code_path }}" class="mb-0 px-lg-5" /></div>
                                            <p class="mt-3 text-center" style="overflow: visible">
                                                <a href="#{{ $card_payment_qr_address }}" class="text-decoration-none"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Copied"
                                                    id="copyPaymentLink">
                                                    <span class="text-primary">{{ $card_payment_qr_address }}</span>
                                                </a>
                                            </p>
                                            <div class="text-center text-secondary small fst-italic">
                                                Click the text to copy the address.
                                            </div>
                                        </div>
                                        <p class="choco-clr"></p>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>

                <div class="tab-pane fade @if (request()->step == 3) show active @endif" id="cardActivation"
                    role="tabpanel" aria-labelledby="cardActivation-tab">
                    <h4>Activate Your Card</h4>
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="image">
                                <img
                                    src="{{ asset('frontend_assets/assets/images/Card-Activation/Card-Icon.png') }}" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <h5 class="position-relative mb-0 mt-4 pt-4">
                                You can find the KIT number on the back of your card
                                <img src="{{ asset('frontend_assets/assets/images/Card-Activation/Arrow.png') }}"
                                    class="arrow" />
                            </h5>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-sm-12 col-md-12 col-lg-10 col-xl-9">
                            <div class="kit-form">
                                <div class="d-flex">
                                    <div style="width: 27%;">
                                        <label>Card Number*</label>
                                        @error('number')
                                            <p class="text-danger m-0 p-0">Card Number is required</p>
                                        @enderror
                                    </div>
                                    <div style="width: 20%;">
                                        <label>Card Type*</label>
                                        @error('card_type')
                                            <p class="text-danger m-0 p-0">Card Type is required</p>
                                        @enderror
                                    </div>
                                    <div style="width: 27%;">
                                        <label>KIT Number</label>
                                        @error('kit_number')
                                            <p class="text-danger m-0 p-0">Kit Number is required</p>
                                        @enderror
                                    </div>
                                </div>

                                <form action="{{ route('card.active') }}" method="POST" class="d-flex" onsubmit="setLoading($(this).find('button')[0]);">
                                    @csrf
                                    <input type="text" placeholder="9999 9999 9999 9999" name="number" maxlength="19" value="{{ old('number') }}" />
                                    <select name="card_type" id="card_type" class="form-select w-50 me-3">
                                        <option value="">-- Select --</option>
                                        {{-- <option value="Visa">Visa card</option> --}}
                                        <option value="Mastercard">Mastercard</option>
                                    </select>
                                    <input type="text" placeholder="200000**********" name="kit_number" id="kit_number" value="{{ old('kit_number') }}" />
                                    <button type="submit" class="btn btn-primary">Activate</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @if (count($activation_card) > 0)
                        <div class="col-12 mt-3">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Card Type</th>
                                            <th>Card Number</th>
                                            <th>Kit Number</th>
                                            <th>Card Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $card_activation_serial = 1;
                                        @endphp
                                        @foreach ($activation_card as $activation)
                                            <tr>
                                                <td>{{ $card_activation_serial }}</td>
                                                <td>{{ $activation->card_type }}</td>
                                                <td>{{ $activation->number }}</td>
                                                <td>{{ $activation->kit_number }}</td>
                                                <td>{{ $activation->status }}
                                                    @if($activation->status == 'Approved')
                                                        <a href="{{ route('necard.deactivate'.($activation->card_type == 'Mastercard' ? '_mc' : ''), ['card_no' => $activation->id, 'step' => request()->step]) }}" class="btn btn-danger ms-3 text-white px-2" style="font-size:0.8em;" onclick="javascript: if (!this.disabled && confirm('Are you sure to deactivate this Card?')) { setLoading(this); return true; } else { return false; }">Deactivate</a>
                                                    @endif
                                                    @if($activation->status == 'Deactivated')
                                                        <a href="{{ route('necard.reactivate'.($activation->card_type == 'Mastercard' ? '_mc' : ''), ['card_no' => $activation->id, 'step' => request()->step]) }}" class="btn btn-primary ms-3 px-3" style="font-size:0.8em;" onclick="javascript: if (!this.disabled && confirm('Are you sure to reactivate this Card?')) { setLoading(this); return true; } else { return false; }">Reactivate</a>
                                                    @endif
                                                </td>
                                                <td> {{ $activation->updated_at->format('d/m/Y') }}</td>
                                            </tr>
                                            @php
                                                $card_activation_serial++;
                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="tab-pane fade @if (request()->step == 4) show active @endif" id="cardTransactions"
                    role="tabpanel" aria-labelledby="cardTransactions-tab">
                    <div class="row">
                        <div class="col-md-12 col-lg-6 col-xl-4"><h4>View Card Transactions</h4></div>
                        <div class="col-md-12 col-lg-6 col-xl-8">
                            <form method="post" action="{{ route('front.ne_card', ['step' => 4]) }}" onsubmit="setLoading($(this).find('button')[0]);">
                                <input type="hidden" name="step" value="4">
                                <input type="hidden" name="card_id" value="{{ $selectedCard ? $selectedCard->id : '' }}">
                            @csrf
                                Select Card:
                                <select name="card_selected" class="form-select d-inline-block w-50" id="cardSelect" onchange="javascript: $(this).parent('form')[0].submit();">
                                    <option value="">-- Select --</option>
                                    @foreach($cards as $card)
                                    <option data-cardno="{{ $card->number }}" value="{{ $card->id }}" {{ $selectedCard && $selectedCard->id == $card->id ? 'selected' : '' }}>{{ $card->number }} ({{ $card->card_type }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary d-none ms-2">Submit</button>
                            </form>
                        </div>
                    </div>
                    @if($selectedCard && strtolower(trim($selectedCard->card_type)) == 'mastercard')
                        <div class="row mb-4">
                            <div class="col-md-12 col-lg-6 col-xl-4">
                                <button class="btn btn-primary ms-4" data-bs-toggle="modal" data-bs-target="#changePinModalMC">Change Card PIN</button>
                            </div>
                            <div class="col-md-12 col-lg-6 col-xl-8">&nbsp;</div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-column">
                                <h6 class="fw-normal">{{ $selectedCard->card_type }} Card Balance: <strong>${{ number_format($card_balance,2) }}</strong></h6>
                                <h6 class="fw-normal">Account USDT Balance: <strong>${{ number_format($account_balance,2) }}</strong></h6>
                            </div>
                            <div class="text-end">If you are having any issue using your card please contact <a href="mailto:support@necard.io">support@necard.io</a>
                                {{-- <a class="fw-semibold" href="#" target="_blank">&quot;Transaction Parameter Release Authorization&quot;</a>. --}}
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-12 d-flex justify-content-around">
                                <button class="btn btn-primary" id="prevMonthTransMC">View {{ now()->subMonth()->format('F') }} Month Transactions</button>
                                &nbsp;
                                <button class="btn btn-primary" id="currentMonthTransMC">View {{ now()->format('F') }} Month Transactions</button>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-10 offset-1 table-responsive">
                                <table class="table table-hover table-striped table-sm table-bordered d-none" id="transTableMC">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Currency Code</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <div>Loading <i class="fa fa-spin fa-spinner"></i></div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if($selectedCard && strtolower(trim($selectedCard->card_type)) == 'visa')
                        <div class="row mb-4">
                            <div class="col-md-12 col-lg-6 col-xl-4">
                                <button class="btn btn-primary ms-4" data-bs-toggle="modal" data-bs-target="#changePinModal">Change Card PIN</button>
                            </div>
                            <div class="col-md-12 col-lg-6 col-xl-8">&nbsp;</div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-column">
                                <h6 class="fw-normal">{{ $selectedCard->card_type }} Card Balance: <strong>${{ number_format($card_balance,2) }}</strong></h6>
                                <h6 class="fw-normal">Account USDT Balance: <strong>${{ number_format($account_balance,2) }}</strong></h6>
                            </div>
                            <div class="text-end">If you are having any issue using your card please contact <a href="mailto:support@necard.io">support@necard.io</a>
                                {{--
                                <a class="fw-semibold" href="https://www.cognitoforms.com/ViaCarte1/transactionparameterreleaseauthorization" target="_blank">&quot;Transaction Parameter Release Authorization&quot;</a>.
                                --}}
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-12 d-flex justify-content-around">
                                <button class="btn btn-primary" id="prevMonthTrans">View {{ now()->subMonth()->format('F') }} Month Transactions</button>
                                &nbsp;
                                <button class="btn btn-primary" id="currentMonthTrans">View {{ now()->format('F') }} Month Transactions</button>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-10 offset-1 table-responsive">
                                <table class="table table-hover table-striped table-sm table-bordered d-none" id="transTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Currency Code</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <div>Loading <i class="fa fa-spin fa-spinner"></i></div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="tab-pane fade @if (request()->step == 5) show active @endif" id="kycVerification"
                    role="tabpanel" aria-labelledby="kycVerification-tab">
                    @if (!isset($card_payments) || empty($card_payments) || count($card_payments) <= 0)
                        <h4>Card Application Required</h4>
                        <div class="row mt-5">
                            <div class="col-sm-12">
                                <h6>Dear Member,</h6>
                                <p>Please complete your NE Card payment before applying for your KYC Verification.</p>
                            </div>
                            <div class="col-sm-12 mt-4">
                                <a class="card-application btn btn-primary" data-bs-toggle="tab"
                                    data-step="2" href="#cardLoad" role="tab" aria-controls="cardLoad"
                                    aria-selected="fals">Start Card Payment</a>
                            </div>
                        </div>
                    @else
                        @foreach($card_payments as $cp)
                            @php $approved_card_payment = false @endphp
                            @if($cp->status == 'Approved')
                                @php $approved_card_payment = true @endphp
                                @break;
                            @endif
                        @endforeach

                        @if(!$approved_card_payment)
                            <h4>Card Application Required</h4>
                            <div class="row mt-5">
                                <div class="col-sm-12">
                                    <h6>Dear Member,</h6>
                                    <p>Please complete your NE Card payment before applying for your KYC Verification.</p>
                                </div>
                                <div class="col-sm-12 mt-4">
                                    <a class="card-application btn btn-primary" data-bs-toggle="tab"
                                        data-step="2" href="#cardLoad" role="tab" aria-controls="cardLoad"
                                        aria-selected="fals">Start Card Payment</a>
                                </div>
                            </div>
                        @else
                            <h4>KYC Verification</h4>
                            @if (!empty($kyc))
                                @if ($kyc->status == 'In Process')
                                    <div class="mb-4 verified-box mx-auto d-flex justify-content-center flex-column align-items-center">
                                        <h4 class="mb-3">In Process</h4>
                                        @if(!empty($kyc->mastercard_kyc_url))
                                            <a href="{{ $kyc->mastercard_kyc_url }}" target="_blank" class="btn btn-primary text-decoration-none">Click here to complete your KYC</a>
                                        @endif
                                        @if(!empty($kyc->status_message))
                                        <div class="alert alert-info p-2 m-0">
                                            <i class="fa fa-exclamation-triangle me-2"></i>
                                            {{ $kyc->status_message }}
                                        </div>
                                        @endif
                                        <h5 class="mt-3">Your Kyc is in process.</h5>
                                        <i class="fa-regular fa-clock mt-3" style="color: #9C4D1F;font-size: 60px"></i>
                                    </div>
                                @endif
                                @if ($kyc->status == 'Approved')
                                    <div class="mb-4 verified-box mx-auto d-flex justify-content-center flex-column align-items-center">
                                        <h4 class="mb-1">Verified</h4>
                                        <h5 class="">Your Kyc has been verified.</h5>
                                        <i class="fa-regular fa-circle-check mt-3" style="color: #9C4D1F;font-size: 60px"></i>
                                    </div>
                                @endif
                                @if ($kyc->status == 'In Process' || $kyc->status == 'Approved')
                                    <div class="ne-form px-xl-5">
                                        <div class="row">
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>First Name*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->first_name }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Middle Name*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->middle_name }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Last Name*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->last_name }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Gender*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->gender!='' ? ($kyc->gender == 0 ? 'Male' : 'Female') : '' }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Nationality*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $country_iso_3_names[$kyc->nationality] ?? $kyc->nationality }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Place of Birth*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $country_iso_3_names[$kyc->place_of_birth] ?? $kyc->place_of_birth }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Birthday*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->birthday }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Email*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->email }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Phone No.*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->phone }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>City*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->city }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Street Address*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->street_address }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Street Address Line 2</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->street_address_2 }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Region/State/Province*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->region_state_province }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Postal / Zip Code*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $kyc->zipcode }}" />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Country*</label>
                                                    <input type="text"
                                                        class="border rounded-pill p-2 px-3 bg-light fw-bold" readonly
                                                        disabled value="{{ $country_iso_3_names[$kyc->country] ?? $kyc->country }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            {{--
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Government Issued Photo ID (Passport / Drivers License )* <span>- Front Side</span></label>
                                                    <div class="form-upload" style="overflow-x: auto">
                                                        @if ($kyc->file1)
                                                            <a href="{{ url('uploads/files/' . $kyc->file1) }}" class="btn" download="" target="_blank">
                                                                <i class="fa fa-download"></i>{{ preg_replace('"^(\d+)\."i', '', $kyc->file1) }}
                                                            </a>
                                                            {{ -- <a href="{{ url('uploads/files/' . $kyc->file1) }}" class="btn" download="" target="_blank">
                                                                @if(preg_match('/\.pdf$/', $kyc->file1))
                                                                    <i class="fa fa-download"></i>{{ preg_replace('"^(\d+)\."i', '', $kyc->file1) }}
                                                                @else
                                                                    <img src="{{ url('uploads/files/' . $kyc->file1) }}" />
                                                                @endif
                                                            </a> -- }}
                                                        @else
                                                            None
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Government Issued Photo ID (Passport / Drivers License )* <span>- Back Side</span></label>
                                                    <div class="form-upload mt-2" style="overflow-x: auto">
                                                        @if ($kyc->file2)
                                                            <a href="{{ url('uploads/files/' . $kyc->file2) }}" class="btn" download="" target="_blank">
                                                                <i class="fa fa-download"></i>{{ preg_replace('"^(\d+)\."i', '', $kyc->file2) }}
                                                            </a>
                                                        @else
                                                            None
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            --}}
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-group">
                                                    <label>Government Issued Utility Bill*</label>
                                                    <div class="form-upload">
                                                        @if ($kyc->file3)
                                                            <a href="{{ url('uploads/files/' . $kyc->file3) }}" class="btn btn-primary mt-lg-4" download="" target="_blank">
                                                                <i class="fa fa-download"></i>{{ preg_replace('"^(\d+)\."i', '', $kyc->file3) }}
                                                            </a>
                                                        @else
                                                            None
                                                        @endif
                                                        @switch($kyc->file3_type)
                                                            @case(5)
                                                                @php $file3_type_text = 'Credit Card Statement' @endphp
                                                                @break
                                                            @case(6)
                                                                @php $file3_type_text = 'Utility Bill' @endphp
                                                                @break
                                                            @case(7)
                                                                @php $file3_type_text = 'Bank Statement' @endphp
                                                                @break
                                                            @case(8)
                                                                @php $file3_type_text = 'Bank Letter' @endphp
                                                                @break
                                                            @default
                                                                @php $file3_type_text = '' @endphp
                                                        @endswitch
                                                        <div class="row border rounded-pill p-2 px-3 bg-light fw-bold mt-2">
                                                            <div class="col-sm-5">Type: </div>
                                                            <div class="col-sm-6">{{ $file3_type_text }}</div>
                                                        </div>
                                                        @if($kyc->file3_lang)
                                                        <div class="row border rounded-pill p-2 px-3 bg-light fw-bold mt-2">
                                                            <div class="col-sm-5">Language: </div>
                                                            <div class="col-sm-6">{{ $kyc->file3_lang }}</div>
                                                        </div>
                                                        @endif
                                                        @if($kyc->file3_issued_by)
                                                        <div class="row border rounded-pill p-2 px-3 bg-light fw-bold mt-2">
                                                            <div class="col-sm-5">Issued By: </div>
                                                            <div class="col-sm-6">{{ $kyc->file3_issued_by }}</div>
                                                        </div>
                                                        @endif
                                                        @if($kyc->file3_issued_date)
                                                        <div class="row border rounded-pill p-2 px-3 bg-light fw-bold mt-2">
                                                            <div class="col-sm-5">Issued Date: </div>
                                                            <div class="col-sm-6">{{ $kyc->file3_issued_date }}</div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($kyc->status == 'Rejected')
                                    {{--
                                    @if (count($kyc_payments) > 0)
                                        @php
                                            $payment_appove = false;
                                        @endphp
                                        @foreach ($kyc_payments as $item)
                                            @php
                                                if ($item->status == 'Approved') {
                                                    $payment_appove = true;
                                                }
                                            @endphp
                                        @endforeach
                                    @else
                                        @php
                                            $payment_appove = false;
                                        @endphp
                                    @endif
                                    --}}
                                    <div class="mb-4 verified-box mx-auto d-flex justify-content-center flex-column align-items-center">
                                        <h4 class="mb-3">Rejected</h4>
                                        @if(!empty($kyc->mastercard_kyc_url))
                                            <a href="{{ $kyc->mastercard_kyc_url }}" target="_blank" class="btn btn-primary text-decoration-none">Click here to complete your KYC</a>
                                        @endif
                                        @if(!empty($kyc->status_message))
                                        <div class="alert alert-danger p-2 m-0">
                                            <i class="fa fa-exclamation-triangle me-2"></i>
                                            {{ $kyc->status_message }}
                                        </div>
                                        @endif
                                        <h5 class="mt-3">Your Kyc verification has been rejected.</h5>
                                        {{-- @if ($payment_appove)
                                            <h5>Your Kyc verification rejected Fee is Approved</h5>
                                        @endif
                                        --}}
                                        <i class="fa-regular fa-circle-xmark mt-3" style="color: #9C4D1F;font-size: 60px"></i>
                                    </div>
                                @endif
                                @if ($kyc->status == 'Retry')
                                    <div class="mb-4 verified-box mx-auto d-flex justify-content-center flex-column align-items-center">
                                        <h4 class="mb-3">Retry</h4>
                                        @if(!empty($kyc->mastercard_kyc_url))
                                            <a href="{{ $kyc->mastercard_kyc_url }}" target="_blank" class="btn btn-primary text-decoration-none">Click here to complete your KYC</a>
                                        @endif
                                        @if(!empty($kyc->status_message))
                                        <div class="alert alert-warning p-2 m-0">
                                            <i class="fa fa-exclamation-triangle me-2"></i>
                                            {{ $kyc->status_message }}
                                        </div>
                                        @endif
                                        <h5 class="mt-3">Your KYC verification needs to be retried.</h5>
                                        <i class="fa-solid fa-repeat mt-3" style="color: #9C4D1F;font-size: 60px"></i>
                                    </div>
                                @endif
                            @endif

                            <!-- ------------------------------------------------------------------------------------------------------ -->

                            @if (empty($kyc) || $kyc->status == 'Rejected' || $kyc->status == 'Retry')
                                <form method="post" enctype="multipart/form-data" onsubmit="setLoading($(this).find('button')[0]);"
                                    @if ($kyc != '') action="{{ route('kyc.verification.update', $id = $kyc->id) }}"
                                        {{-- @if ($kyc->status == 'Rejected')
                                            @if (!$payment_appove) class="d-none" @endif
                                        @endif --}}
                                    @else
                                        action="{{ route('kyc.verification.save') }}" @endif>
                                    @csrf
                                    @if ($kyc != '')
                                        @method('put')
                                    @endif
                                    <div class="ne-form">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>First Name*</label>
                                                    <input type="text" class="form-control" placeholder="First name"
                                                        name="first_name" required
                                                        value="@if ($kyc) {{ $kyc->first_name }}@elseif(old('first_name') != ''){{ old('first_name') }}@else{{ auth()->user()->first_name }}@endif" />
                                                    @error('first_name')
                                                        <span class="text-danger">The first name is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Middle Name*</label>
                                                    <input type="text" class="form-control" placeholder="Middle name"
                                                        name="middle_name"
                                                        value="@if ($kyc != '') {{ $kyc->middle_name }}@elseif(old('middle_name') != ''){{ old('middle_name') }}@else{{ auth()->user()->middle_name }}@endif" />
                                                    @error('middle_name')
                                                        <span class="text-danger">Error in middle name</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Last Name*</label>
                                                    <input type="text" class="form-control" placeholder="Last name"
                                                        name="last_name" required
                                                        value="@if ($kyc != '') {{ $kyc->last_name }}@elseif(old('last_name') != ''){{ old('last_name') }}@else{{ auth()->user()->last_name }}@endif" />
                                                    @error('last_name')
                                                        <span class="text-danger">The last name is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Gender*</label>
                                                    <select class="form-control form-select" name="gender" required>
                                                        <option value="">Gender</option>
                                                        <option value="0"
                                                            {{ !empty($kyc) && $kyc->gender!='' && $kyc->gender == 0 ? ' selected ' : '' }}
                                                            {{ empty($kyc) && old('gender', '')!='' && intval(old('gender')) === 0 ? ' selected ' : '' }}>Male</option>
                                                        <option value="1"
                                                            {{ !empty($kyc) && $kyc->gender!='' && $kyc->gender == 1 ? ' selected ' : '' }}
                                                            {{ empty($kyc) && intval(old('gender')) === 1 ? ' selected ' : '' }}>Female</option>
                                                    </select>
                                                    @error('gender')
                                                        <span class="text-danger">The gender is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Nationality*</label>
                                                    <select class="form-control form-select" name="nationality" required>
                                                        <option value="">Nationality</option>
                                                        @foreach ($countries as $country)
                                                            <option
                                                                value="{{ $country->iso_code_3 }}"
                                                                {{ !empty($kyc) && !empty($kyc->nationality) && $kyc->nationality == $country->iso_code_3 ? ' selected ' : '' }}
                                                                {{ empty($kyc) && old('nationality') == $country->iso_code_3 ? ' selected ' : '' }}>
                                                                {{ $country->country_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('nationality')
                                                        <span class="text-danger">The nationality is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Place of Birth*</label>
                                                    <select class="form-control form-select" name="place_of_birth" required>
                                                        <option value="">Place of Birth</option>
                                                        @foreach ($countries as $country)
                                                            <option
                                                                value="{{ $country->iso_code_3 }}"
                                                                {{ !empty($kyc) && !empty($kyc->place_of_birth) && $kyc->place_of_birth == $country->iso_code_3 ? ' selected ' : '' }}
                                                                {{ empty($kyc) && old('place_of_birth') == $country->iso_code_3 ? ' selected ' : '' }}>
                                                                {{ $country->country_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('place_of_birth')
                                                        <span class="text-danger">The place of birth is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Birthday*</label>
                                                    <input type="date" class="form-control"
                                                        name="birthday" required
                                                        value="@if ($kyc != ''){{ $kyc->birthday }}@elseif(old('birthday') != ''){{ old('birthday') }}@else{{ auth()->user()->birthday }}@endif" />
                                                    @error('birthday')
                                                        <span class="text-danger">The birthday is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Email*</label>
                                                    <input type="email" class="form-control" placeholder="Email"
                                                        name="email" required
                                                        @if ($kyc != '') value="{{ $kyc->email }}" @else value="@if (old('email') != '') {{ old('email') }}@else{{ auth()->user()->email }}@endif" @endif />
                                                    @error('email')
                                                        <span class="text-danger">The email is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Phone No.* (without country code)</label>
                                                    <input type="text" class="form-control" placeholder="Phone"
                                                        name="phone" required
                                                        @if ($kyc != '') value="{{ $kyc->phone }}" @else value="@if (old('phone') != ''){{ old('phone') }}@else{{ auth()->user()->phone }}@endif" @endif />
                                                    @error('phone')
                                                        <span class="text-danger">The phone is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>City*</label>
                                                    <input type="text" class="form-control" placeholder="City"
                                                        name="city" required
                                                        @if ($kyc != '') value="{{ $kyc->city }}" @else value="{{ old('city') }}" @endif />
                                                    @error('city')
                                                        <span class="text-danger">The city is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Street Address*</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Street Address" required name="street_address"
                                                        @if ($kyc != '') value="{{ $kyc->street_address }}"  @else value="{{ old('street_address') }}" @endif />
                                                    @error('street_address')
                                                        <span class="text-danger">The street address is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Street Address Line 2</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Street Address Line 2"
                                                        name="street_address_2"
                                                        @if ($kyc != '') value="{{ $kyc->street_address_2 }}" @else value="{{ old('street_address_2') }}" @endif />
                                                    @error('street_address_2')
                                                        <span class="text-danger">The street address 2 is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Region/State/Province*</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Region/State/Province" required
                                                        name="region_state_province"
                                                        @if ($kyc != '') value="{{ $kyc->region_state_province }}" @else value="{{ old('region_state_province') }}" @endif />
                                                    @error('region_state_province')
                                                        <span class="text-danger">The Region/State/Province is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Postal / Zip Code*</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Post Code / Zip Code" name="zipcode" required
                                                        @if ($kyc != '') value="{{ $kyc->zipcode }}" @else value="{{ old('zipcode') }}" @endif />
                                                    @error('zipcode')
                                                        <span class="text-danger">The Postcode / Zipcode is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label>Country*</label>
                                                    <select class="form-control form-select" name="country" required>
                                                        <option value="">Country</option>
                                                        @foreach ($countries as $country)
                                                            <option
                                                                value="{{ $country->iso_code_3 }}"
                                                                {{ !empty($kyc) && !empty($kyc->country) && $kyc->country == $country->iso_code_3 ? 'selected' : '' }}
                                                                {{ empty($kyc) && old('country') == $country->iso_code_3 ? ' selected ' : '' }}>
                                                                {{ $country->country_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('country')
                                                        <span class="text-danger">The country is required</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{--
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Government Issued Photo ID (Passport / Drivers License )*</label>
                                                    <div class="form-upload" style="overflow-x: auto">
                                                        <label for="file-upload-1">
                                                            <i class="fa fa-plus"></i>Upload Photo ID (Front Side)
                                                            <input type="file" id="file-upload-1" class="file-upload" name="file1" />
                                                        </label>
                                                        <div class="d-flex justify-content-between" class="preview">
                                                            <small id="filename-1" class="filename">
                                                                @if (!empty($kyc) && $kyc->file1)
                                                                    <a href="{{ url('uploads/files/' . $kyc->file1) }}" class="btn" download="" target="_blank">
                                                                        <i class="fa fa-download"></i>{{ preg_replace('"^(\d+)\."i', '', $kyc->file1) }}
                                                                    </a>
                                                                @endif
                                                            </small>
                                                            <small>Upload image or PDF file (Max 5MB)</small>
                                                        </div>
                                                        @error('file1')
                                                            <span class="text-danger">Photo ID Frontside file is required.</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-upload mt-4" style="overflow-x: auto">
                                                        <label for="file-upload-2">
                                                            <i class="fa fa-plus"></i>Upload Photo ID (Back Side)
                                                            <input type="file" id="file-upload-2" class="file-upload" name="file2" id="file2" />
                                                        </label>
                                                        <div class="d-flex justify-content-between" class="preview">
                                                            <small id="filename-2" class="filename">
                                                                @if (!empty($kyc) && $kyc->file2)
                                                                    <a href="{{ url('uploads/files/' . $kyc->file2) }}" class="btn" download="" target="_blank">
                                                                        <i class="fa fa-download"></i>{{ preg_replace('"^(\d+)\."i', '', $kyc->file2) }}
                                                                    </a>
                                                                @endif
                                                            </small>
                                                            <small>Upload image or PDF file (Max 5MB)</small>
                                                        </div>
                                                        @error('file2')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            --}}
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>Government Issued Utility Bill*</label>
                                                    <div class="form-upload" style="overflow-x: auto">
                                                        <label for="file-upload-3"><i class="fa fa-plus"></i>Upload Utility Bill
                                                            <input type="file" id="file-upload-3" class="file-upload" name="file3" id="file3" />
                                                        </label>
                                                        <div class="d-flex justify-content-between" class="preview">
                                                            <small id="filename-3" class="filename">
                                                                @if (!empty($kyc) && $kyc->file3)
                                                                    <a href="{{ url('uploads/files/' . $kyc->file3) }}" class="btn btn-primary" download="" target="_blank">
                                                                        <i class="fa fa-download"></i>{{ preg_replace('"^(\d+)\."i', '', $kyc->file3) }}
                                                                    </a>
                                                                @endif
                                                            </small>
                                                            <small>Upload image or PDF file (Max 5MB)</small>
                                                        </div>
                                                        @error('file3')
                                                            <span class="text-danger">The utility bill upload file is
                                                                required</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="row">
                                                    <div class="form-group col-6">
                                                        <label>Document Language*</label>
                                                        <select class="form-control form-select" name="file3_lang" required>
                                                            <option value="">Language</option>
                                                                <option value="ENG" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='ENG'?'selected':''}}
                                                                    >English</option>
                                                                <option value="SPA" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='SPA'?'selected':''}}
                                                                    >Spanish</option>
                                                                <option value="FRA" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='FRA'?'selected':''}}
                                                                    >French</option>
                                                                <option value="ARA" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='ARA'?'selected':''}}
                                                                    >Arabic</option>
                                                                <option value="ZHO" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='ZHO'?'selected':''}}
                                                                    >Chinese</option>
                                                                <option value="DEU" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='DEU'?'selected':''}}
                                                                    >German</option>
                                                                <option value="HIM" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='HIM'?'selected':''}}
                                                                    >Hindi</option>
                                                                <option value="ITA" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='ITA'?'selected':''}}
                                                                    >Italian</option>
                                                                <option value="JPN" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='JPN'?'selected':''}}
                                                                    >Japanese</option>
                                                                <option value="KOR" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='KOR'?'selected':''}}
                                                                    >Korean</option>
                                                                <option value="POL" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='POL'?'selected':''}}
                                                                    >Polish</option>
                                                                <option value="POR" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='POR'?'selected':''}}
                                                                    >Portuguese</option>
                                                                <option value="RUS" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='RUS'?'selected':''}}
                                                                    >Russian</option>
                                                                <option value="TUR" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='TUR'?'selected':''}}
                                                                    >Turkish</option>
                                                                <option value="VIE" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='VIE'?'selected':''}}
                                                                    >Vietnamese </option>
                                                                <option value="OTH" {{!empty($kyc) && !empty($kyc->file3_lang) && $kyc->file3_lang=='OTH'?'selected':''}}>Other</option>
                                                        </select>
                                                        @error('file3_lang')
                                                            <span class="text-danger">Document language is required</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-6">
                                                        <label>Document Type*</label>
                                                        <select class="form-control form-select" name="file3_type" required>
                                                            <option value="">Type</option>
                                                            <option value="5" {{!empty($kyc) && !empty($kyc->file3_type) && $kyc->file3_type==5 ? 'selected' : ''}}
                                                                >Credit Card Statement</option>
                                                            <option value="6" {{!empty($kyc) && !empty($kyc->file3_type) && $kyc->file3_type==6 ? 'selected' : ''}}
                                                                >Utility Bill</option>
                                                            <option value="7" {{!empty($kyc) && !empty($kyc->file3_type) && $kyc->file3_type==7 ? 'selected' : ''}}
                                                                >Bank Statement</option>
                                                            <option value="8" {{!empty($kyc) && !empty($kyc->file3_type) && $kyc->file3_type==8 ? 'selected' : ''}}
                                                                >Bank Letter</option>
                                                        </select>
                                                        @error('file3_type')
                                                            <span class="text-danger">Document type is required</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-6">
                                                        <label>Issued By*</label>
                                                        <input type="text" class="form-control" name="file3_issued_by" required
                                                        @if ($kyc != '') value="{{ $kyc->file3_issued_by }}" @else value="{{ old('file3_issued_by') }}" @endif />
                                                        @error('file3_issued_by')
                                                            <span class="text-danger">Document Issued by is required</span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group col-6">
                                                        <label>Issue Date:*</label>
                                                        <input type="date" class="form-control" name="file3_issued_date" required max="{{ date('Y-m-d') }}" min="{{ date('Y-m-d', strtotime('-3 months')) }}"
                                                        @if ($kyc != '') id="file3_issued_date" value="{{ $kyc->file3_issued_date }}" @else value="{{ old('file3_issued_date') }}" @endif />
                                                        <div class="text-danger small">Document date should not be older than three months.</div>
                                                        @error('file3_issued_date')
                                                            <span class="text-danger">Document Issued date is required</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="checkbox-container">I agree to the terms & conditions.
                                                    <input type="checkbox" checked="checked" value="1" required />
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="">
                                                    <button class="btn btn-primary btn-submit" type="submit">
                                                        <span>{{ $kyc != '' ? 'Update' : 'Submit' }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @endif
                            {{--
                            <div class="payment-plan mt-4">
                                <div class="row">
                                    @if (count($kyc_payments) > 0)
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>TXID</th>
                                                            <th>File</th>
                                                            <th>Date</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @php
                                                            $kyc_payment_serial = 1;
                                                        @endphp
                                                        @foreach ($kyc_payments as $kyc_payment)
                                                            <tr>
                                                                <td scope="row">{{ $kyc_payment_serial }}</td>
                                                                <td>{{ $kyc_payment->tx_id }}</td>
                                                                <td><img style="height: 50px;width: 50px;"
                                                                        src="{{ asset('uploads/payment_files/' . $kyc_payment->file) }}"
                                                                        alt=""></td>
                                                                <td>{{ $kyc_payment->updated_at->format('d/m/Y') }}</td>

                                                                <td>{{ $kyc_payment->status }}</td>
                                                            </tr>
                                                            @php
                                                                $kyc_payment_serial++;
                                                            @endphp
                                                        @endforeach

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if ($kyc != '')
                                    @if ($kyc->status == 'Rejected')
                                        <h4>Choose Payment Option</h4>
                                        <div class="row">

                                            <div class="col-md-6 col-lg-3">
                                                <div class="text-center qr-code">
                                                    <img
                                                        src="{{ asset('frontend_assets/assets/images/Card-Payment/QR-Code-1.png') }}" />
                                                    <p>
                                                        Scan the QR Code through your external exchange wallet &
                                                        send $325 USDT.
                                                    </p>
                                                    <h6>Please verify the address is a TRC20:</h6>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3">
                                                <div class="text-center qr-code">
                                                    <img
                                                        src="{{ asset('frontend_assets/assets/images/Card-Payment/QR-Code-2.png') }}" />
                                                    <p class="choco-clr">
                                                        Scan the QR Code through your Trust Wallet or the link
                                                        below
                                                    </p>
                                                    <button class="btn btn-primary">
                                                        <span>Let's Go</span>
                                                    </button>
                                                    <p class="choco-clr">
                                                        TSgsEiMExwvvqVvtPQt9RhjcCqQGbbtUQj
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="d-flex align-items-center justify-content-center h-100 w-100"
                                                    style="max-width: 400px; margin: auto">
                                                    <form action="{{ route('payment.store') }}" method="POST"
                                                        enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="text-center">
                                                            <h6>Please upload proof of payment with TXID</h6>
                                                            <div class="form-group my-3">
                                                                <h6>TXID</h6>
                                                                <input type="text" class="form-control tx_id"
                                                                    name="tx_id"
                                                                    style="border: 2px dashed #585123 !important;"
                                                                    value="{{old('tx_id')}}" />
                                                                <input type="text" class="form-control tx_id"
                                                                    name="type" value="kyc" hidden />
                                                                <input type="text" name="step" value="4" hidden />
                                                            </div>
                                                            <div class="file-input-box">
                                                                <div class="wrapper-file-input">
                                                                    <label for="file_new_2" class="w-100">
                                                                        <div class="input-box" id="openFileInputBtn">
                                                                            <p id="file-input-button-x"
                                                                                class="file-input-button" data-id="x">
                                                                                <i class="fa fa-plus"></i>
                                                                                Upload file
                                                                            </p>

                                                                            <input type="file" id="file_new_2"
                                                                                class="file-input visually-hidden"
                                                                                name="file" data-id="x" />
                                                                        </div>
                                                                    </label>
                                                                    <small>Upload supported file (Max 15MB)</small>
                                                                    <p id="file-list-x"></p>
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-primary" style="width: 200px">
                                                                <span>Submit</span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            --}}
                        @endif
                    @endif
                </div>

                <div class="tab-pane fade  @if (request()->step == 6) show active @endif" id="myAccount"
                    role="tabpanel" aria-labelledby="myAccount-tab">
                    <div class="bb-1 pb-2">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-lg-12">
                                <h4 class="mb-1">Account</h4>
                                <p>View and edit your personal info below.</p>
                            </div>
                            {{-- <div class="col-lg-6">
                                <div class="d-flex justify-content-end gap-1">
                                    <button class="btn btn-default mr-2">
                                        <span>Discord</span>
                                    </button>
                                    <button class="btn btn-primary">
                                        <span>Update Info</span>
                                    </button>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <form action="{{ route('profile.update') }}" method="post">
                        @csrf
                        <div class="ne-form pt-4">
                            <h6>Your Profile</h6>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Display Name*</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ auth()->user()->name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Login Email:</label>
                                        <input type="text" class="form-control form-control-plaintext"
                                            name="email" value="{{ auth()->user()->email }}" readonly disabled />
                                        <span class="small text-muted fst-italic ps-2">Your login email can't b
                                            changed.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ne-form">
                            <h6>Personal Information</h6>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>First Name*</label>
                                        <input type="text" class="form-control" name="first_name" required value="{{ auth()->user()->first_name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Middle Name</label>
                                        <input type="text" class="form-control" name="middle_name" value="{{ auth()->user()->middle_name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Last Name*</label>
                                        <input type="text" class="form-control" name="last_name" required value="{{ auth()->user()->last_name }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="title" value="{{ auth()->user()->title }}" />
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Phone #</label>
                                        <input type="text" class="form-control" name="phone"
                                            value="{{ auth()->user()->phone }}" />
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between gap-1">
                                {{-- <button class="btn btn-default mr-2">
                                    <span>Discord</span>
                                </button> --}}
                                <button class="btn btn-primary" type="submit"><span>Update Info</span></button>
                                <a href="{{ route('password.change') }}" role="button"
                                    class="btn btn-secondary text-white">Change Password</a>
                            </div>
                        </div>
                    </form>
                    {{--
                    <div class="ne-form py-3">
                        <h6>Visibility and privacy</h6>
                        <p>Update your personal information.</p>

                        <div class="accordion" id="necard">
                            <div class="card bb-1">
                                <div class="card-header" id="necardhead1">
                                    <a href="#" class="btn-header-link" data-bs-toggle="collapse"
                                        data-bs-target="#profilePrivacy" aria-expanded="true"
                                        aria-controls="profilePrivacy">Profile Privacy</a>
                                </div>

                                <div id="profilePrivacy" class="collapse show" aria-labelledby="necardhead1"
                                    data-bs-parent="#necard">
                                    <div class="card-body">
                                        <p>
                                            Hide your profile page, and social aspects of your
                                            account.
                                        </p>
                                        <a href="#">Make Profile Public</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="necard2">
                                    <a href="#" class="btn-header-link collapsed" data-bs-toggle="collapse"
                                        data-bs-target="#blockedMembers" aria-expanded="false"
                                        aria-controls="blockedMembers">Blocked
                                        Members</a>
                                </div>

                                <div id="blockedMembers" class="collapse" aria-labelledby="necard2"
                                    data-bs-parent="#necard">
                                    <div class="card-body">
                                        Anim pariatur cliche reprehenderit, enim eiusmod high
                                        life accusamus terry richardson ad squid. 3 wolf moon
                                        officia aute, non cupidatat skateboard dolor brunch.
                                        Food truck quinoa nesciunt laborum eiusmod. Brunch 3
                                        wolf moon tempor, sunt aliqua put a bird on it squid
                                        single-origin coffee nulla assumenda shoreditch et.
                                        Nihil anim keffiyeh helvetica, craft beer labore wes
                                        anderson cred nesciunt sapiente ea proident. Ad vegan
                                        excepteur butcher vice lomo. Leggings occaecat craft
                                        beer farm-to-table, raw denim aesthetic synth nesciunt
                                        you probably haven't heard of them accusamus labore
                                        sustainable VHS.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changePinModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="changePinModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header text-white py-2" style="background: linear-gradient(90deg, #7d2cfd, #d91cc2)">
                <h1 class="modal-title fs-5" id="changePinModalLabel">Change Card PIN</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('necard.change_pin') }}" method="post" id="changePinForm" autocomplete="off">
                    @csrf
                    <input type="hidden" name="card_id" id="cardId">
                    <div class="form-group mb-3">
                        <label for="currentPin">Your Visa Card Number</label>
                        <input type="text" style="letter-spacing: 1px;" class="form-control fw-bold form-control-lg" id="cardNo" name="card_no" required autocomplete="off" disabled readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="newPin">New PIN</label>
                        <input type="text" style="letter-spacing: 1px;" class="form-control fw-bold form-control-lg" id="newPin" name="new_pin" required maxlength="4" autocomplete="off">
                        <div class="small text-danger fst-italic">Maximum 4 Digits only.</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="confirmPin">Confirm New PIN</label>
                        <input type="text" style="letter-spacing: 1px" class="form-control fw-bold form-control-lg" id="confirmPin" name="confirm_pin" required maxlength="4" autocomplete="off">
                        <div class="small text-danger fst-italic">Maximum 4 Digits only.</div>
                    </div>
                    <button type="submit" class="btn btn-primary btnSubmit">Change PIN</button>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary d-none">Submit</button>
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changePinModalMC" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="changePinModalMCLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header text-white py-2" style="background: linear-gradient(90deg, #7d2cfd, #d91cc2)">
                <h1 class="modal-title fs-5" id="changePinModalMCLabel">Change Card PIN</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('necard.change_pin_mc') }}" method="post" id="changePinFormMC" autocomplete="off">
                    @csrf
                    <input type="hidden" name="card_id" id="cardId">
                    <div class="form-group mb-3">
                        <label for="currentPin">Your Master Card Number</label>
                        <input type="text" style="letter-spacing: 1px;" class="form-control fw-bold form-control-lg" id="cardNo" name="card_no" required autocomplete="off" disabled readonly>
                    </div>
                    <div class="form-group mb-3">
                        <label for="oldPin">Last 4 Digits Of Your Card / Your Old PIN</label>
                        <input type="text" style="letter-spacing: 1px;" class="form-control fw-bold form-control-lg" id="oldPin" name="old_pin" required maxlength="4" autocomplete="off">
                        <div class="small text-danger fst-italic">Maximum 4 Digits only.</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="newPin">New PIN</label>
                        <input type="text" style="letter-spacing: 1px;" class="form-control fw-bold form-control-lg" id="newPin" name="new_pin" required maxlength="4" autocomplete="off">
                        <div class="small text-danger fst-italic">Maximum 4 Digits only.</div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="confirmPin">Confirm New PIN</label>
                        <input type="text" style="letter-spacing: 1px" class="form-control fw-bold form-control-lg" id="confirmPin" name="confirm_pin" required maxlength="4" autocomplete="off">
                        <div class="small text-danger fst-italic">Maximum 4 Digits only.</div>
                    </div>
                    <button type="submit" class="btn btn-primary btnSubmit">Change PIN</button>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-end">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary d-none">Submit</button>
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmCardPurchaseModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmCardPurchaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white py-2" style="background: linear-gradient(90deg, #7d2cfd, #d91cc2)">
                    <h1 class="modal-title fs-5" id="confirmCardPurchaseModalLabel">New Card Order</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 h6">
                            Are you sure to initiate a new purchase of <span class="cardType"></span>?
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <a href="#" class="btn btn-primary _url" onclick="javascript: $(this).attr('disabled', 'disabled');">Confirm</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</main>
<script type="text/javascript">
    $(document).ready(function() {
        $('#card_type').on('change', function() {
            let cardType = $(this).val();
            if (cardType === 'Visa') {
                $('#kit_number').addClass('fst-italic').removeClass('d-none').val('').removeAttr('disabled');
            } else if (cardType === 'Mastercard') {
                $('#kit_number').addClass('text-muted').addClass('fst-italic').val('Not required').attr('disabled', 'disabled');
            }
        });
        $('#cardPurchase a').on('click', function(e) {
            e.preventDefault();
            let _url = $(this).data('url');
            let _cardType = $(this).data('cardtype');
            $('#confirmCardPurchaseModal').find('._url').attr('href', _url);
            $('#confirmCardPurchaseModal').find('.cardType').text(_cardType);
            $('#confirmCardPurchaseModal').modal('show');
        });
        $('#changePinModal').on('show.bs.modal', function(e) {
            var cardNo = $('#cardSelect option:selected').data('cardno');
            var cardId = $('#cardSelect option:selected').prop('value');
            $(this).find('#cardNo').val(cardNo);
            $(this).find('#cardId').val(cardId);
        });
        $('#changePinModal').on('hide.bs.modal', function(e) {
            $(this).find('#cardNo').val('');
            $(this).find('#cardId').val('');
        });
        $('#changePinModalMC').on('show.bs.modal', function(e) {
            var cardNo = $('#cardSelect option:selected').data('cardno');
            var cardId = $('#cardSelect option:selected').prop('value');
            $(this).find('#cardNo').val(cardNo);
            $(this).find('#cardId').val(cardId);
        });
        $('#changePinModalMC').on('hide.bs.modal', function(e) {
            $(this).find('#cardNo').val('');
            $(this).find('#cardId').val('');
        });
        $("#changePinForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            form.children('button').prop('disabled', true).html('Change PIN <i class="ms-2 fa fa-spin fa-spinner"></i>')
            var actionUrl = form.attr('action');
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(), // serializes the form's elements.
            }).done(function(data) {
                //console.log(data);
                alert(data); // show response from the php script.
                $('#changePinModal').modal('hide');
                form.find('input').val('');
            }).fail(function(xhr, err) {
                var responseText = xhr.responseJSON.message;
                alert('Failed to change PIN: ' + "\n\n" + responseText);
            }).always(function() {
                form.find('button').prop('disabled', false).html('Change PIN');
            });
        });
        $('#prevMonthTrans').on('click', function() {
            $('#prevMonthTrans').prop('disabled', true);
            $('#transTable').removeClass('d-none');
            $('#transTable tfoot').show();
            $('#transTable thead').hide();
            $('#transTable tbody').html('');
            $.ajax({
                url: "{{ route('necard.transactions') }}",
                type: 'POST',
                data: { selectedCard: $('#cardSelect').val(), month: 'prev', _token: '{{ csrf_token() }}' },
            }).done(function($data) {
                $('#transTable thead').show();
                $('#transTable tbody').html($data);
            }).fail(function() {
                $('#transTable tbody').html('<tr><td colspan="6">Failed to load transactions, please try again.</td></tr>');
            }).always(function() {
                $('#transTable tfoot').hide();
                $('#prevMonthTrans').prop('disabled', false);
            });
        });
        $('#currentMonthTrans').on('click', function() {
            $('#currentMonthTrans').prop('disabled', true);
            $('#transTable').removeClass('d-none');
            $('#transTable thead').hide();
            $('#transTable tfoot').show();
            $('#transTable tbody').html('');
            $.ajax({
                url: "{{ route('necard.transactions') }}",
                type: 'POST',
                data: { selectedCard: $('#cardSelect').val(), month: 'current', _token: '{{ csrf_token() }}' },
            }).done(function($data) {
                $('#transTable thead').show();
                $('#transTable tbody').html($data);
            }).fail(function() {
                $('#transTable tbody').html('<tr><td colspan="6">Failed to load transactions, please try again.</td></tr>');
            }).always(function() {
                $('#transTable tfoot').hide();
                $('#currentMonthTrans').prop('disabled', false);
            });
        });
        $('.custom-tab').on('click', function() {
            let step = $(this).data('step');
            window.history.pushState({}, '', `?step=${step}`);
        });
        $('.card-application').on('click', function() {
            $('.nav-tabs a[data-step="2"]').tab('show');
            let step = 2;
            window.history.pushState({}, '', `?step=${step}`);
            window.setTimeout(function() {
                window.scrollTo(0, document.body.scrollHeight);
            }, 350);
        });
        $('#copyLink').on('click', function(e) {
            e.preventDefault();
            const options = {
                placement: 'bottom',
                trigger: 'manual',
            }
            const _url = $(this).attr('href').replace('#','');
            try {
                navigator.clipboard.writeText(_url);
            } catch (error) {
                console.log(error);
            }
            const elem = $(this).get(0);
            const tooltip = new bootstrap.Tooltip(elem, options);
            tooltip.toggle();
            return false;
        });
        $('#copyPaymentLink').on('click', function(e) {
            e.preventDefault();
            const options = {
                placement: 'bottom',
                trigger: 'manual',
            }
            const _url = $(this).attr('href').replace('#','');
            try {
                navigator.clipboard.writeText(_url);
            } catch (error) {
                console.log(error);
            }
            const elem = $(this).get(0);
            const tooltip = new bootstrap.Tooltip(elem, options);
            tooltip.toggle();
            return false;
        });
        $("#changePinFormMC").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            form.children('button').prop('disabled', true).html('Change PIN <i class="ms-2 fa fa-spin fa-spinner"></i>')
            var actionUrl = form.attr('action');
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(), // serializes the form's elements.
            }).done(function(data) {
                //console.log(data);
                alert(data); // show response from the php script.
                $('#changePinModalMC').modal('hide');
                form.find('input').val('');
            }).fail(function(xhr, err) {
                var responseText = xhr.responseJSON.message;
                alert('Failed to change PIN: ' + "\n\n" + responseText);
            }).always(function() {
                form.find('button').prop('disabled', false).html('Change PIN');
            });
        });
        $('#prevMonthTransMC').on('click', function() {
            $('#prevMonthTransMC').prop('disabled', true);
            $('#transTableMC').removeClass('d-none');
            $('#transTableMC tfoot').show();
            $('#transTableMC thead').hide();
            $('#transTableMC tbody').html('');
            $.ajax({
                url: "{{ route('necard.transactions_mc') }}",
                type: 'POST',
                data: { selectedCard: $('#cardSelect').val(), month: 'prev', _token: '{{ csrf_token() }}' },
            }).done(function($data) {
                $('#transTableMC thead').show();
                $('#transTableMC tbody').html($data);
            }).fail(function() {
                $('#transTableMC tbody').html('<tr><td colspan="6">Failed to load transactions, please try again.</td></tr>');
            }).always(function() {
                $('#transTableMC tfoot').hide();
                $('#prevMonthTransMC').prop('disabled', false);
            });
        });
        $('#currentMonthTransMC').on('click', function() {
            $('#currentMonthTransMC').prop('disabled', true);
            $('#transTableMC').removeClass('d-none');
            $('#transTableMC thead').hide();
            $('#transTableMC tfoot').show();
            $('#transTableMC tbody').html('');
            $.ajax({
                url: "{{ route('necard.transactions_mc') }}",
                type: 'POST',
                data: { selectedCard: $('#cardSelect').val(), month: 'current', _token: '{{ csrf_token() }}' },
            }).done(function($data) {
                $('#transTableMC thead').show();
                $('#transTableMC tbody').html($data);
            }).fail(function() {
                $('#transTableMC tbody').html('<tr><td colspan="6">Failed to load transactions, please try again.</td></tr>');
            }).always(function() {
                $('#transTableMC tfoot').hide();
                $('#currentMonthTransMC').prop('disabled', false);
            });
        });
        $('#cardLoadTable .selectCard').on('change', function(e) {
            e.preventDefault();
            let cardId = $(this).val();
            let transId = $(this).data('id');
            const msg = $(this).next('div');
            if(parseInt(transId) > 0) {
                let _url = "{{ route('update-usdt-payment-with-card', ['id' => ':id']) }}";
                _url = _url.replace(':id', transId);
                msg.removeClass('d-none');
                msg.html('<i class="fa fa-spin fa-spinner"></i>');
                $.ajax({
                    url: _url,
                    type: 'POST',
                    data: { card_id: cardId, _token: '{{ csrf_token() }}' },
                }).done(function($data) {
                    msg.toggleClass('alert-success');
                    msg.removeClass('alert-danger');
                    msg.html('<i class="fa fa-check"></i> ' + $data.message);
                }).fail(function(xhr, err) {
                    responseText = xhr.responseJSON.message;
                    msg.removeClass('alert-success');
                    msg.addClass('alert-danger');
                    msg.html('<i class="fa fa-times"></i> ' + responseText);
                }).always(function() {
                    window.setTimeout(function() {
                        msg.addClass('d-none');
                        msg.removeClass('alert-success');
                        msg.removeClass('alert-danger');
                    }, 5000);
                });
            }
        });
        //
        $("#file-upload-1, #file-upload-2, #file-upload-3").on('change', function(e) {
            let fileInput = $(this);
            //console.log(fileInput);
            let _file = fileInput[0].files[0];
            if(fileInput.closest('div').children('canvas').length > 0) {
                fileInput.closest('div').children('canvas').remove();
            }
            if(fileInput.closest('div').children('img').length > 0) {
                fileInput.closest('div').children('img').remove();
            }
            if (_file && _file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    let _img = document.createElement('img');
                    _img.setAttribute('src', e.target.result);
                    fileInput.closest('div').append(_img);
                };
                reader.readAsDataURL(_file);
            }
            //
            if (_file && _file.type === 'application/pdf') {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const typedarray = new Uint8Array(e.target.result);
                    pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
                        // Render the first page
                        pdf.getPage(1).then(function (page) {
                            const viewport = page.getViewport({ scale: 1 });
                            const canvas = document.createElement('canvas');
                            canvas.setAttribute('style', 'border: 1px solid #ccc');
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            const renderContext = {
                                canvasContext: context,
                                viewport: viewport
                            };
                            let _parent = fileInput.closest('div').append(canvas);
                            page.render(renderContext);
                        });
                    });
                };
                reader.readAsArrayBuffer(_file);
            }
        });
        //
    });
</script>
@endsection
@section('head_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
@endsection
