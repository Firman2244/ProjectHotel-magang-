<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Unit test User::hasActiveDoubleShiftPermit().
 * Test Double: Stub waktu (Carbon $now tetap / Carbon::setTestNow).
 * Tidak memakai database: User dibuat di memori.
 */
class UserDoubleShiftPermitTest extends TestCase
{
    private Carbon $now;

    protected function setUp(): void
    {
        parent::setUp();
        $this->now = Carbon::create(2026, 11, 25, 10, 0, 0, 'Asia/Jakarta'); // Stub waktu
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeUser(bool $canDouble, ?string $date, ?int $shiftId): User
    {
        $user = new User();
        $user->can_double_shift = $canDouble;
        $user->double_shift_date = $date;
        $user->shift_id = $shiftId;

        return $user;
    }

    /** DS-TC-01: izin berlaku untuk hari ini. */
    public function test_permit_is_active_when_date_is_today(): void
    {
        // Arrange
        $user = $this->makeUser(true, '2026-11-25', 1);

        // Act
        $result = $user->hasActiveDoubleShiftPermit($this->now);

        // Assert
        $this->assertTrue($result);
    }

    /** DS-TC-02: staf shift 3 (lewat tengah malam) masih berlaku dengan izin kemarin. */
    public function test_permit_is_active_for_shift_three_with_yesterday_date(): void
    {
        // Arrange
        $user = $this->makeUser(true, '2026-11-24', 3);

        // Act
        $result = $user->hasActiveDoubleShiftPermit($this->now);

        // Assert
        $this->assertTrue($result);
    }

    /** DS-TC-03: izin kemarin tidak berlaku untuk shift selain 3. */
    public function test_permit_is_inactive_for_non_night_shift_with_yesterday_date(): void
    {
        // Arrange
        $user = $this->makeUser(true, '2026-11-24', 1);

        // Act
        $result = $user->hasActiveDoubleShiftPermit($this->now);

        // Assert
        $this->assertFalse($result);
    }

    /** DS-TC-04: shift 3 tetapi izin sudah dua hari lalu -> tidak berlaku. */
    public function test_permit_is_inactive_for_shift_three_with_expired_date(): void
    {
        // Arrange
        $user = $this->makeUser(true, '2026-11-23', 3);

        // Act
        $result = $user->hasActiveDoubleShiftPermit($this->now);

        // Assert
        $this->assertFalse($result);
    }

    /** DS-TC-05: tanggal valid tetapi flag can_double_shift = false. */
    public function test_permit_is_inactive_when_flag_is_false(): void
    {
        // Arrange
        $user = $this->makeUser(false, '2026-11-25', 1);

        // Act
        $result = $user->hasActiveDoubleShiftPermit($this->now);

        // Assert
        $this->assertFalse($result);
    }

    /** DS-TC-06: tanggal izin kosong (null). */
    public function test_permit_is_inactive_when_date_is_null(): void
    {
        // Arrange
        $user = $this->makeUser(true, null, 3);

        // Act
        $result = $user->hasActiveDoubleShiftPermit($this->now);

        // Assert
        $this->assertFalse($result);
    }

    /** DS-TC-07: parameter $now tidak diisi -> memakai waktu Asia/Jakarta saat ini. */
    public function test_permit_uses_current_jakarta_time_when_now_is_not_given(): void
    {
        // Arrange
        Carbon::setTestNow($this->now); // Stub jam sistem
        $user = $this->makeUser(true, '2026-11-25', 1);

        // Act
        $result = $user->hasActiveDoubleShiftPermit();

        // Assert
        $this->assertTrue($result);
    }
}
