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
        Schema::create('weekly_rankings', function (Blueprint $table) {
            $table->id();
            $table->string('week_id')->unique(); // Identificador da semana (YYYY-WW)
            $table->unsignedBigInteger('cast_crew_id');
            $table->integer('rank_position')->nullable(); // 1º, 2º, 3º lugar
            $table->integer('total_votes')->default(0);
            $table->decimal('percentage', 5, 2)->default(0); // Percentual de votos
            $table->date('week_start')->nullable();
            $table->date('week_end')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('week_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_rankings');
    }
};
