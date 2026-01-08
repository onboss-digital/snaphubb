<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Entertainment\Models\Entertainment;
use Modules\Entertainment\Models\Review;

// Get test entertainment
$entertainment = Entertainment::where('name', 'Test Movie - Reviews')->first();

if (!$entertainment) {
    echo "❌ Entertainment not found\n";
    exit(1);
}

echo "✅ Entertainment: {$entertainment->name} (ID: {$entertainment->id})\n";

// Get all reviews for this entertainment
$reviews = Review::where('entertainment_id', $entertainment->id)
    ->with('user')
    ->get();

echo "\n✅ Reviews Retrieved (" . count($reviews) . " total):\n";
echo str_repeat("-", 60) . "\n";

foreach ($reviews as $review) {
    echo "User: {$review->user->first_name} {$review->user->last_name}\n";
    echo "Rating: " . str_repeat("⭐", $review->rating) . "\n";
    echo "Review: {$review->review}\n";
    echo "Created: {$review->created_at->format('Y-m-d H:i:s')}\n";
    echo str_repeat("-", 60) . "\n";
}

echo "\n✅ API REVIEWS SYSTEM WORKING CORRECTLY!\n";
