<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenWeatherMapControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    private $body;

    /**
     * @var User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->body = file_get_contents(
            base_path('tests/Fixtures/Helpers/open_weather_map_current_date_data.json')
        );

        $this->user = User::factory()->create();
    }

    /**
     * @test
     */
    public function fetching_current_weather_credential_successfull(): void
    {
        $expectedResponse = file_get_contents(
            base_path('tests/Fixtures/Helpers/current_weather_credential_expected_response.json')
        );

        $params = [
            'lat' => 55,
            'lon' => 15
        ];

        Http::fake([
            Config::get('openweathermap.base_url') . '*' => Http::response($this->body, Response::HTTP_OK),
        ]);

        $response = $this->actingAs($this->user)->json(
            'GET',
            'api/weather/current',
            $params
        );

        $response
            ->assertJson(json_decode($expectedResponse, true))
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function fetching_current_weather_credential_with_invalid_data()
    {
        Http::fake([
            Config::get('openweathermap.base_url') . '*' => Http::response($this->body, Response::HTTP_OK),
        ]);

        // Missing lat param
        $params = [
            'lon' => 15
        ];

        $response = $this->actingAs($this->user)->json(
            'GET',
            'api/weather/current',
            $params
        );

        $response
            ->assertInvalid([
                'lat' => __('validation.required', [
                    'attribute' => 'lat',
                ])
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        // Missing lon param
        $params = [
            'lat' => 15
        ];

        $response = $this->actingAs($this->user)->json(
            'GET',
            'api/weather/current',
            $params
        );

        $response
            ->assertInvalid([
                'lon' => __('validation.required', [
                    'attribute' => 'lon',
                ])
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);


        // Missing lat and lon param
        $params = [];

        $response = $this->actingAs($this->user)->json(
            'GET',
            'api/weather/current',
            $params
        );

        $response
            ->assertInvalid([
                'lon' => __('validation.required', [
                    'attribute' => 'lon',
                ]),
                'lat' => __('validation.required', [
                    'attribute' => 'lat',
                ])
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
