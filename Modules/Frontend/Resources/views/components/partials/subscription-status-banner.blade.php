@php
    $bannerConfig = [
        '7_days' => [
            'bgClass' => 'bg-warning',
            'textClass' => 'text-dark',
            'icon' => 'clock-7', // Clock showing 7
            'message_key' => 'subscription_7_days',
            'button' => true
        ],
        '3_days' => [
            'bgClass' => 'bg-orange',
            'textClass' => 'text-white',
            'icon' => 'clock-3', // Clock showing 3
            'message_key' => 'subscription_3_days',
            'button' => true
        ],
        '1_day' => [
            'bgClass' => 'bg-danger',
            'textClass' => 'text-white',
            'icon' => 'clock-1', // Clock showing 1
            'message_key' => 'subscription_1_day',
            'button' => true
        ],
    ];
    
    $config = $bannerConfig[$status] ?? null;
    if (!$config) return '';
@endphp

<div class="subscription-status-banner {{ $config['bgClass'] }} {{ $config['textClass'] }} py-3 sticky-top" style="z-index: 999; top: 0;">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-3">
            <!-- Icon + Message -->
            <div class="d-flex align-items-center gap-2">
                @if($config['icon'] === 'clock-7')
                    <!-- Clock Icon (7 Days) -->
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                        <text x="12" y="8" text-anchor="middle" font-size="8" font-weight="bold" fill="currentColor">7</text>
                    </svg>
                @elseif($config['icon'] === 'clock-3')
                    <!-- Clock Icon (3 Days) -->
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                        <text x="12" y="8" text-anchor="middle" font-size="8" font-weight="bold" fill="currentColor">3</text>
                    </svg>
                @elseif($config['icon'] === 'clock-1')
                    <!-- Clock Icon (1 Day) -->
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                        <text x="12" y="8" text-anchor="middle" font-size="8" font-weight="bold" fill="currentColor">1</text>
                    </svg>
                @endif
                
                <!-- Message -->
                <span class="fw-semibold">
                    @if($status === '7_days')
                        {{ __('placeholder.subscription_7_days') }}
                    @elseif($status === '3_days')
                        {{ __('placeholder.subscription_3_days') }}
                    @elseif($status === '1_day')
                        {{ __('placeholder.subscription_1_day') }}
                    @endif
                </span>
            </div>

            <!-- Action Button -->
            @if($config['button'])
                <a href="{{ route('subscriptionPlan') }}" class="btn btn-sm btn-light fw-semibold text-uppercase flex-shrink-0">
                    {{ __('placeholder.lbl_subscription_expired_modal_renew_button') }}
                </a>
            @endif
        </div>
    </div>
</div>
