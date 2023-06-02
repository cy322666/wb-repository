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

            DB::statement("ALTER TABLE general ALTER delivery_rub TYPE DOUBLE PRECISION USING delivery_rub::double precision");

            DB::statement("ALTER TABLE general ALTER sale_dt TYPE TIMESTAMP(0) WITHOUT TIME ZONE USING sale_dt::timestamp(0) without time zone");
            DB::statement("ALTER TABLE general ALTER rr_dt TYPE TIMESTAMP(0) WITHOUT TIME ZONE USING rr_dt::timestamp(0) without time zone");
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
