<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDB3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:prepare5';

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

            $array = [];

            if (strripos($row->delivery_rub, ',') !== false) {
                $array['delivery_rub'] = str_replace(',', '.', $row->delivery_rub);
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
