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
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    //($result = null, $message = "" , $notification = null, $error = null , $respose_code = 200) 












public function signup(Request $request)
{
    $validator = Validator::make(
        $request->all(),
        [
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ],
        [
            'email.unique' => 'The email address is already registered.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'password.min' => 'The password must be at least 8 characters long.',
        ]
    );

    
    $mobileNumber = $request->input('mobile_number');

    // Custom validation for unique mobile number with country code
    $validator->after(function ($validator) use ($mobileNumber) {
        if (User::where('mobile_number', $mobileNumber)->exists()) {
            $validator->errors()->add('mobile_number', 'The mobile number already exists.');
        }
    });

    if ($validator->fails()) {
        $validator_error = array_values($validator->errors()->toArray());
        $validator_error = array_merge(...$validator_error);
        return $this->sendError(null, @$validator_error[0], null, $validator_error, 200);
    }

    $input = $request->all();

    // Handle profile image upload
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/users'), $imageName);
        $input['image'] = $imageName;
    }
    
    
    if($input['type']=='Business'){
       $input['employee_code'] = rand(000000,999999); 
    }
    

    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);

    $success['user_data'] = $user;
    $success['token'] = $user->createToken('MyApp')->accessToken;
    
    return $this->sendResponse($success, "Signup Successfully", null, null, 200);
}







public function organization_signup(Request $request)
{
    $validator = Validator::make(
        $request->all(),
        [
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ],
        [
            'email.unique' => 'The email address is already registered.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'password.min' => 'The password must be at least 8 characters long.',
        ]
    );

    
    $mobileNumber = $request->input('mobile_number');

    // Custom validation for unique mobile number with country code
    $validator->after(function ($validator) use ($mobileNumber) {
        if (User::where('mobile_number', $mobileNumber)->exists()) {
            $validator->errors()->add('mobile_number', 'The mobile number already exists.');
        }
    });

    if ($validator->fails()) {
        $validator_error = array_values($validator->errors()->toArray());
        $validator_error = array_merge(...$validator_error);
        return $this->sendError(null, @$validator_error[0], null, $validator_error, 200);
    }

    $input = $request->all();

    // Handle profile image upload
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/users'), $imageName);
        $input['image'] = $imageName;
    }
    
    
   // if($input['type']=='Business'){
       $input['employee_code'] = rand(000000,999999); 
    //}
     $input['type'] = 'Business'; 
    

    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);

    $success['user_data'] = $user;
    $success['token'] = $user->createToken('MyApp')->accessToken;
    
    return $this->sendResponse($success, "Signup Successfully", null, null, 200);
}










