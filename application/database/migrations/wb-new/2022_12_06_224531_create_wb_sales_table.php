<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('wb_sales', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('account_id');
            $table->string('g_number', 50);
            $table->date('date');
            $table->dateTime('last_change_date');
            $table->string('supplier_article', 75);
            $table->string('tech_size', 30);
            $table->string('barcode', 30);
            $table->float('total_price');
            $table->integer('discount_percent');
            $table->boolean('is_supply');
            $table->boolean('is_realization');
            $table->float('promo_code_discount');
            $table->string('warehouse_name', 50);
            $table->string('country_name', 200);
            $table->string('oblast_okrug_name', 200);
            $table->string('region_name', 200);
            $table->unsignedBigInteger('income_id');
            $table->string('sale_id', 15);
            $table->string('sale_id_status');
            $table->unsignedBigInteger('odid');
            $table->float('spp');
            $table->float('for_pay');
            $table->float('finished_price');
            $table->float('price_with_disc');
            $table->unsignedBigInteger('nm_id');
            $table->string('subject', 50);
            $table->string('category', 50);
            $table->string('brand', 50);
            $table->integer('is_storno');
            $table->string('sticker');
            $table->string('srid');
            $table->timestamps();

            $table->unique(['date', 'last_change_date', 'barcode', 'sale_id', 'odid', 'g_number'], 'wb_sales_unique');

//            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wb_sales');
    }
};
