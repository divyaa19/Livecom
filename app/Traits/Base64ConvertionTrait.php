<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait Base64ConvertionTrait
{

     public function base64Convertion($base64,$path){

     	 if($base64){

            $img = $base64;
            $folderPath = $path; //path location
            
            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $uniqid = uniqid();
            $file = $folderPath . $uniqid . '.'.$image_type;
            file_put_contents($file, $image_base64);
            return $file;

        }
        return '';
     }

}