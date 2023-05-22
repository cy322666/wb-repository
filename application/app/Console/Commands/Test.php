<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:prepare';

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
            ->where('updated_at', null)
            ->select('*')
//            ->orderByDesc('id')
            ->get();
//dd($rows->count());
        $i = 1;

        foreach ($rows as $row) {

            $i++;

            $array = [
                'date_from' => Carbon::parse($row->date_from)->format('Y-m-d H:i:s'),
                'date_to'   => Carbon::parse($row->date_to)->format('Y-m-d H:i:s'),
                'create_dt' => Carbon::parse($row->create_dt)->format('Y-m-d H:i:s'),
                'order_dt'  => Carbon::parse($row->order_dt)->format('Y-m-d H:i:s'),
                'sale_dt'   => Carbon::parse($row->sale_dt)->format('Y-m-d H:i:s'),
                'rr_dt'     => Carbon::parse($row->rr_dt)->format('Y-m-d H:i:s'),
            ];

            if ($row->Бренд == 'STEFRIMI') {

                $array['brand_name'] = 'STEFRIMI';
            } else {
                $array['brand_name'] = 'ZAFILINI';
            }

            if ($row->brand_name == 'Stefimi') {

                $array['brand_name'] = 'STEFRIMI';
            }

            if (strripos($row->retail_price, ',') !== false) {

                $array['retail_price'] = str_replace(',', '.', $row->retal_price);
            }

            if (strripos($row->retail_amount, ',') !== false) {

                $array['retail_amount'] = str_replace(',', '.', $row->retail_amount);
            }

            if (strripos($row->commission_percent, ',') !== false) {

                $array['commission_percent'] = str_replace(',', '.', $row->commission_percent);
            }

            if (strripos($row->retail_price_withdisc_rub, ',') !== false) {

                $array['retail_price_withdisc_rub'] = str_replace(',', '.', $row->retail_price_withdisc_rub);
            }

            if (strripos($row->product_discount_for_report, ',') !== false) {

                $array['product_discount_for_report'] = str_replace(',', '.', $row->product_discount_for_report);
            }

            if (strripos($row->supplier_promo, ',') !== false) {

                $array['supplier_promo'] = str_replace(',', '.', $row->supplier_promo);
            }

            if (strripos($row->ppvz_spp_prc, ',') !== false) {

                $array['ppvz_spp_prc'] = str_replace(',', '.', $row->ppvz_spp_prc);
            }

            if (strripos($row->ppvz_kvw_prc_base, ',') !== false) {

                $array['ppvz_kvw_prc_base'] = str_replace(',', '.', $row->ppvz_kvw_prc_base);
            }

            if (strripos($row->ppvz_kvw_prc, ',') !== false) {

                $array['ppvz_kvw_prc'] = str_replace(',', '.', $row->ppvz_kvw_prc);
            }

            if (count($array) > 0) {

                $array['updated_at'] = Carbon::now();

                DB::table('general')->where('id', $row->id)->update($array);
            }

            $i++;

            unset($array);

            dump( $i.' => '.memory_get_usage() - $baseMemory);
        }

        return CommandAlias::SUCCESS;
    }
}