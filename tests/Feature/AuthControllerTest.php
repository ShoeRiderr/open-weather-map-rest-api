<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use Tests\Traits\AuthHelper;

class AuthControllerTest extends TestCase
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
        $user = User::factory()->create($data);

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
        $user = User::factory()->create($data);

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

    /**
     * @test
     */
    public function logout_successfull()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('api/logout');

        $response
            ->assertExactJson([
                'message' => __('auth.log_out')
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function logout_error()
    {
        // Unauthenticated
        $response = $this->postJson('api/logout');

        $response
            ->assertExactJson([
                'message' => 'Unauthenticated.',
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function register_successfull(): void
    {
        $data = $this->prepareUserData();
        $data['password_confirmation'] = $data['password'];

        $response = $this->postJson('api/register', $data);

        $response
            ->assertExactJson([
                'data' => [
                    'id' => 1,
                    'name' => 'test',
                    'surname' => 'Test',
                    'email' => 'test@example.com',
                ]
            ])
            ->assertJsonMissing([
                'data.password' => 'password',
            ])
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('users', [

            'name' => 'test',
            'surname' => 'Test',
            'email' => 'test@example.com',
        ]);
    }

    /**
     * @test
     */
    public function register_wrong_email()
    {
        $user = User::factory()->createOne();

        $data = $this->prepareUserData();
        $data['password_confirmation'] = $data['password'];

        // Wrong email
        $data['email'] = 'test';

        $response = $this->postJson('api/register', $data);

        $response
            ->assertInvalid([
                'email' => __('validation.email', [
                    'attribute' => 'email'
                ])
            ])
            ->assertValid(['name', 'surname', 'password'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('users', [
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
        ]);

        // Duplicated email
        $data['email'] = $user->email;

        $response = $this->postJson('api/register', $data);

        $response
            ->assertInvalid([
                'email' => __('validation.unique', [
                    'attribute' => 'email'
                ])
            ])
            ->assertValid(['name', 'surname', 'password'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('users', [
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $user->email,
        ]);

        // No email
        unset($data['email']);

        $response = $this->postJson('api/register', $data);

        $response
            ->assertInvalid([
                'email' => __('validation.required', [
                    'attribute' => 'email'
                ])
            ])
            ->assertValid(['name', 'surname', 'password'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('users', [
            'name' => $data['name'],
            'surname' => $data['surname'],
        ]);
    }

    /**
     * @test
     */
    public function register_wrong_name()
    {
        $data = $this->prepareUserData();
        $data['password_confirmation'] = $data['password'];

        unset($data['name']);

        $response = $this->postJson('api/register', $data);

        $response
            ->assertInvalid([
                'name' => __('validation.required', [
                    'attribute' => 'name'
                ])
            ])
            ->assertValid(['email', 'surname', 'password'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('users', [
            'surname' => $data['surname'],
            'email' => $data['email'],
        ]);
    }

    /**
     * @test
     */
    public function register_wrong_surname()
    {
        $data = $this->prepareUserData();
        $data['password_confirmation'] = $data['password'];

        unset($data['surname']);

        $response = $this->postJson('api/register', $data);

        $response
            ->assertInvalid([
                'surname' => __('validation.required', [
                    'attribute' => 'surname'
                ])
            ])
            ->assertValid(['email', 'name', 'password'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * @test
     */
    public function register_wrong_password()
    {
        // No confirmation
        $data = $this->prepareUserData();

        $response = $this->postJson('api/register', $data);

        $response
            ->assertInvalid([
                'password' => __('validation.confirmed', [
                    'attribute' => 'password'
                ])
            ])
            ->assertValid(['email', 'name', 'surname'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('users', [
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
        ]);

        // Wrong confirmation
        $data['password_confirmation'] = $data['password'] . 'test';

        $response = $this->postJson('api/register', $data);

        $response
            ->assertInvalid([
                'password' => __('validation.confirmed', [
                    'attribute' => 'password'
                ])
            ])
            ->assertValid(['email', 'name', 'surname'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('users', [
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
        ]);

        // No password
        unset($data['password_confirmation'], $data['password']);

        $response = $this->postJson('api/register', $data);

        $response
            ->assertInvalid([
                'password' => __('validation.required', [
                    'attribute' => 'password'
                ]),
            ])
            ->assertValid(['email', 'name', 'surname'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseMissing('users', [
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
        ]);
    }
}
