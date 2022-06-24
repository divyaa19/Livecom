<?php

namespace App\Http\Controllers;

use App\Models\PurpleTreeStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SellerDashboard extends Controller
{

    public function updatesellerprofileimage(Request $request, $seller_unique_id)
    {
        $this->validate($request, [
            'store_logo' => 'required',
        ]);

        $member = PurpleTreeStore::where('seller_unique_id', $seller_unique_id)
            ->update([
                'store_logo' => $request['store_logo']
            ]);

        // $original_filename = $request->file('store_image')->getClientOriginalName();
        // $original_filename_arr = explode('.', $original_filename);
        // $file_ext = end($original_filename_arr);
        // $contact_name = $request->get('contact_name');
        // $destination_path = 'upload' . DIRECTORY_SEPARATOR . 'store_image' . DIRECTORY_SEPARATOR;
        // $document = 'store_image' . $contact_name . '.' . $file_ext;

        // if ($request->file('store_image')->move($destination_path, $document)) {
        //     $profile_image->document = '/upload/store/' . $document;

        //     $seller_update->store_image = $destination_path . $document;

        //     $seller_update->save();
        // } else {
        //     return response()->json('Cannot upload file');
        // }

            return response()->json([
                'message' => 'seller profile image updated',
                'status' => 'success',
                'success' => true
            ], 200);
    }

    public function updateSellerStoreBanner(Request $request, $seller_unique_id)
    {
            $this->validate($request, [
                'store_banner' => 'required',
            ]);

            $member = PurpleTreeStore::where('seller_unique_id', $seller_unique_id)
                                        ->update([
                                        'store_banner'=> $request['store_banner']
            ]);

            return response()->json([
                'message' => 'seller banner updated',
                'status' => 'success',
                'success' => true
            ], 200);
    }

    public function updateSellerInfo(Request $request, $seller_unique_id): \Illuminate\Http\JsonResponse
    {
        $this->validate(
            $request,
            [
                'store_name' => 'required|string',
                'username' => 'required|string|unique:oc_purpletree_vendor_stores,seller_unique_id,' . $seller_unique_id,
                'telephone' => 'required',
                'email' => 'required|email'
            ]
        );

        $store_name = $request['store_name'];
        $username = $request['username'];
        $telephone = $request['telephone'];
        $email = $request['email'];

        $updateStore = PurpleTreeStore::where('seller_unique_id', $seller_unique_id)->first();

        if ($updateStore->store_name != $store_name) {
            $updateStore->store_name = $store_name;
        }

        if ($updateStore->username != $username) {
            $updateStore->username = $username;
        }
        $updateStore->telephone = $telephone;

        if ($updateStore->email != $email) {
            $updateStore->store_email = $email;
        }

        $updateStore->save();

        return response()->json([
            'status' => 'success',
            'success' => true
        ], 200);
    }

    public function updateSellerStoreInfo(Request $request, $seller_unique_id): \Illuminate\Http\JsonResponse
    {
        $this->validate(
            $request,
            [
                'username' => 'required|string|unique:oc_purpletree_vendor_stores,seller_unique_id,' . $seller_unique_id,
                'telephone' => 'required',
                'idnumber' => 'required',
                'state' => 'required',
                'city' => 'required',
                'postcode' => 'required',
                'address_line_1' => 'required',
            ]
        );

        $username = $request['username'];
        $telephone = $request['telephone'];
        $idnumber = $request['idnumber'];
        $state = $request['state'];
        $city = $request['city'];
        $postcode = $request['postcode'];
        $address_line_1 = $request['address_line_1'];
        
        if(!isset($request['address_line_2'])){
            $request['address_line_2'] = '';
        }

        $updateStore = PurpleTreeStore::where('seller_unique_id', $seller_unique_id)->first();

        // if ($updateStore->store_name != $store_name) {
        //     $updateStore->store_name = $store_name;
        // }

        if ($updateStore->username != $username) {
            $updateStore->username = $username;
        }
        $updateStore->telephone = $telephone;
        $updateStore->idnumber = $idnumber;
        $updateStore->store_state = $state;
        $updateStore->store_city = $city;
        $updateStore->store_zipcode = $postcode;
        $updateStore->store_add1 = $address_line_1;
        $updateStore->store_add2 = $request['address_line_2'];

        $updateStore->save();

        return response()->json([
            'status' => 'success',
            'success' => true
        ], 200);
    }

    public function getshopSummary($seller_unique_id)
    {
        // $shop_summary = PurpleTreeStore::find($seller_unique_id);

        $total_sales = 0;
        $orders = 0;
        $vistors = 0;
        $live_views = 0;

        return response()->json([
            'total_sales' => $total_sales,
            'orders' => $orders,
            'vistors' => $vistors,
            'live_viewes' => $live_views,
            'status' => 'success',
            'success' => true
        ], 200);
    }

    public function getAvailableBalance($seller_unique_id)
    {
        // $shop_summary = PurpleTreeStore::find($seller_unique_id);

        // $balance = 100;

        $temporary = DB::table('oc_seller_balance')
            ->select('amount')
            ->where('seller_unique_id', '=', $seller_unique_id)
            ->first();

        return response()->json([
            'available_balance' => $temporary->amount ?? 0,
            'status' => 'success',
            'success' => true
        ], 200);
    }
}
