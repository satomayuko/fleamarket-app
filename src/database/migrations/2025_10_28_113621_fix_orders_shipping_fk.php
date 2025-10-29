<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixOrdersShippingFk extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_shipping_address_id_foreign');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('shipping_address_id')
                ->references('id')
                ->on('item_addresses')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['shipping_address_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('shipping_address_id')
                ->references('id')
                ->on('addresses')
                ->onDelete('cascade');
        });
    }
}