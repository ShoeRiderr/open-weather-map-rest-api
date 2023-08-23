<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\Traits\AuthHelper;

class LoginTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use AuthHelper;

    /**
     * @test
     */
    public function login_successfull(): void
    {
        $data = $this->prepareUserData();
        $user = User::create($data);

        $response = $this->postJson('api/login', [
            'email' => $user->email,
            'password' => $data['password'],
        ]);

        $response->assertJsonStructure([
            'message',
            'token',
        ])
            ->assertStatus(Response::HTTP_OK);

        $this->assertEquals(auth()->id(), $user->id);
    }

    /**
     * @test
     */
    public function login_fail(): void
    {
        $data = $this->prepareUserData();
        $user = User::create($data);

        // wrong email
        $response = $this->postJson('api/login', [
            'email' => $user->email . 'test',
            'password' => $data['password'],
        ]);

        $response
            ->assertExactJson([
                'error' => __('auth.failed')
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertNotEquals(auth()->id(), $user->id);

        // wrong password
        $response = $this->postJson('api/login', [
            'email' => $user->email,
            'password' => $data['password'] . 'test',
        ]);

        $response
            ->assertExactJson([
                'error' => __('auth.failed')
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertNotEquals(auth()->id(), $user->id);

        // wrong password and email
        $response = $this->postJson('api/login', [
            'email' => $user->email . 'test',
            'password' => $data['password'] . 'test',
        ]);

        $response
            ->assertExactJson([
                'error' => __('auth.failed')
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertNotEquals(auth()->id(), $user->id);
    }
}
