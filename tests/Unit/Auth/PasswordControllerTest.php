<?php

namespace Tests\Unit\Auth;

use App\Http\Controllers\Auth\PasswordController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

/**
 * Unit test PasswordController::update() (ganti password dari halaman profil).
 * Request dan User diganti Test Double sehingga tidak butuh HTTP/DB sungguhan.
 */
class PasswordControllerTest extends TestCase
{
    private const NEW_PASSWORD = 'PasswordBaru123';

    /** PC-TC-01 (Stub + Spy): validasi lolos -> password disimpan sebagai hash. */
    public function test_update_saves_hashed_password_and_redirects_back_with_status(): void
    {
        // Arrange
        $user = Mockery::spy(User::class); // Spy
        $request = Mockery::mock(Request::class); // Stub (tanpa verifikasi pemanggilan)
        $request->shouldReceive('validateWithBag')->andReturn([
            'current_password' => 'password-lama',
            'password' => self::NEW_PASSWORD,
        ]);
        $request->shouldReceive('user')->andReturn($user);
        $controller = new PasswordController();

        // Act
        $response = $controller->update($request);

        // Assert
        $user->shouldHaveReceived('update')
            ->once()
            ->with(Mockery::on(fn (array $attributes): bool =>
                $attributes['password'] !== self::NEW_PASSWORD // bukan plaintext
                && Hash::check(self::NEW_PASSWORD, $attributes['password']) // hash yang valid
            ));
        $this->assertSame('password-updated', $response->getSession()->get('status'));
    }

    /** PC-TC-02 (Mock): validasi gagal -> user tidak disentuh. */
    public function test_update_does_not_touch_user_when_validation_fails(): void
    {
        // Arrange
        $user = Mockery::mock(User::class); // Mock
        $user->shouldNotReceive('update');
        $request = Mockery::mock(Request::class); // Mock + Stub (melempar exception)
        $request->shouldReceive('validateWithBag')
            ->once()
            ->andThrow(ValidationException::withMessages([
                'current_password' => 'The password is incorrect.',
            ]));
        $request->shouldReceive('user')->never();
        $controller = new PasswordController();

        $this->expectException(ValidationException::class);

        // Act
        $controller->update($request);

        // Assert -> diverifikasi oleh expectException() dan ekspektasi Mockery di atas
    }

    /** Mock: aturan validasi dan error bag yang dikirim controller benar. */
    public function test_update_validates_with_expected_rules_and_error_bag(): void
    {
        // Arrange
        $user = Mockery::spy(User::class);
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validateWithBag')
            ->once()
            ->with('updatePassword', Mockery::on(function (array $rules): bool {
                $passwordRules = $rules['password'] ?? [];

                return ($rules['current_password'] ?? null) === ['required', 'current_password']
                    && in_array('required', $passwordRules, true)
                    && in_array('confirmed', $passwordRules, true)
                    && count(array_filter($passwordRules, fn ($r) => $r instanceof Password)) === 1;
            }))
            ->andReturn(['current_password' => 'x', 'password' => self::NEW_PASSWORD]);
        $request->shouldReceive('user')->andReturn($user);
        $controller = new PasswordController();

        // Act
        $response = $controller->update($request);

        // Assert
        $this->assertSame('password-updated', $response->getSession()->get('status'));
    }
}
