<?php

use config\Config;
use services\SessionService;
use services\UsersService;

require_once 'vendor/autoload.php';

require_once __DIR__ . '/services/SessionService.php';
require_once __DIR__ . '/services/UsersService.php';
require_once __DIR__ . '/config/Config.php';

$session = SessionService::getInstance();
$config = Config::getInstance();

$error = '';
$usersService = new UsersService($config->db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y limpiar la entrada
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $username = trim($username);
    $password = trim($password);

    if (!$username || !$password) {
        $error = 'Usuario/a o contraseña inválidos.';
    } else {
        try {
            $user = $usersService->authenticate($username, $password);
            if ($user) {
                $isAdmin = in_array('ADMIN', $user->roles);
                $session->login($user->username, $isAdmin);
                header('Location: index.php');
                exit;
            } else {
                $error = 'Usuario/a o contraseña inválidos.';
            }
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
    <title>Login</title>
    <?php require_once 'linkBootstrap.php'; ?>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
<div class="container w-50">
    <h1>Login</h1>
    <form action="login.php" method="post">
        <div class="form-group">
            <div class="mt-3 mb-3">
                <label for="username">Username:</label>
                <input class="form-control" id="username" name="username" required type="text">
            </div>
            <div class="mt-3 mb-3">
                <label for="password">Password:</label>
                <input class="form-control" id="password" name="password" required type="password">
            </div>
        </div>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <button class="btn btn-primary" type="submit">Login</button>
        <a class="btn btn-secondary" href="index.php">Volver</a>


    </form>
</div>


<?php require_once 'links.php'; ?>
</body>
</html>