<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatRoomOwner;
use App\Models\Customer;
use App\Models\PurpleTreeStore;
use App\Repository\Chat\ChatInterface;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnValue;

class ChatController extends Controller
{

    private ChatInterface $chat;

    public function __construct(
        ChatInterface $chat

    ) {
        $this->chat = $chat;
    }

    public function getRoom(Request $request)
    {
        $this->validate($request, [
            'unique_id' => 'required'
        ]);

        $chatRoom = $this->chat->getChatList($request['unique_id']);

        return response()->json(
            [
                'chat_room' => $chatRoom,
                'status' => 'success',
                'success' => true
            ],
            200
        );
    }

    public function getRoomOwnerByID($chatroom_id)
    {
        $user = auth()->user();
        $id = $user->user_id;

        // dd($id);

        $results = DB::table('oc_chat_room_owner')
            ->select('oc_chat_room_owner.user_unique_id', 'oc_chat_room.name','oc_customer.username')
            ->join('oc_chat_room', 'oc_chat_room.id', '=', 'oc_chat_room_owner.chatroom_id')
            ->join('oc_customer','oc_customer.user_id','=','oc_chat_room_owner.user_unique_id')
            ->where('oc_chat_room_owner.user_unique_id', '=', $id)
            ->where('oc_chat_room_owner.chatroom_id','=',$chatroom_id)
            ->get();

        $recipient = DB::table('oc_chat_room_owner')
                        ->select('oc_chat_room_owner.user_unique_id', 'oc_chat_room.name','oc_customer.username')
                        ->join('oc_chat_room', 'oc_chat_room.id', '=', 'oc_chat_room_owner.chatroom_id')
                        ->join('oc_customer','oc_customer.user_id','=','oc_chat_room_owner.user_unique_id')
                        ->where('oc_chat_room_owner.user_unique_id', '!=', $id)
                        ->where('oc_chat_room_owner.chatroom_id','=',$chatroom_id)
                        ->get();

        $getroom = ChatRoomOwner::where('chatroom_id', $chatroom_id)
            ->where('chatroom_id', $chatroom_id)
            ->get();

        return response()->json(
            [
                'chat_room_owner' => $getroom,
                'chat_room data' => $results,
                'chat_room receive' => $recipient,
                'status' => 'success'
            ],
            200
        );
    }

    public function getRoomOwner()
    {
        $user = auth()->user();
        $id = $user->user_id;

        // dd($id);

        $results = DB::table('oc_chat_room_owner')
            ->select('oc_chat_room_owner.*', 'oc_chat_room.*','oc_customer.username')
            ->join('oc_chat_room', 'oc_chat_room.id', '=', 'oc_chat_room_owner.chatroom_id')
            ->join('oc_customer','oc_customer.user_id','=','oc_chat_room_owner.user_unique_id')
            ->where('oc_chat_room_owner.user_unique_id', '=', $id)
            ->get();

        $recipient = DB::table('oc_chat_room_owner')
                        ->select('oc_chat_room_owner.*', 'oc_chat_room.*','oc_customer.username')
                        ->join('oc_chat_room', 'oc_chat_room.id', '=', 'oc_chat_room_owner.chatroom_id')
                        ->join('oc_customer','oc_customer.user_id','=','oc_chat_room_owner.user_unique_id')
                        ->where('oc_chat_room_owner.user_unique_id', '!=', $id)
                        ->get();

        // $getroom = ChatRoomOwner::where('chatroom_id', $chatroom_id)
        //     ->where('chatroom_id', $chatroom_id)
        //     ->get();

        return response()->json(
            [
                // 'chat_room_owner' => $getroom,
                'chat_room data' => $results,
                'chat_room receive' => $recipient,
                'status' => 'success'
            ],
            200
        );
    }

    public function ChatMessage(Request $request, $chatroom_id)
    {
        $user = auth()->user();
        $id = $user->user_id;

        $results = DB::table('oc_chat_message')
            ->select('oc_chat_message.*', 'oc_chat_room.name')
            ->join('oc_chat_room', 'oc_chat_room.name', '=', 'oc_chat_message.chatroom_id')
            ->where('oc_chat_room.name', '=', $chatroom_id)
            ->orderBy('oc_chat_message.created_at', 'desc')
            ->get();

        return response()->json(
            [
                'chat_message' => $results,
                'status' => 'success'
            ],
            200
        );
    }


