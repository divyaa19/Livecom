<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

use Illuminate\Support\Facades\DB;


class SellerProduct extends Controller
{
    public function getsellerproduct(Request $request)
    {

    $product = DB::table('oc_product')->get();

    return response()->json([
        "success" => true,
        "message" => "data retrived successfully",
        "file" => $product
    ]);

    }

    public function postsellerproduct(Request $request,$id)
    {

        $product=new Product;
        $product->metal = $request->input('metal');
        $product->type = $request->input('type');
       
        $product->hashid = $request->input('hashid');
        $product->model = $request->input('model');
        $product->bid_amount = $request->input('bid_amount');
        $product->sku = $request->input('sku');
        $product->brand = $request->input('brand');
        $product->save();




   

    return response()->json([
        "success" => true,
        "message" => "data retrived successfully",
        "file" => $product
    ]);

    }
}
