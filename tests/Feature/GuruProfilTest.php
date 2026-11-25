<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuruProfilTest extends TestCase
{
    use RefreshDatabase;

    private User $guruUser;

    private Guru $guruModel;

    private Cabang $cabang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cabang = Cabang::create([
            'kode' => 'CAB-TEST-01',
            'nama' => 'Cabang Test Profil',
            'alamat' => 'Jl. Pendidikan No. 1',
            'status' => 'aktif',
        ]);

        $this->guruUser = User::create([
            'name' => 'Bu Guru Profil',
            'email' => 'guru.profil@test.com',
            'password' => Hash::make('1995-05-14'),
            'role' => 'guru',
            'no_pegawai' => 'NIP-PRF-01',
            'tanggal_lahir' => '1995-05-14',
            'cabang_id' => $this->cabang->id,
            'is_active' => true,
        ]);

        $this->guruModel = Guru::create([
            'user_id' => $this->guruUser->id,
            'nama' => 'Bu Guru Profil',
            'email' => $this->guruUser->email,
            'no_pegawai' => 'NIP-PRF-01',
            'no_telp' => '081234567890',
            'tanggal_lahir' => '1995-05-14',
            'alamat' => 'Alamat Guru Asli',
            'jenjang' => 'SD',
            'is_active' => true,
        ]);
    }

    public function test_guru_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->guruUser)->get(route('guru.profil'));

        $response->assertStatus(200);
        $response->assertSee('Bu Guru Profil');
        $response->assertSee('NIP-PRF-01');
        $response->assertSee('guru.profil@test.com');
        $response->assertSee('081234567890');
        $response->assertSee('1995-05-14');
        $response->assertSee('Hanya lihat');
        $response->assertSee('Ganti Password Akun');
    }

    public function test_non_guru_cannot_access_guru_profile(): void
    {
        $cabangUser = User::create([
            'name' => 'Pimpinan Cabang',
            'email' => 'cabang@test.com',
            'password' => Hash::make('password'),
            'role' => 'cabang',
            'cabang_id' => $this->cabang->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($cabangUser)->get(route('guru.profil'));

        $response->assertStatus(403);
    }

    public function test_guru_can_update_profile_with_numeric_phone(): void
    {
        $response = $this->actingAs($this->guruUser)->put(route('guru.profil.update'), [
            'nama' => 'Bu Guru Profil S.Pd.',
            'no_telp' => '089876543210',
            'alamat' => 'Jl. Mawar Baru No. 10',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $this->guruUser->id,
            'name' => 'Bu Guru Profil S.Pd.',
        ]);
        $this->assertDatabaseHas('guru', [
            'id' => $this->guruModel->id,
            'nama' => 'Bu Guru Profil S.Pd.',
            'no_telp' => '089876543210',
            'alamat' => 'Jl. Mawar Baru No. 10',
        ]);
    }

    public function test_guru_cannot_update_phone_with_non_numeric_characters(): void
    {
        $response = $this->actingAs($this->guruUser)->put(route('guru.profil.update'), [
            'nama' => 'Bu Guru Profil S.Pd.',
            'no_telp' => 'abcdefghij',
            'alamat' => 'Jl. Mawar Baru No. 10',
        ]);

        $response->assertSessionHasErrors(['no_telp']);
    }

    public function test_birthdate_is_not_overwritten_by_profile_update(): void
    {
        $originalTglLahir = $this->guruModel->tanggal_lahir;

        $response = $this->actingAs($this->guruUser)->put(route('guru.profil.update'), [
            'nama' => 'Bu Guru Profil S.Pd.',
            'no_telp' => '089876543210',
            'alamat' => 'Jl. Mawar Baru No. 10',
            'tanggal_lahir' => '2000-01-01', // Attacker trying to change birthdate
        ]);

        $response->assertSessionHas('success');
        $freshGuru = $this->guruModel->fresh();
        $this->assertEquals($originalTglLahir, $freshGuru->tanggal_lahir);
    }

    public function test_guru_can_update_password_with_correct_current_password(): void
    {
        $response = $this->actingAs($this->guruUser)->put(route('guru.profil.password'), [
            'current_password' => '1995-05-14',
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'PasswordBaru123',
        ]);

        $response->assertSessionHas('success');

        $freshUser = $this->guruUser->fresh();
        $this->assertTrue(Hash::check('PasswordBaru123', $freshUser->password));
    }

    public function test_guru_cannot_update_password_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->guruUser)->put(route('guru.profil.password'), [
            'current_password' => 'PasswordSalah',
            'password' => 'PasswordBaru123',
            'password_confirmation' => 'PasswordBaru123',
        ]);

        $response->assertSessionHasErrors(['current_password']);

        $freshUser = $this->guruUser->fresh();
        $this->assertTrue(Hash::check('1995-05-14', $freshUser->password));
    }

    public function test_guru_can_upload_profile_photo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('guru_avatar.jpg', 200, 200);

        $response = $this->actingAs($this->guruUser)->put(route('guru.profil.update'), [
            'nama' => 'Bu Guru Profil S.Pd.',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Mawar Baru No. 10',
            'foto' => $file,
        ]);

        $response->assertSessionHas('success');

        $freshUser = $this->guruUser->fresh();
        $freshGuru = $this->guruModel->fresh();

        $this->assertNotNull($freshUser->foto);
        $this->assertEquals($freshUser->foto, $freshGuru->foto);
        $this->assertStringEndsWith('.webp', $freshUser->foto);
        Storage::disk('public')->assertExists($freshUser->foto);
    }

    public function test_guru_can_delete_profile_photo(): void
    {
        Storage::fake('public');

        $existingPath = 'profil/test_old.jpg';
        Storage::disk('public')->put($existingPath, 'dummy-content');

        $this->guruUser->update(['foto' => $existingPath]);
        $this->guruModel->update(['foto' => $existingPath]);

        $response = $this->actingAs($this->guruUser)->put(route('guru.profil.update'), [
            'nama' => 'Bu Guru Profil S.Pd.',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Mawar Baru No. 10',
            'hapus_foto' => 1,
        ]);

        $response->assertSessionHas('success');

        $freshUser = $this->guruUser->fresh();
        $freshGuru = $this->guruModel->fresh();

        $this->assertNull($freshUser->foto);
        $this->assertNull($freshGuru->foto);
        Storage::disk('public')->assertMissing($existingPath);
    }

    public function test_guru_cannot_upload_invalid_file_for_profile_photo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->guruUser)->put(route('guru.profil.update'), [
            'nama' => 'Bu Guru Profil S.Pd.',
            'no_telp' => '081234567890',
            'alamat' => 'Jl. Mawar Baru No. 10',
            'foto' => $file,
        ]);

        $response->assertSessionHasErrors(['foto']);
    }
}
