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
//            ->where('updated_at', null)
            ->select('*')
            ->orderBy('updated_at')
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
            if (strripos($row->delivery_rub, ',') !== false) {

                $array['delivery_rub'] = str_replace(',', '.', $row->delivery_rub);
            }
            if (strripos($row->delivery_rub, ' ') !== false) {

                $array['delivery_rub'] = str_replace(' ', '', $row->delivery_rub);
            }
            if (strripos($row->commission_percent, ',') !== false) {

                $array['commission_percent'] = str_replace(',', '.', $row->commission_percent);
            }
            if (strripos($row->ppvz_for_pay, ',') !== false) {

                $array['ppvz_for_pay'] = str_replace(',', '.', $row->ppvz_for_pay);
            }
            if (strripos($row->ppvz_reward, ',') !== false) {

                $array['ppvz_reward'] = str_replace(',', '.', $row->ppvz_reward);
            }
            if (strripos($row->acquiring_fee, ',') !== false) {

                $array['acquiring_fee'] = str_replace(',', '.', $row->acquiring_fee);
            }
            if (strripos($row->ppvz_vw, ',') !== false) {

                $array['ppvz_vw'] = str_replace(',', '.', $row->ppvz_vw);
            }
            if (strripos($row->ppvz_vw_nds, ',') !== false) {

                $array['ppvz_vw_nds'] = str_replace(',', '.', $row->ppvz_vw_nds);
            }
            if (strripos($row->penalty, ',') !== false) {

                $array['penalty'] = str_replace(',', '.', $row->penalty);
            }
            if (strripos($row->additional_payment, ',') !== false) {

                $array['additional_payment'] = str_replace(',', '.', $row->additional_payment);
            }
            if (strripos($row->ppvz_sales_commission, ',') !== false) {

                $array['ppvz_sales_commission'] = str_replace(',', '.', $row->ppvz_sales_commission);
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

            dump( $i.' : '.$row->id.' => '.memory_get_usage() - $baseMemory);

            unset($array);
            unset($row);
        }

        return CommandAlias::SUCCESS;
    }
}
