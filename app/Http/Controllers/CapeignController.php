<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Promotion;
use App\Models\PromotionProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class CapeginController extends Controller
{

    public function addpromotionbyseller(Request $request)
    {
        //status ongoing=0,upcoming=1,expired=2

        $this->validate($request, [
            'discount.discount_type' => 'required|in:discount_amount,percentage_off',
            'discount.discount_amount' => 'required_if:discount.discount_type,discount_amount | numeric',
            'promotion.product_id' => 'required_if:promotion.promotion_type,product_voucher',
            'promotion.set_private' => 'required',
            // 'promotion.status' => 'required',
            'promotion.promotion_type' => 'required',
            'promotion.promotion_name' => 'required',
            'promotion.promotion_code' => 'nullable',
            'promotion.start_date' => 'required',
            'promotion.end_date' => 'required',
            'promotion.unit_limitation' => 'required | numeric',
            'promotion.voucher_limitation' => 'nullable | numeric',
            'promotion.minimum_spend' => 'nullable | numeric',
            'promotion.active_immediately' => 'required',
        ]);

        $now = Carbon::now();
        // $discount = new Discount();


        //Switch Statement For Promotion Type
        switch ($request['promotion.promotion_type']) {
            case 'shop_voucher':

                // $productbyseller_id = Product::where('store_id','=', $request->seller_id)->get();

                //Insert Promotion
                // foreach($productbyseller_id as $data){
                $shop_voucher = new Promotion();

                $shop_voucher->seller_id = $request['seller_id'];
                // $shop_voucher->product_id = $data['product_id'];
                $shop_voucher->discount_id = 0;
                $shop_voucher->set_private = $request['promotion']['set_private'];
                $shop_voucher->promotion_type = $request['promotion']['promotion_type'];
                $shop_voucher->promotion_name = $request['promotion']['promotion_name'];
                $shop_voucher->promotion_code = $request['promotion']['promotion_code'];
                $shop_voucher->start_date = $request['promotion']['start_date'];
                $shop_voucher->end_date = $request['promotion']['end_date'];
                $shop_voucher->unit_limitation = $request['promotion']['unit_limitation'];
                $shop_voucher->voucher_limitation = $request['promotion']['voucher_limitation'];
                $shop_voucher->minimum_spend = $request['promotion']['minimum_spend'];
                $shop_voucher->active_immediately = $request['promotion']['active_immediately'];
                $shop_voucher->discount_type = $request['discount']['discount_type'];
                $shop_voucher->discount_amount = $request['discount']['discount_amount'];

                //IF Statement For Active Immediately
                if ($request['promotion']['active_immediately'] == true) {
                    $shop_voucher->start_date = $now;
                    $shop_voucher->status = 1;
                } else {
                    $shop_voucher->start_date = $request['promotion']['start_date'];
                    $shop_voucher->status = 0;
                }

                $shop_voucher->created_at = $now;
                $shop_voucher->updated_at = $now;

                $shop_voucher->save();
                // }

                break;
            case 'product_voucher':

                //Insert Promotion
                $product = Promotion::create([
                    'seller_id' => $request['seller_id'],
                    'discount_id' => 0,
                    'set_private' => $request['promotion']['set_private'],
                    'promotion_type' => $request['promotion']['promotion_type'],
                    'promotion_name' => $request['promotion']['promotion_name'],
                    'promotion_code' => $request['promotion']['promotion_code'],
                    'start_date' => $request['promotion']['active_immediately'] ? $now : $request['promotion']['start_date'],
                    'end_date' => $request['promotion']['end_date'],
                    'unit_limitation' => $request['promotion']['unit_limitation'],
                    'voucher_limitation' => $request['promotion']['unit_limitation'],
                    'discount_type' => $request['discount']['discount_type'],
                    'discount_amount' => $request['discount']['discount_amount'],
                    'minimum_spend' => $request['promotion']['minimum_spend'],
                    'active_immediately' => $request['promotion']['active_immediately'],
                    'status' => $request['promotion']['active_immediately'] ? 1 : 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($request['promotion']['product_id'] as $data) {
                    PromotionProduct::create([
                        'promotion_id' => $product['id'],
                        'product_id' => $data,
                    ]);
                }

                break;
            default:
                echo "Error";
        }


        return response()->json([
            "status" => 'success',
            "action" => 'add promotion by seller'
        ]);
    }

    public function getpromotionseller(Request $request)
    {
        $this->validate($request, [
            'seller_id' => 'required'
        ]);

        $results = Promotion::with('product')
            ->where('seller_id', '=', $request['seller_id'])
            ->get();

        return response()->json(
            [
                'promotion' => $results,
                'status' => 'success'
            ],
            200
        );
    }
    
    public function getpromotionforyou(Request $request)
    {
        $results = Promotion::with('product')
            ->get();

        return response()->json(
            [
                'promotion' => $results,
                'status' => 'success'
            ],
            200
        );
    }
    


    //status ongoing=1,upcoming=0,experied=2
    public function getcapginbyseller()
    {
        $user = auth()->user();

        $customer_id = $user->customer_id;

        $promotion_byseller_ongoing = DB::table('oc_seller_promotions')
            ->leftjoin('discounts', 'discounts.seller_id', '=', 'oc_seller_promotions.seller_id')
            ->where('oc_seller_promotions.status', '=', 0)
            ->where('oc_seller_promotions.seller_id', '=', $customer_id)->first();

        $promotion_byseller_upcoming = DB::table('oc_seller_promotions')
            ->leftjoin('discounts', 'discounts.seller_id', '=', 'oc_seller_promotions.seller_id')
            ->where('oc_seller_promotions.status', '=', 1)
            ->where('oc_seller_promotions.seller_id', '=', $customer_id)->first();

        $promotion_byseller_experied = DB::table('oc_seller_promotions')
            ->leftjoin('discounts', 'discounts.seller_id', '=', 'oc_seller_promotions.seller_id')
            ->where('oc_seller_promotions.status', '=', 2)
            ->where('oc_seller_promotions.seller_id', '=', $customer_id)->first();

        return response()->json([

            'ongoing' => $promotion_byseller_ongoing,
            'upcoming' => $promotion_byseller_upcoming,
            'experied' => $promotion_byseller_experied,


        ], 200);
    }

    // public function editpromotion(Request $request,$product_id)
    // {

    //     $user = auth()->user();

    //     $customer_id = $user->customer_id;


    //     $this->validate($request,[

    //         // 'promotion_type' => 'required',
    //         'promotion_name' => 'required',
    //         'discount_type' => 'required',
    //         'unit_limitation' => 'required',
    //         'start_date' => 'required',
    //         'end_date' => 'required',


    //     ]);

    // $promotion=Promotion::where('product_id','=',$product_id);
    // $promotion->promotion_name=$request->input('promotion_name');
    // $promotion->unit_limitation=$request->input('unit_limitation');
    // $promotion->set_private=$request->input('set_private');
    // $promotion->voucher_limitation=$request->input('vourcher_limitation');
    // $promotion->minimum_spend=$request->input('minimum_spend');
    // $promotion->start_date=$request->input('start_date');
    // $promotion->end_date=$request->input('end_date');
    // $promotion->save();
    // $discount=Discount::where('seller_id','=',$customer_id)->where('discounts.product_id','=','oc_seller_promotions.product_id')->first('id');
    // $discount->discount_type=  $request->input('discount_type');
    // $discount->discount_amount=$request->input('discount_amount');
    // $discount->percentage_off= $request->input('percentage_off');

    // $discount->save();
    // }

    public function editpromotion(Request $request, $promotion_id)
    {        
        //status ongoing=0,upcoming=1,expired=2

        $this->validate($request, [
            'discount.discount_type' => 'required|in:discount_amount,percentage_off',
            'discount.discount_amount' => 'required_if:discount.discount_type,discount_amount | numeric',
            'promotion.product_id' => 'required_if:promotion.promotion_type,product_voucher',
            'promotion.set_private' => 'required',
            // 'promotion.status' => 'required',
            'promotion.promotion_type' => 'required',
            'promotion.promotion_name' => 'required',
            'promotion.promotion_code' => 'nullable',
            'promotion.start_date' => 'required',
            'promotion.end_date' => 'required',
            'promotion.unit_limitation' => 'required | numeric',
            'promotion.voucher_limitation' => 'nullable | numeric',
            'promotion.minimum_spend' => 'nullable | numeric',
            'promotion.active_immediately' => 'required',
        ]);

        $now = Carbon::now();

        //Switch Statement For Promotion Type
        switch ($request['promotion.promotion_type']) {
            case 'shop_voucher':

                //Update Promotion
                $shop_voucher = Promotion::find($promotion_id);

                $shop_voucher->seller_id = $request['seller_id'];
                $shop_voucher->discount_id = 0;
                $shop_voucher->set_private = $request['promotion']['set_private'];
                $shop_voucher->promotion_type = $request['promotion']['promotion_type'];
                $shop_voucher->promotion_name = $request['promotion']['promotion_name'];
                $shop_voucher->promotion_code = $request['promotion']['promotion_code'];
                $shop_voucher->start_date = $request['promotion']['start_date'];
                $shop_voucher->end_date = $request['promotion']['end_date'];
                $shop_voucher->unit_limitation = $request['promotion']['unit_limitation'];
                $shop_voucher->voucher_limitation = $request['promotion']['voucher_limitation'];
                $shop_voucher->minimum_spend = $request['promotion']['minimum_spend'];
                $shop_voucher->active_immediately = $request['promotion']['active_immediately'];
                $shop_voucher->discount_type = $request['discount']['discount_type'];
                $shop_voucher->discount_amount = $request['discount']['discount_amount'];

                //IF Statement For Active Immediately
                if ($request['promotion']['active_immediately'] == true) {
                    $shop_voucher->start_date = $now;
                    $shop_voucher->status = 1;
                } else {
                    $shop_voucher->start_date = $request['promotion']['start_date'];
                    $shop_voucher->status = 0;
                }

                $shop_voucher->updated_at = $now;
                $shop_voucher->save();

                break;
            case 'product_voucher':

                //Update Promotion
                $product = Promotion::where('id', $promotion_id)->update([
                    'seller_id' => $request['seller_id'],
                    'discount_id' => 0,
                    'set_private' => $request['promotion']['set_private'],
                    'promotion_type' => $request['promotion']['promotion_type'],
                    'promotion_name' => $request['promotion']['promotion_name'],
                    'promotion_code' => $request['promotion']['promotion_code'],
                    'start_date' => $request['promotion']['active_immediately'] ? $now : $request['promotion']['start_date'],
                    'end_date' => $request['promotion']['end_date'],
                    'unit_limitation' => $request['promotion']['unit_limitation'],
                    'voucher_limitation' => $request['promotion']['unit_limitation'],
                    'discount_type' => $request['discount']['discount_type'],
                    'discount_amount' => $request['discount']['discount_amount'],
                    'minimum_spend' => $request['promotion']['minimum_spend'],
                    'active_immediately' => $request['promotion']['active_immediately'],
                    'status' => $request['promotion']['active_immediately'] ? 1 : 0,
                    'updated_at' => $now,
                ]);

                foreach ($request['promotion']['product_id'] as $data) {
                    DB::table('oc_promotion_product')
                    ->where('promotion_id', $promotion_id)
                    ->update([
                        'promotion_id' => $promotion_id,
                        'product_id' => $data,
                    ]);
                }

                break;
            default:
                echo "Error";
        }


        return response()->json([
            "status" => 'success',
            "action" => 'edit promotion by seller'
        ]);

    }

    public function endpromotion(Request $request, $discount_id)
    {
        $this->validate($request, [
            'seller_id' => 'required'
        ]);

        // DB::table('passengers')->where('id', $id)->update(['name' => $name, 'lasname' => $lastname]);

        $promotion_byseller = DB::table('oc_seller_promotions')
            ->where('id', $discount_id)
            ->where('oc_seller_promotions.seller_id', '=', $request['seller_id'])
            ->update(['status' => 2]);

        return response()->json([
            'promotion' => $promotion_byseller,
            'status' => 'success',
            'action' => 'update status'
        ], 200);
    }

    // public function duplicatepromotion($promotion_id)
    // {
    //     $user = auth()->user();

    //     $customer_id = $user->customer_id;
    //     $promotion_byseller_experied=DB::table('oc_seller_promotions')


    //     ->where('oc_seller_promotions.seller_id','=',$customer_id)->update([

    //         'status'=>0,

    //     ]);

    // }

    public function deletepromotion(Request $request, $discount_id)
    {
        $this->validate($request, [
            'seller_id' => 'required'
        ]);

        $promotion_byseller = DB::table('oc_seller_promotions')
            ->where('id', '=', $discount_id)
            //->where('oc_seller_promotions.status', '=', 2)
            ->where('oc_seller_promotions.seller_id', '=', $request['seller_id'])
            ->delete();

        return response()->json([
            'promotion' => $promotion_byseller,
            'status' => 'success',
            'action' => 'deleted promotion'
        ], 200);
    }

    public function getPromotion($product_id, $seller_id)
    {
        $promotions = DB::table('oc_seller_promotions')
            ->select('oc_seller_promotions.*', 'discounts.*')
            ->join('discounts', 'discounts.id', '=', 'oc_seller_promotions.discount_id')
            ->where('oc_seller_promotions.seller_id', $seller_id)
            ->whereJsonContains('discounts.product_id', $product_id)
            ->get();


        $promotions->transform(function ($i) {
            return (array)$i;
        });

        $array = $promotions->toArray();

        foreach ($promotions as $promotion) {
            $data['promotion'][] = array(
                'promotion_name' => $promotion['promotion_name'],
                'promotion_code' => $promotion['promotion_code'],
                'discount_amount' => 'RM' . $promotion['discount_amount'] . ' ' . 'OFF'
            );
        }

        return response()->json([
            'promotion' => $data,
            'status' => 'success'
        ], 200);
    }
}