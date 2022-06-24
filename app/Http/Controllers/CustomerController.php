<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerBlock;
use App\Models\Follow;
use App\Models\FollowCustomer;
use App\Models\LeaveReasonsQuestion;
use App\Models\Notifications;
use App\Models\PurpleTreeStore;
use App\Models\Reasons;
use App\Models\Store;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function getCustomerBlockList($user_unique_id)
    {
        $blockCustomer = CustomerBlock::where('blocked_by', $user_unique_id)
            ->where('is_blocked', 1)
            ->get();

        // if(count($blockCustomer) != 1){
        //     return response()->json(['result' => 'No result found',
        //                              'status' => 'failed',], 404);
        // }

        return response()->json([
            'block_info' => $blockCustomer,
            'status' => 'success'
        ], 200);
    }

    public function blockOrUnblock(Request $request)
    {
        $user = auth()->user();
        $customer_id = $user->user_id;

        $blocked_user = $request->get('blocked_user');

        if ($blocked_user == $customer_id) {
            return response()->json(['status' => 'failed'], 409);
        }

        $findBlockCustomer = CustomerBlock::where('blocked_at', $blocked_user)
            ->where('blocked_by', $customer_id)
            ->first();

        if (!isset($request['customer_id'])) {
            $request['customer_id'] = 0;
        }

        if (!isset($request['seller_id'])) {
            $request['seller_id'] = 0;
        }

        $get_name = $blocked_user;
        $explode_user = explode("_", $get_name);

        if($explode_user[0] == "ST"){

            $user = PurpleTreeStore::where('seller_unique_id', $blocked_user)
                ->first();

            $name = $user['store_name'];

            $profile_image = $user['store_logo'];

        }
        elseif($explode_user[0] == "CS"){

            $user = Customer::where('user_id', $blocked_user)
                        ->first();

            $name = $user['username'];

            $profile_image = $user['profile_url'];

        }

        $user_name = $name;
        $user_image = $profile_image;


        //Block user
        if ($findBlockCustomer == null) {
            $blockCustomer = new CustomerBlock();
            $blockCustomer->customer_id = $request['customer_id'];
            $blockCustomer->seller_id = $request['seller_id'];
            $blockCustomer->name = $user_name;
            $blockCustomer->profile_image = $user_image;
            $blockCustomer->blocked_by = $customer_id;
            $blockCustomer->blocked_at = $blocked_user;
            $blockCustomer->save();

            $unfollowCustomer = FollowCustomer::where('by_unique_id', $customer_id)
                ->where('unique_id', $blocked_user)
                ->first();

            if ($unfollowCustomer != null) {
                $unfollowCustomer = FollowCustomer::where('by_unique_id', $customer_id)
                    ->where('unique_id', $blocked_user)
                    ->delete();
            }

            return response()->json(['status' => 'success',
                                     'name' => $user_name,
                                     'profile_image' => $user_image,
                                     'action' => 'block'            
                                    ], 201);
        }

        //Unblock User
        if ($findBlockCustomer->is_blocked != 0) {
            $unblockCustomer = CustomerBlock::where('blocked_at', $blocked_user)
                ->where('blocked_by', $customer_id)
                ->first();
            $unblockCustomer->is_blocked = 0;
            $unblockCustomer->save();

            return response()->json([
                'status' => 'success',
                'action' => 'unblock'
            ], 200);
        }

        //Block user again after unblocked
        if ($findBlockCustomer->is_blocked == 0) {
            $blockCustomer = CustomerBlock::where('blocked_at', $blocked_user)
                ->where('blocked_by', $customer_id)
                ->first();
            $blockCustomer->is_blocked = 1;
            $blockCustomer->save();

            $unfollowCustomer = FollowCustomer::where('by_unique_id', $customer_id)
                ->where('unique_id', $blocked_user)
                ->first();

            if ($unfollowCustomer != null) {
                $unfollowCustomer = FollowCustomer::where('by_unique_id', $customer_id)
                    ->where('unique_id', $blocked_user)
                    ->delete();
            }

            return response()->json([
                'status' => 'success',
                'name' => $user_name,
                'profile_image' => $user_image,
                'action' => 'block'
            ], 201);
        }
    }

    public function followOrUnfollow(Request $request, $unique_id)
    {
        $user = auth()->user();
        // $user_id = $user->user_id;

        $following_User = $request['follow_user'];

        if ($following_User == $unique_id) {
            return response()->json(['status' => 'failed'], 409);
        }

        $get_user = $following_User;
        $explode_user = explode("_", $get_user);

        if($explode_user[0] == "ST"){

            $user = PurpleTreeStore::where('seller_unique_id', $get_user)
                ->first();

            $name = $user['store_name'];

            $profile_image = $user['store_logo'];

        }
        elseif($explode_user[0] == "CS"){

            $user = Customer::where('user_id', $get_user)
                        ->first();

            $name = $user['username'];

            $profile_image = $user['profile_url'];
        }

        $user_name = $name;
        $user_image = $profile_image;


        $userFollowing = FollowCustomer::where('by_unique_id', $unique_id)
            ->where('unique_id', $following_User)
            ->first();
            // dd($userFollowing);

        if (!isset($request['follow_by_user_id'])) {
            $request['follow_by_user_id'] = 0;
        }

        if (!isset($request['follow_user_id'])) {
            $request['follow_user_id'] = 0;
        }

        //Follow
        if ($userFollowing == null) {
            $insert_Follow = new Follow();
            $insert_Follow->follow_by_user_id = $request['follow_by_user_id'];
            $insert_Follow->follow_user_id = $request['follow_user_id'];
            $insert_Follow->by_unique_id = $unique_id;
            $insert_Follow->unique_id = $following_User;
            $insert_Follow->profile_image = $user_image;
            $insert_Follow->name = $user_name;
            $insert_Follow->save();

            return response()->json([
                'entity' => 'users',
                'action' => 'follow',
                'status' => 'success'
            ], 201);
        }

        //Notification Follow

        $tomorrow = new DateTime('tomorrow');

        DB::table('oc_notifications')->insert([
            'customer_id' => 0,
            'from_customer_id' => 0,
            'type' => 'socials',
            'notification_title' => $user_name. " started following you",
            'notification_message' => $user_name. " started following you",
            'notification_action' => 'follow',
            'notification_interaction' => '',
            'notification_is_read' => 0,
            'notification_datetime' => Carbon::now(),
            'notification_expire_datetime' => $tomorrow,
            'unique_id' => $following_User,
            'from_unique_id' => $unique_id,
            'name' => $user_name,
            'profile_image' => $user_image
        ]);

        //Unfollow
        if ($userFollowing != null) {
            $unfollowCustomer = FollowCustomer::where('by_unique_id', $unique_id)
                ->where('unique_id', $following_User)
                ->delete();

            return response()->json([
                'entity' => 'users',
                'action' => 'unfollow',
                'status' => 'success'
            ], 201);
        }
    }

    public function deactivate_store(Request $request)
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $customReasons = $request['reasons'];
        $store_id = $request['store_id'];

        //Get reasons ID from input
        if ($request['reasons_id'] != null) {
            $leaveReason = $request['reasons_id'];
            $reasons = LeaveReasonsQuestion::find($leaveReason);
            $reason = $reasons->reasons;

            $leaving = new Reasons();
            $leaving->reasons = $reason;
            $leaving->seller_id = $customer_id;
            $leaving->save();

            //Update Store status
            $store = PurpleTreeStore::where('seller_unique_id', $store_id)->first();
            $store->is_removed = 1;
            $store->save();
        } elseif ($customReasons != "") {
            $leaving = new Reasons();
            $leaving->reasons = $customReasons;
            $leaving->seller_id = $customer_id;
            $leaving->save();

            //Update Store status
            $store = PurpleTreeStore::where('seller_unique_id', $store_id)->first();
            $store->is_removed = 1;
            $store->save();
        } else {
            return response()->json([
                'status' => 'failed'
            ], 201);
        }

        return response()->json([
            'result' => 'store deactivated',
            'status' => 'success'
        ], 201);
    }

    public function deactivate_account(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->user_id;

        $customReasons = $request->get('reasons');

        //Get reasons ID from input
        if ($request->get('reasons_id') != null) {
            $leaveReason = $request->get('reasons_id');
            $reasons = LeaveReasonsQuestion::find($leaveReason);
            $reason = $reasons->reasons;

            $leaving = new Reasons();
            $leaving->reasons = $reason;
            $leaving->unique_id = $user_id;
            $leaving->save();

            //Update member account status
            $member = Customer::where('user_id', $user_id)->first();
            $member->is_delete = 1;
            $member->save();
        } elseif ($customReasons != "") {
            $leaving = new Reasons();
            $leaving->reasons = $customReasons;
            $leaving->unique_id = $user_id;
            $leaving->save();

            //Update member account status
            $member = Customer::where('user_id', $user_id)->first();
            $member->is_delete = 1;
            $member->save();
        } else {
            return response()->json([
                'status' => 'failed'
            ], 404);
        }

        return response()->json([
            'message' => 'account deactivated',
            'status' => 'success'
        ], 201);
    }

    public function switch_account(Request $request)
    {
        $store_id = $request->get('store_id');

        $store = PurpleTreeStore::find($store_id);

        return response()->json($store);
    }

    public function notification_count_member()
    {
        $user = auth()->user();
        $user_id = $user->user_id;

        $user_Notification = Notifications::where('unique_id', $user_id)->count();

        if ($user_Notification > 0) {
            return response()->json([
                'entity' => 'member',
                'action' => 'notification',
                'result' => $user_Notification
            ], 200);
        } else {
            return response()->json([
                'result' => 'no notification',
                'status' => 'failed'
            ], 409);
        }
    }

    public function notification_count_seller()
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $seller_id = Store::find($customer_id);

        if($seller_id == null)
        {
            $seller_Notification = array();
        }
        else
        {   
            $seller_unique_id = $seller_id->seller_unique_id;

        $seller_Notification = Notifications::where('unique_id', $seller_unique_id)->count();

        if ($seller_Notification > 0) {
            return response()->json([
                'entity' => 'seller',
                'action' => 'notification',
                'result' => $seller_Notification
            ], 200);
        } else {
            return response()->json([
                'result' => 'no notification',
                'status' => 'failed'
            ], 409);
        }
        }
        
    }

    public function getMemberInfo()
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $member = Customer::select(
            "customer_id",
            "customer_group_id",
            "store_id",
            "language_id",
            "firstname",
            "lastname",
            "username",
            "nickname",
            "email",
            "telephone_countrycode",
            "telephone",
            "fax",
            "password",
            "profile_url",
            "salt",
            "cart",
            "wishlist",
            "newsletter",
            "address_id",
            "custom_field",
            "ip",
            "status",
            "email_verified",
            "safe",
            "token",
            "referral_token",
            "pin",
            "code",
            "is_blocked",
            "warning_level",
            "block_live_stream",
            "facebook_id",
            "date_added",
            "is_delete",
            "gender",
            "praise_popup",
            "language",
            "select_language_id",
            "google_id",
            "apple_id",
            "referred_by",
            "lastlogin_datetime",
            "user_id as unique_id",
            "ref_link",
            "refer_id",
        )
            ->where('customer_id', $customer_id)->first();

        // dd($member);

        return response()->json(
            [
                'member_info' => $member,
                'status' => 'success'
            ],
            200
        );
    }

    public function getSellerInfo()
    {
        $user = auth()->user();
        $customer_id = $user->customer_id;

        $seller = Store::where('seller_id', $customer_id)->get();

        return response()->json([
            "seller_info" => $seller,
            "status" => 'success'
        ], 200);
    }

    public function getSellerDetails($seller_id)
    {
        $seller = Store::where('seller_unique_id', $seller_id)->first();

        return response()->json([
            "seller_details" => $seller,
            "status" => 'success'
        ], 200);
    }

    public function getMemberDetails($user_unique_id)
    {
        $customer = $member = Customer::select(
            "customer_id",
            "customer_group_id",
            "store_id",
            "language_id",
            "firstname",
            "lastname",
            "username",
            "nickname",
            "email",
            "telephone_countrycode",
            "telephone",
            "fax",
            "password",
            "profile_url",
            "salt",
            "cart",
            "wishlist",
            "newsletter",
            "address_id",
            "custom_field",
            "ip",
            "status",
            "email_verified",
            "safe",
            "token",
            "referral_token",
            "pin",
            "code",
            "is_blocked",
            "warning_level",
            "block_live_stream",
            "facebook_id",
            "date_added",
            "is_delete",
            "gender",
            "praise_popup",
            "language",
            "select_language_id",
            "google_id",
            "apple_id",
            "referred_by",
            "lastlogin_datetime",
            "user_id as unique_id",
            "ref_link",
            "refer_id",
        )
            ->where('user_id', $user_unique_id)->first();

        return response()->json([
            "customer_detail" => $customer,
            "status" => 'success'
        ], 200);
    }

}
