<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se um novo usuário pode ser criado com sucesso
     */
    public function test_user_can_be_created_successfully()
    {
        $userData = [
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'joao@example.com',
            'username' => 'joao_silva',
            'password' => 'password123',
            'mobile' => '5511999999999',
            'gender' => 'male',
            'status' => 1,
            'is_subscribe' => 0,
        ];

        $user = User::create([
            ...$userData,
            'password' => Hash::make($userData['password']),
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com',
            'username' => 'joao_silva',
            'first_name' => 'João',
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Testa se o email precisa ser único
     */
    public function test_user_email_must_be_unique()
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'duplicate@example.com',
            'password' => Hash::make('password123'),
            'status' => 1,
        ]);
    }

    /**
     * Testa se usuário pode ter dados atualizados corretamente
     */
    public function test_user_can_be_updated()
    {
        $user = User::factory()->create([
            'first_name' => 'João',
            'is_subscribe' => 0,
        ]);

        $user->update([
            'first_name' => 'João Atualizado',
            'is_subscribe' => 1,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'João Atualizado',
            'is_subscribe' => 1,
        ]);
    }

    /**
     * Testa se usuário pode ser deletado (soft delete)
     */
    public function test_user_soft_delete_works()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        // Não deve aparecer na query normal
        $this->assertFalse(User::where('id', $userId)->exists());

        // Deve aparecer com withTrashed()
        $this->assertTrue(User::withTrashed()->where('id', $userId)->exists());
    }

    /**
     * Testa se usuário pode ser restaurado após soft delete
     */
    public function test_user_can_be_restored()
    {
        $user = User::factory()->create();
        $user->delete();

        $user->restore();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /**
     * Testa se full_name attribute funciona corretamente
     */
    public function test_full_name_attribute()
    {
        $user = User::factory()->create([
            'first_name' => 'João',
            'last_name' => 'Silva',
        ]);

        $this->assertEquals('João Silva', $user->full_name);
    }

    /**
     * Testa criação múltipla de usuários
     */
    public function test_create_multiple_users()
    {
        $users = User::factory()->count(10)->create();

        $this->assertCount(10, $users);
        $this->assertDatabaseCount('users', 10);
    }

    /**
     * Testa se usuário pode ser buscado por email
     */
    public function test_user_can_be_found_by_email()
    {
        $user = User::factory()->create(['email' => 'search@example.com']);

        $foundUser = User::where('email', 'search@example.com')->first();

        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }
}
