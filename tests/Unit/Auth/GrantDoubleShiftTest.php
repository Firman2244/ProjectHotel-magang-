<?php

namespace Tests\Unit\Shifts;

use App\Http\Controllers\Admin\ShiftController;
use App\Models\ActivityLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Unit test ShiftController::grantDoubleShift() (admin memberi izin double shift).
 * Test Double: Fake (SQLite ":memory:"), Stub (actingAs + waktu tetap Carbon::setTestNow).
 */
class GrantDoubleShiftTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::create(2026, 11, 25, 10, 0, 0, 'Asia/Jakarta')); // Stub waktu
        $this->actingAs(User::factory()->create(['role' => 'admin'])); // Stub identitas
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeRequest(array $input): Request
    {
        return Request::create('/admin/shifts/double-shift', 'POST', $input);
    }

    /** GD-TC-01: user_ids valid -> flag + tanggal hari ini diset, JSON success. */
    public function test_grant_double_shift_sets_flag_and_today_for_selected_users(): void
    {
        // Arrange
        $a = User::factory()->create();
        $b = User::factory()->create();
        $request = $this->makeRequest(['user_ids' => [$a->id, $b->id]]);

        // Act
        $response = (new ShiftController())->grantDoubleShift($request);

        // Assert
        foreach ([$a, $b] as $user) {
            $fresh = $user->fresh();
            $this->assertTrue((bool) $fresh->can_double_shift);
            $this->assertSame('2026-11-25', Carbon::parse($fresh->double_shift_date)->toDateString());
        }
        $this->assertTrue($response->getData(true)['success']);
    }

    /** GD-TC-02: staf yang tidak dipilih tidak ikut berubah. */
    public function test_grant_double_shift_does_not_touch_unselected_users(): void
    {
        // Arrange
        $selected = User::factory()->create();
        $other = User::factory()->create();
        $request = $this->makeRequest(['user_ids' => [$selected->id]]);

        // Act
        (new ShiftController())->grantDoubleShift($request);

        // Assert
        $this->assertFalse((bool) $other->fresh()->can_double_shift);
        $this->assertNull($other->fresh()->double_shift_date);
    }

    /** GD-TC-03: activity log tercatat dengan jumlah staf yang benar. */
    public function test_grant_double_shift_records_activity_log_with_count(): void
    {
        // Arrange
        $users = User::factory()->count(3)->create();
        $request = $this->makeRequest(['user_ids' => $users->pluck('id')->all()]);

        // Act
        (new ShiftController())->grantDoubleShift($request);

        // Assert
        $log = ActivityLog::where('action', 'GRANT_DOUBLE_SHIFT')->first();
        $this->assertNotNull($log);
        $this->assertSame('Admin memberikan otorisasi Double Shift kepada 3 staf.', $log->description);
    }

    /** GD-TC-04: user_ids tidak dikirim -> ValidationException. */
    public function test_grant_double_shift_rejects_missing_user_ids(): void
    {
        // Arrange
        $request = $this->makeRequest([]);

        // Act
        try {
            (new ShiftController())->grantDoubleShift($request);
            $this->fail('ValidationException diharapkan.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('user_ids', $e->errors());
        }
        $this->assertSame(0, ActivityLog::count());
    }

    /** GD-TC-05: salah satu id tidak ada -> ValidationException, tidak ada yang diubah. */
    public function test_grant_double_shift_rejects_unknown_user_id(): void
    {
        // Arrange
        $real = User::factory()->create();
        $request = $this->makeRequest(['user_ids' => [$real->id, 999999]]);

        // Act
        try {
            (new ShiftController())->grantDoubleShift($request);
            $this->fail('ValidationException diharapkan.');
        } catch (ValidationException $e) {
            // Assert
            $this->assertArrayHasKey('user_ids.1', $e->errors());
        }
        $this->assertFalse((bool) $real->fresh()->can_double_shift);
    }
}
