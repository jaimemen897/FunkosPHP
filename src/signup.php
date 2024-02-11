<?php

use config\Config;
use models\User;
use services\SessionService;
use services\UsersService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/services/UsersService.php';
require_once __DIR__ . '/config/Config.php';

$session = SessionService::getInstance();
$config = Config::getInstance();

$error = '';
$usersService = new UsersService($config->db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $surnames = filter_input(INPUT_POST, 'surnames', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);

    $username = trim($username);
    $password = trim($password);
    $name = trim($name);
    $surnames = trim($surnames);
    $email = trim($email);

    if (!$username) {
        $errores['username'] = 'El nombre de usuario es requerido.';
    }
    if (!$password) {
        $errores['password'] = 'La contraseña es requerida.';
    }
    if (!$name) {
        $errores['name'] = 'El nombre es requerido.';
    }
    if (!$surnames) {
        $errores['surnames'] = 'Los apellidos son requeridos.';
    }
    if (!$email) {
        $errores['email'] = 'El correo electrónico es requerido.';
    }
    if (empty($errores)) {
        try {
            $roles = 'USER';
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');
            $user = new User(null, $username, $password, $name, $surnames, $email, $created_at, $updated_at, $roles);
            $usersService->save($user);
            $isAdmin = $session->isAdmin();
            $session->login($user, $isAdmin);
            header('Location: index.php');
        } catch (Exception $e) {
            $error = $e->getMessage();
            echo "<script type='text/javascript'>
            alert('$error');
          </script>";
        }
    } else {
        echo "<script type='text/javascript'>
            alert('Rellena todos los campos');
          </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <?php include 'linkBootstrap.php' ?>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
<div class="container">
    <h1>Registrarse</h1>

    <form class="mt-3" action="signup.php" method="post" autocomplete="on">
        <!--username, password, name, surnames, email-->
        <div class="form-group">
            <label for="username">Username</label>
            <input class="form-control" id="username" name="username" type="text" required>
            <?php if (isset($errores['username'])): ?>
                <small class="text-danger"><?php echo $errores['username']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input class="form-control" id="password" name="password" type="password" required>
            <?php if (isset($errores['password'])): ?>
                <small class="text-danger"><?php echo $errores['password']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="name">Nombre</label>
            <input class="form-control" id="name" name="name" type="text" required>
            <?php if (isset($errores['name'])): ?>
                <small class="text-danger"><?php echo $errores['name']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="surnames">Apellidos</label>
            <input class="form-control" id="surnames" name="surnames" type="text" required>
            <?php if (isset($errores['surnames'])): ?>
                <small class="text-danger"><?php echo $errores['surnames']; ?></small>
            <?php endif; ?>
        </div>
        <div class="form-group mb-3">
            <label for="email">Correo electrónico</label>
            <input class="form-control" id="email" name="email" type="email" required>
            <?php if (isset($errores['email'])): ?>
                <small class="text-danger"><?php echo $errores['email']; ?></small>
            <?php endif; ?>
        </div>

        <button class="btn btn-primary" type="submit">Registrarse</button>
        <a class="btn btn-secondary mx-2" href="index.php">Volver</a>
    </form>
</div>
</body>
