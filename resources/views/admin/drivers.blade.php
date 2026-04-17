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

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
      <div class="page-title">Drivers List</div>
      
        <!-- Success Message -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                
            @php
            $admin = Auth::guard('admin')->user();
            @endphp     
                
              @if($admin->type == 'Admin')   
               <a class="btn btn-danger btn-sm" href="{{route('admin.add_driver')}}">Create Driver</a>
               @endif
     
    </div>

    <!-- SEARCH + FILTER -->
    <!--<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">-->
    <!--  <div class="position-relative" style="max-width:260px;">-->
    <!--    <i class="fa fa-search search-icon"></i>-->
    <!--    <input id="searchInput" type="text" class="form-control search-input" placeholder="Search driver">-->
    <!--  </div>-->

    <!--   <button class="btn btn-outline-danger btn-sm" id="toggleFilter"><i class="fa fa-filter"></i> Filter</button>-->
    <!--    <div id="filterPanel" class="filter-panel d-none">-->
    <!--    <div class="card p-3">-->
    <!--        <h6 class="font-weight-bold mb-2">Filter</h6>-->

    <!--        <label class="small-label">State</label>-->
    <!--        <select id="state" class="form-control form-control-sm mb-2">-->
    <!--            <option value="">All</option>-->
    <!--            <option>TX</option>-->
    <!--            <option>CA</option>-->
    <!--            <option>NY</option>-->
    <!--        </select>-->

    <!--        <label class="small-label">City</label>-->
    <!--        <input id="city" class="form-control form-control-sm mb-2" placeholder="Dallas">-->

    <!--        <label class="small-label">Radius</label>-->
    <!--        <select id="radius" class="form-control form-control-sm mb-2">-->
    <!--            <option value="50">50 miles</option>-->
    <!--            <option value="100">100 miles</option>-->
    <!--            <option value="150">150 miles</option>-->
    <!--        </select>-->

    <!--        <label class="small-label">Status</label>-->
    <!--        <select id="status" class="form-control form-control-sm mb-3">-->
    <!--            <option value="">All</option>-->
    <!--            <option>ON_DUTY</option>-->
    <!--            <option>OFF_DUTY</option>-->
    <!--            <option>SLEEP</option>-->
    <!--            <option>HOME</option>-->
    <!--        </select>-->

    <!--        <button class="btn btn-danger btn-sm btn-block" id="applyFilter">-->
    <!--            Apply Filter-->
    <!--        </button>-->
    <!--    </div>-->
    <!--</div>-->
    <!--</div>-->

    <!-- TABLE -->
    <div class="table-responsive">
    <table class="table" id="driversTable">
    <thead>
        <tr>
            <th>Driver</th>
             <th>Type</th>
               <th>Rating</th>
            <th>Phone</th>
            <th>Company</th>
            <th>MC Number</th>
            <th>Dot Number</th>
             <th>Status</th>
             <th>Language</th>
            <th>Issued Date</th>
            <th>View</th>
        </tr>
    </thead>
    <tbody>

        @forelse($users as $user)
          
        
           @php
           $user_id =  $user->id;
        $rating = DB::table('request_duty')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->first(); 
    @endphp
        
        
            <tr data-status="{{ strtolower($user->status ?? 'inactive') }}">
                <td data-label="Driver">
                    <img src="{{ $user->image ? asset($user->image) : 'https://i.pravatar.cc/50' }}" class="avatar">
                    {{ $user->user_name }}
                </td>
               <td data-label="Phone">{{ $user->type ?? '-' }}</td>
               <td data-label="Phone">{{ $rating->rating ?? '-' }}</td>
               
                <td data-label="Phone">{{ $user->mobile_number ?? '-' }}</td>

                <td data-label="Company">{{ $user->company_name ?? '-' }}</td>

                <td>{{ $user->mc_number ?? 'N/A' }}</td>

                <td data-label="Identifier">
                    <span class="hash">{{ $user->dot_number ?? 'N/A' }}</span>
                </td>
                
                <td data-label="Status">
                    <span class="status {{ strtolower($user->status ?? 'home') }}">
                        {{ ucfirst($user->status ?? 'Home') }}
                    </span>
                </td>
                
                 <td data-label="Identifier">
                    <span class="hash">{{ $user->language ?? 'N/A' }}</span>
                </td>
                
                
                

                <td data-label="Last Seen">
                    {{ $user->issued_date ??  'N/A' }}
                </td>
                
                  <td>
   <a href="{{ route('admin.driver_details', $user->id) }}" class="btn btn-sm btn-view">
    View
