<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use Hash;
use DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
class AdminPageController extends Controller {

   public function dashboard() {
    $data['menu'] = 'dashboard';
    $userCount = User::count();
  //  $activeHotelCount = DB::table('users')->count();
    $totalshifts = DB::table('shifts')->count();
    $totaltasks = DB::table('tasks')->count();
    $totalannouncement = DB::table('announcement')->count();
    return view('admin.pages.dashboard', compact('data', 'userCount', 'totalshifts', 'totaltasks', 'totalannouncement'));
    }
    
    public function customers() {
         $data['menu'] = 'customers';
         $users = DB::table('users')->where('type','User')->get();
         return view('admin.pages.customer-list', compact('data', 'users'));
      }
    
     public function business() {
         $data['menu'] = 'business';
         $users = DB::table('users')->where('type','Business')->get();
         return view('admin.pages.business-list', compact('data', 'users'));
      }
    
    
    
    
    
 /*================================================shift moduls==============================================================================*/  
    
  
      public function shift(){
            $data['menu'] = 'shift'; 
            $shift = DB::table('shifts')->get();
            return view('admin.pages.shift', compact('data', 'shift'));
    }
    
     public function add_shift(){
        $data['menu'] = 'shift';
        return view('admin.pages.add-shift', compact('data'));
    }
    
     public function edit_shift($id){
         $data['menu'] = 'shifts';  
         $shift = DB::table('shifts')->where('id', $id)->first();
         return view('admin.pages.edit-shift', compact('data', 'shift'));
    }
    
    
    public function shift_save(Request $request){
       //  dd($request->all());
        $request->validate([
        'position' => 'required',
        'shift_date' => 'required',
        'user_id' => 'required',
    ]);
         $shift_id = $request->shift_id;
         $data = $request->except(['_token', 'shift_id']);
         /*  if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/hotel'), $imageName);
            
            $data['image']  = $imageName;
        }*/
        
        
       if($shift_id){
            DB::table('shifts')->where('id', $shift_id)->update($data);
            $message = 'Shift updated successfully';
       }else{
            DB::table('shifts')->insert($data);
            $message = 'Shift added successfully';
       } 
       
    return redirect()->route('shift')->with('message', $message);
    }
    
      public function delete_shift($id){
        try {
            
            // Find the user by ID
             DB::table('shifts')->where('id', $id)->delete();
            // Return a success response
            
            return response()->json(['success' => 'shift deleted successfully.'], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error deleting user: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'An error occurred while deleting the user.'], 500);
        }
    }
     
  
    
 /*================================================shift moduls==============================================================================*/  
    
  
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
    
    public function customers_add(){
        $data['menu'] = 'customers';
        return view('admin.pages.add-customer', compact('data'));
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
    
    public function customers_edit($id){
         $data['menu'] = 'customers';  
         $user = User::find($id);
         return view('admin.pages.edit-customer', compact('data', 'user'));
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
    
    
    
     public function customers_save(Request $request){
       //  dd($request->all());
        $request->validate([
        'first_name' => 'required',
        'email' => 'required|email|unique:users,email', // Assuming email is required
        'password' =>'required', // Password is required for new users, nullable for existing ones
    ]);
         $postID = $request->post_id;
         $data = $request->all();
           if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/users'), $imageName);
            
            $data['image']  = $imageName;
        }
        $data['password'] = Hash::make($request->password);
        
        $customer   =   User::updateOrCreate(['id' => $postID], $data);
       return redirect()->route('customers')->with('message', 'Added successfully');
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

   public function customers_update(Request $request){
       //  dd($request->all());
        $request->validate([
        'first_name' => 'required',
        'email' => 'required|email', // Assuming email is required
    ]);
         $postID = $request->post_id;
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
        
        
        $customer   =   User::updateOrCreate(['id' => $postID], $data);
       return redirect()->route('customers')->with('message', 'Added successfully');
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
