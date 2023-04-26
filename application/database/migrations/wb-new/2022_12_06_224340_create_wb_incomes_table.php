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
        Schema::create('wb_incomes', function (Blueprint $table) {
            $table->id();
//            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('income_id');
            $table->string('number', 40);
            $table->date('date');
            $table->dateTime('last_change_date', 3);
            $table->string('supplier_article', 75);
            $table->string('tech_size', 30);
            $table->string('barcode', 30);
            $table->integer('quantity');
            $table->float('total_price');
            $table->date('date_close');
            $table->string('warehouse_name', 50);
            $table->unsignedBigInteger('nm_id');
            $table->string('status', 50);
            $table->timestamps();

            $table->unique(['income_id', 'date', 'last_change_date', 'barcode', 'status'], 'wb_incomes_unique');

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
        Schema::dropIfExists('wb_incomes');
    }
};
