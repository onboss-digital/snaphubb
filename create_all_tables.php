<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$tables = [
    'banners' => function($table) {
        $table->id();
        $table->string('title')->nullable();
        $table->string('description')->nullable();
        $table->string('image')->nullable();
        $table->string('url')->nullable();
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
    },
    'genres' => function($table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
    },
    'entertainment' => function($table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->string('type')->default('movie'); // movie, series
        $table->integer('status')->default(1);
        $table->string('poster')->nullable();
        $table->string('thumbnail')->nullable();
        $table->softDeletes();
        $table->timestamps();
    },
    'seasons' => function($table) {
        $table->id();
        $table->unsignedBigInteger('entertainment_id');
        $table->string('name');
        $table->integer('season_number');
        $table->text('description')->nullable();
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
        $table->foreign('entertainment_id')->references('id')->on('entertainment')->onDelete('cascade');
    },
    'episodes' => function($table) {
        $table->id();
        $table->unsignedBigInteger('season_id');
        $table->string('name');
        $table->integer('episode_number');
        $table->text('description')->nullable();
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
        $table->foreign('season_id')->references('id')->on('seasons')->onDelete('cascade');
    },
    'cast_crew' => function($table) {
        $table->id();
        $table->string('name');
        $table->string('type'); // actor, director, etc
        $table->string('image')->nullable();
        $table->softDeletes();
        $table->timestamps();
    },
    'notifications' => function($table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('type');
        $table->text('message');
        $table->softDeletes();
        $table->timestamps();
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    },
];

foreach ($tables as $tableName => $callback) {
    if (!Schema::hasTable($tableName)) {
        echo "Criando tabela: $tableName\n";
        Schema::create($tableName, $callback);
        echo "✓ Tabela criada: $tableName\n";
    } else {
        echo "✓ Tabela já existe: $tableName\n";
    }
}

echo "\nDone!\n";
