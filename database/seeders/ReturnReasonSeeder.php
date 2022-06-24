<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\ReturnReason;

class ReturnReasonSeeder extends Seeder
{
     /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReturnReason::truncate();

        $reasons =  [
            [
                'return_reason'=> "Did not receive the full order (all items in the order)",
                'created_at'=>Carbon::now()
            ],
            [
                'refund_status'=> "Did not receive part of the order (e.g. missing part(s) of item, missing part(s) of the order)",
                'created_at'=>Carbon::now()
            ],
            [
                'refund_status'=> "Received the wrong product(s) (seller sent me a wrong product/variation)",
                'created_at'=>Carbon::now()
            ],
            [
                'refund_status'=> "Received a product with physical damage (e.g. dented,scratched,shattered)",
                'created_at'=>Carbon::now()
            ],
            [
                'refund_status'=> "Received a faulty product (e.g. malfunction, does not work as intended)",
                'created_at'=>Carbon::now()
            ]
          ];

          ReturnReason::insert($reasons);
    }
}