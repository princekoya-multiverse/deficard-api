@extends('admin.layout', ['title' => 'Kyc Applications', 'breadcrumb' => []])

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
                                <a href="{{ route('admin.kyc.export', request()->all()) }}"
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
                                    <th>Birthday <div class="text-small small">(Y-M-D)</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Photo ID - Front</th>
                                    <th>Photo ID - Back</th>
                                    <th>Govt Bill</th>
                                    <th>status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kyc as $item)
                                <tr >
                                    <td>{{ $item->first_name }} {{ $item->last_name }}
                                        <a href="{{ route('admin.user.profile', ['id' => $item->user_id]) }}" class="btn btn-primary btn-sm mt-2">Profile</a>
                                    </td>
                                    <td>{{ $item->birthday }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-sm table-bordered" style="min-width:370px;">
                                                <tr>
                                                    <td class="col-4">City: </td>
                                                    <td>{{ $item->city }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Street Address 1: </td>
                                                    <td>{{ $item->street_address }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Street Address 2: </td>
                                                    <td>{{ $item->street_address_2 }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Region State Province: </td>
                                                    <td>{{ $item->region_state_province }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Postcode / Zipcode: </td>
                                                    <td>{{ $item->zipcode }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Country: </td>
                                                    <td>{{ $item->country }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($item->file1)
                                            <a href="{{ asset('uploads/files/' . $item->file1) }}"
                                                download="" target="_blank">
                                                @if (preg_match('/jpg$|png$|jpeg$|gif$|bmp$/i', $item->file1))
                                                    <img style="height: 75px;width: 75px;"
                                                        src="{{ asset('uploads/files/' . $item->file1) }}">
                                                @else
                                                    <h5><i class="mdi mdi-download"></i> <i
                                                            class="mdi mdi-file-document"></i></h5>
                                                @endif
                                            </a>
                                        @else
                                            None
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->file2)
                                            <a href="{{ asset('uploads/files/' . $item->file2) }}"
                                                download="" target="_blank">
                                                @if (preg_match('/jpg$|png$|jpeg$|gif$|bmp$/i', $item->file2))
                                                    <img style="height: 75px;width: 75px;"
                                                        src="{{ asset('uploads/files/' . $item->file2) }}">
                                                @else
                                                    <h5><i class="mdi mdi-download"></i> <i
                                                            class="mdi mdi-file-document"></i></h5>
                                                @endif
                                            </a>
                                        @else
                                            None
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->file3)
                                            <a href="{{ asset('uploads/files/' . $item->file3) }}"
                                                download="" target="_blank">
                                                @if (preg_match('/jpg$|png$|jpeg$|gif$|bmp$/i', $item->file3))
                                                    <img style="height: 75px;width: 75px;"
                                                        src="{{ asset('uploads/files/' . $item->file3) }}">
                                                @else
                                                    <h5><i class="mdi mdi-download"></i> <i
                                                            class="mdi mdi-file-document"></i></h5>
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
                                        } elseif ($item->status == 'Retry') {
                                            $class = 'btn-warning';
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
                                                        href="{{ route('admin.kyc.update', ['id' => $item->id, 'status' => 'Approved']) }}">Approved</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.kyc.update', ['id' => $item->id, 'status' => 'Rejected']) }}">Rejected</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.kyc.update', ['id' => $item->id, 'status' => 'Retry']) }}">Retry</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.kyc.update', ['id' => $item->id, 'status' => 'In Process']) }}">In
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
                        {!! $kyc->appends(request()->all())->links('pagination::bootstrap-5') !!}
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
