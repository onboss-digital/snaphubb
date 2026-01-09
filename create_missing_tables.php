<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = [
    'entertainment_downloads',
    'entertainment_views',
    'entertainment_favorites',
    'watch_history'
];

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) {
        echo "Criando tabela: $table\n";
        
        if ($table === 'entertainment_downloads') {
            Schema::create($table, function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('entertainment_id')->nullable();
                $table->unsignedBigInteger('season_id')->nullable();
                $table->unsignedBigInteger('episode_id')->nullable();
                $table->softDeletes();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
        
        if ($table === 'entertainment_views') {
            Schema::create($table, function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('entertainment_id')->nullable();
                $table->unsignedBigInteger('season_id')->nullable();
                $table->unsignedBigInteger('episode_id')->nullable();
                $table->softDeletes();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
        
        if ($table === 'entertainment_favorites') {
            Schema::create($table, function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('entertainment_id')->nullable();
                $table->softDeletes();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
        
        if ($table === 'watch_history') {
            Schema::create($table, function ($table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('entertainment_id')->nullable();
                $table->unsignedBigInteger('episode_id')->nullable();
                $table->bigInteger('watch_time')->default(0);
                $table->softDeletes();
                $table->timestamps();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
        
        echo "✓ Tabela criada: $table\n";
    } else {
        echo "✓ Tabela já existe: $table\n";
    }
}

echo "\nDone!\n";
