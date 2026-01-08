<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\User\Models\Ranking;

echo "<pre style='background: #f5f5f5; padding: 20px; font-family: monospace; white-space: pre-wrap;'>\n";

$ranking = Ranking::find(2);

if (!$ranking) {
    echo "❌ Ranking ID 2 não encontrado\n";
    exit;
}

echo "=== VERIFICANDO CONTENTS ===\n\n";

// Raw contents
echo "Raw contents type: " . gettype($ranking->contents) . "\n";
echo "Raw contents length: " . strlen($ranking->contents) . "\n";
echo "Raw contents (primeiros 500 chars):\n";
echo substr($ranking->contents, 0, 500) . "\n\n";

// Tentar decodificar
$decoded = json_decode($ranking->contents, true);
echo "Decoded type: " . gettype($decoded) . "\n";
echo "Decoded count: " . (is_array($decoded) ? count($decoded) : 'N/A') . "\n";
echo "JSON last error: " . json_last_error_msg() . "\n\n";

if (is_array($decoded) && !empty($decoded)) {
    echo "Primeiro item:\n";
    echo json_encode($decoded[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "❌ Contents está vazio ou não é array!\n";
    echo "Full contents:\n";
    var_dump($ranking->contents);
}

echo "\n</pre>";
