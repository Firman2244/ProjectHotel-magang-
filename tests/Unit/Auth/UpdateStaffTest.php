<?php

namespace Tests\Unit\Staff;

use App\Http\Controllers\Admin\StaffController;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Unit test StaffController::update() (admin mengubah data staf, password opsional).
 * Test Double: Fake (SQLite ":memory:").
 */
class UpdateStaffTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotel;
    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hotel = Hotel::create(['name' => 'Hotel A']);
        $this->staff = User::factory()->create([
            'email' => 'budi@hotel.test', 'hotel_id' => $this->hotel->id, 'department' => 'Housekeeping', 'role' => 'staff',
        ]);
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'name' => 'Budi Baru', 'email' => 'budi@hotel.test',
            'hotel_id' => $this->hotel->id, 'department' => 'Engineering',
        ], $override);
    }

    private function runUpdate(array $input)
    {
        return (new StaffController())->update(Request::create('/admin/staff/1', 'PUT', $input), $this->staff);
    }

    /** SF-TC-01: tanpa password -> data berubah, password lama tetap. */
    public function test_update_keeps_old_password_when_password_is_blank(): void
    {
        // Arrange
        $oldHash = $this->staff->password;

        // Act
        $response = $this->runUpdate($this->payload());

        // Assert
        $fresh = $this->staff->fresh();
        $this->assertSame('Budi Baru', $fresh->name);
        $this->assertSame('Engineering', $fresh->department);
        $this->assertSame($oldHash, $fresh->password);
        $this->assertSame('Data staf berhasil diperbarui!', $response->getSession()->get('success'));
    }

    /** SF-TC-02: password valid + konfirmasi -> password di-hash dan diganti. */
    public function test_update_changes_password_when_valid(): void
    {
        // Arrange
        $input = $this->payload(['password' => 'PasswordBaru123!', 'password_confirmation' => 'PasswordBaru123!']);

        // Act
        $this->runUpdate($input);

        // Assert
        $this->assertTrue(Hash::check('PasswordBaru123!', $this->staff->fresh()->password));
    }

    /** SF-TC-03: konfirmasi password tidak cocok -> ValidationException. */
    public function test_update_rejects_unconfirmed_password(): void
    {
        // Arrange
        $input = $this->payload(['password' => 'PasswordBaru123!', 'password_confirmation' => 'beda']);

        // Act
        try {
            $this->runUpdate($input);
            $this->fail('ValidationException diharapkan.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('password', $e->errors());
        }
    }

    /** SF-TC-04: email milik staf lain -> ditolak (unique). */
    public function test_update_rejects_email_used_by_another_user(): void
    {
        // Arrange
        User::factory()->create(['email' => 'lain@hotel.test']);

        // Act
        try {
            $this->runUpdate($this->payload(['email' => 'lain@hotel.test']));
            $this->fail('ValidationException diharapkan.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('email', $e->errors());
        }
    }

    /** SF-TC-05: email sendiri tidak dianggap duplikat. */
    public function test_update_allows_keeping_own_email(): void
    {
        // Act
        $this->runUpdate($this->payload(['name' => 'Nama Lain']));

        // Assert
        $this->assertSame('Nama Lain', $this->staff->fresh()->name);
    }

    /** SF-TC-06: hotel_id tidak ada / department kosong -> ditolak. */
    public function test_update_rejects_unknown_hotel_and_blank_department(): void
    {
        // Act
        try {
            $this->runUpdate($this->payload(['hotel_id' => 999999, 'department' => '']));
            $this->fail('ValidationException diharapkan.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('hotel_id', $e->errors());
            $this->assertArrayHasKey('department', $e->errors());
        }
    }
}
