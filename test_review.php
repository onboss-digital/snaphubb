<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test: Verify review system is working
use Modules\Entertainment\Models\Entertainment;
use App\Models\User;
use Modules\Entertainment\Models\Review;

// Get a user and entertainment
$user = User::first(); // Get any user
$entertainment = Entertainment::first();

if (!$user || !$entertainment) {
    echo "❌ Missing test data: User or Entertainment\n";
    exit(1);
}

echo "✅ Test Setup:\n";
echo "   User: " . $user->email . " (ID: {$user->id})\n";
echo "   Entertainment: {$entertainment->name} (ID: {$entertainment->id})\n";

// Create a test review
$review = Review::updateOrCreate(
    [
        'entertainment_id' => $entertainment->id,
        'user_id' => $user->id
    ],
    [
        'rating' => 5,
        'review' => 'Test review - This is a perfect video!'
    ]
);

echo "\n✅ Review Created/Updated:\n";
echo "   ID: {$review->id}\n";
echo "   Rating: {$review->rating}\n";
echo "   Review: {$review->review}\n";

// Verify it was saved
$retrieved = Review::where('entertainment_id', $entertainment->id)
    ->where('user_id', $user->id)
    ->first();

if ($retrieved) {
    echo "\n✅ Review Retrieved Successfully from Database\n";
    echo "   Rating: {$retrieved->rating}\n";
    echo "   Text: {$retrieved->review}\n";
    echo "\n✅ REVIEW SYSTEM IS WORKING!\n";
} else {
    echo "\n❌ FAILED: Review not found in database\n";
}
