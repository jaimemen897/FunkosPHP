<?php

namespace services;

use models\Category;
use PDO;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . '/../models/Category.php';

class CategoryService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories ORDER BY id");
        $stmt->execute();

        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $category = new Category(
                $row['id'],
                $row['name'],
                $row['created_at'],
                $row['updated_at'],
                $row['is_deleted']
            );
            $categories[] = $category;
        }
        return $categories;
    }

    public function findAllActive()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories where categories.is_deleted = false ORDER BY id");
        $stmt->execute();

        $categories = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $category = new Category(
                $row['id'],
                $row['name'],
                $row['created_at'],
                $row['updated_at'],
                $row['is_deleted']
            );
            $categories[] = $category;
        }
        return $categories;
    }

    public function findByName($name)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE name = :name");
        $stmt->execute(['name' => $name]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        return new Category(
            $row['id'],
            $row['name'],
            $row['created_at'],
            $row['updated_at'],
            $row['is_deleted']
        );
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }
        return new Category(
            $row['id'],
            $row['name'],
            $row['created_at'],
            $row['updated_at'],
            $row['is_deleted']
        );
    }

    public function save(Category $category)
    {
        $categoryFound = $this->findByName($category->name);
        if ($categoryFound) {
            throw new \Exception('Ya existe una categoría con ese nombre.');
        }

        $sql = "INSERT INTO categories (id, name, created_at, updated_at, is_deleted) 
            VALUES (:id, :name, :created_at, :updated_at, :is_deleted)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->id = Uuid::uuid4()->toString();
        $stmt->bindValue(':id', $category->id, PDO::PARAM_STR);
        $stmt->bindValue(':name', $category->name, PDO::PARAM_STR);
        $category->created_at = date('Y-m-d H:i:s');
        $stmt->bindValue(':created_at', $category->created_at, PDO::PARAM_STR);
        $category->updated_at = date('Y-m-d H:i:s');
        $stmt->bindValue(':updated_at', $category->updated_at, PDO::PARAM_STR);
        $stmt->bindValue(':is_deleted', $category->is_deleted, PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function update(Category $category)
    {
        $sql = "UPDATE categories SET 
              name = :name,
              updated_at = :updated_at
              WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':name', $category->name, PDO::PARAM_STR);
        $category->updated_at = date('Y-m-d H:i:s');
        $stmt->bindValue(':updated_at', $category->updated_at, PDO::PARAM_STR);
        $stmt->bindValue(':id', $category->id, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function deleteById($id)
    {
        $sql = "UPDATE categories SET 
              is_deleted = true
              WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function active($id)
    {
        $sql = "UPDATE categories SET 
              is_deleted = false
              WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}