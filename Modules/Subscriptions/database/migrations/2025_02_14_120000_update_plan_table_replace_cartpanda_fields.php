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
        Schema::table('plan', function (Blueprint $table) {
            // Adicionar novos campos
            $table->string('language')->nullable()->after('currency');
            $table->enum('custom_gateway', ['CartPanda', 'For4pay', 'TriboPay'])->nullable()->after('language');
            $table->string('external_product_id')->nullable()->after('custom_gateway');
            $table->string('external_url')->nullable()->after('external_product_id');
            
            // Remover campos antigos do CartPanda
            $table->dropColumn(['cartpanda_active', 'cartpanda_product_id', 'cartpanda_checkout_url']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan', function (Blueprint $table) {
            // Remover novos campos
            $table->dropColumn(['language', 'custom_gateway', 'external_product_id', 'external_url']);
            
            // Restaurar campos antigos do CartPanda
            $table->boolean('cartpanda_active')->default(false);
            $table->string('cartpanda_product_id')->nullable();
            $table->string('cartpanda_checkout_url')->nullable();
        });
    }
};
