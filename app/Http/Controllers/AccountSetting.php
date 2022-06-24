<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Libraries\OnewaySms;
use App\Models\OtpTables;
use App\Models\OtpHistory;
use App\Models\Address;
use App\Models\Bank;
use App\Models\City;
use App\Models\Customer;
use App\Models\States;
use Exception;

use Illuminate\Support\Facades\Session;

class AccountSetting extends Controller
{
//     public function updatepassword(Request $request)
//       {

//         $user = auth()->user();
//         $customer_id = $user->customer_id;


//           $telephone=DB::table('oc_customer')->where('customer_id','=',$customer_id)->get('telephone');
          
//           $password=DB::table('oc_customer')->where('customer_id','=',$customer_id)->get('password');

//           $telephone_code=DB::table('oc_customer')->where('customer_id','=',$customer_id)->get('telephone_countrycode');
//           foreach($telephone as $data){
//             $phonenumber=$data->telephone;
//         }
//         foreach($telephone_code as $data1){
//             $country_code=$data1->telephone_countrycode;
//         }
//             $this->validate($request, [
//             'current-password' => 'required',
   
   
//             ]);
       
//         if (!(Hash::check($request->get('current-password'), $password))) {
//             $otp = mt_rand(1000,9999);
//             $mobile = $telephone_code. $telephone;
//             $message = 'Your LiveCom One-Time-Password is ' . $otp;
//             $result = OnewaySms::send($mobile, $message);
//             // dd($otp);
            
//             $s='Sucess';

//             try{
                
//                 if( $user = DB::table('otp_tables')
//                             ->where('type', '=', 'Existing User')
//                             ->where('status', '=', 'Success')

//                             ->where('session_id', '=', Session::get('user.session_id'))
//                             ->where('phone_number', '=', Session::get('user.telephone'))
//                             ->get()==null){
//                          $request_otp = new OtpTables;

//                          $request_otp->otp = $otp;
//                          $request_otp->phone_number = $phonenumber;
//                          $request_otp->country_code = $country_code;
//                          $request_otp->session_id = Session::getId();
//                          $request_otp->type = 'Existing User';
//                         $request_otp->status = 'Success';
   
//                         $request_otp->save();

//                 }else{
//         $user = DB::table('otp_tables')
//         ->where('type', '=', 'Existing User')
//                                 ->where('status', '=', 'Success')

//                                 ->where('session_id', '=', Session::get('user.session_id'))
//                                 ->where('phone_number', '=', Session::get('user.telephone'))->update(['otp'=>$otp]);

// }
              
//                 $otp_history = new OtpHistory;
//                 $otp_history->otp = $otp;
//                 $otp_history->phone_number = $telephone;
//                 $otp_history->country_code = $telephone_code;
//                 $otp_history->session_id = Session::getId();
//                 $otp_history->type = 'Old User';
//                 $otp_history->status = 'Success';
                
//                 $otp_history->save();

//                 return response()->json( [
//                     'entity' => 'otp', 
//                     'action' => 'register', 
//                     'status' => 'success'
//         ], 201);

//             }
            
//                 catch (\Exception $e) 
//                     {
//                         // dd($e);
//                         return response()->json( [
//                                'error'=>$e,
//                                 'status' => 'failed'
//                         ], 409);
//                     }
           

//         }else{
//           return response()->json('password didnt match ');
//         }
//       }


      public function comparepassword(Request $request)
      {

        $user = auth()->user();
        $customer_id = $user->customer_id;

        
        $password = DB::table('oc_customer')->where('customer_id','=',$customer_id)->get('password');

        $this->validate($request, [
        'current-password' => 'required',
        ]);

        if (!(Hash::check($request->get('current-password'), $password))) 
        {

            return response()->json(['message' => 'Password match',
            'status'=>'success'
            ], 401);

        }    
    
          return response()->json(['message' => 'Password not match',
          'status'=>'failed'
          ], 401);
      }

      // public function password_request_otp(Request $request)
      // {
      //   $user = auth()->user();
      //   $customer_id = $user->customer_id;

      //       $otpResult = OtpTables::
      //                           where('type', '=', 'Existing User')
      //                           ->where('status', '=', 'Success')

      //                           ->where('session_id', '=', Session::get('user.session_id'))
      //                           ->where('phone_number', '=', Session::get('user.telephone'))
      //                           ->first('otp');
                                
      //                           $otpResults = $request->input('otp');
                                
      //   if($otpResults==$otpResult->otp){
      //       $this->validate($request, [
               
      //           'new-password' => 'required|string|min:8',
      //           'password_confirmation' => 'required_with:new-password|same:new-password|min:8'
               
