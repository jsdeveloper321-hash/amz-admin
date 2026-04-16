<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    public function sendResponse($result = [], $message = "" , $notification = [], $error = [] , $respose_code = 200)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
            'notification' => null,
            'error' => null,
            'status' => '1',
        ];

        return response()->json($response, $respose_code);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($result = [], $message = "" , $notification = [], $error = [] , $respose_code = 200)
    {
     	$response = [
            'success' => false,
            'data'    => $result,
            'message' => $message,
            'notification' => null,
            'error' => null,
            'status' => '0',

        ];

        return response()->json($response, $respose_code);
    }
    
}


