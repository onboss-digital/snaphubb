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
            // Alterar o enum para incluir 'Stripe'
            $table->enum('custom_gateway', ['CartPanda', 'For4pay', 'TriboPay', 'Stripe'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan', function (Blueprint $table) {
            // Reverter para o enum original
            $table->enum('custom_gateway', ['CartPanda', 'For4pay', 'TriboPay'])->nullable()->change();
        });
    }
};
