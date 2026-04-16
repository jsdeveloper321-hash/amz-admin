<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use App\Models\Category;
use Hash;
use DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Mail\AccountActivatedMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AdminPageController extends Controller {

   public function dashboard1111() {
    $data['menu'] = 'dashboard';
    $userCount = DB::table('users')->where(['type'=>'User'])->count();
    $InstitutionCount= DB::table('users')->where(['type'=>'Institution'])->count();
    return view('admin.dashboard', compact('data', 'userCount','InstitutionCount'));
    }
    
    public function dashboard()
    {
        $data['menu'] = 'dashboard';
        $cities = DB::table('states')->orderBy('name', 'ASC')->get();
         return view('admin.dashboard', compact('data','cities'));
    }

    public function dashboardData()
    {
        $total = DB::table('users')->count();

        $on    = DB::table('duty_logs')->where('status','on_duty')->count();
        $off   = DB::table('duty_logs')->where('status','off_duty')->count();
        $sleep = DB::table('duty_logs')->where('status','break')->count();
        $home  = DB::table('duty_logs')->where('status','home')->count();

       // $drivers = DB::table('users')->select('user_name','status','lat','lng','type')->get();
       
       $drivers = DB::table('users')
    ->select('user_name','status','lat','lng','type')
    ->whereNotNull('lat')
    ->whereNotNull('lng')
    ->whereIn('status', ['off_duty','home'])
    ->get();
  
      $cities = DB::table('states')
        ->where('status','active')
        ->select('id','name','lat','lng')
        ->get();

        return response()->json([
            'total'   => $total,
            'on'      => $on,
            'off'     => $off,
            'sleep'   => $sleep,
            'home'    => $home,
            'drivers' => $drivers,
              'cities'  => $cities
        ]);
    }

    
    
     public function add_driver() {
         $data['menu'] = 'drivers';
      
           return view('admin.add_driver', compact('data'));
      }
    
   public function drivers() {
         $data['menu'] = 'drivers';
         $users = DB::table('users')->get();
           return view('admin.drivers', compact('data', 'users'));
      }
      
       
  public function driver_details($id){
      $userId = $id;
     $data['menu'] = 'approval details';
     $users_details = DB::table('users')->where('id', $id)->first();
     
     
     $dutyLogs = DB::table('duty_logs')
        ->where('user_id', $userId)
        ->orderBy('start_time', 'desc')
        ->limit(10) // last 10 logs
        ->get();
     
     
     
      $recentActivities = DB::table('duty_logs')
        ->leftJoin('users', 'duty_logs.user_id', '=', 'users.id')
        ->select(
            'users.user_name',
            'duty_logs.status',
            'duty_logs.created_at'
        )
        ->orderBy('duty_logs.created_at', 'desc')
        ->limit(5)
        ->get();
     
     
     
      $recentTrips = DB::table('duty_logs')
        ->leftJoin('users', 'duty_logs.user_id', '=', 'users.id')
        ->select(
            'duty_logs.duty_request_id',
            'duty_logs.start_time',
            'duty_logs.end_time',
            'duty_logs.duration_minutes',
            'users.user_name'
        )
        ->whereNotNull('duty_logs.end_time') // only completed trips
        ->orderBy('duty_logs.start_time', 'desc')
        ->limit(5)
        ->get();

     
     
      

    $today      = Carbon::today();
    $weekStart  = Carbon::now()->startOfWeek();
    $weekEnd    = Carbon::now()->endOfWeek();
    
    
    $monthStart = Carbon::now()->startOfMonth();
    $monthEnd   = Carbon::now()->endOfMonth();

    // Aaj ke total minutes (sirf user_id)
    $todayMinutes = DB::table('duty_logs')
        ->where('user_id', $userId)
        ->whereDate('start_time', $today)
        ->sum('duration_minutes');

    // Is week ke total minutes (sirf user_id)
    $weekMinutes = DB::table('duty_logs')
        ->where('user_id', $userId)
        ->whereBetween('start_time', [$weekStart, $weekEnd])
        ->sum('duration_minutes');
        
        
        // This Month Miles
    $monthMiles = DB::table('duty_logs')
        ->where('user_id', $userId)
        ->whereBetween('start_time', [$monthStart, $monthEnd])
        ->sum('duration_minutes');

    // Total Trips
    $totalTrips = DB::table('duty_logs')
        ->where('user_id', $userId)
        ->count();  
        
        
        

    $hoursToday = round($todayMinutes / 60, 1);
    $hoursWeek  = round($weekMinutes / 60, 1);
     
     
     
     
     
     
     
     return view('admin.driver_details', compact('data', 'users_details','hoursToday', 'hoursWeek','monthMiles','totalTrips','dutyLogs','recentActivities','recentTrips'));
}
      
      
      
      
      
      
      
      
  public function approvals()
{
    $data['menu'] = 'approvals';

    $users = DB::table('users')
       // ->where('type', 'Driver')
        ->orderBy('created_at', 'desc')
        ->get();

    $totalCount    = $users->count();
    $approvedCount = $users->where('approval_status', 'Approved')->count();
    $warningCount  = $users->where('approval_status', 'Warning')->count();
    $rejectedCount = $users->where('approval_status', 'Rejected')->count();
    $pendingCount  = $users->where('approval_status', 'Pending')->count();

    return view(
        'admin.approvals',
        compact(
            'data',
            'users',
            'totalCount',
            'approvedCount',
            'warningCount',
            'rejectedCount',
            'pendingCount'
        )
    );
}

      
      
public function approval_details($id)
{
    $data['menu'] = 'approval details';

    $users_details = DB::table('users')
        ->where('id', $id)
        ->first();
        
        
  $createdAt = Carbon::parse($users_details->created_at);
  $now = Carbon::now();

$years = $createdAt->diffInYears($now);
$months = $createdAt->copy()->addYears($years)->diffInMonths($now);
      
        
        

    return view('admin.approval-details', compact('data', 'users_details','years','months'));
}
     
     
     
     
     
      
   public function updateApprovalStatus(Request $request, $id)
{
    $status = $request->status; // Approved / Rejected

    if (!in_array($status, ['Approved', 'Rejected'])) {
        return response()->json([
            'status' => 0,
            'message' => 'Invalid status'
        ]);
    }

    $user = DB::table('users')->where('id', $id)->first();
    if (!$user) {
        return response()->json([
            'status' => 0,
            'message' => 'User not found'
        ]);
    }

    DB::table('users')->where('id', $id)->update([
        'approval_status' => $status,
        'approval_date'   => now()
    ]);

    return response()->json([
        'status'  => 1,
        'message' => "User $status successfully"
    ]);
}





      
      
      
      
        public function add_announcements() {
         $data['menu'] = 'announcements';
         
         $cities = DB::table('states')
                ->orderBy('name', 'ASC')
                ->get();
         
         
         return view('admin.add_announcements', compact('data','cities'));
      }
      
      
     public function announcements()
{
    $data['menu'] = 'announcements';

    $announcements = DB::table('announcements')
        ->orderBy('sent_date', 'desc')
        ->get();

    $totalCount  = $announcements->count();
    $urgentCount = $announcements->where('type', 'Urgent')->count();
    $normalCount = $announcements->where('type', 'Normal')->count();

    return view(
        'admin.announcements',
        compact('data', 'announcements', 'totalCount', 'urgentCount', 'normalCount')
    );
}



public function storeAnnouncement(Request $request)
{
    $request->validate([
        'title' => 'required',
        'message' => 'required',
        'type' => 'required',
        'status' => 'required'
    ]);

    DB::table('announcements')->insert([
        'title'             => $request->title,
        'city' => $request->city,
        'message'           => $request->message,
        'type'              => $request->type,
       'driver_status'          => $request->driver_status,
        'sent_date'       => $request->sent_date,
        'status'            => $request->status,
         'radius'            => $request->radius,
         'latitude'            => $request->latitude,
          'longitude'            => $request->longitude,
        //'sent_date'         => now(),
        'created_at'        => now(),
        'updated_at'        => now()
    ]);
return redirect('admin/dashboard')
        ->with('success', 'Announcement published successfully');
  //  return redirect()->back()->with('success', 'Announcement published successfully');
}




public function add_dash_announcement(Request $request)
{
    $request->validate([
        'title' => 'required',
        'message' => 'required',
        'type' => 'required',
        'status' => 'required'
    ]);

    DB::table('announcements')->insert([
        'title'             => $request->title,
        'city' => $request->city,
        'message'           => $request->message,
        'type'              => $request->type,
       'driver_status'          => $request->driver_status,
        'sent_date'       => $request->sent_date,
        'status'            => $request->status,
         'radius'            => $request->radius,
           'latitude'            => $request->latitude,
          'longitude'            => $request->longitude,
        //'sent_date'         => now(),
        'created_at'        => now(),
        'updated_at'        => now()
    ]);
return redirect('admin/announcements')
        ->with('success', 'Announcement published successfully');
  //  return redirect()->back()->with('success', 'Announcement published successfully');
}







public function update_announcements(Request $request, $id)
{
    // Validation (optional but recommended)
    $request->validate([
        'title' => 'required|string|max:255',
        'message' => 'required|string',
        'city' => 'required|integer',
        'driver_status' => 'required|string',
        'radius' => 'required|integer',
        'type' => 'required|string',
        'sent_date' => 'nullable|date',
        'status' => 'required|string',
    ]);

    // DB query se update
    DB::table('announcements')
        ->where('id', $id)
        ->update([
            'title' => $request->title,
            'message' => $request->message,
            'city' => $request->city,
            'driver_status' => $request->driver_status,
            'radius' => $request->radius,
            'type' => $request->type,
            'sent_date' => $request->sent_date,
            'status' => $request->status,
            'updated_at' => now(), // agar timestamps use ho rahe hain
        ]);
        
      return redirect('admin/announcements')
        ->with('success', 'Announcement updated successfully');  
        


}



public function edit_announcement($id)
{
    $announcement = DB::table('announcements')->where('id', $id)->first();
  
     $cities = DB::table('states')->orderBy('name', 'ASC')->get();
         
     return view('admin.update_announcements', compact('announcement', 'cities'));
}

// Delete Method
public function delete_announcement($id)
{
    DB::table('announcements')->where('id', $id)->delete();
 
      return redirect('admin/announcements')
        ->with('success', 'Announcement deleted successfully');                    
}





 public function add_offer() {
         $data['menu'] = 'add offer';
         return view('admin.add-offer', compact('data'));
      }

      
      
      
     public function offer(){
     $data['menu'] = 'offer';
    $offers = DB::table('offers')->get();
    foreach ($offers as $offer) {
    $offer->completed = DB::table('offer_driver_status')
        ->where('offer_id', $offer->id)
        ->where('status', 'completed')
        ->count();

    $offer->pending = DB::table('offer_driver_status')
        ->where('offer_id', $offer->id)
        ->where('status', 'pending')
        ->count();
}

    return view('admin.offer', compact('offers','data'));
} 
      
      
      
 public function storeOffer(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'driver_type' => 'required|in:all,company,exact',

        'mc_number' => 'required_if:driver_type,company|nullable|string',
        'dot_number' => 'required_if:driver_type,company|nullable|string',

        'phone_numbers' => 'required_if:driver_type,exact|array',
        'phone_numbers.*' => 'exists:users,mobile_number'
    ]);

    // Insert Offer
    $offerId = DB::table('offers')->insertGetId([
        'title' => $request->title,
        'description' => $request->description,
        'driver_type' => $request->driver_type,
        'mc_number' => $request->mc_number,
        'dot_number' => $request->dot_number,
        'created_at' => now()
    ]);

    $query = DB::table('users')->where('approval_status', 'Approved');

    if ($request->driver_type == 'company') {
        if ($request->mc_number) {
            $query->where('mc_number', $request->mc_number);
        }
        if ($request->dot_number) {
            $query->where('dot_number', $request->dot_number);
        }
    }

    elseif ($request->driver_type == 'exact') {

        // Extra validation safety
        $validPhones = DB::table('users')
            ->whereIn('mobile_number', $request->phone_numbers)
            ->pluck('mobile_number')
            ->toArray();

        if (count($validPhones) !== count($request->phone_numbers)) {
            return back()->with('error', 'Some phone numbers are invalid');
        }

        $query->whereIn('mobile_number', $request->phone_numbers);
    }

    $drivers = $query->get();

    foreach ($drivers as $driver) {
        DB::table('offer_driver_status')->insert([
            'offer_id' => $offerId,
            'driver_id' => $driver->id,
            'status' => 'pending',
            'created_at' => now()
        ]);
        
        
          // 🔥 Notification Insert
        DB::table('notifications')->insert([
            'user_id' => $driver->id,
            'title' => 'New Offer Available',
            'message' => $request->title,
            'type' => 'offer',
            'reference_id' => $offerId,
            'is_read' => 0,
            'created_at' => now()
        ]);   
        
        
    }

    return back()->with('success', 'Offer Published Successfully');
}     
      
      
       
      
 public function searchDriver(Request $request)
{
    $search = $request->search;

    $drivers = DB::table('users')
        ->where('approval_status', 'Approved')
        ->where('mobile_number', 'LIKE', "%$search%")
        ->limit(10)
        ->get(['id', 'user_name', 'mobile_number']);

    return response()->json($drivers);
}     
      
     public function storeOffer_old(Request $request)
{
    $request->validate([
        'title' => 'required',
        'description' => 'required',
        'drivers' => 'required'
    ]);

  $offerId =  DB::table('offers')->insertGetId([
        'title' => $request->title,
        'description' => $request->description,
        'video_or_url' => $request->video_or_url,
        'url' => $request->url,
        'assign_drivers' => json_encode($request->drivers),
        'created_at' => now()
    ]);


 

 $drivers = DB::table('users')
           // ->where('role', 'driver')
            ->where('approval_status', 'Approved')
            ->get();

        foreach ($drivers as $driver) {
            DB::table('offer_driver_status')->insert([
                'offer_id' => $offerId,
                'driver_id' => $driver->id,
                'status' => 'pending',
                'created_at' => now()
            ]);
        }



    return redirect()->back()->with('success', 'Offer published successfully');
}
 
 
 
 
 public function edit_offer($id)
{
    $offer = DB::table('offers')->where('id', $id)->first();
    return view('admin.edit_offer', compact('offer'));
}



