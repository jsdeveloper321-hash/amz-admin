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



        <h5 class="mb-1">Driver Approvals</h5>
        <p class="text-muted mb-4">Review and manage pending driver applications</p>

       <div class="row mb-4">

    <div class="col-xl col-lg col-md-4 col-sm-6 mb-3">
        <div class="summary-card summary-total active" data-filter="all">
            <div class="summary-value">{{ $users->count() }}</div>
            <div class="summary-label">Total Applications</div>
        </div>
    </div>

    <div class="col-xl col-lg col-md-4 col-sm-6 mb-3">
        <div class="summary-card summary-approved" data-filter="Approved">
           <div class="summary-value text-success">
    {{ $users->where('approval_status','Approved')->count() }}
</div>

            <div class="summary-label">Approved</div>
        </div>
    </div>

    <div class="col-xl col-lg col-md-4 col-sm-6 mb-3">
        <div class="summary-card summary-warning" data-filter="Warning">
            <div class="summary-value text-warning">   {{ $users->where('approval_status','Warning')->count() }}</div>
            <div class="summary-label">Warning</div>
        </div>
    </div>

    <div class="col-xl col-lg col-md-6 col-sm-6 mb-3">
        <div class="summary-card summary-rejected" data-filter="Rejected">
            <div class="summary-value text-danger">   {{ $users->where('approval_status','Rejected')->count() }}</div>
            <div class="summary-label">Rejected</div>
        </div>
    </div>

    <div class="col-xl col-lg col-md-6 col-sm-12 mb-3">
        <div class="summary-card summary-pending" data-filter="Pending">
            <div class="summary-value text-primary">   {{ $users->where('approval_status','Pending')->count() }}</div>
            <div class="summary-label">Pending</div>
        </div>
    </div>

</div>



        <!-- Tab Content -->
        <div class="tab-content">

            <!-- ALL -->
            <div class="tab-pane fade show active" id="tab-all">
                <div class="table-responsive">
                <table class="table table-hover" id="approval1">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                        <th>Subscription</th>
                    </tr>
                    </thead>
                  <tbody>
@foreach($users as $user)
<tr data-status="{{ $user->approval_status }}">
    <td>{{ $user->user_name }}</td>
    <td>{{ $user->mobile_number }}</td>
    <td>{{ $user->company_name }}</td>

    <td>
        <span class="badge 
            @if($user->approval_status=='Approved') badge-success
            @elseif($user->approval_status=='Warning') badge-warning
            @elseif($user->approval_status=='Rejected') badge-danger
            @else badge-primary
            @endif">
            {{ $user->approval_status }}
        </span>
    </td>

    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}</td>

    <td>
   <a href="{{ route('admin.approval_details', $user->id) }}" class="btn btn-sm btn-view">
    View Application
</a>
    </td>

    <td>
        @if($user->subscription_status == 'Active')
            <button class="btn btn-sm btn-success">Subscription</button>
        @else
            <button class="btn btn-sm btn-danger">Unsubscribed</button>
        @endif
    </td>
</tr>
@endforeach
</tbody>


                </table>
            </div>
            </div>

            <!-- APPROVED -->
            <div class="tab-pane fade" id="tab-approved">
                 <div class="table-responsive">
                <table class="table" id="approval2">
                    <tbody>
                   
                    </tbody>
                </table>
            </div>
            </div>

            <!-- WARNING -->
            <div class="tab-pane fade" id="tab-warning">
                 <div class="table-responsive">
                <table class="table" id="approval3">
                    <tbody>
                    
                    </tbody>
                </table>
            </div>
            </div>

            <!-- REJECTED -->
            <div class="tab-pane fade" id="tab-rejected">
                <div class="table-responsive"> 
                <table class="table" id="approval4">
                    <tbody>
                    
                    </tbody>
                </table>
               </div>
            </div>

            <!-- PENDING -->
            <div class="tab-pane fade" id="tab-pending">
                 <div class="table-responsive">
                <table class="table" id="approval5">
                    <tbody>
                   
                    </tbody>
                </table>
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
<script>
    document.querySelectorAll('.summary-card').forEach(card => {
    card.addEventListener('click', function () {

        document.querySelectorAll('.summary-card')
            .forEach(c => c.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display =
                filter === 'all' || row.dataset.status === filter
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
    new DataTable('#approval1', {
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>
<script>
$(document).ready(function () {
    new DataTable('#approval2', {
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>
<script>
$(document).ready(function () {
    new DataTable('#approval3', {
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>

<script>
$(document).ready(function () {
    new DataTable('#approval4', {
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>

<script>
$(document).ready(function () {
    new DataTable('#approval5', {
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>
</body>
</html>
