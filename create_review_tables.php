<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = [
    'reviews' => function($table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->unsignedBigInteger('entertainment_id')->nullable();
        $table->text('comment')->nullable();
        $table->decimal('rating', 3, 1)->nullable();
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('entertainment_id')->references('id')->on('entertainments')->onDelete('cascade');
    },
    'languages' => function($table) {
        $table->id();
        $table->string('name');
        $table->string('code')->unique();
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
    },
    'activity_log' => function($table) {
        $table->id();
        $table->string('log_name')->nullable();
        $table->text('description');
        $table->string('subject_type')->nullable();
        $table->unsignedBigInteger('subject_id')->nullable();
        $table->string('event')->nullable();
        $table->string('causer_type')->nullable();
        $table->unsignedBigInteger('causer_id')->nullable();
        $table->text('properties')->nullable();
        $table->string('batch_uuid')->nullable();
        $table->timestamps();
    },
    'social_logins' => function($table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('provider');
        $table->string('provider_id');
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
