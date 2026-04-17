@include('admin.include.header')
@include('admin.include.navbar')

<div class="overlay" id="overlay"></div>

<div class="container">
    <div class="page-wrapper">
        <a href="{{ route('admin.approvals') }}" class="text-dark mb-5 d-inline-block">
            <i class="fa fa-arrow-left mr-1"></i> Back to Approvals
        </a>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="page-title">Driver Approvals</div>
                <div class="submitted-text">Submitted on {{ \Carbon\Carbon::parse($users_details->approval_date)->format('d M Y, h:i A') }}
</div>
            </div>
            
            @php
    $admin = Auth::guard('admin')->user();
@endphp
   @if($admin->type == 'SuperAdmin')  
            
            <div>
               <button class="btn btn-success btn-sm"
        onclick="updateStatus({{ $users_details->id }}, 'Approved')">
    Approve
</button>

<button class="btn btn-outline-danger btn-sm"
        onclick="updateStatus({{ $users_details->id }}, 'Rejected')">
    Reject
</button>
            </div>
           @endif 
            
            
        </div>

        <div class="row">
            <!-- LEFT COLUMN -->
            <div class="col-lg-8">
                <!-- License & Validation -->
                <div class="card card-custom p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>License & Validation Status</strong>
                       <span class="
    {{ $users_details->approval_status == 'Approved' ? 'status-approved' : '' }}
    {{ $users_details->approval_status == 'Rejected' ? 'status-rejected' : '' }}
">
    {{ $users_details->approval_status }}
</span>
                    </div>

                    <p class="text-muted mb-3" style="font-size:13px;">
                        DOT/MC Validation Result<br>
                        DOT and MC numbers are valid and active
                    </p>

                    <div class="info-row">
                        <span>DOT Number</span>
                        <strong>{{ $users_details->dot_number }}</strong>
                    </div>

                    <div class="info-row">
                        <span>MC Number</span>
                        <strong>MC-{{ $users_details->mc_number }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Auto-detected Company</span>
                        <strong>{{ $users_details->company_name }}</strong>
                    </div>
                </div>

                <!-- Personal Info -->
                <div class="card card-custom p-3">
                    <strong class="mb-3 d-block">Personal Information</strong>

                    <div class="info-row">
                        <span>Full Name</span>
                        <strong>{{ $users_details->user_name }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Phone Number</span>
                        <strong>{{ $users_details->mobile_number }}</strong>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-4">
                <!-- Actions -->
                <!--<div class="card card-custom p-3 mb-4">
                    <button class="btn btn-outline-secondary action-btn">Schedule Interview</button>
                    <button class="btn btn-outline-secondary action-btn">Request Documents</button>
                    <button class="btn btn-outline-secondary action-btn">Send Message</button>
                    <button class="btn btn-outline-secondary action-btn">Add Notes</button>
                </div>-->

                <!-- Experience -->
                <div class="experience-card">
                    <h6>Experience</h6>
                    <h3>   @if($years > 0)
            {{ $years }} Year{{ $years > 1 ? 's' : '' }}
        @elseif($months > 0)
            {{ $months }} Month{{ $months > 1 ? 's' : '' }}
        @else
            Less than 1 Month
        @endif</h3>
                </div>
            </div>
        </div>
    </div>
</div>



<style>
    
    .status-approved {
    color: #28a745;   /* green */
    font-weight: 600;
}

.status-rejected {
    color: #dc3545;   /* red */
    font-weight: 600;
}
</style>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function updateStatus(id, status) {

    if (!confirm(`Are you sure you want to ${status.toLowerCase()} this user?`)) return;

    fetch(`{{ route('admin.update_approval_status', ':id') }}`.replace(':id', id), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(res => res.json())
    .then(data => {
        console.log(data);

        if (data.status === 1) {
            alert(data.message);
            location.reload(); // ya button disable
        } else {
            alert(data.message);
        }
    })
    .catch(err => console.error(err));
}
</script>
