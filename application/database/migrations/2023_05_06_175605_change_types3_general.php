<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general', function (Blueprint $table) {
            $table->float('delivery_rub')->change();
            $table->float('retail_amount')->change();
            $table->float('commission_percent')->change();
            $table->float('retail_price_withdisc_rub')->change();
            $table->float('product_discount_for_report')->change();
            $table->float('supplier_promo')->change();
            $table->float('ppvz_spp_prc')->change();
            $table->float('ppvz_kvw_prc_base')->change();
            $table->float('ppvz_kvw_prc')->change();
            $table->float('ppvz_sales_commission')->change();
            $table->float('ppvz_for_pay')->change();
            $table->float('ppvz_reward')->change();
            $table->float('acquiring_fee')->change();
            $table->float('ppvz_vw')->change();
            $table->float('ppvz_vw_nds')->change();
            $table->float('penalty')->change();
            $table->float('additional_payment')->change();
        });

        DB::statement("ALTER TABLE general ALTER order_dt TYPE TIMESTAMP(0) WITHOUT TIME ZONE USING date_from::timestamp(0) without time zone");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
