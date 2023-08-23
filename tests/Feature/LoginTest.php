<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\AuthHelper;

class LoginTest extends TestCase
{
    use WithFaker;
    use DatabaseMigrations;
    use RefreshDatabase;
    use AuthHelper;

    /**
     * @test
     */
    public function login_successfull(): void
    {
        $user = User::create($this->prepareUserData());

        $response = $this->postJson('api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertJsonStructure([
            'token'
        ])
            ->assertOk();
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
            ->assertInvalid([
                'error' => __('auth.failed')
            ])
            ->assertStatus(422);

        // wrong password
        $response = $this->postJson('api/login', [
            'email' => $user->email,
            'password' => $data['password'] . 'test',
        ]);

        $response
            ->assertInvalid([
                'error' => __('auth.failed')
            ])
            ->assertStatus(422);

        // wrong password and email
        $response = $this->postJson('api/login', [
            'email' => $user->email . 'test',
            'password' => $data['password'] . 'test',
        ]);

        $response
            ->assertInvalid([
                'error' => __('auth.failed')
            ])
            ->assertStatus(422);
    }
}
