<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\CustomerLike;
use App\Models\PurpleTreeStore;
use App\Models\Vod;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Owenoj\LaravelGetId3\GetId3;
use Illuminate\Support\Facades\Storage;

class VODController extends Controller
{
    public function likeorUnlike(Request $request, $user_id)
    {
        $user = auth()->user();
        // $user_id = $user->user_id;

        $sent_like_to = $request->get('sent_like_to');

        if($sent_like_to == $user_id)
        {
            return response()->json(['status' => 'failed'], 409);
        }

        $like = CustomerLike::where('by_unique_id',$user_id)
                                        ->where('unique_id',$sent_like_to)
                                        ->first();

        //Like
        if($like == null)
        {
            $insert_Like = new CustomerLike;
            $insert_Like->by_unique_id = $user_id;
            $insert_Like->unique_id = $sent_like_to;
            $insert_Like->save();

            $like_count = Vod::where('unique_id', $user_id)->get()->count();

            $like_update = Vod::where('unique_id', $user_id)->update([
                'like_count' => $like_count
            ]);

            //Send Notification Like
            $tomorrow = new DateTime('tomorrow');

            $get_user = $user_id;
            $explode_user = explode("_", $get_user);

            if($explode_user[0] == "ST"){

                $user = PurpleTreeStore::where('seller_unique_id', $user_id)
                    ->first();

                $name = $user['store_name'];

                $profile_image = $user['store_logo'];

            }
            elseif($explode_user[0] == "CS"){

                $user = Customer::where('user_id', $user_id)
                            ->first();

                $name = $user['username'];

                $profile_image = $user['profile_url'];
            }

            $user_name = $name;
            $user_image = $profile_image;

            DB::table('oc_notifications')->insert([
                'customer_id' => 0,
                'from_customer_id' => 0,
                'type' => 'socials',
                'notification_title' => $user_name. " liked your video",
                'notification_message' => $user_name. " liked your video",
                'notification_action' => 'like',
                'notification_interaction' => '',
                'notification_is_read' => 0,
                'notification_datetime' => Carbon::now(),
                'notification_expire_datetime' => $tomorrow,
                'unique_id' => $sent_like_to,
                'from_unique_id' => $user_id,
                'name' => $user_name,
                'profile_image' => $user_image,
            ]);

           return response()->json( [
                'entity' => 'users', 
                'action' => 'like', 
                'like count' => $like_update,
                'status' => 'success'
                ], 201);
        }

        //UnLike 
        if($like != null)
        {
            $unlike = CustomerLike::where('by_unique_id',$user_id)
                                    ->where('unique_id',$sent_like_to)
                                    ->delete();

             $like_count = Vod::where('unique_id', $user_id)->count();

             $like_update = Vod::where('unique_id', $user_id)->update([
                'like_count' => $like_count
             ]);

            return response()->json( [
                'entity' => 'users', 
                'action' => 'unlike', 
                'like count' => $like_update,
                'status' => 'success'
                ], 201);
        }

    }

    public function uploadVOD(Request $request, $unique_id)
    {
        $user = auth()->user();
        $user_id = $user->user_id;

        $this->validate($request,[
            'video' => 'required|mimes:mp4',
            'caption' => 'required',
        ]);

            $caption = $request->get('caption');

            $vod = new Vod;

            $vod->unique_id = $unique_id;
            $vod->like_count = 0;
            $vod->status = '';
            $vod->caption = $caption;


            // $video = $request->file('video');
            // $video_name = time() . "." . $video->getClientOriginalExtension();
            // $validation['filename'] = $video_name;
            // $video->storeAs('videos', $video_name);

            // $path = Storage::path("videos/" . $video_name);
            // $getID3 = new \getID3;
            // $video_file = $getID3->analyze($path);
            // $duration_seconds = $video_file['playtime_string'];

            // $duration_limit = '1:00';

            // if($duration_seconds > $duration_limit){
            //     return response()->json(['error' => 'video duration too long',
            //                              'status' => 'failed'], 403);
            // }
            $vod->path = $request['vod_path'];


            $vod->save();



            // $original_filename = $request->file('video')->getClientOriginalName();
            // $original_filename_arr = explode('.', $original_filename);
            // $file_ext = end($original_filename_arr);

            // $file_number = str_random(20);

            // $destination_path = 'upload'. DIRECTORY_SEPARATOR . 'vod'. DIRECTORY_SEPARATOR;
            // $document = $file_number. '.' . $file_ext;

            // if ($request->file('video')->move($destination_path, $document)) 
            // {
            //     $video = '/upload/vod/' . $document;
            //     $vod->path = $destination_path . $document;

            // }else{
            //     return response()->json('Cannot upload file');
            // }

            return response()->json(['message' => 'vod uploaded',
                                     'status' => 'success',
                                     'success' => true], 200);
    }

    public function getVOD(){

        $vod = DB::table('oc_vod')->get();

        return response()->json(['vod' => $vod,
                                     'status' => 'success',
                                     'success' => true], 200);
    }

    public function getVODbyUnique_id($unique_id){

        $vod = DB::table('oc_vod')->where('unique_id', $unique_id)->get();

        return response()->json(['vod' => $vod,
                                     'status' => 'success',
                                     'success' => true], 200);
    }
}
