<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view): void {
            static $cachedSettings = null;

            if ($cachedSettings === null) {
                try {
                    if (Schema::hasTable('settings')) {
                        $cachedSettings = Setting::query()->pluck('value', 'key')->all();
                    } else {
                        $cachedSettings = [];
                    }
                } catch (\Throwable) {
                    $cachedSettings = [];
                }
            }

            $view->with('siteSettings', $cachedSettings);
        });
    }
}
