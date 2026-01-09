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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cast_crew_id'); // A pessoa votada
            $table->string('week_id')->nullable(); // Identificador da semana (YYYY-WW)
            $table->integer('vote_count')->default(1); // Quantidade de votos do usuário
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Cast crew foreign key - usar constrained para auto-namespacing
            $table->unique(['user_id', 'cast_crew_id', 'week_id'], 'unique_user_cast_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
