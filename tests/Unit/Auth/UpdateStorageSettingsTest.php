<?php

namespace Tests\Unit\Storage;

use App\Http\Controllers\Admin\StorageController;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Unit test StorageController::updateSettings() (pengaturan auto-delete gambar).
 * Test Double: Fake (SQLite ":memory:").
 */
class UpdateStorageSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function runUpdate(array $input)
    {
        return (new StorageController())->updateSettings(Request::create('/admin/storage/settings', 'POST', $input));
    }

    /** ST-TC-01: belum ada baris setting -> dibuat default lalu diisi nilai baru. */
    public function test_update_settings_creates_setting_row_when_missing(): void
    {
        // Arrange
        $this->assertSame(0, Setting::count());

        // Act
        $response = $this->runUpdate(['auto_delete_days' => 30]);

        // Assert
        $this->assertSame(1, Setting::count());
        $setting = Setting::find(1);
        $this->assertSame(30, (int) $setting->auto_delete_days);
        $this->assertSame('storage_config', $setting->key);
        $this->assertSame('Pengaturan auto-delete berhasil diperbarui!', $response->getSession()->get('success'));
    }

    /** ST-TC-02: baris setting sudah ada -> dipakai ulang (tidak ada baris baru). */
    public function test_update_settings_reuses_existing_row(): void
    {
        // Arrange
        Setting::create(['id' => 1, 'key' => 'storage_config', 'auto_delete_days' => 7]);

        // Act
        $this->runUpdate(['auto_delete_days' => 14]);

        // Assert
        $this->assertSame(1, Setting::count());
        $this->assertSame(14, (int) Setting::find(1)->auto_delete_days);
    }

    /** ST-TC-03: batas bawah 0 (nonaktifkan auto-delete) diterima. */
    public function test_update_settings_accepts_zero(): void
    {
        // Arrange
        Setting::create(['id' => 1, 'key' => 'storage_config', 'auto_delete_days' => 7]);

        // Act
        $this->runUpdate(['auto_delete_days' => 0]);

        // Assert
        $this->assertSame(0, (int) Setting::find(1)->auto_delete_days);
    }

    /** ST-TC-04: nilai negatif ditolak, nilai lama tetap. */
    public function test_update_settings_rejects_negative_value(): void
    {
        // Arrange
        Setting::create(['id' => 1, 'key' => 'storage_config', 'auto_delete_days' => 7]);

        // Act
        try {
            $this->runUpdate(['auto_delete_days' => -1]);
            $this->fail('ValidationException diharapkan.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('auto_delete_days', $e->errors());
        }
        $this->assertSame(7, (int) Setting::find(1)->auto_delete_days);
    }

    /** ST-TC-05: bukan angka bulat / kosong ditolak. */
    public function test_update_settings_rejects_non_integer_and_missing_value(): void
    {
        foreach ([['auto_delete_days' => 'abc'], ['auto_delete_days' => 1.5], []] as $input) {
            // Act
            try {
                $this->runUpdate($input);
                $this->fail('ValidationException diharapkan.');
            } catch (ValidationException $e) {
                // Assert
                $this->assertArrayHasKey('auto_delete_days', $e->errors());
            }
        }
    }
}
