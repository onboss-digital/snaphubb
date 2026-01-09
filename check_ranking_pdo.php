<?php
// Conexão direta com PDO
$host = '127.0.0.1';
$db = 'snaphubb';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    
    $stmt = $pdo->prepare("SELECT id, name, status, start_date, end_date, contents FROM rankings WHERE name LIKE ? LIMIT 1");
    $stmt->execute(['%VOTAÇÃO%']);
    $ranking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ranking) {
        echo "❌ Nenhum ranking com 'VOTAÇÃO' no nome encontrado\n";
        exit(1);
    }
    
    echo "=== RANKING ENCONTRADO ===\n";
    echo "ID: " . $ranking['id'] . "\n";
    echo "Nome: " . $ranking['name'] . "\n";
    echo "Status: " . $ranking['status'] . "\n";
    echo "Start: " . $ranking['start_date'] . "\n";
    echo "End: " . $ranking['end_date'] . "\n";
    echo "Hoje: " . date('Y-m-d') . "\n";
    
    $inRange = date('Y-m-d') >= $ranking['start_date'] && date('Y-m-d') <= $ranking['end_date'];
    echo "No período? " . ($inRange ? "✅ SIM" : "❌ NÃO") . "\n";
    
    $contents = json_decode($ranking['contents'], true);
    echo "\nConteúdo (primeiros 300 chars):\n";
    echo substr(json_encode($contents, JSON_PRETTY_PRINT), 0, 300) . "\n";
    echo "\nTotal de itens: " . (is_array($contents) ? count($contents) : 0) . "\n";
    
    if (!empty($contents)) {
        echo "\n1º Item:\n";
        echo json_encode($contents[0], JSON_PRETTY_PRINT) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}
