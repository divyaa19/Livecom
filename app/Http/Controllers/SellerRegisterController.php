<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurpleTreeStore;
use App\Models\Store;
use App\Models\City;
use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Str;
use Laravel\Lumen\Routing\Controller as BaseController;
use Kreait\Firebase\DynamicLink\GetStatisticsForDynamicLink\FailedToGetStatisticsForDynamicLink;

class SellerRegisterController extends BaseController
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function SellerRegister(Request $request)
    {
        $config = [
            'table' => 'oc_purpletree_vendor_stores',
            'field' => 'seller_unique_id',
            'length' => 7,
            'prefix' => 'ST_'
        ];

        $seller_unique_id = IdGenerator::generate($config);

        $dynamicLinks = app('firebase.dynamic_links');


        $user = auth()->user();
        $customer_id = $user->customer_id;

        $this->validate(
            $request,
            [
                'contact_name' => 'required|regex:/^[\pL\s\-]+$/u|unique:oc_purpletree_vendor_stores|strictly_profane',
                'telephone' => 'required',
                'identification_no' => 'required|string',
                'state' => 'required',
                'city' => 'required',
                'store_type' => 'required|string',
                'store_name' => 'required|strictly_profane',
                'postcode' => 'required|numeric',
                'address_line_1' => 'required',
                'document' => 'nullable',
                'SSM' => 'nullable',
                'front' => 'nullable',
                'back' => 'nullable',
            ]
        );

        $city_id = $request['city'];

        $username = Str::random(10);

        $url = env('BASE_URL').'/api/v1/register';

        $link = $dynamicLinks->createDynamicLink($url);

        $r = $link;

        $r = explode('/', $r);
        $r = array_filter($r);
        $r = array_merge($r, array());
        $r = preg_replace('/\?.*/', '', $r);

        $refer_id = $r[2];

        /* $response = null;
         $front = (object)['front' => ""];
         $back = (object)['back' => ""];
         $user = (object)['document' => ""];
         $profile_image = (object)['store_image' => ""];*/


        $seller_table = new PurpleTreeStore();

        if (!isset($request['address_line_2'])) {
            $request['address_line_2'] = null;
        }

        if (!isset($request['firstname'])) {
            $request['firstname'] = "";
        }

        if (!isset($request['lastname'])) {
            $request['lastname'] = "";
        }

        if (!isset($request['company_telephone_countrycode'])) {
            $request['company_telephone_countrycode'] = "";
        }

        if (!isset($request['gender'])) {
            $request['gender'] = "";
        }

        if (!isset($request['nationality'])) {
            $request['nationality'] = "";
        }

        if (!isset($request['store_email'])) {
            $request['store_email'] = "";
        }

        if (!isset($request['store_phone'])) {
            $request['store_phone'] = "";
        }

        if (!isset($request['store_banner'])) {
            $request['store_banner'] = "";
        }

        if (!isset($request['store_timings'])) {
            $request['store_timings'] = "";
        }

        if (!isset($request['vacation'])) {
            $request['vacation'] = 0;
        }

        if (!isset($request['google_map'])) {
            $request['google_map'] = "";
        }

        if (!isset($request['google_map_link'])) {
            $request['google_map_link'] = "";
        }

        if (!isset($request['store_video'])) {
            $request['store_video'] = "";
        }

        if (!isset($request['store_description'])) {
            $request['store_description'] = "";
        }

        if (!isset($request['store_description'])) {
            $request['store_description'] = "";
        }

        if (!isset($request['store_country'])) {
            $request['store_country'] = 0;
        }

        if (!isset($request['store_shipping_policy'])) {
            $request['store_shipping_policy'] = "";
        }

        if (!isset($request['store_return_policy'])) {
            $request['store_return_policy'] = "";
        }

        if (!isset($request['store_meta_keywords'])) {
            $request['store_meta_keywords'] = "";
        }

        if (!isset($request['store_meta_descriptions'])) {
            $request['store_meta_descriptions'] = "";
        }

        if (!isset($request['swiftcode'])) {
            $request['swiftcode'] = "";
        }

        if (!isset($request['bankname'])) {
            $request['bankname'] = "";
        }

        if (!isset($request['bankcode'])) {
            $request['bankcode'] = "";
        }

        if (!isset($request['bankaccount'])) {
            $request['bankaccount'] = 0;
        }

        if (!isset($request['bankaccountholdername'])) {
            $request['bankaccountholdername'] = "";
        }

        if (!isset($request['store_bank_details'])) {
            $request['store_bank_details'] = "";
        }

        if (!isset($request['store_tin'])) {
            $request['store_tin'] = "";
        }

        if (!isset($request['store_shipping_type'])) {
            $request['store_shipping_type'] = "";
        }

        if (!isset($request['store_shipping_order_type'])) {
            $request['store_shipping_order_type'] = "";
        }

        if (!isset($request['store_live_chat_enable'])) {
            $request['store_live_chat_enable'] = 0;
        }

        if (!isset($request['store_live_chat_code'])) {
            $request['store_live_chat_code'] = "";
        }

        if (!isset($request['store_status'])) {
            $request['store_status'] = 0;
        }

        if (!isset($request['store_commission'])) {
            $request['store_commission'] = "";
        }

        if (!isset($request['is_removed'])) {
            $request['is_removed'] = 0;
        }

        if (!isset($request['seller_paypal_id'])) {
            $request['seller_paypal_id'] = "";
        }

        if (!isset($request['multi_store_id'])) {
            $request['multi_store_id'] = 0;
        }

        if (!isset($request['sort_order'])) {
            $request['sort_order'] = 0;
        }

        if (!isset($request['store_address'])) {
            $request['store_address'] = "";
        }


        $seller_table->contact_name = $request->input('contact_name');
        $seller_table->seller_id = $customer_id;
        $seller_table->telephone = $request['telephone'];
        $seller_table->company_telephone_countrycode = $request['company_telephone_countrycode'] ?? "+60";

        $seller_table->countrycode = $request['telephone_countrycode'];
        $seller_table->idnumber = $request->input('identification_no');
        $seller_table->store_state = $request->input('state');
        $seller_table->store_city = $city_id;
        $seller_table->seller_type = $request['store_type'];
        $seller_table->store_name = $request['store_name'];
        $seller_table->store_zipcode = $request['postcode'];
        $seller_table->store_add1 = $request['address_line_1'];
        $seller_table->store_add2 = $request['address_line_2'];
        $seller_table->firstname = $request->input('firstname');
        $seller_table->lastname = $request->input('lastname');
        $seller_table->username = $username;

        $seller_table->gender = $request->input('gender');
        $seller_table->nationality = $request->input('nationality');
        $seller_table->store_email = $request->input('email');
        $seller_table->email = $request->input('email');
        $seller_table->store_phone = $request->input('store_phone');
        $seller_table->store_banner = $request->input('store_banner');
        $seller_table->store_timings = $request->input('store_timings');
        $seller_table->vacation = $request->input('vacation');
        $seller_table->google_map = $request->input('google_map');
        $seller_table->google_map_link = $request->input('google_map_link');
        $seller_table->store_video = $request->input('store_video');
        $seller_table->store_description = $request->input('store_description');
        $seller_table->store_address = $request->input('store_address');
        $seller_table->store_country = $request->input('store_country');
        $seller_table->store_shipping_policy = $request->input('store_shipping_policy');
        $seller_table->store_return_policy = $request->input('store_return_policy');
        $seller_table->store_meta_descriptions = $request->input('store_meta_descriptions');
        $seller_table->swiftcode = $request->input('swiftcode');
        $seller_table->bankcode = $request->input('bankcode');
        $seller_table->bankname = $request->input('bankname');
        $seller_table->bankaccount = $request->input('bankaccount');
        $seller_table->bankaccountholdername = $request->input('bankaccountholdername');
        $seller_table->store_bank_details = $request->input('store_bank_details');
        $seller_table->store_tin = $request->input('store_tin');
        $seller_table->store_shipping_type = $request->input('store_shipping_type');
        $seller_table->store_shipping_order_type = $request->input('store_shipping_order_type');
        $seller_table->store_live_chat_enable = $request->input('store_live_chat_enable');
        $seller_table->store_live_chat_code = $request->input('store_live_chat_code');
        $seller_table->store_status = $request->input('store_status');
        $seller_table->is_removed = $request->input('is_removed');
        $seller_table->store_created_at = Carbon::now()->toDateTimeString();
        $seller_table->store_updated_at = Carbon::now()->toDateTimeString();
        $seller_table->seller_paypal_id = $request->input('seller_paypal_id');
        $seller_table->multi_store_id = $request->input('multi_store_id');
        $seller_table->sort_order = $request->input('sort_order');
        $seller_table->seller_unique_id = $seller_unique_id;
        $seller_table->store_meta_keywords = "";
        $seller_table->company_document = $request['SSM'];
        $seller_table->id_document = $request->input('id_document');
        $seller_table->bank_document = $request['document'];
        $seller_table->store_image = $request->input('store_logo');
        $seller_table->id_document_back = $request['back'];
        $seller_table->store_logo = $request->input('store_logo');
        $seller_table->document = "";
        $seller_table->seller_refer_id = $refer_id;
        $seller_table->save();

        return response()->json(['status' => 'success'], 200);
    }

    public function updateSeller(Request $request)
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        //Use customer_id to get Seller Unique ID
        $seller_unique_id = Store::find($customer_id);
        $unique_id = $seller_unique_id->seller_unique_id;

        $this->validate($request, [
            'store_name' => 'required|unique:oc_purpletree_vendor_stores',
            'store_email' => 'required|unique:oc_purpletree_vendor_stores|email',
            'store_phone' => 'required|unique:oc_purpletree_vendor_stores|numeric',
            'contact_name' => 'required|regex:/^[\pL\s\-]+$/u|unique:oc_purpletree_vendor_stores|strictly_profane',
        ]);

        if (Store::where('store_name', '=', $request->get('store_name'))->first() != null) {
            return response()->json(['status' => 'store_name exists'], 301);
        } else {
            if (!isset($request['address_line_2'])) {
                $request['address_line_2'] = null;
            }

            $seller_update = Store::where('seller_id', '=', $customer_id)->update([
                'store_name' => $request->get('store_name'),
                'store_email' => $request->get('store_email'),
                'store_phone' => $request->get('store_phone'),
                'contact_name' => $request->get('contact_name'),

            ]);


            return response()->json(['status' => 'success'], 200);
        }
    }

}
