<?php

namespace Tests\Unit\Shifts;

use App\Http\Controllers\Admin\ShiftController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Unit test ShiftController::updateShift() (admin memindahkan staf ke shift lain).
 * Test Double: Fake (database SQLite ":memory:") dan Spy (query log untuk mencatat query UPDATE).
 */
class UpdateShiftTest extends TestCase
{
    use RefreshDatabase;

    private int $shiftPagi;
    private int $shiftSiang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shiftPagi = DB::table('shifts')->insertGetId([
            'name' => 'Pagi', 'start_time' => '07:00', 'end_time' => '15:00', 'deadline_time' => '14:00',
        ]);
        $this->shiftSiang = DB::table('shifts')->insertGetId([
            'name' => 'Siang', 'start_time' => '15:00', 'end_time' => '23:00', 'deadline_time' => '22:00',
        ]);
    }

    private function makeRequest(array $input): Request
    {
        return Request::create('/admin/shifts/update', 'POST', $input);
    }

    /** Spy: jumlah query UPDATE yang dijalankan sejak pencatatan dimulai. */
    private function countUpdateQueries(): int
    {
        return collect(DB::getQueryLog())
            ->filter(fn (array $log) => str_starts_with(strtolower(ltrim($log['query'])), 'update'))
            ->count();
    }

    /** SH-TC-01: staf dipindah ke shift berbeda -> shift_id berubah. */
    public function test_update_shift_changes_shift_when_different(): void
    {
        // Arrange
        $staff = User::factory()->create(['shift_id' => $this->shiftPagi]);
        $request = $this->makeRequest(['user_id' => $staff->id, 'shift_id' => $this->shiftSiang]);
        $controller = new ShiftController();

        // Act
        $response = $controller->updateShift($request);

        // Assert
        $this->assertSame($this->shiftSiang, (int) $staff->fresh()->shift_id);
        $this->assertTrue($response->getData(true)['success']);
    }

    /** SH-TC-02: shift yang dipilih sama dengan shift sekarang -> tidak ada query UPDATE. */
    public function test_update_shift_does_not_update_when_shift_is_unchanged(): void
    {
        // Arrange
        $staff = User::factory()->create(['shift_id' => $this->shiftPagi]);
        $request = $this->makeRequest(['user_id' => $staff->id, 'shift_id' => $this->shiftPagi]);
        $controller = new ShiftController();
        DB::flushQueryLog();
        DB::enableQueryLog(); // Spy

        // Act
        $response = $controller->updateShift($request);

        // Assert
        $this->assertSame(0, $this->countUpdateQueries());
        $this->assertSame($this->shiftPagi, (int) $staff->fresh()->shift_id);
        $this->assertTrue($response->getData(true)['success']);
    }

    /** SH-TC-03: shift_id kosong (staf dilepas dari shift) -> shift_id menjadi null. */
    public function test_update_shift_unassigns_staff_when_shift_id_is_null(): void
    {
        // Arrange
        $staff = User::factory()->create(['shift_id' => $this->shiftPagi]);
        $request = $this->makeRequest(['user_id' => $staff->id, 'shift_id' => null]);
        $controller = new ShiftController();

        // Act
        $response = $controller->updateShift($request);

        // Assert
        $this->assertNull($staff->fresh()->shift_id);
        $this->assertTrue($response->getData(true)['success']);
    }

    /** SH-TC-04: user_id tidak ada di database -> ValidationException. */
    public function test_update_shift_rejects_unknown_user(): void
    {
        // Arrange
        $request = $this->makeRequest(['user_id' => 999999, 'shift_id' => $this->shiftSiang]);
        $controller = new ShiftController();

        // Act
        try {
            $controller->updateShift($request);
            $this->fail('ValidationException diharapkan, tetapi tidak dilempar.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('user_id', $e->errors());
        }
    }

    /** SH-TC-05: shift_id tidak ada di database -> ValidationException, shift lama tetap. */
    public function test_update_shift_rejects_unknown_shift_and_keeps_old_shift(): void
    {
        // Arrange
        $staff = User::factory()->create(['shift_id' => $this->shiftPagi]);
        $request = $this->makeRequest(['user_id' => $staff->id, 'shift_id' => 999999]);
        $controller = new ShiftController();

        // Act
        try {
            $controller->updateShift($request);
            $this->fail('ValidationException diharapkan, tetapi tidak dilempar.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('shift_id', $e->errors());
        }
        $this->assertSame($this->shiftPagi, (int) $staff->fresh()->shift_id);
    }
}
