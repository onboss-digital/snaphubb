<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('plan', function (Blueprint $table) {
            $table->boolean('cartpanda_active')->default(false);
            $table->string('cartpanda_product_id')->nullable();
            $table->string('cartpanda_checkout_url')->nullable();
        });
    }

    public function down()
    {
        Schema::table('plan', function (Blueprint $table) {
            $table->dropColumn('cartpanda_active');
            $table->dropColumn('cartpanda_product_id');
            $table->dropColumn('cartpanda_checkout_url');
        });
    }
};
