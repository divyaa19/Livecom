<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Models\OtpTables;
use App\Models\OtpHistory;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Oc_customer;
use App\Libraries\OnewaySms;
use App\Models\Store;
use DateTime;

class TransferController extends Controller
{

    public function getFollow(Request $request){

    $user = auth()->user();
    $user_id = $user->user_id;

    // Get Current User following who
    $user_following = DB::table('oc_customer')
                        ->select(DB::raw("CONCAT(oc_customer.firstname, ' ', oc_customer.lastname) as name"),'oc_customer.user_id')
                        ->join('oc_follow','oc_follow.unique_id','=','oc_customer.user_id')
                        ->where('oc_follow.by_unique_id',$user_id)
                        ->get();

    //Get Current User followers
    $user_followers = DB::table('oc_customer')
                        ->select(DB::raw("CONCAT(oc_customer.firstname, ' ', oc_customer.lastname) as name"),'oc_customer.user_id')
                        ->join('oc_follow','oc_follow.by_unique_id','=','oc_customer.user_id')
                        ->where('oc_follow.unique_id',$user_id)
                        ->get();
                        

    $stores = DB::table('oc_purpletree_vendor_stores')
                    ->select(DB::raw("CONCAT(oc_purpletree_vendor_stores.firstname, ' ', oc_purpletree_vendor_stores.lastname) as name"),'oc_purpletree_vendor_stores.id','oc_purpletree_vendor_stores.seller_type','oc_purpletree_vendor_stores.companyname')
                    ->get();

    $data = array(
        'following' => $user_following,
        'followers' => $user_followers,
        'stores' => $stores
    );

    return response()->json($data);

    }

