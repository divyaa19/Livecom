<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        OrderStatus::truncate();

        $OrderStatus =  [
            [
                'language_id' => 1,
                'name' => 'Cancelled',
                'sort_order' => 0
            ],
            [
                'language_id' => 1,
                'name' => 'To Ship',
                'sort_order' => 0
            ],
            [
                'language_id' => 1,
                'name' => 'Shipped Out',
                'sort_order' => 0
            ],
            [
                'language_id' => 1,
                'name' => 'Paid',
                'sort_order' => 0
            ],
            [
                'language_id' => 1,
                'name' => 'To Receive',
                'sort_order' => 0
            ],
            [
                'language_id' => 1,
                'name' => 'Delivered',
                'sort_order' => 0
            ],
            [
                'language_id' => 1,
                'name' => 'Unpaid',
                'sort_order' => 0
            ],
            [
                'language_id' => 1,
                'name' => 'Forfeited',
                'sort_order' => 0
            ],
            [
                'language_id' => 1,
                'name' => 'Processing Refund',
                'sort_order' => 0
            ],
            [
                'language_id' => 1,
                'name' => 'Refunded',
                'sort_order' => 0
            ]
        ];
        OrderStatus::insert($OrderStatus);
    }
}
