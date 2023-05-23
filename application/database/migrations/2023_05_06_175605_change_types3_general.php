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
        DB::statement("ALTER TABLE general ALTER delivery_rub TYPE DOUBLE PRECISION USING delivery_rub::double precision");
        DB::statement("ALTER TABLE general ALTER retail_amount TYPE DOUBLE PRECISION USING retail_amount::double precision");
        DB::statement("ALTER TABLE general ALTER commission_percent TYPE DOUBLE PRECISION USING commission_percent::double precision");
        DB::statement("ALTER TABLE general ALTER retail_price_withdisc_rub TYPE DOUBLE PRECISION USING retail_price_withdisc_rub::double precision");
        DB::statement("ALTER TABLE general ALTER product_discount_for_report TYPE DOUBLE PRECISION USING product_discount_for_report::double precision");
        DB::statement("ALTER TABLE general ALTER supplier_promo TYPE DOUBLE PRECISION USING supplier_promo::double precision");
        DB::statement("ALTER TABLE general ALTER ppvz_spp_prc TYPE DOUBLE PRECISION USING ppvz_spp_prc::double precision");
        DB::statement("ALTER TABLE general ALTER ppvz_kvw_prc_base TYPE DOUBLE PRECISION USING ppvz_kvw_prc_base::double precision");
        DB::statement("ALTER TABLE general ALTER ppvz_kvw_prc TYPE DOUBLE PRECISION USING ppvz_kvw_prc::double precision");
        DB::statement("ALTER TABLE general ALTER ppvz_sales_commission TYPE DOUBLE PRECISION USING ppvz_sales_commission::double precision");
        DB::statement("ALTER TABLE general ALTER ppvz_for_pay TYPE DOUBLE PRECISION USING ppvz_for_pay::double precision");
        DB::statement("ALTER TABLE general ALTER ppvz_reward TYPE DOUBLE PRECISION USING ppvz_reward::double precision");
        DB::statement("ALTER TABLE general ALTER acquiring_fee TYPE DOUBLE PRECISION USING acquiring_fee::double precision");
        DB::statement("ALTER TABLE general ALTER ppvz_vw TYPE DOUBLE PRECISION USING ppvz_vw::double precision");
        DB::statement("ALTER TABLE general ALTER ppvz_vw_nds TYPE DOUBLE PRECISION USING ppvz_vw_nds::double precision");
        DB::statement("ALTER TABLE general ALTER penalty TYPE DOUBLE PRECISION USING penalty::double precision");
        DB::statement("ALTER TABLE general ALTER additional_payment TYPE DOUBLE PRECISION USING additional_payment::double precision");
        DB::statement("ALTER TABLE general ALTER order_dt TYPE TIMESTAMP(0) WITHOUT TIME ZONE USING order_dt::timestamp(0) without time zone");
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
