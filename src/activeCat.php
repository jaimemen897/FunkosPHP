<?php

use config\Config;
use services\CategoryService;
use services\SessionService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/services/SessionService.php';

$session = SessionService::getInstance();
if (!$session->isAdmin()) {
    echo "<script type='text/javascript'>
            alert('No tienes permisos para eliminar una categoría');
            window.location.href = 'indexcategory.php';
          </script>";
    exit;
}


$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$cat = null;

if ($id === false) {
    header('Location: indexcategory.php');
    exit;
} else {
    $config = Config::getInstance();
    $categoryService = new CategoryService($config->db);

    $cat = $categoryService->findById($id);
    if ($cat) {
        $categoryService->active($id);
        header('Location: indexcategory.php');
    }
}

