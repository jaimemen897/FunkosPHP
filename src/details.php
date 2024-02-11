<?php

use config\Config;
use services\FunkosService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkosService.php';
require_once __DIR__ . '/models/Funko.php';

$session = $sessionService = SessionService::getInstance();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$funko = null;

if ($id === false) {
    header('Location: index.php');
    exit;
} else {
    $config = Config::getInstance();
    $funkosService = new FunkosService($config->db);
    $funko = $funkosService->findById($id);
    if ($funko === null) {
        header('Location: index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Funko</title>
    <?php require_once 'linkBootstrap.php'; ?>
</head>
<body>
<?php require_once 'header.php'; ?>
<div class="container">

    <h1>Detalles del Funko</h1>
    <dl class="row mt-4">
        <div class="col-sm-6">
            <dt class="col-sm-2">ID</dt>
            <dd class="col-sm-10"><?php echo htmlspecialchars($funko->id); ?></dd>

            <dt class="col-sm-2">Nombre</dt>
            <dd class="col-sm-10"><?php echo htmlspecialchars($funko->name); ?></dd>

            <dt class="col-sm-2">Precio</dt>
            <dd class="col-sm-10"><?php echo htmlspecialchars($funko->price); ?></dd>

            <dt class="col-sm-2">Stock</dt>
            <dd class="col-sm-10"><?php echo htmlspecialchars($funko->stock); ?></dd>

            <dt class="col-sm-2">Categoría</dt>
            <dd class="col-sm-10"><?php echo htmlspecialchars($funko->category_name); ?></dd>
        </div>
        <div class="col-sm-6">
            <dt class="col-sm-2">Imagen</dt>
            <dd class="col-sm-10">
                <img alt="Funko Image" class="img-fluid" src="<?php echo htmlspecialchars($funko->image); ?>"
                     width="280" height="280">
            </dd>
        </div>
    </dl>
    <a class="btn btn-primary" href="index.php">Volver</a>
</div>

<?php require_once 'footer.php'; ?>

<?php require_once 'links.php'; ?>
</body>
</html>
