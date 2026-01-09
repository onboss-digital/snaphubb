<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_purchased_bumps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bump_id')->constrained('order_bumps')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->constrained('plan')->onDelete('set null');
            $table->timestamp('purchased_at')->useCurrent();
            $table->timestamps();
            
            // Índice para buscar bumps do usuário rapidamente
            $table->unique(['user_id', 'bump_id']);
            $table->index('user_id');
            $table->index('bump_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_purchased_bumps');
    }
};
