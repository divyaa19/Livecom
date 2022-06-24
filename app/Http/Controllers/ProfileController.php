<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ProfileController extends Controller
{
    public function getProfileImage(Request $request, $id)
    {

    $profile = DB::table('oc_customer')
                ->select('profile_url')
                ->where('user_id',$id)
                ->get();

    $profile_image = $profile->pluck('profile_url');

    return response()->json(
        [
            'profile_url' => $profile_image,
            'status'=>'success'
        ], 200);

    }

    public function getSellerProfileImage(Request $request, $seller_id)
    {

    $profile = DB::table('oc_purpletree_vendor_stores')
                ->select('store_logo')
                ->where('seller_unique_id',$seller_id)
                ->get();

    $profile_image = $profile->pluck('store_logo');

    return response()->json(
        [
            'profile_url' => $profile_image,
            'status'=>'success'
        ], 200);

    }

    public function getSellerBanner(Request $request, $seller_id)
    {

    $banner = DB::table('oc_purpletree_vendor_stores')
                ->select('store_banner')
                ->where('seller_unique_id',$seller_id)
                ->get();

    $store_banner = $banner->pluck('store_banner');

    return response()->json(
        [
            'banner' => $store_banner,
            'status'=>'success'
        ], 200);

    }

    public function getSellerStoreImage(Request $request, $seller_id)
    {

    $store = DB::table('oc_purpletree_vendor_stores')
                ->select('store_image')
                ->where('seller_unique_id',$seller_id)
                ->get();

    $store_image = $store->pluck('store_image');

    return response()->json(
        [
            'store_image' => $store_image,
            'status'=>'success'
        ], 200);

    }

    public function getSellerProfile(Request $request, $unique_id)
    {
        $id = DB::table('oc_purpletree_vendor_stores')
        //->select('seller_unique_id')
        ->where('seller_unique_id',$unique_id)
        ->first();

    return response()->json(
        [
            'profile' => $id,
            'status'=>'success'
        ], 200);
    }

}
