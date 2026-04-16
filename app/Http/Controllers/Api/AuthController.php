<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use Hash;

class AuthController extends Controller
{
   

public function login(Request $request)
    {
        
      
        
        // Step 1: Validation
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = array_merge(...array_values($validator->errors()->toArray()));
            return $this->sendError([], $errors[0], [], $errors, 400);
        }

        // Step 2: Check user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->sendError([], 'Email not registered', [], [], 404);
        }

        // Step 3: Password verify
        if (!Hash::check($request->password, $user->password)) {
            return $this->sendError([], 'Invalid password', [], [], 401);
        }

        
        $data = $user->toArray();
        
                  
        
        
            // ✅ Image logic (important part)
    if (!empty($user->image)) {
        $data['image'] = url("/") . "/" . "public/uploads/users/" .$user->image;
     
    } else {
        $data['image'] = 'null';
       
    }
        
      $request_duty = DB::table('request_duty')->where('user_id',$user->id)->orderBy('id', 'desc')->first();
    
      
     if($request_duty){
        $data['rpm']= $request_duty->rpm;
        
          $data['rating'] = $request_duty->rating; 
     }else{
         $data['rpm'] = '0';
          $data['rating'] = '0'; 
     }
        
        
        
        
        
       $data['token'] = $user->createToken('MyApp')->accessToken;
        
         

        return $this->sendResponse(
            $data,
            'login successfully',
            [],
            [],
            200
        );
    }





public function signup(Request $request)
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
        ],
        [
            'email.unique' => 'The email address is already registered.',
            'password.min' => 'The password must be at least 6 characters long.',
        ]
    );

    // Step 2: Unique mobile number check
    $mobileNumber = $request->input('mobile_number');
    $validator->after(function ($validator) use ($mobileNumber) {
        if (User::where('mobile_number', $mobileNumber)->exists()) {
            $validator->errors()->add('mobile_number', 'The mobile number already exists.');
        }
    });

    if ($validator->fails()) {
        $errors = array_merge(...array_values($validator->errors()->toArray()));
        return $this->sendError(null, $errors[0], null, $errors, 200);
    }

    // Step 3: Collect input
    $input = $request->all();

    // Step 4: Profile image upload
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/users'), $imageName);
        $input['image'] = $imageName;
    }

    // Step 5: Password encrypt
    $input['password'] = bcrypt($input['password']);

    // Step 6: Create user
    $user = User::create([
        'user_name'             => $input['user_name'],
        'email'                 => $input['email'],
        'mobile_number'         => $input['mobile_number'],
        'password'              => $input['password'],
        'image'                 => $input['image'] ?? null,

        // Extra fields save
        'driver_license_number' => $input['driver_license_number'] ?? null,
        'issued_date'           => $input['issued_date'] ?? null,
        'language'              => $input['language'] ?? null,
        'dot_number'            => $input['dot_number'] ?? null,
        'mc_number'             => $input['mc_number'] ?? null,
        'company_name'          => $input['company_name'] ?? null,
        'company_authorised'    => $input['company_authorised'] ?? null,
    ]);


$data = $user->toArray();   

$success = [
    'user_data' => $data,
    'token'     => $user->createToken('MyApp')->accessToken,
];

    return $this->sendResponse($success, "Signup Successfully", null, null, 200);
}




