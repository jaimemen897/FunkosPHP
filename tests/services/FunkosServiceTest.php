<?php

namespace services;

use models\Funko;
use PDO;
use PHPUnit\Framework\TestCase;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/../../public/models/Category.php';
require_once __DIR__ . '/../../public/models/Funko.php';
require_once __DIR__ . '/../../public/services/CategoryService.php';
require_once __DIR__ . '/../../public/services/FunkosService.php';

class FunkosServiceTest extends TestCase
{

    private $pdo;
    private $funkosService;
    private $stmt;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->stmt = $this->createMock(\PDOStatement::class);
        $this->funkosService = new FunkosService($this->pdo);
    }

    public function testFindAllWithCategoryName()
    {
        $expectedResult = [
            new Funko(
                'id1',
                'name1',
                'image1',
                10,
                100,
                'created_at1',
                'updated_at1',
                'category_id1',
                'category_name1'
            ),
            new Funko(
                'id2',
                'name2',
                'image2',
                10,
                100,
                'created_at2',
                'updated_at2',
                'category_id2',
                'category_name2'
            )
        ];

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->willReturnOnConsecutiveCalls(
                [
                    'id' => 'id1',
                    'name' => 'name1',
                    'image' => 'image1',
                    'price' => 10,
                    'stock' => 100,
                    'created_at' => 'created_at1',
                    'updated_at' => 'updated_at1',
                    'category_id' => 'category_id1',
                    'category_name' => 'category_name1'
                ],
                [
                    'id' => 'id2',
                    'name' => 'name2',
                    'image' => 'image2',
                    'price' => 10,
                    'stock' => 100,
                    'created_at' => 'created_at2',
                    'updated_at' => 'updated_at2',
                    'category_id' => 'category_id2',
                    'category_name' => 'category_name2'
                ],
                false
            );

        $this->assertEquals($expectedResult, $this->funkosService->findAllWithCategoryName(''));
    }

    public function testFindById()
    {

        $expectedFunko = new Funko(1, 'Funko 1', 'image.jpg', 100, 10,
            '2021-01-01', '2021-01-01', 1, 'Category 1');

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->willReturn([
                'id' => 1,
                'name' => 'Funko 1',
                'image' => 'image.jpg',
                'price' => 100,
                'stock' => 10,
                'created_at' => '2021-01-01',
                'updated_at' => '2021-01-01',
                'category_id' => 1,
                'category_name' => 'Category 1'
            ]);

        $this->assertEquals($expectedFunko, $this->funkosService->findById(1));
    }

    public function testFindByIdNotFound()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('fetch')
            ->willReturn(false);

        $this->assertNull($this->funkosService->findById(1));
    }

    public function testSave()
    {
        $funko = new Funko(1, 'Funko 1', 'image.jpg', 100, 10,
            '2021-01-01', '2021-01-01', 1, 'Category 1');

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(true);

        $this->assertTrue($this->funkosService->save($funko));
    }

    public function testUpdate()
    {
        $funko = new Funko(1, 'Funko 1', 'image.jpg', 100, 10,
            '2021-01-01', '2021-01-01', 1, 'Category 1');

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(true);

        $this->assertTrue($this->funkosService->update($funko));
    }

    public function testUpdateNotFound()
    {
        $funko = new Funko(1, 'Funko 1', 'image.jpg', 100, 10,
            '2021-01-01', '2021-01-01', 1, 'Category 1');

        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(false);

        $this->assertFalse($this->funkosService->update($funko));
    }

    public function testDeleteById()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(true);

        $this->assertTrue($this->funkosService->deleteById(1));
    }

    public function testDeleteByIdNotFound()
    {
        $this->pdo->method('prepare')
            ->willReturn($this->stmt);

        $this->stmt->method('execute')
            ->willReturn(false);

        $this->assertFalse($this->funkosService->deleteById(1));
    }
}
