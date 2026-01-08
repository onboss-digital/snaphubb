<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Entertainment\Models\Entertainment;
use App\Models\User;
use Modules\Entertainment\Models\Review;

// Get first user
$user = User::first();

// Create test entertainment
$entertainment = Entertainment::create([
    'name' => 'Test Movie - Reviews',
    'slug' => 'test-movie-reviews-' . time(),
    'description' => 'Test video for review system testing',
    'type' => 'movie',
    'age_restriction' => 'U',
    'duration' => 120,
    'language' => 'en',
    'release_date' => now(),
    'status' => 1
]);

echo "✅ Entertainment Created: {$entertainment->name} (ID: {$entertainment->id})\n";

// Create some test reviews
for ($i = 1; $i <= 3; $i++) {
    $testUser = User::skip($i - 1)->first();
    if ($testUser) {
        $review = Review::create([
            'entertainment_id' => $entertainment->id,
            'user_id' => $testUser->id,
            'rating' => $i,
            'review' => "Test review #{$i}: This is a test review with " . ($i) . " star(s)."
        ]);
        echo "✅ Review #{$i} created (Rating: {$review->rating} stars)\n";
    }
}

echo "\n✅ All test data created successfully!\n";
echo "\n📌 Next Steps:\n";
echo "   1. Open: http://127.0.0.1:8002/entertainment/{$entertainment->slug}\n";
echo "   2. Log in as a user\n";
echo "   3. Test submitting a new review\n";
echo "   4. Verify the review appears immediately\n";
