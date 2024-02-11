<?php

namespace services;

use models\Funko;
use PDO;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . '/../models/Funko.php';

class FunkosService
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllWithCategoryName($searchTerm)
    {
        $sql = "SELECT f.*, c.name AS category_name
            FROM funkos f
            LEFT JOIN categories c ON f.category_id = c.id";

        if (is_string($searchTerm) && $searchTerm !== '') {
            $sql .= " WHERE LOWER(f.name) LIKE :searchTerm OR LOWER(c.name) LIKE :searchTerm";
            $stmt = $this->pdo->prepare($sql);
            $searchTerm = '%' . strtolower($searchTerm) . '%';
            $stmt->bindValue(':searchTerm', $searchTerm);
        } else {
            $stmt = $this->pdo->prepare($sql);
        }

        $stmt->execute();

        $funkos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $funkos[] = new Funko(
                $row['id'],
                $row['name'],
                $row['image'],
                $row['price'],
                $row['stock'],
                $row['created_at'],
                $row['updated_at'],
                $row['category_id'],
                $row['category_name'],
            );
        }
        return $funkos;
    }

    public function findById($id)
    {
        $sql = "SELECT f.*, c.name AS category_name
                FROM funkos f
                LEFT JOIN categories c ON f.category_id = c.id
                WHERE f.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new Funko(
            $row['id'],
            $row['name'],
            $row['image'],
            $row['price'],
            $row['stock'],
            $row['created_at'],
            $row['updated_at'],
            $row['category_id'],
            $row['category_name'],
        );
    }

    public function save(Funko $funko)
    {
        $sql = "INSERT INTO funkos (id, name, image, price, stock, created_at, updated_at, category_id)
            VALUES (:id, :name, :image, :price, :stock, :created_at, :updated_at, :category_id)";

        $stmt = $this->pdo->prepare($sql);

        $funko->id = Uuid::uuid4()->toString();
        $stmt->bindValue(':id', $funko->id);
        $stmt->bindValue(':name', $funko->name);
        $stmt->bindValue(':image', $funko->image);
        $stmt->bindValue(':price', $funko->price, PDO::PARAM_INT);
        $stmt->bindValue(':stock', $funko->stock, PDO::PARAM_INT);
        $funko->created_at = date('Y-m-d H:i:s');
        $stmt->bindValue(':created_at', $funko->created_at);
        $funko->updated_at = date('Y-m-d H:i:s');
        $stmt->bindValue(':updated_at', $funko->updated_at);
        $stmt->bindValue(':category_id', $funko->category_id);

        return $stmt->execute();
    }

    public function update(Funko $funko)
    {
        $sql = "UPDATE funkos SET 
                  name = :name,
                  category_id = :category_id, 
                  stock = :stock, 
                  price = :price,
                  updated_at = :updated_at
                  WHERE id = :id";

        if ($funko->image !== Funko::$IMAGEN_DEFAULT) {
            $sql = "UPDATE funkos SET 
                  name = :name,
                  category_id = :category_id, 
                  stock = :stock, 
                  price = :price,
                  image = :image,
                  updated_at = :updated_at
                  WHERE id = :id";

        }

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id', $funko->id, PDO::PARAM_STR);
        $stmt->bindValue(':name', $funko->name);
        if ($funko->image !== Funko::$IMAGEN_DEFAULT) {
            $stmt->bindValue(':image', $funko->image);
        }
        $stmt->bindValue(':price', $funko->price, PDO::PARAM_INT);
        $stmt->bindValue(':stock', $funko->stock, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', $funko->category_id);
        $funko->updated_at = date('Y-m-d H:i:s');
        $stmt->bindValue(':updated_at', $funko->updated_at);

        return $stmt->execute();
    }

    public function deleteById($id)
    {
        $sql = "DELETE FROM funkos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}