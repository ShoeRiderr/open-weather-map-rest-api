<?php

namespace App\Http\Resources\OpenWeatherMap;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentWeatherResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'temp' => $this->get('temp'),
            'pressure' => $this->get('pressure'),
            'humidity' => $this->get('humidity'),
            'clouds' => $this->get('clouds'),
            'wind_speed' => $this->get('wind_speed'),
            'wind_deg' => $this->get('wind_deg'),
        ];
    }
}
