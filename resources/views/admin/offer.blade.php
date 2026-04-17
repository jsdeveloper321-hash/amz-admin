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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Offer</h4>
        <a href="{{route('admin.add_offer')}}" class="btn btn-danger">Add Offer</a>
    </div>

    

    <!-- TABLE -->
    <div class="card card-custom">
       <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-2">
           <table class="table table-hover mb-0" id="offer">
    <thead>
        <tr>
             <th>Id</th>
            <th>Offer Name</th>
            <th>Description</th>
            <th>Assigned Drivers</th>
            <th>Completed / Pending</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>

        @forelse($offers as $offer)
        
  <tr>       
                     <td>{{ $offer->id }}</td>
                    <td>{{ $offer->title }}</td>
                    <td>{{ $offer->description }}</td>
                   <td> {{ $offer->driver_type }} </td>

                <td>{{ $offer->completed ?? 0 }} / {{ $offer->pending ?? 0 }} </td>
                
                   <td>
                        <!-- Edit Button -->
                        <a href="{{ route('admin.edit_offer', $offer->id) }}" 
                           class="btn btn-sm btn-primary">Edit</a>

                        <!-- Delete Button -->
                        <form action="{{ route('admin.delete_offer', $offer->id) }}" 
                              method="POST" style="display:inline-block;" 
                              onsubmit="return confirm('Are you sure you want to delete this offer?');">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                
                
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted">
                    No offers found
                </td>
            </tr>
        @endforelse

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


<script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap5.js"></script>   
  <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>  
 <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>  
 
<script>
$(document).ready(function () {
    new DataTable('#offer', {
        language: {
            emptyTable: "No records found"
        }
    });
});
</script>


</body>
</html>
