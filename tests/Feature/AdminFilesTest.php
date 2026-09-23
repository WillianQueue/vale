<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminFilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_administrators_can_access_file_panel(): void
    {
        $user = User::factory()->create();
        $admin = $this->createAdmin();

        $this->actingAs($user)
            ->get(route('admin.files'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.files'))
            ->assertOk()
            ->assertSee('Armazenar arquivos');
    }

    public function test_administrator_can_upload_and_delete_a_document(): void
    {
        $admin = $this->createAdmin();
        $arquivo = UploadedFile::fake()->createWithContent(
            'base-teste.txt',
            'Conteúdo do documento de teste.'
        );

        $response = $this->actingAs($admin)
            ->post(route('admin.files.upload'), [
                'arquivo' => $arquivo,
            ]);

        $response->assertRedirect();
        $arquivos = File::files(storage_path('app/documentos'));
        $arquivoCriado = collect($arquivos)
            ->first(fn ($item) => str_starts_with($item->getFilename(), 'base-teste-'));

        $this->assertNotNull($arquivoCriado);

        $this->actingAs($admin)
            ->delete(route('admin.files.deletar', $arquivoCriado->getFilename()))
            ->assertRedirect();

        $this->assertFalse(File::exists($arquivoCriado->getPathname()));
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin->refresh();
    }
}
