<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalRequestController extends Controller
{
    public function RequestWithdraw(Request $request)
    {
        $user = auth()->user();

        $ids = Auth::user()->name;

        // $name = Auth::user()->email;

        // $name = $id->company_name;

        $this->validate($request, [
            'amount' => 'required|numeric',
        ]);

        $now = Carbon::now();

        $withdraw = DB::table('oc_withdrawal_request')->insert([
            'name' => $ids,
            'amount' => $request['amount'],
            'request_time' => $now,
            'status' => '1',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        return response()->json([
            'action' => $withdraw,
            'status' => 'success',
            'success' => true
        ], 201);
    }

    public function addBankUser(Request $request)
    {
        $user = auth()->user();
        $id = $user->unique_id;

        $this->validate($request, [
            'account_number' => 'required',
            'unique_id' => 'required',
            'bank_id' => 'required'

        ]);

        $now = Carbon::now();

        $bankuser = DB::table('oc_bank_user')->insert([
            'bank_id' => $request['bank_id'],
            'account_number' => $request['account_number'],
            'unique_id' => $request['unique_id'],
            'created_by' => $request['unique_id'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return response()->json([
            'bank_user' => $bankuser,
            'status' => 'success',
            'success' => true
        ], 201);
    }
}
