<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Modules\Entertainment\Models\Entertainment;
use Modules\Subscriptions\Models\Subscription;
use Modules\Entertainment\Models\Review;
use Carbon\Carbon;

$allUsers = User::where('user_type', 'user')->get();
$totalusers = $allUsers->count();
$activeusers = $allUsers->where('status', 1)->count();
$totalSubscribers = $allUsers->where('is_subscribe', 1)->count();

echo "totalusers: {$totalusers}\n";
echo "activeusers: {$activeusers}\n";
echo "totalSubscribers: {$totalSubscribers}\n";

// reviews
$totalreview = Review::count();
echo "totalreview: {$totalreview}\n";

// subscriptions soon to expire
$currentDate = Carbon::now();
$expiryThreshold = $currentDate->copy()->addDays(7);
$subscriptions = Subscription::with('user')
    ->where('status', 'active')
    ->whereDate('end_date', '<=', $expiryThreshold)
    ->get();
$userIds = $subscriptions->pluck('user_id');
$totalsoontoexpire = $allUsers->whereIn('id', $userIds)->where('status',1)->count();
echo "totalsoontoexpire: {$totalsoontoexpire}\n";

echo "Done\n";
