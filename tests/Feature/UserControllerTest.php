<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @test
     */
    public function delete_successfull(): void
    {
        $response = $this->actingAs($this->user)->deleteJson('api/users/' . $this->user->id);

        $response
            ->assertExactJson([
                'message' => __('user.delete.success')
            ])
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('users', $this->user->toArray());
    }

    /**
     * @test
     */
    public function delete_unauthorized(): void
    {
        $response = $this->deleteJson('api/users/' . $this->user->id);

        $response
            ->assertExactJson([
                'message' => 'Unauthenticated.',
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseMissing('users', $this->user->toArray());
    }

    /**
     * @test
     */
    public function attempt_to_delete_other_user(): void
    {
        $user2 = User::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson('api/users/' . $user2->id);

        $response
            ->assertExactJson([
                'error' => __('user.delete.remove_other_user'),
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'name' => $user2->name,
            'surname' => $user2->surname,
            'email' => $user2->email,
        ]);
    }
}
