<?php
session_start();

// Mostrar mensaje de error si existe
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <main class="split">
        <section class="split-left">
            <div class="brand">Control Firmas</div>
            <div class="logo"></div>
            <!-- <h1 class="headline">Bienvenido de nuevo</h1>
            <p class="subtitle">Accede para gestionar tus firmas de forma sencilla y rápida.</p> -->
            <div class="shapes" aria-hidden="true">
                <span class="blob blob-1"></span>
                <span class="blob blob-2"></span>
                <span class="blob blob-3"></span>
            </div>
        </section>
        <section class="split-right">
            <div class="card" role="region" aria-label="Formulario de inicio de sesión">
                <h2 class="card-title">Iniciar sesión</h2>
                <?php if ($error_message): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                <form method="post" action="public/index.php?controller=auth&action=login">
                    <div class="field">
                        <input id="username" name="username" type="text" autocomplete="username" placeholder=" " required>
                        <label for="username">Usuario</label>
                    </div>
                    <div class="field password-field">
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder=" " required>
                        <label for="password">Contraseña</label>
                        <button type="button" class="toggle-password" aria-label="Mostrar u ocultar contraseña" title="Mostrar u ocultar contraseña">
                            <svg class="icon-eye" viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" stroke="currentColor" stroke-width="1.5"/>
                                <circle cx="12" cy="12" r="3.75" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </button>
                    </div>
                    <button class="btn" type="submit">Entrar</button>
                    <div class="form-extras">
                        <label class="remember"><input type="checkbox" name="remember" value="1"> Recordarme</label>
                        <a class="forgot" href="#" title="Recuperar contraseña">¿Olvidaste tu contraseña?</a>
                    </div>
                </form>
            </div>
            <div class="credit">© <?php echo date('Y'); ?> ASURCOL</div>
        </section>
    </main>
</body>
</html>