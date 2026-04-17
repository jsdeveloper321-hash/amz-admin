@include('admin.include.header')
@include('admin.include.navbar')

<div class="container-fluid">
  <div class="row">

    <!-- SIDEBAR -->
    <div class="col-lg-2 sidebar">
      <input id="searchDriver" class="form-control mb-3" placeholder="Search driver...">
      <div id="driverList"></div>
    </div>

    <!-- MAIN -->
    <div class="col-lg-10 mt-3">

      <!-- DASHBOARD CARD -->
      <div class="dashboard-card p-4 mb-3">
        <div class="row">
          <div class="col-md-4 text-center">
            <canvas id="donutChart"></canvas>
            <h5 class="mt-2 font-weight-bold">
              Total Drivers<br>
              <span id="totalDrivers">0</span>
            </h5>
          </div>
          <div class="col-md-8">
            <small>On Duty</small>
            <div class="progress mb-2"><div class="progress-bar bg-success" id="onBar"></div></div>
            <small>Off Duty</small>
            <div class="progress mb-2"><div class="progress-bar bg-danger" id="offBar"></div></div>
            <small>Sleep</small>
            <div class="progress mb-2"><div class="progress-bar bg-warning" id="sleepBar"></div></div>
            <small>Home</small>
            <div class="progress mb-2"><div class="progress-bar bg-primary" id="homeBar"></div></div>

            <div style="height:350px; overflow-y:auto" class="mt-4">
              <div class="card card-custom">
                <div class="card-header bg-white">
                  <h5>Create Announcement</h5>
                  @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                  @endif
                </div>
                <div class="card-body">
                  <form method="POST" action="{{ route('admin.announcements.store') }}">
                    @csrf
                    <div class="mb-3">
                      <input type="text" name="title" class="form-control" placeholder="Enter title" required>
                    </div>
                    <div class="mb-3">
                      <textarea name="message" class="form-control" rows="5" placeholder="Enter message" required></textarea>
                    </div>
                    
                
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
                      <select name="driver_status" class="form-control" required placeholder="Status...">
                        <option value="">Driver Status</option>
                        <option value="on_duty">On Duty</option>
                        <option value="off_duty">Off Duty</option>
                        <option value="sleep">Sleep</option>
                        <option value="home">Home</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <select name="radius" class="form-control" required>
                        <option value="">Radius</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="150">150</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <select name="type" class="form-control" required>
                        <option value="">Type</option>
                        <option value="Normal">Normal</option>
                        <option value="Urgent">Urgent</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <input type="datetime-local" name="sent_date" class="form-control">
                    </div>
                    
                      <!-- Schedule -->
   

    <!-- Status -->
    <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="Sent">Sent</option>
            <option value="Draft">Draft</option>
        </select>
    </div>
                    
                    
                    
                    
                    
                    
                    
                    <div class="mb-3 text-end">
                      <button type="submit" class="btn btn-primary">Publish</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- FILTER -->
      <div class="card p-3 mb-2">
        <div class="row">
          <div class="col-md-3 position-relative">
            <input type="text" id="locationSearch" class="form-control" placeholder="Search location...">
            <div id="locationSuggestions" class="list-group position-absolute w-100" style="z-index:1000;"></div>
          </div>
          <div class="col-md-3">
            <select id="radiusSelect" class="form-control">
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="150">150</option>
            </select>
          </div>
          <div class="col-md-6">
            <label><input type="checkbox" class="status" value="on_duty" checked> On</label>
            <label><input type="checkbox" class="status" value="off_duty" checked> Off</label>
            <label><input type="checkbox" class="status" value="break" checked> Break</label>
            <label><input type="checkbox" class="status" value="home" checked> Home</label>
          </div>
        </div>
      </div>

      <!-- MAP -->
      <div class="card p-3 position-relative">
        <button id="fullscreenBtn" class="btn btn-dark btn-sm" style="position:absolute; top:10px; right:10px; z-index:1000;">
          Fullscreen
        </button>
        <div id="map"></div>
      </div>

    </div>
  </div>
</div>

