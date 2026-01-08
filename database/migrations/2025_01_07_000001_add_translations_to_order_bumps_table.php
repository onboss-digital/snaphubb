<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_bumps', function (Blueprint $table) {
            // English translations
            $table->string('title_en')->nullable()->after('title');
            $table->text('description_en')->nullable()->after('description');
            $table->string('text_button_en')->nullable()->after('text_button');
            
            // Spanish translations
            $table->string('title_es')->nullable()->after('title_en');
            $table->text('description_es')->nullable()->after('description_en');
            $table->string('text_button_es')->nullable()->after('text_button_en');
        });
    }

    public function down(): void
    {
        Schema::table('order_bumps', function (Blueprint $table) {
            $table->dropColumn([
                'title_en',
                'description_en',
                'text_button_en',
                'title_es',
                'description_es',
                'text_button_es',
            ]);
        });
    }
};
