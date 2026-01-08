<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = [
    'entertainments' => function($table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->string('type')->default('movie'); // movie, series
        $table->integer('status')->default(1);
        $table->string('poster')->nullable();
        $table->string('thumbnail')->nullable();
        $table->decimal('rating', 3, 1)->nullable();
        $table->integer('release_year')->nullable();
        $table->softDeletes();
        $table->timestamps();
    },
    'live_tv' => function($table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->string('thumbnail')->nullable();
        $table->string('stream_url')->nullable();
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
    },
    'videos' => function($table) {
        $table->id();
        $table->unsignedBigInteger('entertainment_id')->nullable();
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('video_url')->nullable();
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
        $table->foreign('entertainment_id')->references('id')->on('entertainments')->onDelete('cascade');
    },
    'video_stream_content_mapping' => function($table) {
        $table->id();
        $table->unsignedBigInteger('video_id')->nullable();
        $table->string('stream_content_id')->nullable();
        $table->softDeletes();
        $table->timestamps();
        $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
    },
    'video_download_mappings' => function($table) {
        $table->id();
        $table->unsignedBigInteger('video_id')->nullable();
        $table->string('download_url')->nullable();
        $table->softDeletes();
        $table->timestamps();
        $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
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
