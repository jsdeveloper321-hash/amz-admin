<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayPalController extends Controller
{
    
      public function add_payment_status_check(Request $request)
      {
        $user_id = $request->input('user_id');
        $token = $request->input('token');
        $order_number = $request->input('order_number');
        
        $payments = DB::table('payment')
            ->where('payment_token_id', $token)
            ->get()
            ->toArray();
            
            if(empty($payment))
            {
                 $status = @$payments[0]->payment_status;
                 if($status=='COMPLETED')
                 {
                      $updated = DB::table('payment')
        ->where('payment_token_id', $token)  
        ->update(['user_id' => $user_id, 'order_number' => $order_number]); 
         

                   $success['res'] =  @$payments[0]->payment_data;

                    return $this->sendResponse($result = $success, $message = " successfully.", $notification = null, $error = null, $respose_code = 200);
        
                 }
                 else
                 {
                     return $this->sendError([], 'Not Valid.', [], [], 200);  
                 }
            }
            else
            {
               return $this->sendError([], 'Not Valid.', [], [], 200); 
            }
            
             
            
           
            
            
            $result = json_decode($payments, true);


        // print_r($result['status']);


      }
    
    // Create PayPal Order
    public function createOrder(Request $request)
    {
        $currency = $request->input('currency'); 
        
        
        $amount = $request->input('amount'); 
        
        $accessToken = $this->getAccessToken();
       
        // Set up order details
        $orderDetails = [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => $currency, // config('paypal.currency', 'USD'),
                        "value" => $amount // Modify as needed
                    ]
                ]
            ],
            "application_context" => [
                "return_url" => route('paypal.success'),
                "cancel_url" => route('paypal.cancel')
            ]
        ];
        
        
        $paypalBaseUrl = env('PAYPAL_API_BASE_URL');

        // Set up cURL request to PayPal API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paypalBaseUrl."/v2/checkout/orders");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderDetails));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

      

        $order = json_decode($response, true);

        if (isset($order['id']) && isset($order['links'])) {
            // Redirect to the PayPal approval URL
            foreach ($order['links'] as $link) {
                if ($link['rel'] === 'approve') {
                  //  return redirect()->away($link['href']);
                  
            $success['url'] = $link['href'];

            return $this->sendResponse($result = $success, $message = " successfully.", $notification = null, $error = null, $respose_code = 200);
        
                    
                    // return $this->sendResponse([], 'Booking added successfully.', [], [], 200);
                    
                }
            }
        }

        return response()->json(['error' => 'Unable to create PayPal order.'], 500);
    }

    // Capture PayPal Order
    public function captureOrder(Request $request)
    {
        
        
        $paypalBaseUrl = env('PAYPAL_API_BASE_URL');

        $accessToken = $this->getAccessToken();
        $orderId = $request->query('token');  // PayPal sends 'token' as the order ID
       
        // Set up cURL request to capture payment
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paypalBaseUrl."/v2/checkout/orders/{$orderId}/capture");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);


        if (isset($result['status']) && $result['status'] == "COMPLETED") {
            // return response()->json(['success' => 'Payment completed successfully.', 'transaction_id' =>$result['id']]);
           $tranid =  $result['id'];
        //   $payment_status = 
          DB::table('payment')->insert([
    'payment_token_id' => $tranid,
    'payment_status' => 'COMPLETED',
    'transaction_id' => $tranid,
    'payment_data' => $response
    
]);

        //   return redirect()->away('https://server-php-8-3.technorizen.com/success?transaction_id='.$result['id'].''); die;


            
            $success['url'] = $paypalBaseUrl.'/success?transaction_id='.$result['id'].'&status=Payment completed successfully';

            return $this->sendResponse($result = $success, $message = " successfully.", $notification = null, $error = null, $respose_code = 200);
        
        
        }
        else
        
        {
           $sta =  $result['status'];
             DB::table('payment')->insert([
    'payment_token_id' => $tranid,
    'payment_status' => $sta,
    'transaction_id' => $tranid,
    'payment_data' => $response
    
]);
             return redirect()->away('https://server-php-8-3.technorizen.com/cancel');
        }
           
            // $success['url'] = $paypalBaseUrl.'cancel?transaction_id=&status=Payment Cancelled';
            // return $this->sendResponse($result = $success, $message = " Payment Failed.", $notification = null, $error = null, $respose_code = 200);
        
    }

    // Get PayPal Access Token
    private function getAccessToken()
    {
        
        $paypalClientId = env('PAYPAL_CLIENT_ID');
        $paypalSecret = env('PAYPAL_SECRET');
        $paypalBaseUrl = env('PAYPAL_API_BASE_URL');
    
        $clientId = $paypalClientId;//'Your-Client-ID';
        $secret = $paypalSecret;//'Your-Secret-Key';


        // Set up cURL request for access token
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $paypalBaseUrl."/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Accept-Language: en_US',
        ));
       
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        
        // echo '<pre>';
        // print_r($response);
        // echo '</pre>'; die;

        if (isset($result['access_token'])) {
            return $result['access_token'];
        }

        throw new \Exception('Could not obtain PayPal access token.');
    }
}
