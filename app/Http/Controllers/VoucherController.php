<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
   

    public function getvoucher($product_id)
    {
       
        $id=$product_id;

        $voucher=DB::table('oc_seller_promotions')
        ->where('product_id','=',$product_id)
        ->get();
      
            return response()->json($voucher);
       

    }
    public function applyvoucher(Request $request)
    {
        $product_id=2131;
        $product=DB::table('oc_product')->where('product_id',$product_id)->first('price');
        $amt=$product->price;
        $dis=$request->input('amount');

        $new_amount=$amt-$dis;
        
        return response()->json($new_amount);

    }
}