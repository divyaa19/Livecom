<?php

namespace App\Http\Controllers;

use App\Libraries\OnewaySms;
use App\Models\Customer;
use App\Models\Oc_customer;
use App\Models\OtpHistory;
use App\Models\OtpTables;
use App\Models\PurpleTreeStore;
use Carbon\Carbon;
use DateTime;
use DB;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\DynamicLink\GetStatisticsForDynamicLink\FailedToGetStatisticsForDynamicLink;
use Laravel\Socialite\Facades\Socialite;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

use App\Traits\SendinBlue;

class AuthController extends Controller
{
    use SendinBlue;

    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'request_otp',
                'register',
                'redirectToFacebook',
                'handleFacebookCallback',
                'redirectToGoogle',
                'handleGoogleCallback',
                'redirectToApple',
                'handleAppleCallback',
                'refRegister'
            ]
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * Store OTP.
     *
     * @param Request $request
     * @return Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function request_otp(Request $request)
    {
        $this->validate($request, [
            'telephone_countrycode' => 'requiredIf:type,new_user|string',
            'telephone' => 'requiredIf:type,new_user|string|min:9',
            'type' => 'required|in:new_user,withdrawal,transfer,update_password,forgot_password'
        ], [
            'type.in' => 'type `new_user,withdrawal,transfer,update_password,forgot_password` are accepted',
            'user_id' => 'required_if:type,new_user',
            'seller_unique_id'=> 'required_if:type,withdrawal'
        ]);
        //Generate LiveCom OTP message and send it
        $otp = mt_rand(1000, 9999);

        $userData = Customer::where('user_id', $request['user_id'])->first();

        $sellerData = DB::table('oc_purpletree_vendor_stores')
                            ->select('oc_purpletree_vendor_stores.*','oc_customer.*')
                            ->join('oc_customer','oc_customer.customer_id','oc_purpletree_vendor_stores.seller_id')
                            ->where('seller_unique_id',$request['seller_unique_id'])
                            ->first();

        $forgot_password = Customer::where('email',$request['email'])->first();

        if(!$forgot_password){
            return response()->json(['message' => 'users not found',
                                     'status' => 'failed'], 404);
        }

        // dd($sellerData->telephone_countrycode);

        if ($request['type'] == 'new_user') {
            $mobile = $request['telephone_countrycode'] . $request['telephone'];
        } elseif($request['type'] == 'withdrawal') {
            $mobile = $sellerData->telephone_countrycode . $sellerData->telephone;
        }elseif($request['type'] == 'forgot_password') {
            $mobile = $request['telephone_countrycode'] . $request['telephone'];
        }else{
            $mobile = $userData['telephone_countrycode'] . $userData['telephone'];
        }

        // dd($request);
        if($mobile != null)
        {
            $message = 'Your LiveCom One-Time-Password is ' . $otp;
            OnewaySms::send($mobile, $message);
            $session_id = Session::getId();


            $request_otp = new OtpTables;
            $request_otp->otp = $otp;
            $request_otp->phone_number = $request['telephone'];
            $request_otp->country_code = $request['telephone_countrycode'];
            $request_otp->session_id = Session::getId();
            $request_otp->type = $request->type;
            $request_otp->status = 'success';
            // dd($request_otp);
            $request_otp->save();

            $otp_history = new OtpHistory;
            $otp_history->otp = $otp;
            $otp_history->phone_number = $request['telephone'];
            $otp_history->country_code = $request['telephone_countrycode'];
            $otp_history->session_id = Session::getId();
            $otp_history->type = $request['type'];
            $otp_history->status = 'success';
            // dd($otp_history);
            $otp_history->save();
        }

        if($mobile == null && $request['type'] == 'forgot_password')
        {
            $request_otp = new OtpTables;
            $request_otp->otp = $otp;
            $request_otp->email = $request['email'];
            $request_otp->session_id = Session::getId();
            $request_otp->type = $request->type;
            $request_otp->status = 'success';
            $request_otp->save();

            $otp_history = new OtpHistory;
            $otp_history->otp = $otp;
            $otp_history->email = $request['email'];
            $otp_history->session_id = Session::getId();
            $otp_history->type = $request['type'];
            $otp_history->status = 'success';
            $otp_history->save();

            $email_obj = (object)['email'=> $request['email']];
            $obj = (object) array();
            $obj->email = $request['email'];
            $obj->to = [$email_obj];
            $obj->templateId = 1;
            $obj->params = (object)['otp' => $otp,
                                    'username' => $forgot_password->email
                                   ];
        // dd($obj);     
        


            // create contract list in sendinblue
            $url = 'https://api.sendinblue.com/v3/smtp/email';
            $this->sendEmail($url,$obj);

            $session_id = Session::getId();

        }

        if($request['type'] != "withdrawal" && $request['type'] != "forgot_password"){
            $request_otp = new OtpTables;
            $request_otp->otp = $otp;
            $request_otp->phone_number = $request['type'] == 'new_user' ? $request['telephone'] : $userData['telephone'];
            $request_otp->country_code = $request['type'] == 'new_user' ? $request['telephone_countrycode'] : $userData['telephone_countrycode'];
            $request_otp->session_id = Session::getId();
            $request_otp->type = $request->type;
            $request_otp->status = 'success';
            // dd($request_otp);
            $request_otp->save();

            $otp_history = new OtpHistory;
            $otp_history->otp = $otp;
            $otp_history->phone_number = $request['type'] == 'new_user' ? $request['telephone'] : $userData['telephone'];
            $otp_history->country_code = $request['type'] == 'new_user' ? $request['telephone_countrycode'] : $userData['telephone_countrycode'];
            $otp_history->session_id = Session::getId();
            $otp_history->type = $request['type'];
            $otp_history->status = 'success';
            // dd($otp_history);
            $otp_history->save();
        }

        if($request['type'] == "withdrawal"){
            $request_otp = new OtpTables;
            $request_otp->otp = $otp;
            $request_otp->phone_number = $sellerData->telephone;
            $request_otp->country_code = $sellerData->telephone_countrycode;
            $request_otp->session_id = Session::getId();
            $request_otp->type = $request->type;
            $request_otp->status = 'success';
            // dd($request_otp);
            $request_otp->save();

            $otp_history = new OtpHistory;
            $otp_history->otp = $otp;
            $otp_history->phone_number = $sellerData->telephone;
            $otp_history->country_code = $sellerData->telephone_countrycode;
            $otp_history->session_id = Session::getId();
            $otp_history->type = $request['type'];
            $otp_history->status = 'success';
            // dd($otp_history);
            $otp_history->save();
        }

            return response()->json([
                'entity' => 'users',
                'action' => $request['type'],
                'status' => 'success',
                'session_id' => $session_id
            ], 201);
      /*  } catch (\Exception $e) {
            // dd($e);
            return response()->json([
                'entity' => 'users',
                'action' => $request['type'],
                'status' => 'failed'
            ], 409);
        }*/
    }

    /**
     * Store a new user.
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $dynamicLinks = app('firebase.dynamic_links');

        $referred_by = $request['referred_by'];

        $this->validate(
            $request,
            [
                'otp' => 'required',
                'session_id' => 'required',
                'username' => 'required|string|unique:oc_customer',
                'email' => 'required|string|unique:oc_customer',
                'telephone_countrycode' => 'required|string',
                'telephone' => 'required|string|min:9',
                'password' => 'required|string|min:8|regex:/[A-Z]/|regex:/[0-9]/|confirmed',
                't&C' => 'required'
            ]
        );

        $otpResult = OtpTables::where('otp', $request->otp)
            ->where('status', 'success')
            ->where('type', 'new_user')
            ->where('session_id', $request->session_id)
            ->first();


        // $session = Session::get();

        $user_id = IdGenerator::generate(
            [
                'table' => 'oc_customer',
                'field' => 'user_id',
                'length' => 7,
                'prefix' => 'CS_'
            ]
        );

        //if(Session::get('user.otp') == $request->otp){


        // }

        // else{

        if (!$otpResult) {
            return response()->json([
                'result' => 'Session_id failed to validate with OTP Number'
            ], 409);
        }

        if (!isset($request['firstname'])) {
            $request['firstname'] = '';
        }

        if (!isset($request['lastname'])) {
            $request['lastname'] = '';
        }

        if (!isset($request['nickname'])) {
            $request['nickname'] = '';
        }

        if (!isset($request['facebook_id'])) {
            $request['facebook_id'] = null;
        }
        if (!isset($request['google_id'])) {
            $request['google_id'] = null;
        }

        if (!isset($request['fax'])) {
            $request['fax'] = '';
        }

        if (!isset($request['salt'])) {
            $request['salt'] = "";
        }

        if (!isset($request['contact_name'])) {
            $request['contact_name'] = "";
        }

        if (!isset($request['identification_no'])) {
            $request['identification_no'] = "";
        }

        if (!isset($request['state'])) {
            $request['state'] = "";
        }

        if (!isset($request['city'])) {
            $request['city'] = "";
        }

        if (!isset($request['postcode'])) {
            $request['postcode'] = "";
        }

        if (!isset($request['address_line_1'])) {
            $request['address_line_1'] = "";
        }

        if (!isset($request['id'])) {
            $request['id'] = "";
        }

        if (!isset($request['store_type'])) {
            $request['store_type'] = "";
        }

        if (!isset($request['store_name'])) {
            $request['store_name'] = "";
        }

        if (!isset($request['email_verified'])) {
            $request['email_verified'] = 0;
        }

        if (!isset($request['token'])) {
            $request['token'] = "";
        }

        if (!isset($request['safe'])) {
            $request['safe'] = "";
        }

        if (!isset($request['code'])) {
            $request['code'] = "";
        }

        if (!isset($request['referred_by'])) {
            $request['referred_by'] = 0;
        }

        $url = env('BASE_URL').'/api/v1/register';

        $link = $dynamicLinks->createDynamicLink($url);

        $r = $link;

        $r = explode('/', $r);
        $r = array_filter($r);
        $r = array_merge($r, array());
        $r = preg_replace('/\?.*/', '', $r);

        $refer_id = $r[2];

        $oc_customer = new Oc_customer;
        $dt = new DateTime;
        // $oc_customer->contact_name = $request->input('contact_name');
        // $oc_customer->identification_no = $request->input('identification_no');
        // $oc_customer->state = $request->input('state');
        // $oc_customer->city = $request->input('city');
        // $oc_customer->postcode = $request->input('postcode');
        // $oc_customer->address_line_1 = $request->input('address_line_1');
        // $oc_customer->id = $request->input('id');
        // $oc_customer->store_type = $request->input('store_type');
        // $oc_customer->store_name = $request->input('store_name');
        $oc_customer->customer_group_id = '1';
        $oc_customer->language_id = '1';
        $oc_customer->status = '1';
        $oc_customer->email_verified = $request->input('email_verified');
        $oc_customer->token = $request['token'];
        $oc_customer->safe = $request['safe'];
        $oc_customer->code = $request['code'];
        $oc_customer->firstname = $request['firstname'];
        $oc_customer->lastname = $request['lastname'];
        $oc_customer->nickname = $request['nickname'];
        $oc_customer->username = $request['username'];
        $oc_customer->fax = $request['fax'];
        $oc_customer->salt = $request['salt'];
        $oc_customer->date_added = $dt->format('Y-m-d H:i:s');
        $oc_customer->custom_field = '';
        $oc_customer->safe = '0';

        $oc_customer->email = $request['email'];
        $oc_customer->telephone_countrycode = $request['telephone_countrycode'];
        $oc_customer->telephone = $request['telephone'];
        $oc_customer->password = app('hash')->make($request['password']);
        $oc_customer->ip = $request->ip();
        $oc_customer->ref_link = $link;
        $oc_customer->referred_by = $request['referred_by'];
        $oc_customer->user_id = $user_id;
        $oc_customer->refer_id = $refer_id;
        $oc_customer->save();

        $obj = (object) array();
        $obj->email = $request['email'];
        $obj->FIRSTNAME = $request['firstname'];
        $obj->LASTNAME = $request['lastname'];
        $obj->SMS = '';
        $obj->listIds = [2];

 
        // create contract list in sendinblue
        $url = 'https://api.sendinblue.com/v3/contacts';
        $this->sendEmail($url,$obj);

        //Send Notification to Member
        $tomorrow = new DateTime('tomorrow');

            DB::table('oc_notifications')->insert([
                'customer_id' => 0,
                'from_customer_id' => 0,
                'type' => 'alerts',
                'notification_title' => "Key in your bank account" ,
                'notification_message' => "Key in your bank account" ,
                'notification_action' => 'alert member',
                'notification_interaction' => "Key in your bank account" ,
                'notification_is_read' => 0,
                'notification_datetime' => Carbon::now(),
                'notification_expire_datetime' => $tomorrow,
                'unique_id' => $user_id,
                'from_unique_id' => 0
            ]);

        return response()->json([
            'entity' => 'new_user',
            'action' => 'register',
            'status' => 'success'
        ], 201);
    }


    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return Response
     */






    public function login(Request $request)
    {
        //validate incoming request

    //    dd($request);
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);
        // dd(Auth::attempt($credentials));
        if (!$token = Auth::attempt($credentials)) {
            // dd($token);
            return response()->json([
                'message' => 'Invaild Username/Password',
                'status' => 'failed'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Get user details.
     *
     * @param Request $request
     * @return Response
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return Response
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
        // return Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return JsonResponse
     */
    public function handleFacebookCallback()
    {
        $FacebookUser = Socialite::driver('facebook')->stateless()->user();
        $user = Oc_customer::query()->firstOrNew(['email' => $FacebookUser->getEmail()]);

        if (!$user->exists) {
            $user->email = $FacebookUser->getEmail();
            $user->fb_id = $FacebookUser->getID();
            $user = $user->save();
        }

        $token = JWTAuth::fromUser($user);

        return new JsonResponse([
            'token' => $token,
            'provider' => 'Facebook',
            'status' => 'success'
        ]);
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return Response
     */

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
        // return Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return JsonResponse
     */

    public function handleGoogleCallback()
    {
        $GoogleUser = Socialite::driver('google')->stateless()->user();
        $user = Oc_customer::query()->firstOrNew(['email' => $GoogleUser->getEmail()]);

        if (!$user->exists) {
            $user->email = $GoogleUser->getEmail();
            $user->google_id = $GoogleUser->getID();
            $user = $user->save();
        }

        $token = JWTAuth::fromUser($user);

        return new JsonResponse([
            'token' => $token,
            'provider' => 'Google',
            'status' => 'success'
        ]);
    }

    /**
     * Redirect the user to the Apple authentication page.
     *
     * @return Response
     */

    public function redirectToApple()
    {
        return Socialite::driver('apple')->stateless()->redirect();
        // return Socialite::driver('apple')->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Obtain the user information from Apple.
     *
     * @return JsonResponse
     */
    public function handleAppleCallback()
    {
        $AppleUser = Socialite::driver('apple')->stateless()->user();
        $user = Oc_customer::query()->firstOrNew(['email' => $AppleUser->getEmail()]);

        if (!$user->exists) {
            $user->email = $AppleUser->getEmail();
            $user = $user->save();
        }

        $token = JWTAuth::fromUser($user);

        return new JsonResponse([
            'token' => $token,
            'provider' => 'Apple',
            'status' => 'success'
        ]);
    }

    /**
     * Verify OTP.
     *
     * @param Request $request
     * @return Response
     */
    public function otpverify(Request $request)
    {
        $this->validate($request, [
            'otp_number' => 'required|min:4|'
        ]);

        try {
            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'status' => 'success'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'entity' => 'users',
                'action' => 'create',
                'status' => 'failed'
            ], 409);
        }
    }

    public function detectClicks()
    {
        $dynamicLinks = app('firebase.dynamic_links');

        try {
            $stats = $dynamicLinks->getStatistics('https://livecomgetreferral.page.link/member-referral');
        } catch (FailedToGetStatisticsForDynamicLink $e) {
            echo $e->getMessage();
            exit;
        }

        // $stats = $dynamicLinks->getStatistics('https://livecomgetreferral.page.link/member-referral');

        // $eventStats = $stats->eventStatistics();

        // $allClicks = $eventStats->clicks();
    }


}

// 53ogFAyiLSnSeoEYXzkbWowvryC2pr63ehSt2MGT
