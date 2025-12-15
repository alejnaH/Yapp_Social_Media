<?php

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('PHPUNIT_RUNNING')) {
            define('PHPUNIT_RUNNING', true);
        }

        require_once __DIR__ . '/../vendor/autoload.php';

        $fakeUser = (object)[
            "UserID"   => 1,              
            "Username" => "testuser",
            "Email"    => "test@example.com",
            "Role"     => "admin"
        ];

        Flight::map('auth_middleware', function () use ($fakeUser) {
            return new class($fakeUser)
            {
                private $fakeUser;

                public function __construct($fakeUser)
                {
                    $this->fakeUser = $fakeUser;
                }

                public function verifyToken($token = null)
                {
                    // Pretend token is valid, inject fake user
                    Flight::set('user', $this->fakeUser);
                    Flight::set('jwt_token', 'fake-token');
                    return true;
                }

                public function authorizeRole($requiredRole)
                {
                    // Always allow
                    return true;
                }

                public function authorizeRoles($roles)
                {
                    // Always allow
                    return true;
                }

                public function authorizePermission($permission)
                {
                    // Always allow
                    return true;
                }
            };
        });

       
        $_SERVER['HTTP_AUTHENTICATION'] = "Bearer faketoken";

        require_once __DIR__ . '/../index.php';

    }

    private function runRoute(string $method, string $uri, ?array $body = null): string
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI']    = $uri;

        http_response_code(200);

        if ($body !== null) {
            Flight::request()->body = json_encode($body);
            $_SERVER['CONTENT_TYPE'] = 'application/json';
        }

        ob_start();
        Flight::start();
        $output = ob_get_clean();

        return $output;
    }

    public function testGetAllUsers()
    {
        $output = $this->runRoute('GET', '/users');

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
    }

    public function testGetUserById()
    {
        $output = $this->runRoute('GET', '/users/1');

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
    }

    public function testGetUserByEmail()
    {
        $output = $this->runRoute('GET', '/users/email/test@example.com');

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
    }

    public function testGetUserByUsername()
    {
        $output = $this->runRoute('GET', '/users/username/testuser');

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
    }

    public function testCreateUser()
    {
        $output = $this->runRoute('POST', '/users', [
            "Username" => "phpunit_user",
            "Email"    => "phpunit_user@gmail.com",
            "Password" => "12345",
            "FullName" => "PHPUnit Test User",
            "Role"     => "user"
        ]);

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
    }

    public function testUpdateUser()
    {
        $output = $this->runRoute('PUT', '/users/1', [
            "Username" => "updated_phpunit_user",
            "Email"    => "updated_phpunit_user@gmail.com",
            "FullName" => "Updated PHPUnit User",
            "Role"     => "admin"
        ]);

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
    }

    public function testDeleteUser()
    {
        $output = $this->runRoute('DELETE', '/users/1');

        $this->assertEquals(200, http_response_code());
        $this->assertJson($output);
    }
}
