@extends('admin.layout', ['title' => 'Kyc Payments'])

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
                                <a href="{{ route('admin.kyc.payment.export', request()->all()) }}"
                                    class="btn btn-primary mb-2">Export</a>
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
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>TXID</th>
                                    <th>File</th>
                                    <th>status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kyc_payments as $item)
                                    <tr>
                                        <td>{{ $item->user->name }}
                                            <a href="{{ route('admin.user.profile', ['id' => $item->user_id]) }}" class="btn btn-primary btn-sm mt-2">Profile</a>
                                        </td>
                                        <td>{{ $item->user->email }}</td>
                                        <td>{{ $item->user->phone }}</td>
                                        <td>{{ $item->tx_id }}</td>
                                        <td>
                                            <img style="height: 50px;width: 50px;"
                                                src="{{ asset('uploads/payment_files/' . $item->file) }}"
                                                alt="">
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
                                                            href="{{ route('admin.kyc.payment.update', ['id' => $item->id, 'status' => 'Approved']) }}">Approved</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.kyc.payment.update', ['id' => $item->id, 'status' => 'Rejected']) }}">Rejected</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.kyc.payment.update', ['id' => $item->id, 'status' => 'In Process']) }}">In
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
                        {!! $kyc_payments->appends(request()->all())->links('pagination::bootstrap-5') !!}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('recordsPerPage').addEventListener('change', function() {
            let perPage = this.value;
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', perPage);
            window.location.href = url.href;
        });
    </script>
@endsection
