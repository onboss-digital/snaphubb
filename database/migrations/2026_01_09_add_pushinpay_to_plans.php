<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plan', function (Blueprint $table) {
            // Adicionar suporte a Stripe e Push in Pay
            $table->string('stripe_product_id')->nullable();
            $table->string('pushinpay_product_id')->nullable();
            $table->json('gateway_config')->nullable();
            
            // Index para melhor performance
            $table->index('pushinpay_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan', function (Blueprint $table) {
            $table->dropIndexIfExists('plan_pushinpay_product_id_index');
            $table->dropColumnIfExists('stripe_product_id');
            $table->dropColumnIfExists('pushinpay_product_id');
            $table->dropColumnIfExists('gateway_config');
        });
    }
};
