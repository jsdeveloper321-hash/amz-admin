@include('admin.include.header')

<!-- NAVBAR -->
@include('admin.include.navbar')

<div class="overlay" id="overlay"></div>

<div class="">
  
    <div class="page-wrapper pt-4 pb-0 pl-2">

    <style>
        /* Sidebar */
.settings-sidebar {
    background: #ffd1d1;
    min-height: 100vh;
    padding-top: 20px;
    border-radius: 0px 10px 10px 0px;
    box-shadow: 10px 0 10px -5px rgb(223 207 207 / 50%);
}

.settings-sidebar .tab-link {
    display: block;
    padding: 16px 24px;
    font-size: 15px;
    font-weight: 600;
    color: #000;
    text-decoration: none;
}

.settings-sidebar .tab-link.active,
.settings-sidebar .tab-link:hover {
    color:#920707;
}

/* Content */
.settings-content {
    background: #fff;
    padding: 30px;
    border-radius: 14px;
   /* box-shadow: 0 10px 25px rgba(0,0,0,.12);*/
    min-height: 500px;
}


    </style>    

     <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-3 col-lg-2 p-0">
            <div class="settings-sidebar">
                <a class="tab-link active" data-toggle="tab" href="#profile">Admin Profile</a>
               <!-- <a class="tab-link" data-toggle="tab" href="#apikeys">API Keys</a>-->
                <a class="tab-link" data-toggle="tab" href="#mapsetting">Map Refresh Settings</a>
               <!-- <a class="tab-link" data-toggle="tab" href="#notifications">Notifications</a>-->
            </div>
        </div>

        <!-- CONTENT -->
        <div class="col-md-9 col-lg-10">
            <div class="tab-content settings-content offer">

                <!-- ADMIN PROFILE -->
                <div class="tab-pane fade show active" id="profile">
                  
                 @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif 
                  
                <!-- ADMIN PROFILE -->
            <form method="POST" action="{{ route('admin.profile.update') }}">
    @csrf

    <input type="hidden" name="id" value="{{ $admins->id }}">

    <div class="form-group">
        <label>Full Name</label>
        <input type="text"
               name="user_name"
               class="form-control danger-input @error('user_name') is-invalid @enderror"
               value="{{ old('user_name', $admins->user_name) }}"
               required>

        @error('user_name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email"
               name="email"
               class="form-control danger-input @error('email') is-invalid @enderror"
               value="{{ old('email', $admins->email) }}"
               required>

        @error('email')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label>Current Password</label>
        <input type="password"
               name="current_password"
               class="form-control danger-input @error('current_password') is-invalid @enderror">

        @error('current_password')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label>New Password</label>
        <input type="password"
               name="new_password"
               class="form-control danger-input @error('new_password') is-invalid @enderror">

        @error('new_password')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password"
               name="new_password_confirmation"
               class="form-control danger-input @error('new_password_confirmation') is-invalid @enderror">

        @error('new_password_confirmation')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <button class="btn add-btn mt-2">Save Changes</button>
</form>

                  
                  
                  
                  
                  
                  
                  
                  
                  
                  
                </div>

                <!-- API KEYS -->
                <div class="tab-pane fade" id="apikeys">
                    <h4 class="font-weight-bold mb-3">API Keys</h4>

    <!-- API Keys Table -->
    <div class="card mb-4 api-card">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Key Name</th>
                    <th>Key</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Production Key</strong></td>
                    <td>AbcD</td>
                    <td>Dec 15, 2025</td>
                    <td>
                        <a href="#" class="text-danger font-weight-bold">Revoke</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Generate Section -->
    <h6 class="font-weight-bold mb-2">Generate or Manually Set API Key</h6>

    <div class="custom-control custom-radio mb-2">
        <input type="radio" id="autoKey" name="apiKey" class="custom-control-input" checked>
        <label class="custom-control-label" for="autoKey">
            <strong>Generate New Key (Recommended)</strong><br>
            <small class="text-muted">A strong, random API key will be generated automatically.</small>
        </label>
    </div>

    <div class="custom-control custom-radio mb-3">
        <input type="radio" id="manualKey" name="apiKey" class="custom-control-input">
        <label class="custom-control-label" for="manualKey">
            <strong>Manually Enter Key (Not Recommended)</strong>
        </label>
    </div>

    <!-- Warning -->
    <div class="alert alert-warning api-warning">
        <strong>⚠ Caution:</strong> Manually setting an API key is less secure.<br>
        <small>Use a strong, unique key to avoid security risks.</small>
    </div>

    <!-- Input -->
    <div class="form-group">
        <input type="text"
               class="form-control form-control-sm danger-input"
               placeholder="Enter API Key..."
               disabled
               id="manualKeyInput">
    </div>

    <button class="btn add-btn btn-sm">Save Key</button>
                </div>

                <!-- MAP SETTINGS -->
               
               
               <div class="tab-pane fade" id="mapsetting">
    <h4 class="font-weight-bold mb-4">Map Refresh Settings</h4>

    

    <form method="POST" action="{{ route('admin.map.setting') }}">
        @csrf

        <div class="form-group">
            <label><strong>Auto-Refresh Interval (Seconds)</strong></label>
            <input type="number"
                   name="refresh_interval"
                   class="form-control"
                   value="{{ $refreshInterval ?? 30 }}"
                   min="5"
                   required>
        </div>

        <button type="submit" class="btn add-btn mt-3">
            Save Changes
        </button>
    </form>
</div>
               
               
               
               
               
               
               
               

                <!-- NOTIFICATIONS -->
                <div class="tab-pane fade" id="notifications">
                    <h4 class="font-weight-bold mb-4">Notification Settings</h4>

    <div class="settings-list">

        <div class="setting-item">
            <label class="custom-checkbox checked">
                <input type="checkbox" checked>
                <span class="checkmark"></span>
                Email Notifications
            </label>
        </div>

        <div class="setting-item">
            <label class="custom-checkbox">
                <input type="checkbox">
                <span class="checkmark"></span>
                Email Notifications
            </label>
        </div>

        <div class="setting-item">
            <label class="custom-checkbox checked">
                <input type="checkbox" checked>
                <span class="checkmark"></span>
                Email Notifications
            </label>
        </div>

        <div class="setting-item">
            <label class="custom-checkbox checked">
                <input type="checkbox" checked>
                <span class="checkmark"></span>
                Email Notifications
            </label>
        </div>

    </div>

    <button class="btn add-btn  mt-3">Save Changes</button>
                </div>

            </div>
        </div>

    </div>
   
</div>

   </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const tabs = document.querySelectorAll(".tab-link");
    const panes = document.querySelectorAll(".tab-pane");

    tabs.forEach(tab => {
        tab.addEventListener("click", function (e) {
            e.preventDefault();

            /* Remove active from all tabs */
            tabs.forEach(t => t.classList.remove("active"));

            /* Hide all panes */
            panes.forEach(p => {
                p.classList.remove("show", "active");
            });

            /* Activate clicked tab */
            this.classList.add("active");

            /* Show corresponding pane */
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.classList.add("show", "active");
            }
        });
    });

});
</script>



<!-- JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>




</body>
</html>
