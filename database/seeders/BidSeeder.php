<?php

namespace Database\Seeders;

use App\Models\BidType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BidType::truncate();

        $Bid = [
            [
                'bid_type' => "Auction High",
                'created_at' => Carbon::now()
            ],
            [
                'bid_type' => "Auction Low",
                'created_at' => Carbon::now()
            ],
            [
                'bid_type' => "E-commerce",
                'created_at' => Carbon::now()
            ],
            [
                'bid_type' => "Lucky Draw",
                'created_at' => Carbon::now()
            ],
        ];

        BidType::insert($Bid);
    }
}