    public function insert_transfer(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required',
            'recipient' => 'required',
            'otp' => 'required',
            'session_id' => 'required'
        ]);

        //Get current user ID
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $amount = $request->get('amount');

        //Get Current user Wallet ID with current authenticated customer ID
        $from_wallet = DB::table('oc_wallet')
                        ->select('wallet_id')
                        ->leftJoin('oc_customer','oc_customer.customer_id','=','oc_wallet.customer_id')
                        ->where('oc_customer.customer_id', $customer_id)
                        ->first();

        $walletID = $from_wallet->wallet_id;

        //Get Current user wallet Unlocked token
        $unlock_token = DB::table('oc_wallet')
                        ->select('balance')
                        ->where('wallet_id', $walletID)
                        ->first();

        $unlocked_LT = $unlock_token->balance;

        if($amount > $unlocked_LT)
        {
            return response()->json('Input amount is higher than Unlocked LiveTokens');
        }
        
        $recipient = $request->get('recipient');

        //Recipient Wallet ID
        $recipient_wallet = DB::table('oc_wallet')
                            ->select('wallet_id')
                            ->leftJoin('oc_customer','oc_customer.customer_id','=','oc_wallet.customer_id')
                            ->where('oc_customer.customer_id', $recipient)
                            ->first();

        if($recipient_wallet == NULL)
        {
            return response()->json('Recipient Not Found');
        }

        $recipient_walletID = $recipient_wallet->wallet_id;

        //Get today total transfer
        $today_transfer = DB::table('oc_bid_transaction')
                            ->select(DB::raw("SUM(amount) as totalTransfer"))
                            ->where('trans_type',3)
                            ->where('wallet_id', $walletID)
                            ->whereDate('date_added', Carbon::today('Asia/Kuala_Lumpur'))
                            ->first();

        $total_transfer = $today_transfer->totalTransfer;

        $totalSum = $amount + $total_transfer;

        if($totalSum <= 1000)
        {

            $otpResult = OtpTables::where('otp', $request->otp)
                ->where('status', 'success')
                ->where('type','transfer')
                ->where('session_id', $request->session_id)
                ->first();

            if(!$otpResult)
            {
                return response()->json( [
                    'result' => 'Session_id failed to validate with OTP Number'
                ], 409);
            }
        

            $wallet = DB::table('oc_wallet')
                            ->select('wallet_id')
                            ->leftJoin('oc_customer','oc_customer.customer_id','=','oc_wallet.customer_id')
                            ->where('oc_customer.customer_id', $customer_id)
                            ->first();

            $from_wallet = $wallet->wallet_id;
            $to_wallet = $recipient_walletID;

            // dd($to_wallet);

            $wallet_balance = DB::table('oc_wallet')
                                ->select('balance')
                                ->where('wallet_id', $from_wallet)
                                ->first();

            $old_balance = $wallet_balance->balance;
            $new_balance = $old_balance - $amount;

                //Recipient
                $recipient_wallet_balance = DB::table('oc_wallet')
                                                ->select('balance')
                                                ->where('wallet_id', $to_wallet)
                                                ->first();

                $recipient_old_balance = $recipient_wallet_balance->balance;
                $recipient_new_balance = $recipient_old_balance + $amount;
                    
                    if(!isset($request['trans_type'])) {
                        $request['trans_type'] = 3;
                    }

                    if(!isset($request['free_token'])) {
                        $request['free_token'] = 0;
                    }

                    if(!isset($request['status'])) {
                        $request['status'] = 3;
                    }

                    if(!isset($request['note'])) {
                        $request['note'] = 'NULL';
                    }

                    $transfer = new Transaction;
                    $dt = new DateTime;
                    $transfer->wallet_id = $from_wallet;
                    $transfer->ref_wallet = $to_wallet;
                    $transfer->amount = $amount;
                    $transfer->trans_type = $request->get('trans_type');
                    $transfer->free_token = $request->get('free_token');
                    $transfer->status = $request->get('status');
                    $transfer->note = $request->get('note');
                    $transfer->trans_direction = 'deb';
                    $transfer->date_added= $dt->format('Y-m-d H:i:s');
                    $transfer->save();

                    $receive = new Transaction;
                    $dt = new DateTime;
                    $receive->wallet_id = $to_wallet;
                    $receive->ref_wallet = $from_wallet;
                    $receive->amount = $amount;
                    $receive->trans_type = $request->get('trans_type');
                    $receive->free_token = $request->get('free_token');
                    $receive->status = $request->get('status');
                    $receive->note = $request->get('note');
                    $receive->trans_direction = 'cre';
                    $receive->date_added= $dt->format('Y-m-d H:i:s');
                    $receive->save();

                    $wallet = Wallet::find($from_wallet);
                    $wallet->balance = $new_balance;
                    $wallet->save();

                    $recipient_wallet = Wallet::find($to_wallet);
                    $recipient_wallet->balance = $recipient_new_balance;
                    $recipient_wallet->save();

                    // dd($withdrawal);

                    return response()->json( [
                                'entity' => 'Bank', 
                                'action' => 'Transfer', 
                                'status' => 'success'
                    ], 201);
        }else{
                return response()->json('You have exceeded your daily transfer limit');
             }
    }

    public function transfer(Request $request)
    {

        // $this->validate($request,
        //     [
        //         'otp' => 'required',
        //         'session_id' => 'required',
        //         'amount' => 'required',
        //         'recipient' => 'required'
        //     ]
        // );

        // $otpResult = OtpTables::where('otp', $request->otp)
        //     ->where('status', 'success')
        //     ->where('session_id', $request->session_id)
        //     ->first();

        // // dd(Session::all());

        // // if(Session::get('transfer.otp') == $request->input('otp'))
        // // {

        // //     $otpResult = OtpTables::where('otp', '=', $request->input('otp'))
        // //                             ->where('status', '=', 'Success')
        // //                             ->where('type', '=', 'Transfer')
        // //                             ->where('session_id', '=', Session::get('transfer.session_id'))
        // //                             ->where('phone_number', '=', Session::get('transfer.telephone'))
        // //                             ->first();
        // // }
        // if(!$otpResult){
        //     return response()->json( [
        //         'result' => 'Session_id failed to validate with OTP Number'
        //     ], 409);
        // }



        // if ($otpResult != null) 
        // {

        //     $user = auth()->user();
        //     $customer_id = $user->customer_id;
            
        //     $wallet = DB::table('oc_wallet')
        //                 ->select('wallet_id')
        //                 ->leftJoin('oc_customer','oc_customer.customer_id','=','oc_wallet.customer_id')
        //                 ->where('oc_customer.customer_id', $customer_id)
        //                 ->first();

        //     $from_wallet = $wallet->wallet_id;
        //     $to_wallet = Session::get('transfer.recipient_walletID');

        //     // dd($to_wallet);

        //     $wallet_balance = DB::table('oc_wallet')
        //                     ->select('balance')
        //                     ->where('wallet_id', $from_wallet)
        //                     ->first();

        //     $old_balance = $wallet_balance->balance;
        //     $new_balance = $old_balance - $amount;

        //     //Recipient
        //     $recipient_wallet_balance = DB::table('oc_wallet')
        //                                 ->select('balance')
        //                                 ->where('wallet_id', $to_wallet)
        //                                 ->first();

        //     $recipient_old_balance = $recipient_wallet_balance->balance;
        //     $recipient_new_balance = $recipient_old_balance + $amount;
            
        //     if(!isset($request['trans_type'])) {
        //         $request['trans_type'] = 3;
        //     }

        //     if(!isset($request['free_token'])) {
        //         $request['free_token'] = 0;
        //     }

        //     if(!isset($request['status'])) {
        //         $request['status'] = 3;
        //     }

        //     if(!isset($request['note'])) {
        //         $request['note'] = 'NULL';
        //     }

        //             $transfer = new Transaction;
        //             $dt = new DateTime;
        //             $transfer->wallet_id = $from_wallet;
        //             $transfer->ref_wallet = $to_wallet;
        //             $transfer->amount = $amount;
        //             $transfer->trans_type = $request->get('trans_type');
        //             $transfer->free_token = $request->get('free_token');
        //             $transfer->status = $request->get('status');
        //             $transfer->note = $request->get('note');
        //             $transfer->trans_direction = 'deb';
        //             $transfer->date_added= $dt->format('Y-m-d H:i:s');
        //             $transfer->save();

        //             $receive = new Transaction;
        //             $dt = new DateTime;
        //             $receive->wallet_id = $to_wallet;
        //             $receive->ref_wallet = $from_wallet;
        //             $receive->amount = $amount;
        //             $receive->trans_type = $request->get('trans_type');
        //             $receive->free_token = $request->get('free_token');
        //             $receive->status = $request->get('status');
        //             $receive->note = $request->get('note');
        //             $receive->trans_direction = 'cre';
        //             $receive->date_added= $dt->format('Y-m-d H:i:s');
        //             $receive->save();

        //             $wallet = Wallet::find($from_wallet);
        //             $wallet->balance = $new_balance;
        //             $wallet->save();

        //             $recipient_wallet = Wallet::find($to_wallet);
        //             $recipient_wallet->balance = $recipient_new_balance;
        //             $recipient_wallet->save();

        //             // dd($withdrawal);

        //             return response()->json( [
        //                         'entity' => 'Bank', 
        //                         'action' => 'Transfer', 
        //                         'status' => 'success'
        //             ], 201);
        // }
    }

    public function getTransferDetails()
    {

        // dd(Session::all());


        $user = auth()->user();
        $customer_id = $user->customer_id;

        $wallet = DB::table('oc_wallet')
                    ->select('wallet_id')
                    ->leftJoin('oc_customer','oc_customer.customer_id','=','oc_wallet.customer_id')
                    ->where('oc_customer.customer_id', $customer_id)
                    ->first();

        $walletID = $wallet->wallet_id;

        // dd($walletID);

        $recipient = Session::get('transfer.recipient_id');

        // dd($recipient);

        //Recipient First & LastName
        $recipient_name = DB::table('oc_customer')
                            ->select('firstname')
                            ->where('oc_customer.customer_id', $recipient)
                            ->get();

        $recipient_name->transform(function($i) {
                return (array)$i;
        });

        $array = $recipient_name->toArray();

        $transferDetails = DB::table('oc_bid_transaction')
                            ->select('date_added','amount','transaction_id')
                            ->where('wallet_id', $walletID)
                            ->orderBy('date_added','desc')
                            ->first();

        $recipient_firstname = $recipient_name->pluck('firstname');

        $data = array(
        'transfer_details' => $transferDetails,
        'recipient_firstname' => $recipient_firstname
        );

        return response()->json($data);

    }

    public function searchFollowing(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->user_id;

        $this->validate($request,[
            'search' => 'required'
        ]);

        $search = '%'.$request->get('search').'%';

        $searchFollowing = DB::table('oc_customer')
                            ->select(DB::raw("CONCAT(oc_customer.firstname, ' ', oc_customer.lastname) as name"),'oc_customer.profile_url')
                            ->join('oc_follow','oc_follow.unique_id','=','oc_customer.user_id')
                            ->where('oc_follow.by_unique_id','=',$user_id)
                            ->where('oc_customer.firstname','like',$search)
                            ->orWhere('oc_customer.lastname','like',$search)
                            ->whereNotNull('user_id')
                            ->where('user_id','!=','')
                            ->where('user_id','!=',$user_id)
                            ->get();

        // dd($searchFollowing);
        
        $searchFollowing->transform(function($i) {
                return (array)$i;
        });

        $array = $searchFollowing->toArray();

        if (!$array){

            return response()->json(['message' => 'Not Found',
                                    'status'=>'failed'
                                    ], 401);
            
    }


        foreach ($searchFollowing as $following){
            $data['following'][]=array(
                'name' => $following['name'],
                'profile picture' => $following['profile_url']
            );
        }
        
        return response()->json($data);
    }

    public function searchStore(Request $request)
    {
        $this->validate($request,[
            'search' => 'required'
        ]);

        $search = '%'.$request->get('search').'%';

        $searchStore  = DB::table('oc_purpletree_vendor_stores')
                        ->select(DB::raw("CONCAT(oc_purpletree_vendor_stores.firstname, ' ', oc_purpletree_vendor_stores.lastname) as name"),'oc_purpletree_vendor_stores.store_image')
                        ->where('oc_purpletree_vendor_stores.firstname', 'LIKE', $search)
                        ->orWhere('oc_purpletree_vendor_stores.lastname','LIKE',$search)
                        ->get();
        
        $searchStore->transform(function($i) {
                return (array)$i;
        });

        $array = $searchStore->toArray();

        if (!$array){

            return response()->json(['message' => 'Not Found',
                                    'status'=>'failed'
                                    ], 401);
            
    }


        foreach ($searchStore as $store){
            $data['stores'][]=array(
                'Name' => $store['name'],
                'Store Image' => $store['store_image']
            );
        }
        
        return response()->json($data);
    }

    public function searchFollowers(Request $request)
    {

        $user = auth()->user();
        $user_id = $user->user_id;
        
        $this->validate($request,[
            'search' => 'required'
        ]);

        $search = '%'.$request->get('search').'%';

        $searchFollowers = DB::table('oc_customer')
                            ->select(DB::raw("CONCAT(oc_customer.firstname, ' ', oc_customer.lastname) as name"),'oc_customer.profile_url','oc_customer.user_id')
                            ->join('oc_follow','oc_follow.by_unique_id','=','oc_customer.user_id')
                            ->where('oc_follow.unique_id','=',$user_id)
                            ->where('oc_customer.firstname','like',$search)
                            ->orWhere('oc_customer.lastname','like',$search)
                            ->whereNotNull('user_id')
                            ->where('user_id','!=','')
                            ->where('user_id','!=',$user_id)
                            ->get();

        // dd($searchFollowers);
        
        $searchFollowers->transform(function($i) {
                return (array)$i;
        });

        $array = $searchFollowers->toArray();

        if (!$array){

            return response()->json(['message' => 'Not Found',
                                    'status'=>'failed'
                                    ], 401);
            
    }


        foreach ($searchFollowers as $follower){
            $data['follower'][]=array(
                'name' => $follower['name'],
                'profile picture' => $follower['profile_url']
            );
        }
        
        return response()->json($data);
    }
}
