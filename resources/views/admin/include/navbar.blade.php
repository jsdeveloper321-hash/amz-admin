<nav class="navbar navbar-expand-lg">
  <button class="btn btn-light d-lg-none mr-2" id="menuToggle">
    <i class="fas fa-bars"></i>
  </button>

  <a class="navbar-brand font-weight-bold" href="{{route('admin.dashboard')}}">
   <img src=" {{ asset('public/assets/images/logo.png') }}" style="width:100px"/>

  </a>

  <button class="navbar-toggler" data-toggle="collapse" data-target="#topMenu">
    <span class="navbar-toggler-icon"></span>
  </button>
  
     @php
    $admin = Auth::guard('admin')->user();
@endphp
  
  

  <div class="collapse navbar-collapse" id="topMenu">
    <ul class="navbar-nav ml-auto mr-5">
        
        
        
 @if($admin->type == 'Admin')
   <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{route('admin.dashboard')}}">Home</a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.drivers') ? 'active' : '' }}" href="{{route('admin.drivers')}}">Drivers</a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.announcements') ? 'active' : '' }}" href="{{route('admin.announcements')}}">Announcements</a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.offer') ? 'active' : '' }}" href="{{route('admin.offer')}}">Offer</a>
</li>

@elseif($admin->type == 'SuperAdmin')


   <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{route('admin.dashboard')}}">Home</a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.drivers') ? 'active' : '' }}" href="{{route('admin.drivers')}}">Drivers</a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.announcements') ? 'active' : '' }}" href="{{route('admin.announcements')}}">Announcements</a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.offer') ? 'active' : '' }}" href="{{route('admin.offer')}}">Offer</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.approvals') ? 'active' : '' }}" href="{{route('admin.approvals')}}">Approvals</a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{route('admin.reports')}}">Reports</a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{route('admin.settings')}}">Settings</a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.sub_admin') ? 'active' : '' }}" href="{{ route('admin.sub_admin') }}">Admins</a>
</li>
@endif
        
   <!--
        
     <li class="nav-item"><a class="nav-link active" href="{{route('admin.dashboard')}}">Home</a></li>
     <li class="nav-item"><a class="nav-link" href=" {{route('admin.drivers')}}">Drivers</a></li>
     <li class="nav-item"><a class="nav-link" href=" {{route('admin.approvals')}}">Approvals</a></li>
     <li class="nav-item"><a class="nav-link" href=" {{route('admin.announcements')}}">Announcements</a></li>
     <li class="nav-item"><a class="nav-link" href="{{route('admin.offer')}}">Offer</a></li>
     <li class="nav-item"><a class="nav-link" href="{{route('admin.reports')}}">Reports</a></li>
     <li class="nav-item"><a class="nav-link" href=" {{route('admin.settings')}}">Settings</a></li>
     -->
       
    </ul>
    <a class="nav-link" href=" {{route('admin.logout')}}">
    <button class="btn logout-btn">Logout</button>
    </a>
    
 
  </div>
</nav>


