<?php

use config\Config;
use services\CategoryService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/services/CategoryService.php';
$session = SessionService::getInstance();


if (!$session->isAdmin()) {
    header('Location: login.php');
    exit;
}
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
    echo "<h1 class='mb-4'>Listado de categorias</h1>";
    $config = Config::getInstance();
    ?>

    <table class="table table-striped">
        <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Eliminada</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $searchTerm = $_GET['search'] ?? null;
        $categoryService = new CategoryService($config->db);
        $categories = $categoryService->findAll();
        ?>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?php echo htmlspecialchars($cat->id); ?></td>
                <td><?php echo htmlspecialchars($cat->name); ?></td>
                <td><?php echo htmlspecialchars($cat->is_deleted ? 'Sí' : 'No'); ?></td>
                <td>
                    <a class="btn btn-primary btn-sm"
                       href="detailsCat.php?id=<?php echo $cat->id; ?>">Detalles</a>
                    <a class="btn btn-secondary btn-sm"
                       href="updateCat.php?id=<?php echo $cat->id; ?>">Editar</a>
                    <a class="btn btn-success btn-sm"
                       href="activeCat.php?id=<?php echo $cat->id; ?>">Activar</a>
                    <a class="btn btn-danger btn-sm"
                       href="deleteCat.php?id=<?php echo $cat->id; ?>"
                       onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
        <a class="btn btn-primary btn-block" href="createCategory.php">Nuevo Funko</a>

    <p class="mt-4 text-center text-muted">
        <?php
            echo "<span>Nº de visitas: {$session->getVisitCount()}</span>";
            echo "<span>, desde el último login en: {$session->getLastLoginDate()}</span>";
        ?>
    </p>

</div>

<?php require_once 'footer.php'; ?>

<?php require_once 'links.php'; ?>
</body>
</html>
