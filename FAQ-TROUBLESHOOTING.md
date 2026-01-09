# ❓ Subscription Banner - FAQ & Troubleshooting

## Frequently Asked Questions

### Q1: Why isn't the banner showing?

**A:** Check these in order:

1. **Are you logged in?**
   - Banner only shows for authenticated users
   - Log out and log back in if just created account

2. **Does the user have an active subscription?**
   ```php
   // In Tinker, check:
   $user = Auth::user();
   $user->subscriptions()->latest('end_date')->first();
   // Should return a subscription with status = 'ACTIVE' or 'INACTIVE'
   ```

3. **Are you on the subscription plan page?**
   - Banner intentionally hides on `/subscriptions` page
   - Check from homepage or any other page

4. **Is the subscription status correct?**
   ```php
   // Check subscription status
   $subscription = Subscription::find(1);
   echo $subscription->status; // Should be 'ACTIVE' or 'INACTIVE'
   echo config('constant.SUBSCRIPTION_STATUS.ACTIVE'); // Compare values
   ```

5. **Did you clear caches?**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

6. **Did you recompile assets?**
   ```bash
   npm run dev
   # or for production:
   npm run production
   ```

---

### Q2: Banner shows but message is wrong?

**A:** Check these steps:

1. **Verify subscription end_date:**
   ```php
   $sub = Subscription::find(1);
   echo $sub->end_date; // Should be a valid date
   echo $sub->end_date->diffInDays(now()); // Should give days remaining
   ```

2. **Check translation key exists:**
   ```php
   // Run in Tinker
   echo __('placeholder.subscription_active');
   echo __('placeholder.subscription_7_days');
   // Should output the translated message
   ```

3. **Verify app locale:**
   ```php
   echo app()->getLocale(); // Should be 'en', 'es', etc.
   ```

4. **Check language file has key:**
   ```bash
   grep "subscription_active" lang/en/placeholder.php
   # Should find: 'subscription_active' => '...'
   ```

---

### Q3: Button doesn't work?

**A:** Try these fixes:

1. **Verify route exists:**
   ```php
   // In routes:
   Route::get('/subscriptions', ...)->name('subscriptionPlan');
   
   // In Tinker:
   echo route('subscriptionPlan'); // Should output URL
   ```

2. **Check button code:**
   - Open browser DevTools (F12)
   - Right-click button → Inspect
   - Should see: `<a href="..." class="btn btn-light">`

3. **Test manually:**
   ```bash
   # Test route works
   curl http://localhost:8000/subscriptions
   ```

---

### Q4: Banner styling looks broken?

**A:** Try these solutions:

1. **Recompile assets:**
   ```bash
   npm run dev
   ```

2. **Clear browser cache:**
   - Ctrl+Shift+Delete (Windows)
   - Cmd+Shift+Delete (Mac)
   - Then reload page

3. **Check CSS loaded:**
   - F12 → Network tab
   - Filter by CSS
   - Should see `modules/frontend/style.css` loaded
   - Should be large file (456 KiB+)

4. **Check for CSS conflicts:**
   - F12 → Elements tab
   - Click on banner element
   - Check "Styles" panel
   - Look for `!important` overrides

5. **Verify Bootstrap is loaded:**
   ```bash
   # Check in page source (Ctrl+U)
   # Should see: bootstrap.css or bootstrap.min.css loaded
   ```

---

### Q5: Text not translating correctly?

**A:** Follow these steps:

1. **Check language file:**
   ```bash
   # View specific language file
   cat lang/es/placeholder.php | grep subscription
   ```

2. **Verify locale is set:**
   ```php
   // In Tinker:
   app()->setLocale('es');
   echo __('placeholder.subscription_active');
   ```

3. **Clear translation cache:**
   ```bash
   php artisan cache:clear
   ```

4. **Check translation key exactly:**
   - Key: `placeholder.subscription_7_days`
   - NOT: `placeholder.subscription7days` (no underscore)
   - NOT: `placeholder.subscription_7_days_warning` (wrong key)

5. **Verify language file syntax:**
   ```php
   // Should be valid PHP array:
   return [
       'subscription_active' => 'Message here',
       'subscription_7_days' => 'Message here',
   ];
   ```

---

### Q6: Banner shows wrong status for subscription?

**A:** Debug the status detection:

1. **Check days calculation:**
   ```php
   use Carbon\Carbon;
   
   $sub = Subscription::find(1);
   $endDate = Carbon::parse($sub->end_date);
   $daysRemaining = $endDate->diffInDays(now(), false);
   
   echo "Days remaining: $daysRemaining";
   
   if ($daysRemaining > 0) {
       echo "Status: active";
   } elseif ($daysRemaining <= 0 && $daysRemaining > -1) {
       echo "Status: 1_day";
   } elseif ($daysRemaining <= -1 && $daysRemaining > -3) {
       echo "Status: 3_days";
   } elseif ($daysRemaining <= -3 && $daysRemaining > -7) {
       echo "Status: 7_days";
   } else {
       echo "Status: expired";
   }
   ```

2. **Verify subscription status:**
   ```php
   echo $sub->status; // Should be 'ACTIVE' or 'INACTIVE'
   ```

3. **Check subscription ordering:**
   ```php
   // User might have multiple subscriptions
   $subs = Subscription::where('user_id', $user->id)
       ->orderBy('end_date', 'desc')
       ->get();
   echo count($subs); // Check how many subscriptions
   ```

---

### Q7: Animation not smooth?

**A:** Check animation settings:

1. **Browser GPU acceleration:**
   ```css
   /* Should be present in banner.scss */
   will-change: transform;
   transform: translateZ(0);
   ```

