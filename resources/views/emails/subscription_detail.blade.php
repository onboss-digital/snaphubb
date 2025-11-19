<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('email.subscription_detail.title') }}</title>
    <!-- Include Bootstrap 5 CSS -->
</head>
<body>
    <div class="container">
        <h2>{{ __('email.subscription_detail.title') }}</h2>

        <p>{{ __('email.subscription_detail.user') }}: {{ optional($subscriptionDetail->user)->first_name .' '. optional($subscriptionDetail->user)->last_name }}</p>
        <p>{{ __('email.subscription_detail.email') }}: {{ optional($subscriptionDetail->user)->email ?? '-' }}</p>
        <p>{{ __('email.subscription_detail.contact_no') }}: {{ optional($subscriptionDetail->user)->mobile ?? '-' }}</p>

        <table style="border:1px solid black;width:100%">
            <thead>
                <tr>
                    <th style="border:1px solid black">{{ __('email.subscription_detail.table.plan') }}</th>
                    <th style="border:1px solid black">{{ __('email.subscription_detail.table.end_date') }}</th>
                    <th style="border:1px solid black">{{ __('email.subscription_detail.table.amount') }}</th>
                    <th style="border:1px solid black">{{ __('email.subscription_detail.table.tax_amount') }}</th>
                    <th style="border:1px solid black">{{ __('email.subscription_detail.table.total_amount') }}</th>
                    <th style="border:1px solid black">{{ __('email.subscription_detail.table.duration') }}</th>
                    <th style="border:1px solid black">{{ __('email.subscription_detail.table.status') }}</th>
                </tr>
            </thead>
            <tbody>
                    <tr>
                        <td style="border:1px solid black">{{ $subscriptionDetail->name ?? '-' }}</td>
                        <td style="border:1px solid black">{{ \Carbon\Carbon::parse($subscriptionDetail->end_date)->format('Y-m-d') ?? '-'}}</td>
                        <td style="border:1px solid black">{{ $subscriptionDetail->amount ?? '-'}}</td>
                        <td style="border:1px solid black">{{ $subscriptionDetail->tax_amount ?? '-' }}</td>
                        <td style="border:1px solid black">{{ $subscriptionDetail->total_amount ?? '-' }}</td>
                        <td style="border:1px solid black">{{ ($subscriptionDetail->duration ?? '-') . ' ' . ($subscriptionDetail->type ?? '-') }}</td>
                        <td style="border:1px solid black">{{ $subscriptionDetail->status ?? '-' }}</td>
                    </tr>
            </tbody>
        </table>

    </div>
  
</body>
</html>

