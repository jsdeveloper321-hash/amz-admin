@include('admin.include.header')

<!-- NAVBAR -->
@include('admin.include.navbar')
<div class="overlay" id="overlay"></div>

<div class="container">
  
    <div class="page-wrapper">

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
  

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="#" class="back-link">← Back to Approvals</a>
        
        @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
        <!--<button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#announcementModal">Create Announcement</button>-->
         <a class="btn btn-danger btn-sm" href="{{route('admin.add_announcements')}}">Create Announcement</a>
    </div>

    <!-- SUMMARY TABS -->
   <div class="row mb-4">

    <div class="col-lg col-md-4 col-sm-6 mb-3">
        <div class="summary-tab active" data-filter="all">
            <div class="summary-value">{{ $totalCount }}</div>
            <div class="summary-label">Total Announcements</div>
        </div>
    </div>

    <div class="col-lg col-md-4 col-sm-6 mb-3">
        <div class="summary-tab summary-urgent" data-filter="Urgent">
            <div class="summary-value">{{ $urgentCount }}</div>
            <div class="summary-label">Urgent</div>
        </div>
    </div>

    <div class="col-lg col-md-4 col-sm-6 mb-3">
        <div class="summary-tab summary-normal" data-filter="Normal">
            <div class="summary-value">{{ $normalCount }}</div>
            <div class="summary-label">Normal</div>
        </div>
    </div>

</div>


    <!-- TABLE -->
    <div class="card card-custom p-3">
        <table class="table mb-0" id="announcement1">
            <thead>
            <tr>
                <th>Announcement</th>
                <th>Type</th>
                <th>Sent Date</th>
                <th>Delivered / Read</th>
                <th>Read Rate</th>
                <th>Action</th>
            </tr>
            </thead>
           <tbody>
@forelse($announcements as $row)

@php
    $readRate = $row->delivered_count > 0
        ? round(($row->read_count / $row->delivered_count) * 100)
        : 0;
@endphp

<tr data-type="{{ $row->type }}">
    <td>
        <strong>{{ $row->title }}</strong>
        <div class="text-muted small">
            {{ $row->message }}
        </div>
    </td>

    <td>
        @if($row->type == 'Urgent')
            <span class="badge badge-danger">Urgent</span>
        @else
            <span class="badge badge-primary">Normal</span>
        @endif
    </td>

    <td>{{ \Carbon\Carbon::parse($row->sent_date)->format('M d, Y') }}</td>

    <td>{{ $row->delivered_count }} / {{ $row->read_count }}</td>

    <td>
        <div class="progress">
            <div class="progress-bar bg-success"
                 style="width: {{ $readRate }}%">
            </div>
        </div>
    </td>
    
      <td>
        <!-- Edit Button -->
        <a href="{{ route('admin.edit_announcement', $row->id) }}" 
           class="btn btn-sm btn-primary">
           Edit
        </a>

        <!-- Delete Button -->
        <form action="{{ route('admin.delete_announcement', $row->id) }}" 
              method="POST" style="display:inline-block;" 
              onsubmit="return confirm('Are you sure you want to delete this announcement?');">
            @csrf
            @method('POST')
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </td>
    
    
</tr>

@empty
<tr>
    <td colspan="5" class="text-center">No announcements found</td>
</tr>
@endforelse
</tbody>

        </table>
   
</div>

   </div>
</div>

<style>
    .modal-custom {
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(0,0,0,.15);
}

/* Announcement type buttons */
.type-btn {
    border: none;
    padding: 6px 18px;
    font-size: 12px;
    border-radius: 6px;
    background: #e5eaff;
    color: #003cff;
    cursor: pointer;
}

.type-btn.urgent {
    background: #ffd6d6;
    color: #dc3545;
}

.type-btn.active {
    box-shadow: inset 0 0 0 1px #003cff;
}

/* Driver selection box */
.driver-box {
    border: 1px solid #f1f1f1;
    border-radius: 10px;
    padding: 12px;
}

.driver-list {
    max-height: 230px;
    overflow-y: auto;
}

.driver-item {
    display: block;
    font-size: 12px;
    padding: 6px;
    border-radius: 6px;
    cursor: pointer;
}

.driver-item:hover {
    background: #f8f9fa;
}

.driver-item input {
    margin-right: 6px;
}

/* Form tweaks */
label {
    font-size: 12px;
    font-weight: 600;
}

.form-control-sm {
    font-size: 12px;
}

</style>
<!-- MODAL -->



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
<script>
document.querySelectorAll('.summary-tab').forEach(tab => {
    tab.addEventListener('click', function () {

        document.querySelectorAll('.summary-tab')
            .forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;

        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display =
                filter === 'all' || row.dataset.type === filter
                ? ''
                : 'none';
        });
    });
});
</script>

<script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap5.js"></script>   
  <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>  
 <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>  
 
 
 
 
 
<script>
$(document).ready(function () {
    new DataTable('#announcement1', {
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>
<script>
$(document).ready(function () {
    new DataTable('#announcement2', {
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>
<script>
$(document).ready(function () {
    new DataTable('#announcement3', {
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>





</body>
</html>
