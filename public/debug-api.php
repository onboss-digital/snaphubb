<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Voting\Http\Controllers\VotingController;
use Illuminate\Http\Request;

echo "<pre style='background: #f5f5f5; padding: 20px; font-family: monospace; white-space: pre-wrap;'>\n";
echo "=== TESTANDO getAllCandidates ===\n\n";

try {
    $controller = new VotingController();
    $request = new Request();
    
    $response = $controller->getAllCandidates($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content:\n";
    echo json_encode(json_decode($response->getContent(), true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n</pre>";
