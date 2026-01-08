<?php
/**
 * EXEMPLO: Como popular os Order Bumps com todos os novos campos (Fase 1)
 * 
 * Execute isso no tinker ou em um seeder após rodar as migrations
 * php artisan tinker
 * > include('exemplo-bumps-fase1.php');
 */

use Modules\Subscriptions\Models\OrderBump;

// ============================================
// BUMP 1: Criptografía anónima / Video Gravador
// ============================================
$bump1 = OrderBump::find(1) ?? OrderBump::create([
    'external_id' => '3nidg2uzc0',
    'plan_id' => 1,
]);

$bump1->update([
    // Português (padrão)
    'title' => 'Criptografía anónima',
    'description' => 'Acesso a conteúdos ao vivo e eventos',
    'text_button' => null,
    
    // Inglês
    'title_en' => 'Anonymous Encryption',
    'description_en' => 'Access to live content and events',
    'text_button_en' => null,
    
    // Espanhol
    'title_es' => 'Cifrado anónimo',
    'description_es' => 'Acceso a contenidos en vivo y eventos',
    'text_button_es' => null,
    
    // Informações de preço e desconto
    'price' => 9.99,
    'original_price' => 49.99,
    'discount_percentage' => 80,
    
    // Psicologia e apresentação
    'icon' => 'video',  // video, book, star, lock
    'badge' => 'POPULAR',
    'badge_color' => 'red',  // red, gold, blue
    'social_proof_count' => 1250,
    'urgency_text' => 'Válido apenas nesta compra',
    'recommended' => true,  // Será pré-selecionado
]);

// ============================================
// BUMP 2: Guia Premium
// ============================================
$bump2 = OrderBump::find(2) ?? OrderBump::create([
    'external_id' => '7fjk3ldw0',
    'plan_id' => 1,
]);

$bump2->update([
    // Português (padrão)
    'title' => 'Guia Premium',
    'description' => 'Acesso ao guia completo de estratégias',
    'text_button' => null,
    
    // Inglês
    'title_en' => 'Premium Guide',
    'description_en' => 'Access to the complete strategies guide',
    'text_button_en' => null,
    
    // Espanhol
    'title_es' => 'Guía Premium',
    'description_es' => 'Acceso a la guía completa de estrategias',
    'text_button_es' => null,
    
    // Informações de preço e desconto
    'price' => 14.99,
    'original_price' => 79.99,
    'discount_percentage' => 81,
    
    // Psicologia e apresentação
    'icon' => 'book',
    'badge' => 'BEST SELLER',
    'badge_color' => 'gold',
    'social_proof_count' => 3500,
    'urgency_text' => '⚡ 80% OFF - Apenas hoje',
    'recommended' => false,  // Não será pré-selecionado
]);

echo "✅ Order Bumps atualizados com dados completos da Fase 1!\n";
echo "\n📊 Dados inseridos:\n";
echo "Bump 1: " . $bump1->title . " - Recomendado: " . ($bump1->recommended ? 'Sim' : 'Não') . "\n";
echo "Bump 2: " . $bump2->title . " - Recomendado: " . ($bump2->recommended ? 'Sim' : 'Não') . "\n";
