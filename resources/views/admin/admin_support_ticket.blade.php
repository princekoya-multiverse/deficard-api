@extends('admin.layout', ['title' => 'Support Tickets'])

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
                        </div>
                    </form>
                </div>
                <div class="card-body min-vh-1010">
                    <div class="table-responsive min-vh-100">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Ticket#</th>
                                    <th>User Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>TXID</th>
                                    <th>File</th>
                                    <th>status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->user->name }}
                                            <br>
                                            <a href="{{ route('admin.user.profile', ['id' => $item->user_id]) }}" class="btn btn-primary btn-sm mt-2">Profile</a>
                                        </td>
                                        <td>{{ $item->user->email }}</td>
                                        <td>{{ $item->user->phone }}</td>
                                        <td>{{ $item->message }}</td>
                                        <td>@if ($item->file)
                                                <a href="{{ asset('uploads/tickets/' . $item->file) }}"
                                                    download="" target="_blank">
                                                    @if (preg_match('/jpg$|png$|jpeg$|gif$|bmp$/i', $item->file))
                                                        <img style="height: 75px;width: 75px;"
                                                            src="{{ asset('uploads/tickets/' . $item->file) }}">
                                                    @else
                                                        <h5><i class="mdi mdi-download"></i><i
                                                                class="mdi mdi-file-document"></i></h5>
                                                    @endif
                                                </a>
                                            @else
                                                None
                                            @endif
                                        </td>
                                        <td>
                                            <?php
                                            $class = '';
                                            if ($item->status == 'Active') {
                                                $class = 'btn-secondary';
                                            } elseif ($item->status == 'Open') {
                                                $class = 'btn-warning';
                                            } elseif ($item->status == 'Resolved') {
                                                $class = 'btn-success';
                                            } elseif ($item->status == 'Need Reply') {
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
                                                            href="{{ route('admin.support_ticket.update', ['id' => $item->id, 'status' => 'Active']) }}">Active</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.support_ticket.update', ['id' => $item->id, 'status' => 'Open']) }}">Open</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.support_ticket.update', ['id' => $item->id, 'status' => 'Resolved']) }}">Resolved</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.support_ticket.update', ['id' => $item->id, 'status' => 'Need Reply']) }}">Need Reply</a>
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
                        {!! $tickets->appends(request()->all())->links('pagination::bootstrap-5') !!}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
