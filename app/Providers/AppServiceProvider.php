<?php

namespace App\Providers;

use App\Services\OpenWheatherMapService;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OpenWheatherMapService::class, function (Application $app) {
            $client = Http::baseUrl(Config::get('openwheathermap.base_url'))
                ->timeout(Config::get('openwheathermap.timeout', 10))
                ->connectTimeout(Config::get('services.example.connect_timeout', 2))
                ->withOptions([
                    'query' => [
                        'appid' => Config::get('openwheathermap.api_key'),
                        'exclude' => 'minutely,hourly,daily,alerts'
                    ]
                ]);

            return new OpenWheatherMapService($client);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
