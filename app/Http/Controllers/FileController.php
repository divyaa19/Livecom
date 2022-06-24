<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Owenoj\LaravelGetId3\GetId3;
use Illuminate\Support\Facades\Storage;
use App\Models\Vod;

class FileController extends Controller
{
    public function uploadFile(Request $request){

        $extension = $request->file('file')->getClientOriginalExtension();
        $file_ext = $request->file('file')->getClientOriginalName();
        $files_name = pathinfo($file_ext, PATHINFO_FILENAME);
        $file_name = $files_name . '-' . uniqid() . '.' . $extension;
        $file_name = str_replace(' ','_',$file_name);
        $destination_path = 'upload'. DIRECTORY_SEPARATOR . 'store_image'. DIRECTORY_SEPARATOR;
        $document = 'image' . '.' . $file_ext;

        $path = $request->file('file')->move($destination_path,$file_name);

        // dd($path);

         DB::table('oc_file')->insert([
            'path' => $path
        ]);

        return response()->json( [
            'path' => env('APP_URL').'/'.$path,
            'status' => 'success',
            'success' => true
        ], 201);
    }

    public function uploadVideo(Request $request, $unique_id){

        $extension = $request->file('video')->getClientOriginalExtension();
        $file_ext = $request->file('video')->getClientOriginalName();
        $files_name = pathinfo($file_ext, PATHINFO_FILENAME);
        $file_name = $files_name . '-' . uniqid() . '.' . $extension;
        $file_name = str_replace(' ','_',$file_name);
        $destination_path = 'upload'. DIRECTORY_SEPARATOR . 'vod'. DIRECTORY_SEPARATOR;
        $document = 'vod' . '.' . $file_ext;

        $path = $request->file('video')->move($destination_path,$file_name);

        $getID3 = new \getID3;
        $video_file = $getID3->analyze($path);
        $duration_seconds = $video_file['playtime_string'];

        $duration_limit = '1:00';

        if($duration_seconds > $duration_limit){
            return response()->json(['error' => 'video duration too long',
                                     'status' => 'failed'], 403);
        }

         DB::table('oc_file')->insert([
            'path' => $path
        ]);

            $caption = $request['caption'];

            $vod = new Vod;
            $vod->unique_id = $unique_id;
            $vod->like_count = 0;
            $vod->status = '';
            $vod->caption = $caption;
            $vod->path = env('APP_URL').'/'.$path;
            $vod->save();

        return response()->json( [
            'result' => 'vod uploaded',
            'status' => 'success',
            'success' => true
        ], 201);
    }

}