      //       ]);
      //       $user = DB::table('oc_customer')->where('customer_id','=',$customer_id)->update([
      //           'password'=>bcrypt($request->get('new-password'))
      //       ]);
      //     return response()->json('Your password has been changed!');

      //   }else{
      //     return response()->json('something went worng');
      //   }

      // }

      public function checkCurrentPassword(Request $request){

        $user = auth()->user();
        $user_unique_id = $user->user_id;
        

        $this->validate($request,[
          'current_password' => 'required',
          ]);

          // dd($request);

        $user_password = DB::table('oc_customer')
                            ->where('user_id',$user_unique_id)
                            ->first();

        $password = $user_password->password;

        $current_password = $request['current_password'];
        
        if(Hash::check($current_password, $password)){

          return response()->json(['message' => 'password match',
                                   'status' => 'success',
                                   'success' => true], 200);
        }else{
          return response()->json(['message' => 'incorrect password',
                                   'status' => 'failed'], 405);
        }
      }

      public function updatePassword(Request $request){

        $user = auth()->user();
        $user_unique_id = $user->user_id;
        
        $this->validate($request, [
          'new_password' => 'required|string|min:8|regex:/[A-Z]/|regex:/[0-9]/|confirmed',
          ]);

          $otpResult = OtpTables::where('otp', $request->otp)
                ->where('status', 'success')
                ->where('type', 'update_password')
                ->where('session_id', $request->session_id)
                ->first();

            if (!$otpResult) {
                return response()->json([
                    'result' => 'Session_id failed to validate with OTP Number'
                ], 409);
            }

            $password = app('hash')->make($request['new_password']);

            // dd($password);

            $customer = DB::table('oc_customer')
                            ->where('user_id',$user_unique_id)
                            ->update(['password' => $password]);

            return response()->json(['message' => 'password updated',
                                     'status' => 'success',
                                     'success' => true], 200);
      }

      public function deliveryaddress(Request $request)
      {
        $user = auth()->user();
        $customer_id = $user->user_id;

        $updateaddress = new Address;

        $req_state = $request->input('state');
        $req_city = $request->input('city');

        $state = States::where('id', $req_state)->first();

        $city = City::where('state_id', $req_state)->where('id', $req_city)->first();

           $updateaddress-> customer_id = $customer_id;
           $updateaddress-> firstname = '';
           $updateaddress-> lastname ='';
           $updateaddress-> contact_name = $request->input('contact_name');
           $updateaddress-> company = '';
           $updateaddress-> address_1 = $request->input('address_1');
           $updateaddress-> address_2 = $request->input('address_2');
           $updateaddress-> city = $city->city;
           $updateaddress-> postcode = $request->input('postcode');
           $updateaddress-> state = $state->states;
           $updateaddress-> country_id = $request->input('country_id');
           $updateaddress-> zone_id = 0;
           $updateaddress-> add_telephone = $request->input('add_telephone');
           $updateaddress-> custom_field = '';
           $updateaddress-> label = $request->input('label');
           $updateaddress-> default = $request->input('default');
           $updateaddress->save();
     
            return response()->json( [
                        
                        'status' => 'success'
            ], 201);
      }

      public function getdeliveryaddress()
      {
        $user = auth()->user();
        $customer_id = $user->user_id;
        $getaddress=DB::table('oc_address')
                    ->select('contact_name','add_telephone','state','city','postcode','address_1','address_2','label','default')
                    ->where('customer_id','=',$customer_id)->get();

        return response()->json(['address'=> $getaddress,
                                 'status' => 'success',
                                 'success' => true],200);

      }
      public function deletedeliveryaddress($address_id, $user_id)
      {
        $getaddress=DB::table('oc_address')->where('address_id','=',$address_id)->where('customer_id','=',$user_id)->delete();

        return response()->json(['message'=> 'address deleted',
                                 'status' => 'success',
                                 'success' => true],200);

      }
      public function updatedeliveryaddress(Request $request, $address_id)
      {
        $user = auth()->user();
        $customer_id = $user->user_id;

        $req_state = $request->input('state');
        $req_city = $request->input('city');

        $state = States::where('id', $req_state)->first();

        $city = City::where('state_id', $req_state)->where('id', $req_city)->first();

        $updateaddress=DB::table('oc_address')->where('address_id','=',$address_id)->where('customer_id','=',$customer_id)->update([
            'contact_name'=>$request->input('contact_name'),
            'address_1'=>$request->input('address_1'),
            'address_2'=>$request->input('address_2'),
            'city'=> $city->city,
            'postcode'=>$request->input('postcode'),
            'state'=> $state->states,
            'add_telephone'=>$request->input('add_telephone'),
            'label'=>$request->input('label'),
            'default'=>$request->input('default'),
        ]);

            return response()->json([
                        'address' => $updateaddress,
                        'status' => 'success'
            ], 201);

      }

