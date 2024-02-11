<?php


use config\Config;
use services\FunkosService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkosService.php';
require_once __DIR__ . '/models/Funko.php';

$session = SessionService::getInstance();
if (!$session->isAdmin()) {
    echo "<script type='text/javascript'>
            alert('No tienes permisos para modificar un funko');
            window.location.href = 'index.php';
          </script>";
    exit;
}


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
        echo "<script type='text/javascript'>
                alert('No existe el funko');
                window.location.href = 'index.php';
                </script>";
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

    <h1>Actualizar Imagen Funko</h1>

    <dl class="row">
        <div class="col-sm-6">
            <dt class="col-sm-2">ID:</dt>
            <dd class="col-sm-10"><?php echo htmlspecialchars($funko->id); ?></dd>
            <dt class="col-sm-2">Nombre:</dt>
            <dd class="col-sm-10"><?php echo htmlspecialchars($funko->name); ?></dd>
            <dt class="col-sm-2">Precio:</dt>
            <dd class="col-sm-10"><?php echo htmlspecialchars($funko->price); ?></dd>
            <dt class="col-sm-2">Stock:</dt>
            <dd class="col-sm-10"><?php echo htmlspecialchars($funko->stock); ?></dd>
            <form action="update_image_file.php" enctype="multipart/form-data" method="post">
                <div class="form-group mb-3">
                    <label for="image">Imagen:</label>
                    <input accept="image/*" class="form-control-file" id="image" name="image" required type="file">
                    <small class="text-danger"></small>
                    <input name="id" value="<?php echo $id; ?>" type="hidden">
                </div>

                <button class="btn btn-primary" type="submit">Actualizar</button>
                <a class="btn btn-secondary mx-2" href="index.php">Volver</a>
            </form>
        </div>
        <div class="col-sm-6">
            <dt class="col-sm-2">Imagen:</dt>

            <dd class="col-sm-10">
                <img alt="Funko Image" class="img-fluid" src="<?php echo htmlspecialchars($funko->image); ?>" width="300"
                     height="300">
            </dd>
        </div>
    </dl>



</div>

<?php require_once 'footer.php'; ?>

<?php require 'links.php'; ?>
</body>
</html>