<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Pessoa Teste',
            'email' => 'pessoa@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'pessoa@example.com',
        ]);
    }

    public function test_invalid_login_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'pessoa@example.com',
            'password' => bcrypt('senha-correta'),
        ]);

        $response = $this->from(route('login'))
            ->post(route('login.post'), [
                'email' => $user->email,
                'password' => 'senha-incorreta',
            ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