public function verifyOtp(Request $request)
{
    // Step 1: Validation
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'otp'     => 'required|digits:4',
    ]);

    if ($validator->fails()) {
        $errors = array_merge(...array_values($validator->errors()->toArray()));
        return $this->sendError(null, $errors[0], null, $errors, 200);
    }

    // Step 2: User fetch
    $user = User::find($request->user_id);

    // Step 3: OTP match check
    if ($user->otp != $request->otp) {
        return $this->sendError(null, 'Invalid OTP', null, [], 200);
    }

    // Step 4: OTP expiry check (agar field hai)
    if ($user->otp_expire_at && now()->gt($user->otp_expire_at)) {
        return $this->sendError(null, 'OTP expired', null, [], 200);
    }

    // Step 5: OTP verified update
    $user->otp_verified = 1;
    $user->otp = null;
    $user->otp_expire_at = null;
    $user->save();

    // Step 6: Response (ALL user fields + token)
    $data = $user->toArray();

    $success = [
        'user_data' => $data,
        'token'     => $user->createToken('MyApp')->accessToken,
    ];

    return $this->sendResponse(
        $success,
        'OTP verified successfully',
        null,
        null,
        200
    );
}



  public function getProfile(Request $request){
       // Authenticating user based on the access token
        if (!Auth::guard('api')->check()) {
            return $this->sendError($result = null, $message = 'Unauthorized.', $notification = null, $error = null, $respose_code = 200);
        }        
        
        $user = Auth::guard('api')->user();
        
       if($user){
       if (!empty($user->image)) {
       $user->image = url("/") . "/" . "public/uploads/users/" .$user->image;
     
     } else {
        $user->image = 'null';
       
    }
    
    $request_duty = DB::table('request_duty')->where('user_id',$user->id)->orderBy('id', 'desc')->first();
     if($request_duty){
        $user->rating = $request_duty->rating; 
         $user->rpm = $request_duty->rpm;
     }else{
           $user->rating = ''; 
        $user->rpm = '0'; 
     }
     
    
    
            
            
            //  $user->image = url("/") . "/" . "public/uploads/users/" .$user->image;
              $success['token'] = '';
              $success['user_data'] = $user; 
           
            return $this->sendResponse($result = $success, $message = 'Profile retrive successfully.', $notification = null, $error = null, $respose_code = 200);
        }else{
            return $this->sendError($result = null, $message = 'User not found.', $notification = null, $error = null, $respose_code = 200);
        }
        
    }
    
  
  
  public function forgotPassword(Request $request)
{
    // Step 1: Validation
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
    ]);

    if ($validator->fails()) {
        $errors = array_merge(...array_values($validator->errors()->toArray()));
        return $this->sendError(null, $errors[0], null, $errors, 200);
    }

    // Step 2: Get user
    $user = User::where('email', $request->email)->first();

    // Step 3: Generate OTP
    $otp = '9999';//rand(1000, 9999);

    // Step 4: Save OTP
    $user->otp = $otp;
    $user->otp_verified = 0;
    $user->otp_expire_at = now()->addMinutes(10);
    $user->save();

    // ================= EMAIL SEND =================

    $subject = "Your OTP verification code";
    $to = $user->email;

    $separator = md5(time());

    $message = "
        <h2>OTP Verification</h2>
        <p>Your one-time password (OTP) is:</p>
        <h1 style='color:#2e6da4;'>$otp</h1>
        <p>Please do not share it with anyone.</p>
    ";

    $headers  = "From: admin@technorizen.com\r\n";
    $headers .= "Reply-To: admin@technorizen.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"\r\n";

    $body  = "--" . $separator . "\r\n";
    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
    $body .= $message . "\r\n\r\n";
    $body .= "--" . $separator . "--";

    @mail($to, $subject, $body, $headers);

    // =================================================

    return $this->sendResponse(
        [
            'user_id' => $user->id,
            'otp'     => $otp // ⚠️ testing ke liye, production me hata dena
        ],
        'OTP sent to registered email',
        null,
        null,
        200
    );
}

  
    
    
  
    public function createNewPassword(Request $request)
    {
        $user_id = $request->input('user_id'); 
        // Authenticating user based on the access token
        // if (!Auth::guard('api')->check()) {
        //     return $this->sendError($result = null, $message = 'Obehörig.', $notification = null, $error = null, $respose_code = 200);
        // }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => ['required'],
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            $validator_error = array_values($validator->errors()->toArray());
            $validator_error = array_merge(...$validator_error);
            return $this->sendError($result = null, $message = @$validator_error[0], $notification = null, $error = $validator_error, $respose_code = 200);
        }

        // $user = Auth::guard('api')->user();
        

        // Check if the old password matches the user's current password
        // if (!Hash::check($request->old_password, $user->password)) {
        //     return $this->sendError($result = null, $message = 'Invalid old password.', $notification = null, $error = null, $respose_code = 200);
        // }

        // Update user's password
        // $user->password = bcrypt($request->password);
        // $user->save();
        
        $hashedPassword = bcrypt($request->password);

    // Update user's password in the database using DB facade
        DB::table('users')
        ->where('id', $user_id) // Assuming you are passing user_id
        ->update(['password' => $hashedPassword]);

        return $this->sendResponse($result = null, $message = 'The password has been updated.', $notification = null, $error = null, $respose_code = 200);
    }
    
    
    
   public function changePassword(Request $request)
    {
        // Authenticating user based on the access token
        if (!Auth::guard('api')->check()) {
            return $this->sendError($result = null, $message = 'Unauthorized.', $notification = null, $error = null, $respose_code = 200);
        }

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => ['required'],
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            $validator_error = array_values($validator->errors()->toArray());
            $validator_error = array_merge(...$validator_error);
            return $this->sendError($result = null, $message = @$validator_error[0], $notification = null, $error = $validator_error, $respose_code = 200);
        }

        $user = Auth::guard('api')->user();
        

        // Check if the old password matches the user's current password
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->sendError($result = null, $message = 'Invalid old password.', $notification = null, $error = null, $respose_code = 200);
        }

        // Update user's password
        $user->password = bcrypt($request->password);
        $user->save();

        return $this->sendResponse($result = null, $message = 'Password updated successfully.', $notification = null, $error = null, $respose_code = 200);
    }


   
   
    
    public function updateProfile(Request $request)
{
    // Authenticate the user
    if (!Auth::guard('api')->check()) {
        return $this->sendError(null, 'Unauthorized.', null, null, 200);
    }

    $user = Auth::guard('api')->user();
    $data = $request->all();
    // Handle image upload
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('uploads/users'), $imageName);
        $data['image'] = $imageName;
    }
    
    DB::table('users')->where('id', $user->id)->update($data);
    
    
    
      $userss = DB::table('users')->where('id', $user->id)->first();
    
    
    
     if (!empty($userss->image)) {
       $userss->image = url("/") . "/" . "public/uploads/users/" .$user->image;
     
    } else {
        $userss->image = 'null';
       
    }


    return $this->sendResponse($userss, 'Profile updated.', null, null, 200);
}





