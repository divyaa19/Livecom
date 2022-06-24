<?php

namespace App\Http\Controllers;

use App\Models\Oc_product_shipping_option;
use App\Models\Oc_product_variation;
use App\Models\oc_seller_products_new;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductShipment;
use App\Models\ProductVariation;
use App\Models\PurpleTreeProduct;
use App\Models\SellerProduct;
use App\Repository\Product\ProductInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;



class ProductController extends Controller
{
    public ProductInterface $product;


    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function getSellerproduct()
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $orders = DB::table('oc_purpletree_vendor_products')
            ->Join('oc_product', 'oc_product.product_id', '=', 'oc_purpletree_vendor_products.product_id')
            ->where('seller_id', '=', $customer_id)
            ->get();

        return response()->json([$orders], 200);
    }

    public function getlivestreamSellerproduct()
    {
        $user = auth()->user();

        $customer_id = $user->product_id;

        $product = DB::table('oc_product')
            ->where('customer_id', '=', $customer_id)
            ->where('selling_mode', '=', 2)
            ->where('shedule_mode', '=', 1)->get();

        return response()->json([
            "livestream_product" => $product

        ], 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function all(Request $request, $seller_id): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'status' => 'success',
            'product' => $this->product->all($seller_id),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allStreamProduct(Request $request, $seller_id): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'status' => 'success',
            'product' => $this->product->allStreamProduct($seller_id),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allStoreProduct(Request $request, $seller_id): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'status' => 'success',
            'product' => $this->product->allStoreProduct($seller_id),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function home(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'status' => 'success',
            'product' => $this->product->home(),
        ]);
    }
    /**
     * @param Request $request
     * @param $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request, $product_id): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'status' => 'success',
            'product' => $this->product->one($product_id),
        ]);
    }


    /**
     * @param Request $request
     * @param $seller_unique_id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addproduct(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'product.sell_mode' => 'nullable',
            'product.buy_mode' => 'nullable',
            'product.image' => 'nullable|array',
            'product.title' => 'nullable',
            'product.status' => 'nullable|in:draft,active',
            'product.category' => 'nullable | numeric',
            'product.description' => 'nullable',
            'product.code' => 'nullable',
            'product.price' => 'nullable | numeric',
            'product.stock' => 'nullable | numeric',
            'variations.*.variation_id' => 'nullable',
            'variations.*.variation_title' => 'nullable',
            'variations.*.variation_value' => 'nullable',
            'variation_data.data.*.type' => 'nullable | numeric',
            'variation_data.data.*.variation' => 'nullable | numeric',
            'variation_data.data.*.variation_price' => 'nullable | numeric',
            'variation_data.data.*.variation_stock' => 'nullable | numeric',
            'specification.*.specification_id' => 'nullable | numeric',
            'specification.*.specification_title' => 'nullable',
            'specification.*.specification_value' => 'nullable',
            'shipment.shipment_free' => 'nullable',
            'shipment.shipment_courier' => 'nullable | numeric',
            'shipment.length' => 'nullable',
            'shipment.width' => 'nullable',
            'shipment.height' => 'nullable',
            'shipment.weight' => 'nullable',
            'shipment_region.region' => 'required_if:shipment.shipment_free,0',
            'shipment_region.fee' => 'required_if:shipment.shipment_free,0 | numeric',
            'shipment_region.limit' => 'required_if:shipment.shipment_free,0 | numeric',
            'buy_mode_data.quantity' => 'nullable | numeric',
            'buy_mode_data.starting_price' => 'required_unless:product.buy_mode,marketplace,lucky_draw| numeric',
            'buy_mode_data.bid_increment' => 'required_if:product.buy_mode,auction_high | numeric',
            'buy_mode_data.duration' => 'nullable | numeric',
            'buy_mode_data.discount_interval' => 'required_if:product.buy_mode,auction_low | numeric',
            'buy_mode_data.discount_interval_type' => 'required_if:product.buy_mode,auction_low | numeric',
            'buy_mode_data.drop_price' => 'required_if:product.buy_mode,auction_low | numeric'
        ]);

        $now = Carbon::now();

        //Insert product
        $product = new Product();

        $status = $request->input('product.status') == "draft" ? 0 : 1;

        $productdata = $product->create([
            'sell_mode' => $request->input('product.sell_mode'),
            'buy_mode' => $request->input('product.buy_mode'),
            'title' => $request->input('product.title'),
            'category' => $request->input('product.category'),
            'description' => $request->input('product.description'),
            'code' => $request->input('product.code'),
            'price' => $request->input('product.price'),
            'stock' => $request->input('product.stock'),
            'date_added' => $now,
            'date_modified' => $now,
            'status' => $status,
            'store_id' => $request['seller_id'],
        ]);

        // dd($productdata);

        foreach ($request['product']['image'] as $data) {
            $image = new ProductImage();
            $image->product_id = $productdata['product_id'];
            $image->image = $data;
            $image->save();
        }

        // foreach ($request['product.image'] as $data) {
        //     $image = new ProductImage();
        //     $image->product_id = $productdata['product_id'];
        //     $image->image = $data;
        //     $image->save();
        // }

        //IF Statement for Specifications
        if ($request['specification'] != null) {
            foreach ($request['specification'] as $data) {
                DB::table('oc_product_specifications')->insert([
                    'product_id' => $productdata['product_id'],
                    'specification_id' => $data['specification_id'],
                    'specification_title' => $data['specification_title'],
                    'specification_value' => $data['specification_value'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        // if ($request['specification'] != null) {
        //     foreach ($request['specification'] as $data) {
        //         DB::table('oc_specifications_product')->insert([
        //             'product_id' => $productdata['product_id'],
        //             'specification_title1' => $data['specification_title1'],
        //             'specification_value1' => $data['specification_value1'],
        //             'specification_title2' => $data['specification_title2'],
        //             'specification_value2' => $data['specification_value2'],
        //             'specification_title3' => $data['specification_title3'],
        //             'specification_value3' => $data['specification_value3'],
        //             'specification_title4' => $data['specification_title4'],
        //             'specification_value4' => $data['specification_value4'],
        //             'specification_title5' => $data['specification_title5'],
        //             'specification_value5' => $data['specification_value5'],
        //             'specification_title6' => $data['specification_title6'],
        //             'specification_value6' => $data['specification_value6'],
        //             'specification_title7' => $data['specification_title7'],
        //             'specification_value7' => $data['specification_value7'],

        //             'created_at' => $now,
        //             'updated_at' => $now
        //         ]);
        //     }
        // }

        //Switch Statement for Buy Mode
        switch ($request['product.buy_mode']) {
            case 'marketplace':

                DB::table('oc_buy_mode_data')->insert([
                    'product_id' => $productdata['product_id'],
                    'quantity' => $request['buy_mode_data.quantity'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);

                //IF Statement for Variation
                if($request['variations'] != null){
                    foreach($request['variations'] as $data){
                        foreach($data['variation_value'] as $variationValue) {
                             DB::table('oc_product_variations')->insert([
                                // $variation = new ProductVariation;
                                // $variationdata = $variation->create([
                                'variation_id' => $data['variation_id'],
                                'product_id' => $productdata['product_id'],
                                'variation_title' => $data['variation_title'],
                                'variation_value' => $variationValue,
                                'created_at' => $now,
                                'updated_at' => $now
                            ]);
                        }
                    }

                    foreach($request['variation_data'] as $data){
                        foreach($data['data'] as $variation){
                            DB::table('oc_product_variations_data')->insert([
                            'variation_id' => $productdata['product_id'],
                            'type' => $data['type'],
                            'variation' => $variation['variation'],
                            'variation_price' => $variation['variation_price'],
                            'variation_stock' => $variation['variation_stock'],
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                        }

                    }
                }


                break;
            case 'auction_high':

                DB::table('oc_buy_mode_data')->insert([
                    'product_id' => $productdata['product_id'],
                    'quantity' => $request['buy_mode_data.quantity'],
                    'starting_price' => $request['buy_mode_data.starting_price'],
                    'bid_increment' => $request['buy_mode_data.bid_increment'],
                    'duration' => $request['buy_mode_data.duration'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);

                // if($request->input('product.sell_mode') == 'livestore')
                // {
                //     DB::table('oc_product_session')->insert([
                //         'product_id' => $productdata['product_id'],
                //         'bid_type' => $request['product.buy_mode'],
                //         'initial_price' => $request['buy_mode_data.starting_price'],
                //         'session_price' => $request['buy_mode_data.starting_price'],
                //         'min_bid' => $request['buy_mode_data.bid_increment'],
                //         'price_tick' => $request['buy_mode_data.bid_increment'],
                //         'tick_time' => $request['buy_mode_data.duration']
                //     ]);
                // }

                break;
            case 'auction_low':

                DB::table('oc_buy_mode_data')->insert([
                    'product_id' => $productdata['product_id'],
                    'quantity' => $request['buy_mode_data.quantity'],
                    'starting_price' => $request['buy_mode_data.starting_price'],
                    'discount_interval' => $request['buy_mode_data.discount_interval'],
                    'discount_interval_type' => $request['buy_mode_data.discount_interval_type'],
                    'duration' => $request['buy_mode_data.duration'],
                    'drop_price' => $request['buy_mode_data.drop_price'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);

                // if($request->input('product.sell_mode') == 'livestore')
                // {
                //     DB::table('oc_product_session')->insert([
                //         'product_id' => $productdata['product_id'],
                //         'bid_type' => $request['product.buy_mode'],
                //         'initial_price' => $request['buy_mode_data.starting_price'],
                //         'session_price' => $request['buy_mode_data.starting_price'],
                //         'min_bid' => $request['buy_mode_data.discount_interval'],
                //         'price_tick' => $request['buy_mode_data.discount_interval'],
                //         'tick_time' => $request['buy_mode_data.duration']
                //     ]);
                // }

                break;
            case 'lucky_draw':

                DB::table('oc_buy_mode_data')->insert([
                    'product_id' => $productdata['product_id'],
                    'quantity' => $request['buy_mode_data.quantity'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);

                break;
            default:
                echo "Error";
        }

        //IF ELSE Statement for Shipment
        $shipment = new ProductShipment();
        $shipmentdata = $shipment->insert([
            'product_id' => $productdata['product_id'],
            'shipment_free' => $request['shipment']['shipment_free'],
            'shipment_courier' => $request['shipment']['shipment_courier'],
            'length' => $request['shipment']['length'],
            'width' => $request['shipment']['width'],
            'height' => $request['shipment']['height'],
            'weight' => $request['shipment']['weight'],
            'created_at' => $now,
            'updated_at' => $now
        ]);

        if ($request['shipment_free'] == 0) {
            foreach ($request['shipment_region'] as $data) {
                DB::table('oc_product_shipment_region')->insert([
                    'shipment_id' => $productdata['product_id'],
                    'region' => $data['region'],
                    'fee' => $data['fee'],
                    'limit' => $data['limit'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        return response()->json([
            "status" => 'success',
            "action" => 'add product'
        ]);
    }

    public function getproduct(
        Request $request
    ) {
        $this->validate($request, [
            'seller_id' => 'required'
        ]);

        $results = DB::table('oc_product')
            ->select(
                'oc_product.*',
                'oc_product_image.*',
                'oc_product_variations.*',
                'oc_product_specifications.*',
                'oc_product_shipment.*',
                'oc_product_shipment_region.*',
                'oc_buy_mode_data.*'
            )
            ->leftjoin('oc_product_image', 'oc_product_image.product_id', '=', 'oc_product.product_id')
            ->leftjoin('oc_product_variations', 'oc_product_variations.product_id', '=', 'oc_product.product_id')
            ->leftjoin(
                'oc_product_specifications',
                'oc_product_specifications.product_id',
                '=',
                'oc_product.product_id'
            )
            ->leftjoin('oc_product_shipment', 'oc_product_shipment.product_id', '=', 'oc_product.product_id')
            ->leftjoin(
                'oc_product_shipment_region',
                'oc_product_shipment_region.shipment_id',
                '=',
                'oc_product_shipment.id'
            )
            ->leftjoin('oc_buy_mode_data', 'oc_buy_mode_data.product_id', '=', 'oc_product.product_id')
            ->where('oc_product.store_id', '=', $request['seller_id'])
            ->get();

        return response()->json(
            [
                'product' => $results,
                'status' => 'success'
            ],
            200
        );
    }

    public function updateproduct(Request $request, $product_id)
    {
        $this->validate($request, [
            'product.sell_mode' => 'required',
            'product.buy_mode' => 'required',
            'product.image' => 'required|array',
            // 'product.image.*.cover_photo' => 'required',
            // 'product.image.*.image1' => 'nullable',
            // 'product.image.*.image2' => 'nullable',
            // 'product.image.*.image3' => 'nullable',
            // 'product.image.*.image4' => 'nullable',
            // 'product.image.*.image5' => 'nullable',
            // 'product.image.*.image6' => 'nullable',


            'product.title' => 'required',
            'product.status' => 'required|in:draft,active',
            'product.category' => 'required | numeric',
            'product.description' => 'nullable',
            'product.code' => 'nullable',
            'product.price' => 'required | numeric',
            'product.stock' => 'required | numeric',
            'variations.*.variation_id' => 'nullable',
            'variations.*.variation_title' => 'nullable',
            'variations.*.variation_value' => 'nullable',
            'variation_data.data.*.type' => 'nullable | numeric',
            'variation_data.data.*.variation' => 'nullable | numeric',
            'variation_data.data.*.variation_price' => 'nullable | numeric',
            'variation_data.data.*.variation_stock' => 'nullable | numeric',
            'specification.*.specification_id' => 'nullable | numeric',
            'specification.*.specification_title' => 'nullable',
            'specification.*.specification_value' => 'nullable',
            // 'specification.*.specification_title1' => 'nullable',
            // 'specification.*.specification_value1' => 'nullable',
            // 'specification.*.specification_title2' => 'nullable',
            // 'specification.*.specification_value2' => 'nullable',
            // 'specification.*.specification_title3' => 'nullable',
            // 'specification.*.specification_value3' => 'nullable',
            // 'specification.*.specification_title4' => 'nullable',
            // 'specification.*.specification_value4' => 'nullable',
            // 'specification.*.specification_title5' => 'nullable',
            // 'specification.*.specification_value5' => 'nullable',
            // 'specification.*.specification_title6' => 'nullable',
            // 'specification.*.specification_value6' => 'nullable',
            // 'specification.*.specification_title7' => 'nullable',
            // 'specification.*.specification_value7' => 'nullable',


            'shipment.shipment_free' => 'nullable',
            'shipment.shipment_courier' => 'nullable | numeric',
            'shipment.length' => 'nullable',
            'shipment.width' => 'nullable',
            'shipment.height' => 'nullable',
            'shipment.weight' => 'nullable',
            'shipment_region.region' => 'required_if:shipment.shipment_free,0',
            'shipment_region.fee' => 'required_if:shipment.shipment_free,0 | numeric',
            'shipment_region.limit' => 'required_if:shipment.shipment_free,0 | numeric',
            'buy_mode_data.quantity' => 'nullable | numeric',
            'buy_mode_data.starting_price' => 'required_unless:product.buy_mode,marketplace| numeric',
            'buy_mode_data.bid_increment' => 'required_if:product.buy_mode,auction_high | numeric',
            'buy_mode_data.duration' => 'nullable | numeric',
            'buy_mode_data.discount_interval' => 'required_if:product.buy_mode,auction_low | numeric',
            'buy_mode_data.discount_interval_type' => 'required_if:product.buy_mode,auction_low | numeric',
            'buy_mode_data.drop_price' => 'required_if:product.buy_mode,auction_low | numeric'
        ]);

        $now = Carbon::now();

        //Insert product
        $product = Product::where('product_id',$product_id)->first();

        if(!$product){
            return response()->json(['message' => 'product not found',
                                     'status' => 'failed'], 404);
        }
        $status = $request->input('product.status') == "draft" ? 0 : 1;

        $productdata = $product->update([
            'sell_mode' => $request->input('product.sell_mode'),
            'buy_mode' => $request->input('product.buy_mode'),
            'title' => $request->input('product.title'),
            'category' => $request->input('product.category'),
            'description' => $request->input('product.description'),
            'code' => $request->input('product.code'),
            'price' => $request->input('product.price'),
            'stock' => $request->input('product.stock'),
            'date_modified' => $now,
            'status' => $status,
            'store_id' => $request['seller_id'],
        ]);

        //Delete Image
        DB::table('oc_product_image')
        ->where('product_id','=', $product_id)
        ->delete();

        //Insert Image
        foreach ($request['product']['image'] as $data) {
            $image = new ProductImage();
            $image->product_id = $product_id;
            $image->image = $data;
            $image->save();
        }
        // dd($image);

        // for($i=0; $i < count($request['product']['image']); $i++){
        //      DB::table('oc_product_image')
        //    ->where('product_id', $product_id)
        //    ->update([
        //         'product_id' => $product_id,
        //         'image' => $request['product']['image'][$i]
        //    ]);
        // }

        // $jaja = DB::table('oc_product_image')->where('product_id', $product_id)->get();

        // foreach ($jaja as $key => $data) {

        // dd($data);

        //     $update_product_image = DB::table('oc_product_image')->where('product_id', $data->product_id)
        //     ->update(array(
        //       'product_id' => $product_id,
        //       'image' => $request->input('product.image'),
        //     )); 
        // }

        //Update Image
        // foreach($request['product']['image'] as $data){
        //     DB::table('oc_image_product')
        //     ->where('product_id', '=', $product_id)
        //     ->update([
        //         'product_id' => $product_id,
        //         'cover_photo' => $data['cover_photo'],
        //         'image1' => $data['image1'],
        //         'image2' => $data['image2'],
        //         'image3' => $data['image3'],
        //         'image4' => $data['image4'],
        //         'image5' => $data['image5'],
        //         'image6' => $data['image6'],
        //         'created_at' => $now,
        //         'updated_at' => $now
        //     ]);
        // }



        // ProductImage::where('product_id', $product_id)        
        // ->update(array(
        //   'product_id' => $product_id,
        //   'image' => $request['product']['image'],
        // )); 

        //Delete Specification
        DB::table('oc_product_specifications')
        ->where('product_id','=', $product_id)
        ->delete();

        //IF Statement for Specifications
        if ($request['specification'] != null) {
            foreach ($request['specification'] as $data) {
                DB::table('oc_product_specifications')->insert([
                    'product_id' => $product_id,
                    'specification_id' => $data['specification_id'],
                    'specification_title' => $data['specification_title'],
                    'specification_value' => $data['specification_value'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        // if ($request['specification'] != null) {
        //     foreach ($request['specification'] as $data) {
        //         DB::table('oc_specifications_product')
        //         ->where('product_id','=', $product_id)
        //         ->update([
        //             'product_id' => $product_id,
        //             'specification_title1' => $data['specification_title1'],
        //             'specification_value1' => $data['specification_value1'],
        //             'specification_title2' => $data['specification_title2'],
        //             'specification_value2' => $data['specification_value2'],
        //             'specification_title3' => $data['specification_title3'],
        //             'specification_value3' => $data['specification_value3'],
        //             'specification_title4' => $data['specification_title4'],
        //             'specification_value4' => $data['specification_value4'],
        //             'specification_title5' => $data['specification_title5'],
        //             'specification_value5' => $data['specification_value5'],
        //             'specification_title6' => $data['specification_title6'],
        //             'specification_value6' => $data['specification_value6'],
        //             'specification_title7' => $data['specification_title7'],
        //             'specification_value7' => $data['specification_value7'],

        //             'updated_at' => $now
        //         ]);
        //     }
        // }

        //Switch Statement for Buy Mode
        switch ($request['product.buy_mode']) {
            case 'marketplace':

                DB::table('oc_buy_mode_data')
                ->where('product_id','=', $product_id)
                ->update([
                    'product_id' => $product_id,
                    'quantity' => $request['buy_mode_data.quantity'],
                    'updated_at' => $now
                ]);

                //Delete Variations
                DB::table('oc_product_variations')
                ->where('product_id','=', $product_id)
                ->delete();

                //IF Statement for Variation
                if($request['variations'] != null){
                    foreach($request['variations'] as $data){
                        foreach($data['variation_value'] as $variationValue) {
                             DB::table('oc_product_variations')->insert([
                                'variation_id' => $data['variation_id'],
                                'product_id' => $product_id,
                                'variation_title' => $data['variation_title'],
                                'variation_value' => $variationValue,
                                'created_at' => $now,
                                'updated_at' => $now
                            ]);
                        }
                    }

                    //Delete Variations
                    DB::table('oc_product_variations_data')
                    ->where('variation_id','=', $product_id)
                    ->delete();

                    foreach($request['variation_data'] as $data){
                        foreach($data['data'] as $variation){
                            DB::table('oc_product_variations_data')->insert([
                            'variation_id' => $product_id,
                            'type' => $data['type'],
                            'variation' => $variation['variation'],
                            'variation_price' => $variation['variation_price'],
                            'variation_stock' => $variation['variation_stock'],
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                        }

                    }
                }

                break;
            case 'auction_high':

                DB::table('oc_buy_mode_data')
                ->where('product_id','=', $product_id)
                ->update([
                    'product_id' => $product_id,
                    'quantity' => $request['buy_mode_data.quantity'],
                    'starting_price' => $request['buy_mode_data.starting_price'],
                    'bid_increment' => $request['buy_mode_data.bid_increment'],
                    'duration' => $request['buy_mode_data.duration'],
                    'updated_at' => $now
                ]);

                break;
            case 'auction_low':

                DB::table('oc_buy_mode_data')
                ->where('product_id','=', $product_id)
                ->update([
                    'product_id' => $product_id,
                    'quantity' => $request['buy_mode_data.quantity'],
                    'starting_price' => $request['buy_mode_data.starting_price'],
                    'discount_interval' => $request['buy_mode_data.discount_interval'],
                    'discount_interval_type' => $request['buy_mode_data.discount_interval_type'],
                    'duration' => $request['buy_mode_data.duration'],
                    'drop_price' => $request['buy_mode_data.drop_price'],
                    'updated_at' => $now
                ]);

                break;
            default:
                echo "Error";
        }

        //Update Shipment
        DB::table('oc_product_shipment')->where('product_id',$product_id)->update([
            'product_id' => $product_id,
            'shipment_free' => $request['shipment']['shipment_free'],
            'shipment_courier' => $request['shipment']['shipment_courier'],
            'length' => $request['shipment']['length'],
            'width' => $request['shipment']['width'],
            'height' => $request['shipment']['height'],
            'weight' => $request['shipment']['weight'],
            'created_at' => $now,
            'updated_at' => $now
        ]);

        if ($request['shipment_free'] == 0) {

            // Delete Shipment Region
            DB::table('oc_product_shipment_region')
            ->where('shipment_id','=', $product_id)
            ->delete();
            
            //Insert Shipment Region
            foreach ($request['shipment_region'] as $data) {
                DB::table('oc_product_shipment_region')->insert([
                    'shipment_id' => $product_id,
                    'region' => $data['region'],
                    'fee' => $data['fee'],
                    'limit' => $data['limit'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        //IF ELSE Statement for Shipment
        // $shipment = ProductShipment::where('product_id','=', $product_id);
        // $shipmentdata = DB::table('oc_product_shipment')
        // ->where('product_id', '=', $product_id)
        // ->update([
        //     'product_id' => $product_id,
        //     'shipment_free' => $request['shipment']['shipment_free'],
        //     'shipment_courier' => $request['shipment']['shipment_courier'],
        //     'created_at' => $now,
        //     'updated_at' => $now
        // ]);

        // if ($request['shipment']['shipment_free'] == 0) {

        //     foreach ($request['shipment_region'] as $data) {
        // // dd($data);
        //        $haha =  DB::table('oc_product_shipment_region')
        //         ->where('shipment_id','=', $shipment['id'])
        //         ->where('id','=', $data)
        //         ->update([
        //             'shipment_id' => $shipment['id'],
        //             'region' => $data['region'],
        //             'fee' => $data['fee'],
        //             'limit' => $data['limit'],
        //             'created_at' => $now,
        //             'updated_at' => $now
        //         ]);


        //     }
        // }

        return response()->json([
            "status" => 'success',
            "action" => 'edit product'
        ]);
    }

    public function deleteproduct(Request $request, $product_id)
    {
            DB::table('oc_product')
            ->where('product_id', '=', $product_id)
            ->delete();

            return response()->json([
                'status' => 'success',
                'action' => 'deleted product'
            ], 200);
    
        }
    
        public function deletetohistory($product_id)
        {
            DB::table('oc_product')
            ->where('product_id', $product_id)
            ->update([
                'status' => 2
            ]);
    
            return response()->json([
                'status' => 'success',
                'action' => 'deleted product to history'
            ], 200);
        }
    public function addsellerproduct(
        Request $request
    ) {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        // $customer_id = 1000;
        // $p_ids=20;

        $this->validate($request, [
            'type' => 'required',
            'bid_amount' => 'required',
            'brand' => 'required',
            'quantity' => 'required',
            'stock_status' => 'required',
            'price' => 'required|numeric',
        ]);

        $product_table = new oc_seller_products_new;

        $product_table->customer_id = $customer_id;
        $product_table->metal = $request->input('metal');
        $product_table->type = $request->input('type');
        $product_table->guid = $request->input('guid');
        $product_table->hashid = $request->input('hashid');
        $product_table->model = $request->input('model');
        $product_table->description = $request->input('description');
        $product_table->category = $request->input('category');

        $product_table->selling_mode_id = $request->input('selling_mode_id');
        $product_table->buying_mode_id = $request->input('buying_mode_id');
        $product_table->shedule_id = $request->input('shedule_id');
        $product_table->date_avaliable = $request->input('date_avaliable');
        $product_table->start_date = $request->input('start_date');
        $product_table->start_time = $request->input('start_time');
        $product_table->end_time = $request->input('end_time');
        $product_table->end_date = $request->input('end_date');

        $product_table->sku = $request->input('sku');
        $product_table->upc = $request->input('upc');
        $product_table->ean = $request->input('ean');
        $product_table->jan = $request->input('jan');
        $product_table->isbn = $request->input('isbn');
        $product_table->mpn = $request->input('mpn');
        $product_table->brand = $request->input('brand');
        $product_table->location = $request->input('location');
        $product_table->quantity = $request->input('quantity');
        $product_table->stock_status_id = $request->input('stock_status_id');
        $product_table->manufacture_id = $request->input('manufacture_id');
        $product_table->price = $request->input('price');
        $product_table->price_extra_type = $request->input('price_extra_type');
        $product_table->shipping = $request->input('shipping');
        $product_table->points = $request->input('points');
        $product_table->tax_class_id = $request->input('tax_class_id');
        $product_table->date_avaliable = $request->input('date_avaliable');
        $product_table->weight_calss_id = $request->input('weight_calss_id');
        $product_table->weight = $request->input('weight');
        $product_table->length = $request->input('length');
        $product_table->width = $request->input('width');
        $product_table->height = $request->input('height');
        $product_table->length_calss_id = $request->input('length_calss_id');
        $product_table->suubtract = $request->input('subtract');
        $product_table->minimum = $request->input('minimum');
        $product_table->sort_order = $request->input('sort_order');
        $product_table->status = $request->input('status');
        $product_table->viwed = $request->input('viewed');
        $product_table->shipping_state = $request->input('shipping_state');
        $product_table->unique_id = 0;
        $product_table->bid_amount = $request->input('bid_amount');
        $product_table->is_blocked = $request->input('is_blocked');
        $product_table->store_id = $request->input('store_id');
        $product_table->variation_id = $request->input('variation_id');
        $product_table->store_id = $request->input('store_id');
        $product_table->seller_id = $customer_id;
        $product_table->price_extra = $request->input('price_extra');

        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'required|mimes:jpeg,png,jpg|max:20000',
            ]);

            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);

            $destination_path = 'upload' . DIRECTORY_SEPARATOR . 'store_image' . DIRECTORY_SEPARATOR;
            $document = 'image' . '.' . $file_ext;

            if ($request->file('image')->move($destination_path, $document)) {
                $image = '/upload/store/' . $document;
                $product_table->image = $destination_path . $document;
            } else {
                return response()->json('Cannot upload file');
            }
        }
        $product_table->save();


        $prod_id = DB::table('oc_seller_products_news')
            ->where('customer_id', '=', $customer_id)
            ->first('id');

        // dd($prod_id->id);

        $p_ids = (int)$prod_id->id;

        $prod = DB::table('oc_seller_products_news')
            ->where('customer_id', '=', $customer_id)
            ->first();

        // $bid = BidType::where('id',$prod->buying_mode_id)->first();

        // $bid_type = $bid->bid_type;

        // if(!isset($request['deposit_rate'])){
        //     $request['deposit_rate'] = 0;
        // }

        // if(!isset($request['cutoff_price'])){
        //     $request['cutoff_price'] = 0;
        // }

        // if(!isset($request['price_tick'])){
        //     $request['price_tick'] = 0;
        // }

        // if(!isset($request['tick_time'])){
        //     $request['tick_time'] = 0;
        // }

        // if(!isset($request['tick_type'])){
        //     $request['tick_type'] = '';
        // }

        // if(!isset($request['run_time'])){
        //     $request['run_time'] = 0;
        // }

        // if(!isset($request['run_type'])){
        //     $request['run_type'] = '';
        // }

        // if(!isset($request['bid_letters'])){
        //     $request['bid_letters'] = '';
        // }

        // if(!isset($request['timer_start'])){
        //     $request['timer_start'] = 0;
        // }

        // if(!isset($request['timer_end'])){
        //     $request['timer_end'] = 0;
        // }

        // if(!isset($request['old_price'])){
        //     $request['old_price'] = 0;
        // }

        // if(!isset($request['live_id'])){
        //     $request['live_id'] = 0;
        // }

        // //if selling_mode_id = 2 insert into oc_stream_product_details

        // if($request->input('selling_mode_id') == 2){

        //     $product_session = new ProductSession;
        //     $product_session->product_id = $p_ids;
        //     $product_session->stream_id = $request['live_id'];
        //     $product_session->bid_type = $bid_type;
        //     $product_session->deposit_rate =  $request['deposit_rate'];
        //     $product_session->cutoff_price = $request['cutoff_price'];
        //     $product_session->price_tick = $request['price_tick'];
        //     $product_session->tick_time = $request['tick_time'];
        //     $product_session->tick_type = $request['tick_type'];
        //     $product_session->run_time = $request['run_time'];
        //     $product_session->run_type = $request['run_type'];
        //     $product_session->bid_letters = $request['bid_letters'];
        //     $product_session->timer_start = $request['timer_start'];
        //     $product_session->timer_end = $request['timer_end'];
        //     $product_session->old_price = $request['old_price'];
        //     $product_session->quantity = $prod->quantity;
        //     $product_session->status = 1;
        //     $product_session->save();

        //     $live_product = new LiveStreamProduct;
        //     $live_product->product_id = $p_ids;
        //     $live_product->stream_id = $request['live_id'];
        //     $live_product->bid_type = $bid_type;
        //     $live_product->sku = '';
        //     $live_product->quantity = $prod->quantity;
        //     $live_product->reserve_price = 0;
        //     $live_product->deposit_rate = $request['deposit_rate'];
        //     $live_product->cutoff_price = $request['cutoff_price'];
        //     $live_product->price_tick = $request['bid_increment'];
        //     $live_product->tick_time = $request['tick_time'];
        //     $live_product->tick_type = '';
        //     $live_product->current_price = $request['starting_price'];
        //     $live_product->min_bid = 0;
        //     $live_product->max_bid = 0;
        //     $live_product->bid_open = 1;
        //     $live_product->run_time = $request['game_duration'];
        //     $live_product->run_type = '';
        //     $live_product->bids_per_click = 0;
        //     $live_product->timer_start = 0;
        //     $live_product->timer_end = 0;
        //     $live_product->save();
        // }

        // if($request->input('selling_mode_id') == 1)
        // {
        //     $product_session = new ProductSession;
        //     $product_session->product_id = $p_ids;
        //     $product_session->stream_id = 0;
        //     $product_session->bid_type = $bid_type;
        //     $product_session->deposit_rate =  $request['deposit_rate'];
        //     $product_session->cutoff_price = $request['cutoff_price'];
        //     $product_session->price_tick = $request['price_tick'];
        //     $product_session->tick_time = $request['tick_time'];
        //     $product_session->run_time = $request['run_time'];
        //     $product_session->run_type = $request['run_type'];
        //     $product_session->bid_letters = $request['bid_letters'];
        //     $product_session->timer_start = $request['timer_start'];
        //     $product_session->timer_end = $request['timer_end'];
        //     $product_session->old_price = $request['old_price'];
        //     $product_session->quantity = $prod->quantity;
        //     $product_session->status = 1;
        //     $product_session->save();

        //     $store_product = new LiveStoreProduct;
        //     $store_product->product_id = $p_ids;
        //     $store_product->joined_count = 0;
        //     $store_product->status = 1;
        //     $store_product->save();
        // }

        $product_table_seller = new PurpleTreeProduct;
        $product_table_seller->product_id = $p_ids;
        $product_table_seller->seller_id = $customer_id;

        $product_table_seller->is_featured = $request->input('is_featured');
        $product_table_seller->is_category_featured = $request->input('is_category_featured');
        $product_table_seller->delivery_address = $request->input('delivery_address');
        $product_table_seller->is_approved = $request->input('is_approved');
        $product_table_seller->vacation = $request->input('vacation');
        $product_table_seller->save();

        $product_variation = new Oc_product_variation;
        $product_variation->product_id = (int)$p_ids;
        $product_variation->value = $request->input('value');
        $product_variation->price = $request->input('price');
        $product_variation->stock = $request->input('stock');
        $product_variation->variation_size = $request->input('variation_size');
        $product_variation->variation_title = $request->input('variation_title');
        $product_variation->save();

        $v_id = $product_variation->id;

        $product_table->variation_id = (int)$v_id;
        $product_table->update();

        $shipping_data = array(
            explode(',', $request->input('shipping_fees')),
            explode(',', $request->input('order_limit')),
            explode(',', $request->input('country_id')),
        );

        foreach ($shipping_data as $data) {
            $seller_shipping = new Oc_product_shipping_option;
            $seller_shipping->seller_id = $customer_id;
            $seller_shipping->product_id = (int)$p_ids;
            $seller_shipping->shipping_courier_id = $request->input('shipping_courier_id');
            $seller_shipping->shipping_fees = $data[0];
            $seller_shipping->order_limit = $data[1];
            $seller_shipping->country_id = $data[2];

            $seller_shipping->length = $request->input('length');
            $seller_shipping->width = $request->input('width');
            $seller_shipping->height = $request->input('height');
            $seller_shipping->weight = $request->input('weight');

            $seller_shipping->save();
        }

        $variation_id = DB::table('oc_product_variations')
            ->where('product_id', '=', (int)$p_ids)
            ->first('id');
        // dd($variation_id);
        $v_id = $variation_id->id;
        $va_id = DB::table('oc_seller_products_news')->where('id', '=', (int)$p_ids)->update(['variation_id' => $v_id]);


        return response()->json(['status' => 'success'], 200);
    }

    // public function updatesellerproduct(
    //     Request $request,
    //     $product_id
    // ) {
    //     $user = auth()->user();
    //     $customer_id = $user->customer_id;
    //     $this->validate($request, [
    //         'type' => 'required',
    //         'bid_amount' => 'required',
    //         'brand' => 'required',
    //         'quantity' => 'required',
    //         'stock_status' => 'required',
    //         'price' => 'required|numeric',

    //     ]);
    //     $product_table = SellerProduct::where('id', '=', $product_id)->find($product_id);
    //     $product_table->brand = $request->input('brand');
    //     $product_table->location = $request->input('location');
    //     $product_table->quantity = $request->input('quantity');
    //     $product_table->stock_status_id = $request->input('stock_status_id');
    //     $product_table->price = $request->input('price');
    //     $product_table->price_extra_type = $request->input('price_extra_type');
    //     $product_table->date_available = $request->input('date_available');
    //     $product_table->subtract = $request->input('subtract');
    //     $product_table->minimum = $request->input('minimum');
    //     $product_table->sort_order = $request->input('sort_order');
    //     $product_table->status = $request->input('status');
    //     $product_table->shipping_state = $request->input('shipping_state');

    //     if ($request->hasFile('image')) {
    //         $this->validate($request, [
    //             'image' => 'required|mimes:jpeg,png,jpg|max:20000',
    //         ]);

    //         $original_filename = $request->file('image')->getClientOriginalName();
    //         $original_filename_arr = explode('.', $origfinal_filename);
    //         $file_ext = end($original_filename_arr);

    //         $destination_path = 'upload' . DIRECTORY_SEPARATOR . 'store_image' . DIRECTORY_SEPARATOR;
    //         $document = 'image' . '.' . $file_ext;

    //         if ($request->file('store_image')->move($destination_path, $document)) {
    //             $image->document = '/upload/store/' . $document;
    //             $product_table->image = $destination_path . $document;
    //         } else {
    //             return response()->json('Cannot upload file');
    //         }
    //     }


    //     $product_table_seller = PurpleTreeProduct::where('product_id', '=', $product_id)->find();
    //     $product_table_seller->is_featured = $request->input('is_featured');
    //     $product_table_seller->is_category_featured = $request->input('is_category_featured');
    //     $product_table_seller->delivery_address = $request->input('delivery_address');
    //     $product_table_seller->is_approved = $request->input('is_approved');
    //     $product_table_seller->save();

    //     $product_table->save();
    // }

    // public function editvariation(
    //     Request $request,
    //     $product_id,
    //     $product_variation_id
    // ) {
    //     $seller_shipping = DB::table('oc_product_variations')
    //         ->where('product_id', '=', $product_id)
    //         ->where('id', '=', 'product_variation_id')
    //         ->update([
    //             'value' => $request->input('value'),
    //             'price' => $shipping_data['price'],
    //             'stock' => $shipping_data['stock'],
    //             'variation_size' => $shipping_data['variation_size'],
    //             'variation_title' => $request->input('variation_title'),
    //         ]);
    // }


public function addspecifications(Request $request, $product_id)
{
    // $id = Auth::product_id();
    // $user = auth()->user();
    // $id = $user->product_id;
    // $data = $request->get('oc_product');
    // $id = $user->product_id;
    // $product = new Product;
    // $product->product_id = $request->product_id;


    $this->validate($request, [
        'title' => 'required'
    ]);

    $now = Carbon::now();

    $specifications = DB::table('oc_product_specifications')->insert([
                      'product_id' => $product_id,
                      'title' => $request['title'],
                      'created_at' => $now,
                      'updated_at' => $now
    ]);

    if($specifications === null)
    {

    }
    else
    {
        return response()->json( [
        'action' => $specifications,
        'status' => 'success',
        'success' => true
    ], 201);
    }
    

}

// public function editspecification(Request $request,$product_id,$product_specification_id)

    // public function editshippingoptions(
    //     Request $request,
    //     $product_id,
    //     $product_specification_id
    // ) {
    //     $seller_shipping = DB::table('oc_product_shipping_options')
    //         ->where('id', '=', $product_specification_id)
    //         ->where('product_id', '=', $product_id)
    //         ->update([
    //             'shipping_courier_id' => $request->input('shipping_courier_id'),
    //             'shipping_fees' => $shipping_data['shipping_fees'],
    //             'order_limit' => $shipping_data['order_limit'],
    //             'country_id' => $shipping_data['country_id'],
    //             'length' => $request->input('length'),
    //             'width' => $request->input('width'),
    //             'height' => $request->input('height'),
    //             'weight' => $request->input('weight'),
    //         ]);
    // }

    public function deletespecification(
        Request $request,
        $product_id,
        $product_specification_id
    ) {
        $seller_shipping = DB::table('oc_product_shipping_options')
            ->where('product_id', '=', $product_id)
            ->where('id', '=', 'product_specification_id')
            ->delete();
    }

    public function deletevariation(
        Request $request,
        $product_id,
        $product_variation_id
    ) {
        $seller_shipping = DB::table('oc_product_variations')
            ->where('product_id', '=', $product_id)
            ->where('id', '=', 'product_variaion_id')
            ->delete();
    }

    public function searchProduct(Request $request,$seller_unique_id){

        $this->validate($request,[
            'search' => 'required'
        ]);

        $search = '%'.$request['search'].'%';

        $searchProduct = DB::table('oc_product')
                             ->select('oc_product.*','oc_product_image.*')
                             ->leftJoin('oc_product_image','oc_product_image.product_id','oc_product.product_id')
                             ->where('oc_product.title', 'LIKE', $search)
                             ->where('oc_product.store_id',$seller_unique_id)
                             ->get();

        return response()->json(['search' => $searchProduct,
                                 'status' => 'success',
                                 'success' => true], 200);

    }
    
}