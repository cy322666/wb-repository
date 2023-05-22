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
        DB::statement("ALTER TABLE general ALTER date_to TYPE TIMESTAMP(0) WITHOUT TIME ZONE USING date_from::timestamp(0) without time zone");
        DB::statement("ALTER TABLE general ALTER create_dt TYPE TIMESTAMP(0) WITHOUT TIME ZONE USING date_from::timestamp(0) without time zone");
        DB::statement("ALTER TABLE general ALTER date_from TYPE TIMESTAMP(0) WITHOUT TIME ZONE USING date_from::timestamp(0) without time zone");
        DB::statement("ALTER TABLE general ALTER ppvz_kvw_prc TYPE DOUBLE PRECISION USING ppvz_kvw_prc::double precision");
        DB::statement("ALTER TABLE general ALTER retail_amount TYPE DOUBLE PRECISION USING retail_amount::double precision");
        DB::statement("ALTER TABLE general ALTER commission_percent TYPE DOUBLE PRECISION USING commission_percent::double precision");
        DB::statement("ALTER TABLE general ALTER retail_price_withdisc_rub TYPE DOUBLE PRECISION USING retail_price_withdisc_rub::double precision");
        DB::statement("ALTER TABLE general ALTER ppvz_spp_prc TYPE DOUBLE PRECISION USING ppvz_spp_prc::double precision");
        DB::statement("ALTER TABLE general ALTER ppvz_kvw_prc_base TYPE DOUBLE PRECISION USING ppvz_kvw_prc_base::double precision");

        Schema::table('general', function (Blueprint $table) {

        });
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
