@extends('admin.layout', ['title' => 'Users'])

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <form class="g-3 d-flex justify-content-between">
                        <div class="col-auto">
                            <select id="recordsPerPage" class="form-select" name="per_page">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Records</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Records</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Records</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Records</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-auto p-1">
                                <select id="progress_status" class="form-select" name="progress_status">
                                    <option value="">- Card Status -</option>
                                    @foreach ($progressStatuses as $ps )
                                    <option value="{{$ps}}" @selected($ps == request()->progress_status)>{{ $ps }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto p-1">
                                <select id="staatus" class="form-select" name="status">
                                    <option value="">- KYC Status -</option>
                                    @foreach ($status as $s )
                                    <option value="{{$s}}" @selected($s == request()->status)>{{ $s }}</option>
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
                                <a href="{{ route('admin.users.export', request()->all()) }}"
                                    class="btn btn-primary mb-2">Export</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" id="usersTable">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Middle Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>KYC Status</th>
                                    <th>Register At</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $item)
                                    <tr>
                                        <td>{{ $item->first_name }}</td>
                                        <td>{{ $item->middle_name }}</td>
                                        <td>{{ $item->last_name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>{{ $item->kyc_status }}</td>
                                        <td>{{ $item->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.user.profile', ['id' => $item->id]) }}"
                                                class="btn btn-primary btn-sm">User
                                                Profile</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {!! $users->appends(request()->all())->links('pagination::bootstrap-5') !!}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot_scripts')
    <script>
        $(function() {});
        // JavaScript for search functionality
        // document.getElementById('searchInput').addEventListener('input', function() {
        //     let filter = this.value.toLowerCase();
        //     let rows = document.querySelectorAll('#usersTableBody tr');
        //     rows.forEach(row => {
        //         let cells = row.querySelectorAll('td');
        //         let match = false;
        //         cells.forEach(cell => {
        //             if (cell.textContent.toLowerCase().includes(filter)) {
        //                 match = true;
        //             }
        //         });
        //         if (match) {
        //             row.style.display = '';
        //         } else {
        //             row.style.display = 'none';
        //         }
        //     });
        // });

        /*
        // JavaScript for export functionality
        document.getElementById('exportButton').addEventListener('click', function() {
            let table = document.getElementById('usersTable');
            let rows = table.querySelectorAll('tr');
            let csvContent = '';

            rows.forEach(row => {
                let cols = row.querySelectorAll('td, th');
                let rowData = Array.from(cols).map(col => `"${col.textContent.trim()}"`).join(',');
                csvContent += rowData + '\n';
            });

            let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'users.csv';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
        */

        // JavaScript for handling records per page change
        document.getElementById('recordsPerPage').addEventListener('change', function() {
            let perPage = this.value;
            let url = new URL(window.location.href);
            url.searchParams.set('per_page', perPage);
            window.location.href = url.href;
        });
    </script>
@endsection
