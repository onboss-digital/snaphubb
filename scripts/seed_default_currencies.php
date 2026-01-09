<?php
/**
 * Seed Default Currencies
 * 
 * Este script garante que as moedas padrão existam no banco de dados.
 * Útil para quando o sistema é instalado ou quando moedas são deletadas.
 * 
 * Uso: php scripts/seed_default_currencies.php
 */

if (!function_exists('app')) {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
}

use Modules\Currency\Models\Currency;

// Moedas padrão do sistema
$defaultCurrencies = [
    [
        'currency_name' => 'Brazilian Real',
        'currency_symbol' => 'R$',
        'currency_code' => 'BRL',
        'currency_position' => 'left',
        'thousand_separator' => '.',
        'decimal_separator' => ',',
        'no_of_decimal' => 2,
        'is_primary' => 1,
    ],
    [
        'currency_name' => 'US Dollar',
        'currency_symbol' => '$',
        'currency_code' => 'USD',
        'currency_position' => 'left',
        'thousand_separator' => ',',
        'decimal_separator' => '.',
        'no_of_decimal' => 2,
        'is_primary' => 0,
    ],
    [
        'currency_name' => 'Euro',
        'currency_symbol' => '€',
        'currency_code' => 'EUR',
        'currency_position' => 'left',
        'thousand_separator' => '.',
        'decimal_separator' => ',',
        'no_of_decimal' => 2,
        'is_primary' => 0,
    ],
    [
        'currency_name' => 'British Pound',
        'currency_symbol' => '£',
        'currency_code' => 'GBP',
        'currency_position' => 'left',
        'thousand_separator' => ',',
        'decimal_separator' => '.',
        'no_of_decimal' => 2,
        'is_primary' => 0,
    ],
];

echo "Verificando moedas no banco de dados...\n";
echo "=====================================\n\n";

$existingCount = Currency::count();
echo "Moedas existentes: {$existingCount}\n\n";

if ($existingCount === 0) {
    echo "Nenhuma moeda encontrada. Criando moedas padrão...\n\n";
    
    foreach ($defaultCurrencies as $index => $currencyData) {
        try {
            $currency = Currency::create($currencyData);
            echo "✓ Moeda criada: {$currency->currency_name} ({$currency->currency_code})\n";
        } catch (\Exception $e) {
            echo "✗ Erro ao criar moeda {$currencyData['currency_name']}: {$e->getMessage()}\n";
        }
    }
    
    echo "\n" . Currency::count() . " moedas foram criadas com sucesso!\n";
} else {
    echo "O sistema já possui moedas configuradas. Nenhuma ação foi realizada.\n";
    echo "\nMoedas atuais:\n";
    
    Currency::all()->each(function ($currency) {
        $primary = $currency->is_primary ? ' (PRIMÁRIA)' : '';
        echo "  - {$currency->currency_name} ({$currency->currency_code}) {$primary}\n";
    });
}

echo "\n";