public function update_offer(Request $request, $id)
{
    // Validation
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'driver_type' => 'required|in:all,company,exact',
        'mc_number' => 'required_if:driver_type,company|nullable|string',
        'dot_number' => 'required_if:driver_type,company|nullable|string',
        'phone_numbers' => 'required_if:driver_type,exact|array',
       // 'phone_numbers.*' => 'exists:users,mobile_number'
    ]);

    // Update Offer
    DB::table('offers')
        ->where('id', $id)
        ->update([
            'title' => $request->title,
            'description' => $request->description,
            'driver_type' => $request->driver_type,
            'mc_number' => $request->mc_number,
            'dot_number' => $request->dot_number,
          //  'updated_at' => now()
        ]);

    // Delete existing driver assignments
    DB::table('offer_driver_status')->where('offer_id', $id)->delete();

    // Driver query
    $query = DB::table('users')->where('approval_status', 'Approved');

    if ($request->driver_type == 'company') {
        if ($request->mc_number) $query->where('mc_number', $request->mc_number);
        if ($request->dot_number) $query->where('dot_number', $request->dot_number);
    } 
    elseif ($request->driver_type == 'exact') {
        $validPhones = DB::table('users')
            ->whereIn('mobile_number', $request->phone_numbers)
            ->pluck('mobile_number')
            ->toArray();

        if (count($validPhones) !== count($request->phone_numbers)) {
            return back()->with('error', 'Some phone numbers are invalid');
        }

        $query->whereIn('mobile_number', $request->phone_numbers);
    }

    $drivers = $query->get();

    foreach ($drivers as $driver) {

        // Insert into offer_driver_status
        DB::table('offer_driver_status')->insert([
            'offer_id' => $id,
            'driver_id' => $driver->id,
            'status' => 'pending',
            'created_at' => now()
        ]);

        // Insert notification
        DB::table('notifications')->insert([
            'user_id' => $driver->id,
            'title' => 'Updated Offer Available',
            'message' => $request->title,
            'type' => 'offer',
            'reference_id' => $id,
            'is_read' => 0,
            'created_at' => now()
        ]);
    }

    return redirect()->route('admin.offer')
                     ->with('success', 'Offer updated successfully!');
}
 
 
 
 
 public function delete_offer($id)
{
    // Delete offer, driver assignments and notifications
    DB::table('offers')->where('id', $id)->delete();
    DB::table('offer_driver_status')->where('offer_id', $id)->delete();
    DB::table('notifications')->where('reference_id', $id)->where('type','offer')->delete();

    return redirect()->route('admin.offer')
                     ->with('success','Offer deleted successfully!');
}
 
 
 
 public function reports(Request $request)
{
    $data = 'reports';

    $query = DB::table('duty_logs')
        ->leftJoin('users', 'duty_logs.user_id', '=', 'users.id')
        ->select(
            'users.user_name as driver_name',
            'users.company_name',
            DB::raw('SUM(duty_logs.duration_minutes) as total_miles'),
            DB::raw('MAX(duty_logs.end_time) as last_seen')
        )
        ->groupBy('duty_logs.user_id', 'users.user_name', 'users.company_name');

    // ✅ 1. Custom Date Range
    if ($request->from_date && $request->to_date) {
        $query->whereBetween('duty_logs.start_time', [
            $request->from_date . ' 00:00:00',
            $request->to_date . ' 23:59:59'
        ]);
    }

    // ✅ 2. Quick Filters
    elseif ($request->filter_type) {

        if ($request->filter_type == 'today') {
            $query->whereDate('duty_logs.start_time', Carbon::today());
        }

        elseif ($request->filter_type == 'week') {
            $query->whereBetween('duty_logs.start_time', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ]);
        }

        elseif ($request->filter_type == 'last7') {
            $query->where('duty_logs.start_time', '>=', Carbon::now()->subDays(7));
        }

        elseif ($request->filter_type == 'month') {
            $query->whereMonth('duty_logs.start_time', Carbon::now()->month)
                  ->whereYear('duty_logs.start_time', Carbon::now()->year);
        }

        elseif ($request->filter_type == '4weeks') {
            $query->where('duty_logs.start_time', '>=', Carbon::now()->subWeeks(4));
        }
    }

    // ✅ 3. Specific Month Filter
    if ($request->month) {
        $date = explode('-', $request->month);

        $query->whereYear('duty_logs.start_time', $date[0])
              ->whereMonth('duty_logs.start_time', $date[1]);
    }

    // ✅ 4. Company Filter
    if ($request->company) {
        $query->where('users.company_name', $request->company);
    }

    // ✅ Final Data
    $dutyLogs = $query->orderBy('last_seen', 'desc')->get();

    // Companies list
    $companies = DB::table('users')
        ->select('company_name')
        ->distinct()
        ->pluck('company_name');

    return view('admin.reports', compact('dutyLogs', 'companies', 'data'));
}
 
 
 
 
 
 
 
 public function reports_old(Request $request)
{
    
    $data = 'reports';
    
    $week = $request->input('week'); // e.g., '2025-12-09'
    $company = $request->input('company');

    $query = DB::table('duty_logs')
        ->leftJoin('users', 'duty_logs.user_id', '=', 'users.id')
        ->select(
            'users.user_name as driver_name',
            'users.company_name',
            DB::raw('SUM(duty_logs.duration_minutes) as total_miles'),
            DB::raw('MAX(duty_logs.end_time) as last_seen')
        )
        ->groupBy('duty_logs.user_id', 'users.user_name', 'users.company_name');

    // Filter by week
    if ($week) {
        $startOfWeek = Carbon::parse($week)->startOfWeek()->toDateString();
        $endOfWeek = Carbon::parse($week)->endOfWeek()->toDateString();
        $query->whereBetween('duty_logs.start_time', [$startOfWeek, $endOfWeek]);
    }

    // Filter by company
    if ($company) {
        $query->where('users.company_name', $company);
    }

    $dutyLogs = $query->orderBy('last_seen', 'desc')->get();

    // Companies list for filter dropdown
    $companies = DB::table('users')->select('company_name')->distinct()->pluck('company_name');

    return view('admin.reports', compact('dutyLogs','companies','data'));
}
 
 
 
 
 
 
      
      public function reports11() {
         $data['menu'] = 'reports';
         return view('admin.reports', compact('data'));
      }
      
       public function settings() {
         $data['menu'] = 'settings';
         $admin = Auth::guard('admin')->user();
         $admins = DB::table('admins')->where('id', $admin->id)->first();
         
         
         $refreshInterval = DB::table('settings')
        ->where('setting_key', 'map_refresh_interval')
        ->value('setting_value');
         
         
         return view('admin.settings', compact('data','admins','refreshInterval'));
      }
      
  public function updateSettings(Request $request)
{
    $adminId = $request->id;

    $request->validate(
        [
            'user_name'    => 'required|string|max:255',
            'email'        => 'required|email',
            'new_password' => 'nullable|min:6|confirmed',
        ],
        [
            'user_name.required' => 'Full name is required',
            'email.required'     => 'Email is required',
            'email.email'        => 'Enter a valid email address',
            'new_password.min'   => 'Password must be at least 6 characters',
            'new_password.confirmed' => 'New password and confirm password do not match',
        ]
    );

    // get admin
    $admin = DB::table('admins')->where('id', $adminId)->first();

    if (!$admin) {
        return back()->with('error', 'Admin not found');
    }

    // update name & email
    DB::table('admins')
        ->where('id', $adminId)
        ->update([
            'user_name'   => $request->user_name,
            'email'       => $request->email,
            'updated_at'  => now(),
        ]);

    // password update
    if ($request->new_password) {

        if (!$request->current_password) {
            return back()->withErrors([
                'current_password' => 'Current password is required'
            ]);
        }

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect'
            ]);
        }

        DB::table('admins')
            ->where('id', $adminId)
            ->update([
                'password'   => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);
    }

    return back()->with('success', 'Settings updated successfully');
}





