<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:prepare2';

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
        $baseMemory = memory_get_usage();

        $rows = DB::table('general')
//            ->where('updated_at', null)
            ->select('*')
            ->orderBy('updated_at')
            ->get();
//dd($rows->count());
        $i = 1;

        foreach ($rows as $row) {
            $i++;

            $array = [

            ];

            if (strripos($row->delivery_rub, ',') !== false) {
                $array['delivery_rub'] = str_replace(',', '.', $row->delivery_rub);
            }
            if (strripos($row->delivery_rub, ' ') !== false) {
                $array['delivery_rub'] = str_replace(' ', '', $row->delivery_rub);
            }

            if (count($array) > 0) {

                $array['updated_at'] = Carbon::now();

                DB::table('general')->where('id', $row->id)->update($array);
            }

            $i++;

            dump($i . ' : ' . $row->id . ' => ' . memory_get_usage() - $baseMemory);

            unset($array);
            unset($row);
        }
    }
}
