@include('admin.include.header')
@include('admin.include.navbar')

<div class="container">
    <div class="page-wrapper">

        <div class="card">
            <div class="card-header">
                <h5>Add Driver</h5>
            </div>

            <div class="card-body">

              
               

                <form method="POST" action="{{ route('admin.driver_store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Name -->
                    <div class="mb-3">
                        <label>Driver Name</label>
                        <input type="text" name="user_name"
                               value="{{ old('user_name') }}"
                               class="form-control @error('user_name') is-invalid @enderror">
                        @error('user_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mobile -->
                    <div class="mb-3">
                        <label>Mobile Number</label>
                        <input type="text" name="mobile_number"
                               value="{{ old('mobile_number') }}"
                               class="form-control @error('mobile_number') is-invalid @enderror">
                        @error('mobile_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div class="mb-3">
                        <label>Profile Image</label>
                        <input type="file" name="image"
                               class="form-control @error('image') is-invalid @enderror">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <!-- Driver License -->
                    <div class="mb-3">
                        <label>Driver License Number</label>
                        <input type="text" name="driver_license_number"
                               value="{{ old('driver_license_number') }}"
                               class="form-control">
                    </div>

                    <!-- Issued Date -->
                    <div class="mb-3">
                        <label>Issued Date</label>
                        <input type="date" name="issued_date"
                               value="{{ old('issued_date') }}"
                               class="form-control">
                    </div>

                    <!-- Language -->
                    <div class="mb-3">
                        <label>Language</label>
                        <input type="text" name="language"
                               value="{{ old('language') }}"
                               class="form-control">
                    </div>

                    <!-- DOT -->
                    <div class="mb-3">
                        <label>DOT Number</label>
                        <input type="text" name="dot_number"
                               value="{{ old('dot_number') }}"
                               class="form-control">
                    </div>

                    <!-- MC -->
                    <div class="mb-3">
                        <label>MC Number</label>
                        <input type="text" name="mc_number"
                               value="{{ old('mc_number') }}"
                               class="form-control">
                    </div>

                    <!-- Company -->
                    <div class="mb-3">
                        <label>Company Name</label>
                        <input type="text" name="company_name"
                               value="{{ old('company_name') }}"
                               class="form-control">
                    </div>

                    <!-- Company Authorised -->
                    <div class="mb-3">
                        <label>Company Authorised</label>
                        <input type="text" name="company_authorised"
                               value="{{ old('company_authorised') }}"
                               class="form-control">
                    </div>

                    <!-- Submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            Add Driver
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>