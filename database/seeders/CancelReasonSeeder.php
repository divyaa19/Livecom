<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\CancelOrderReasons;

class CancelReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CancelOrderReasons::truncate();

        $Question =  [
            [
                'cancel_reason'=> "Need to change delivery address",
                'created_at'=>Carbon::now()
            ],
            [
                'cancel_reason'=> 'Seller is not responsive to my inquiries',
                'created_at'=>Carbon::now()
            ],
            [
                'cancel_reason'=> 'Modify existing order(colour,size,etc.)',
                'created_at'=>Carbon::now()
            ],
            [
                'cancel_reason'=> "Other/change of mind",
                'created_at'=>Carbon::now()
            ] 
          ];

          CancelOrderReasons::insert($Question);
    }
}
