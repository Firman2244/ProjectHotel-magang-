<?php

namespace Tests\Unit\Shifts;

use App\Http\Controllers\Admin\ShiftController;
use App\Models\ActivityLog;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Unit test ShiftController::updateConfig() (admin mengubah jam operasional shift).
 * Test Double: Fake (SQLite ":memory:") dan Stub (actingAs).
 */
class UpdateConfigTest extends TestCase
{
    use RefreshDatabase;

    private int $pagi;
    private int $siang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin'])); // Stub identitas
        $this->pagi = DB::table('shifts')->insertGetId(['name' => 'Pagi', 'start_time' => '07:00', 'end_time' => '15:00', 'deadline_time' => '14:00']);
        $this->siang = DB::table('shifts')->insertGetId(['name' => 'Siang', 'start_time' => '15:00', 'end_time' => '23:00', 'deadline_time' => '22:00']);
    }

    private function row(int $id, string $s, string $e, string $d): array
    {
        return ['id' => $id, 'start_time' => $s, 'end_time' => $e, 'deadline_time' => $d];
    }

    private function runUpdate(array $shifts)
    {
        $request = Request::create('/admin/shifts/config', 'POST', ['shifts' => $shifts]);

        return (new ShiftController())->updateConfig($request);
    }

    /** SC-TC-01: dua shift valid -> semua jam tersimpan, pesan sukses, log tercatat. */
    public function test_update_config_saves_all_shifts_and_records_log(): void
    {
        // Arrange
        $input = [$this->row($this->pagi, '06:00', '14:00', '13:00'), $this->row($this->siang, '14:00', '22:00', '21:00')];

        // Act
        $response = $this->runUpdate($input);

        // Assert
        $this->assertSame('06:00', substr(Shift::find($this->pagi)->start_time, 0, 5));
        $this->assertSame('21:00', substr(Shift::find($this->siang)->deadline_time, 0, 5));
        $this->assertSame('Konfigurasi jam operasional berhasil diperbarui!', $response->getSession()->get('success'));
        $this->assertSame(1, ActivityLog::where('action', 'UPDATE_SHIFT_CONFIG')->count());
    }

    /** SC-TC-02: hanya satu shift dikirim -> shift lain tidak berubah. */
    public function test_update_config_leaves_other_shifts_untouched(): void
    {
        // Arrange
        $input = [$this->row($this->pagi, '06:30', '14:30', '13:30')];

        // Act
        $this->runUpdate($input);

        // Assert
        $this->assertSame('06:30', substr(Shift::find($this->pagi)->start_time, 0, 5));
        $this->assertSame('15:00', substr(Shift::find($this->siang)->start_time, 0, 5));
    }

    /** SC-TC-03: shifts tidak dikirim -> ValidationException. */
    public function test_update_config_rejects_missing_shifts(): void
    {
        // Arrange & Act
        try {
            Request::create('/x', 'POST', []);
            (new ShiftController())->updateConfig(Request::create('/x', 'POST', []));
            $this->fail('ValidationException diharapkan.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('shifts', $e->errors());
        }
    }

    /** SC-TC-04: format jam salah (bukan H:i) -> ValidationException, data lama tetap. */
    public function test_update_config_rejects_invalid_time_format(): void
    {
        // Arrange
        $input = [$this->row($this->pagi, '25:99', '14:00', '13:00')];

        // Act
        try {
            $this->runUpdate($input);
            $this->fail('ValidationException diharapkan.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('shifts.0.start_time', $e->errors());
        }
        $this->assertSame('07:00', substr(Shift::find($this->pagi)->start_time, 0, 5));
        $this->assertSame(0, ActivityLog::count());
    }

    /** SC-TC-05: id shift tidak ada -> ValidationException. */
    public function test_update_config_rejects_unknown_shift_id(): void
    {
        // Arrange
        $input = [$this->row(999999, '06:00', '14:00', '13:00')];

        // Act
        try {
            $this->runUpdate($input);
            $this->fail('ValidationException diharapkan.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('shifts.0.id', $e->errors());
        }
    }
}
