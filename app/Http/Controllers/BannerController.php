<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    public function getBanner(){

        $getBanner = Banner::where('banner_type','home')
                            ->get();

        return response()->json(
            [
                'banner' => $getBanner,
                'status' => 'success'
    
            ], 200);
    }
}
