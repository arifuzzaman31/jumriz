<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_costs', function (Blueprint $table) {
            $table->Increments('id');
            $table->double('minimum_order_amount')->nullable();
            $table->double('shipping_amount')->default(0);
            $table->double('order_amount');
            $table->double('discount_amount');
            $table->double('vat_gst_amount')->default(0);
            $table->tinyInteger('shipping_status')->default(0);
            $table->tinyInteger('vat_gst_status')->default(0);
            $table->tinyInteger('discount_status')->default(0);
            $table->timestamps();
        });
    }
    // ALTER TABLE `shipping_costs` ADD COLUMN `vat_gst_status` TINYINT(4) NOT NULL DEFAULT 0;
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_costs');
    }
}
