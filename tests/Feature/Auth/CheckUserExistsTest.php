<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckUserExistsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_true_if_email_exists()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/check-user-exists', ['email' => 'test@example.com']);

        $response->assertStatus(200)
                 ->assertJson(['exists' => true]);
    }

    /** @test */
    public function it_returns_false_if_email_does_not_exist()
    {
        $response = $this->postJson('/api/check-user-exists', ['email' => 'nonexistent@example.com']);

        $response->assertStatus(200)
                 ->assertJson(['exists' => false]);
    }

    /** @test */
    public function it_returns_validation_error_for_invalid_email()
    {
        $response = $this->postJson('/api/check-user-exists', ['email' => 'invalid-email']);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_returns_validation_error_if_email_is_not_provided()
    {
        $response = $this->postJson('/api/check-user-exists', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('email');
    }
}
