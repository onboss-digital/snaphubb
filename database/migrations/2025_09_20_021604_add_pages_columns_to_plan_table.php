<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plan', function (Blueprint $table) {
            $table->string('pages_product_external_id')->nullable()->after('external_url');
            $table->string('pages_upsell_url')->nullable()->after('pages_product_external_id');
            $table->string('pages_upsell_succes_url')->nullable()->after('pages_upsell_url');
            $table->string('pages_downsell_url')->nullable()->after('pages_upsell_succes_url');
            $table->string('pages_upsell_fail_url')->nullable()->after('pages_downsell_url');
        });
    }

    public function down(): void
    {
        Schema::table('plan', function (Blueprint $table) {
            $table->dropColumn([
                'pages_product_external_id',
                'pages_upsell_url',
                'pages_upsell_succes_url',
                'pages_downsell_url',
                'pages_upsell_fail_url',
            ]);
        });
    }
};
