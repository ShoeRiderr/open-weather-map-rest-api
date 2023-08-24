<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpenWeatherMap\GetCurrendWeatherRequest;
use App\Http\Resources\OpenWeatherMap\CurrentWeatherResource;
use App\Services\OpenWeatherMapService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class OpenWeatherMapController extends Controller
{
    public function __construct(private OpenWeatherMapService $openWeatherMapService)
    {
    }

    public function getCurrentWeather(GetCurrendWeatherRequest $request)
    {
        try {
            $response = $this->openWeatherMapService
                ->getCurrentWeatherInfo($request->validated())
                ->getResponse();

            return CurrentWeatherResource::make($response->collect('current'));
        } catch (Throwable $e) {
            Log::error($e);

            return response()->json(['error' => __('error.server_error')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
