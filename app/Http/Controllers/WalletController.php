<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankList;
use App\Models\Notifications;
use App\Models\OtpTables;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\ShopBalance;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function getWallet()
    {
        //Get current user ID
        $user = auth()->user();
        $customer_id = $user->customer_id;

        //Get Wallet ID with given Customer ID
        $wallet_table = DB::table('oc_wallet')
            ->select('*')
            ->leftJoin('oc_customer', 'oc_customer.customer_id', '=', 'oc_wallet.customer_id')
            ->where('oc_customer.customer_id', $customer_id)
            ->get();

        $walletID = $wallet_table[0]->wallet_id;

        $wallet = Wallet::find($walletID);

        return response()->json($wallet);
    }

    public function getBank()
    {
        $user = auth()->user();
        $id = $user->customer_id;

        $getBank = Bank::where('customer_id', $id)->get();

        if (count($getBank) == 0) {
            return response()->json([
                'result' => 'result not found',
                'status' => 'failed',
            ], 404);
        }

        foreach ($getBank as $userBank) {
            $data['user_bank'][] = array(
                'id' => $userBank['id'],
                'name' => $userBank['bank_account_name'],
                'account_no' => $userBank['bank_account_number'],
                'default' => $userBank['default'],
                'bank_name' => $userBank['bank_name'],
                'bank_id' => $userBank['bank_id']
            );
        }

        return response()->json($data);
    }

    public function addBankDetails(Request $request)
    {
        $this->validate($request, [
            'bank_account_name' => 'required',
            'bank_account_number' => 'required|min:7|unique:oc_customer_affiliate',
            'bank_id' => 'required'
        ]);

        $user = auth()->user();
        $id = $user->customer_id;

        $addBank = new Bank;

        if (!isset($request['company'])) {
            $request['company'] = "";
        }

        if (!isset($request['website'])) {
            $request['website'] = "";
        }

        if (!isset($request['tracking'])) {
            $request['tracking'] = "";
        }

        if (!isset($request['tax'])) {
            $request['tax'] = "";
        }

        if (!isset($request['payment'])) {
            $request['payment'] = "";
        }

        if (!isset($request['cheque'])) {
            $request['cheque'] = "";
        }

        if (!isset($request['paypal'])) {
            $request['paypal'] = "";
        }

        if (!isset($request['bank_branch_number'])) {
            $request['bank_branch_number'] = "";
        }

        if (!isset($request['bank_swift_code'])) {
            $request['bank_swift_code'] = "";
        }

        if (!isset($request['custom_field'])) {
            $request['custom_field'] = "";
        }

        if (!isset($request['status'])) {
            $request['status'] = 0;
        }

        if ($request['default'] == 1) {
            Bank::where('customer_id', $user->customer_id)
                ->update([
                    'default' => 0
                ]);
        }

        $get_bank_id = $request['bank_id'];

        $bank_id = BankList::find($get_bank_id);

        $bank_name = $bank_id->bank_name ?? "Bank";

        $addBank->customer_id = $id;
        $addBank->company = $request->get('company');
        $addBank->website = $request->get('website');
        $addBank->tracking = $request->get('tracking');
        $addBank->tax = $request->get('tax');
        $addBank->payment = $request->get('payment');
        $addBank->cheque = $request->get('cheque');
        $addBank->paypal = $request->get('paypal');
        $addBank->bank_branch_number = $request->get('bank_branch_number');
        $addBank->bank_swift_code = $request->get('bank_swift_code');
        $addBank->custom_field = $request->get('custom_field');
        $addBank->status = $request->get('status');
        $addBank->bank_account_name = $request['bank_account_name'];
        $addBank->bank_account_number = $request['bank_account_number'];
        $addBank->bank_name = $bank_name;
        //$addBank->default_bank = $request->get('default_bank');
        $addBank->date_added = Carbon::now();
        $addBank->default = $request['default'];
        $addBank->bank_id = $request['bank_id'];

        $addBank->save();

        return response()->json(['status' => 'success'], 201);
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateBankDetails(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'bank_account_name' => 'required',
            'bank_account_number' => 'required|min:7',
            'bank_id' => 'required',
            'default' => 'required'
        ]);


        $user = auth()->user();

        if ($request['default'] == 1) {
            Bank::where('customer_id', $user->customer_id)
                ->update([
                    'default' => 0
                ]);
        }

        $updateBank = Bank::where([
            'id' => $id,
            'customer_id' => $user->customer_id
        ])->first();

        $bankname = BankList::find($request['bank_id']);

        $updateBank->bank_account_name = $request['bank_account_name'];
        $updateBank->bank_account_number = $request['bank_account_number'];
        $updateBank->bank_name = $bankname['bank_name'];
        $updateBank->default = $request['default'];
        $updateBank->bank_id = $request['bank_id'];
        $updateBank->save();


        return response()->json(['status' => 'success'], 201);
    }


    public function deleteBankDetails(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $user = auth()->user();
        $id = $user->customer_id;

        $bank = Bank::where(
            [
                'id' => $request['id'],
                'customer_id' => $id
            ]
        )->delete();

        return response()->json([
            'status' => 'success',
            'success' => $bank
        ], 201);
    }

    public function withdrawal(Request $request,$seller_unique_id)
    {
        $this->validate($request, [
            'bank_account_name' => 'required',
            'bank_account_number' => 'required|min:7',
            'bank_id' => 'required',
            'amount' => 'required|numeric|gte:50|lte:1500',
            'otp' => 'required',
            'session_id' => 'required'
        ]);

        //Get current user ID
        $user = auth()->user();
        $user_id = $user->user_id;

        $amount = $request['amount'];
        $bank_account_name = $request['bank_account_name'];
        $bank_account_number = $request['bank_account_number'];

        $now = Carbon::now();

        $available_balance = ShopBalance::where('seller_unique_id',$seller_unique_id)->first();

        if(!$available_balance){
            return response()->json(['message' => 'insufficient balance',
                                     'status' => 'failed'], 405);
        }

        $balance = $available_balance->amount;

        // dd($balance);

        //Get Wallet ID with given Customer ID
        // $wallet = DB::table('oc_wallet')
        //             ->select('wallet_id')
        //             ->leftJoin('oc_customer','oc_customer.customer_id','=','oc_wallet.customer_id')
        //             ->where('oc_customer.customer_id', $customer_id)
        //             ->first();

        // $walletID = $wallet->wallet_id;

        //Get Wallet Balance
        // $wallet_balance = DB::table('oc_wallet')
        //     ->select('balance')
        //     ->where('wallet_id', $walletID)
        //     ->first();

        // $wallet_LT = $wallet_balance->balance;


        // if ($amount > $wallet_LT) {
        //     return response()->json('Input amount is higher than Unlocked LiveTokens');
        // }

        // $total_wallet_balance = DB::table("oc_wallet")
        //                             ->select("sum (balance + free_balance + seller_lock_token) as total")
        //                             ->where("wallet_id", $walletID)
        //                             ->first();

        // if($request->get('amount') > $total_wallet_balance){
        //     return response()->json('Insufficient LT in Wallet');
        // }

        //Get today total withdraw
        // $today_withdraw = DB::table('oc_bid_transaction')
        //     ->select(DB::raw("SUM(amount) as totalWithdraw"))
        //     ->where('trans_type', 8)
        //     ->where('wallet_id', $walletID)
        //     ->whereDate('date_added', Carbon::today('Asia/Kuala_Lumpur'))
        //     ->first();

        // $total_withdraw = $today_withdraw->totalWithdraw;

        // $totalSum = $amount + $total_withdraw;

        if ($amount <= 1500) {
            $otpResult = OtpTables::where('otp', $request->otp)
                ->where('status', 'success')
                ->where('type', 'withdrawal')
                ->where('session_id', $request->session_id)
                ->first();

            $withdraw = $balance - $amount;

            if($withdraw > 0){
                $available_balance->amount = $withdraw;
                $available_balance->save();
            }else{
                return response()->json(['message' => 'something went wrong',
                                         'status' => 'failed'], 405);
            }

            if (!$otpResult) {
                return response()->json([
                    'result' => 'Session_id failed to validate with OTP Number'
                ], 409);
            }

        }else{
            return response()->json(['message' => 'exceed daily withdraw amount',
                                     'status' => 'failed'], 405);
        }

            $id = $request['bank_id'];

            $bank_id = Bank::find($id);
            if($bank_id != null){
                $bank_name = $bank_id->bank_account_name;
            }else{
                return response()->json(['message' => 'not found',
                                         'status' => 'failed'], 405);
            }

            

            // $wallet = DB::table('oc_wallet')
            //     ->select('wallet_id')
            //     ->leftJoin('oc_customer', 'oc_customer.customer_id', '=', 'oc_wallet.customer_id')
            //     ->where('oc_customer.customer_id', $customer_id)
            //     ->first();

            // $wallet_no = $wallet->wallet_id;

            // $wallet_balance = DB::table('oc_wallet')
            //     ->select('balance')
            //     ->where('wallet_id', $wallet_no)
            //     ->first();

            // $wallet_LT = $wallet_balance->balance;

            // $remain_balance = $wallet_LT - $amount;

            // $processing_fee = $amount * 0.038;

            if (!isset($request['trans_type'])) {
                $request['trans_type'] = 8;
            }

            if (!isset($request['free_token'])) {
                $request['free_token'] = 0;
            }

            if (!isset($request['status'])) {
                $request['status'] = 2;
            }

            if (!isset($request['note'])) {
                $request['note'] = 'NULL';
            }

            $withdrawal = new Transaction;
            $dt = new DateTime;
            $withdrawal->wallet_id = 0;
            $withdrawal->processing_fee = 0;
            $withdrawal->amount = $amount;
            $withdrawal->trans_type = $request['trans_type'];
            $withdrawal->free_token = $request['free_token'];
            $withdrawal->status = $request['status'];
            $withdrawal->note = $request['note'];
            $withdrawal->trans_direction = 'deb';
            $withdrawal->date_added = $dt->format('Y-m-d H:i:s');
            $withdrawal->bank_account_name = $bank_account_name;
            $withdrawal->bank_account_number = $bank_account_number;
            $withdrawal->bank_name = $bank_name;
            $withdrawal->user_unique_id = $user_id;
            $withdrawal->save();

            // $wallet = Wallet::find($wallet_no);
            // $wallet->balance = $remain_balance;
            // $wallet->save();

            // dd($withdrawal);

            return response()->json([
                'completed_time' => $withdrawal->date_added,
                'withdraw_amount' => '-' . $withdrawal->amount,
                'withdraw_to' => $withdrawal->bank_name . ' ' . $withdrawal->bank_account_number,
                'transaction_id' => $withdrawal->id,
                'withdraw_from' => 'sales',
                'status' => 'success',
                'success' => true
            ], 201);
    }

    public function insert_withdrawal(Request $request)
    {
        // dd(Session::all());

        // if(Session::get('bank.otp') == $request->input('otp')){

        //     $otpResult = OtpTables::where('otp', '=', $request->input('otp'))
        //                             ->where('status', '=', 'Success')
        //                             ->where('type', '=', 'Withdrawal')
        //                             ->where('session_id', '=', Session::get('bank.session_id'))
        //                             ->where('phone_number', '=', Session::get('bank.telephone'))
        //                             ->first();
        // }
        // else{
        //     return response()->json( [
        //         'result' => 'Session_id failed to validate with OTP Number'
        //     ], 409);
        // }

        // dd($otpResult);

        // if ($otpResult != null) {

        // $user = auth()->user();
        // $customer_id = $user->customer_id;

        // $wallet = DB::table('oc_wallet')
        //             ->select('wallet_id')
        //             ->leftJoin('oc_customer','oc_customer.customer_id','=','oc_wallet.customer_id')
        //             ->where('oc_customer.customer_id', $customer_id)
        //             ->first();

        // $wallet_no = $wallet->wallet_id;

        // $wallet_balance = DB::table('oc_wallet')
        //                 ->select('balance')
        //                 ->where('wallet_id', $wallet_no)
        //                 ->first();

        // $wallet_LT = $wallet_balance->balance;

        // $remain_balance = $wallet_LT - Session::get('bank.amount');

        // $processing_fee = Session::get('bank.amount') * 0.038;

        //     if(!isset($request['trans_type'])) {
        //         $request['trans_type'] = 8;
        //     }

        //     if(!isset($request['free_token'])) {
        //         $request['free_token'] = 0;
        //     }

        //     if(!isset($request['status'])) {
        //         $request['status'] = 2;
        //     }

        //     if(!isset($request['note'])) {
        //         $request['note'] = 'NULL';
        //     }

        //             $withdrawal = new Transaction;
        //             $dt = new DateTime;
        //             $withdrawal->wallet_id = $wallet_no;
        //             $withdrawal->processing_fee = $processing_fee;
        //             $withdrawal->amount = Session::get('bank.amount');
        //             $withdrawal->trans_type = $request->get('trans_type');
        //             $withdrawal->free_token = $request->get('free_token');
        //             $withdrawal->status = $request->get('status');
        //             $withdrawal->note = $request->get('note');
        //             $withdrawal->trans_direction = 'deb';
        //             $withdrawal->date_added= $dt->format('Y-m-d H:i:s');
        //             $withdrawal->bank_account_name = Session::get('bank.bank_account_name');
        //             $withdrawal->bank_account_number = Session::get('bank.bank_account_number');
        //             $withdrawal->bank_name = Session::get('bank.bank_name');
        //             $withdrawal->save();

        //             $wallet = Wallet::find($wallet_no);
        //             $wallet->balance = $remain_balance;
        //             $wallet->save();

        //             // dd($withdrawal);

        //             return response()->json( [
        //                         'Completed Time'=>$withdrawal->date_added,
        //                         'Withdraw Amount'=>'-'.$withdrawal->amount,
        //                         'Withdraw To' => $withdrawal->bank_name.' '.$withdrawal->bank_account_number,
        //                         'Transaction ID'=> $withdrawal->id,
        //                         'Withdraw From'=> 'LiveWallet'
        //             ], 201);
        // }
    }

    public function getWithdrawDetails()
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $wallet = DB::table('oc_wallet')
            ->select('wallet_id')
            ->leftJoin('oc_customer', 'oc_customer.customer_id', '=', 'oc_wallet.customer_id')
            ->where('oc_customer.customer_id', $customer_id)
            ->first();

            if($wallet === null)
            {
                $withdrawDetails = array();
            }
            else
            {
                $walletID = $wallet->wallet_id;

                // dd($walletID);

                $withdrawDetails = DB::table('oc_bid_transaction')
                    ->select('date_added', 'amount', 'bank_name', 'bank_account_name', 'bank_account_number', 'transaction_id')
                    ->where('wallet_id', $walletID)
                    ->orderBy('date_added', 'desc')
                    ->first();
            }
        

        return response()->json($withdrawDetails);
    }

}
