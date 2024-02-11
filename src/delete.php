<?php

use config\Config;
use models\Funko;
use services\FunkosService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkosService.php';
require_once __DIR__ . '/models/Funko.php';
require_once __DIR__ . '/services/SessionService.php';

$session = SessionService::getInstance();
if (!$session->isAdmin()) {
    echo "<script type='text/javascript'>
            alert('No tienes permisos para eliminar un funko');
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
    if ($funko) {
        if ($funko->image !== Funko::$IMAGEN_DEFAULT) {
            $imageUrl = $funko->image;                 // http://localhost:8080/uploads/imagen.jpg
            $basePath = $config->uploadPath;            // /var/www/html/public/uploads/
            $imagePathInUrl = parse_url($imageUrl, PHP_URL_PATH); // /uploads/imagen.jpg
            $imageFile = basename($imagePathInUrl);     // imagen.jpg
            $imageFilePath = $basePath . $imageFile;    // /var/www/html/public/uploads/imagen.jpg
            // Borramos la imagen
            // Verificar si el archivo existe y luego borrarlo
            if (file_exists($imageFilePath)) {
                unlink($imageFilePath);
            }
        }
        $funkosService->deleteById($id);
        header('Location: index.php');
    }
}

