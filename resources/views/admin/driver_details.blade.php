@include('admin.include.header')

<!-- NAVBAR -->
@include('admin.include.navbar')

<div class="overlay" id="overlay"></div>

<div class="container">
  
    <div class="page-wrapper">

<a href="{{route('admin.drivers')}}" class="text-dark mb-5 d-inline-block">
  <i class="fa fa-arrow-left mr-1"></i> Back to Drivers
</a>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


<!-- DRIVER HEADER -->
<div class="driver-header">
  <div class="driver-info">
    <img src="https://i.pravatar.cc/100?img=11">
    <div>
      <h5 class="mb-1">{{$users_details->user_name}}</h5>
      <span class="badge badge-active">{{$users_details->approval_status}}</span>
    </div>
  </div>

  <div class="driver-actions">
  <!--  <button class="btn btn-outline-secondary btn-sm">Re-run Validation</button>
    <button class="btn btn-outline-secondary btn-sm">Send Message</button>
    <button class="btn add-btn btn-sm">Add Admin Note</button>-->
  </div>
</div>

<!-- TABS -->
<ul class="nav nav-tabs">
  <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#overview">Overview</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#license">License & Validation</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#miles">Miles & Trips</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#training">Training</a></li>
  <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#activity">Activity Log</a></li>
</ul>

<div class="tab-content mt-3">

