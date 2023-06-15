<?php

namespace App\Console\Commands\WB;

use App\Jobs\WB\WbIncomesJob;
use App\Models\Account;
use App\Models\WB\WbIncome;
use App\Models\WB\WbSalesReport;
use App\Services\WB\Wildberries;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as CommandAlias;

class WbIncomesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wb:incomes {account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Получение поставок и обновление сущности 'поставки'";

    private static int $limit = 100_000;//100_000
    /**
     * Execute the console command.
     *
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        $account = Account::query()->find($this->argument('account'));

        $wbApi = (new Wildberries([
            'standard'  => $account->token_standard,
            'statistic' => $account->token_statistic,
        ]));

        $dateAt = Carbon::create(2022, 10, 1); //Carbon::parse($lastModel->rr_dt);
        $dateTo = Carbon::now();

        $salesReportsResponse = $wbApi->getSupplierReportDetailByPeriod(
            $dateAt,
            $dateTo,
            $account->token_adv,//rrd_id
            static::$limit,
        );

        $reports = json_decode($salesReportsResponse->getBody()->getContents(), true);

        $wbSalesReports = array_map(
            fn ($salesReport) => [
                'realizationreport_id'  => $salesReport['realizationreport_id'],
                'date_from'             => $salesReport['date_from'],
                'date_to'               => $salesReport['date_to'],
                'create_dt'             => $salesReport['create_dt'],
                'suppliercontract_code' => $salesReport['suppliercontract_code'],
                'rrd_id'                => $salesReport['rrd_id'],
                'gi_id'                 => $salesReport['gi_id'],
                'subject_name'          => $salesReport['subject_name'],
                'nm_id'         => $salesReport['nm_id'],
                'brand_name'    => $salesReport['brand_name'],
                'sa_name'       => $salesReport['sa_name'],
                'ts_name'       => $salesReport['ts_name'],
                'barcode'       => $salesReport['barcode'],
                'doc_type_name' => $salesReport['doc_type_name'],
                'quantity'      => $salesReport['quantity'],
                'retail_price'  => $salesReport['retail_price'],
                'retail_amount' => $salesReport['retail_amount'],
                'sale_percent'  => $salesReport['sale_percent'],
                'commission_percent' => $salesReport['commission_percent'],

                'office_name'        => $salesReport['office_name'],
                'supplier_oper_name' => $salesReport['supplier_oper_name'],
                'order_dt'           => $salesReport['order_dt'],
                'sale_dt'            => $salesReport['sale_dt'],
                'rr_dt'              => $salesReport['rr_dt'],
                'shk_id'             => $salesReport['shk_id'],
                'retail_price_withdisc_rub' => $salesReport['retail_price_withdisc_rub'],
                'delivery_amount'       => $salesReport['delivery_amount'],
                'return_amount'         => $salesReport['return_amount'],
                'delivery_rub'          => $salesReport['delivery_rub'],
                'gi_box_type_name'      => $salesReport['gi_box_type_name'],
                'product_discount_for_report' => $salesReport['product_discount_for_report'],
                'supplier_promo'        => $salesReport['supplier_promo'],
                'rid'                   => $salesReport['rid'],

                'ppvz_spp_prc'          => $salesReport['ppvz_spp_prc'] ?? null,
                'ppvz_kvw_prc_base'     => $salesReport['ppvz_kvw_prc_base'] ?? null,
                'ppvz_kvw_prc'          => $salesReport['ppvz_kvw_prc'] ?? null,
                'ppvz_sales_commission' => $salesReport['ppvz_sales_commission'] ?? null,
                'ppvz_for_pay'          => $salesReport['ppvz_for_pay'] ?? null,
                'ppvz_reward'           => $salesReport['ppvz_reward'] ?? null,
                'acquiring_fee'         => $salesReport['acquiring_fee'] ?? null,
                'acquiring_bank'        => $salesReport['acquiring_bank'] ?? null,
                'ppvz_vw'               => $salesReport['ppvz_vw'] ?? null,
                'ppvz_vw_nds'           => $salesReport['ppvz_vw_nds'] ?? null,
                'ppvz_office_id'        => $salesReport['ppvz_office_id'] ?? null,
                'ppvz_office_name'      => $salesReport['ppvz_office_name'] ?? null,
                'ppvz_supplier_id'      => $salesReport['ppvz_supplier_id'] ?? null,
                'ppvz_supplier_name'    => $salesReport['ppvz_supplier_name'] ?? null,
                'ppvz_inn'              => $salesReport['ppvz_inn'] ?? null,

                'declaration_number' => $salesReport['declaration_number'] ?? null,
                'bonus_type_name'    => $salesReport['bonus_type_name'] ?? '',
                'sticker_id'         => $salesReport['sticker_id'],
                'site_country'       => $salesReport['site_country'],
                'penalty'            => $salesReport['penalty'],
                'additional_payment' => $salesReport['additional_payment'],
                'srid'               => $salesReport['srid'],
            ],
            $reports
        );

        foreach ($wbSalesReports as $salesReport) {

            $double = WbIncome::query()
                ->where('rrd_id', $salesReport['rrd_id'])
                ->where('rr_dt', '!=', $salesReport['rr_dt'])
                ->first();

            if ($double) {

            dd($double->toArray(), $salesReport);
                $double->fill($salesReport);
                $double->save();

                dump('double : '.$salesReport['rrd_id']);

            } else {
                try {
                    dd($salesReport);
                    WbIncome::query()->create($salesReport);

                    dump('created : '.$salesReport['rrd_id']);

                } catch (\Throwable $e) {

                    continue;
                }
            }
        }

        return CommandAlias::SUCCESS;
    }
}
