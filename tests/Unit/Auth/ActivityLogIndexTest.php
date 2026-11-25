<?php

namespace Tests\Unit\Logs;

use App\Http\Controllers\ActivityLogController;
use App\Models\ActivityLog;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Unit test ActivityLogController::index() (filter hotel, tanggal, aksi) dan clear().
 * Test Double: Fake (SQLite ":memory:") dan Stub (actingAs).
 */
class ActivityLogIndexTest extends TestCase
{
    use RefreshDatabase;

    private Hotel $hotelA;
    private Hotel $hotelB;
    private User $userA;
    private User $userB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hotelA = Hotel::create(['name' => 'Hotel A']);
        $this->hotelB = Hotel::create(['name' => 'Hotel B']);
        $this->userA = User::factory()->create(['hotel_id' => $this->hotelA->id]);
        $this->userB = User::factory()->create(['hotel_id' => $this->hotelB->id]);
    }

    private function log(User $u, string $action, string $date): ActivityLog
    {
        $log = ActivityLog::create(['user_id' => $u->id, 'action' => $action, 'description' => 'x']);
        $log->created_at = $date . ' 12:00:00';
        $log->save();

        return $log;
    }

    private function logsFor(array $query): array
    {
        $request = Request::create('/admin/activity-logs', 'GET', $query);
        $view = (new ActivityLogController())->index($request);

        return $view->getData()['logs']->items();
    }

    /** AL-TC-01: tanpa parameter hotel -> memakai hotel pertama. */
    public function test_index_defaults_to_first_hotel(): void
    {
        // Arrange
        $this->log($this->userA, 'LOGIN', '2026-11-20');
        $this->log($this->userB, 'LOGIN', '2026-11-20');

        // Act
        $items = $this->logsFor([]);

        // Assert
        $this->assertCount(1, $items);
        $this->assertSame($this->userA->id, $items[0]->user_id);
    }

    /** AL-TC-02: parameter hotel dipilih -> hanya log milik user di hotel itu. */
    public function test_index_filters_by_requested_hotel(): void
    {
        // Arrange
        $this->log($this->userA, 'LOGIN', '2026-11-20');
        $this->log($this->userB, 'LOGIN', '2026-11-20');

        // Act
        $items = $this->logsFor(['hotel' => $this->hotelB->id]);

        // Assert
        $this->assertCount(1, $items);
        $this->assertSame($this->userB->id, $items[0]->user_id);
    }

    /** AL-TC-03: tidak ada hotel sama sekali -> tidak difilter hotel (semua log tampil). */
    public function test_index_shows_all_logs_when_no_hotel_exists(): void
    {
        // Arrange
        $this->log($this->userA, 'LOGIN', '2026-11-20');
        $this->log($this->userB, 'LOGIN', '2026-11-21');
        Hotel::query()->delete();

        // Act
        $items = $this->logsFor([]);

        // Assert
        $this->assertCount(2, $items);
    }

    /** AL-TC-04: filter tanggal. */
    public function test_index_filters_by_date(): void
    {
        // Arrange
        $this->log($this->userA, 'LOGIN', '2026-11-20');
        $this->log($this->userA, 'LOGIN', '2026-11-21');

        // Act
        $items = $this->logsFor(['date' => '2026-11-21']);

        // Assert
        $this->assertCount(1, $items);
        $this->assertSame('2026-11-21', $items[0]->created_at->toDateString());
    }

    /** AL-TC-05: filter aksi. */
    public function test_index_filters_by_action(): void
    {
        // Arrange
        $this->log($this->userA, 'LOGIN', '2026-11-20');
        $this->log($this->userA, 'GRANT_DOUBLE_SHIFT', '2026-11-20');

        // Act
        $items = $this->logsFor(['action' => 'GRANT_DOUBLE_SHIFT']);

        // Assert
        $this->assertCount(1, $items);
        $this->assertSame('GRANT_DOUBLE_SHIFT', $items[0]->action);
    }

    /** AL-TC-06: urutan terbaru lebih dulu. */
    public function test_index_orders_newest_first(): void
    {
        // Arrange
        $this->log($this->userA, 'OLD', '2026-11-01');
        $this->log($this->userA, 'NEW', '2026-11-25');

        // Act
        $items = $this->logsFor([]);

        // Assert
        $this->assertSame(['NEW', 'OLD'], array_map(fn ($l) => $l->action, $items));
    }

    /** AL-TC-07: clear() mengosongkan log lalu mencatat satu log CLEAR_LOGS. */
    public function test_clear_truncates_logs_and_records_clear_entry(): void
    {
        // Arrange
        $this->actingAs($this->userA);
        $this->log($this->userA, 'LOGIN', '2026-11-20');
        $this->log($this->userB, 'LOGIN', '2026-11-20');

        // Act
        $response = (new ActivityLogController())->clear();

        // Assert
        $this->assertSame(1, ActivityLog::count());
        $this->assertSame('CLEAR_LOGS', ActivityLog::first()->action);
        $this->assertSame('Seluruh riwayat Activity Log berhasil dibersihkan.', $response->getSession()->get('success'));
    }
}
