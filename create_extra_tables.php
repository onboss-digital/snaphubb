<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = [
    'mobile_settings' => function($table) {
        $table->id();
        $table->string('slug')->unique();
        $table->text('value')->nullable();
        $table->softDeletes();
        $table->timestamps();
    },
    'settings' => function($table) {
        $table->id();
        $table->string('key')->unique();
        $table->text('value')->nullable();
        $table->softDeletes();
        $table->timestamps();
    },
    'faqs' => function($table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
    },
    'pages' => function($table) {
        $table->id();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('content')->nullable();
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
    },
    'media' => function($table) {
        $table->id();
        $table->unsignedBigInteger('model_id')->nullable();
        $table->string('model_type')->nullable();
        $table->string('name')->nullable();
        $table->string('file_name');
        $table->string('mime_type')->nullable();
        $table->bigInteger('size');
        $table->string('disk');
        $table->string('conversions_disk')->nullable();
        $table->text('custom_properties')->nullable();
        $table->softDeletes();
        $table->timestamps();
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
