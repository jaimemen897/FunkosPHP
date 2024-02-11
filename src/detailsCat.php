<?php

use config\Config;
use services\CategoryService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/models/Category.php';

$session = $sessionService = SessionService::getInstance();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$cat = null;

if ($id === false) {
    header('Location: index.php');
    exit;
} else {
    $config = Config::getInstance();
    $categoryService = new CategoryService($config->db);
    $cat = $categoryService->findById($id);
    if ($cat === null) {
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de la categoría</title>
    <?php require_once 'linkBootstrap.php'; ?>
</head>
<body>
<?php require_once 'header.php'; ?>
<div class="container">

    <h1>Detalles de la categoría</h1>
    <dl class="row mt-4">
        <dt class="col-sm-2">ID</dt>
        <dd class="col-sm-10"><?php echo htmlspecialchars($cat->id); ?></dd>

        <dt class="col-sm-2">Nombre</dt>
        <dd class="col-sm-10"><?php echo htmlspecialchars($cat->name); ?></dd>

        <dt class="col-sm-2">Eliminada</dt>
        <dd class="col-sm-10"><?php echo htmlspecialchars($cat->is_deleted ? 'Sí': 'No'); ?></dd>

    </dl>
    <a class="btn btn-primary" href="indexcategory.php">Volver</a>
</div>

<?php require_once 'footer.php'; ?>

<?php require_once 'links.php'; ?>
</body>
</html>