public function add_training_offer(Request $request)
{
        $admin = Auth::guard('admin')->user();
    
    
    $request->validate([
        'offer_text' => 'required|string'
    ]);

    DB::table('training_offer')->insert([
        'user_id'    => $admin->id,
        'offer_text' => $request->offer_text,
        'created_at' => now()
    ]);

    return back()->with('success', 'Offer sent successfully');
}


public function saveMapSetting(Request $request)
{
    $request->validate([
        'refresh_interval' => 'required|numeric|min:5'
    ]);

    DB::table('settings')->updateOrInsert(
        ['setting_key' => 'map_refresh_interval'],
        [
            'setting_value' => $request->refresh_interval,
            'updated_at' => now(),
            'created_at' => now()
        ]
    );

    return back()->with('success', 'Map setting updated successfully');
}






public function view_admin($id)
{
    // Admin Details
    $admin = DB::table('admins')->where('id', $id)->first();

    // States
    $states = DB::table('user_states')
        ->join('states', 'user_states.state_id', '=', 'states.id')
        ->where('user_states.user_id', $id)
        ->pluck('states.name');

    // Logs (login/logout)
    $logs = DB::table('admin_logs')
        ->where('admin_id', $id)
        ->orderBy('login_time', 'desc')
        ->get();

    return view('admin.view_admin', compact('admin','states','logs'));
}



 public function add_sub_admin() {
         $data['menu'] = 'Admin';
         
         $states = DB::table('states')->get();
         
         return view('admin.add_sub_admin', compact('data','states'));
      }

      
      
      
     public function sub_admin(){
     $data['menu'] = 'Admin';
    $admins = DB::table('admins')->where('type','Admin')->get();
    
    

    return view('admin.sub_admin', compact('admins','data'));
} 
      
      
      public function store_sub_admin(Request $request)
{
    $request->validate([
        'user_name'     => 'required|string|max:255',
        'phone_number'  => 'required|string|max:20',
        'email'         => 'required|email|unique:admins,email',
        'password'      => 'required|min:6',
        'profile_image' => 'required|image|mimes:jpg,jpeg,png',
        'states' => 'required|array|max:15'
    ]);

    // Upload Profile Image
    if ($request->hasFile('profile_image')) {
        $image = $request->file('profile_image');
        $imageName = time().'_'.$image->getClientOriginalName();
        $image->move(public_path('uploads/admins'), $imageName);
    }

    // Insert Data
    $adminId  = DB::table('admins')->insertGetId([
        'user_name'     => $request->user_name,
        'phone_number'  => $request->phone_number,
        'email'         => $request->email,
        'password'      => Hash::make($request->password), 
        'profile_image' => $imageName,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);
    
    
    
      // Step 2: Insert states in user_states table
    $stateData = [];

    foreach ($request->states as $stateId) {
        $stateData[] = [
            'user_id' => $adminId,
            'state_id' => $stateId,
            'created_at' => now()
        ];
    }

    DB::table('user_states')->insert($stateData);
    
    

    return redirect('admin/sub_admin')->with('success', 'Sub Admin created successfully');
}



public function driver_store(Request $request)
{
    // Step 1: Validation
    $validator = Validator::make(
        $request->all(),
        [
            'user_name'              => 'required|string',
            'mobile_number'          => 'required|string',
            'email'                  => 'required|email|unique:users,email',
            'password'               => 'required|min:6',

            // Extra fields
            'driver_license_number'  => 'nullable|string',
            'issued_date'            => 'nullable',
            'language'               => 'nullable|string',
            'dot_number'             => 'nullable|string',
            'mc_number'              => 'nullable|string',
            'company_name'           => 'nullable|string',
            'company_authorised'     => 'nullable|string',

            // Image
            'image'                  => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]
    );

    // Step 2: Mobile unique check
    $mobileNumber = $request->input('mobile_number');
    $validator->after(function ($validator) use ($mobileNumber) {
        if (DB::table('users')->where('mobile_number', $mobileNumber)->exists()) {
            $validator->errors()->add('mobile_number', 'The mobile number already exists.');
        }
    });

    // Step 3: Validation fail
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    // Step 4: Image Upload
    $imageName = null;
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/users'), $imageName);
    }

    // Step 5: Insert Data
    DB::table('users')->insert([
        'user_name'             => $request->user_name,
        'email'                 => $request->email,
        'mobile_number'         => $request->mobile_number,
        'password'              => Hash::make($request->password),
        'image'                 => $imageName,

        // Extra fields
        'driver_license_number' => $request->driver_license_number,
        'issued_date'           => $request->issued_date,
        'language'              => $request->language,
        'dot_number'            => $request->dot_number,
        'mc_number'             => $request->mc_number,
        'company_name'          => $request->company_name,
        'company_authorised'    => $request->company_authorised,

        // Timestamps (important)
        'created_at'            => now(),
        'updated_at'            => now(),
    ]);

    
    
    return redirect('admin/drivers')->with('success', 'Driver Added successfully');
}




    /*================================================================================================================*/  
    /*================================================================================================================*/
    /*================================================================================================================*/
    /*================================================================================================================*/
    
    
      
       public function manager() {
         $data['menu'] = 'manager';
         $users = DB::table('users')->where(['type'=>'User'])->get();
      //  $users = DB::table('users')->get();
         return view('admin.pages.manager', compact('data', 'users'));
      }
    
    
    public function payroll_settings() {
         $data['menu'] = 'payroll settings';
         $payroll_settings = DB::table('payroll_settings')->get();
      //  $users = DB::table('users')->get();
         return view('admin.pages.payroll_settings', compact('data', 'payroll_settings'));
      }
    
     public function payroll_settings_edit($id){
         $data['menu'] = 'payroll settings';  
         $settings = DB::table('payroll_settings')->where('id', $id)->first();
         return view('admin.pages.edit_payroll_settings', compact('data', 'settings'));
    }
    
     
     public function payroll_settings_update(Request $request){
       //  dd($request->all());
        $request->validate([
        'default_hourly_rate' => 'required',
        'overtime_rate' => 'required',
        'weekend_rate' => 'required',
    ]);
         $id = $request->id;
         $data = $request->except(['_token', 'id']);
       
        DB::table('payroll_settings')->where('id', $id)->update($data);
          $message = 'payroll settings updated successfully';
       
       
    return redirect()->route('payroll_settings')->with('message', $message);
    }
     
    
   /*  public function business() {
         $data['menu'] = 'business';
         $users = DB::table('users')->where('type','Business')->get();
         return view('admin.pages.business-list', compact('data', 'users'));
      }*/


 public function updateStatus(Request $request)
{
    $user = User::find($request->id);
    $user->status = $request->status;
    $user->save();

    // अगर account Active हुआ है तो HTML email भेजें
    if ($request->status == 'ACTIVE11') {

        // सुरक्षित रूप से नाम और लिंक बनाएं
        $name = htmlspecialchars($user->full_name ?: 'User', ENT_QUOTES, 'UTF-8');
       
        $companyName = 'sissi';
        $logoUrl = 'https://server-php-8-3.technorizen.com/sissi/public/uploads/logo.png'; // अपनी logo path बदलें अगर चाहिए

        // HTML message (inline CSS)
      $htmlMessage = '
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f8; margin:0; padding:30px;">
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.08);">
          
          <tr>
            <td style="padding:24px; text-align:center; border-bottom: 1px solid #eef2f5;">
              <img src="'.$logoUrl.'" alt="'.$companyName.'" style="max-height:60px;">
            </td>
          </tr>

          <tr>
            <td style="padding:28px 36px;">
              <h2 style="margin:0 0 8px 0; font-size:20px; color:#0f1724;">Hello '.$name.',</h2>

              <p style="margin:0 0 16px 0; color:#475569; line-height:1.5;">
                Great news! Your account has been <strong>successfully activated</strong>. You can now log in and start using your dashboard.
              </p>

            

              <p style="margin:0 0 8px 0; color:#475569;">
                If you face any issues or need assistance, feel free to reply to this email or contact our support team at:  
                <a href="mailto:supports@shiftroster.com">supports@shiftroster.com</a>
              </p>

              <hr style="border:none; border-top:1px solid #eef2f5; margin:20px 0;">

              <p style="margin:0; color:#9aa4b2; font-size:13px;">
                Thank you,<br>
                <strong>'.$companyName.' Team</strong>
              </p>
            </td>
          </tr>

          <tr>
            <td style="padding:14px 20px; text-align:center; background:#fbfdff; color:#8b98a8; font-size:12px;">
              © '.date('Y').' '.$companyName.'. All rights reserved.
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
';


     //   $plainMessage = "Hello {$user->full_name},\n\nYour account is now active. You can login here: {$loginUrl}\n\nIf you need help contact supports@technorizen.com\n\nThank you,\n{$companyName}";

        // Headers for HTML email
        $to = $user->email;
        $subject = "Account Activated - {$companyName}";
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";
        $headers[] = "From: {$companyName} <supports@technorizen.com>";
        $headers[] = "Reply-To: supports@technorizen.com";
        $headers[] = "X-Mailer: PHP/".phpversion();

        // send mail (PHP mail)
        // Note: कुछ servers पर mail() disabled हो सकता है; production में SMTP बेहतर है.
        mail($to, $subject, $htmlMessage, implode("\r\n", $headers));

        // वैकल्पिक: plain text भेजना हो तो उपर वाले के साथ भेजें या अलग से।
        // mail($to, $subject, $plainMessage, implode("\r\n", $headers));
    }

    return response()->json(['success' => true]);
}
   
    
    
    
       public function edit_profile(){
           
           $admin = Auth::guard('admin')->user();
           
         $data['menu'] = 'Edit Profile';  
         $edit = DB::table('admins')->where('id', $admin->id)->first();
         return view('admin.pages.update_profile', compact('data', 'edit'));
    }
    
    
      
    public function update_profile(Request $request){
       //  dd($request->all());
        $request->validate([
        'user_name' => 'required',
        'phone_number' => 'required',
      
    ]);
         $id = $request->id;
         $data = $request->except(['_token', 'id']);
          if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            
            $data['profile_image']  = $imageName;
        }
        
        
       
            DB::table('admins')->where('id', $id)->update($data);
            $message = 'Profile updated successfully';
       
       
    return redirect()->route('edit_profile')->with('message', $message);
    }
    
    
    
    /*================================================customers moduls==============================================================================*/   
    
    
  public function customers_add(){
        $data['menu'] = 'customers';
        return view('admin.pages.add-customer', compact('data'));
    }  
    
    
    public function customers_edit($id){
         $data['menu'] = 'customers';  
         $user = User::find($id);
         return view('admin.pages.edit-customer', compact('data', 'user'));
    }
     
    
     public function customers_save(Request $request){
       //  dd($request->all());
        $request->validate([
        'user_name' => 'required',
        'email' => 'required|email|unique:users,email', // Assuming email is required
        'password' =>'required', // Password is required for new users, nullable for existing ones
    ]);
         
         $data = $request->all();
           if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            
            $data['image']  = $imageName;
        }
        $data['password'] = Hash::make($request->password);
     

        
        $customer   =   User::create($data);
       return redirect()->route('customers')->with('message', 'Added successfully');
    }
    
    
    
    
     public function customers_update(Request $request){
       //  dd($request->all());
        $request->validate([
        'user_name' => 'required',
        'email' => 'required', // Assuming email is required
    ]);
         $id = $request->id;
         $data = $request->except('password');
           if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            
            $data['image']  = $imageName;
        }
        
        if($request->password){
           $data['password'] = Hash::make($request->password); 
        }
        
        
        $customer   =   User::updateOrCreate(['id' => $id], $data);
       return redirect()->route('customers')->with('message', 'updated successfully');
    }
    
    
    /*================================================supervisor moduls==============================================================================*/   
     public function supervisor() {
         $data['menu'] = 'supervisor';
         $users = DB::table('users')->where(['type'=>'Institution'])->get();
      //  $users = DB::table('users')->get();
         return view('admin.pages.supervisor', compact('data', 'users'));
      }
    
  public function supervisor_add(){
        $data['menu'] = 'supervisor';
        return view('admin.pages.add_supervisor', compact('data'));
    }  
    
    
    public function supervisor_edit($id){
         $data['menu'] = 'supervisor';  
         $user = User::find($id);
         return view('admin.pages.edit_supervisor', compact('data', 'user'));
    }
     
    
     public function supervisor_save(Request $request){
       //  dd($request->all());
        $request->validate([
        'user_name' => 'required',
        'email' => 'required|email|unique:users,email', // Assuming email is required
        'password' =>'required', // Password is required for new users, nullable for existing ones
    ]);
         
         $data = $request->all();
           if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            
            $data['image']  = $imageName;
        }
        $data['password'] = Hash::make($request->password);
        $data['type'] = 'Institution';

        
        $customer   =   User::create($data);
       return redirect()->route('supervisor')->with('message', 'Added successfully');
    }
    
    
     public function supervisor_update(Request $request){
       //  dd($request->all());
        $request->validate([
        'user_name' => 'required',
        'email' => 'required', // Assuming email is required
    ]);
         $id = $request->id;
         $data = $request->except('password');
           if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            
            $data['image']  = $imageName;
        }
        
        if($request->password){
           $data['password'] = Hash::make($request->password); 
        }
        
        
        $customer   =   User::updateOrCreate(['id' => $id], $data);
       return redirect()->route('supervisor')->with('message', 'updated successfully');
    }
    
    
    
    
    
 /*============================================category===========================================================================*/
  public function category() {
         $data['menu'] = 'category';
         $category = Category::all();
         return view('admin.pages.category-list', compact('data', 'category'));
      
    }
    
    
    public function category_add(){
        $data['menu'] = 'category';
        return view('admin.pages.add_category', compact('data'));
    }
    
     public function category_edit($id){
         $data['menu'] = 'category';  
         $edit = Category::find($id);
         return view('admin.pages.edit_category', compact('data', 'edit'));
    }
    
    
   
 
    
     public function category_save(Request $request){
       //  dd($request->all());
        $request->validate([
        'category_name' => 'required',
    
    ]);
         $postID = $request->post_id;
         
         $data = $request->all();
         
           if ($request->hasFile('category_image')) {
            $image = $request->file('category_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/categories'), $imageName);
            
            $data['category_image']  = $imageName;
        }
        
        
        $category = Category::create($data);
       return redirect()->route('category')->with('message', 'Data Added successfully');
    }
    
 
   

   public function category_update(Request $request){
       //  dd($request->all());
        $request->validate([
        'category_name' => 'required',
    
    ]);
         $category_id = $request->category_id;
         $data = $request->all();
         
           if ($request->hasFile('category_image')) {
            $image = $request->file('category_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/categories'), $imageName);
            
            $data['category_image']  = $imageName;
        }
        
     $category = Category::findOrFail($category_id);
     $category->update($data);
        
       
       return redirect()->route('category')->with('message', 'Added successfully');
    }
    
 
    
     public function category_delete($id){
        try {
            // Find the user by ID
            $Category = Category::findOrFail($id);

            // Delete the user
            $Category->delete();

            // Return a success response
            return response()->json(['success' => 'data deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error deleting user: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'An error occurred while deleting the data.'], 500);
        }
    }
    
 
 
 
 /*============================================category===========================================================================*/
    
    
    
  
      public function service(){
            $data['menu'] = 'services'; 
            $service = DB::table('services')->get();
            return view('admin.pages.service', compact('data', 'service'));
    }
    
     public function add_service(){
        $data['menu'] = 'services';
        return view('admin.pages.add_service', compact('data'));
    }
    
     public function edit_service($id){
         $data['menu'] = 'services';  
         $service = DB::table('services')->where('id', $id)->first();
         return view('admin.pages.edit_service', compact('data', 'service'));
    }
    
    
    public function service_save(Request $request){
       //  dd($request->all());
        $request->validate([
        'company_id' => 'required',
        'name' => 'required',
        'price' => 'required',
    ]);
         $company_id = $request->company_id;
         
         $data = $request->except(['_token']);
         
          if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            
            $data['image']  = $imageName;
        }
        
      
            DB::table('services')->insert($data);
            $message = 'service added successfully';
       
       return redirect()->route('service', ['id' => $company_id])->with('message', $message);
   // return redirect()->route('service')->with('message', $message);
    }
    
    
    
    
       public function service_update(Request $request){
       //  dd($request->all());
        $request->validate([
        'name' => 'required',
        'price' => 'required',
    ]);
         $id = $request->id;
           $company_id = $request->company_id;
           
         $data = $request->except('_token');
         
           if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            
            $data['image']  = $imageName;
        }
        
           DB::table('services')->where('id', $id)->update($data);
        
        return redirect()->route('service', ['id' => $company_id])->with('message', 'updated successfully');
      
    }
    
    
    
    
    
      public function delete_service($id){
        try {
            
            // Find the user by ID
             DB::table('services')->where('id', $id)->delete();
            // Return a success response
            
            return response()->json(['success' => 'service deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error deleting user: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'An error occurred while deleting the user.'], 500);
        }
    }
     
  
    
 /*================================================services moduls==============================================================================*/  
    
    
    
    
    
    /*================================================================================================================================*/
       /*================================================================================================================================*/
           /*================================================================================================================================*/
               /*================================================================================================================================*/
                   /*================================================================================================================================*/
                       /*================================================================================================================================*/
    
    
    
    
      /*================================================manager moduls==============================================================================*/   
    
    
  public function manager_add(){
        $data['menu'] = 'manager';
        return view('admin.pages.add_manager', compact('data'));
    }  
    
    
    public function manager_edit($id){
         $data['menu'] = 'manager';  
         $user = User::find($id);
         return view('admin.pages.edit_manager', compact('data', 'user'));
    }
     
    
     public function manager_save(Request $request){
       //  dd($request->all());
        $request->validate([
        'first_name' => 'required',
        'email' => 'required|email|unique:users,email', // Assuming email is required
        'password' =>'required', // Password is required for new users, nullable for existing ones
    ]);
         
         $data = $request->all();
           if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            
            $data['image']  = $imageName;
        }
        $data['password'] = Hash::make($request->password);
        $data['position'] = 'Manager';

        
        $customer   =   User::create($data);
       return redirect()->route('manager')->with('message', 'Added successfully');
    }
    
    
     public function manager_update(Request $request){
       //  dd($request->all());
        $request->validate([
        'first_name' => 'required',
        'email' => 'required', // Assuming email is required
    ]);
         $id = $request->id;
         $data = $request->except('password');
           if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            
            $data['image']  = $imageName;
        }
        
        if($request->password){
           $data['password'] = Hash::make($request->password); 
        }
        
        
        $customer   =   User::updateOrCreate(['id' => $id], $data);
       return redirect()->route('manager')->with('message', 'updated successfully');
    }
    
    
    
    
    
    
    
    
    
    
   /*================================================End moduls==============================================================================*/    
  /*================================================End moduls==============================================================================*/     
 /*================================================End moduls==============================================================================*/  
    
  
      public function task(){
            $data['menu'] = 'tasks'; 
            $tasks = DB::table('tasks')->get();
            return view('admin.pages.task', compact('data', 'tasks'));
    }
    
     public function add_task(){
        $data['menu'] = 'Task';
        return view('admin.pages.add_task', compact('data'));
    }
    
     public function edit_task($id){
         $data['menu'] = 'Task';  
         $task = DB::table('tasks')->where('id', $id)->first();
         return view('admin.pages.edit_task', compact('data', 'task'));
    }
    
    
    public function task_save(Request $request){
       //  dd($request->all());
        $request->validate([
        'heading' => 'required',
        'content' => 'required',
        'description' => 'required',
    ]);
         $task_id = $request->task_id;
         $data = $request->except(['_token', 'task_id']);
         /*  if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/hotel'), $imageName);
            
            $data['image']  = $imageName;
        }*/
        
        
       if($task_id){
            DB::table('tasks')->where('id', $task_id)->update($data);
            $message = 'Task updated successfully';
       }else{
            DB::table('tasks')->insert($data);
            $message = 'Task added successfully';
       } 
       
    return redirect()->route('task')->with('message', $message);
    }
    
      public function delete_task($id){
        try {
            
            // Find the user by ID
             DB::table('tasks')->where('id', $id)->delete();
            // Return a success response
            
            return response()->json(['success' => 'task deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
           // Log::error('Error deleting task: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'An error occurred while deleting the task.'], 500);
        }
    }
       
       
   
    
 /*================================================task point moduls==============================================================================*/  
    
  
      public function task_point(){
            $data['menu'] = 'task_details'; 
            $tasks = DB::table('task_details')->get();
            return view('admin.pages.task_point', compact('data', 'tasks'));
    }
    
     public function add_task_point(){
        $data['menu'] = 'task_details';
        return view('admin.pages.add_task_point', compact('data'));
    }
    
     public function edit_task_point($id){
         $data['menu'] = 'task_details';  
         $task = DB::table('task_details')->where('id', $id)->first();
         return view('admin.pages.edit_task_point', compact('data', 'task'));
    }
    
    
    public function task_point_save(Request $request){
       //  dd($request->all());
        $request->validate([
        'task_point' => 'required',
     
    ]);
         $task_point_id= $request->task_point_id;
         $data = $request->except(['_token', 'task_point_id']);
         /*  if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/hotel'), $imageName);
            
            $data['image']  = $imageName;
        }*/
        
        
       if($task_point_id){
            DB::table('task_details')->where('id', $task_point_id)->update($data);
            $message = 'Task updated successfully';
       }else{
            DB::table('task_details')->insert($data);
            $message = 'Task added successfully';
       } 
       
    return redirect()->route('task_point')->with('message', $message);
    }
    
      public function delete_task_point($id){
        try {
            
            // Find the user by ID
             DB::table('task_details')->where('id', $id)->delete();
            // Return a success response
            
            return response()->json(['success' => 'task deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
           // Log::error('Error deleting task: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'An error occurred while deleting the task.'], 500);
        }
    }
           
       
       
       
  /*================================================end api==============================================================================*/  
    
  
    
      public function promo_codes(){
    $data['menu'] = 'promo_codes'; 
        $promo_codes = DB::table('promo_codes')
    ->where('status', 'ACTIVE')
    ->get();
            return view('admin.pages.promo_codes', compact('data', 'promo_codes'));
    }
    
    public function bookings(){
            $data['menu'] = 'bookings'; 
            $bookings = DB::table('bookings')->get();
            return view('admin.pages.bookings', compact('data', 'bookings'));
    }
    
    public function supports(){
            $data['menu'] = 'supports'; 
            $supports = DB::table('supports')->get();
            return view('admin.pages.support', compact('data', 'supports'));
    }
    
     public function gallary($id){
            $data['menu'] = 'hotels'; 
            $gallery = DB::table('hotel_gallery')->where('hotel_id', $id)->get();
            return view('admin.pages.gallery', compact('data', 'gallery', 'id'));
    }
    
    
    
    public function promo_code_edit($id){
         $data['menu'] = 'promo_codes';  
         $promo_code = DB::table('promo_codes')->where('id', $id)->first();
         return view('admin.pages.edit-promocode', compact('data', 'promo_code'));
    }
    
   
    
    public function promo_codes_add(){
        $data['menu'] = 'promo_codes';
        return view('admin.pages.add-promocode', compact('data'));
    }
    
    public function gallary_add($id){
        $data['menu'] = 'hotels';
        return view('admin.pages.add-gallery', compact('data', 'id'));
    }
    
   
    
    public function edit_terms_and_conditions(){
        $data['menu'] = 'terms_and_conditions';  
        $terms_and_conditions = DB::table('terms_and_conditions')->where('tac_id', 1)->first();
        $id = 1;
        return view('admin.pages.edit_terms_and_conditions', compact('data', 'terms_and_conditions', 'id'));
    }
    
    public function edit_privacy_policies(){
        $data['menu'] = 'privacy_policies';  
        $privacy_policies = DB::table('privacy_policies')->where('privacy_policy_id', 1)->first();
        $id = 1;
        return view('admin.pages.edit_privacy_policies', compact('data', 'privacy_policies', 'id'));
    }
    
    public function edit_about_us(){
        $data['menu'] = 'about_us';  
        $about_us = DB::table('about_us')->where('about_us_id', 1)->first();
        $id = 1;
        return view('admin.pages.edit_about_us', compact('data', 'about_us', 'id'));
    }
    
    public function terms_and_conditions_save(Request $request, $id){
        $data['tac_text'] = $request->tac_text;
        DB::table('terms_and_conditions')->where('tac_id', 1)->update($data);
        return redirect()->route('terms_and_conditions.edit')->with('message', 'Update successfully');
    }
    
    
    
     public function privacy_policies_save(Request $request, $id){
        $data['privacy_policy_text'] = $request->privacy_policy_text;
        DB::table('privacy_policies')->where('privacy_policy_id', 1)->update($data);
        return redirect()->route('privacy_policies.edit')->with('message', 'Update successfully');
    }
    
    public function about_us_save(Request $request, $id){
        $data['about_us_text'] = $request->about_us_text;
        DB::table('about_us')->where('about_us_id', 1)->update($data);
        return redirect()->route('about_us.edit')->with('message', 'Update successfully');
    }
    
    
    
    
    
    public function promo_code_save(Request $request){
          $request->validate([
        'code_name' => 'required',
        'discount_type' => 'required',
    ]);
    $data = $request->except('_token');
    DB::table('promo_codes')->insert($data);
    return redirect()->route('promocodes')->with('message', 'Added successfully');
    
    }
    
     public function gallary_save(Request $request, $id){
       //  dd($request->all());
        $request->validate([
        'image' => 'required',
    ]);
    
        $data['hotel_id'] = $id;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/hotel'), $imageName);
            $data['image']  = $imageName;
        }
        
       DB::table('hotel_gallery')->insert($data);

       return redirect()->route('hotel')->with('message', 'Added successfully');
    }
    
     public function delete_customer($id){
        try {
            // Find the user by ID
            $user = User::findOrFail($id);

            // Delete the user
            $user->delete();

            // Return a success response
            return response()->json(['success' => 'User deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error deleting user: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'An error occurred while deleting the user.'], 500);
        }
    }
    
  
     public function delete_gallary($id){
        try {
            
            // Find the user by ID
             DB::table('hotel_gallery')->where('id', $id)->delete();
            // Return a success response
            
            return response()->json(['success' => 'User deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error deleting user: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'An error occurred while deleting the user.'], 500);
        }
    }
    
    
     public function delete_support($id){
        try {
            
            // Find the user by ID
             DB::table('supports')->where('id', $id)->delete();
            // Return a success response
            
            return response()->json(['success' => 'User deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error deleting user: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'An error occurred while deleting the user.'], 500);
        }
    }

  
    public function promo_update(Request $request){
         $postID = $request->post_id;
         $data = $request->except(['_token', 'post_id']);
         DB::table('promo_codes')->where('id', $postID)->update($data);
         return redirect()->route('promocodes')->with('message', 'Update successfully');
    }



    
    public function notifications() {
        //if (Auth::guard('admin')->check()) {
            return view('admin.pages.notifications');
        //}
    }

  
  
}
