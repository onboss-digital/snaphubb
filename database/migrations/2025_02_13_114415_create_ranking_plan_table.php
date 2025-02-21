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
        Schema::create('ranking_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ranking_id')->constrained('rankings')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plan')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking_plan');
    }
};