    public function sentChat(Request $request)
    {
        $user = auth()->user();

        $id = $user->user_id;


        $this->validate($request, [
            'message' => 'required_if:type,text',
            'media' => 'required_unless:type,text',
            'chatroom_id' => 'required',
            'sent_to' => 'required',
            'sent_by' => 'required',
        ]);

        $now = Carbon::now();
        $room = DB::table('oc_chat_room')
            ->where('name', $request['chatroom_id'])
            ->first(); 

        $get_user2 = $request['sent_to'];
        $get_user1 = $id;
        $explode_user1 = explode("_", $get_user1);
        $explode_user2 = explode("_", $get_user2);

        if($explode_user1[0] == "ST"){

            $user1 = PurpleTreeStore::where('seller_unique_id', $get_user1)
                ->first('store_name');

            $chat1 = $user1['store_name'];

        }
        elseif($explode_user1[0] == "CS"){

            $user1 = Customer::where('user_id', $get_user1)
                        ->first('username');

            $chat1 = $user1['username'];
        }else{
            return response()->json(['message' => 'something went wrong',
                                     'status' => 'failed'] , 404);
        }

        if($explode_user2[0] == "ST"){

            $user2 = PurpleTreeStore::where('seller_unique_id', $request['sent_to'])
                ->first('store_name');

            $chat2 = $user2['store_name'];

                
        }
        elseif($explode_user2[0] == "CS"){

            $user2 = Customer::where('user_id', $request['sent_to'])
                        ->first('username');

            $chat2 = $user2['username'];
        }else{
            return response()->json(['message' => 'something went wrong',
                                     'status' => 'failed'] , 404);
        }

        if ($room == null) {
            $newRoom = new ChatRoom([
                'name' => $get_user1.'&&'.$get_user2,
                'status' => 'active',
                'created_by' => $id,
                'created_at' => $now,
                'updated_at' => $now
            ]);

            $newRoom->save();

            DB::table('oc_chat_room_owner')->insert([
                'chatroom_id' => $newRoom['name'],
                'user_unique_id' => $id
            ]);

            DB::table('oc_chat_room_owner')->insert([
                'chatroom_id' => $newRoom['name'],
                'user_unique_id' => $request['sent_to']
            ]);

            DB::table('oc_chat_message')->insert([
                'message' => $request['message'],
                'media' => $request['media'],
                'type' => $request['type'],
                'chatroom_id' => $newRoom['name'],
                'created_by' => $id,
                'sent_to' => $request['sent_to'],
                'created_at' => $now,
                'updated_at' => $now
            ]);
        } else {
            DB::table('oc_chat_message')->insert([
                'message' => $request['message'],
                'type' => $request['type'],
                'media' => $request['media'],
                'chatroom_id' => $request['chatroom_id'],
                'created_by' => $id,
                'sent_to' => $request['sent_to'],
                'created_at' => $now,
                'updated_at' => $now
            ]);


            $chatroom = new Message;
            $chatroom->oc_chat_room = $request->id;
        }


        return response()->json([
            'action' => 'sent message',
            'status' => 'success',
            'success' => true
        ], 201);
    }

    public function checkIfRoomExist(Request $request, $user_id)
    {
        $user = auth()->user();

        $id = $user->user_id;

        $sender = ChatRoomOwner::where([
            'user_unique_id' => $id
        ])->first();

        $receiver = ChatRoomOwner::where([
            'user_unique_id' => $user_id
        ])->first();
        $isExist = false;
        if ($sender != null && $receiver != null) {
            $isExist = $receiver['chatroom_id'] == $sender['chatroom_id'];
        }


        return response()->json([
            'status' => 'success',
            'success' => true,
            'data' => [
                'is_exist' => $isExist,
                'chatroom_id' => $isExist ? $receiver['chatroom_id'] : 0
            ]
        ], 201);
    }

    public function getRoomMessage(){
        $room = DB::table('oc_chat_message')
                    ->select('*')
                    ->get();
                    

        return response()->json(['status' => 'success',
                                 'success' => true,
                                 'data' => $room], 201);
    }

    public function updateIsRead(Request $request)
    {
        
        $chatroom_id = $request['chatroom_id'];

        $is_read = DB::table('oc_chat_message')->where('chatroom_id', $chatroom_id)->update([
                    'is_read' => 1
        ]);

        return response()->json(['status' => 'success',
                                 'is_read' => $is_read
                                ], 201);
    }

    public function countUnRead($chatroom_id)
    {   

        $count = DB::table('oc_chat_message')->where('chatroom_id', $chatroom_id)->where('is_read', 0)->count();

        return response()->json(['status' => 'success',
                                 'new message' => $count
                                ], 201);
    }
}