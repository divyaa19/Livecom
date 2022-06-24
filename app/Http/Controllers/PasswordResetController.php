<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Oc_customer;
use DB;
use Hash;
class PasswordResetController extends Controller
{
    public function PasswordReset(Request $request)
      {
          $this->validate($request,[
              'email' => 'required|email|exists:oc_customer',
              'password' => 'required|string|min:8|regex:/[A-Z]/|regex:/[0-9]/|confirmed',
              'password_confirmation' => 'required'
          ]);
  
          $updatePassword = DB::table('password_resets')
                              ->where([
                                'email' => $request->email, 
                                'token' => $request->token
                              ])
                              ->first();
  
          if(!$updatePassword){
              return response()->json('Invalid token!');
          }
  
          $user = Oc_customer::where('email', $request->email)
                      ->update(['password' => app('hash')->make($request->password)]);
 
          DB::table('password_resets')->where(['email'=> $request->email])->delete();
  
          return response()->json('Your password has been changed!');
      }
}