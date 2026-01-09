<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

// Lista de todas as tabelas que precisam existir
$tables = [
    // Core tables
    'users',
    'password_resets',
    'personal_access_tokens',
    'failed_jobs',
    
    // Permission tables
    'permissions',
    'roles',
    'model_has_permissions',
    'model_has_roles',
    'role_has_permissions',
    
    // Entertainment
    'entertainments',
    'seasons',
    'episodes',
    'cast_crew',
    'reviews',
    'genres',
    
    // Video
    'videos',
    'video_stream_content_mapping',
    'video_download_mappings',
    
    // Entertainment tracking
    'entertainment_downloads',
    'entertainment_views',
    'entertainment_favorites',
    'watch_history',
    
    // Live TV
    'live_tv',
    
    // Settings & Config
    'settings',
    'mobile_settings',
    'banners',
    'pages',
    'faqs',
    
    // Subscription & Payment
    'plan',
    'planlimitation',
    'planlimitation_mapping',
    'subscriptions',
    'subscriptions_transactions',
    
    // Currency & Tax
    'currencies',
    'taxes',
    'languages',
    
    // Media
    'media',
    
    // Notifications
    'notifications',
    
    // Social
    'social_logins',
    
    // Activity
    'activity_log',
];

$created = 0;
$exists = 0;

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) {
        echo "⚠️  Faltando: $table\n";
    } else {
        $exists++;
    }
}

echo "\n✓ Tabelas existentes: $exists\n";
echo "⚠️  Tabelas faltando: " . (count($tables) - $exists) . "\n";
echo "✓ Total esperado: " . count($tables) . "\n";
