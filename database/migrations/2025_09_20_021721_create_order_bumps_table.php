<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_bumps', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->string('title');
            $table->string('text_button')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('plan_id')
                  ->constrained('plan')
                  ->onDelete('cascade'); // Se excluir plano, exclui os bumps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_bumps');
    }
};
