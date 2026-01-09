<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = [
    'permissions' => function($table) {
        $table->bigIncrements('id');
        $table->string('name');
        $table->string('guard_name')->default('web');
        $table->timestamps();
        $table->unique(['name', 'guard_name']);
    },
    'roles' => function($table) {
        $table->bigIncrements('id');
        $table->string('name');
        $table->string('guard_name')->default('web');
        $table->string('title')->nullable();
        $table->timestamps();
        $table->unique(['name', 'guard_name']);
    },
    'model_has_permissions' => function($table) {
        $table->unsignedBigInteger('permission_id');
        $table->string('model_type');
        $table->unsignedBigInteger('model_id');
        $table->primary(['permission_id', 'model_id', 'model_type']);
        $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
    },
    'model_has_roles' => function($table) {
        $table->unsignedBigInteger('role_id');
        $table->string('model_type');
        $table->unsignedBigInteger('model_id');
        $table->primary(['role_id', 'model_id', 'model_type']);
        $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
    },
    'role_has_permissions' => function($table) {
        $table->unsignedBigInteger('permission_id');
        $table->unsignedBigInteger('role_id');
        $table->primary(['permission_id', 'role_id']);
        $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
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
