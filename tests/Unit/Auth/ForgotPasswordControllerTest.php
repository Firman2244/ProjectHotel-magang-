<?php

namespace Tests\Unit\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Unit test ForgotPasswordController::update() + resolveUser().
 * Fake: session in-memory (Store + ArraySessionHandler) dan database SQLite ":memory:".
 */
class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    private const SESSION_KEY = 'forgot_password_user_id';
    private const NEW_PASSWORD = 'PasswordBaru123';

    private function makeRequest(array $input, ?int $userId = null): Request
    {
        $request = Request::create('/forgot-password/account', 'PUT', $input);
        $session = new Store('testing', new ArraySessionHandler(10)); // Fake session
        if ($userId !== null) {
            $session->put(self::SESSION_KEY, $userId);
        }
        $request->setLaravelSession($session);

        return $request;
    }

    /** FP-TC-01: session valid + input valid -> password berubah, session dihapus. */
    public function test_update_changes_password_and_clears_session_when_input_is_valid(): void
    {
        // Arrange
        $user = User::factory()->create();
        $request = $this->makeRequest([
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
        ], $user->id);
        $controller = new ForgotPasswordController();

        // Act
        $response = $controller->update($request);

        // Assert
        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $user->fresh()->password));
        $this->assertFalse($request->session()->has(self::SESSION_KEY));
        $this->assertSame(route('login', ['email' => $user->email]), $response->getTargetUrl());
        $this->assertSame(
            'Password berhasil diubah. Silakan login dengan password baru.',
            $response->getSession()->get('status')
        );
    }

    /** FP-TC-02: session kosong -> kembali ke langkah input email. */
    public function test_update_redirects_to_email_step_when_session_is_empty(): void
    {
        // Arrange
        $user = User::factory()->create();
        $oldHash = $user->password;
        $request = $this->makeRequest([
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
        ]);
        $controller = new ForgotPasswordController();

        // Act
        $response = $controller->update($request);

        // Assert
        $this->assertSame(route('password.request'), $response->getTargetUrl());
        $this->assertSame($oldHash, $user->fresh()->password);
    }

    /** FP-TC-03: id di session tidak ada di database. */
    public function test_update_redirects_to_email_step_when_session_user_does_not_exist(): void
    {
        // Arrange
        $other = User::factory()->create();
        $oldHash = $other->password;
        $request = $this->makeRequest([
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => self::NEW_PASSWORD,
        ], 999999);
        $controller = new ForgotPasswordController();

        // Act
        $response = $controller->update($request);

        // Assert
        $this->assertSame(route('password.request'), $response->getTargetUrl());
        $this->assertSame($oldHash, $other->fresh()->password);
    }

    /** FP-TC-04: konfirmasi tidak cocok. */
    public function test_update_rejects_mismatched_confirmation_and_keeps_old_password(): void
    {
        // Arrange
        $user = User::factory()->create();
        $oldHash = $user->password;
        $request = $this->makeRequest([
            'password' => self::NEW_PASSWORD,
            'password_confirmation' => 'PasswordLain123',
        ], $user->id);
        $controller = new ForgotPasswordController();

        // Act
        $errors = $this->captureValidationErrors($controller, $request);

        // Assert
        $this->assertSame(
            'Konfirmasi password tidak cocok dengan password baru.',
            $errors['password'][0]
        );
        $this->assertSame($oldHash, $user->fresh()->password);
        $this->assertTrue($request->session()->has(self::SESSION_KEY));
    }

    /** FP-TC-05: password kurang dari 8 karakter. */
    public function test_update_rejects_password_shorter_than_eight_characters(): void
    {
        // Arrange
        $user = User::factory()->create();
        $oldHash = $user->password;
        $request = $this->makeRequest([
            'password' => 'abc123',
            'password_confirmation' => 'abc123',
        ], $user->id);
        $controller = new ForgotPasswordController();

        // Act
        $errors = $this->captureValidationErrors($controller, $request);

        // Assert
        $this->assertArrayHasKey('password', $errors);
        $this->assertSame($oldHash, $user->fresh()->password);
        $this->assertTrue($request->session()->has(self::SESSION_KEY));
    }

    /** FP-TC-06: password kosong. */
    public function test_update_rejects_empty_password(): void
    {
        // Arrange
        $user = User::factory()->create();
        $oldHash = $user->password;
        $request = $this->makeRequest([
            'password' => '',
            'password_confirmation' => '',
        ], $user->id);
        $controller = new ForgotPasswordController();

        // Act
        $errors = $this->captureValidationErrors($controller, $request);

        // Assert
        $this->assertSame('Password baru wajib diisi.', $errors['password'][0]);
        $this->assertSame($oldHash, $user->fresh()->password);
    }

    /** Menjalankan update() dan mengembalikan error validasi (gagal jika tidak ada exception). */
    private function captureValidationErrors(ForgotPasswordController $controller, Request $request): array
    {
        try {
            $controller->update($request);
        } catch (ValidationException $e) {
            return $e->errors();
        }

        $this->fail('ValidationException diharapkan dilempar, tetapi tidak terjadi.');
    }
}
