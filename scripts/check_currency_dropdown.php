<?php
/**
 * Check Currency Dropdown
 * 
 * Este script verifica se o dropdown de moedas (em /setting/currency-settings) 
 * está funcionando corretamente e lista as moedas disponíveis.
 * 
 * Uso: php scripts/check_currency_dropdown.php
 */

if (!function_exists('app')) {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
}

use Modules\Currency\Models\Currency;

echo "Verificação do Dropdown de Moedas\n";
echo "==================================\n\n";

// Verificar moedas no banco
$currencies = Currency::all();
$count = $currencies->count();

echo "Total de moedas no banco: {$count}\n";

if ($count === 0) {
    echo "\n⚠️  AVISO: Nenhuma moeda encontrada no banco de dados!\n";
    echo "Isso causará um dropdown vazio em /setting/currency-settings\n";
    echo "e também em /app/plans/create\n\n";
    echo "Solução: Execute o comando abaixo para criar moedas padrão:\n";
    echo "   php scripts/seed_default_currencies.php\n";
} else {
    echo "\nMoedas disponíveis para o dropdown:\n";
    echo "-----------------------------------\n\n";
    
    $currencies->each(function ($currency, $index) {
        $primary = $currency->is_primary ? ' [PRIMÁRIA]' : '';
        echo ($index + 1) . ". {$currency->currency_name}{$primary}\n";
        echo "   Código: {$currency->currency_code}\n";
        echo "   Símbolo: {$currency->currency_symbol}\n";
        echo "   Posição: {$currency->currency_position}\n";
        echo "\n";
    });
    
    // Verificar se há moeda primária
    $primary = Currency::where('is_primary', 1)->first();
    if (!$primary) {
        echo "⚠️  AVISO: Nenhuma moeda primária configurada!\n";
        echo "Defina uma moeda como primária em /setting/currency-settings\n";
    } else {
        echo "✓ Moeda primária configurada: {$primary->currency_name} ({$primary->currency_code})\n";
    }
}

echo "\n";