public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required',
        'password' => 'required',
        'type' => 'required', // Add validation for 'type'
    ]);

    if ($validator->fails()) {
        $validator_error = array_values($validator->errors()->toArray());
        $validator_error = array_merge(...$validator_error);
        return $this->sendError($result = null, $message = @$validator_error[0], $notification = null, $error = $validator_error, $respose_code = 200);
    }

    $email = $request->input('email');
    $password = $request->input('password');
    $type = $request->input('type');

    // Attempt authentication with email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $user = User::where('email', $email)->where('type', $type)->first();
        if (!$user) {
            return $this->sendError($result = null, $message = 'No user found for the provided email and type.', $notification = null, $error = null, $respose_code = 200);
        }
        $credentials = ['email' => $email, 'password' => $password];
    } else {
        // Attempt authentication with mobile number and country code
        $parts = explode('-', $email);
        if (count($parts) == 2) {
            $countryCode = $parts[0];
            $mobileNumber = $parts[1];

            $user = User::where('country_code', $countryCode)
                ->where('mobile_number', $mobileNumber)
                ->where('type', $type)
                ->first();

            if ($user) {
                if ($user->delete_at != "") {
                    return $this->sendError($result = null, $message = 'Your account was not found.', $notification = null, $error = null, $respose_code = 200);
                }
                $credentials = ['email' => $user->email, 'password' => $password];
            } else {
                return $this->sendError($result = null, $message = 'No user found for the given mobile number and type.', $notification = null, $error = null, $respose_code = 200);
            }
        } else {
            return $this->sendError($result = null, $message = 'Invalid identity format.', $notification = null, $error = null, $respose_code = 200);
        }
    }

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        if ($user->delete_at != '') {
            return $this->sendError($result = null, $message = 'Your account was not found.', $notification = null, $error = null, $respose_code = 200);
        }

        // Type mismatch after login safety check
        if ($user->type != $type) {
            return $this->sendError($result = null, $message = 'User type mismatch.', $notification = null, $error = null, $respose_code = 200);
        }

        $user->device_token = $request->device_token;
        $user->save();

        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['user_data'] = $user;

        return $this->sendResponse($result = $success, $message = "User login successfully.", $notification = null, $error = null, $respose_code = 200);
    } else {
        return $this->sendError($result = null, $message = 'Invalid user credentials.', $notification = null, $error = null, $respose_code = 200);
    }
}




      public function getProfile(Request $request)
    {
        
               
        
        // Authenticating user based on the access token
        if (!Auth::guard('api')->check()) {
            return $this->sendError($result = null, $message = 'Unauthorized.', $notification = null, $error = null, $respose_code = 200);
        }        
        
        $user = Auth::guard('api')->user();
        
        //print_r($user);
        
        if($user){
            
              $user->image = url("/") . "/" . "public/uploads/users/" .$user->image;
              $success['token'] = '';
              $success['user_data'] = $user; 
           
            return $this->sendResponse($result = $success, $message = 'Profile retrive successfully.', $notification = null, $error = null, $respose_code = 200);
        }else{
            return $this->sendError($result = null, $message = 'User not found.', $notification = null, $error = null, $respose_code = 200);
        }
        
    }

     
     public function getProfiledetail(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return $this->sendError([], 'User ID is required.', [], [], 200);
    }

    $user = DB::table('users')->where('id', $user_id)->first();

    if ($user) {
        $user->image = !empty($user->image)
            ? url('public/uploads/users/' . $user->image)
            : null;

        $success['token'] = '';
        $success['user_data'] = $user;

        return $this->sendResponse($success, 'Profile retrieved successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'User not found.', [], [], 200);
    }
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


    return $this->sendResponse(null, 'Profile updated.', null, null, 200);
}


    
   

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

// // Email Headers
// $headers = "From: Drommen <drommen@gmail.com>\r\n";
// $headers .= "Reply-To: admin@technorizen.no\r\n";
// $headers .= "MIME-Version: 1.0\r\n";
// $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"\r\n";

// // Final Email Body
// $body = "--" . $separator . "\r\n";
// $body .= "Content-Type: text/html; charset=UTF-8\r\n";
// $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
// $body .= $message . "\r\n\r\n";
// if (mail($to, $subject, $body, $headers)) {
//     echo "OTP sent successfully to $to";
// } else {
//     echo "Failed to send OTP.";
// }

// $to = 'arjunsharmaji0422@gmail.com';

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
    
    
    
     
    


    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            $validator_error = array_values($validator->errors()->toArray());
            $validator_error = array_merge(...$validator_error);
            return $this->sendError($result = null, $message = @$validator_error[0], $notification = null, $error = $validator_error, $respose_code = 200);
        }

        $identity = $request->input('identity');
        $otp = $request->input('otp');

        // Attempt authentication with email
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $identity];

            $user = User::where('email', $identity)->first();
            if(!$user){
                return $this->sendError($result = null, $message = 'The email address was not found for the specified mobile number.', $notification = null, $error = null, $respose_code = 200);
            }
        } else {
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
                    return $this->sendError($result = null, $message = 'The email address was not found for the specified mobile number..', $notification = null, $error = null, $respose_code = 200);
                }
            } else {
                return $this->sendError($result = null, $message = 'Invalid identity format.', $notification = null, $error = null, $respose_code = 200);
            }
        }

        if ($otp != $user->otp) {
            $result1 = [
        'user_id' => $user->id,
        // Add other result data if necessary
    ];
    
            return $this->sendError($result = $result1, $message = 'OTP did not match.', $notification = null, $error = null, $respose_code = 200);
        }

        $dbTimestamp = strtotime($user->otp_time);
        $currentTime = time();
        $actualTime = strtotime('-15 minutes', $dbTimestamp);
        $futureTime = $dbTimestamp;

        if ($currentTime >= $actualTime && $currentTime <= $futureTime) {
        } else {
           
         $result1 = [
        'user_id' => $user->id,
        // Add other result data if necessary
    ];
            return $this->sendError($result = $result1, $message = 'The OTP has expired.', $notification = null, $error = null, $respose_code = 200);
        }

        $user->otp = null;

        $user->otp_time = null;
        
        $user->otp_verify = "TRUE";

        $user->save();
        
        
         $result1 = [
        'user_id' => $user->id,
        // Add other result data if necessary
    ];
        return $this->sendResponse($result = $result1, $message = 'OTP has been verified.', $notification = null, $error = null, $respose_code = 200);
    }
    

