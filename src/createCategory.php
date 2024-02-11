<?php

use config\Config;
use models\Category;
use services\CategoryService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/services/CategoryService.php';

$session = SessionService::getInstance();
if (!$session->isAdmin()) {
    echo "<script type='text/javascript'>
            alert('No tienes permisos para crear una categoría');
            window.location.href = 'index.php';
          </script>";
    exit;
}

$config = Config::getInstance();
$categoryService = new CategoryService($config->db);

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

    $name = trim($name);
    if (!isset($name) || $name === '') {
        $errores['name'] = 'El nombre es obligatorio.';
    }

    if (empty($errores)) {
        $category = new Category();
        $category->name = $name;

        try {
            $categoryService->save($category);
            header('Location: index.php');
        } catch (Exception $e) {
            $errores['name'] = 'Ya existe una categoría con ese nombre.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear categoría</title>
    <?php require 'linkBootstrap.php'; ?>
</head>
<body>
<?php require 'header.php'; ?>
<div class="container">
    <h1>Crear categoría</h1>
    <form action="createCategory.php" method="post">
        <div class="form-group mt-3 mb-3">
            <label for="name">Nombre</label>
            <input id="name" name="name" class="form-control" type="text" value="<?= $name ?? '' ?>">
            <?php if (isset($errores['name'])): ?>
                <div class="alert alert-danger"><?= $errores['name'] ?></div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Crear</button>
        <a class="btn btn-secondary mx-2" href="index.php">Volver</a>
    </form>
</div>
</body>