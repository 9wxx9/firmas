<?php
session_start();

// Mostrar mensaje de error si existe
$error_message = '';
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // ✅ Importante: limpiar después de usar
}

// Mostrar mensaje de éxito si existe
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // ✅ Importante: limpiar después de usar
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrate</title>
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
            <div class="card" role="region" aria-label="Formulario de registro">
                <h2 class="card-title">registro</h2>
                <?php if ($error_message): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                <?php if ($success_message): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
        <?php echo htmlspecialchars($success_message); ?>
    </div>
<?php endif; ?>
                <form method="post" action="public/index.php?controller=auth&action=register">
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
                    <div class="field">
                                <input type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       placeholder=" " 
                                       required>
                                <label for="confirm_password">
                                    <i class="fas fa-lock mr-2"></i>
                                    Confirmar Contraseña *
                                </label>
                                <button type="button" class="toggle-password" aria-label="Mostrar u ocultar contraseña" title="Mostrar u ocultar contraseña">
                            <svg class="icon-eye" viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" stroke="currentColor" stroke-width="1.5"/>
                                <circle cx="12" cy="12" r="3.75" stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                        </button>
                            </div>
                            <div class="field">
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       placeholder=" " 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                       required>
                                <label for="email">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Correo Electrónico *
                                </label>
                            </div>
                            <div class="field">
                                <input type="tel" 
                                       id="telefono" 
                                       name="telefono" 
                                       placeholder=" " 
                                       value="<?php echo htmlspecialchars($telefono ?? ''); ?>">
                                <label for="telefono">
                                    <i class="fas fa-phone mr-2"></i>
                                    Teléfono
                                </label>
                            </div>
                            <div class="field">
                                <input type="text" 
                                       id="rol" 
                                       name="rol" 
                                       placeholder=" " 
                                       value="<?php echo htmlspecialchars($rol ?? ''); ?>">
                                <label for="rol">
                                    rol
                                </label>
                            </div>
                    <button class="btn" type="submit">registrar</button>
                </form>
            </div>
            <div class="credit">© <?php echo date('Y'); ?> ASURCOL</div>
        </section>
    </main>
</body>
</html>