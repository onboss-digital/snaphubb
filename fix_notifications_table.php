<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Deletar tabela notifications antiga se existir
if (Schema::hasTable('notifications')) {
    Schema::drop('notifications');
    echo "✓ Tabela notifications deletada\n";
}

// Criar tabela notifications com estrutura correta
Schema::create('notifications', function ($table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->string('notifiable_type');
    $table->unsignedBigInteger('notifiable_id');
    $table->text('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
    
    $table->index(['notifiable_type', 'notifiable_id']);
});

echo "✓ Tabela notifications criada com estrutura correta\n";
