<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use DB;
use Illuminate\Support\Facades\Mail;
  
class UserController extends Controller {
 
 
    
public function get_my_shift(Request $request) 
{
    $user_id = $request->input('user_id');

    if (!$user_id) {
        return $this->sendError([], 'User ID is required.', [], [], 400);
    }

    // Fetch shifts for the user where status is not 'cancel'
    $shifts = DB::table('shifts')
        ->where('user_id', $user_id)
        ->where(function ($query) {
            $query->whereNull('status')
                  ->orWhere('status', '!=', 'cancel');
        })
        ->get();

    if (!$shifts->isEmpty()) {
        foreach ($shifts as $shift) {
            $user = DB::table('users')->where('id', $shift->admin_id)->first();

            if ($user) {
                $user->image = !empty($user->image) 
                    ? url('public/uploads/users/' . $user->image) 
                    : null;

                $shift->user_details = $user;
            }
        }

        return $this->sendResponse($shifts, 'Shift retrieved successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'Shift not found.', [], [], 200);
    }
}




public function get_my_shift_old(Request $request) 
{
    $user_id = $request->input('user_id');

    $shifts = DB::table('shifts')->where('user_id', $user_id)->get();

    if (!$shifts->isEmpty()) {
        foreach ($shifts as $shift) {
            $user = DB::table('users')->where('id', $shift->user_id)->first();

            if ($user) {
                // Add full profile image path
                $user->image = !empty($user->image) 
                    ? url('public/uploads/users/' . $user->image) 
                    : null;

                $shift->user_details = $user;
            }
        }

        return $this->sendResponse($result = $shifts, $message = 'Shift retrieved successfully.', $notification = [], $error = [], $respose_code = 200);
    } else {
        return $this->sendError($result = [], $message = 'Shift not found.', $notification = [], $error = [], $respose_code = 200);
    }
}

    
    
public function get_shift_details(Request $request) 
{
    $shift_id = $request->input('shift_id');

    $shifts = DB::table('shifts')->where('id', $shift_id)->first();

    if ($shifts) {
      // $shift = $shifts->first(); // Get single shift object

        $user = DB::table('users')->where('id', $shifts->user_id)->first();

        if ($user) {
            // Add full profile image path
            $user->image = !empty($user->image) 
                ? url('public/uploads/users/' . $user->image) 
                : null;

            $shifts->user_details = $user;
        }

        return $this->sendResponse($result = $shifts, $message = 'Shift retrieved successfully.', $notification = [], $error = [], $respose_code = 200);
    } else {
        return $this->sendError($result = [], $message = 'Shift not found.', $notification = [], $error = [], $respose_code = 200);
    }
}


    
   
    
public function get_task_shiftid(Request $request) {
     $shift_id = $request->input('shift_id');
     $tasks = DB::table('tasks')->where('shift_id', $shift_id)->get();
      if ($tasks) {
       return $this->sendResponse($result = $tasks, $message = 'data retrieved successfully.', $notification = [], $error = [], $respose_code = 200);
    } else {
        return $this->sendError($result = [], $message = 'data not found.', $notification = [], $error = [], $respose_code = 200);
    }
}

 public function get_task_details_taskid(Request $request) {
    $task_id = $request->input('task_id');

    // Fetch the main task (to get description)
    $task = DB::table('tasks')->where('id', $task_id)->first();
    if (!$task) {
        return $this->sendError([], 'Task not found.', [], [], 200);
    }

    // Fetch task details for this task
    $task_details = DB::table('task_details')->where('task_id', $task_id)->get();
    if ($task_details->isEmpty()) {
        return $this->sendError([], 'Task details not found.', [], [], 200);
    }

    // Prepare task details array (without description)
    $data = [];
    foreach ($task_details as $val) {
        $data[] = [
            'id' => $val->id,
            'task_id' => $val->task_id,
            'task_point' => $val->task_point,
            'status' => $val->status,
            'date_time' => $val->date_time,
        ];
    }

    // Return response with single description outside the data array
    return response()->json([
        'success' => true,
        'data' => $data,
        'description' => $task->description, // <- Only once, outside of data
        'message' => 'Data retrieved successfully.',
        'notification' => null,
        'error' => null,
        'status' => '1'
    ], 200);
}



public function delete_shift_by_id11(Request $request)
{
   $shift_id = $request->input('shift_id');

    // ✅ Check if shift_id provided
    if (empty($shift_id)) {
        return $this->sendError([], 'Shift ID is required.', [], [], 200);
    }

    // ✅ Check if shift exists
    $shift = DB::table('shifts')->where('id', $shift_id)->first();
    if (!$shift) {
        return $this->sendError([], 'Shift not found.', [], [], 200);
    }

    // ✅ Delete shift
    DB::table('shifts')->where('id', $shift_id)->delete();

    // ✅ Response
    return $this->sendResponse([], 'Shift deleted successfully.', [], [], 200);
}

  
  
  
  
  public function get_open_shift_by_date_old(Request $request)
{
    $user_id = $request->input('user_id');
    $shift_date = date('Y-m-d', strtotime($request->input('shift_date')));

    if (empty($user_id) || empty($shift_date)) {
        return $this->sendError([], 'User ID and shift date are required.', [], [], 200);
    }

  
 


    // Step 2: Get other usersâ€™ shifts after your shift ends
    $later_shifts = DB::table('shifts')
        ->where('user_id',0)
       ->get();

    // Step 3: Attach user details to each shift
    foreach ($later_shifts as $shift) {
            $admins = DB::table('admins')->where('id',1)->first();
        if ($admins) {
            $admins->profile_image = !empty($admins->profile_image)? url('public/uploads/users/' . $admins->profile_image): null;
            $shift->admin_details = $admins;
        }
        
        
        
       /* $user = DB::table('users')->where('id', $shift->user_id)->first();

        if ($user) {
            $user->image = !empty($user->image)
                ? url('public/uploads/users/' . $user->image)
                : null;

            $shift->user_details = $user;
        }*/
    }

    if (!$later_shifts->isEmpty()) {
        return $this->sendResponse($later_shifts, 'Later shifts retrieved successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'No later shifts found.', [], [], 200);
    }
}

  
  
  
  
  
  public function get_open_shift_by_date(Request $request)
{
    $user_id = $request->input('user_id');
    $shift_date = date('Y-m-d', strtotime($request->input('shift_date')));

    if (empty($user_id) || empty($shift_date)) {
        return $this->sendError([], 'User ID and shift date are required.', [], [], 200);
    }

    // Step 1: Get shifts with user_id = 0 for the given date
    $later_shifts = DB::table('shifts')
        ->where('user_id', 0)
        ->whereDate('shift_date', $shift_date)
        ->get();

    // Step 2: Filter out shifts where cancel_ids contain current user_id
    $filtered_shifts = $later_shifts->filter(function ($shift) use ($user_id) {
        $cancel_ids = array_filter(explode(',', $shift->cancel_ids ?? ''));
        return !in_array($user_id, $cancel_ids); // if user_id is not in cancel_ids, keep the shift
    })->values(); // reset keys

    // Step 3: Attach admin details to each shift
    foreach ($filtered_shifts as $shift) {
        $admins = DB::table('users')->where('id', $shift->admin_id)->first();
        if ($admins) {
            $admins->profile_image = !empty($admins->image)
                ? url('public/uploads/users/' . $admins->image)
                : null;
                
                
             $admins->image = !empty($admins->image)
                ? url('public/uploads/users/' . $admins->image)
                : null;
                     
                
            $shift->admin_details = $admins;
        }
    }

    if (!$filtered_shifts->isEmpty()) {
        return $this->sendResponse($filtered_shifts, 'Later shifts retrieved successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'No later shifts found.', [], [], 200);
    }
}

  
  
  
  
  
  
  
  
  
  
  
  public function get_shift_by_date(Request $request)
{
    $user_id = $request->input('user_id');
   // $shift_date = $request->input('shift_date'); // e.g., '2025-06-16'
    
    $shift_date = date('Y-m-d', strtotime($request->input('shift_date')));
    

    if (empty($user_id) || empty($shift_date)) {
        return $this->sendError([], 'User ID and shift date are required.', [], [], 200);
    }




    // Step 1: Get My Shift for selected date
    $my_shifts = DB::table('shifts')
        ->where('user_id', $user_id)
        ->whereDate('shift_date', $shift_date)
        ->get();

    // For determining "On Shift Now" and "On Shift Later"
    $my_start = null;
    $my_end = null;

    if (!$my_shifts->isEmpty()) {
        $first_shift = $my_shifts->first(); // Assume one shift per user per day
       // $my_start = $first_shift->start_time;
        $my_start = date('H:i:s', strtotime($first_shift->start_time));
         //$my_end = $first_shift->end_time;
        $my_end = date('H:i:s', strtotime($first_shift->end_time)); 
        
    }

    // Step 2: On Shift Now - other users with overlapping shift time
    $on_shift_now = [];
    if ($my_start && $my_end) {
        $on_shift_now = DB::table('shifts')
            ->where('user_id', '!=', $user_id)
            ->where('user_id', '!=', 0)
            ->whereDate('shift_date', $shift_date)
            ->where(function ($query) use ($my_start, $my_end) {
                $query->whereBetween('start_time', [$my_start, $my_end])
                      ->orWhereBetween('end_time', [$my_start, $my_end])
                      ->orWhere(function ($q) use ($my_start, $my_end) {
                          $q->where('start_time', '<=', $my_start)
                            ->where('end_time', '>=', $my_end);
                      });
            })
            ->get();
    }

    // Step 3: On Shift Later - other users starting after my shift ends
    $on_shift_later = [];
    if ($my_end) {
        $on_shift_later = DB::table('shifts')
            ->where('user_id', '!=', $user_id)
            ->whereDate('shift_date', $shift_date)
            ->where('start_time', '>', $my_end)
            ->get();
    }

    // Step 4: Open Shifts - shifts with no user assigned
    $open_shifts = DB::table('shifts')
       // ->whereNull('user_id')
       ->where('user_id', 0)
        // ->where('assign_id', 0)
        ->whereDate('shift_date', $shift_date)
        ->get();

    // Add user details to all groups
    $addUserDetails = function (&$shifts) {
        foreach ($shifts as $shift) {
            if (!empty($shift->user_id)) {
                $user = DB::table('users')->where('id', $shift->admin_id)->first();
                if ($user) {
                    $user->image = !empty($user->image) 
                        ? url('public/uploads/users/' . $user->image) 
                        : null;
                    $shift->user_details = $user;
                }
            }
        }
    };

    $addUserDetails($my_shifts);
    $addUserDetails($on_shift_now);
    $addUserDetails($on_shift_later);

    return $this->sendResponse([
        'my_shifts'     => $my_shifts,
        'on_shift_now'  => $on_shift_now,
        'on_shift_later'=> $on_shift_later,
        'open_shifts'   => $open_shifts
    ], 'Shifts retrieved successfully.', [], [], 200);
}

   
    
public function get_absence_reasons(Request $request) {
   
     $absence_reasons = DB::table('absence_reasons')->get();
      if ($absence_reasons) {
       return $this->sendResponse($result = $absence_reasons, $message = 'data retrieved successfully.', $notification = [], $error = [], $respose_code = 200);
    } else {
        return $this->sendError($result = [], $message = 'data not found.', $notification = [], $error = [], $respose_code = 200);
    }
}
  
  
  
 public function insert_absence_shift(Request $request) {
    $shift_id = $request->input('shift_id');
    $user_id = $request->input('user_id');
    $status = $request->input('status');  // e.g., 'Absent' ya 'Present' ya jo bhi ho
    $reasons = $request->input('reasons');  
    

    if (!$shift_id || !$user_id || !$status) {
        return $this->sendError([], 'Required fields are missing.', [], [], 400);
    }

    // Insert into absence_shift table
    $inserted = DB::table('absence_shift')->insert([
        'shift_id' => $shift_id,
        'user_id' => $user_id,
        'status' => $status,
        'reasons' => $status,
        
        
    ]);

    if ($inserted) {
        return $this->sendResponse([], 'Data inserted successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'Failed to insert data.', [], [], 500);
    }
}
 
  
  
  
 
  public function add_announcement(Request $request)
{
    // Validate request (only check required, not file type)
    $validator = Validator::make($request->all(), [
        'title'     => 'required|string',
        'image' => 'required|file|max:5120' // max 5MB (aap change kar sakte ho)
    ]);

    if ($validator->fails()) {
        return $this->sendError([], $validator->errors()->first(), [], [], 200);
    }

    $file_name = null;

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension(); 
        $file_name = uniqid() . '_' . time() . '.' . $extension;

        // Upload folder: public/uploads/users
        $file->move(public_path('uploads/users'), $file_name);
    }

    // Save in DB (only file name, not object)
    $inserted = DB::table('announcement')->insertGetId([
        'title'     => $request->input('title'),
        'user_id'     => $request->input('user_id'),
         'content'     => $request->input('content'),
         'image' => $file_name,   // just file name
        'created_at' => now(),
        'updated_at' => now()
    ]);

    if ($inserted) {
        // Full URL response
        $file_url = url('public/uploads/users/' . $file_name);

        return $this->sendResponse([
            'id'       => $inserted,
            'user_id'     => $request->input('user_id'),
              'title'     => $request->input('title'),
                'content'     => $request->input('content'),
            'image' => $file_url
        ], 'File uploaded successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'Failed to upload file.', [], [], 500);
    }
}
 
  
  
  
  
  
  
  public function get_announcement(Request $request) 
{
    $user_id = $request->input('user_id');
    
    if (empty($user_id)) {
        return $this->sendError(
            $result = [],
            $message = 'User ID  are required.',
            $notification = [],
            $error = [],
            $respose_code = 200
        );
    }

    // Step 1: Get announcements EXCLUDING current user
    $announcements = DB::table('announcement')
       // ->where('user_id', $user_id)
        ->orderBy('created_at', 'desc')
        ->get();

    $data = [];

    foreach ($announcements as $val) {
        // Step 2: Get user who posted this announcement
        $user = DB::table('users')->where('id', $val->user_id)->first();

        // Step 3: Match employee_code
       
            // Image formatting
            $val->image = !empty($val->image) 
                ? url('public/uploads/users/' . $val->image) 
                : null;
                
               $user->image = !empty($user->image) 
                ? url('public/uploads/users/' . $user->image) 
                : null;   
                

          //  $user->profile_image = $val->profile_image; // optional: attach image to user

            $val->admin_details = $user;

            $data[] = $val;
        
    }

    if (!empty($data)) {
        return $this->sendResponse(
            $result = $data,
            $message = 'Announcements retrieved successfully.',
            $notification = [],
            $error = [],
            $respose_code = 200
        );
    } else {
        return $this->sendError(
            $result = [],
            $message = 'No matching announcements found.',
            $notification = [],
            $error = [],
            $respose_code = 200
        );
    }
}

 
 
 
 
 public function get_current_shift(Request $request)
{
    $user_id = $request->input('user_id');
    $current_datetime = now();
    $current_date = $current_datetime->format('Y-m-d');
  //   $current_time = $current_datetime->format('H:i:s'); 

    if (empty($user_id)) {
        return $this->sendError([], 'User ID is required.', [], [], 200);
    }

    // Step 1: Get the currently running shift for the user
    $running_shift = DB::table('shifts')
        ->where('user_id', $user_id)
        ->whereDate('shift_date', $current_date)
        ->where('status','Checkin')
      //  ->whereTime('start_time', '<=', $current_time)
       // ->whereTime('end_time', '>=', $current_time)
        ->first();

    if ($running_shift) {
        // Add user details
        $user = DB::table('users')->where('id', $running_shift->admin_id)->first();
        if ($user) {
            $user->image = !empty($user->image)
                ? url('public/uploads/users/' . $user->image)
                : null;

            $running_shift->user_details = $user;
        }

        return $this->sendResponse($running_shift, 'Running shift found.', [], [], 200);
    } else {
        return $this->sendError([], 'No current shift found.', [], [], 200);
    }
}




public function get_running_shift(Request $request)
{
    $user_id = $request->input('user_id');
    $current_datetime = now(); // current time in Asia/Kolkata
    $current_date = $current_datetime->format('Y-m-d');
    $current_time = $current_datetime->format('H:i:s');

    if (empty($user_id)) {
        return $this->sendError([], 'User ID is required.', [], [], 200);
    }

    // Step 1: Get currently running shift
    $running_shift = DB::table('shifts')
        ->where('user_id', $user_id)
        ->where('status', 'Pending')
        ->whereDate('shift_date', $current_date)
        ->whereTime('start_time', '<=', $current_time)
        ->whereTime('end_time', '>=', $current_time)
        ->first();

    // Step 2: Get next shift (same day, after current time)
    $next_shift = DB::table('shifts')
        ->where('user_id', $user_id)
        ->where('status', 'Pending')
        ->whereDate('shift_date', $current_date)
        ->whereTime('start_time', '>', $current_time)
        ->orderBy('start_time', 'asc')
        ->first();

    // Step 3: Attach admin details to running shift
    if ($running_shift && !empty($running_shift->admin_id)) {
        $admin = DB::table('users')->where('id', $running_shift->admin_id)->first();
        if ($admin) {
            $admin->image = !empty($admin->image)
                ? url('public/uploads/users/' . $admin->image)
                : null;
            $running_shift->admin_details = $admin;
        }
    }

    // Step 4: Attach admin details to next shift
    if ($next_shift && !empty($next_shift->admin_id)) {
        $admin = DB::table('users')->where('id', $next_shift->admin_id)->first();
        if ($admin) {
            $admin->image = !empty($admin->image)
                ? url('public/uploads/users/' . $admin->image)
                : null;
                
            $next_shift->admin_details = $admin;
        }
    }

    // Step 5: Return result
    if ($running_shift || $next_shift) {
        return $this->sendResponse([
            'running_shift' => $running_shift,
            'next_shift' => $next_shift,
        ], 'Running and next shift retrieved successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'No running or next shift found.', [], [], 200);
    }
}




 
public function shift_checkin_checkout(Request $request) {
    $shift_id = $request->input('shift_id');
    $user_id = $request->input('user_id');
    $status = $request->input('status'); 
    $checkin_address = $request->input('checkin_address');
    $checkin_lat = $request->input('checkin_lat'); 
    $checkin_lon = $request->input('checkin_lon'); 

    if (!$shift_id || !$user_id || !$status) {
        return $this->sendError([], 'Required fields are missing.', [], [], 400);
    }

    $now = now(); // current datetime

    // Check if shift exists
    $existing = DB::table('shifts')
        ->where('id', $shift_id)
        ->where('user_id', $user_id)
        ->first();

    if (!$existing) {
        return $this->sendError([], 'Data not found.', [], [], 404);
    }

    // Prepare update data
    $updateData = [
        'status' => $status,
       // 'updated_at' => $now,
    ];

    if ($status === 'Checkin') {
        $updateData['check_in_date'] = $now;
        $updateData['checkin_address'] = $checkin_address;
        $updateData['checkin_lat'] = $checkin_lat;
        $updateData['checkin_lon'] = $checkin_lon;
        
    } elseif ($status === 'Checkout') {
        $updateData['check_out_date'] = $now;
    } else {
        return $this->sendError([], 'Invalid status value. Only Checkin or Checkout allowed.', [], [], 400);
    }

    // Perform update
    $updated = DB::table('shifts')
        ->where('id', $shift_id)
        ->where('user_id', $user_id)
        ->update($updateData);

    if ($updated) {
        // Fetch updated record
        $updated_shift = DB::table('shifts')
            ->where('id', $shift_id)
            ->where('user_id', $user_id)
            ->first();

        return $this->sendResponse($updated_shift, $status . ' successful.', [], [], 200);
    } else {
        return $this->sendError([], 'Failed to update shift.', [], [], 500);
    }
}

 
  
  
  
 public function shift_accept_cancel(Request $request) {
    $shift_id = $request->input('shift_id');
    $user_id = $request->input('user_id');
    $status = $request->input('status');  
   

    if (!$shift_id || !$user_id || !$status) {
        return $this->sendError([], 'Required fields are missing.', [], [], 400);
    }

    $now = now(); // current datetime

    // Get existing shift
    $existing = DB::table('shifts')
        ->where('id', $shift_id)
        ->first();

    if (!$existing) {
        return $this->sendError([], 'Data not found.', [], [], 404);
    }

    $updateData = [];


    // Handle cancel â†’ add user_id to cancel_ids
    if ($status === 'Cancel') {
        $cancel_ids = $existing->cancel_ids ?? '';
        $cancel_array = array_filter(explode(',', $cancel_ids));

        if (!in_array($user_id, $cancel_array)) {
            $cancel_array[] = $user_id;
        }

        $updateData['cancel_ids'] = implode(',', $cancel_array);
    }

    // Handle accept â†’ only one user allowed
    if ($status === 'Accept') {
        $updateData['user_id'] = (string)$user_id; 
        $updateData['status'] = $status;  
    }

    // Update shift
    $updated = DB::table('shifts')
        ->where('id', $shift_id)
        ->update($updateData);

    if ($updated) {
        $updated_shift = DB::table('shifts')->where('id', $shift_id)->first();
        return $this->sendResponse($updated_shift, ucfirst($status) . ' successful.', [], [], 200);
    } else {
        return $this->sendError([], 'Failed to update shift.', [], [], 500);
    }
}
 
  
public function get_directory(Request $request)
{
    $user_id = $request->input('user_id');

    // 1. Get the employee_code of the given user_id
    $employee_code = DB::table('users')
        ->where('id', $user_id)
        ->value('other_employee_code'); // This gets a single value

    if (!$employee_code) {
        return $this->sendError([], 'User not found or employee code missing.', [], [], 200);
    }

    // 2. Get all users with the same employee_code
    $matching_users = DB::table('users')
        ->where('other_employee_code', $employee_code)
        ->get();

    if ($matching_users->isNotEmpty()) {
        // 3. Format image URLs
        foreach ($matching_users as $user) {
            $user->image = !empty($user->image)
                ? url('public/uploads/users/' . $user->image)
                : null;
        }

        return $this->sendResponse($matching_users, 'Directory list found.', [], [], 200);
    } else {
        return $this->sendError([], 'No directory data found.', [], [], 200);
    }
}






  
  
public function insert_send_message(Request $request)
{
    $user_id         = $request->input('user_id');
    $email_sender_id = $request->input('email_sender_id');
    $subject         = $request->input('subject');
    $message         = $request->input('message');

    // Validation
    if (!$user_id || !$email_sender_id || !$subject || !$message) {
        return $this->sendError([], 'Required fields are missing.', [], [], 400);
    }

    // Data insert
    $data = [
        'user_id'         => $user_id,
        'email_sender_id' => $email_sender_id,
        'subject'         => $subject,
        'message'         => $message,
        'created_at'      => now(),
        'updated_at'      => now(),
    ];

    $inserted = DB::table('send_message')->insertGetId($data);

    if ($inserted) {
        $responseData = DB::table('send_message')->where('id', $inserted)->first();

        // ðŸ“¨ Email send karna
        $receiver = DB::table('users')->where('id', $email_sender_id)->value('email');
        if ($receiver) {
            $htmlContent = '
                <div style="font-family: Arial, sans-serif; padding:20px; background:#f9f9f9;">
                    <div style="max-width:600px; margin:auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                        <div style="background:#4CAF50; color:#fff; padding:15px; text-align:center;">
                            <h2 style="margin:0;">' . e($subject) . '</h2>
                        </div>
                        <div style="padding:20px; color:#333;">
                            <p style="font-size:15px; line-height:1.6;">' . nl2br(e($message)) . '</p>
                        </div>
                        <div style="background:#f1f1f1; padding:10px; text-align:center; font-size:12px; color:#777;">
                            Â© ' . date('Y') . ' Your Company. All rights reserved.
                        </div>
                    </div>
                </div>
            ';

            Mail::send([], [], function ($mail) use ($receiver, $subject, $htmlContent) {
                $mail->to($receiver)
                    ->subject($subject)
                    ->html($htmlContent);
            });
        }

        return $this->sendResponse($responseData, 'Message inserted successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'Failed to insert data.', [], [], 500);
    }
}
  
  
  
  public function add_user_document(Request $request)
{
    // Validate request (only check required, not file type)
    $validator = Validator::make($request->all(), [
        'name'     => 'required|string',
        'document' => 'required|file|max:5120' // max 5MB (aap change kar sakte ho)
    ]);

    if ($validator->fails()) {
        return $this->sendError([], $validator->errors()->first(), [], [], 200);
    }

    $file_name = null;

    if ($request->hasFile('document')) {
        $file = $request->file('document');
        $extension = $file->getClientOriginalExtension(); 
        $file_name = uniqid() . '_' . time() . '.' . $extension;

        // Upload folder: public/uploads/users
        $file->move(public_path('uploads/users'), $file_name);
    }

    // Save in DB (only file name, not object)
    $inserted = DB::table('user_document')->insertGetId([
        'name'     => $request->input('name'),
        'user_id'     => $request->input('user_id'),
        'document' => $file_name,   // just file name
        'created_at' => now(),
        'updated_at' => now()
    ]);

    if ($inserted) {
        // Full URL response
        $file_url = url('public/uploads/users/' . $file_name);

        return $this->sendResponse([
            'id'       => $inserted,
            'name'     => $request->input('name'),
            'document' => $file_url
        ], 'File uploaded successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'Failed to upload file.', [], [], 500);
    }
}

  
  public function get_user_document(Request $request)
{
      $user_id =  $request->input('user_id');
    
    $user_document = DB::table('user_document')->where('user_id',$user_id)->get();

    if ($user_document->isNotEmpty()) {
        foreach ($user_document as $val) {
            $val->document = !empty($val->document)
                ? url('public/uploads/users/' . $val->document)
                : null;
        }

        return $this->sendResponse($user_document, 'data list found.', [], [], 200);
    } else {
        return $this->sendError([], 'No data  found.', [], [], 200);
    }
}


   public function delete_employee_by_id(Request $request)
{
    $user_id = $request->input('user_id');

    // ✅ Check required field
    if (empty($user_id)) {
        return $this->sendError([], 'User ID is required.', [], [], 400);
    }

    // ✅ Check if employee exists
    $employee = DB::table('users')->where('id', $user_id)->first();

    if (!$employee) {
        return $this->sendError([], 'Employee not found.', [], [], 200);
    }

    // ✅ Optionally delete related data (uncomment if needed)
    // DB::table('employee_documents')->where('user_id', $user_id)->delete();
    // DB::table('insurance')->where('user_id', $user_id)->delete();
    // DB::table('shifts')->where('user_id', $user_id)->delete();

    // ✅ Delete employee
    DB::table('users')->where('id', $user_id)->delete();

    return $this->sendResponse([], 'Employee deleted successfully.', [], [], 200);
}



public function add_insurance(Request $request)
{
    $user_id = $request->input('user_id');
    $member_name = $request->input('member_name');
    $group_id = $request->input('group_id');
    $group_name = $request->input('group_name');
    $effective_date = $request->input('effective_date');
    $plan = $request->input('plan');
    $rx_bin_pcn = $request->input('rx_bin_pcn');
    $dependents = $request->input('dependents');
    $type = $request->input('type');

    // ✅ Validation
    if (empty($user_id) || empty($type)) {
        return $this->sendError([], 'User ID and Type are required.', [], [], 400);
    }


    // ✅ Check if record already exists
    $existing = DB::table('insurance')
        ->where('user_id', $user_id)
        ->where('type', $type)
        ->first();

    if ($existing) {
        // ✅ Update record
        DB::table('insurance')
            ->where('id', $existing->id)
            ->update([
                'member_name' => $member_name,
                'group_id' => $group_id,
                'group_name' => $group_name,
                'effective_date' => $effective_date,
                'plan' => $plan,
                'rx_bin_pcn' => $rx_bin_pcn,
                'dependents' => $dependents,
                'updated_at' => now()
            ]);

        return $this->sendResponse([], 'Insurance updated successfully.', [], [], 200);
    } else {
        // ✅ Insert new record
        DB::table('insurance')->insert([
            'user_id' => $user_id,
            'member_name' => $member_name,
            'group_id' => $group_id,
            'group_name' => $group_name,
            'effective_date' => $effective_date,
            'plan' => $plan,
            'rx_bin_pcn' => $rx_bin_pcn,
            'dependents' => $dependents,
            'type' => $type,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $this->sendResponse([], 'Insurance added successfully.', [], [], 200);
    }
}


  
  public function get_insurance(Request $request)
{
       $user_id =  $request->input('user_id');
       $type =  $request->input('type');
    
    $user_document = DB::table('insurance')->where('user_id',$user_id)->where('type',$type)->get();

    if ($user_document->isNotEmpty()) {
        foreach ($user_document as $val) {
          //  $val->document = !empty($val->document)
            //    ? url('public/uploads/users/' . $val->document)
            //    : null;
        }

        return $this->sendResponse($user_document, 'data list found.', [], [], 200);
    } else {
        // return $this->sendError([], 'No data  found.', [], [], 200);
        return $this->sendResponse([], 'data list found.', [], [], 200);
    }
}





public function get_absence_shift(Request $request)
{
    $user_id = $request->input('user_id');
    $today = date('Y-m-d'); // aaj ki date

    if (empty($user_id)) {
        return $this->sendError([], 'User ID is required.', [], [], 200);
    }

    // Step 1: Get shifts with user_id = 0, status = pending, aur jo date nikal chuki hai
    $later_shifts = DB::table('shifts')
        ->where('user_id', $user_id)
        ->where('status', 'pending')
        ->whereDate('shift_date', '<', $today) // sirf past date wale
        ->get();

    // Step 2: (Removed cancel_ids filter)
    $filtered_shifts = $later_shifts;  

    // Step 3: Attach admin details
    foreach ($filtered_shifts as $shift) {
        $admins = DB::table('users')->where('id', $shift->admin_id)->first();
        if ($admins) {
            $admins->profile_image = !empty($admins->image)
                ? url('public/uploads/users/' . $admins->image)
                : null;

            $admins->image = !empty($admins->image)
                ? url('public/uploads/users/' . $admins->image)
                : null;

            $shift->admin_details = $admins;
        }
    }

    if (!$filtered_shifts->isEmpty()) {
        return $this->sendResponse($filtered_shifts, 'Pending past shifts retrieved successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'No pending past shifts found.', [], [], 200);
    }
}


  
  

public function get_shift_history(Request $request)
{
    $user_id = $request->input('user_id');
    $today = date('Y-m-d'); // aaj ki date

    if (empty($user_id)) {
        return $this->sendError([], 'User ID is required.', [], [], 200);
    }

    // Step 1: Get shifts with user_id = 0, status = pending, aur jo date nikal chuki hai
    $later_shifts = DB::table('shifts')
        ->where('user_id', $user_id)
        ->where('status', 'Checkout')
        //->whereDate('shift_date', '<', $today) // sirf past date wale
        ->get();

    // Step 2: (Removed cancel_ids filter)
    $filtered_shifts = $later_shifts;  

    // Step 3: Attach admin details
    foreach ($filtered_shifts as $shift) {
        $admins = DB::table('users')->where('id', $shift->admin_id)->first();
        if ($admins) {
            $admins->profile_image = !empty($admins->image)
                ? url('public/uploads/users/' . $admins->image)
                : null;

            $admins->image = !empty($admins->image)
                ? url('public/uploads/users/' . $admins->image)
                : null;

            $shift->admin_details = $admins;
        }
    }

    if (!$filtered_shifts->isEmpty()) {
        return $this->sendResponse($filtered_shifts, 'Pending past shifts retrieved successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'No pending past shifts found.', [], [], 200);
    }
}


  /*==========================================================================================================================*/  
   /*=================================================admin api =========================================================================*/  
     /*==========================================================================================================================*/ 


public function add_shift(Request $request)
{
    // Validate request
    $validator = Validator::make($request->all(), [
        'name'       => 'required|string|max:255',
        'shift_date' => 'required|date',
        'start_time' => 'required|date_format:H:i',
        'end_time'   => 'required|date_format:H:i|after:start_time',
        'admin_id'   => 'required|integer',
        'position'   => 'required|string|max:255',
        'address'    => 'required|string|max:500',
    ]);

    if ($validator->fails()) {
        return $this->sendError([], $validator->errors()->first(), [], [], 200);
    }

    // Insert shift record
    $inserted = DB::table('shifts')->insertGetId([
        'name'       => $request->input('name'),
        'shift_date' => $request->input('shift_date'),
        'start_time' => $request->input('start_time'),
        'end_time'   => $request->input('end_time'),
        'admin_id'   => $request->input('admin_id'),
        'position'   => $request->input('position'),
        'address'    => $request->input('address'),
        'amount'    => $request->input('amount'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    if ($inserted) {
        // admin_id ke hisaab se pura data le aao
        
        $adminShifts = DB::table('shifts')
            ->where('id', $inserted)
            ->first();
 return $this->sendResponse($adminShifts, 'Shift added successfully.', [], [], 200);
        
    } else {
        return $this->sendError([], 'Failed to add shift.', [], [], 500);
    }
}

   
   
  
  
   
    
  /*==========================================================================================================================*/  
   /*==========================================================================================================================*/  
     /*==========================================================================================================================*/  
       /*==========================================================================================================================*/  
         /*==========================================================================================================================*/  
           /*==========================================================================================================================*/  
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
           
    
    
     public function get_payment(Request $request) {

  return $this->sendResponse($result = 'success', $message = 'successfully.', $notification = [], $error = [], $respose_code = 200);

    }
    
    public function get_terms_and_condition(Request $request) {

        $categories = DB::table('terms_and_conditions')->where('tac_admin_status', 'ACTIVE')->get();
        if ($categories) {
            return $this->sendResponse($result = $categories, $message = 'T&C Retrieve successfully.', $notification = [], $error = [], $respose_code = 200);
        } else {
            return $this->sendError($result = [], $message = 'T&C not found.', $notification = [], $error = [], $respose_code = 200);
        }
    }
    
    public function get_about_us(Request $request) {

        $categories = DB::table('about_us')->whereNull('about_us_deleted_at')->where('about_us_admin_status', 'ACTIVE')->get();
        if ($categories) {
            return $this->sendResponse($result = $categories, $message = 'About Us Retrieve successfully.', $notification = [], $error = [], $respose_code = 200);
        } else {
            return $this->sendError($result = [], $message = 'About Us not found.', $notification = [], $error = [], $respose_code = 200);
        }
    }
    
    public function get_faqs(Request $request) {

        $categories = DB::table('faqs')->whereNull('faq_deleted_at')->where('faq_admin_status', 'ACTIVE')->get();
        if ($categories) {
            return $this->sendResponse($result = $categories, $message = 'Faq Retrieve successfully.', $notification = [], $error = [], $respose_code = 200);
        } else {
            return $this->sendError($result = [], $message = 'Faq not found.', $notification = [], $error = [], $respose_code = 200);
        }
    }
    
    public function get_privacy_policy(Request $request) {

        // $categories = DB::table('privacy_policies')->whereNull('pp_deleted_at')->where('pp_admin_status', 'ACTIVE')->get();
                $categories = DB::table('privacy_policies')->where('privacy_policy_admin_status', 'ACTIVE')->get();

        if ($categories) {
            return $this->sendResponse($result = $categories, $message = 'Privacy Policy successfully.', $notification = [], $error = [], $respose_code = 200);
        } else {
            return $this->sendError($result = [], $message = 'Privacy Policy found.', $notification = [], $error = [], $respose_code = 200);
        }
    }
    
    
    public function get_support(Request $request) {

        $categories = DB::table('supports')->whereNull('support_deleted_at')->where('support_admin_status', 'ACTIVE')->get();
        if ($categories) {
            return $this->sendResponse($result = $categories, $message = 'Support Detail successfully.', $notification = [], $error = [], $respose_code = 200);
        } else {
            return $this->sendError($result = [], $message = 'Support detail not found.', $notification = [], $error = [], $respose_code = 200);
        }
    }
    


public function ask_support(Request $request)
{
        $user_id = $request->input('user_id');
        $message = $request->input('message');
        
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'message' => 'required',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return $this->sendError([], $validator->errors()->first(), [], [], 200);
    }

    
    // Insert the support ask into the database
    $support_ask = DB::table('supports')->insertGetId([
        'user_id' => $user_id,
        'message' => $message
        
    ]);

    // Check if insertion was successful
    if ($support_ask) {
        return $this->sendResponse(null, 'Support request submitted successfully.', [], [], 200);
    } else {
        return $this->sendError(null, 'Failed to submit support request.', [], [], 200);
    }
}
    

public function ask_support1(Request $request)
{
    // Validate the request data
    $validator = Validator::make($request->all(), [
        'message' => 'required',
        'support_ask_title' => 'required|string',
        'support_ask_message' => 'required|string',
        'support_ask_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:12048', // Optional image upload validation..
        'support_ask_full_name' => 'required|string',
        'support_ask_mobile_number' => 'required|numeric',
        'support_ask_country_code' => 'required|string',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return $this->sendError([], $validator->errors()->first(), [], [], 200);
    }

    // Check if the support_id exists
    $support_id = $request->input('support_ask_support_id');
    $support = DB::table('supports')->where('support_id', $support_id)->first();
    if (!$support) {
        return $this->sendError([], 'Support category not found.', [], [], 200);
    }

    // Handle the image upload if provided
    $image_name = null;
    
    if ($request->hasFile('support_ask_image')) {
        $image = $request->file('support_ask_image');
        $image_extension = $image->getClientOriginalExtension(); // Get the original extension
        $image_name = uniqid() . '_' . time() . '.' . $image_extension; // Generate a unique name with timestamp and original extension
        $support_image = $image->move('public/uploads/support', $image_name); // Store the image with the generated name
    }

    // Insert the support ask into the database
    $support_ask = DB::table('supports_asks')->insertGetId([
        'support_ask_support_id' => $support_id,
        'support_ask_title' => $request->input('support_ask_title'),
        'support_ask_message' => $request->input('support_ask_message'),
        'support_ask_image' => $image_name,
        'support_ask_created_at' => now(),
        'support_ask_updated_at' => now(),
        'support_ask_full_name' => $request->input('support_ask_full_name'),
        'support_ask_mobile_number' => $request->input('support_ask_mobile_number'),
        'support_ask_country_code' => $request->input('support_ask_country_code'),
        
    ]);

    // Check if insertion was successful
    if ($support_ask) {
        return $this->sendResponse([], 'Support request submitted successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'Failed to submit support request.', [], [], 200);
    }
}

        
    
    

}
