@include('admin.include.header')

<!-- NAVBAR -->
@include('admin.include.navbar')

<div class="overlay" id="overlay"></div>

<div class="container">
  
    <div class="page-wrapper">

   
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Add Admin</h4>
        
        @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
       
    </div>

   <form action="{{ route('admin.store_sub_admin') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="card shadow-sm border-0 rounded-lg p-4 offer">
        <h6 class="font-weight-bold mb-3"> Admin Create Form</h6>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- User Name --}}
        <div class="form-group">
            <label>User Name</label>
            <input type="text" name="user_name" class="form-control @error('user_name') is-invalid @enderror" value="{{ old('user_name') }}">
            @error('user_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label>Email</label>
            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- Mobile Number --}}
        <div class="form-group">
            <label>Mobile Number</label>
            <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}">
            @error('phone_number')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- Profile Image --}}
        <div class="form-group">
            <label>Profile Image</label>
            <input type="file" name="profile_image" class="form-control @error('profile_image') is-invalid @enderror">
            @error('profile_image')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        
        
        
        
        {{-- States Multi Select --}}
<div class="form-group">
    <label>Select States (Max 15)</label>

    <select name="states[]" class="form-control @error('states') is-invalid @enderror" multiple>
        @foreach($states as $state)
            <option value="{{ $state->id }}"
                {{ (collect(old('states'))->contains($state->id)) ? 'selected' : '' }}>
                {{ $state->name }}
            </option>
        @endforeach
    </select>

    @error('states')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
        
        
        

        <button type="submit" class="btn add-btn mt-3" style="width:150px">
            Submit
        </button>
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
<script>
$('select[name="states[]"]').on('change', function () {
    if ($(this).val().length > 15) {
        alert('Maximum 15 states allowed');
        $(this).val($(this).val().slice(0, 15));
    }
});
</script>

</body>
</html>