      public function createbankaccount(Request $request)
      {
        $user = auth()->user();
        $customer_id = $user->customer_id;
        // $customer_id=999;

        $telephone=DB::table('oc_customer')->where('customer_id','=',$customer_id)->get('telephone');  
        // $password=DB::table('oc_customer')->where('customer_id','=',$customer_id)->get('password');

        $telephone_code=DB::table('oc_customer')->where('customer_id','=',$customer_id)->get('telephone_countrycode');
        foreach($telephone as $data){
          $phonenumber=$data->telephone;
      }
      foreach($telephone_code as $data1){
          $country_code=$data1->telephone_countrycode;
      }

        $this->validate($request, [
            'bank_account_name' => 'required',
            'bank_account_number' => 'required|min:7',
            'bank_name' => 'required',
            
        ]);

            $otp = mt_rand(1000,9999);
            $mobile = $country_code.$phonenumber;
            $message = 'Your LiveCom One-Time-Password is ' . $otp;
            $result = OnewaySms::send($mobile, $message);
            Session::put('add_bank', [
                'bank_account_number' => $request->input('bank_account_number'),
                'bank_account_name'   => $request->input('bank_account_name'),
                'bank_name'           => $request->input('bank_name'),
                'otp'                 => $otp,
                'telephone'           => $phonenumber,
                'session_id'          => Session::getId()
            ]);
            try{
                $request_otp = new OtpTables;
                $request_otp->otp = $otp;
                $request_otp->phone_number = $phonenumber;
                $request_otp->country_code = $country_code;
                $request_otp->session_id = Session::getId();
                $request_otp->type = 'New BankAccount';
                $request_otp->status = 'Success';
                // dd($request_otp);
                $request_otp->save();
    
                $otp_history = new OtpHistory;
                $otp_history->otp = $otp;
                $otp_history->phone_number = $phonenumber;
                $otp_history->country_code = $country_code;
                $otp_history->session_id = Session::getId();
                $otp_history->type = 'New BankAccount';
                $otp_history->status = 'Success';
                // dd($otp_history);
                $otp_history->save();

                return response()->json( [
                    'entity' => 'Withdraw', 
                    'action' => 'create', 
                    'status' => 'success'
                ], 201);

            }catch(Exception $e){

                return response()->json([
                    'error'=>$e
                ],400);

            }


            // if($otp==$request->input('otp')){
            //     $addbank->save();
            //     return response()->json( [
                    
            //         'status' => 'success'
            //     ], 201);


            // }else{
            //     return response()->json([
            //         'error'=>'Otp Did not match'
            //     ],400);

            // }
      }
      public function otp_to_createbankaccount(Request $request)
      {

        $user = auth()->user();
        $customer_id = $user->customer_id;

        if(Session::get('add_bank.otp') == $request->input('otp')){

            
            $addbank=new Bank;

            $addbank->customer_id=$customer_id;
            $addbank->bank_name=Session::get('add_bank.bank_name');
            $addbank->bank_account_number=Session::get('add_bank.bank_account_number');
            $addbank->bank_account_name=Session::get('add_bank.bank_account_name');


            $addbank->save();
            return response()->json( [
                
                'status' => 'success'
            ], 201);
        }else{
            return response()->json([
                'error'=>'failed'
            ],400);

        }
      }

      public function getbankaccount()
      {
        $user = auth()->user();
        $customer_id = $user->customer_id;
        // $customer_id=999;

        $bankdetails=Bank::where('customer_id','=',$customer_id)->get();
        return response()->json( [
            
                
            'status' => 'success',
            'details'=>$bankdetails
        ], 201);
      }

      public function editbankaccount(Request $request,$account_number)
      {
        $user = auth()->user();
        $customer_id = $user->customer_id;
       

        $this->validate($request, [
            'bank_account_name' => 'required',
            'bank_account_number' => 'required|min:7',
            'bank_name' => 'required',
            
        ]);

        $bankdetails=Bank::where('bank_account_number','=',$account_number)->where('customer_id','=',$customer_id)->update([

            'bank_account_name' => $request->input('bank_account_name'),
            'bank_account_number' => $request->input('bank_account_number'),
            'bank_name' => $request->input('bank_name'),


        ]);
        return response()->json( [
            
                
            'status' => 'success',
            'details'=>$bankdetails
        ], 201);
      }

      public function deletebankaccount($account_number)
      {
        $user = auth()->user();
        $customer_id = $user->customer_id;
        // $customer_id=999;

        $bankdetails=Bank::where('bank_account_number','=',$account_number)->where('customer_id','=',$customer_id)->delete();
        return response()->json( [
            
                
            'status' => 'success',
            'details'=>$bankdetails
        ], 201);
      }
      
}