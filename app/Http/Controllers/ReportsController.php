<?php

namespace App\Http\Controllers;

use App\Models\ReportedSeller;
use App\Models\ReportReason;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function reportseller(Request $request)
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $this->validate($request, [
            'seller_id' => 'required',
            'complain' => 'required_if:reason_id,6'
        ]);

        $now = Carbon::now();
        
        //Get Reason ID from Table Report Reason
        if($request['reason_id'] != 6){
            $reportReason = $request['reason_id'];
            $reasons = ReportReason::where('id',$reportReason)->first();
            $reason = $reasons->reason;


            //Insert into Reported Seller
            $reporting = new ReportedSeller();
            $reporting->seller_id = $request['seller_id'];
            if(!isset($request['stream_id']) ){
                $reporting->stream_id = 0;
            }else{
                $reporting->stream_id = $request['stream_id'];
            }
            $reporting->reported_by = $customer_id;
            $reporting->complain = $reason;
            $reporting->datetime_added = $now;
            $reporting->reported_id = 0;
            $reporting->save();

        } elseif($request['reason_id'] == "6"){

            //Insert into Reported Seller
            $reporting = new ReportedSeller();
            $reporting->seller_id = $request['seller_id'];
            if(!isset($request['stream_id']) ){
                $reporting->stream_id = 0;
            }else{
                $reporting->stream_id = $request['stream_id'];
            }
            $reporting->reported_by = $customer_id;
            $reporting->complain = $request['complain'];
            $reporting->datetime_added = $now;
            $reporting->reported_id = 0;
            $reporting->save();

        }

        return response()->json([
            'result' => 'seller reported',
            'status' => 'success'
        ], 201);
    }
}
