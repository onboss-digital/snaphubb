<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

// If Telescope classes are present, define the provider extending the TelescopeApplicationServiceProvider.
if (class_exists(\Laravel\Telescope\TelescopeApplicationServiceProvider::class)) {
    class TelescopeServiceProvider extends \Laravel\Telescope\TelescopeApplicationServiceProvider
    {
        public function register(): void
        {
            $this->hideSensitiveRequestDetails();

            \Laravel\Telescope\Telescope::filter(function (\Laravel\Telescope\IncomingEntry $entry) {
                if ($this->app->environment('local')) {
                    return true;
                }

                return $entry->isReportableException() ||
                       $entry->isFailedRequest() ||
                       $entry->isFailedJob() ||
                       $entry->isScheduledTask() ||
                       $entry->hasMonitoredTag();
            });
        }

        protected function hideSensitiveRequestDetails(): void
        {
            if ($this->app->environment('local')) {
                return;
            }

            \Laravel\Telescope\Telescope::hideRequestParameters(['_token']);

            \Laravel\Telescope\Telescope::hideRequestHeaders([
                'cookie',
                'x-csrf-token',
                'x-xsrf-token',
            ]);
        }

        protected function gate(): void
        {
            Gate::define('viewTelescope', function ($user) {
                return in_array($user->email, [
                    //
                ]);
            });
        }
    }
} else {
    // Fallback empty provider when Telescope is not installed (e.g., in --no-dev installs).
    class TelescopeServiceProvider extends ServiceProvider
    {
        public function register(): void
        {
            // Telescope not installed; noop.
        }
    }
}
