<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use DB;
    
    
class ChatController extends Controller
{
    
    
    public function get_employee_code_by_users(Request $request) {
    $user_id = $request->input('user_id');

    // pehle user ka employee_code nikal lo
  $employee_code = DB::table('users')
        ->where('id', $user_id)
        ->value('other_employee_code'); 

    if (!$employee_code) {
        return $this->sendError(
            [],
            'Employee code not found for this user.',
            [],
            [],
            200
        );
    }

    // ab us employee_code se matching users nikalo (apne user ko chodkar)
    $categories = DB::table('users')
        ->where('id', '!=', $user_id)
        ->where('other_employee_code', $employee_code)
        ->get();

    if (!$categories->isEmpty()) {
        $result = $categories->map(function ($user) {
            // image path set karo
            if (!empty($user->image)) {
                $user->image = url('public/uploads/users/' . $user->image);
            } else {
                $user->image = url('public/uploads/users/default.png');
            }
            return $user;
        });

        return $this->sendResponse(
            $result,
            'Employee list retrieved successfully.',
            [],
            [],
            200
        );
    } else {
        return $this->sendError(
            [],
            'No matching employee found.',
            [],
            [],
            200
        );
    }
}

 
 
    
    public function insert_chat(Request $request)
{
    $validator = Validator::make($request->all(), [
        'sender_id'   => 'required|numeric',
        'receiver_id' => 'required|numeric',
        'chat_message'=> 'required',
    ]);

    if ($validator->fails()) {
        $errors = $validator->errors()->all();
        return $this->sendError([], $errors[0], [], $errors, 200);
    }

    $data = [
        'chat_sender_id'   => $request->input('sender_id'),
        'chat_receiver_id' => $request->input('receiver_id'),
        'chat_message'     => $request->input('chat_message'),
        'chat_type'        => 'IMAGE',   // abhi IMAGE fix hai
        'chat_created_at'  => now(),
        'chat_updated_at'  => now(),
    ];
    
    // insert aur ID return karo
    $chat_id = DB::table('chats')->insertGetId($data);

    if ($chat_id) {
        // ab inserted record fetch karo
        $inserted_chat = DB::table('chats')->where('id', $chat_id)->first();

      

        return $this->sendResponse(
            $inserted_chat,
            'Message sent successfully.',
            [],
            [],
            200
        );
    } else {
        return $this->sendError([], 'Failed to send message.', [], [], 200);
    }
}


public function get_chat(Request $request)
{
   
    
      $user_id = $request->input('sender_id');
      $receiver_id = $request->input('receiver_id');
      
   
    // Validate the request parameters
    $validator = Validator::make($request->all(), [
        'receiver_id' => 'required|numeric', // The receiver's user ID
    ]);

    // If validation fails, return an error response
    if ($validator->fails()) {
        $errors = $validator->errors()->all();
        return $this->sendError([], $errors[0], [], $errors, 200);
    }
    
    
    
        // Step 1: Seen update (yaha hi karenge)
    DB::table('chats')
        ->where('chat_sender_id', $receiver_id)
        ->where('chat_receiver_id', $user_id)
        ->where('chat_status', '!=', 'SEEN')
        ->update([
            'chat_status'    => 'SEEN',
            'chat_updated_at'=> now()
        ]);
    
    
    
    
    $chats = DB::table('chats')
                ->where(function ($query) use ($user_id, $receiver_id) {
                    $query->where('chat_sender_id', $user_id)
                          ->where('chat_receiver_id', $receiver_id);
                })
                ->orWhere(function ($query) use ($user_id, $receiver_id) {
                    $query->where('chat_sender_id', $receiver_id)
                          ->where('chat_receiver_id', $user_id);
                })
                ->orderBy('chat_created_at')
                ->get();

    // If no chats found, return a not found response
    if ($chats->isEmpty()) {
        return $this->sendError([], 'No chats found.', [], [], 200);
    }

    // Prepare the chats response with proper message format
    foreach ($chats as $chat) {
     
    }

    // Return the chats as a success response
    return $this->sendResponse($chats, 'Chats retrieved successfully.', [], [], 200);
}




public function get_last_messages(Request $request)
{
    $user_id = $request->input('user_id'); // jis user ke liye conversation list chahiye

    // Subquery: har conversation ka last chat_id nikal lo
    $subQuery = DB::table('chats')
        ->select(DB::raw('MAX(id) as last_chat_id'))
        ->where(function ($q) use ($user_id) {
            $q->where('chat_sender_id', $user_id)
              ->orWhere('chat_receiver_id', $user_id);
        })
        ->groupBy(DB::raw("CASE 
                              WHEN chat_sender_id = $user_id 
                              THEN chat_receiver_id 
                              ELSE chat_sender_id 
                            END"));

    $lastMessages = DB::table('chats')
        ->joinSub($subQuery, 'last_chats', function ($join) {
            $join->on('chats.id', '=', 'last_chats.last_chat_id');
        })
        ->leftJoin('users as sender', 'chats.chat_sender_id', '=', 'sender.id')
        ->leftJoin('users as receiver', 'chats.chat_receiver_id', '=', 'receiver.id')
        ->orderBy('chats.chat_created_at', 'DESC')
        ->select(
            'chats.*',
            'sender.first_name as sender_first_name',
            'sender.last_name as sender_last_name',
            'sender.image as sender_image',
            'receiver.first_name as receiver_first_name',
            'receiver.last_name as receiver_last_name',
            'receiver.image as receiver_image'
        )
        ->get();

    // format response
    $result = $lastMessages->map(function ($chat) use ($user_id) {
        // time ago
        $chat->time_ago = $this->timeAgo($chat->chat_created_at);

     

        // sender image path
        $chat->sender_image = !empty($chat->sender_image)
            ? url('public/uploads/users/' . $chat->sender_image)
            : url('public/uploads/users/default.png');

        // receiver image path
        $chat->receiver_image = !empty($chat->receiver_image)
            ? url('public/uploads/users/' . $chat->receiver_image)
            : url('public/uploads/users/default.png');

        // conversation partner set karo
        if ($chat->chat_sender_id == $user_id) {
            $chat->conversation_user_id    = $chat->chat_receiver_id;
            $chat->conversation_first_name = $chat->receiver_first_name;
            $chat->conversation_last_name  = $chat->receiver_last_name;
            $chat->conversation_image      = $chat->receiver_image;
        } else {
            $chat->conversation_user_id    = $chat->chat_sender_id;
            $chat->conversation_first_name = $chat->sender_first_name;
            $chat->conversation_last_name  = $chat->sender_last_name;
            $chat->conversation_image      = $chat->sender_image;
        }

        return $chat;
    });

    return $this->sendResponse($result, 'Last messages retrieved successfully.', [], [], 200);
}

// Helper function for "time ago"
private function timeAgo($datetime)
{
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60)
        return $diff . " sec ago";
    elseif ($diff < 3600)
        return floor($diff / 60) . " min ago";
    elseif ($diff < 86400)
        return floor($diff / 3600) . " hours ago";
    else
        return floor($diff / 86400) . " days ago";
}


 
}