2. **Disable browser extensions:**
   - Some extensions block animations
   - Try incognito/private mode

3. **Check frame rate:**
   - F12 → Rendering tab
   - Enable "Paint flashing"
   - Should be smooth green flashes

4. **Clear CSS cache:**
   ```bash
   npm run dev  # Recompile
   ```

---

### Q8: Component not registered?

**A:** Verify registration:

1. **Check file exists:**
   ```bash
   ls -la app/View/Components/SubscriptionStatusBanner.php
   ```

2. **Check namespace:**
   ```php
   // File should start with:
   namespace App\View\Components;
   class SubscriptionStatusBanner extends Component
   ```

3. **Check Laravel auto-discovery:**
   ```php
   // In Tinker:
   $components = app('view')->getFinder()->getExtensions();
   dd($components); // Should include Components folder
   ```

4. **If not auto-discovered, register manually:**
   ```php
   // In AppServiceProvider.php
   use App\View\Components\SubscriptionStatusBanner;
   
   Blade::component('subscription-status-banner', SubscriptionStatusBanner::class);
   ```

---

### Q9: Mobile layout broken?

**A:** Fix responsive design:

1. **Check viewport meta tag:**
   ```html
   <!-- Should be in <head> -->
   <meta name="viewport" content="width=device-width, initial-scale=1">
   ```

2. **Test different screen sizes:**
   - F12 → Toggle device toolbar (Ctrl+Shift+M)
   - Test: 320px, 768px, 1024px, 1200px

3. **Check media queries:**
   ```bash
   # Should be in banner.scss:
   @media (max-width: 576px) { ... }
   @media (max-width: 768px) { ... }
   ```

4. **Verify Bootstrap grid:**
   ```php
   // Should use Bootstrap flexbox classes:
   d-flex, flex-wrap, gap-*, align-items-center, etc.
   ```

---

### Q10: SVG icons not rendering?

**A:** Debug SVG display:

1. **Check SVG syntax:**
   ```html
   <!-- Open browser DevTools (F12) -->
   <!-- Right-click SVG → Inspect -->
   <!-- Should be valid SVG element -->
   ```

2. **Check `currentColor` support:**
   - Modern browsers support `currentColor`
   - Falls back to black/white if unsupported

3. **Force SVG color:**
   ```scss
   svg {
       color: inherit; // Or explicit: #ffffff
   }
   ```

4. **Test SVG directly:**
   ```html
   <!-- Open in new tab -->
   <svg viewBox="0 0 24 24">...</svg>
   ```

---

## Common Error Messages

### "Class not found: SubscriptionStatusBanner"
**Solution:** Run `composer dump-autoload`

### "Unknown locale error"
**Solution:** Check language code matches `lang/` folders exactly

### "Route 'subscriptionPlan' not defined"
**Solution:** Verify route exists with exact name in routes files

### "Undefined variable: $status"
**Solution:** Component not rendering; check authentication status

### "SQLSTATE[42S22]: Column not found"
**Solution:** Check `subscriptions` table has `end_date` and `status` columns

---

## Performance Issues

### Banner Causing Page Slow?

1. **Check database query:**
   ```php
   // Enable query logging in config/database.php
   // Then check how many queries for subscriptions
   ```

2. **Optimize if needed:**
   ```php
   // Add eager loading in component
   Subscription::where('user_id', $user->id)
       ->orderBy('end_date', 'desc')
       ->select('id', 'user_id', 'status', 'end_date') // Only needed columns
       ->first();
   ```

3. **Cache if needed:**
   ```php
   Cache::remember("user.{$user->id}.subscription", 60, function() {
       return Subscription::where('user_id', $user->id)
           ->orderBy('end_date', 'desc')
           ->first();
   });
   ```

---

## Browser Compatibility

### Old Browser Issues?

| Feature | IE11 | Edge 12 | Chrome 50 |
|---------|------|--------|----------|
| Flexbox | ⚠️ | ✅ | ✅ |
| CSS Grid | ❌ | ⚠️ | ✅ |
| SVG | ✅ | ✅ | ✅ |
| Animations | ✅ | ✅ | ✅ |
| CSS Gradient | ⚠️ | ✅ | ✅ |

**IE11 Support:** Add fallback styles or disable banner for IE11

---

## Getting Help

### If issue persists:

1. **Check these logs:**
   ```bash
   tail -50 storage/logs/laravel.log
   ```

2. **Gather info for support:**
   ```
   - Laravel version: 11
   - PHP version: 8.1+
   - Browser: Chrome/Firefox/Safari
   - Error message: [exact message from F12]
   - Steps to reproduce: [exact steps]
   - Screenshot: [if visual issue]
   ```

3. **Test in isolation:**
   ```php
   // In Tinker, test component directly:
   $component = new \App\View\Components\SubscriptionStatusBanner();
   dd($component->status, $component->endDate, $component->daysRemaining);
   ```

---

## Performance Monitoring

### Track banner performance:

```php
// In component, add timing:
$start = microtime(true);

// ... subscription query logic ...

$duration = (microtime(true) - $start) * 1000; // Convert to ms
if ($duration > 100) {
    \Log::warning('SubscriptionStatusBanner slow', ['duration_ms' => $duration]);
}
```

---

## Best Practices

✅ **Do:**
- Clear caches after code changes
- Recompile assets after SCSS changes
- Test in multiple browsers
- Monitor error logs
- Update test users regularly

❌ **Don't:**
- Modify component logic without updating tests
- Remove the route check (subscriptionPlan)
- Use hardcoded dates in subscriptions
- Disable caching for performance
- Mix languages in one file

---

Last Updated: December 2024
For latest troubleshooting: Check GitHub issues or contact support team
