<?php

namespace services;

use models\User;
use PDO;
use PHPUnit\Framework\TestCase;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/../../public/models/User.php';
require_once __DIR__ . '/../../public/services/UsersService.php';

class UsersServiceTest extends TestCase
{

    private $pdo;
    private $usersService;
    private $stmt;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->stmt = $this->createMock(\PDOStatement::class);
        $this->usersService = new UsersService($this->pdo);
    }

    public function testFindUserByUsername()
    {
        $expectedResult = new User(
            'id',
            'username',
            'password',
            'name',
            'surnames',
            'email',
            'created_at',
            'updated_at',
            false,
            ['role1', 'role2']
        );

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->willReturn([
                'id' => 'id',
                'username' => 'username',
                'password' => 'password',
                'name' => 'name',
                'surnames' => 'surnames',
                'email' => 'email',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'is_deleted' => false
            ]);

        $this->stmt->method('fetchAll')
            ->willReturn(['role1', 'role2']);

        $this->assertEquals($expectedResult, $this->usersService->findUserByUsername('username'));
    }

    public function testSave()
    {
        $user = new User(
            'id',
            'username',
            'password',
            'name',
            'surnames',
            'email',
            'created_at',
            'updated_at',
            false,
            ['role1', 'role2']
        );

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(true);

        $this->assertTrue($this->usersService->save($user));
    }

    public function testAuthenticate()
    {
        $serviceMock = $this->createMock(UsersService::class);

        $serviceMock->expects($this->once())
            ->method('authenticate')
            ->willReturn(
                new User(1, 'User 1', '2021-01-01', 'user', 'surname', 'email', '2021-01-01', '2021-01-01', false)
            );

        $result = $serviceMock->authenticate('User 1', 'password');
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('User 1', $result->username);
    }
}
