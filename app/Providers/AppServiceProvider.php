<?php

namespace App\Providers;

use App\Services\OpenWeatherMapService;
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
        $this->app->singleton(OpenWeatherMapService::class, function (Application $app) {
            $client = Http::baseUrl(Config::get('openweathermap.base_url'))
                ->timeout(Config::get('openweathermap.timeout', 10))
                ->connectTimeout(Config::get('services.example.connect_timeout', 2))
                ->withOptions([
                    'query' => [
                        'appid' => Config::get('openweathermap.api_key'),
                        'exclude' => 'minutely,hourly,daily,alerts'
                    ]
                ]);

            return new OpenWeatherMapService($client);
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
