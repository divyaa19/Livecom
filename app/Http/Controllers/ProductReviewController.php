<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\OrderCartList;
use App\Models\PurpleTreeStore;
use DB;
use Carbon\Carbon;

class ProductReviewController extends Controller
{
    public function getProductReview($product_id,$seller_unique_id)
    {
        $reviews = DB::table('oc_review')
                        ->select('*')
                        ->where('product_id',$product_id)
                        ->where('seller_id',$seller_unique_id)
                        ->get();

        // dd($reviews);

        $reviews->transform(function($i) {
                return (array)$i;
        });

        $array = $reviews->toArray();

        if (!$array)
        {
                return response()->json(['message' => 'review not found',
                                        'status'=>'failed'
                                        ], 404);    
        }

        foreach ($reviews as $review)
        {
            $data['review'][]=array(
                'author' => $review['author'],
                'review' => $review['text'],
                'rating' => $review['rating'],
                'date_added' => $review['date_added']
            );
        }

        return response()->json(['review' => $data,
                                 'status' => 'success'], 200);
    }

    public function addProductReview(Request $request,$order_cart_list_id)
    {
        $user = auth()->user();
        $customer = $user;

        $this->validate($request,[
            'seller_service' => 'required',
            'product_quality' => 'required',
            'image' => 'required'
        ]);

        $review = $request['review'];

        $seller_service = $request['seller_service'];
        $product_quality = $request['product_quality'];

        $order_cart = OrderCartList::find($order_cart_list_id);

        $store_name = $order_cart->store_name;
        $order_id = $order_cart->order_id;
        $product_id = $order_cart->product_id;
        $author = $user->lastname.' '.$user->firstname;

        $product = DB::table('oc_product')
                        ->select('oc_product.image','oc_product_description.name')
                        ->join('oc_product_description','oc_product_description.product_id','=','oc_product.product_id')
                        ->where('oc_product.product_id',$product_id)
                        ->first();

        $product_name = $product->name;
        $product_image = $product->image;

        $seller = PurpleTreeStore::where('store_name',$store_name)->first();

        $seller_id = $seller->seller_id;

        $customer_id = $user->customer_id;

        $sum_of_review = $seller_service + $product_quality;

        $overall_rating = $sum_of_review / 2;

        $add_review = new Review;
        $add_review->order_id = $order_id;
        $add_review->seller_id = $seller_id;
        $add_review->product_id = $product_id;
        $add_review->customer_id = $customer_id;
        $add_review->author = $author;
        $add_review->text = $review;
        $add_review->rating = 0;
        $add_review->seller_service = $seller_service;
        $add_review->product_quality = $product_quality;
        $add_review->overall_rating = $overall_rating;

        $date = Carbon::now()->format('Y-m-d_H-i-s');


        if ($request->hasFile('image')) 
        {
            $original_filename = $request->file('image')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = 'upload'. DIRECTORY_SEPARATOR . 'review_image'. DIRECTORY_SEPARATOR;
            $document = 'review-image-' . $date .'.' . $file_ext;

            if ($request->file('image')->move($destination_path, $document))
            {
                $user = '/upload/review_image/' . $document;

                $add_review->image = $destination_path . $document;

            }else{
                return response()->json('Cannot upload file');
            }

        }

        if ($request->hasFile('image_2')) 
        {
            $this->validate($request,[
                'image_2' => 'required|mimes:jpeg,png,jpg|max:20000',
            ]);
            
            $original_filename = $request->file('image_2')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = 'upload'. DIRECTORY_SEPARATOR . 'review_image'. DIRECTORY_SEPARATOR;
            $document = 'review-image_2-' . $date .'.' . $file_ext;

            if ($request->file('image_2')->move($destination_path, $document))
            {
                $user = '/upload/review_image/' . $document;

                $add_review->image_2 = $destination_path . $document;

            }else{
                return response()->json('Cannot upload file');
            }

        }

        if ($request->hasFile('image_3')) 
        {
            $this->validate($request,[
                'image_3' => 'required|mimes:jpeg,png,jpg|max:20000',
            ]);

            $original_filename = $request->file('image_3')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = 'upload'. DIRECTORY_SEPARATOR . 'review_image'. DIRECTORY_SEPARATOR;
            $document = 'review-image_3-' . $date .'.' . $file_ext;

            if ($request->file('image_3')->move($destination_path, $document))
            {
                $user = '/upload/review_image/' . $document;

                $add_review->image_3 = $destination_path . $document;

            }else{
                return response()->json('Cannot upload file');
            }

        }

        if ($request->hasFile('image_4')) 
        {

            $this->validate($request,[
                'image_4' => 'required|mimes:jpeg,png,jpg|max:20000',
            ]);
            
            $original_filename = $request->file('image_4')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = 'upload'. DIRECTORY_SEPARATOR . 'review_image'. DIRECTORY_SEPARATOR;
            $document = 'review-image_4-' . $date .'.' . $file_ext;

            if ($request->file('image_4')->move($destination_path, $document))
            {
                $user = '/upload/review_image/' . $document;

                $add_review->image_4 = $destination_path . $document;

            }else{
                return response()->json('Cannot upload file');
            }

        }

        if ($request->hasFile('image_5')) 
        {

            $this->validate($request,[
                'image_5' => 'required|mimes:jpeg,png,jpg|max:20000',
            ]);
            
            $original_filename = $request->file('image_5')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = 'upload'. DIRECTORY_SEPARATOR . 'review_image'. DIRECTORY_SEPARATOR;
            $document = 'review-image_5-' . $date .'.' . $file_ext;

            if ($request->file('image_5')->move($destination_path, $document))
            {
                $user = '/upload/review_image/' . $document;

                $add_review->image_5 = $destination_path . $document;

            }else{
                return response()->json('Cannot upload file');
            }

        }

        if ($request->hasFile('image_6')) 
        {

            $this->validate($request,[
                'image_6' => 'required|mimes:jpeg,png,jpg|max:20000',
            ]);
            
            $original_filename = $request->file('image_6')->getClientOriginalName();
            $original_filename_arr = explode('.', $original_filename);
            $file_ext = end($original_filename_arr);
            $destination_path = 'upload'. DIRECTORY_SEPARATOR . 'review_image'. DIRECTORY_SEPARATOR;
            $document = 'review-image_6-' . $date .'.' . $file_ext;

            if ($request->file('image_6')->move($destination_path, $document))
            {
                $user = '/upload/review_image/' . $document;

                $add_review->image_6 = $destination_path . $document;

            }else{
                return response()->json('Cannot upload file');
            }

        }
            $add_review->save();

            $data = array(
                'store_name' => $store_name,
                'product_image' => $product_image,
                'product_name' => $product_name,
                'quantity' => $order_cart->quantity,
                'price' => $order_cart->price,
                'discount_price' => $order_cart->discount_price,
                'variation' => 'Blue,L',
                'shipping' => $order_cart->shipping,
                'type' => $order_cart->type
            );

            return response()->json(['product' => $data,
                                    'message' => 'review added',
                                    'status' => 'success'], 200);
    }

    public function getStore_Overall_Review(Request $request,$seller_unique_id)
    {

        $overall_rating = Review::where('seller_id',$seller_unique_id)->sum('overall_rating');

        if(!$overall_rating){
            return response()->json(['message' => 'not found',
                                     'status' => 'failed'], 404);
        }

        $store_total_rating = Review::where('seller_id',$seller_unique_id)->count();

        $total_rating = $overall_rating / $store_total_rating;

        return response()->json(['overall_rating' => $total_rating,
                                 'status' => 'success',
                                 'success' => true], 200);

    }

    public function getStoreTotalReview(Request $request, $store_id)
    {
        $store = Review::where('seller_id',$store_id)->get()->count();

        return response()->json(['total_review' => $store,
                                 'status' => 'success',
                                 'success' => true], 200);
    }
}
