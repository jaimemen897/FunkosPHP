<?php

namespace services;

use models\Category;
use PDO;
use PHPUnit\Framework\TestCase;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/../../public/models/Category.php';
require_once __DIR__ . '/../../public/services/CategoryService.php';


class CategoryServiceTest extends TestCase
{
    private $pdo;
    private $categoryService;
    private $stmt;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->stmt = $this->createMock(\PDOStatement::class);
        $this->categoryService = new CategoryService($this->pdo);
    }

    public function testFindAll()
    {
        $expectedResult = [
            new Category(
                'id1',
                'name1',
                'created_at1',
                'updated_at1',
                false
            ),
            new Category(
                'id2',
                'name2',
                'created_at2',
                'updated_at2',
                false
            )
        ];

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->will($this->onConsecutiveCalls(
                [
                    'id' => 'id1',
                    'name' => 'name1',
                    'created_at' => 'created_at1',
                    'updated_at' => 'updated_at1',
                    'is_deleted' => false
                ],
                [
                    'id' => 'id2',
                    'name' => 'name2',
                    'created_at' => 'created_at2',
                    'updated_at' => 'updated_at2',
                    'is_deleted' => false
                ],
                false
            ));

        $this->assertEquals($expectedResult, $this->categoryService->findAll());
    }

    public function testFindAllActive()
    {
        $expectedResult = [
            new Category(
                'id1',
                'name1',
                'created_at1',
                'updated_at1',
                false
            ),
            new Category(
                'id2',
                'name2',
                'created_at2',
                'updated_at2',
                false
            )
        ];

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->will($this->onConsecutiveCalls(
                [
                    'id' => 'id1',
                    'name' => 'name1',
                    'created_at' => 'created_at1',
                    'updated_at' => 'updated_at1',
                    'is_deleted' => false
                ],
                [
                    'id' => 'id2',
                    'name' => 'name2',
                    'created_at' => 'created_at2',
                    'updated_at' => 'updated_at2',
                    'is_deleted' => false
                ],
                false
            ));

        $this->assertEquals($expectedResult, $this->categoryService->findAllActive());
    }

    public function testFindByName()
    {
        $expectedResult = new Category(
            'id1',
            'name1',
            'created_at1',
            'updated_at1',
            false
        );

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->willReturn(
                [
                    'id' => 'id1',
                    'name' => 'name1',
                    'created_at' => 'created_at1',
                    'updated_at' => 'updated_at1',
                    'is_deleted' => false
                ]
            );

        $this->assertEquals($expectedResult, $this->categoryService->findByName('name1'));
    }

    public function testFindByNameNotFound()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->willReturn(false);

        $this->assertFalse($this->categoryService->findByName('name1'));
    }

    public function testFindById()
    {
        $expectedResult = new Category(
            'id1',
            'name1',
            'created_at1',
            'updated_at1',
            false
        );

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->willReturn(
                [
                    'id' => 'id1',
                    'name' => 'name1',
                    'created_at' => 'created_at1',
                    'updated_at' => 'updated_at1',
                    'is_deleted' => false
                ]
            );

        $this->assertEquals($expectedResult, $this->categoryService->findById('id1'));
    }

    public function testFindByIdNotFound()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->willReturn(false);

        $this->assertFalse($this->categoryService->findById('id1'));
    }

    public function testSave()
    {
        $category = new Category(
            'id',
            'name',
            'created_at',
            'updated_at',
            false
        );

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(true);

        $this->assertTrue($this->categoryService->save($category));
    }

    public function testUpdate()
    {
        $category = new Category(
            'id',
            'name',
            'created_at',
            'updated_at',
            false
        );

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(true);

        $this->assertTrue($this->categoryService->update($category));
    }

    public function testUpdateNotFound()
    {
        $category = new Category(
            'id',
            'name',
            'created_at',
            'updated_at',
            false
        );

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(false);

        $this->assertFalse($this->categoryService->update($category));
    }

    public function testDeleteById()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(true);

        $this->assertTrue($this->categoryService->deleteById('id'));
    }

    public function testDeleteByIdNotFound()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(false);

        $this->assertFalse($this->categoryService->deleteById('id'));
    }
}
