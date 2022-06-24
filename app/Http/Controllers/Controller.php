<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
  
class Controller extends BaseController
{
    protected function respondWithToken($token)
    {

        // $newToken = auth()->refresh();
        // $newToken = auth()->refresh(true, true);

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'status' => 'success'
        ], 200);
    }


}