<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDB1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:prepare3';

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

            if (strripos($row->retail_price, '-') !== false) {
                $array['retail_price'] = str_replace('-', '', $row->retail_price);
            }

            if (strripos($row->retail_amount, '-') !== false) {
                $array['retail_amount'] = str_replace('-', '', $row->retail_amount);
            }

            if (strripos($row->ppvz_sales_commission, '-') !== false) {
                $array['ppvz_sales_commission'] = str_replace('-', '', $row->ppvz_sales_commission);
            }

            if (strripos($row->retail_price_withdisc_rub, '-') !== false) {
                $array['retail_price_withdisc_rub'] = str_replace('-', '', $row->retail_price_withdisc_rub);
            }

            if ($row->date_from == '31.12.1969') {
                $array['date_to']   = Carbon::create(2022, 5, 6);
                $array['date_from'] = Carbon::create(2022, 5, 6)->subDays(6);

            } elseif ($row->date_to == $row->date_from) {

                $array['date_from'] = Carbon::parse($row->date_to)->subDays(6);
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
