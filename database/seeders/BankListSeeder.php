<?php

namespace Database\Seeders;

use App\Models\BankList;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BankListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BankList::truncate();

        $Banklist = [
            [
                'bank_name' => 'AFFIN BANK BERHAD / AFFIN ISLAMIC BANK',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'AL-RAJHI BANKING & INVESTMENT COPR (M) BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'ALLIANCE BANK MALAYSIA BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'AMBANK BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'BANK ISLAM MALAYISA',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'BANK KERJASAMA RAKYAT MALAYSIA BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'BANK MUAMALAT',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'BANK OF AMERICA',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'BANK OF CHINA (MALAYSIA) BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'BANK PERTANIAN MALAYSIA BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'BANK SIMPANAN NASIONAL BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'BNP PARIBAS MALAYSIA',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'BANGKOK BANK BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'CHINA CONST BK (M) BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'CIMB BANK BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'CITIBANK BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'DEUTSCHE BANK (MSIA) BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'HONG LEONG BANK',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'HSBC BANK MALAYSIA BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'INDUSTRIAL & COMMERCIAL BANK OF CHINA',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'J.P. MORGAN CHASE BANK BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'KUWAIT FINANCE HOUSE (MALAYSIA) BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'MAYBANK',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'MBSB BANK',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'MIZUHO BANK (MALAYSIA) BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'MUFG BANK (MALAYSIA) BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'OCBC BANK (MALAYSIA) BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'PUBLIC BANK',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'RHB BANK',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'STANDARD CHARTERED BANK',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'SUMITOMO MITSUI BANKING CORPORATION MALAYSIA BERHAD',
                'created_at' => Carbon::now()
            ],
            [
                'bank_name' => 'UNITED OVERSEAS BANK BERHAD',
                'created_at' => Carbon::now()
            ],
        ];

        BankList::insert($Banklist);
    }
}
