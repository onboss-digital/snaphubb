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
        Schema::table('ranking_responses', function (Blueprint $table) {
            // Adicionar coluna content_slug se não existir
            if (!Schema::hasColumn('ranking_responses', 'content_slug')) {
                $table->string('content_slug')->nullable()->after('ranking_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ranking_responses', function (Blueprint $table) {
            if (Schema::hasColumn('ranking_responses', 'content_slug')) {
                $table->dropColumn('content_slug');
            }
        });
    }
};
