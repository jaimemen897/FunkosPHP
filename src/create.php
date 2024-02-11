<?php

use config\Config;
use models\Funko;
use services\CategoryService;
use services\FunkosService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkosService.php';
require_once __DIR__ . '/models/Funko.php';
require_once __DIR__ . '/services/CategoryService.php';

$session = SessionService::getInstance();
if (!$session->isAdmin()) {
    echo "<script type='text/javascript'>
            alert('No tienes permisos para crear un funko');
            window.location.href = 'index.php';
          </script>";
    exit;
}

$config = Config::getInstance();
$categoryService = new CategoryService($config->db);
$funkosService = new FunkosService($config->db);

// Obtenemos todas las categorías
$categories = $categoryService->findAllActive();

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);

    $category = $categoryService->findByName($category);

    //si is_deleted es true, la categoría está eliminada
    if ($category && $category->is_deleted) {
        $errores['category'] = 'La categoría seleccionada está eliminada.';
    }

    $name = trim($name);
    if (!isset($name) || $name === '') {
        $errores['name'] = 'El nombre es obligatorio.';
    }

    if (!isset($price)) {
        $errores['price'] = 'El precio es obligatorio.';
    } elseif ($price < 0) {
        $errores['price'] = 'El precio no puede ser negativo.';
    }

    if (!isset($stock)) {
        $errores['stock'] = 'El stock es obligatorio.';
    } elseif ($stock < 0) {
        $errores['stock'] = 'El stock no puede ser negativo.';
    }

    if (!isset($category)) {
        $errores['category'] = 'La categoría es obligatoria.';
    }

    if (count($errores) === 0) {
        $funko = new Funko();
        $funko->name = $name;
        $funko->image = 'https://via.placeholder.com/300x300';
        $funko->price = $price;
        $funko->stock = $stock;
        $funko->created_at = date('Y-m-d H:i:s');
        $funko->updated_at = date('Y-m-d H:i:s');
        $funko->category_id = $category->id;

        try {
            $funkosService->save($funko);
            echo "<script type='text/javascript'>
                alert('Funko creado correctamente');
                window.location.href = 'index.php';
                </script>";
        } catch (Exception $e) {
            $error = $e->getMessage();
            echo "<script type='text/javascript'>
            alert('$error');
          </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Funko</title>
    <?php require 'linkBootstrap.php'; ?>
</head>
<body>
<?php require_once 'header.php'; ?>
<div class="container">
    <h1>Crear Funko</h1>

    <form action="create.php" method="post">
        <div class="form-group">
            <label for="name">Nombre</label>
            <input class="form-control" id="name" name="name" type="text" required>
            <?php if (isset($errores['name'])): ?>
                <small class="text-danger"><?php echo $errores['name']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="price">Precio</label>
            <input class="form-control" id="price" min="0" name="price" type="number" required value="0">
            <?php if (isset($errores['price'])): ?>
                <small class="text-danger"><?php echo $errores['price']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="image">Imagen</label>
            <input class="form-control" id="image" name="image" readonly type="text">
        </div>
        <div class="form-group">
            <label for="stock">Stock</label>
            <input class="form-control" id="stock" min="0" name="stock" type="number" required value="0">
            <?php if (isset($errores['stock'])): ?>
                <small class="text-danger"><?php echo $errores['stock']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group mb-3">
            <label for="category">Categoría</label>
            <select class="form-control" id="category" name="category" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat->name; ?>"><?php echo $cat->name; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['category'])): ?>
                <small class="text-danger"><?php echo $errores['category']; ?></small>
            <?php endif; ?>
        </div>

        <button class="btn btn-primary" type="submit">Crear</button>
        <a class="btn btn-secondary mx-2" href="index.php">Volver</a>
    </form>
</div>

<?php require_once 'footer.php'; ?>
<?php require 'links.php'; ?>
</body>
</html>