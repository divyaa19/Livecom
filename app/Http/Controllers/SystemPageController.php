<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SystemPageController extends Controller
{
    public function getblockedaccount()
    {

        $user = auth()->user();
       
        $customer_id=999;

        $status_name=DB::table('oc_customer_blocked_list')
        ->join('oc_customer','oc_customer.customer_id','=','oc_customer_blocked_list.seller_id')
        ->where('oc_customer_blocked_list.customer_id','=',$customer_id)->where('oc_customer_blocked_list.is_blocked','=',0)
        ->get();


        return response()->json([ $status_name],200);
    }
    public function removeblockedaccount($seller_id)
    {

        $user = auth()->user();
       
        $customer_id=999;

        $status_name=DB::table('oc_customer_blocked_list')
       
        ->where('customer_id','=',$customer_id)->where('seller_id','=',$seller_id)->where('oc_customer_blocked_list.is_blocked','=',1)
        ->update([
            'is_blocked'=>0
        ]);


        return response()->json([ 'sucess'=>'removed from block list'],200);
    }
    public function deactivateshop($seller_id)
    {

        $user = auth()->user();
       
        $customer_id=999;

        $status_name=DB::table('oc_purpletree_vendor_stores')
       
        ->where('customer_id','=',$customer_id)->where('is_removed','=',0)
        ->update([
            'is_removed'=>1,
        ]);


        return response()->json([ 'sucess'=>'Store have been deactivated '],200);
    }
}