<?php

namespace App\Services;

use App\Helpers\StringHelper;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Throwable;

class OpenWeatherMapService
{
    /**
     * @var Response
     */
    private $response;

    public function __construct(private PendingRequest $client)
    {
    }

    public function getCurrentWeatherInfo(array $queryParams): self
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
        $baseQuery = Arr::get($this->client->getOptions(), 'query');
        $query = array_merge($query, $baseQuery);

        $this->response = $this->client
            ->get('/onecall', $query);

        return $this;
    }

    public function filterQueryParams(array $queryParams, array $allowedKeys): array
    {
        if (!StringHelper::allArrayKeysAreString($queryParams) || StringHelper::hasStringKeys($allowedKeys)) {
            return [];
        }

        return array_filter($queryParams, function ($key) use ($allowedKeys) {
            return in_array($key, $allowedKeys);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