<style>
#map {height:500px;width:100%;transition:0.3s;}
.fullscreen-map {position:fixed !important; top:0; left:0; width:100vw !important; height:100vh !important; z-index:9999; background:#fff;}
.dot{ width:14px;height:14px;border-radius:50%;display:inline-block;border:2px solid #fff;}
.rhombus{
  width:16px;
  height:16px;
  transform:rotate(45deg);
  display:inline-block;
  border:2px solid #fff;
}
.on_duty{background:#28a745;} .off_duty{background:#dc3545;} .break{background:#ffc107;} .home{background:#007bff;}
</style>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>




<script>
/* ===== GLOBALS ===== */
let map, donutChart, markers=[], radiusCircle=null, drivers=[], selectedLocation=null;
const driverList=document.getElementById("driverList");
const radiusSelect=document.getElementById("radiusSelect");
const searchInput=document.getElementById("searchDriver");
const locationSearch=document.getElementById("locationSearch");
const locationSuggestions=document.getElementById("locationSuggestions");
const statusMap={on_duty:'on_duty',off_duty:'off_duty',break:'break',home:'home'};

/* ===== MAP INIT ===== */
map = L.map('map').setView([36.966428,-95.844032],5);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
let markerCluster=L.markerClusterGroup().addTo(map);

/* ===== CHART ===== */
donutChart=new Chart(document.getElementById("donutChart"),{
  type:"doughnut",
  data:{labels:["On","Off","Sleep","Home"],datasets:[{data:[0,0,0,0],backgroundColor:["#28a745","#dc3545","#ffc107","#007bff"],borderWidth:0}]},
  options:{cutout:"70%"}
});

/* ===== DISTANCE CALC ===== */
function distanceMiles(lat1,lng1,lat2,lng2){
  const R=3958.8,dLat=(lat2-lat1)*Math.PI/180,dLng=(lng2-lng1)*Math.PI/180;
  const a=Math.sin(dLat/2)**2+Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
  return R*(2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a)));
}

/* ===== ICON ===== */
function pinIcon(status,type){
  let shape='dot';
  if(type?.toLowerCase()==='team') shape='rhombus';

  return L.divIcon({
    className:'',
    html:`<span class="${shape} ${status}"></span>`,
    iconSize:[16,16],
    iconAnchor:[8,8]
  });
}

/* ===== GET CURRENT LOCATION ===== */
function setDefaultLocation() {
  locationSearch.value = "Detecting location...";

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        selectedLocation = [lat, lng];
        locationSearch.value = "Current Location";

        radiusSelect.value = 50; // default radius

        renderMap();
      },
      (error) => {
        console.warn("Location denied, using fallback");

        // fallback (Indore)
        selectedLocation = [36.966428,-95.844032];
        locationSearch.value = "Search Location";

        radiusSelect.value = 50;

        renderMap();
      }
    );
  } else {
    console.warn("Geolocation not supported");

    selectedLocation = [36.966428,-95.844032];
    radiusSelect.value = 50;

    renderMap();
  }
}

/* ===== LOCATION AUTOCOMPLETE ===== */
async function fetchLocationSuggestions(query){
  if(!query) return [];
  const url=`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`;
  const res=await fetch(url);
  return res.json();
}

locationSearch.addEventListener("input", async function(){
  const q=this.value.trim(); locationSuggestions.innerHTML='';
  if(!q) return;
  const results=await fetchLocationSuggestions(q);
  results.slice(0,5).forEach(loc=>{
    const item=document.createElement("a");
    item.className="list-group-item list-group-item-action";
    item.innerText=loc.display_name;
    item.dataset.lat=loc.lat;
    item.dataset.lon=loc.lon;
    locationSuggestions.appendChild(item);
  });
});

locationSuggestions.addEventListener("click", function(e){
  if(e.target.matches("a")){
    selectedLocation=[parseFloat(e.target.dataset.lat),parseFloat(e.target.dataset.lon)];
    locationSearch.value=e.target.innerText;
    locationSuggestions.innerHTML='';
    renderMap();
  }
});

