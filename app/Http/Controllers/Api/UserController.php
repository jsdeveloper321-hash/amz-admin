<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use DB;
use Illuminate\Support\Facades\Mail;

use Mpdf\Mpdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use App\Services\FmcsaService;

  
class UserController extends Controller {
    
    
    
    public function check(Request $request)
{
    $request->validate([
        'docket_number' => 'required|string'
    ]);

    $docketNumber = $request->docket_number;
    $webKey = '6e1b90075560acec3cb2e888c13c585a9002f253';

    if (!$webKey) {
        return response()->json([
            'success' => false,
            'message' => 'FMCSA WebKey missing'
        ], 500);
    }

    $url = "https://mobile.fmcsa.dot.gov/qc/services/carriers/docket-number/{$docketNumber}?webKey={$webKey}";

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'User-Agent: Laravel-FMCSA-Client'
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);

        return response()->json([
            'success' => false,
            'message' => 'cURL Error: ' . $error
        ], 500);
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        return response()->json([
            'success' => false,
            'status'  => $httpCode,
            'message' => 'FMCSA API error'
        ], $httpCode);
    }

    $data = json_decode($response, true);

    if (empty($data['content'][0])) {
        return response()->json([
            'success' => false,
            'message' => 'No carrier found for this docket number'
        ]);
    }
  
    $carrier   = $data['content'][0]['carrier'] ?? [];
    $authority = $data['content'][0]['carrier']['carrierOperation'] ?? [];

    return response()->json([
        'success'            => true,
        'docket_number'      => $docketNumber,
        'allowed_to_operate' => $carrier['allowedToOperate'] ?? 'N',
        'legal_name'         => $carrier['legalName'] ?? null,
        'dba_name'           => $carrier['dbaName'] ?? null,
        'carrier_operation'  => $authority,
    ]);
}

    
    
    
    
    
    
    
    
    
    
    
   public function  get_rating(Request $request) {

        // $categories = DB::table('privacy_policies')->whereNull('pp_deleted_at')->where('pp_admin_status', 'ACTIVE')->get();
                $categories = DB::table('get_rating')->get();

        if ($categories) {
            return $this->sendResponse($result = $categories, $message = 'rating successfully.', $notification = [], $error = [], $respose_code = 200);
        } else {
            return $this->sendError($result = [], $message = 'rating found.', $notification = [], $error = [], $respose_code = 200);
        }
    } 
    
    
    
 public function add_duty_request(Request $request)
{
    $user = Auth::guard('api')->user();
    if (!$user) {
        return $this->sendError([], 'Unauthorized', [], [], 401);
    }

    $validator = Validator::make($request->all(), [
        'rating' => 'nullable|string',
        'rpm'    => 'required|numeric',
    ]);

    if ($validator->fails()) {
        return $this->sendError([], $validator->errors()->first(), [], [], 422);
    }

    // 🔹 aaj ki date
    $today = date('Y-m-d');

    // 🔹 check aaj ka record exist karta hai ya nahi
    $existingDuty = DB::table('request_duty')
        ->where('user_id', $user->id)
        ->whereDate('created_at', $today)
        ->first();

    if ($existingDuty) {
        // ✅ UPDATE
        DB::table('request_duty')
            ->where('id', $existingDuty->id)
            ->update([
                'rating'              => $request->rating,
                'start_location'      => $request->start_location,
                'start_location_lat'  => $request->start_location_lat,
                'start_location_lon'  => $request->start_location_lon,
                'end_location'        => $request->end_location,
                'end_location_lat'    => $request->end_location_lat,
                'end_location_lon'    => $request->end_location_lon,
                'rpm'                 => $request->rpm,
                'driver_type'                 => $request->driver_type,
                'updated_at'          => now(),
            ]);

        $requestDutyId = $existingDuty->id;

    } else {
        // ✅ INSERT
        $requestDutyId = DB::table('request_duty')->insertGetId([
            'user_id'             => $user->id,
            'rating'              => $request->rating,
            'start_location'      => $request->start_location,
            'start_location_lat'  => $request->start_location_lat,
            'start_location_lon'  => $request->start_location_lon,
            'end_location'        => $request->end_location,
            'end_location_lat'    => $request->end_location_lat,
            'end_location_lon'    => $request->end_location_lon,
            'rpm'                 => $request->rpm,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }


    if($user->id){

DB::table('users')
             ->where('id', $user->id)
             ->update([
                'type'                 => $request->driver_type,
            ]);

}

    $requestDutyData = DB::table('request_duty')
        ->where('id', $requestDutyId)
        ->first();

    return $this->sendResponse(
        $requestDutyData,
        'Request duty saved successfully',
        [],
        [],
        200
    );
}

    
    
 public function change_duty(Request $request)
{
    $request->validate([
        'status' => 'required|in:driving,on_duty,break,off_duty,home'
    ]);

    $driver = Auth::guard('api')->user();
    $now = now();

    // ðŸ”´ previous open duty close
    $lastDuty = DB::table('duty_logs')
        ->where('user_id',$driver->id)
        ->whereNull('end_time')
        ->orderBy('id','desc')
        ->first();

    if ($lastDuty) {
        $minutes = \Carbon\Carbon::parse($lastDuty->start_time)->diffInMinutes($now);

        DB::table('duty_logs')
            ->where('id',$lastDuty->id)
            ->update([
                'end_time' => $now,
                'duration_minutes' => $minutes
            ]);
    }

    // ðŸŸ¢ new duty start
    DB::table('duty_logs')->insert([
        'user_id' => $driver->id,
        'status'    => $request->status,
        'duty_request_id'    => $request->duty_request_id,
        'start_time'=> $now,
        'created_at'=> $now,
         'updated_at'=> $now
        
    ]);

    return $this->sendResponse([], 'Duty status changed', [], [], 200);
}   
    
    
    
    
    
    
    
 public function duty_log_summary(Request $request)
{
    $driver = Auth::guard('api')->user();

    $dates = DB::table('duty_logs')
        ->where('user_id', $driver->id)
        ->select(DB::raw('DATE(start_time) as log_date'))
        ->groupBy('log_date')
        ->orderBy('log_date','desc')
        ->get();

    $result = [];

    foreach ($dates as $row) {

        $data = DB::table('duty_logs')
            ->where('user_id',$driver->id)
            ->whereDate('start_time',$row->log_date)
            ->select(
                DB::raw('SUM(duration_minutes) as total'),
                DB::raw('SUM(CASE WHEN status="driving" THEN duration_minutes END) as driving'),
                DB::raw('SUM(CASE WHEN status="on_duty" THEN duration_minutes END) as on_duty'),
                DB::raw('SUM(CASE WHEN status="break" THEN duration_minutes END) as break_time'),
                DB::raw('SUM(CASE WHEN status="off_duty" THEN duration_minutes END) as off_duty')
            )
            ->first();

        $total = $data->total ?? 0;

        $result[] = [
            'log_entry' => date('M d, Y', strtotime($row->log_date)),
            'total_driving_hours' => gmdate('H\h i\m', ($data->driving ?? 0) * 60),
            'total_on_duty'       => gmdate('H\h i\m', ($data->on_duty ?? 0) * 60),
            'break_time'          => gmdate('i\m', ($data->break_time ?? 0) * 60),
            'percentage' => [
                'driving'  => $total ? round(($data->driving / $total) * 100) : 0,
                'on_duty'  => $total ? round(($data->on_duty / $total) * 100) : 0,
                'break'    => $total ? round(($data->break_time / $total) * 100) : 0,
                'off_duty' => $total ? round(($data->off_duty / $total) * 100) : 0
            ]
        ];
    }

    return $this->sendResponse(
        $result,
        'Duty log list fetched',
        [],
        [],
        200
    );
}
   
    
    
 public function get_current_duty_request(Request $request)
{
    $user = Auth::guard('api')->user();
    if (!$user) {
        return $this->sendError([], 'Unauthorized', [], [], 401);
    }

    $today = now()->toDateString();

    // Fetch today's duty request for current user
    $requestDuty = DB::table('request_duty')
        ->where('user_id', $user->id)
        ->whereDate('created_at', $today)
        ->first();

    if (!$requestDuty) {
        return $this->sendResponse(null, 'Duty request not found', [], [], 200);
    }

    return $this->sendResponse($requestDuty, 'Duty request fetched successfully', [], [], 200);
}
   
    

public function add_duty_video(Request $request)
{
    // âœ… Validation
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|string',
        'video'   => 'required|file|max:25600' // 20MB max
    ]);

    if ($validator->fails()) {
        return $this->sendError([], $validator->errors()->first(), [], [], 200);
    }

    $video_name = null;

    // âœ… Video Upload
    if ($request->hasFile('video')) {
        $file = $request->file('video');
        $extension = $file->getClientOriginalExtension();

        $video_name = uniqid() . '_' . time() . '.' . $extension;

        // Upload path
        $file->move(public_path('uploads/duty_videos'), $video_name);
    }

    // âœ… Insert into DB
    $insertedId = DB::table('request_duty_video')->insertGetId([
        'user_id'     => $request->user_id,
        'video_path' => $video_name,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    if ($insertedId) {
        $video_url = url('public/uploads/duty_videos/' . $video_name);

        return $this->sendResponse([
            'id'         => $insertedId,
            'user_id'    => $request->user_id,
            'video_url'  => $video_url
        ], 'Video uploaded successfully.', [], [], 200);
    }

    return $this->sendError([], 'Failed to upload video.', [], [], 500);
}

 
 public function get_duty_videos(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|string',
    ]);

    if ($validator->fails()) {
        return $this->sendError([], $validator->errors()->first(), [], [], 200);
    }

    $videos = DB::table('request_duty_video')
        ->where('user_id', $request->user_id)
        ->orderBy('id', 'desc')
        ->get();

    if ($videos->isEmpty()) {
        return $this->sendError([], 'No videos found for this user.', [], [], 200);
    }

    // Full URL add karna
    $videos = $videos->map(function ($item) {
        $item->video_url = url('public/uploads/duty_videos/' . $item->video_path);
        return $item;
    });

    return $this->sendResponse($videos, 'Videos fetched successfully.', [], [], 200);
}

 
 

