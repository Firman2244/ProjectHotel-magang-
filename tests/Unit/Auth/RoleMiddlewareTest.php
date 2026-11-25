<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Unit test RoleMiddleware::handle() (hak akses berdasarkan role).
 * Test Double: Dummy (Request), Stub + Mock (facade Auth), Spy ($next).
 */
class RoleMiddlewareTest extends TestCase
{
    private function makeUser(string $role): User
    {
        $user = new User();
        $user->role = $role;

        return $user;
    }

    /** Spy: mencatat setiap pemanggilan $next beserta request-nya. */
    private function makeNextSpy(array &$calls): \Closure
    {
        return function (Request $request) use (&$calls) {
            $calls[] = $request;

            return new Response('lolos');
        };
    }

    /** RM-TC-01: sudah login dan role sesuai -> request diteruskan. */
    public function test_handle_passes_request_when_user_has_required_role(): void
    {
        // Arrange
        $dummyRequest = Request::create('/admin/dashboard'); // Dummy
        $calls = [];
        Auth::shouldReceive('check')->once()->andReturn(true); // Stub
        Auth::shouldReceive('user')->once()->andReturn($this->makeUser('admin'));
        $middleware = new RoleMiddleware();

        // Act
        $response = $middleware->handle($dummyRequest, $this->makeNextSpy($calls), 'admin');

        // Assert
        $this->assertCount(1, $calls);
        $this->assertSame($dummyRequest, $calls[0]);
        $this->assertSame('lolos', $response->getContent());
    }

    /** RM-TC-02: belum login -> 403, role tidak pernah dicek (short-circuit). */
    public function test_handle_aborts_with_403_when_user_is_not_logged_in(): void
    {
        // Arrange
        $dummyRequest = Request::create('/admin/dashboard'); // Dummy
        $calls = [];
        Auth::shouldReceive('check')->once()->andReturn(false);
        Auth::shouldReceive('user')->never(); // Mock: kondisi kedua tidak boleh dievaluasi
        $middleware = new RoleMiddleware();

        // Act
        try {
            $middleware->handle($dummyRequest, $this->makeNextSpy($calls), 'admin');
            $this->fail('HttpException 403 diharapkan, tetapi tidak dilempar.');
        } catch (HttpException $e) {
            // Assert
            $this->assertSame(403, $e->getStatusCode());
            $this->assertSame('Akses Ditolak: Anda bukan Admin.', $e->getMessage());
        }
        $this->assertCount(0, $calls);
    }

    /** RM-TC-03: sudah login tetapi role berbeda -> 403. */
    public function test_handle_aborts_with_403_when_role_does_not_match(): void
    {
        // Arrange
        $dummyRequest = Request::create('/admin/dashboard'); // Dummy
        $calls = [];
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($this->makeUser('staff'));
        $middleware = new RoleMiddleware();

        // Act
        try {
            $middleware->handle($dummyRequest, $this->makeNextSpy($calls), 'admin');
            $this->fail('HttpException 403 diharapkan, tetapi tidak dilempar.');
        } catch (HttpException $e) {
            // Assert
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertCount(0, $calls);
    }
}
