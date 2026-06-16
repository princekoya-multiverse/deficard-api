@extends('admin.layout', ['title' => 'Card Load'])

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
            <div class="card">
                <div class="card-header">
                    <form class="g-3 d-flex justify-content-between">
                        <div class="col-auto">
                            <select id="recordsPerPage" class="form-select" name="per_page">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Records
                                </option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Records
                                </option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Records
                                </option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Records
                                </option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-auto p-1">
                                <select id="staatus" class="form-select" name="status">
                                    <option value="">Select Status</option>
                                    @foreach ($status as $s )
                                    <option value="{{$s}}" @selected($s == request()->status)>{{$s}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto p-1">
                                <input type="text" class="form-control" name="search" id="search"
                                    placeholder="Search..." value="{{ request()->get('search') }}">
                            </div>
                            <div class="col-auto p-1">
                                <button type="submit" class="btn btn-primary mb-2">Search</button>
                            </div>
                            <div class="col-auto p-1">
                                <a  href="{{route('admin.card.load.export',request()->all())}}" class="btn btn-primary mb-2">Export</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
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
                                        <td>{{ $item->user->name }}
                                            <br>{{ $item->user->email }}
                                            <br>
                                            <a href="{{ route('admin.user.profile', ['id' => $item->user_id]) }}" class="btn btn-primary btn-sm mt-2">Profile</a>
                                        </td>
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
                                            <div class="mt-1"> <i class="mdi mdi-alert blink text-danger"></i> Card ID: <span style="font-size:1.2em">{{ $item->card?->card_id }}</span></div>
                                            <div> <i class="mdi mdi-alert blink text-danger"></i> Holder ID: <span style="font-size:1.2em">{{ $item->card?->card_holder_id }}</span></div>
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
                                                        <input type="hidden" name="per_page" value="{{ request()->per_page }}">
                                                        <input type="hidden" name="status" value="{{ request()->status }}">
                                                        <input type="hidden" name="search" value="{{ request()->search }}">
                                                        <button type="submit" class="btn btn-sm btn-danger btnTransLoad">Card Load</button>
                                                        <label class="mt-2 d-block"><input type="checkbox" name="manual" value="1" class="form-check-input border border-danger" id="manual"> Manual Load</label>
                                                        </form>
                                                    @endif
                                                @endif
                                            @else
                                                @if ($item->file)
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
                                        <td>
                                            <?php
                                            if ($item->status == 'In Process') {
                                                $class = 'btn-secondary';
                                            } elseif ($item->status == 'Approved') {
                                                $class = 'btn-success';
                                            } elseif ($item->status == 'Rejected') {
                                                $class = 'btn-danger';
                                            }
                                            ?>
                                            <div class="btn-group"> <button type="button"
                                                    class="btn {{ $class }} dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{ $item->status }}
                                                     <i class="mdi mdi-chevron-down"></i>
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
                    <div class="mt-3">
                        {!! $card_loads->appends(request()->all())->links('pagination::bootstrap-5') !!}
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
            document.getElementById('recordsPerPage').addEventListener('change', function() {
                let perPage = this.value;
                let url = new URL(window.location.href);
                url.searchParams.set('per_page', perPage);
                window.location.href = url.href;
            });
        });
    </script>
@endsection
