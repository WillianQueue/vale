<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConversationHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_prompt_uses_history_from_the_active_conversation_only(): void
    {
        Http::fake([
            '*' => Http::response("{\"response\":\"Você enviou 5.\",\"done\":true}\n"),
        ]);

        $user = User::factory()->create();
        $conversation = $this->createConversationWithMessage(
            $user,
            'Vou te enviar o número 5.',
            'Entendi, você enviou o número 5.'
        );
        $this->createConversationWithMessage(
            $user,
            'Informação privada de outra conversa.',
            'Esta mensagem não deve entrar no prompt.'
        );

        $this->actingAs($user)
            ->postJson(route('chat.enviar'), [
                'pergunta' => 'Qual número eu enviei?',
                'conversation_id' => $conversation->id,
                'historico' => [[
                    'question' => 'Histórico enviado pelo cliente.',
                    'answer' => 'Não deve substituir o histórico persistido.',
                ]],
            ])
            ->assertOk()
            ->streamedContent();

        Http::assertSent(function (ClientRequest $request): bool {
            $prompt = $request['prompt'];

            return str_contains($prompt, 'Usuário: Vou te enviar o número 5.')
                && str_contains($prompt, 'Vale: Entendi, você enviou o número 5.')
                && !str_contains($prompt, 'Informação privada de outra conversa.')
                && !str_contains($prompt, 'Histórico enviado pelo cliente.')
                && str_contains($prompt, "PERGUNTA:\nQual número eu enviei?");
        });
    }

    public function test_guest_prompt_contains_history_from_the_current_page(): void
    {
        Http::fake([
            '*' => Http::response("{\"response\":\"Você enviou 5.\",\"done\":true}\n"),
        ]);

        $this->postJson(route('chat.enviar'), [
            'pergunta' => 'Qual número eu enviei?',
            'historico' => [[
                'question' => 'Vou te enviar o número 5.',
                'answer' => 'Certo, vou lembrar do número 5 nesta conversa.',
            ]],
        ])
            ->assertOk()
            ->streamedContent();

        Http::assertSent(fn (ClientRequest $request): bool =>
            str_contains(
                $request['prompt'],
                "Usuário: Vou te enviar o número 5.\n"
                . 'Vale: Certo, vou lembrar do número 5 nesta conversa.'
            )
        );
    }

    public function test_guest_prompt_contains_more_than_ten_history_turns(): void
    {
        Http::fake([
            '*' => Http::response("{\"response\":\"Resposta.\",\"done\":true}\n"),
        ]);

        $historico = [];

        for ($turno = 1; $turno <= 12; $turno++) {
            $historico[] = [
                'question' => "Pergunta {$turno}",
                'answer' => "Resposta {$turno}",
            ];
        }

        $this->postJson(route('chat.enviar'), [
            'pergunta' => 'Pergunta atual',
            'historico' => $historico,
        ])
            ->assertOk()
            ->streamedContent();

        Http::assertSent(fn (ClientRequest $request): bool =>
            str_contains($request['prompt'], 'Usuário: Pergunta 1')
            && str_contains($request['prompt'], 'Usuário: Pergunta 12')
        );
    }

    public function test_history_prompt_is_not_truncated_at_twelve_thousand_characters(): void
    {
        Http::fake([
            '*' => Http::response("{\"response\":\"Resposta.\",\"done\":true}\n"),
        ]);

        $historico = [];

        for ($turno = 0; $turno < 10; $turno++) {
            $historico[] = [
                'question' => str_repeat('Q', 1000),
                'answer' => str_repeat('A', 2000),
            ];
        }

        $this->postJson(route('chat.enviar'), [
            'pergunta' => 'Pergunta atual',
            'historico' => $historico,
        ])
            ->assertOk()
            ->streamedContent();

        Http::assertSent(function (ClientRequest $request): bool {
            $prompt = $request['prompt'];
            $historyStart = strpos($prompt, "HISTÓRICO DA CONVERSA (do mais antigo ao mais recente):\n");
            $questionStart = strpos($prompt, "\n\nPERGUNTA:\n");

            if ($historyStart === false || $questionStart === false) {
                return false;
            }

            $historyStart += mb_strlen(
                "HISTÓRICO DA CONVERSA (do mais antigo ao mais recente):\n"
            );

            return mb_strlen(substr($prompt, $historyStart, $questionStart - $historyStart)) > 12000;
        });
    }

    public function test_authenticated_prompt_contains_all_turns_from_the_active_conversation(): void
    {
        Http::fake([
            '*' => Http::response("{\"response\":\"Resposta.\",\"done\":true}\n"),
        ]);

        $user = User::factory()->create();
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'title' => 'Conversa longa',
        ]);

        for ($turno = 1; $turno <= 12; $turno++) {
            Chat::create([
                'user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'pergunta' => "Pergunta salva {$turno}",
                'resposta' => "Resposta salva {$turno}",
                'fonte' => 'conhecimento_geral',
            ]);
        }

        $this->actingAs($user)
            ->postJson(route('chat.enviar'), [
                'pergunta' => 'Continue a conversa.',
                'conversation_id' => $conversation->id,
            ])
            ->assertOk()
            ->streamedContent();

        Http::assertSent(fn (ClientRequest $request): bool =>
            str_contains($request['prompt'], 'Usuário: Pergunta salva 1')
            && str_contains($request['prompt'], 'Vale: Resposta salva 1')
            && str_contains($request['prompt'], 'Usuário: Pergunta salva 12')
            && str_contains($request['prompt'], 'Vale: Resposta salva 12')
        );
    }

    public function test_user_cannot_send_a_prompt_using_another_users_conversation(): void
    {
        Http::fake();

        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $conversation = Conversation::create([
            'user_id' => $owner->id,
            'title' => 'Conversa privada',
        ]);

        $this->actingAs($otherUser)
            ->postJson(route('chat.enviar'), [
                'pergunta' => 'O que foi dito?',
                'conversation_id' => $conversation->id,
            ])
            ->assertNotFound();

        Http::assertNothingSent();
    }

    private function createConversationWithMessage(
        User $user,
        string $question,
        string $answer
    ): Conversation {
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'title' => $question,
        ]);

        Chat::create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'pergunta' => $question,
            'resposta' => $answer,
            'fonte' => 'conhecimento_geral',
        ]);

        return $conversation;
    }
}
