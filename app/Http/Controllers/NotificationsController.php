<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    public function sentNotification(Request $request)
    {
        $user = auth()->user();
        $id = $user->customer_id;

        $this->validate($request,[
            'notification_title'       => 'required',
            'notification_message'     => 'required',
            'notification_interaction' => 'required',
            'unique_id'                => 'required',
            'from_unique_id'           => 'required',
            'type'                     => 'required',
        ]);

        $now = Carbon::now();
        // $message = DB::table('oc_notifications')
        // ->where('notification_id', $request['customer_id'])
        // ->first();

       DB::table('oc_notifications')->insert([
        'customer_id' => $id,
        'from_customer_id' => 0,
        'notification_title' => $request['notification_title'],
        'notification_message' => $request['notification_message'],
        'notification_interaction' => $request['notification_interaction'],
        'notification_action' => $request['notification_action'],
        'notification_datetime' => $now,
        'notification_expire_datetime' => $now,
        'unique_id' => $request['unique_id'],
        'from_unique_id' => $request['from_unique_id'],
        'type' => $request['type'],
       ]);

       return response()->json( [
        'action' => 'sent notification',
        'status' => 'success',
        'success' => true
         ], 201);

    }

    public function getNotifications(Request $request, $unique_id){

        $today = Carbon::now()->format('Y-m-d');

        if($request['type'] == 'social'){

            $result = DB::table('oc_notifications')
            ->where('type', '=', 'social')
            ->whereDate('notification_datetime','=', $today)
            ->where('unique_id','=', $unique_id)
            ->get();

        }

        if($request['type'] == 'promotion'){
            $result = DB::table('oc_notifications')
            ->where('type', '=', 'promotion')
            ->whereDate('notification_datetime','=', $today)
            ->where('unique_id','=', $unique_id)
            ->get();
        }

        if($request['type'] == 'alert'){
            $result = DB::table('oc_notifications')
            ->where('type','=', 'alert')
            ->whereDate('notification_datetime','=', $today)
            ->where('unique_id','=', $unique_id)
            ->get();
        }

        if($request['type'] == 'activity'){
            $result = DB::table('oc_notifications')
            ->where('type', '=', 'activity')
            ->whereDate('notification_datetime','=', $today)
            ->where('unique_id','=', $unique_id)
            ->get();
        }

        if($request['type'] == 'buy_mode'){
            $result = DB::table('oc_notifications')
            ->where('type', '=', 'buy_mode')
            ->whereDate('notification_datetime','=', $today)
            ->where('unique_id','=', $unique_id)
            ->get();
        }
        
        if($request['type'] == 'general'){
            $result = DB::table('oc_notifications')
            ->whereDate('notification_datetime','=', $today)
            ->where('unique_id','=', $unique_id)
            ->get();
        }
            
        return response()->json( [
        'notification' => $result,
        'status' => 'success',
        'success' => true
         ], 201);
    }

    public function isRead(Request $request)
    {
        $this->validate($request, [

        ]);
    }

    public function deleteNotification(Request $request, $notification_id)
    {
        // $this->validate($request, [
        //     'unique_id' => 'required'
        // ]);

        $deletenotification = DB::table('oc_notifications')
            ->where('notification_id', '=', $notification_id)
            // ->where('unique_id', '=', $request['unique_id'])
            ->delete();

        return response()->json([
            'promotion' => $deletenotification,
            'status' => 'success',
            'action' => 'deleted promotion'
        ], 200);
    }
}
