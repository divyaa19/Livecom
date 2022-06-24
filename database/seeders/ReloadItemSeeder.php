<?php

namespace Database\Seeders;

use App\Models\ReloadItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReloadItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReloadItem::truncate();

        $reloads = [
            [
                'value' => 20,
                'price' => 19.90,
                'status' => 'active',
                'created_at' => Carbon::now()
            ],
            [
                'value' => 60,
                'price' => 59.90,
                'status' => 'active',
                'created_at' => Carbon::now()
            ],
            [
                'value' => 100,
                'price' => 99.90,
                'status' => 'active',
                'created_at' => Carbon::now()
            ],
            [
                'value' => 500,
                'price' => 499.90,
                'status' => 'active',
                'created_at' => Carbon::now()
            ],
            [
                'value' => 1000,
                'price' => 999.90,
                'status' => 'active',
                'created_at' => Carbon::now()
            ]
        ];

        ReloadItem::insert($reloads);
    }
}
