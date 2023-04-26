<?php

namespace App\Console\Commands\WB;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Predis\Command\Traits\DB;

class WbPrepare extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $rows = \Illuminate\Support\Facades\DB::table('general')->get();

        foreach ($rows as $row) {

            \Illuminate\Support\Facades\DB::table('general')->where('rrd_id', $row->rrd_id)
                ->update([
                    'date_from' => Carbon::parse($row->date_from)->format('Y-m-d H:i:s'),
                    'date_to'   => Carbon::parse($row->date_to)->format('Y-m-d H:i:s'),
                    'create_dt' => Carbon::parse($row->create_dt)->format('Y-m-d H:i:s'),
                    'order_dt'  => Carbon::parse($row->order_dt)->format('Y-m-d H:i:s'),
                    'sale_dt'   => Carbon::parse($row->sale_dt)->format('Y-m-d H:i:s'),
                    'rr_dt'     => Carbon::parse($row->rr_dt)->format('Y-m-d H:i:s'),
                ]);
        }

        return Command::SUCCESS;
    }
}
