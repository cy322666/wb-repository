<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDB2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:prepare4';

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
        $rows = DB::table('general')
//            ->where('updated_at', null)
            ->select('*')
            ->orderBy('updated_at')
            ->get();

        $i = 1;

        foreach ($rows as $row) {
            $i++;

            $array = [
                'rr_dt' => Carbon::parse($row->rr_dt)->format('Y-m-d H:i:s'),
            ];

            if (strripos($row->sale_percent, '-') !== false) {
                $array['sale_percent'] = str_replace('-', '', $row->sale_percent);
            }

            if (count($array) > 0) {

                $array['updated_at'] = Carbon::now();

                DB::table('general')->where('id', $row->id)->update($array);
            }

            $i++;

            dump($i . ' : ' . $row->id);

            unset($array);
            unset($row);
        }
    }
}
