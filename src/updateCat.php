<?php

use config\Config;
use models\Category;
use services\CategoryService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/models/Category.php';

$session = SessionService::getInstance();
if (!$session->isAdmin()) {
    header('Location: index.php');
    exit;
}

$config = Config::getInstance();
$categoryService = new CategoryService($config->db);

$categorias = $categoryService->findAll();
$errores = [];
$cat = null;

$catId = -1;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $catId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

    if (!$catId) {
        echo "<script type='text/javascript'>
            alert('No se proporcionó un ID de funko');
            window.location.href = 'indexcategory.php';
          </script>";
        header('Location: indexcategory.php');
        exit;
    }

    try {
        $cat = $categoryService->findById($catId);
    } catch (Exception $e) {
        $error = 'Error en el sistema. Por favor intente más tarde.';
        header('Location: indexcategory.php');
    }

    if (!$cat) {
        header('Location: indexcategory.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catId = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);


    $name = trim($name);
    if (empty($name)) {
        $errores['nombre'] = 'El nombre es obligatorio.';
    }

    if (count($errores) === 0) {
        $cat = new Category();
        $cat->id = $catId;
        $cat->updated_at = date('Y-m-d H:i:s');
        $cat->name = $name;


        try {
            $categoryService->update($cat);
            header('Location: indexcategory.php');
        } catch (Exception $e) {
            $error = $e->getMessage();

        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Categoría</title>
    <?php require 'linkBootstrap.php'; ?>
</head>
<body>
<?php require_once 'header.php'; ?>
<div class="container">
    <h1>Actualizar Categoría</h1>
<!-- <?= $error ?> -->
    <form action="updateCat.php" method="post">

        <input type="hidden" name="id" value="<?php echo $catId; ?>">

        <div class="form-group mb-3">
            <label for="name">Nombre</label>
            <input class="form-control" id="name" name="name" type="text" required
                   value="<?php echo htmlspecialchars($cat->name); ?>">
            <?php if (isset($errores['name'])): ?>
                <small class="text-danger"><?php echo $errores['name']; ?></small>
            <?php endif; ?>
        </div>

        <button class="btn btn-primary" type="submit">Actualizar</button>
        <a class="btn btn-secondary mx-2" href="indexcategory.php">Volver</a>
    </form>
</div>

<?php require_once 'footer.php'; ?>

<?php require 'links.php'; ?>
</body>
</html>


