<?php

use services\SessionService;

require_once __DIR__ . '/services/SessionService.php';
$session = SessionService::getInstance();
?>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img alt="Logo" class="d-inline-block align-text-top" src="/images/favicon.png" width="30" height="30">
                Inicio
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if ($session->isAdmin()) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="create.php">Nuevo Funko</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="indexcategory.php">Categorías</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($session->isLoggedIn()) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="signup.php">Registrar</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <span class="navbar-text">
                    <?php echo htmlspecialchars($session->isLoggedIn() ? $session->getUsername() : 'Invitado'); ?>
                </span>
            </div>
        </div>
    </nav>
</header>