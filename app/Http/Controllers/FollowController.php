<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Follow;
use App\Models\PurpleTreeStore;
use DB;

class FollowController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function followCount(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->validate($request, [
            'user_id' => 'required'
        ]);

       // $following = Follow::where('by_unique_id', '=', $request['user_id'])->get()->count();


       // $customer_id = $user->customer_id;

      //  $seller_unique_id = PurpleTreeStore::where('customer_id',$customer_id)->first();

        $following = Follow::where('by_unique_id','=',$request['user_id'])->get()->count();

        $followers = Follow::where('unique_id', '=', $request['user_id'])->get()->count();


        $data =
            [
                'following' => $following ?? 0,
                'followers' => $followers ?? 0,
                'liked' => 0
            ];


        return response()->json([
            'result' => $data,
            'status' => 'success'
        ], 200);
    }

    public function storeFollowCount($seller_unique_id)
    {

        $user = auth()->user();
        $user_id = $user->user_id;

        $customer_id = $user->customer_id;

        $seller = PurpleTreeStore::where('seller_id',$customer_id)->first();

        // $seller_unique_id = $seller->seller_unique_id;

        $following = Follow::where('by_unique_id','=',$seller_unique_id)->get()->count();

        $followers = Follow::where('unique_id','=',$seller_unique_id)->get()->count();

        $data = array([
                        'following' => $following,
                        'followers' => $followers
                      ]);

        return response()->json(['result' => $data,
                                 'status' => 'success'
                                ], 200);

    }

    public function getFollow(Request $request, $unique_id){
        $user = auth()->user();
        $user_id = $user->user_id;

        // Get Current User following who
        $user_following = DB::table('oc_follow')
            ->select('oc_follow.unique_id','name','profile_image')
            ->where('oc_follow.by_unique_id', $unique_id)
            ->get();

        //Get Current User followers
        $user_followers = DB::table('oc_follow')
            ->select('oc_follow.by_unique_id','name','profile_image')
            ->where('oc_follow.unique_id', $unique_id)
            ->get();


        // $stores = DB::table('oc_purpletree_vendor_stores')
        //     ->select(
        //         DB::raw(
        //             "store_name as name, oc_purpletree_vendor_stores.store_logo as profile_url "
        //         ),
        //         'oc_purpletree_vendor_stores.id',
        //         'oc_purpletree_vendor_stores.seller_type',
        //         'oc_purpletree_vendor_stores.companyname',
        //         'oc_purpletree_vendor_stores.seller_unique_id as user_id'
        //     )->whereNotNull('oc_purpletree_vendor_stores.seller_unique_id')
        //     ->orWhere('oc_purpletree_vendor_stores.seller_unique_id', '!=', "''")
        //     ->get();

        $data = array(
            'following' => $user_following,
            'followers' => $user_followers
            // 'stores' => $stores
        );

        return response()->json($data);
    }
}
