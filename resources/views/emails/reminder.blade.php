<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('email.reminder.title') }}</title>
</head>
<body>
    <p>{{ __('email.reminder.greeting', ['name' => $user->first_name ?? '']) }}</p>

    <p>{!! __('email.reminder.body', ['days' => setting('upcoming')]) !!}</p>
    
    <p>{{ __('email.best_regards') }}</p>
</body>
</html>