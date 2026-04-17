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
  

   

    <!-- SUMMARY TABS -->
 


    <!-- TABLE -->
    <div class="card card-custom">
      
        <div class="card-header">
            <h5 class="mb-0">Create Announcement</h5>
        </div>

        <div class="card-body">

      <form method="POST" action="{{ route('admin.add_dash_announcement.store') }}">
    @csrf

    <!-- Announcement Title -->
    <div class="mb-3">
        <label class="form-label">Announcement Title</label>
        <input type="text" name="title" class="form-control" placeholder="Enter title" required>
    </div>

    <!-- Message -->
    <div class="mb-3">
        <label class="form-label">Announcement Message</label>
        <textarea name="message" class="form-control" rows="5" placeholder="Enter full message" required></textarea>
    </div>

    <!-- City -->
    
 
    
    
<!--   <div class="mb-3">
    <label class="form-label">States</label>
    <select name="city" class="form-control" required>
        <option value="">Select States</option>
        @foreach($cities as $city)
            <option value="{{ $city->id }}">
                {{ $city->name }}
            </option>
        @endforeach
    </select>
</div>-->

    <!-- Driver Status -->
 <div class="mb-3">
    <label class="form-label">Address</label>

    <input type="text" name="city" id="address" class="form-control" placeholder="Search location...">

   <!-- <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="getLocation()">
        Use Current Location
    </button>-->

    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
</div>


 <div class="mb-3">
                      <select name="driver_status" class="form-control" required>
                        <option value="">Driver Status</option>
                        <option value="on_duty">On Duty</option>
                        <option value="off_duty">Off Duty</option>
                        <option value="sleep">Sleep</option>
                        <option value="home">Home</option>
                      </select>
                    </div>


    <!-- Radius -->
    <div class="mb-3">
        <label class="form-label">Radius (miles)</label>
        <select name="radius" class="form-control" required>
            <option value="">Select Radius</option>
            <option value="50">50 Miles</option>
            <option value="100">100 Miles</option>
            <option value="150">150 Miles</option>
        </select>
    </div>

    <!-- Announcement Type -->
    <div class="mb-3">
        <label class="form-label">Announcement Type</label>
        <select name="type" class="form-control" required>
            <option value="">Select Type</option>
            <option value="Normal">Normal</option>
            <option value="Urgent">Urgent</option>
        </select>
    </div>

    <!-- Schedule -->
    <div class="mb-3">
        <label class="form-label">Schedule Date & Time</label>
        <input type="datetime-local" name="sent_date" class="form-control">
    </div>

    <!-- Status -->
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="Sent">Sent</option>
            <option value="Draft">Draft</option>
        </select>
    </div>

    <!-- Button -->
    <div class="text-end">
        <button type="submit" class="btn btn-primary">
            Publish Announcement
        </button>
    </div>

</form>


        </div>
    </div>  
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

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDgFGS91BvviXh_f-nmvtEggUHJcaGyUwA
 &libraries=places"></script>
<script>
let autocomplete;

function initAutocomplete() {
    const input = document.getElementById('address');

    autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();

        if (!place.geometry) return;

        document.getElementById('latitude').value = place.geometry.location.lat();
        document.getElementById('longitude').value = place.geometry.location.lng();
    });
}

// Current Location Button
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {

            let lat = position.coords.latitude;
            let lng = position.coords.longitude;

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            // Reverse Geocoding
            const geocoder = new google.maps.Geocoder();
            const latlng = { lat: lat, lng: lng };

            geocoder.geocode({ location: latlng }, function(results, status) {
                if (status === "OK") {
                    if (results[0]) {
                        document.getElementById("address").value = results[0].formatted_address;
                    }
                }
            });

        }, function(error) {
            alert("Location permission denied!");
        });
    } else {
        alert("Geolocation not supported!");
    }
}

// Init call
google.maps.event.addDomListener(window, 'load', initAutocomplete);
</script>


</body>
</html>
