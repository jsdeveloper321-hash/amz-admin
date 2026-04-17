@include('admin.include.header')

<!-- NAVBAR -->
@include('admin.include.navbar')

<div class="overlay" id="overlay"></div>

<div class="container">
  
    <div class="page-wrapper">

   
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Add Offer</h4>
        
        @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
       
    </div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin-bottom:0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form action="{{ route('admin.offer.store') }}" method="POST">
    @csrf

    <div class="card p-4">
        <h5>Offer Upload</h5>

        <!-- Title -->
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <!-- Driver Type -->
        <div class="form-group">
            <label>Assign Drivers</label>

            <!-- All -->
            <div>
                <input type="radio" name="driver_type" value="all" checked> All Drivers
            </div>

            <!-- Company -->
            <div>
                <input type="radio" name="driver_type" value="company"> By Company
            </div>

            <div id="companyFields" style="display:none;">
                <input type="text" name="mc_number" class="form-control mt-2" placeholder="MC Number">
                <input type="text" name="dot_number" class="form-control mt-2" placeholder="DOT Number">
            </div>

            <!-- Exact -->
            <div>
                <input type="radio" name="driver_type" value="exact"> Exact Driver
            </div>

            <div id="phoneField" style="display:none;">
                <input type="text" id="driverSearch" class="form-control mt-2" placeholder="Search driver by phone">
                <small id="searchMessage"></small>

                <div id="driverList" class="list-group mt-2"></div>

                <div id="selectedDrivers" class="mt-2"></div>
            </div>

        </div>

        <button class="btn btn-primary mt-3">Submit</button>
    </div>
</form>


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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){

    // Toggle fields
    $('input[name="driver_type"]').change(function(){
        let type = $(this).val();

        $('#companyFields').hide();
        $('#phoneField').hide();

        if(type === 'company'){
            $('#companyFields').show();
        }

        if(type === 'exact'){
            $('#phoneField').show();
        }
    });

    let typingTimer;
    let doneTypingInterval = 500;

    // Search driver
    $('#driverSearch').on('keyup', function(){

        clearTimeout(typingTimer);

        let value = $(this).val();

        $('#searchMessage').text('');

        if(value.length < 3){
            $('#driverList').empty();
            return;
        }

        typingTimer = setTimeout(function(){

            $.ajax({
                url: "{{ route('search.driver') }}",
                method: "GET",
                data: { search: value },
                success: function(data){

                    $('#driverList').empty();

                    if(data.length === 0){
                        $('#searchMessage')
                            .text('❌ No driver found')
                            .css('color','red');
                    } else {
                        $('#searchMessage')
                            .text('✅ Driver found, select below')
                            .css('color','green');

                        data.forEach(driver => {
                            $('#driverList').append(`
                                <a href="#" class="list-group-item select-driver"
                                   data-id="${driver.id}"
                                   data-phone="${driver.mobile_number}">
                                   ${driver.user_name} (${driver.mobile_number})
                                </a>
                            `);
                        });
                    }
                }
            });

        }, doneTypingInterval);
    });

    // Select driver
    $(document).on('click', '.select-driver', function(e){
        e.preventDefault();

        let id = $(this).data('id');
        let phone = $(this).data('phone');

        if($('#driver_'+id).length) return;

        $('#selectedDrivers').append(`
            <div id="driver_${id}" style="display:inline-block; margin:5px; padding:6px 10px; background:#007bff; color:#fff; border-radius:5px;">
                ${phone}
                <input type="hidden" name="phone_numbers[]" value="${phone}">
                <span class="remove-driver" data-id="${id}" style="cursor:pointer; margin-left:8px;">×</span>
            </div>
        `);
    });

    // Remove driver
    $(document).on('click', '.remove-driver', function(){
        let id = $(this).data('id');
        $('#driver_'+id).remove();
    });

});
</script>


</body>
</html>
