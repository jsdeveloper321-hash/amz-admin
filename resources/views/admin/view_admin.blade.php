@include('admin.include.header')
@include('admin.include.navbar')

<div class="container mt-4">

    <!-- Admin Details -->
    <div class="card mb-3">
        <div class="card-header"><b>Admin Details</b></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <img src="{{ asset('uploads/admins/' . $admin->profile_image) }}"
                         width="100" height="100" style="border-radius:50%;">
                </div>
                <div class="col-md-9">
                    <p><b>Name:</b> {{ $admin->user_name }}</p>
                    <p><b>Email:</b> {{ $admin->email }}</p>
                    <p><b>Phone:</b> {{ $admin->phone_number }}</p>
                    <p><b>Created:</b> {{ $admin->created_at }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- States -->
    <div class="card mb-3">
        <div class="card-header"><b>Assigned States</b></div>
        <div class="card-body">
            @if(count($states))
                @foreach($states as $state)
                    <span class="badge bg-primary">{{ $state }}</span>
                @endforeach
            @else
                <p>No states assigned</p>
            @endif
        </div>
    </div>

    <!-- Login Logs -->
    <div class="card">
        <div class="card-header"><b>Login Logs</b></div>
        <div class="card-body">

            <table class="table table-bordered" id="logTable">
                <thead>
                    <tr>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                        <th>Total Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->login_time }}</td>
                            <td>{{ $log->logout_time ?? 'Active' }}</td>
                            <td>
                                @if($log->logout_time)
                                    {{ \Carbon\Carbon::parse($log->login_time)->diffForHumans($log->logout_time, true) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>

<script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
<script>
new DataTable('#logTable');
</script>
