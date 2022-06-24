<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerLike;
use App\Models\Like;
use Illuminate\Http\Request;

class MemberDashboardController extends Controller
{
    public function getMemberInfo()
    {
        $user = auth()->user();
        $user_id = $user->user_id;

        $memberInfo = Customer::where('user_id', $user_id)->first();

        $member_name = $memberInfo->lastname . ' ' . $memberInfo->firstname;
        $member_username = $memberInfo->username;
        $member_profile_picture = $memberInfo->profile_url;

        $data = array(
            [
                'member_name' => $member_name,
                'member_username' => $member_username,
                'member_profile_picture' => $member_profile_picture
            ]
        );

        return response()->json([
            'result' => $data,
            'status' => 'success'
        ], 200);
    }

    public function updateInfo(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->user_id;

        $this->validate(
            $request,
            [
                'nickname' => 'required|string',
                'username' => 'required|string',
                'telephone' => 'required',
                'email' => 'required|email'
            ]
        );

        $profile_name = $request['nickname'];
        $username = $request['username'];
        $telephone = $request['telephone'];
        $email = $request['email'];

        $updateMember = Customer::where('user_id',$user_id)->first();

        $updateMember->nickname = $profile_name;
        $updateMember->username = $username;
        $updateMember->telephone = $telephone;
        $updateMember->email = $email;
        $updateMember->save();

        return response()->json([
            'status' => 'success',
            'success' => true
        ], 200);
    }

    public function updateProfileImage(Request $request)
    {
        $user = auth()->user();
        $user_id = $user->user_id;

        // $member = Customer::where('user_id', $user_id)->first();

        $this->validate($request, [
            'member_profile' => 'required',
        ]);

        $member = Customer::where('user_id', $user_id)
                            ->update([
                                    'profile_url'=> $request['member_profile']
        ]);

        // $member->profile_url = $request['member_profile'];

        // $member->save();

        return response()->json([
            'message' => 'profile image updated',
            'status' => 'success',
            'success' => true
        ], 200);
    }

    public function getReferralLink()
    {
        $user = auth()->user();
        $user_id = $user->user_id;

        // dd($user);

        $member = Customer::where('user_id', $user_id)->first();


        $referral_link = $member->ref_link;

        return response()->json([
            'ref_link' => $referral_link,
            'status' => 'success',
            'success' => true
        ], 200);
    }

    public function getLikeCount(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required'
        ]);

        $likeCount = CustomerLike::where('unique_id', $request['user_id'])->get()->count();

        return response()->json([
            'likes' => $likeCount,
            'status' => 'success',
            'success' => true
        ], 201);
    }

    public function memberMyPurchase($user_id)
    {
        $to_pay = 0;
        $to_ship = 0;
        $to_receive = 0;
        $delivered = 0;

        return response()->json([
            'to_pay' => $to_pay,
            'to_ship' => $to_ship,
            'to_receive' => $to_receive,
            'delivered' => $delivered,
            'status' => 'success',
            'success' => true
        ], 201);
    }
}
