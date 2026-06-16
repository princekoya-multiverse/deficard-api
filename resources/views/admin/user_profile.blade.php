@extends('admin.layout', ['title' => 'User Profile', 'breadcrumb' => ['Users' => 'admin.users']])

@section('content')
    <div class="row">
        <div class="col-xl-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card" id="userCard">
                <div class="card-header">
                    <h4 class="card-title">{{ ucwords(strtolower($user->first_name.' '.$user->last_name)) }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- first column -->
                        <div class="col-lg-6">

                            <div class="col-md-6 h3 text-warning">Profile</div>
                            <table class="table table-sm table-striped">
                                <tr>
                                    <td width="25%"><strong>First Name:</strong></td>
                                    <td width="75%">{{ ucwords(strtolower($user->first_name)) }}</td>
                                </tr>
                                <tr>
                                    <td width="25%"><strong>Middle Name:</strong></td>
                                    <td width="75%">{{ ucwords(strtolower($user->middle_name)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Name:</strong></td>
                                    <td>{{ ucwords(strtolower($user->last_name)) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $user->email }}
                                        <button class="btn btn-primary btn-sm btn-toggle-next">Change Email</button>
                                        <form method="post" action="{{ route('admin-users-update-email', ['id' => $user->id]) }}" class="d-none mt-1">
                                            @csrf
                                            <input type="text" name="new_email" class="form-control" placeholder="" size="10">
                                            <input type="submit" class="btn btn-primary btn-sm ms-1" value="Save">
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $user->phone }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Registered At:</strong></td>
                                    <td>{{ $user->created_at?->format('d M, Y') }}</td>
                                </tr>
                            </table>

                            <div class="col-md-6 h3 text-warning mt-5">Card Activations</div>
                            <table class="table table-sm table-striped" id="tableCardActivations">
                                <tr>
                                    <th>Card Number</th>
                                    <th>Card Holder ID</th>
                                    <th>Card ID</th>
                                    <th>Kit Number</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                                @if($card_activations)
                                @foreach ($card_activations as $item)
                                <tr>
                                    <td><strong>{{ $item->number }}</strong></td>
                                    <td><input type="text" class="chid form-control p-1" data-id="{{ $item->id }}" value="{{ $item->card_holder_id }}"></td>
                                    <td><input type="text" class="cid form-control p-1" data-id="{{ $item->id }}" value="{{ $item->card_id }}"></td>
                                    <td><strong>{{ $item->kit_number }}</strong></td>
                                    <td><strong>{{ $item->card_type }}</strong></td>
                                    <td><?php
                                        if ($item->status == 'In Process') { $class = 'btn-secondary';
                                        } elseif ($item->status == 'Approved') { $class = 'btn-success';
                                        } elseif ($item->status == 'Rejected') { $class = 'btn-danger';
                                        } else { $class = 'btn-danger';
                                        }
                                        ?>
                                        <div class="btn-group">
                                            @if($item->status != 'Blocked')
                                                <button type="button"
                                                    class="btn {{ $class }} btn-sm dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <strong>{{ $item->status }}</strong>
                                                    <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <ul class="dropdown-menu" style="">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.card.activation.update', ['id' => $item->id, 'status' => 'Approved']) }}">Approved</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.card.activation.update', ['id' => $item->id, 'status' => 'Rejected']) }}">Rejected</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.card.activation.update', ['id' => $item->id, 'status' => 'In Process']) }}">In Process</a>
                                                    </li>
                                                </ul>
                                            @else
                                                <span class="btn bg-danger-subtle px-4">Blocked</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </table>

                            <div class="col-md-6 h3 text-warning mt-5">Progress</div>
                            <form method="post" action="{{ route('admin.card.updateProgress', ['user_id' => $user->id]) }}">
                                @csrf
                                <table class="table table-striped table-bordered table-sm">
                                    <tr>
                                        <th width="25%"><div class="pt-1">Select Status:</div></th>
                                        <td><select class="form-select" name="progress_status">
                                                <option value="" selected>-----</option>
                                                <option value="KYC Started">KYC Started</option>
                                                <option value="KYC Rejected">KYC Rejected</option>
                                                <option value="KYC Approved">KYC Approved</option>
                                                <option value="KYC Retried">KYC Retried</option>
                                                <option value="Card Issued">Card Issued</option>
                                                <option value="Card Mailed">Card Mailed</option>
                                                <option value="Card Returned">Card Returned</option>
                                                <option value="Card Lost">Card Lost</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Notes/Details: <span class="text-mutted small">(Optional)</span></th>
                                        <td>
                                            <textarea id="progress_details" class="form-control w-100 px-2 py-1" rows="4" name="progress_details"></textarea>
                                            <div class="mt-2">
                                                <label>
                                                    <input type="checkbox" name="notify_user" value="1" class="form-check-input" id="notify_user">
                                                    Email user about progress
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <td><input type="submit" value="Submit" class="btn btn-primary"></td>
                                    </tr>
                                </table>
                            </form>
                            @if($user_progress)
                            @foreach($user_progress as $up)
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th class="bg-info" width="25%">Status</th>
                                    <td class="bg-info fw-bold">{{ $up->progress_status }}</td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <th>{{ $up->updated_at->format('d M, Y H:i a') }}</th>
                                </tr>
                                <tr>
                                    <th>Details</th>
                                    <td>{{ $up->details }}</td>
                                </tr>
                            </table>
                            @endforeach
                            @endif
                        </div>

                        <!-- second column -->
                        <div class="col-lg-6">
                            <div class="col-md-6 h3 text-warning">KYC</div>
                            <table class="table table-sm table-striped" id="tableKYC">
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><?php
                                        $class='';
                                        if ($kyc?->status == 'In Process') { $class = 'btn-secondary';
                                        } elseif ($kyc?->status == 'Approved') { $class = 'btn-success';
                                        } elseif ($kyc?->status == 'Rejected') { $class = 'btn-danger';
                                        } elseif ($kyc?->status == 'Retry') { $class = 'btn-warning';
                                        }
                                        ?>
                                        <div class="btn-group">
                                            @if(isset($kyc))
                                            <button type="button" class="btn {{ $class }} btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <strong>{{ $kyc?->status }}</strong> <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.kyc.update', ['id' => $kyc?->id, 'status' => 'Approved']) }}">Approved</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.kyc.update', ['id' => $kyc?->id, 'status' => 'Rejected']) }}">Rejected</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.kyc.update', ['id' => $kyc?->id, 'status' => 'Retry']) }}">Retry</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.kyc.update', ['id' => $kyc?->id, 'status' => 'In Process']) }}">In Process</a>
                                                </li>
                                            </ul>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>KYC Status Message:</strong></td>
                                    <td>@if(isset($kyc))
                                        <div class="d-flex">
                                            <input type="text" class="form-control" placeholder="Kyc Message" id="kyc_message" name="kyc_message" value="{{ $kyc?->status_message }}">
                                            <input type="button" id="btnUpdateKycStatus" class="btn btn-primary btn-sm ms-1" value="Save">
                                            <input type="button" id="btnEmailKycStatus" class="btn btn-primary btn-sm ms-1" value="Email User">
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @if(isset($kyc) && isset($kyc->mastercard_kyc_url) && !empty($kyc->mastercard_kyc_url))
                                <tr>
                                    <td><strong>Mastercard KYC URL:</strong></td>
                                    <td><a class="text-decoration-underline" target="_blank" href="{{ $kyc->mastercard_kyc_url }}">{{ $kyc->mastercard_kyc_url }}</a></td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>First Name:</strong></td>
                                    <td>{{ $kyc?->first_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Middle Name:</strong></td>
                                    <td>{{ $kyc?->middle_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Name:</strong></td>
                                    <td>{{ $kyc?->last_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td>{{ $kyc?->gender === 1 ? 'Femail' : ($kyc?->gender!='' ? 'Male' : '') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $kyc?->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone:</strong></td>
                                    <td>{{ $kyc?->phone }}</td>
                                </tr>
                                <tr>
                                    <td width="30%"><strong>Birthday:</strong></td>
                                    <td width="70%">{{ $kyc?->birthday ? \Carbon\Carbon::parse($kyc?->birthday)->format('d M, Y') : '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>City:</strong></td>
                                    <td>{{ $kyc?->city }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Street Address 1:</strong></td>
                                    <td>{{ $kyc?->street_address }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Street Address 2:</strong></td>
                                    <td>{{ $kyc?->street_address_2 }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Region State Province:</strong></td>
                                    <td>{{ $kyc?->region_state_province }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Postcode / Zipcode:</strong></td>
                                    <td>{{ $kyc?->zipcode }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nationality:</strong></td>
                                    <td>{{ $country_iso_3_names[$kyc?->nationality] ?? $kyc?->nationality }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Place of Birth:</strong></td>
                                    <td>{{ $country_iso_3_names[$kyc?->place_of_birth] ?? $kyc?->place_of_birth }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Country:</strong></td>
                                    <td>{{ $country_iso_3_names[$kyc?->country] ?? $kyc?->country }}</td>
                                </tr>
                                <tr>
                                    <td class="pt-2"><strong>KYC Files:</strong></td>
                                    <td class="m-0 p-0"><table class="table table-sm p-0 m-0">
                                        <tr>
                                            <th>Photo ID - Front:</th>
                                            <th>Photo ID - Back</th>
                                            <th>Govt Bill</th>
                                        </tr>
                                        <tr>
                                            <td>@if ($kyc?->file1)
                                                <a href="{{ asset('uploads/files/' . $kyc->file1) }}" download="" target="_blank">
                                                    @if (preg_match('/jpg$|png$|jpeg$|gif$|bmp$/i', $kyc->file1))
                                                        <img style="height: 100px;width: 100px;"
                                                            src="{{ asset('uploads/files/' . $kyc->file1) }}">
                                                    @else
                                                        <h5><i class="mdi mdi-download"></i> <i class="mdi mdi-file-document"></i></h5>
                                                    @endif
                                                </a>
                                                @else
                                                    None
                                                @endif
                                            </td>
                                            <td>@if ($kyc?->file2)
                                                <a href="{{ asset('uploads/files/' . $kyc->file2) }}" download="" target="_blank">
                                                    @if (preg_match('/jpg$|png$|jpeg$|gif$|bmp$/i', $kyc->file2))
                                                        <img style="height: 100px; width: 100px;" src="{{ asset('uploads/files/' . $kyc->file2) }}">
                                                    @else
                                                        <h5><i class="mdi mdi-download"></i> <i class="mdi mdi-file-document"></i></h5>
                                                    @endif
                                                </a>
                                                @else
                                                    None
                                                @endif
                                            </td>
                                            <td>@if ($kyc?->file3)
                                                <a href="{{ asset('uploads/files/' . $kyc->file3) }}" download="" target="_blank">
                                                    @if (preg_match('/jpg$|png$|jpeg$|gif$|bmp$/i', $kyc->file3))
                                                        <img style="height: 100px;width: 100px;" src="{{ asset('uploads/files/' . $kyc->file3) }}">
                                                    @else
                                                        <h5><i class="mdi mdi-download"></i> <i class="mdi mdi-file-document"></i></h5>
                                                    @endif
                                                </a>
                                            @else
                                                None
                                            @endif
                                            </td>
                                        </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table> <!-- end kyc table -->


                            <div class="col-md-6 mt-5 h3 text-warning">Card Applications</div>
                            @foreach ($cards as $card)
                            <table class="table table-sm table-striped table-bordered" id="tableCardApplications">
                                <tr>
                                    <th width="20%" class="fs-5 text-info">{{ $card->card_type }}</th>
                                    <th width="40%">{{ ($card->updated_at ?? $card->created_at)->format('d M, Y H:i a') }}</th>
                                    <td width="40%">
                                        <?php
                                        if ($card?->status == 'In Process') {
                                            $class = 'btn-secondary';
                                        } elseif ($card?->status == 'Approved') {
                                            $class = 'btn-success';
                                        } elseif ($card?->status == 'Rejected') {
                                            $class = 'btn-danger';
                                        } elseif ($card?->status == 'Pending') {
                                            $class = 'btn-warning';
                                        } else {
                                            $class = 'btn-default';
                                        }
                                        ?>
                                        <div class="btn-group">
                                            <button type="button" class="btn {{ $class }} dropdown-toggle btn-sm"
                                                data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 110px">{{ $card->status }} <i class="mdi mdi-chevron-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" style="">
                                                <li><a class="dropdown-item" href="{{ route('admin.card.update', ['id' => $card->id, 'status' => 'Approved']) }}">Approved</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.card.update', ['id' => $card->id, 'status' => 'Rejected']) }}">Rejected</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.card.update', ['id' => $card->id, 'status' => 'In Process']) }}">In Process</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.card.update', ['id' => $card->id, 'status' => 'Pending']) }}">Pending</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Card Holder ID</th>
                                    <td><input type="text" class="chid form-control p-1" data-id="{{ $card->id }}" value="{{ $card->card_holder_id }}"></td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <th>Transaction</th>
                                    <td colspan="2"><div class="pt-1 pb-2">
                                            <a href="https://{{config('app.debug') ? 'nile.' : ''}}tronscan.org/#/transaction/{{$card->tx_id}}" target="_blank">
                                            {{ $card->tx_id }}
                                            </a>
                                        </div>
                                        @if($card->trans_address)
                                            <div class="">
                                                <a href="https://{{config('app.debug') ? 'nile.' : ''}}tronscan.org/#/address/{{ $card->trans_address }}/transfers" target="_blank">{{ $card->trans_address }}</a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @if($card?->file)
                                <tr>
                                    <th>File Uploaded:</th>
                                    <td colspan="2">
                                            <a href="{{ asset('uploads/payment_files/' . $card->file) }}" download="" target="_blank">
                                                @if(preg_match('/jpg$|png$|jpeg$|gif$|bmp$/i',$card->file))
                                                    <img style="height: 75px;width: 75px;" src="{{ asset('uploads/payment_files/' . $card->file) }}">
                                                @else
                                                    <h5><i class="mdi mdi-download"></i> <i class="mdi mdi-file-document"></i></h5>
                                                @endif
                                            </a>
                                    </td>
                                </tr>
                                @endif
                            </table> <!-- end card applications table -->
                            @endforeach

                        </div>
                    </div> <!-- end row -->

                    <div class="row">
                        <div class="col sm-12">
                            <div class="col-md-6 h3 text-warning mt-5">Card Load History</div>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>TXID</th>
                                        <th>Date</th>
                                        <th>Card To Load</th>
                                        <th>File / Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($card_loads as $item)
                                        <tr>
                                            <td><a href="https://{{config('app.debug') ? 'nile.' : ''}}tronscan.org/#/transaction/{{$item->tx_id}}" target="_blank">
                                                {{ $item->tx_id }}
                                                </a>
                                                @if($item->type == 'USDT')
                                                    <div class="py-2"><a href="https://{{config('app.debug') ? 'nile.' : ''}}tronscan.org/#/address/{{ $item->trans_address }}/transfers" target="_blank">
                                                            {{ $item->trans_address }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{!! str_replace('~','<br>',$item->updated_at->format('d M, Y~H:i a')) !!}</td>
                                            <td class="fw-bold">{{ $item->card?->number }}
                                                <div> {{ $item->card?->card_type }}</div>
                                                <div class="mt-1"> <i class="mdi mdi-alert blink text-danger"></i> Card ID: <span style="font-size:1.5em">{{ $item->card?->card_id }}</span></div>
                                                <div> <i class="mdi mdi-alert blink text-danger"></i> Holder ID: <span style="font-size:1.5em">{{ $item->card?->card_holder_id }}</span></div>
                                            </td>
                                            <td>@if($item->type == 'USDT')
                                                    <strong>{{ abs($item->trans_amount - $item->trans_fee) }} USDT</strong>
                                                    @if($item->trans_fee)
                                                        &nbsp; Fee: {{ $item->trans_fee }}
                                                        <br>
                                                        <strong>Paid: {{ $item->trans_amount }}</strong>
                                                    @endif
                                                    @if(!$item->trans_loaded)
                                                        @if($item->api_trans_id > 0)
                                                            <div class="bg-info p-1" title="{{ $item->api_response }}">
                                                            @switch($item->api_status)
                                                                @case(100) <i class="mdi mdi-check-circle"></i> Manual @break
                                                                @case(200) <i class="mdi mdi-loading mdi-spin"></i> Processing @break
                                                                @case(400) <i class="mdi mdi-loading mdi-spin"></i> Rechecking vai API @break
                                                                @default <i class="text-danger mdi alert-circle"></i> Error via API @break
                                                            @endswitch
                                                            </div>
                                                            <div>
                                                                <i class="mdi mdi-alert blink text-danger"></i>
                                                                <span class="text-danger">API Transaction ID: {{ $item->api_trans_id }}</span>
                                                            </div>
                                                        @endif
                                                        @if(intval($item->api_trans_id) <= 0 || now()->diffInMinutes($item->updated_at, true) > 5)
                                                            <br>
                                                            <form method="post" action="{{ route('admin.card.load.done') }}" id="formCardLoad">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $item->id }}">
                                                                <input type="hidden" name="user_id" value="{{ $item->user_id }}">
                                                                <input type="hidden" name="per_page" value="{{ request()->per_page }}">
                                                                <input type="hidden" name="status" value="{{ request()->status }}">
                                                                <input type="hidden" name="search" value="{{ request()->search }}">
                                                                <button type="submit" class="btn btn-sm btn-danger btnTransLoad">Card Load</button>
                                                                <label class="mt-2 d-block"><input type="checkbox" name="manual" value="1" class="form-check-input border border-danger" id="manual"> Manual Load</label>
                                                            </form>
                                                        @endif
                                                    @endif
                                                @else
                                                    @if ($item?->file)
                                                        <a href="{{ asset('uploads/payment_files/' . $item->file) }}"
                                                            download="" target="_blank">
                                                            @if (preg_match('/jpg$|png$|jpeg$|gif$|bmp$/i', $item->file))
                                                                <img style="height: 75px;width: 75px;"
                                                                    src="{{ asset('uploads/payment_files/' . $item->file) }}">
                                                            @else
                                                                <h5><i class="mdi mdi-download"></i> <i
                                                                        class="mdi mdi-file-document"></i></h5>
                                                            @endif
                                                        </a>
                                                    @else
                                                        None
                                                    @endif
                                                @endif
                                            </td>
                                            <td><?php
                                                $class = '';
                                                if ($item->status == 'In Process') { $class = 'btn-secondary';
                                                } elseif ($item->status == 'Approved') { $class = 'btn-success';
                                                } elseif ($item->status == 'Rejected') { $class = 'btn-danger';
                                                }
                                                ?>
                                                <div class="btn-group">
                                                    <button type="button" class="btn {{ $class }} btn-sm dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">{{ $item->status }} <i class="mdi mdi-chevron-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu" style="">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.card.load.update', ['id' => $item->id, 'status' => 'Approved']) }}">Approved</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.card.load.update', ['id' => $item->id, 'status' => 'Rejected']) }}">Rejected</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.card.load.update', ['id' => $item->id, 'status' => 'In Process']) }}">In
                                                                Process</a>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot_scripts')
    <style>
    @keyframes blink {
        0% {
        opacity: 1; /* Fully visible at the start */
        }
        50% {
        opacity: 0; /* Fully invisible at the halfway point */
        }
        100% {
        opacity: 1; /* Fully visible at the end of the cycle */
        }
    }
    tr:hover .blink {
        animation: blink 1s infinite; /* Adjust the duration as needed */
        color: red; /* Change color to red when blinking */
    }
    </style>
    <script>
        $(function() {
            $('#formCardLoad').on('submit', function(e) {
                e.preventDefault();
                if(confirm('Are you sure to process load ' + ($('#manual').is(':checked') ? 'manually' : 'via Card API') + '?')) {
                    $('#formCardLoad .btnTransLoad').prop('disabled', true);
                    $('#formCardLoad .btnTransLoad').html('<i class="mdi mdi-loading mdi-spin"></i> Processing...');
                    return this.submit();
                }
            });
            $('#userCard .btn-toggle-next').on('click', function() {
                $(this).next().toggleClass('d-none').toggleClass('d-flex');
            });
            $('#tableKYC #btnUpdateKycStatus').on('click', function() {
                let _this = $('#tableKYC #kyc_message');
                let message = _this.val();
                _this.css('background-size', '16px 16px');
                _this.css('background', 'url("{{ asset('assets/images/loading.gif') }}") no-repeat right');
                $.ajax({
                    url: "{{ route('admin-kyc-update-message') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: '{{ $kyc?->id }}',
                        message: message
                    },
                    success: function(response) {
                        _this.css('border', '1px solid green');
                        _this.css('background-color', '#d4edda');
                        _this.css('background-image', 'none');
                    }
                }).fail(function() {
                    _this.css('border', '1px solid red');
                    _this.css('background-color', 'pink');
                    _this.css('background-image', 'none');
                });
            });
            $('#tableKYC #btnEmailKycStatus').on('click', function() {
                let _this = $('#tableKYC #kyc_message');
                let message = _this.val();
                _this.css('background-size', '16px 16px');
                _this.css('background', 'url("{{ asset('assets/images/loading.gif') }}") no-repeat right');
                $.ajax({
                    url: "{{ route('admin-kyc-email-message') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: '{{ $kyc?->id }}',
                        message: message
                    },
                    success: function(response) {
                        _this.css('border', '1px solid green');
                        _this.css('background-color', '#d4edda');
                        _this.css('background-image', 'none');
                    }
                }).fail(function() {
                    _this.css('border', '1px solid red');
                    _this.css('background-color', 'pink');
                    _this.css('background-image', 'none');
                });
            });
            $('#tableCardApplications .chid').on('change', function() {
                let _this = $(this);
                let chid = $(this).val();
                let id = $(this).data('id');
                _this.css('background-size', '16px 16px');
                _this.css('background', 'url("{{ asset('assets/images/loading.gif') }}") no-repeat right');
                $.ajax({
                    url: "{{ route('admin-payment-card-update-holder-id') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        chid: chid
                    },
                    success: function(response) {
                        _this.css('border', '1px solid green');
                        _this.css('background-color', '#d4edda');
                        _this.css('background-image', 'none');
                    }
                }).fail(function() {
                    _this.css('border', '1px solid red');
                    _this.css('background-color', 'pink');
                    _this.css('background-image', 'none');
                });
            });
            $('#tableCardActivations .chid').on('change', function() {
                let _this = $(this);
                let chid = $(this).val();
                let id = $(this).data('id');
                _this.css('background-size', '16px 16px');
                _this.css('background', 'url("{{ asset('assets/images/loading.gif') }}") no-repeat right');
                $.ajax({
                    url: "{{ route('admin-card-update-ids') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        chid: chid
                    },
                    success: function(response) {
                        _this.css('border', '1px solid green');
                        _this.css('background-color', '#d4edda');
                        _this.css('background-image', 'none');
                    }
                }).fail(function() {
                    _this.css('border', '1px solid red');
                    _this.css('background-color', 'pink');
                    _this.css('background-image', 'none');
                });
            });
            $('#tableCardActivations .cid').on('change', function() {
                let _this = $(this);
                let cid = $(this).val();
                let id = $(this).data('id');
                _this.css('background', 'url("{{ asset('assets/images/loading.gif') }}") no-repeat right');
                $.ajax({
                    url: "{{ route('admin-card-update-ids') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        cid: cid
                    },
                    success: function(response) {
                        _this.css('border', '1px solid green');
                        _this.css('background-color', '#d4edda');
                        _this.css('background-image', 'none');
                    }
                }).fail(function() {
                    _this.css('border', '1px solid red');
                    _this.css('background-color', 'pink');
                    _this.css('background-image', 'none');
                });
            });
        });
    </script>
@endsection
