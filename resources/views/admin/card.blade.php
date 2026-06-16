@extends('admin.layout', ['title' => 'Card Applications', 'breadcrumb' => []])

@section('content')
    <div class="row">
        <div class="col-xl-12">
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
                                <a  href="{{route('admin.card.export',request()->all())}}" class="btn btn-primary mb-2">Export</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" id="tableCardApplications">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Card Type</th>
                                    <th>Card Holder ID</th>
                                    <th class="text-nowrap">Last Update</th>
                                    <th>Transaction Details</th>
                                    <th>File</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cards as $item)
                                    <tr>
                                        <td>{{ $item->user->name }} <div class="">{{ $item->user->email }}</div>
                                        <a href="{{ route('admin.user.profile', ['id' => $item->user_id]) }}" class="btn btn-primary btn-sm mt-2">Profile</a>
                                        </td>
                                        <td>{{ $item->card_type }}</td>
                                        <td><input type="text" class="chid form-control p-1" data-id="{{ $item->id }}" value="{{ $item->card_holder_id }}"></td>
                                        <td>{{ ($item->updated_at ?? $item->created_at)->format('d M, Y H:i a') }}</td>
                                        <td><a href="https://{{config('app.debug') ? 'nile.' : ''}}tronscan.org/#/transaction/{{$item->tx_id}}" target="_blank">
                                                {{ $item->tx_id }}
                                            </a>
                                            @if($item->trans_address)
                                                <div class="py-2"><a href="https://{{config('app.debug') ? 'nile.' : ''}}tronscan.org/#/address/{{ $item->trans_address }}/transfers" target="_blank">
                                                        {{ $item->trans_address }}
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        {{-- <td>{{ $item->tx_id }} <div class="mt-2"> {{ $item->trans_address }}</div> </td> --}}
                                        <td>
                                            @if($item->file)
                                                <a href="{{ asset('uploads/payment_files/' . $item->file) }}" download="" target="_blank">
                                                    @if(preg_match('/jpg$|png$|jpeg$|gif$|bmp$/i',$item->file))
                                                        <img style="height: 75px;width: 75px;" src="{{ asset('uploads/payment_files/' . $item->file) }}">
                                                    @else
                                                        <h5><i class="mdi mdi-download"></i> <i class="mdi mdi-file-document"></i></h5>
                                                    @endif
                                                </a>
                                            @else
                                                None
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
                                            } elseif ($item->status == 'Pending') {
                                                $class = 'btn-warning';
                                            } else {
                                                $class = 'btn-default';
                                            }
                                            ?>
                                            <div class="btn-group"> <button type="button"
                                                    class="btn {{ $class }} dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 110px">
                                                    {{ $item->status }} <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <ul class="dropdown-menu" style="">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.card.update', ['id' => $item->id, 'status' => 'Approved']) }}">Approved</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.card.update', ['id' => $item->id, 'status' => 'Rejected']) }}">Rejected</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.card.update', ['id' => $item->id, 'status' => 'In Process']) }}">In
                                                            Process</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.card.update', ['id' => $item->id, 'status' => 'Pending']) }}">Pending</a>
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
                        {!! $cards->appends(request()->all())->links('pagination::bootstrap-5') !!}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot_scripts')
    <script>
        document.getElementById('recordsPerPage').addEventListener('change', function() {
            let perPage = this.value;
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', perPage);
            window.location.href = url.href;
        });
        $(function() {
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
        });
    </script>
@endsection
