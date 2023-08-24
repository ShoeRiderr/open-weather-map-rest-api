<?php

return [
    'base_url' => env('OPEN_WEATHER_MAP_BASE_URL', 'https://api.openweathermap.org/data/3.0/'),
    'api_key' => env('OPEN_WEATHER_MAP_API_KEY'),
    'timeout' => env('OPEN_WEATHER_MAP_TIMEOUT', 10),
    'connect_timeout' => env('OPEN_WEATHER_MAP_CONNECT_TIMEOUT', 2),
];
