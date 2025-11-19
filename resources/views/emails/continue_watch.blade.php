<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('email.continue_watch.title') }}</title>
</head>
<body>
    <p>{{ __('email.continue_watch.greeting', ['name' => $user->first_name ?? '']) }}</p>

    <p>{{ __('email.continue_watch.body') }}</p>
    
    <p>{{ __('email.best_regards') }}</p>
</body>
</html>