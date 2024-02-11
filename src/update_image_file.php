<?php

use config\Config;
use services\FunkosService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/services/FunkosService.php';
require_once __DIR__ . '/models/Funko.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $config = Config::getInstance();

        $id = $_POST['id'];
        $uploadDir = $config->uploadPath;

        $archivo = $_FILES['image'];

        $nombre = $archivo['name'];
        $tipo = $archivo['type'];
        $tmpPath = $archivo['tmp_name'];
        $error = $archivo['error'];

        $allowedTypes = ['image/jpeg', 'image/png'];
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($fileInfo, $tmpPath);
        $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

        if (in_array($detectedType, $allowedTypes) && in_array($extension, $allowedExtensions)) {

            $funkosService = new FunkosService($config->db);
            $funko = $funkosService->findById($id);
            if ($funko === null) {
                header('Location: index.php');
                exit;
            }

            $newName = $funko->id . '.' . $extension;

            move_uploaded_file($tmpPath, $uploadDir . $newName);

            $funko->image = $config->uploadUrl . $newName;

            $funkosService->update($funko);


            header('Location: update-image.php?id=' . $id);
            exit;
        }
        header('Location: index.php');
        exit;

    } else {
        switch ($_FILES['image']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                die('El archivo es demasiado grande');
            case UPLOAD_ERR_PARTIAL:
                die('El archivo fue solo parcialmente subido');
            case UPLOAD_ERR_NO_FILE:
                die('No se subió ningún archivo');
            case UPLOAD_ERR_NO_TMP_DIR:
                die('Falta el directorio temporal');
            case UPLOAD_ERR_CANT_WRITE:
                die('Falló al escribir el archivo al disco');
            case UPLOAD_ERR_EXTENSION:
                die('Una extensión de PHP detuvo la subida del archivo');
            default:
                die('Error desconocido en la subida del archivo');
        }
    }
} else {
    die('Método de solicitud no permitido');

    /*header('Location: index.php');
    exit;*/
}