</a>
    </td>
                
                
            </tr>
        @empty
           
        @endforelse

    </tbody>
</table>

    </div>

 
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
/* Sidebar toggle */
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
document.getElementById("menuToggle").onclick = () => {
  sidebar.classList.toggle("show");
  overlay.classList.toggle("show");
};
overlay.onclick = () => {
  sidebar.classList.remove("show");
  overlay.classList.remove("show");
};

/* Drivers */
const driverList = document.getElementById("driverList");
for(let i=1;i<=12;i++){
  driverList.innerHTML += `
    <div class="driver-item">
      <img src="https://i.pravatar.cc/50?img=${i}">
      <div>John Doe ${i}</div>
    </div>`;
}

/* Chart */
new Chart(document.getElementById("donutChart"), {
  type:"doughnut",
  data:{
    labels:["On Duty","Off Duty","Pending"],
    datasets:[{data:[25,20,5],backgroundColor:["#28a745","#dc3545","#ffc107"],borderWidth:0}]
  },
  options:{cutoutPercentage:70,legend:{display:true}}
});


</script>
<script>
/* Initialize map */
const map = L.map('map').setView([40.73, -73.93], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap'
}).addTo(map);

/* Helper to create pin icon */
function pinIcon(type){
  return L.divIcon({
    className:'',
    html:`<div class="pin ${type}"></div>`,
    iconSize:[18,18],
    iconAnchor:[9,18],
    popupAnchor:[0,-18]
  });
}

/* Driver data */
const drivers = [
  {name:'John Smith',status:'on',lat:40.74,lng:-73.98,vehicle:'Truck #21'},
  {name:'Alex Doe',status:'off',lat:40.71,lng:-73.95,vehicle:'Van #08'},
  {name:'Rahul Singh',status:'sleep',lat:40.72,lng:-73.91,vehicle:'Truck #12'},
  {name:'Maria Lee',status:'home',lat:40.75,lng:-73.88,vehicle:'—'},
  {name:'David Kim',status:'on',lat:40.70,lng:-73.99,vehicle:'Truck #33'}
];

/* Add markers */
drivers.forEach(d=>{
  L.marker([d.lat,d.lng],{icon:pinIcon(d.status)})
    .addTo(map)
    .bindPopup(`
      <strong>${d.name}</strong><br>
      Status: ${d.status.toUpperCase()}<br>
      Vehicle: ${d.vehicle}
    `);
});

/* Legend */
const legend = L.control({position:'bottomleft'});
legend.onAdd = function () {
  const div = L.DomUtil.create('div','map-legend');
  div.innerHTML = `
    <div><span class="pin on icon"></span> On Duty</div>
    <div><span class="pin off icon"></span> Off Duty</div>
    <div><span class="pin sleep icon"></span> Sleep</div>
    <div><span class="pin home icon"></span> Home</div>
  `;
  return div;
};
legend.addTo(map);
</script>

<script>
const searchInput = document.getElementById("searchInput");
const statusFilter = document.getElementById("statusFilter");
const rows = document.querySelectorAll("#driversTable tbody tr");

function filterTable(){
  const search = searchInput.value.toLowerCase();
  const status = statusFilter.value;

  rows.forEach(row=>{
    const text = row.innerText.toLowerCase();
    const rowStatus = row.dataset.status;
    const matchSearch = text.includes(search);
    const matchStatus = status === "all" || rowStatus === status;
    row.style.display = (matchSearch && matchStatus) ? "" : "none";
  });
}

searchInput.addEventListener("keyup", filterTable);
statusFilter.addEventListener("change", filterTable);
</script>

<script>
  /* FILTER TOGGLE */
document.getElementById("toggleFilter").onclick = () =>
    document.getElementById("filterPanel").classList.toggle("d-none");

/* APPLY FILTER */
document.getElementById("applyFilter").onclick = () => {
    document.getElementById("filterPanel").classList.add("d-none");
    applyFilter();
};
  </script>

 <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap5.js"></script>   
  <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>  
 <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>  
 
<script>
$(document).ready(function () {
    new DataTable('#driversTable', {
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>
</body>
</html>
