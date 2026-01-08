<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = [
    'subscriptions_transactions' => function($table) {
        $table->id();
        $table->unsignedBigInteger('subscription_id')->nullable();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('transaction_id')->nullable();
        $table->decimal('amount', 10, 2)->default(0);
        $table->string('currency')->nullable();
        $table->string('status')->default('pending');
        $table->string('payment_method')->nullable();
        $table->softDeletes();
        $table->timestamps();
        $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    },
    'planlimitation' => function($table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->softDeletes();
        $table->timestamps();
    },
    'planlimitation_mapping' => function($table) {
        $table->id();
        $table->unsignedBigInteger('plan_id');
        $table->unsignedBigInteger('planlimitation_id');
        $table->integer('limit_value')->default(0);
        $table->softDeletes();
        $table->timestamps();
        $table->foreign('plan_id')->references('id')->on('plan')->onDelete('cascade');
        $table->foreign('planlimitation_id')->references('id')->on('planlimitation')->onDelete('cascade');
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
