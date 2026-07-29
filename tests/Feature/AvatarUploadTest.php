<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengguna_dapat_mengganti_foto_profil(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['avatar_path' => null]);

        $response = $this->actingAs($user)->post('/profil/foto', [
            'avatar' => UploadedFile::fake()->create('foto.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertRedirect(route('profil.edit'));

        $user->refresh();

        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_foto_lama_dihapus_saat_diganti(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)->post('/profil/foto', [
            'avatar' => UploadedFile::fake()->create('lama.jpg', 100, 'image/jpeg'),
        ]);

        $oldPath = $user->refresh()->avatar_path;

        $this->actingAs($user)->post('/profil/foto', [
            'avatar' => UploadedFile::fake()->create('baru.jpg', 100, 'image/jpeg'),
        ]);

        $newPath = $user->refresh()->avatar_path;

        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_berkas_bukan_gambar_ditolak(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['avatar_path' => null]);

        $response = $this->actingAs($user)->post('/profil/foto', [
            'avatar' => UploadedFile::fake()->create('dokumen.pdf', 100),
        ]);

        $response->assertSessionHasErrors('avatar');

        $this->assertNull($user->refresh()->avatar_path);
    }
}