public function get_nearby_users(Request $request)
{
    

    $lat     = $request->latitude;
    $lon     = $request->longitude;
    $user_id = $request->user_id;

    /* ---------------------------------
       STEP 1: Update user lat & lon
    --------------------------------- */
    DB::table('users')
        ->where('id', $user_id)
        ->update([
            'lat'  => $lat,
            'lng' => $lon,
            'updated_at' => now()
        ]);

    /* ---------------------------------
       STEP 2: Find nearby users (100 KM)
    --------------------------------- */
    $radius = 100; // KM

    $users = DB::table('users')
        ->select(
            'users.*',
            DB::raw("
                ( 6371 * acos(
                    cos(radians($lat))
                    * cos(radians(users.lat))
                    * cos(radians(users.lng) - radians($lon))
                    + sin(radians($lat))
                    * sin(radians(users.lat))
                ) ) AS distance
            ")
        )
        ->whereNotNull('users.lat')
        ->whereNotNull('users.lng')
        ->where('users.id', '!=', $user_id) // current user exclude
        ->having('distance', '<=', $radius)
        ->orderBy('distance', 'asc')
        ->get();

    /* ---------------------------------
       STEP 3: Count nearby users
    --------------------------------- */
    $count = $users->count();

    /* ---------------------------------
       STEP 4: Response
    --------------------------------- */
    return $this->sendResponse(
        [
            'total_nearby_users' => $count,
            'users' => $users
        ],
        'Nearby users fetched successfully',
        [],
        [],
        200
    );
}

 
 
 
 
 
 
 
 
 
 
 
    
    public function get_announcements(Request $request)
{
    $userId = $request->user_id;

    // 👉 User ki location nikaalo
    $user = DB::table('users')->where('id', $userId)->first();

    if (!$user) {
        return $this->sendError([], 'User not found.', [], [], 200);
    }

    $userLat = $user->lat;
    $userLng = $user->lng;

    // 👉 Announcements fetch with radius check
    $announcements = DB::table('announcements')
        ->where('status', 'Sent')
        ->where(function ($query) use ($userLat, $userLng) {

            $query->whereRaw("
                (
                    6371 * acos(
                        cos(radians(?)) 
                        * cos(radians(latitude)) 
                        * cos(radians(longitude) - radians(?)) 
                        + sin(radians(?)) 
                        * sin(radians(latitude))
                    )
                ) <= radius
            ", [$userLat, $userLng, $userLat]);

        })
        ->orderBy('id', 'DESC')
        ->get();

    if ($announcements->count() > 0) {
        return $this->sendResponse($announcements, 'Announcements fetched successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'No announcements found.', [], [], 200);
    }
}
    
    
 
 
 public function get_offers(Request $request)
{
    $driverId = $request->user_id;

    // driver details
    $driver = DB::table('users')->where('id', $driverId)->first();

    if (!$driver) {
        return $this->sendError([], 'Driver not found.', [], [], 200);
    }

    // 👉 Offers fetch
    $offers = DB::table('offers')
        ->where(function ($query) use ($driverId, $driver) {

            // ✅ All Drivers
            $query->where('assign_drivers', 'all');

            // ✅ Company based
            $query->orWhere(function ($q) use ($driver) {
                $q->where('assign_drivers', 'company')
                  ->where('mc_number', $driver->mc_number);
            });

            // ✅ Exact drivers
            $query->orWhereIn('id', function ($sub) use ($driverId) {
                $sub->select('offer_id')
                    ->from('offer_driver_status')
                    ->where('driver_id', $driverId);
            });

        })
        ->orderBy('id', 'DESC')
        ->get();

    if ($offers->count() > 0) {
        return $this->sendResponse($offers, 'Offers fetched successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'No offers found.', [], [], 200);
    }
}
 
 
 
 /*------------------------------------------end api------------------------------------------------------------*/
 /*------------------------------------------------------------------------------------------------------*/
 /*------------------------------------------------------------------------------------------------------*/
 /*------------------------------------------------------------------------------------------------------*/
 /*------------------------------------------------------------------------------------------------------*/
 /*------------------------------------------------------------------------------------------------------*/
 /*------------------------------------------------------------------------------------------------------*/
 
 
 
 
 
public function add_invoice(Request $request)
{
    $user = Auth::guard('api')->user();
    if (!$user) {
        return $this->sendError([], 'Unauthorized', [], [], 401);
    }

    $price    = $request->price;
    $tax      = $request->tax ?? 0;
    $discount = $request->discount ?? 0;
    $netAmount = ($price + $tax) - $discount;

    // Generate a unique random code
    do {
        $randomCode = strtoupper(Str::random(8)); // 8-char random code
    } while (Invoice::where('qr_code', $randomCode)->exists());

    $invoice = Invoice::create([
        'user_id'        => $user->id,
        'customer_name'  => $request->customer_name,
        'car_model'      => $request->car_model,
        'car_variant'    => $request->car_variant,
        'vin_chassis_no' => $request->vin_chassis_no,
        'price'          => $price,
        'tax'            => $tax,
        'discount'       => $discount,
        'net_amount'     => $request->net_amount,
        'qr_code'        => $randomCode,
        'accessories_addons' => $request->accessories_addons
    ]);

    /* ================= QR CODE ================= */
    $qrPath = "qr/invoice_{$invoice->id}.png";
    if (!file_exists(public_path('qr'))) {
        mkdir(public_path('qr'), 0777, true);
    }

    QrCode::format('png')->size(300)->generate(
        "Invoice Code: {$invoice->qr_code}", // QR contains the random code
        public_path($qrPath)
    );

    /* ================= PDF ================= */
    if (!file_exists(public_path('invoices'))) {
        mkdir(public_path('invoices'), 0777, true);
    }

    $html = view('invoice.mpdf', [
        'invoice' => $invoice,
        'qrPath'  => $qrPath
    ])->render();

    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
    ]);

    $mpdf->WriteHTML($html);

    $pdfPath = public_path("invoices/invoice_{$invoice->id}.pdf");
    $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

    $invoice->update([
        'pdf_path' => "invoices/invoice_{$invoice->id}.pdf",
        'qr_path'  => $qrPath,
    ]);

    return $this->sendResponse([
        'invoice' => $invoice,
        'pdf_url' => url('public/' . $invoice->pdf_path),
        'qr_url'  => url('public/' . $invoice->qr_path),
        'qr_code' => $invoice->qr_code, // send the code for reference
    ], 'Invoice created with random QR code', [], [], 200);
}
 

 
 
 
 
 public function get_invoices_by_user(Request $request)
{
    
     $user = Auth::guard('api')->user();
      $userId = $user->id;

    if (!$userId) {
        return $this->sendError([], 'User ID is required', [], [], 400);
    }

    // Fetch invoices for the user
    $invoices = Invoice::where('user_id', $userId)->get();

    if ($invoices->isEmpty()) {
        return $this->sendResponse([], 'No invoices found for this user', [], [], 200);
    }

    // Format response with URLs
    $data = $invoices->map(function ($invoice) {
        return [
            'id'           => $invoice->id,
            'customer_name'=> $invoice->customer_name,
            'car_model'    => $invoice->car_model,
            'car_variant'  => $invoice->car_variant,
            'vin_chassis_no'=> $invoice->vin_chassis_no,
            'price'        => $invoice->price,
            'tax'          => $invoice->tax,
            'discount'     => $invoice->discount,
            'net_amount'   => $invoice->net_amount,
            'qr_code'      => $invoice->qr_code,
             'accessories_addons'      => $invoice->accessories_addons,
            'pdf_url'       => $invoice->pdf_path ? url('public/' . $invoice->pdf_path) : null,
            'qr_url'        => $invoice->qr_path ? url('public/' . $invoice->qr_path) : null,
            'created_at'   => $invoice->created_at,
        ];
    });

    return $this->sendResponse($data, 'Invoices fetched successfully', [], [], 200);
}

 
 
 
 
 
 
  public function add_invoice_image(Request $request)
{
    // Validate request (only check required, not file type)
    $validator = Validator::make($request->all(), [
        'user_id'     => 'required|string',
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
        $file->move(public_path('uploads/invoices'), $file_name);
    }

    // Save in DB (only file name, not object)
    $inserted = DB::table('invoices')->insertGetId([
        'user_id'     => $request->input('user_id'),
        'pdf_path' => $file_name, 
        'created_at' => now(),
        'updated_at' => now()
       
    ]);

    if ($inserted) {
        // Full URL response
        $file_url = url('public/uploads/invoices/' . $file_name);

        return $this->sendResponse([
            'id'       => $inserted,
            'user_id'     => $request->input('user_id'),
           'pdf_path' => $file_url
        ], 'File uploaded successfully.', [], [], 200);
    } else {
        return $this->sendError([], 'Failed to upload file.', [], [], 500);
    }
}
 
  
  
 
 
 
 /*==============================================================================================================================*/
  /*==============================================================================================================================*/
   /*==============================================================================================================================*/
    /*==============================================================================================================================*/
     /*==============================================================================================================================*/
      /*==============================================================================================================================*/
       /*==============================================================================================================================*/
        /*==============================================================================================================================*/
         /*==============================================================================================================================*/
          /*==============================================================================================================================*/
 
 
 
 
        
    
    

}
