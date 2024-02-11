<?php

namespace models;

class Funko
{
    public static $IMAGEN_DEFAULT = 'https://via.placeholder.com/150';
    private $id;
    private $name;
    private $image;
    private $price;
    private $stock;
    private $created_at;
    private $updated_at;
    private $category_id;
    private $category_name;

    public function __construct($id = null, $name = null, $image = null, $price = null, $stock = null, $created_at = null,
                                $updated_at = null, $category_id = null, $category_name = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image ?? self::$IMAGEN_DEFAULT;
        $this->price = $price;
        $this->stock = $stock;
        $this->category_id = $category_id;
        $this->category_name = $category_name;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}