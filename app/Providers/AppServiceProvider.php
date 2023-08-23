<?php

namespace App\Providers;

use App\Helpers\GuzzleClients\OpenWeatherMapClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OpenWeatherMapClient::class, function () {
            return new OpenWeatherMapClient([
                'base_uri' => Config::get('openwheathermap.base_uri'),
                'query' => [
                    'appid' => Config::get('openwheathermap.api_key')
                ]
            ]);
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
