<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\OnewaySms;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SmsController extends Controller
{
    public function otp_generate(Request $request)
    {
        $otp = mt_rand(1000,9999);
        return $otp;
    }

    public function send_sms(Request $request)
    {
    
        $otp = mt_rand(1000,9999);
        $mobile = $request->input('phone_number');
        $message = 'Your LiveCom One-Time-Password is ' .  $otp;
        $debug = false;

        $result = OnewaySms::send($mobile, $message, $debug);

        return $result;
    }

    public function validateotp(Request $request){
        
        $this->validate($request,[
            'otp' => 'required|in:'.$otp
        ]);
    }
}