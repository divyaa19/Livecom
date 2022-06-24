<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\RefundStatus;

class RefundStatusSeeder extends Seeder
{
     /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RefundStatus::truncate();

        $Status =  [
            [
                'refund_status'=> "pending",
                'created_at'=>Carbon::now()
            ],
            [
                'refund_status'=> "success",
                'created_at'=>Carbon::now()
            ],
            [
                'refund_status'=> "failed",
                'created_at'=>Carbon::now()
            ],
            [
                'refund_status'=> "payment transferred",
                'created_at'=>Carbon::now()
            ]
          ];

          RefundStatus::insert($Status);
    }
}