<?php

namespace Tests\Unit\Profile;

use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Unit test ProfileController::deleteAvatar().
 * Test Double: Spy (User yang mencatat save()), Stub (resolver user pada Request),
 * Fake (Storage::fake untuk disk "public").
 */
class DeleteAvatarTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public'); // Fake filesystem
    }

    /** Spy: User asli yang hanya mencatat berapa kali save() dipanggil (tanpa menyentuh database). */
    private function makeUserSpy(?string $avatar): User
    {
        $user = new class extends User {
            public int $saveCalls = 0;

            public function save(array $options = [])
            {
                $this->saveCalls++;

                return true;
            }
        };
        $user->avatar = $avatar;

        return $user;
    }

    /** Stub: request yang selalu mengembalikan user yang sudah ditentukan. */
    private function makeRequest(User $user): Request
    {
        $request = Request::create('/profile/avatar', 'DELETE');
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    /** PF-TC-01: avatar ada di storage -> file dihapus, avatar = null, disimpan. */
    public function test_delete_avatar_removes_file_and_clears_avatar_column(): void
    {
        // Arrange
        Storage::disk('public')->put('avatars/budi.jpg', 'isi-gambar');
        $user = $this->makeUserSpy('avatars/budi.jpg');
        $controller = new ProfileController();

        // Act
        $response = $controller->deleteAvatar($this->makeRequest($user));

        // Assert
        Storage::disk('public')->assertMissing('avatars/budi.jpg');
        $this->assertNull($user->avatar);
        $this->assertSame(1, $user->saveCalls);
        $this->assertSame(route('profile.edit'), $response->getTargetUrl());
        $this->assertSame('avatar-deleted', $response->getSession()->get('status'));
    }

    /** PF-TC-02: kolom avatar terisi tetapi file sudah tidak ada -> tidak error. */
    public function test_delete_avatar_does_not_fail_when_file_is_already_missing(): void
    {
        // Arrange
        $user = $this->makeUserSpy('avatars/hilang.jpg');
        $controller = new ProfileController();

        // Act
        $response = $controller->deleteAvatar($this->makeRequest($user));

        // Assert
        $this->assertNull($user->avatar);
        $this->assertSame(1, $user->saveCalls);
        $this->assertSame('avatar-deleted', $response->getSession()->get('status'));
    }

    /** PF-TC-03: user tidak punya avatar -> file lain tidak boleh terhapus. */
    public function test_delete_avatar_leaves_other_files_untouched_when_user_has_no_avatar(): void
    {
        // Arrange
        Storage::disk('public')->put('avatars/milik-orang-lain.jpg', 'isi-gambar');
        $user = $this->makeUserSpy(null);
        $controller = new ProfileController();

        // Act
        $response = $controller->deleteAvatar($this->makeRequest($user));

        // Assert
        Storage::disk('public')->assertExists('avatars/milik-orang-lain.jpg');
        $this->assertNull($user->avatar);
        $this->assertSame(1, $user->saveCalls);
        $this->assertSame('avatar-deleted', $response->getSession()->get('status'));
    }
}
