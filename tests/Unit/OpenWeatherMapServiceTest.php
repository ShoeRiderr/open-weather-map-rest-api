<?php

namespace Tests\Unit;

use App\Helpers\GuzzleClients\OpenWeatherMapClient;
use App\Helpers\StringHelper;
use App\Services\OpenWeatherMapService;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;

class OpenWeatherMapServiceTest extends TestCase
{
    /**
     * @test
     */
    public function filter_query_params_successfull(): void
    {
        $queryParams = [
            'lat' => 'test',
            'lon' => 'test',
            'exclude' => 'test',
            'test' => 'test',
        ];

        $availableQueryKeys = [
            'lat',
            'lon',
            'exclude',
            'units',
            'lang',
        ];

        $response = (new OpenWeatherMapService(new PendingRequest()))
            ->filterQueryParams($queryParams, $availableQueryKeys);

        $diff = array_diff(array_keys($queryParams), array_keys($response));
        $result = true;

        // Check if any filtered value from the difference between variable $queryParams and $response is present
        // in the $availableQueryKeys variable
        foreach ($diff as $k) {
            if (in_array($k, $availableQueryKeys)) {
                $result = false;
                break;
            }
        }

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function filter_query_params_invalid_params(): void
    {
        // $queryParams is not associative list
        $queryParams = [
            'lat',
            'lon' => 'test',
            'exclude' => 'test',
            'test' => 'test',
        ];
        $availableQueryKeys = [
            'lat',
            'lon',
            'exclude',
            'units',
            'lang',
        ];

        $response = (new OpenWeatherMapService(new PendingRequest()))
            ->filterQueryParams($queryParams, $availableQueryKeys);

        $this->assertEmpty($response);

        // $queryParams is not associative list and $availableQueryKeys is not sequential list
        $queryParams = [
            'lat',
            'lon' => 'test',
            'exclude' => 'test',
            'test' => 'test',
        ];
        $availableQueryKeys = [
            'lat' => 'test',
            'lon',
            'exclude',
            'units',
            'lang',
        ];

        $response = (new OpenWeatherMapService(new PendingRequest()))
            ->filterQueryParams($queryParams, $availableQueryKeys);

        $this->assertEmpty($response);

        // $availableQueryKeys is not sequential list
        $queryParams = [
            'lat' => 'test',
            'lon' => 'test',
            'exclude' => 'test',
            'test' => 'test',
        ];
        $availableQueryKeys = [
            'lat' => 'test',
            'lon',
            'exclude',
            'units',
            'lang',
        ];

        $response = (new OpenWeatherMapService(new PendingRequest()))
            ->filterQueryParams($queryParams, $availableQueryKeys);

        $this->assertEmpty($response);
    }
}
