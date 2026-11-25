<?php

namespace Tests\Unit\Notes;

use App\Http\Controllers\NoteController;
use App\Models\Note;
use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Unit test NoteController::verifyTask() (admin memverifikasi laporan perbaikan + bagi poin).
 * Test Double: Fake (database SQLite ":memory:") dan Stub (identitas user via actingAs).
 */
class VerifyTaskTest extends TestCase
{
    use RefreshDatabase;

    private function makeNote(array $attributes = []): Note
    {
        $owner = User::factory()->create();

        return Note::create(array_merge([
            'user_id' => $owner->id,
            'category' => 'Engineering',
            'title' => '[ENGINEERING] AC kamar 101 rusak',
            'message' => 'AC tidak dingin',
            'status' => 'open',
        ], $attributes));
    }

    private function actAs(string $role): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user); // Stub identitas

        return $user;
    }

    /** NT-TC-01: bukan admin -> 403, data tidak berubah. */
    public function test_verify_task_aborts_with_403_when_user_is_not_admin(): void
    {
        // Arrange
        $this->actAs('staff');
        $resolver = User::factory()->create();
        $note = $this->makeNote(['status' => 'resolved', 'resolved_by' => $resolver->id]);
        $controller = new NoteController();

        // Act
        try {
            $controller->verifyTask($note);
            $this->fail('HttpException 403 diharapkan, tetapi tidak dilempar.');
        } catch (HttpException $e) {
            // Assert
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertSame('resolved', $note->fresh()->status);
        $this->assertSame(0, PointHistory::count());
    }

    /** NT-TC-02: laporan sudah berstatus verified -> ditolak, poin tidak bertambah. */
    public function test_verify_task_rejects_note_that_is_already_verified(): void
    {
        // Arrange
        $this->actAs('admin');
        $resolver = User::factory()->create();
        $note = $this->makeNote(['status' => 'verified', 'resolved_by' => $resolver->id]);
        $controller = new NoteController();

        // Act
        $response = $controller->verifyTask($note);

        // Assert
        $this->assertSame('Laporan ini sudah diverifikasi sebelumnya.', $response->getSession()->get('error'));
        $this->assertSame(0, PointHistory::count());
    }

    /** NT-TC-03: laporan masih open (belum diselesaikan staf) -> ditolak. */
    public function test_verify_task_rejects_note_that_is_not_resolved_yet(): void
    {
        // Arrange
        $this->actAs('admin');
        $note = $this->makeNote(['status' => 'open']);
        $controller = new NoteController();

        // Act
        $response = $controller->verifyTask($note);

        // Assert
        $this->assertSame('Laporan belum diselesaikan oleh staf.', $response->getSession()->get('error'));
        $this->assertSame('open', $note->fresh()->status);
        $this->assertSame(0, PointHistory::count());
    }

    /** NT-TC-04: status resolved tetapi resolved_by kosong -> ditolak. */
    public function test_verify_task_rejects_resolved_note_without_resolver(): void
    {
        // Arrange
        $this->actAs('admin');
        $note = $this->makeNote(['status' => 'resolved', 'resolved_by' => null]);
        $controller = new NoteController();

        // Act
        $response = $controller->verifyTask($note);

        // Assert
        $this->assertSame('Laporan belum diselesaikan oleh staf.', $response->getSession()->get('error'));
        $this->assertSame('resolved', $note->fresh()->status);
        $this->assertSame(0, PointHistory::count());
    }

    /** NT-TC-05: resolved tanpa helper -> verified, hanya penyelesai yang dapat 15 poin. */
    public function test_verify_task_gives_points_only_to_resolver_when_no_helpers(): void
    {
        // Arrange
        $admin = $this->actAs('admin');
        $resolver = User::factory()->create();
        $note = $this->makeNote(['status' => 'resolved', 'resolved_by' => $resolver->id, 'helpers' => null]);
        $controller = new NoteController();

        // Act
        $response = $controller->verifyTask($note);

        // Assert
        $note->refresh();
        $this->assertSame('verified', $note->status);
        $this->assertSame($admin->id, (int) $note->verified_by);
        $this->assertTrue($note->is_read);
        $this->assertSame(1, PointHistory::count());
        $point = PointHistory::first();
        $this->assertSame($resolver->id, (int) $point->user_id);
        $this->assertSame('WORK_ORDER', $point->type);
        $this->assertSame('Perbaikan: [ENGINEERING] AC kamar 101 rusak', $point->description);
        $this->assertEquals(15, (float) $point->points);
        $this->assertSame(
            'Laporan perbaikan telah diverifikasi dan poin dibagikan!',
            $response->getSession()->get('success')
        );
    }

    /** NT-TC-06: ada helper (dan duplikat id penyelesai) -> poin dibagi tanpa duplikasi. */
    public function test_verify_task_shares_points_with_helpers_without_duplicates(): void
    {
        // Arrange
        $this->actAs('admin');
        $resolver = User::factory()->create();
        $helperOne = User::factory()->create();
        $helperTwo = User::factory()->create();
        $note = $this->makeNote([
            'status' => 'resolved',
            'resolved_by' => $resolver->id,
            'helpers' => json_encode([$helperOne->id, $helperTwo->id, $resolver->id]),
        ]);
        $controller = new NoteController();

        // Act
        $controller->verifyTask($note);

        // Assert
        $receivers = PointHistory::pluck('user_id')->map(fn ($id) => (int) $id)->sort()->values()->all();
        $expected = collect([$resolver->id, $helperOne->id, $helperTwo->id])->sort()->values()->all();
        $this->assertSame($expected, $receivers);
        $this->assertSame(3, PointHistory::count());
    }

    /** NT-TC-07: isi helpers bukan JSON array yang valid -> diabaikan, hanya penyelesai dapat poin. */
    public function test_verify_task_ignores_invalid_helpers_value(): void
    {
        // Arrange
        $this->actAs('admin');
        $resolver = User::factory()->create();
        $note = $this->makeNote([
            'status' => 'resolved',
            'resolved_by' => $resolver->id,
            'helpers' => 'bukan-json',
        ]);
        $controller = new NoteController();

        // Act
        $controller->verifyTask($note);

        // Assert
        $this->assertSame('verified', $note->fresh()->status);
        $this->assertSame(1, PointHistory::count());
        $this->assertSame($resolver->id, (int) PointHistory::first()->user_id);
    }
}
