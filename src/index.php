<?php

use config\Config;
use services\FunkosService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkosService.php';
require_once __DIR__ . '/models/Funko.php';
$session = SessionService::getInstance();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio</title>
    <?php require_once 'linkBootstrap.php'; ?>
</head>
<body>
<?php require_once 'header.php'; ?>
<div class="container py-5 mb-5">


    <?php
    echo "<h1 class='mb-4'>Listado de funkos</h1>";
    $config = Config::getInstance();
    ?>

    <form action="index.php" class="mb-4" method="get" autocomplete="off">
        <div class="input-group">
            <input type="search" class="form-control" id="search" name="search" placeholder="Buscar por nombre">
            <div class="input-group-append">
                <button class="btn btn-primary btn-search" type="submit">Buscar</button>
            </div>
        </div>
    </form>

    <table class="table table-striped">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Imagen</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $searchTerm = $_GET['search'] ?? null;
        $funkosService = new FunkosService($config->db);
        $funkos = $funkosService->findAllWithCategoryName($searchTerm);
        ?>
        <?php foreach ($funkos as $funko): ?>
            <tr>
                <td><?php echo htmlspecialchars($funko->id); ?></td>
                <td><?php echo htmlspecialchars($funko->name); ?></td>
                <td><?php echo htmlspecialchars($funko->price); ?></td>
                <td><?php echo htmlspecialchars($funko->stock); ?></td>
                <td>
                    <img alt="Imagen del funko" height="50"
                         src="<?php echo htmlspecialchars($funko->image); ?>" width="50">
                </td>
                <td>
                    <a class="btn btn-primary btn-sm"
                       href="details.php?id=<?php echo $funko->id; ?>">Detalles</a>
                    <?php if ($session->isAdmin()): ?>
                        <a class="btn btn-secondary btn-sm"
                           href="update.php?id=<?php echo $funko->id; ?>">Editar</a>
                        <a class="btn btn-info btn-sm"
                           href="update-image.php?id=<?php echo $funko->id; ?>">Imagen</a>
                        <a class="btn btn-danger btn-sm"
                           href="delete.php?id=<?php echo $funko->id; ?>"
                           onclick="return confirm('¿Estás seguro de que deseas eliminar este funko?');">
                            Eliminar
                        </a>
                    <?php endif; ?>

                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($session->isAdmin()): ?>
        <a class="btn btn-primary btn-block" href="create.php">Nuevo Funko</a>
    <?php endif; ?>

    <p class="mt-4 text-center text-muted">
        <?php
        if ($session->isLoggedIn()) {
            echo "<span>Nº de visitas: {$session->getVisitCount()}</span>";
            echo "<span>, desde el último login en: {$session->getLastLoginDate()}</span>";
        }
        ?>
    </p>

</div>

<?php require_once 'footer.php'; ?>

<?php require_once 'links.php'; ?>
</body>
</html>
