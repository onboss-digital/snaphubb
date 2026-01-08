<?php
/**
 * EXEMPLO: Como popular os campos de tradução nos Order Bumps
 * 
 * Execute isso no tinker ou em um seeder após rodar a migration
 * php artisan tinker
 * > include('exemplo-update-order-bumps.php');
 */

use Modules\Subscriptions\Models\OrderBump;

// Atualizar Bump 1: Criptografía anónima
$bump1 = OrderBump::find(1); // Ou use: ::where('external_id', '3nidg2uzc0')->first()
if ($bump1) {
    $bump1->update([
        // Português (campos principais)
        'title' => 'Criptografía anónima',
        'description' => 'Acesso a conteúdos ao vivo e eventos',
        
        // Inglês
        'title_en' => 'Anonymous Encryption',
        'description_en' => 'Access to live content and events',
        
        // Espanhol
        'title_es' => 'Cifrado anónimo',
        'description_es' => 'Acceso a contenidos en vivo y eventos',
    ]);
}

// Atualizar Bump 2: Guia Premium
$bump2 = OrderBump::find(2); // Ou use: ::where('external_id', '7fjk3ldw0')->first()
if ($bump2) {
    $bump2->update([
        // Português (campos principais)
        'title' => 'Guia Premium',
        'description' => 'Acesso ao guia completo de estratégias',
        
        // Inglês
        'title_en' => 'Premium Guide',
        'description_en' => 'Access to the complete strategies guide',
        
        // Espanhol
        'title_es' => 'Guía Premium',
        'description_es' => 'Acceso a la guía completa de estrategias',
    ]);
}

echo "✅ Order Bumps atualizados com sucesso!";