/*---------------------------------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------------------*/
/*---------------------------------------------------------------------------------------------------------------------------------------------*/








  
    public function createNewPassword(Request $request)
    {
        $user_id = $request->input('user_id'); 
        // Authenticating user based on the access token
        // if (!Auth::guard('api')->check()) {
        //     return $this->sendError($result = null, $message = 'Obehörig.', $notification = null, $error = null, $respose_code = 200);
        // }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => ['required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
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
            'password' => ['required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
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


    
    
    
    
    
    
    
    
    
    
    
    public function createNewPasswordWithoutLogin(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'otp' => 'required',
            'password' => ['required', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            $validator_error = array_values($validator->errors()->toArray());
            $validator_error = array_merge(...$validator_error);
            return $this->sendError($result = null, $message = @$validator_error[0], $notification = null, $error = $validator_error, $respose_code = 200);
        }




        $identity = $request->input('identity');
        $otp = $request->input('otp');
        $password = $request->input('password');

        // Attempt authentication with email
        if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $identity];

            $user = User::where('email', $identity)->first();
        } else {
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
                    return $this->sendError($result = null, $message = 'E-postadressen hittades inte för det angivna mobilnumret.', $notification = null, $error = null, $respose_code = 200);
                }
            } else {
                return $this->sendError($result = null, $message = 'Ogiltigt identitetsformat.', $notification = null, $error = null, $respose_code = 200);
            }
        }
 
        
         if(!$user) {
                    return $this->sendError($result = null, $message = 'Användaren hittades inte.', $notification = null, $error = null, $respose_code = 200);
                }
        

        if ($otp != $user->otp_reset_password) {
            return $this->sendError($result = null, $message = 'OTP matchade inte.', $notification = null, $error = null, $respose_code = 200);
        }

        $dbTimestamp = strtotime($user->otp_reset_password_expiration);
        $currentTime = time();
        $actualTime = strtotime('-15 minutes', $dbTimestamp);
        $futureTime = $dbTimestamp;

        if ($currentTime >= $actualTime && $currentTime <= $futureTime) {
        } else {
            return $this->sendError($result = null, $message = 'OTP har gått ut.', $notification = null, $error = null, $respose_code = 200);
        }

        $user->password = bcrypt($request->password);

        $user->otp_reset_password = null;

        $user->otp_reset_password_expiration = null;

        $user->save();


        return $this->sendResponse($result = null, $message = 'Lösenordet har uppdaterats framgångsrikt med otp.', $notification = null, $error = null, $respose_code = 200);
    }
    
    public function delete_acc(){
        
          if (!Auth::guard('api')->check()) {
            return $this->sendError($result = null, $message = 'Obehörig.', $notification = null, $error = null, $respose_code = 200);
        }        
        
        $user = Auth::guard('api')->user();
        

    if (!$user) {
        return $this->sendError([], 'Användaren hittades inte.', [], [], 404);
    }

    DB::table('users')->where('id', $user->id)->update([
    'delete_at' => Carbon::now(),
]);

    return $this->sendResponse([], 'Användaren har raderats.', [], [], 200);
    }
    
  
    
    
}
