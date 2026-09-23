<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_open_another_users_conversation(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $conversation = Conversation::create([
            'user_id' => $owner->id,
            'title' => 'Privada',
        ]);

        $this->actingAs($otherUser)
            ->get(route('chat.show', $conversation))
            ->assertForbidden();
    }

    public function test_user_can_delete_their_own_conversation(): void
    {
        $user = User::factory()->create();
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'title' => 'Minha conversa',
        ]);

        $this->actingAs($user)
            ->delete(route('chat.destroy', $conversation))
            ->assertOk()
            ->assertJson(['status' => 'sucesso']);

        $this->assertDatabaseMissing('conversations', [
            'id' => $conversation->id,
        ]);
    }
}
