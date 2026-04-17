@include('admin.include.header')
@include('admin.include.navbar')

<div class="overlay" id="overlay"></div>

<div class="container">
    <div class="page-wrapper">
        <div class="card card-custom">
            <div class="card-header">
                <h5 class="mb-0">Update Announcement</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.update_announcements', $announcement->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Announcement Title -->
                    <div class="mb-3">
                        <label class="form-label">Announcement Title</label>
                        <input type="text" name="title" class="form-control" 
                               value="{{ old('title', $announcement->title) }}" required>
                    </div>

                    <!-- Message -->
                    <div class="mb-3">
                        <label class="form-label">Announcement Message</label>
                        <textarea name="message" class="form-control" rows="5" required>{{ old('message', $announcement->message) }}</textarea>
                    </div>

                    <!-- City -->
                    <div class="mb-3">
                        <label class="form-label">States</label>
                        <select name="city" class="form-control" required>
                            <option value="">Select States</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" 
                                    {{ $announcement->city == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Driver Status -->
                    <div class="mb-3">
                        <label class="form-label">Driver Status</label>
                        <select name="driver_status" class="form-control" required>
                            <option value="">Select Status</option>
                            <option value="on_duty" {{ $announcement->driver_status=='on_duty' ? 'selected' : '' }}>On Duty</option>
                            <option value="off_duty" {{ $announcement->driver_status=='off_duty' ? 'selected' : '' }}>Off Duty</option>
                            <option value="sleep" {{ $announcement->driver_status=='sleep' ? 'selected' : '' }}>Sleep</option>
                            <option value="home" {{ $announcement->driver_status=='home' ? 'selected' : '' }}>Home</option>
                        </select>
                    </div>

                    <!-- Radius -->
                    <div class="mb-3">
                        <label class="form-label">Radius (miles)</label>
                        <select name="radius" class="form-control" required>
                            <option value="">Select Radius</option>
                            <option value="50" {{ $announcement->radius == 50 ? 'selected' : '' }}>50 Miles</option>
                            <option value="100" {{ $announcement->radius == 100 ? 'selected' : '' }}>100 Miles</option>
                            <option value="150" {{ $announcement->radius == 150 ? 'selected' : '' }}>150 Miles</option>
                        </select>
                    </div>

                    <!-- Announcement Type -->
                    <div class="mb-3">
                        <label class="form-label">Announcement Type</label>
                        <select name="type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="Normal" {{ $announcement->type=='Normal' ? 'selected' : '' }}>Normal</option>
                            <option value="Urgent" {{ $announcement->type=='Urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <!-- Schedule -->
                    <div class="mb-3">
                        <label class="form-label">Schedule Date & Time</label>
                        <input type="datetime-local" name="sent_date" class="form-control" 
                               value="{{ old('sent_date', \Carbon\Carbon::parse($announcement->sent_date)->format('Y-m-d\TH:i')) }}">
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="Sent" {{ $announcement->status=='Sent' ? 'selected' : '' }}>Sent</option>
                            <option value="Draft" {{ $announcement->status=='Draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>

                    <!-- Button -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Update Announcement</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>