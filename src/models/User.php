<?php

namespace models;

class User
{
    public $id;
    public $username;
    public $password;
    public $name;
    public $surnames;
    public $email;
    public $created_at;
    public $updated_at;
    public $is_deleted;
    public $roles = [];

    public function __construct($id, $username, $password, $name, $surnames, $email, $created_at, $updated_at, $is_deleted, $roles = [])
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
        $this->surnames = $surnames;
        $this->email = $email;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->is_deleted = $is_deleted;
        $this->roles = $roles;
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