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
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/models/Funko.php';

$session = SessionService::getInstance();
if (!$session->isAdmin()) {
    echo "<script type='text/javascript'>
            alert('No tienes permisos para modificar un funko');
            window.location.href = 'index.php';
          </script>";
    exit;
}

$config = Config::getInstance();
$categoryService = new CategoryService($config->db);
$funkosService = new FunkosService($config->db);

$category = $categoryService->findAll();
$errores = [];
$funko = null;

$funkoId = -1;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $funkoId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

    if (!$funkoId) {
        echo "<script type='text/javascript'>
            alert('No se proporcionó un ID de funko');
            window.location.href = 'index.php';
          </script>";
        header('Location: index.php');
        exit;
    }

    try {
        $funko = $funkosService->findById($funkoId);
    } catch (Exception $e) {
        $error = 'Error en el sistema. Por favor intente más tarde.';
        header('Location: index.php');
    }

    if (!$funko) {
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $funkoId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $category = $categoryService->findByName($category);

    $name = trim($name);
    if (empty($name)) {
        $errores['nombre'] = 'El nombre es obligatorio.';
    }

    if (!isset($price) || $price === '') {
        $errores['price'] = 'El price es obligatorio.';
    } elseif ($price < 0) {
        $errores['price'] = 'El price no puede ser negativo.';
    }

    if (!isset($stock) || $stock === '') {
        $errores['stock'] = 'El stock es obligatorio.';
    } elseif ($stock < 0) {
        $errores['stock'] = 'El stock no puede ser negativo.';
    }

    if (empty($category)) {
        $errores['category'] = 'La categoría es obligatoria.';
    }

    if (count($errores) === 0) {
        $funko = new Funko();
        $funko->name = $name;
        $funko->price = $price;
        $funko->stock = $stock;
        $funko->category_name = $category->name;
        $funko->id = $funkoId;


        try {
            $funkosService->update($funko);
            echo "<script type='text/javascript'>
                window.location.href = 'index.php';
                </script>";
        } catch (Exception $e) {
            $error = 'Error en el sistema. Por favor intente más tarde.';
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Funko</title>
    <?php require 'linkBootstrap.php'; ?>
</head>
<body>
<?php require_once 'header.php'; ?>
<div class="container">
    <h1>Actualizar Funko</h1>

    <form action="update.php" method="post">

        <input type="hidden" name="id" value="<?php echo $funkoId; ?>">

        <div class="form-group">
            <label for="name">Nombre</label>
            <input class="form-control" id="name" name="name" type="text" required
                   value="<?php echo htmlspecialchars($funko->name); ?>">
            <?php if (isset($errores['name'])): ?>
                <small class="text-danger"><?php echo $errores['name']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="price">Precio</label>
            <input class="form-control" id="price" min="0.0" name="price" step="0.01" type="number" required
                   value="<?php echo htmlspecialchars($funko->price); ?>">
            <?php if (isset($errores['price'])): ?>
                <small class="text-danger"><?php echo $errores['price']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="stock">Stock</label>
            <input class="form-control" id="stock" min="0" name="stock" type="number" required
                   value="<?php echo htmlspecialchars($funko->stock); ?>">
            <?php if (isset($errores['stock'])): ?>
                <small class="text-danger"><?php echo $errores['stock']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label for="category">Categoría</label>
            <select class="form-control" id="category" name="category" required>
                <option value="">Seleccione una categoría</option>
                <?php foreach ($category as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat->name); ?>"
                        <?php if ($cat->name == $funko->category_name) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['category'])): ?>
                <small class="text-danger"><?php echo $errores['category']; ?></small>
            <?php endif; ?>
        </div>

        <button class="btn btn-primary" type="submit">Actualizar</button>
        <a class="btn btn-secondary mx-2" href="index.php">Volver</a>
    </form>
</div>

<?php require_once 'footer.php'; ?>

<?php require 'links.php'; ?>
</body>
</html>