/* ===== RENDER MAP ===== */
function renderMap(){
  markerCluster.clearLayers();
  driverList.innerHTML='';
  if(radiusCircle) map.removeLayer(radiusCircle);
  if(!selectedLocation) return;

  const cityCoords=selectedLocation;
  const radius=parseInt(radiusSelect.value)||50;
  const activeStatus=[...document.querySelectorAll('.status:checked')].map(e=>e.value);
  const searchText=searchInput?.value?.toLowerCase()?.trim()||'';

  map.setView(cityCoords,11);

  radiusCircle=L.circle(cityCoords,{
    radius:radius*1609,
    color:'#007bff',
    fillOpacity:0.08
  }).addTo(map);

  const bounds=L.latLngBounds([]);

  drivers.forEach(d=>{
    if(!d.lat||!d.lng) return;

    const userName=d.user_name||'';
    const statusRaw=d.status||'';
    const typeRaw=d.type||'team';

    if(searchText && !userName.toLowerCase().includes(searchText)) return;
    if(!activeStatus.includes(statusMap[statusRaw])) return;

    const lat=parseFloat(d.lat),lng=parseFloat(d.lng);

    if(distanceMiles(cityCoords[0],cityCoords[1],lat,lng)>radius) return;

    const marker=L.marker([lat,lng],{icon:pinIcon(statusMap[statusRaw],typeRaw)})
      .bindPopup(`<b>${userName}</b><br>${statusRaw.replace('_',' ').toUpperCase()}<br>Type: ${typeRaw.toUpperCase()}`);

    markerCluster.addLayer(marker);
    bounds.extend([lat,lng]);

    driverList.innerHTML+=`
      <div class="driver-item">
        <img src="https://parkinson-ahmedabad.com/wp-content/uploads/2018/11/male-1.png" width="40">
        <div>
          <strong>${userName}</strong><br>
          <small>${statusRaw.replace('_',' ').toUpperCase()}</small><br>
          <small>Type: ${typeRaw.toUpperCase()}</small>
        </div>
      </div>`;
  });

  if(bounds.isValid()) map.fitBounds(bounds.pad(0.2));
}

/* ===== DASHBOARD LOAD ===== */
function loadDashboard(){
  fetch("{{ route('admin.dashboardData') }}")
    .then(res=>res.json())
    .then(data=>{
      drivers=data.drivers||[];

      totalDrivers.innerText=data.total||0;
      onBar.style.width=data.total?(data.on/data.total*100)+'%':'0%';
      offBar.style.width=data.total?(data.off/data.total*100)+'%':'0%';
      sleepBar.style.width=data.total?(data.sleep/data.total*100)+'%':'0%';
      homeBar.style.width=data.total?(data.home/data.total*100)+'%':'0%';

      donutChart.data.datasets[0].data=[
        data.on||0,
        data.off||0,
        data.sleep||0,
        data.home||0
      ];
      donutChart.update();

      renderMap();
    })
    .catch(err=>console.error("Dashboard Load Error:",err));
}

/* ===== EVENTS ===== */
document.querySelectorAll('#radiusSelect,.status')
  .forEach(e=>e.addEventListener('change',renderMap));

searchInput?.addEventListener('keyup',renderMap);

/* ===== INIT ===== */
setDefaultLocation();   // 👈 AUTO LOCATION
loadDashboard();
setInterval(loadDashboard,20000);

/* ===== FULLSCREEN ===== */
const btn=document.getElementById("fullscreenBtn");
const mapDiv=document.getElementById("map");
let isFull=false;

btn.addEventListener("click",()=>{
  isFull=!isFull;
  mapDiv.classList.toggle("fullscreen-map");
  btn.innerText=isFull?"Exit":"Fullscreen";
  setTimeout(()=>map.invalidateSize(),300);
});

document.addEventListener("keydown",(e)=>{
  if(e.key==="Escape" && isFull){
    mapDiv.classList.remove("fullscreen-map");
    btn.innerText="Fullscreen";
    isFull=false;
    setTimeout(()=>map.invalidateSize(),300);
  }
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








