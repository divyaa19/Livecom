<?php

namespace Database\Seeders;

use App\Models\ReportReason;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReportReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReportReason::truncate();

        $ReportList = [
            [
                'reason' => 'Seller is rude or indecent',
                'status' => 'active',
                'created_by' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'reason' => 'Counterfeit items',
                'status' => 'active',
                'created_by' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'reason' => 'Suspect fraudulent activities',
                'status' => 'active',
                'created_by' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'reason' => 'Violence or dangerous organisations',
                'status' => 'active',
                'created_by' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'reason' => 'Bullying or harrasment',
                'status' => 'active',
                'created_by' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'reason' => 'Other reason',
                'status' => 'active',
                'created_by' => '',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        ReportReason::insert($ReportList);
    }
}
