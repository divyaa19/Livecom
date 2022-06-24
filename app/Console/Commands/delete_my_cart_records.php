<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class delete_my_cart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete_my_cart_records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete my cart records that is over 24 hours';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('oc_cart')->where('date_added', '<=', Carbon::now()->subDay())->delete();
    }
}
