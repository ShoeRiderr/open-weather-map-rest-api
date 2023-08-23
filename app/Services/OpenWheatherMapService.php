<?php

use App\Helpers\GuzzleClients\OpenWeatherMapClient;

class OpenWheatherMapService
{
    public function __construct(private OpenWeatherMapClient $openWeatherMapClient)
    {
    }

    public function getCurrentWheatherInfo(array $queryParams)
    {
        // Available query params:
        $availableQueryKeys = [
            'lat',
            'lon',
            'exclude',
            'units',
            'lang',
        ];
        $query = $this->filterQueryParams($queryParams, $availableQueryKeys);

        $this->openWeatherMapClient->get('onecall', [
            'query' => $query
        ]);
    }

    private function filterQueryParams(array $queryParams, array $allowedKeys)
    {
        return array_filter($queryParams, function ($key) use ($allowedKeys) {
            return in_array($key, $allowedKeys);
        }, ARRAY_FILTER_USE_KEY);
    }
}
