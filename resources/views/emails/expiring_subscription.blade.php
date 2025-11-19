<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('email.expiring_subscription.title') }}</title>
    </head>
<body>
    <p>{{ __('email.expiring_subscription.greeting', ['name' => $user->first_name ?? '']) }}</p>

    <p>{!! __('email.expiring_subscription.body', ['days' => setting('expiry_plan')]) !!}</p>
    
    <p>{{ __('email.best_regards') }}</p>
</body>
</html>