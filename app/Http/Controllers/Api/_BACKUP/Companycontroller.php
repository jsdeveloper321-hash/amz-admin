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
  
class Companycontroller extends Controller {



public function get_company_by_users(Request $request)
{
    $user_id = $request->input('user_id');

    // 1. Get the employee_code of the given user_id
    $employee_code = DB::table('users')
        ->where('id', $user_id)
        ->value('employee_code'); // This gets a single value

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











public function add_shift(Request $request)
{
    // Validate request
    $validator = Validator::make($request->all(), [
      //  'name'       => 'required|string|max:255',
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
       // 'name'       => $request->input('name'),
        'shift_date' => $request->input('shift_date'),
        'start_time' => $request->input('start_time'),
        'end_time'   => $request->input('end_time'),
        'admin_id'   => $request->input('admin_id'),
        'position'   => $request->input('position'),
        'address'    => $request->input('address'),
        'amount'    => $request->input('amount'),
         'user_id'    => $request->input('user_id'),
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


public function add_task(Request $request)
{
    // Validate request
    $validator = Validator::make($request->all(), [
        'heading'     => 'required',
        'content'     => 'required',
        'task_points' => 'required', // array ya string dono allow
    ]);

    if ($validator->fails()) {
        return $this->sendError([], $validator->errors()->first(), [], [], 200);
    }

    DB::beginTransaction();
    try {
        // ðŸŸ¢ Insert task record (parent table)
        $taskId = DB::table('tasks')->insertGetId([
            'heading'     => $request->input('heading'),
            'content'     => $request->input('content'),
            'shift_id'    => $request->input('shift_id'),
            'description' => $request->input('description'),
        ]);

        // ðŸŸ¢ Handle task_points (array ya comma-separated string)
        $taskPoints = $request->input('task_points');

        if (!is_array($taskPoints)) {
            // string ko array me convert kar do
            $taskPoints = explode(',', $taskPoints);
        }

        $detailsData = [];
        $now = now();

        foreach ($taskPoints as $point) {
            $detailsData[] = [
                'task_id'    => $taskId,
                'task_point' => trim($point), // spaces clean
                'status'     => 'Pending',
                'date_time'  => $now
            ];
        }

        // ðŸŸ¢ Insert all task details in one go
        if (!empty($detailsData)) {
            DB::table('task_details')->insert($detailsData);
        }

        DB::commit();

        // ðŸŸ¢ Response with task + details
        $task = DB::table('tasks')->where('id', $taskId)->first();
        $task->details = DB::table('task_details')->where('task_id', $taskId)->get();

        return $this->sendResponse($task, 'Task added successfully with details.', [], [], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return $this->sendError([], 'Failed to add task: '.$e->getMessage(), [], [], 500);
    }
}



public function add_task_old(Request $request)
{
    // Validate request
    $validator = Validator::make($request->all(), [
        'heading'       => 'required',
        'content' => 'required',
     
    ]);

    if ($validator->fails()) {
        return $this->sendError([], $validator->errors()->first(), [], [], 200);
    }

    // Insert shift record
    $inserted = DB::table('tasks')->insertGetId([
         'heading' => $request->input('heading'),
         'content' => $request->input('content'),
         'shift_id'   => $request->input('shift_id'),
         'description'   => $request->input('description'),
     
    ]);

    if ($inserted) {
        // admin_id ke hisaab se pura data le aao
        
        $adminShifts = DB::table('tasks')
            ->where('id', $inserted)
            ->first();
 return $this->sendResponse($adminShifts, 'tasks added successfully.', [], [], 200);
        
    } else {
        return $this->sendError([], 'Failed to add tasks.', [], [], 500);
    }
}



public function get_task_details_by_shift(Request $request) {
    $shift_id = $request->input('shift_id');

    // Step 1: Fetch shift
    $shift = DB::table('shifts')->where('id', $shift_id)->first();
    if (!$shift) {
        return $this->sendError([], 'Shift not found.', [], [], 200);
    }

    // Step 2: Fetch all tasks for this shift
    $tasks = DB::table('tasks')->where('shift_id', $shift_id)->get();

    $tasksData = [];
    foreach ($tasks as $task) {
        // Step 3: Fetch task_details for each task
        $task_details = DB::table('task_details')->where('task_id', $task->id)->get();

        $taskDetailsArray = $task_details->map(function($detail) {
            return [
                'id' => $detail->id,
                'task_point' => $detail->task_point,
                'status' => $detail->status,
                'date_time' => $detail->date_time,
            ];
        });

        $tasksData[] = [
            'task_id' => $task->id,
            'description' => $task->description,
            'task_details' => $taskDetailsArray,
        ];
    }

    // Step 4: Combine shift info with tasks
    $shiftData = [
        'shift_id' => $shift->id,
        'shift_name' => $shift->name ?? null, // any other shift fields you want
        'shift_start' => $shift->start_time ?? null,
        'shift_end' => $shift->end_time ?? null,
        'tasks' => $tasksData,
    ];

    return response()->json([
        'success' => true,
        'data' => $shiftData,
        'message' => 'Shift, tasks, and task details retrieved successfully.',
        'status' => '1'
    ], 200);
}

 
   
public function get_my_shift_by_admin(Request $request) 
{
    $user_id = $request->input('user_id');
    $shift_date = $request->input('shift_date');

    if (!$user_id) {
        return $this->sendError([], 'User ID is required.', [], [], 400);
    }

    // Fetch shifts for the user where status is not 'cancel'
  //  $shifts = DB::table('shifts')->where('admin_id', $user_id)->get();
    
    
     $query = DB::table('shifts')
        ->where('admin_id', $user_id);

    // 👇 Agar shift_date diya gaya hai to filter lagao
    if (!empty($shift_date)) {
        $query->whereDate('shift_date', $shift_date);
    }

    // Execute query
    $shifts = $query->get(); 
    
    
    

    if (!$shifts->isEmpty()) {
        foreach ($shifts as $shift) {
            $user = DB::table('users')->where('id', $shift->user_id)->first();

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



  public function delete_shift(Request $request)
{
    $shift_id = $request->input('shift_id'); // 👈 delete करने वाला shift_id

    if (empty($shift_id)) {
        return $this->sendError([], 'Shift ID is required.', [], [], 400);
    }

    // Check karo shift exist karta hai ya nahi
    $shift = DB::table('shifts')->where('id', $shift_id)->first();

    if (!$shift) {
        return $this->sendError([], 'Shift not found.', [], [], 404);
    }

    // Shift delete karo
    $deleted = DB::table('shifts')->where('id', $shift_id)->delete();

    if ($deleted) {
        return $this->sendResponse([], 'Shift deleted successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'Failed to delete shift. Please try again.', [], [], 500);
    }
}




}
