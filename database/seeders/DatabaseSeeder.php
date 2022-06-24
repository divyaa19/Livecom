<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ReloadItemSeeder::class,
            BankListSeeder::class,
            LeaveReasonSeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            OrderStatusSeeder::class,
            CancelReasonSeeder::class,
            RefundStatusSeeder::class,
            ReturnReasonSeeder::class,
            BidSeeder::class,
            SellingSeeder::class,
            CourierSeeder::class,
        ]);
    }
}
