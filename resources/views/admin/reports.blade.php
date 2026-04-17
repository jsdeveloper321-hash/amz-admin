@include('admin.include.header')

<!-- NAVBAR -->
@include('admin.include.navbar')
<div class="overlay" id="overlay"></div>

<div class="container">
   <link href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap5.css" rel="stylesheet">
    <style>
      .filter-panel {
    position:absolute;
    top:40%;
    right:20px;
    width:280px;
    z-index:1000;
}


  .dt-layout-row{
         display: flex;
         justify-content: space-between;
         margin-bottom:25px;
     }
     .dt-search input {
    margin-left: 0.5em;
    display: inline-block;
    width: auto;
    border-radius: 20px;
    border: 1px solid #ddd;
    height: 32px;
    outline:0;
    padding:10px 10px;
  }
  .dt-paging-button
  {
    padding: 4px 10px;
    margin: 2px;
    border: 1px solid #ddd;
  }
 
    </style>  
    <div class="page-wrapper">

   

    <!-- Header -->
     <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-weight-bold mb-0">Weekly Miles Report</h5>
        <!--<button class="btn add-btn">Export CSV</button>-->
    </div>

    
<!-- Filters -->
<form method="GET" action="{{ route('admin.reports') }}" class="row mb-3">

    <!-- From Date -->
    <div class="col-md-2">
        <label>From Date</label>
        <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
    </div>

    <!-- To Date -->
    <div class="col-md-2">
        <label>To Date</label>
        <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
    </div>

    <!-- Quick Filters -->
    <div class="col-md-2">
        <label>Quick Filter</label>
        <select class="form-control" name="filter_type">
            <option value="">Select</option>
            <option value="today" {{ request('filter_type')=='today'?'selected':'' }}>Today</option>
            <option value="week" {{ request('filter_type')=='week'?'selected':'' }}>This Week</option>
            <option value="last7" {{ request('filter_type')=='last7'?'selected':'' }}>Last 7 Days</option>
            <option value="month" {{ request('filter_type')=='month'?'selected':'' }}>This Month</option>
            <option value="4weeks" {{ request('filter_type')=='4weeks'?'selected':'' }}>Last 4 Weeks</option>
        </select>
    </div>

    <!-- Month Filter -->
    <div class="col-md-2">
        <label>Select Month</label>
        <input type="month" class="form-control" name="month" value="{{ request('month') }}">
    </div>

    <!-- Company -->
    <div class="col-md-2">
        <label>Select Company</label>
        <select class="form-control" name="company">
            <option value="">All Companies</option>
            @foreach($companies as $comp)
                <option value="{{ $comp }}" {{ request('company') == $comp ? 'selected' : '' }}>
                    {{ $comp }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Submit -->
    <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">Filter</button>
    </div>

</form>

<!-- Table Card -->
<div class="card card-custom report">
    <div class="card-body p-2">
        <div class="table-responsive">
          <table class="table mb-0" id="reports">
            <thead>
                <tr>
                    <th>Driver Name</th>
                    <th>Company</th>
                    <th>Total Miles</th>
                    <th>Last Seen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dutyLogs as $log)
                    <tr>
                        <td><strong>{{ $log->driver_name ?? 'N/A' }}</strong></td>
                        <td>{{ $log->company_name ?? 'N/A' }}</td>
                        <td>{{ $log->total_miles ?? 0 }}</td>
                        <td>{{ $log->last_seen ? \Carbon\Carbon::parse($log->last_seen)->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                @empty
                    
                @endforelse
            </tbody>
          </table>
        </div>
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
const map = L.map('map').setView([40.73,-73.93],12);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
L.marker([40.73,-73.93]).addTo(map).bindPopup("Current Location");
</script>

<script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap5.js"></script>   
<script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>  
 
<script>
$(document).ready(function () {
    $('#reports').DataTable({
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>
</body>
</html>
