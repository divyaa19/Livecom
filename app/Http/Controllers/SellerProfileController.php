<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\PurpleTreeOrderHistory;
use App\Models\PurpleTreeStore;
use App\Models\SellerProduct;
use App\Models\Product;
Use Carbon\Carbon;
Use App\Models\Follow;

class SellerProfileController extends Controller
{
    public function sellerCatalog(Request $request)
    {
        $result = DB::table('oc_product')
                        ->select('oc_product.*')
                        ->where('store_id',$request['seller_unique_id'])
                        ->simplePaginate($request->limit);

        if(!$result->isEmpty())
        {
            return response()->json(['catalog' => $result,
                                 'status' => 'success'], 200);
        }else{
            return response()->json(['status' => 'not found'], 404);
        }
    }

    public function productInfo(Request $request,$id)
    {
        $product = DB::table('oc_product')
                            ->select('oc_product.*','oc_product_image.*')
                            ->join('oc_product_image','oc_product_image.product_id','=','oc_product.product_id')
                            ->where('oc_product.product_id',$id)
                            ->first();

        $promotion = DB::table('oc_seller_promotions')
                        ->select('oc_seller_promotions.*')
                        ->where('product_id',$id)
                        ->first();

        if($promotion === null)
        {
            $sales = array();
        }
        else
        {
             $sales = PurpleTreeOrderHistory::where('seller_unique_id','=',$request['seller_unique_id'])->get()->count();
            }

        if(!$product)
        {
            return response()->json(['result' => 'no product available',
                                     'status' => 'not found'], 404);
        }else{
            return response()->json(['product' => $product,
                                     'promotion' => $promotion,
                                     'sales' => $sales,
                                     'status' => 'success'], 200);
        }

    }

    public function sellerPlaylist(Request $request)
    {   
        $liveStreams = DB::table('oc_live')
                    ->select('*')
                    ->where('unique_id',$request['seller_unique_id'])
                    ->simplePaginate($request->limit);

        $upcoming = DB::table('oc_live')
                    ->select('*')
                    ->where('unique_id',$request['seller_unique_id'])
                    ->where('is_schedule','yes')
                    ->simplePaginate($request->limit);

        $vod = DB::table('oc_vod')
                    ->select('*')
                    ->where('unique_id',$request['seller_unique_id'])
                    ->simplePaginate($request->limit);

        
        if(sizeof($liveStreams) || sizeof($upcoming) || sizeof($vod)){
            return response()->json(['live' => $liveStreams,
                                     'upcoming' => $upcoming,
                                     'vod' => $vod,
                                     'status' => 'success'], 200);
        }else{
            return response()->json(['result' => 'not found',
                                     'status' => 'failed'], 404);
        }
    }
}
