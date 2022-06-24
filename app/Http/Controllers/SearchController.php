<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;


class SearchController extends Controller
{
    public function search(Request $request)
    {
        $search=$request['search']??"";

        if($search!=""){
            $customer=Customer::
            leftjoin('oc_purpletree_vendor_stores','oc_purpletree_vendor_stores.seller_id','=','oc_customer.customer_id')
            ->leftjoin('oc_seller_products_news','oc_seller_products_news.customer_id','=','oc_customer.customer_id')
            ->leftjoin('oc_streams','oc_streams.streamer_id','=','oc_customer.customer_id')


            ->orwhere('username','LIKE',"%$search")
            ->orwhere('brand','LIKE',"%$search")
            ->orwhere('title','LIKE',"%$search")


            
            ->get();

        }else{
            // $customer=Customer::all();
        return response()->json(['fail'=>'not found'],400);

        }

        $data=compact('customer','search');

        return response()->json(['sucess'=>$data],200);

    }
    
}