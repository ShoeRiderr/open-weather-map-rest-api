<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function delete_successfull(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson('api/users/' . $user->id);

        $response
            ->assertExactJson([
                'message' => __('user.delete.success')
            ])
            ->assertStatus(200);

        $this->assertDatabaseMissing('users', $user->toArray());
    }

    /**
     * @test
     */
    public function delete_unauthorized(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson('api/users/' . $user->id);

        $response
            ->assertExactJson([
                'message' => 'Unauthenticated.',
            ])
            ->assertStatus(401);

        $this->assertDatabaseMissing('users', $user->toArray());
    }

    /**
     * @test
     */
    public function delete_other_user(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson('api/users/' . $user2->id);

        $response
            ->assertExactJson([
                'error' => __('user.delete.remove_other_user'),
            ])
            ->assertStatus(422);

        $this->assertDatabaseHas('users', [
            'id' => $user2->id,
            'name' => $user2->name,
            'surname' => $user2->surname,
            'email' => $user2->email,
        ]);
    }
}
