<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_administrators_can_access_account_panel(): void
    {
        $user = User::factory()->create();
        $admin = $this->createAdmin();

        $this->actingAs($user)
            ->get(route('admin.accounts'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.accounts'))
            ->assertOk()
            ->assertSee('Gerenciar contas');
    }

    public function test_administrator_can_promote_and_edit_a_account(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create([
            'name' => 'Usuário Original',
            'email' => 'original@example.com',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.accounts.toggle-admin', $user))
            ->assertRedirect();

        $this->assertTrue($user->refresh()->is_admin);

        $this->actingAs($admin)
            ->put(route('admin.accounts.update', $user), [
                'name' => 'Usuário Editado',
                'email' => 'editado@example.com',
                'password' => 'nova-senha',
                'password_confirmation' => 'nova-senha',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Usuário Editado',
            'email' => 'editado@example.com',
        ]);
    }

    public function test_administrator_cannot_delete_the_own_account(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->delete(route('admin.accounts.delete', $admin))
            ->assertRedirect()
            ->assertSessionHasErrors('conta');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_deleting_a_user_also_deletes_their_conversations(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create();
        Conversation::create([
            'user_id' => $user->id,
            'title' => 'Conversa de teste',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.accounts.delete', $user))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('conversations', ['user_id' => $user->id]);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin->refresh();
    }
}