<!-- OVERVIEW TAB -->
<div class="tab-pane fade show active" id="overview">
  <div class="row">

    <!-- LEFT -->
    <div class="col-lg-4">
      <div class="info-card">
        <h6>Contact Information</h6>
        <p><i class="fa fa-phone mr-2"></i> <strong>Phone</strong> <br> 
          <span class="pl-3">{{$users_details->mobile_number}}</span></p>
        <p><i class="fa fa-envelope mr-2"></i> <strong>Email</strong> <br><span class="pl-3">{{$users_details->email}}</span></p>
      </div>

      <div class="info-card">
        <h6>Compliance</h6>
        <p>DOT Number <br>  <strong>{{$users_details->dot_number}}</strong></p>
        <p>MC Number <br>  <strong>{{$users_details->mc_number}}</strong></p>
        <p>Last Seen<br> <strong> {{$users_details->updated_at}}  </strong></p>
      </div>

      <div class="info-card">
        <h6>Weekly Summary</h6>
        <p>Miles this week <br>  <strong>{{ $hoursWeek }}</strong></p>
        <p>Hours today <br>  <strong>{{ $hoursToday }}</strong></p>
        <small class="text-success">On track for weekly goal</small>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="col-lg-8">
      <div class="info-card">
        <h6>Current Location</h6>
        <div id="map"></div>
      </div>

      <div class="info-card">
    <h6>Recent Activity</h6>
    <ul class="list-unstyled activity">

        @forelse($recentActivities as $activity)
            <li>
                {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                by <strong>{{ $activity->name ?? 'Driver' }}</strong>

                <small class="text-muted">
                    • {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                </small>
            </li>
        @empty
            <li class="text-muted">No recent activity found</li>
        @endforelse

    </ul>
</div>
      
      
      
    </div>

  </div>
</div>

<!-- OTHER TABS (STRUCTURE READY) -->
<div class="tab-pane fade" id="license">

 <div class="card card-custom p-4">
       
        <!-- Header -->
        <div class="section-title">License & Validation Status</div>

        <!-- Top Info -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="label">License Number</div>
                <div class="value">{{$users_details->driver_license_number}}</div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="label">Issued Date</div>
                <div class="value">{{$users_details->issued_date}}</div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="label">Company Name</div>
                <div class="value">{{$users_details->company_name}}</div>
            </div>

          <!--  <div class="col-md-6">
                <div class="label">Expiration Date</div>
                <div class="value">12/15/2026</div>
            </div>-->
        </div>

        <div class="divider"></div>

        <!-- Validation History -->
     <!--   <div class="section-title">Validation History</div>

        <div class="history-item d-flex justify-content-between align-items-center">
            <div>
                <div class="history-date">Dec 10, 2024</div>
                <div class="history-subtext">Validated by Admin User</div>
            </div>
            <span class="badge badge-success">Passed</span>
        </div>

        <div class="history-item d-flex justify-content-between align-items-center">
            <div>
                <div class="history-date">Nov 10, 2024</div>
                <div class="history-subtext">Validated by Admin User</div>
            </div>
            <span class="badge badge-success">Passed</span>
        </div>

        <div class="history-item d-flex justify-content-between align-items-center">
            <div>
                <div class="history-date">Oct 10, 2024</div>
                <div class="history-subtext">Validated by System Auto</div>
            </div>
            <span class="badge badge-success">Passed</span>
        </div>-->

    </div>


</div>
<div class="tab-pane fade" id="miles">
 <div class="card card-custom p-4">

        <!-- Header -->
        <div class="card-title">Miles & Trips</div>

        <!-- Stats -->
        <div class="row">
    <div class="col-md-4 mb-3">
        <div class="stat-box">
            <div class="stat-label">This Week</div>
            <div class="stat-value">{{ number_format($hoursWeek) }}</div>
            <div class="stat-unit">miles</div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="stat-box">
            <div class="stat-label">This Month</div>
            <div class="stat-value">{{ number_format($monthMiles) }}</div>
            <div class="stat-unit">miles</div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="stat-box">
            <div class="stat-label">Total Trips</div>
            <div class="stat-value">{{ $totalTrips }}</div>
            <div class="stat-unit">trips</div>
        </div>
    </div>
</div>


        <div class="divider"></div>

        <!-- Recent Trips -->
        <div class="section-subtitle">Recent Trips</div>

     <div class="info-card">
    <h6>Recent Trips</h6>

    @forelse($recentTrips as $trip)

        <div class="trip-item d-flex justify-content-between">
            <div>
                <div class="trip-route">
                    Trip #{{ $trip->duty_request_id }}
                </div>

                <div class="trip-date">
                    {{ \Carbon\Carbon::parse($trip->start_time)->format('M d, Y') }}
                </div>
            </div>

            <div class="trip-meta">
                <div>
                    {{ round(($trip->duration_minutes ?? 0) / 60, 1) }} hr
                </div>
                <div>
                    {{ $trip->duration_minutes ?? 0 }} min
                </div>
            </div>
        </div>

    @empty
        <div class="text-muted">No recent trips found</div>
    @endforelse

</div>

    </div>


</div>
<div class="tab-pane fade" id="training">
  <div class="card card-custom p-4">
        <div class="card-title">Offer</div>

   <form action="{{ route('admin.add_training_offer') }}" method="POST">
    @csrf

    <textarea
        name="offer_text"
        class="form-control offer-textarea rounded mb-3"
        rows="3"
        placeholder="Write offer details or paste a link here."
        required
    >{{ old('offer_text') }}</textarea>

    <button type="submit" class="btn btn-send">Send</button>
</form>
    </div>
</div>
<div class="tab-pane fade" id="activity">
  <div class="card card-custom p-4">
        <div class="card-title">Activity Log</div>

     @foreach($dutyLogs as $log)
<div class="log-row d-flex justify-content-between mb-2">
    <div>
        <div class="log-time">
            {{ \Carbon\Carbon::parse($log->start_time)->format('h:i A') }}
        </div>
        <div class="log-date">
            {{ \Carbon\Carbon::parse($log->start_time)->format('M d, Y') }}
        </div>
    </div>

    <div class="text-right">
        <div class="log-action">
            Status changed to {{ ucfirst(str_replace('_', ' ', $log->status)) }}
        </div>
        <div class="log-user">
            {{ $log->created_by ?? 'System' }}
        </div>
    </div>
</div>
@endforeach

    


    </div>

 </div>

</div>
</div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const lat = {{ $users_details->lat ?? 0 }};
    const lng = {{ $users_details->lng ?? 0 }};

    const map = L.map('map').setView([lat, lng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng])
        .addTo(map)
        .bindPopup("Current Location")
        .openPopup();
</script>

</body>
</html>
