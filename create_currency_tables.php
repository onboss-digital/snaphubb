<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = [
    'currencies' => function($table) {
        $table->id();
        $table->string('name');
        $table->string('code')->unique();
        $table->string('symbol')->nullable();
        $table->decimal('rate', 10, 4)->default(1);
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
    },
    'taxes' => function($table) {
        $table->id();
        $table->string('name');
        $table->decimal('percentage', 5, 2);
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
    },
    'plan' => function($table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2)->default(0);
        $table->string('stripe_id')->nullable();
        $table->integer('duration_days')->default(30);
        $table->integer('status')->default(1);
        $table->softDeletes();
        $table->timestamps();
    },
    'subscriptions' => function($table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('plan_id');
        $table->timestamp('start_date')->nullable();
        $table->timestamp('end_date')->nullable();
        $table->string('status')->default('active');
        $table->softDeletes();
        $table->timestamps();
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('plan_id')->references('id')->on('plan')->onDelete('cascade');
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