/*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*///////////////////////////////////////////////////////end api//////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
/*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/






    
    
    
    
   

    public function passwordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
        ]);

        if ($validator->fails()) {
            $validator_error = array_values($validator->errors()->toArray());
            $validator_error = array_merge(...$validator_error);
            return $this->sendError($result = null, $message = @$validator_error[0], $notification = null, $error = $validator_error, $respose_code = 200);
        }

        $identity = $request->input('identity'); 

        // Attempt authentication with email
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $identity];

            $user = User::where('email', $identity)->first();
            
            
            
        } else {
            
          //  echo 'sd';die;
            // Attempt authentication with mobile number and country code
            $parts = explode('-', $identity);
            if (count($parts) == 2) {
                $countryCode = $parts[0];
                $mobileNumber = $parts[1];
                // Check if there's an associated email
                $user = User::where('country_code', $countryCode)
                    ->where('mobile_number', $mobileNumber)
                    ->first();
                if ($user) {
                    // Use email for authentication
                    $credentials = ['email' => $user->email];
                } else {
                    return $this->sendError($result = null, $message = 'The email address was not found for the specified mobile number.', $notification = null, $error = null, $respose_code = 200);
                }
            } else {
                return $this->sendError($result = null, $message = 'Invalid identity format.', $notification = null, $error = null, $respose_code = 200);
            }
        }


        if(!$user){
                    return $this->sendError($result = null, $message = 'User not found.', $notification = null, $error = null, $respose_code = 200);

        }

        $rand = "9999";
        rand(1111, 9999);

        $user->otp = $rand;

        $currentDateTime = date('Y-m-d H:i:s');
        $futureDateTime = date('Y-m-d H:i:s', strtotime($currentDateTime . ' +15 minutes'));

        $user->otp_time = $futureDateTime;
        

        $user->save();
        
        $subject = "Your OTP verification code";

// Email recipient
$to = $user->email; // Replace with actual user email

// Unique boundary separator
$separator = md5(time());

// Message Body
$message = "
    <h2>OTP Verification</h2>
    <p>Your one-time password (OTP) is:</p>
    <h1 style='color: #2e6da4;'>$rand</h1>
    <p>This OTP is valid for 10 minutes. Do not share it with anyone.</p>
";



// Headers
$headers = "From: admin@technorizen.com\r\n";
$headers .= "Reply-To: admin@technorizen.no\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"\r\n";

// Body
$body = "--" . $separator . "\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$body .= $message . "\r\n\r\n";
mail($to, $subject, $body, $headers);

        return $this->sendResponse($result = null, $message = 'otp send successfully.', $notification = null, $error = null, $respose_code = 200);
    }
    
    
    
     
    





   

/*---------------------------------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------------------*/


    
    public function delete_acc(){
     if (!Auth::guard('api')->check()) {
            return $this->sendError($result = null, $message = 'Obehörig.', $notification = null, $error = null, $respose_code = 200);
        }        
        
   $user = Auth::guard('api')->user();
    if (!$user) {
        return $this->sendError([], 'Användaren hittades inte.', [], [], 404);
    }

    DB::table('users')->where('id', $user->id)->update(['delete_at' => Carbon::now()]);

    return $this->sendResponse([], 'Användaren har raderats.', [], [], 200);
    }
    
  
    
    
}
