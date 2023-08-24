<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpenWheatherMap\GetCurrendWheatherRequest;
use App\Http\Resources\OpenWheatherMap\CurrentWheatherResource;
use App\Services\OpenWheatherMapService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class OpenWheatherMapController extends Controller
{
    public function __construct(private OpenWheatherMapService $openWheatherMapService)
    {
    }

    public function getCurrentWheather(GetCurrendWheatherRequest $request)
    {
        try {
            $response = $this->openWheatherMapService
                ->getCurrentWheatherInfo($request->validated())
                ->getResponse();

            return CurrentWheatherResource::make($response->collect('current'));
        } catch (Throwable $e) {
            Log::error($e);

            return response()->json(['error' => __('error.server_error')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
