# 🚀 Quick Start: Subscription Status Banner

## 1️⃣ View the Banner Immediately

### Option A: Using Test Seeder (Fastest)

```bash
# Create 5 test users with different subscription statuses
php artisan db:seed --class=TestSubscriptionStatusSeeder

# Then log in as any of these:
# Email: test-subscription@example.com (Active - GREEN)
# Email: test-7days@example.com (7 Days - YELLOW)
# Email: test-3days@example.com (3 Days - ORANGE)
# Email: test-1day@example.com (1 Day - RED)
# Email: test-expired@example.com (Expired - DARK RED)
# Password: password (for all)
```

### Option B: Manual Database Insert

```php
use App\Models\User;
use Modules\Subscriptions\Models\Subscription;
use Carbon\Carbon;

// Create user
$user = User::create([
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
]);

// Create subscription ending in 5 days
Subscription::create([
    'user_id' => $user->id,
    'plan_id' => 1,
    'status' => 'ACTIVE', // or use config('constant.SUBSCRIPTION_STATUS.ACTIVE')
    'start_date' => now()->subDays(25),
    'end_date' => now()->addDays(5),
    'price' => 99,
    'currency' => 'USD',
    'transaction_id' => 'TEST-001',
    'payment_gateway' => 'stripe',
]);
```

## 2️⃣ Test All Scenarios

### Green Banner (Active)
- Log in and check homepage
- Should see: "You are protected until [date]"
- Icon: Shield with checkmark ✓

### Yellow Banner (7 Days)
- Create subscription ending in 5 days
- Should see: "Your subscription expires in 7 days"
- Icon: Clock with 7️⃣

### Orange Banner (3 Days)
- Create subscription ending in 2 days
- Should see: "ATTENTION: 3 days remaining"
- Icon: Clock with 3️⃣

### Red Banner (1 Day)
- Create subscription ending in 12 hours
- Should see: "URGENT: Last chance today!"
- Icon: Clock with 1️⃣

### Dark Red Banner (Expired)
- Create subscription ending 10 days ago
- Should see: "Subscription expired - Renew now"
- Icon: X mark ❌
- Button: "Renew Now" appears

## 3️⃣ Test Languages

Add language code to URL:
- `/es` - Spanish (Español)
- `/fr` - French (Français)
- `/de` - German (Deutsch)
- `/pt` - Portuguese (Português)
- `/el` - Greek (Ελληνικά)
- `/ar` - Arabic (العربية)

## 4️⃣ Verify Hiding Behavior

### Banner Should Hide In These Cases:
1. ❌ User is not logged in
2. ❌ User has no subscriptions
3. ❌ User is on subscription plan page (`/subscriptions`)
4. ❌ Subscription is cancelled (status ≠ ACTIVE/INACTIVE)

Test these by navigating to `/subscriptions` while logged in - banner should disappear.

## 5️⃣ Check Styling

The banner should have:
- ✅ Smooth slide-down animation
- ✅ Color-coded background (green/yellow/orange/red)
- ✅ Professional SVG icons
- ✅ "Renew Now" button for urgent statuses
- ✅ Responsive layout on mobile
- ✅ Sticky positioning (stays visible when scrolling)

## 6️⃣ Run Automated Tests

```bash
# Run all subscription banner tests
php artisan test tests/Unit/Components/SubscriptionStatusBannerTest.php

# Or run specific test
php artisan test tests/Unit/Components/SubscriptionStatusBannerTest.php --filter test_banner_shows_7_day_warning
```

## 7️⃣ File Locations

| Component | Location |
|-----------|----------|
| PHP Logic | `app/View/Components/SubscriptionStatusBanner.php` |
| View Template | `Modules/Frontend/Resources/views/components/partials/subscription-status-banner.blade.php` |
| Styling | `Modules/Frontend/Resources/assets/sass/custom/subscription/banner.scss` |
| Tests | `tests/Unit/Components/SubscriptionStatusBannerTest.php` |
| Seeder | `database/seeders/TestSubscriptionStatusSeeder.php` |
| Translations | `lang/{locale}/placeholder.php` |
| Master Layout | `Modules/Frontend/Resources/views/layouts/master.blade.php` |

## 8️⃣ CSS Classes Used

The banner uses Bootstrap 5 classes:
- `.bg-success` - Green (active)
- `.bg-warning` - Yellow (7 days)
- `.bg-orange` - Orange (3 days) [custom]
- `.bg-danger` - Red (1 day & expired)
- `.subscription-status-banner` - Custom styling

## 9️⃣ Translation Keys

Find these in `lang/{locale}/placeholder.php`:
- `subscription_active` - "You are protected until..."
- `subscription_7_days` - "Your subscription expires in 7 days"
- `subscription_3_days` - "ATTENTION: 3 days remaining"
- `subscription_1_day` - "URGENT: Last chance today!"
- `subscription_expired` - "Subscription expired - Renew now"
- `lbl_subscription_expired_modal_renew_button` - "Renew Now" (already exists)

## 🔟 Common Issues & Solutions

**Q: Banner not appearing?**
A: 
1. Log in as authenticated user
2. Verify user has an active subscription
3. Check subscription is not on plan page
4. Clear cache: `php artisan cache:clear`

**Q: Wrong message showing?**
A:
1. Check subscription end_date is correct
2. Run: `php artisan tinker` and verify days calculation
3. Verify language locale is correct

**Q: Styling looks wrong?**
A:
1. Recompile assets: `npm run dev`
2. Clear browser cache (Ctrl+Shift+Delete)
3. Check for CSS conflicts in DevTools

**Q: Translation not working?**
A:
1. Verify translation key exists in `lang/{locale}/placeholder.php`
2. Check language code matches app locale
3. Clear cache: `php artisan cache:clear`

---

**Need help?** Check the full documentation:
- `SUBSCRIPTION-BANNER-IMPLEMENTATION.md` - Complete guide
- `SUBSCRIPTION-BANNER-COMPLETE.md` - Full summary
- Component source files - See comments in code
