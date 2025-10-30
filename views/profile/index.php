<?php
// Obtener datos del usuario
require_once __DIR__ . '/../../controllers/profileController.php';
require_once __DIR__ . '/../../config/database.php';

$profileController = new ProfileController($pdo);
$user = $profileController->showProfile();

// Manejar mensajes de éxito/error
$message = '';
$messageType = '';
if (isset($_SESSION['profile_message'])) {
    $message = $_SESSION['profile_message'];
    $messageType = $_SESSION['profile_message_type'] ?? 'info';
    unset($_SESSION['profile_message'], $_SESSION['profile_message_type']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Control Firmas</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/firmas/assets/css/main.css'; ?>" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content flex-1 overflow-y-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">Mi Perfil</h1>
                    <div class="flex space-x-2">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-user-edit mr-2"></i>Editar Perfil
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6">
                <!-- Mensajes -->
                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-md <?php echo $messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : ($messageType === 'error' ? 'bg-red-50 border border-red-200 text-red-800' : 'bg-blue-50 border border-blue-200 text-blue-800'); ?>">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : ($messageType === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'); ?>"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium"><?php echo htmlspecialchars($message); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Información del Perfil -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Información Personal</h3>
                            </div>
                            <div class="p-6">
                                <form id="profileForm" method="POST" action="index.php?controller=profile&action=update">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo</label>
                                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre'] ?? ''); ?>"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                        </div>

                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                        </div>

                                        <div>
                                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                            <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user['telefono'] ?? ''); ?>"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                                            <input type="text" id="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50" disabled>
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                                        <textarea id="direccion" name="direccion" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($user['direccion'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="fas fa-save mr-2"></i>Guardar Cambios
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar del Perfil -->
                    <div class="lg:col-span-1">
                        <!-- Avatar -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Foto de Perfil</h3>
                            </div>
                            <div class="p-6 text-center">
                                <div class="mb-4">
                                    <?php if (!empty($user['avatar'])): ?>
                                        <img src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/firmas/uploads/avatars/' . htmlspecialchars($user['avatar']); ?>"
                                            alt="Avatar" class="w-24 h-24 rounded-full mx-auto object-cover border-4 border-gray-200">
                                    <?php else: ?>
                                        <div class="w-24 h-24 rounded-full mx-auto bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user text-3xl text-gray-500"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <form id="avatarForm" method="POST" action="index.php?controller=profile&action=upload_picture" enctype="multipart/form-data">
                                    <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                                    <button type="button" onclick="document.getElementById('avatar').click()"
                                        class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        <i class="fas fa-camera mr-2"></i>Cambiar Foto
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Información de la Cuenta -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Información de la Cuenta</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Rol</label>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $user['rol'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'; ?>">
                                            <?php echo ucfirst($user['rol'] ?? 'usuario'); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Miembro desde</label>
                                        <p class="text-sm text-gray-900"><?php echo date('d/m/Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                                    </div>
                                    <div class="pt-4">
                                        <button type="button" onclick="openPasswordModal()"
                                            class="w-full px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                            <i class="fas fa-key mr-2"></i>Cambiar Contraseña
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Cambiar Contraseña -->
    <div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Cambiar Contraseña</h3>
                </div>
                <form id="passwordForm" method="POST" action="index.php?controller=profile&action=change_password">
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                                <input type="password" id="current_password" name="current_password" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                                <input type="password" id="new_password" name="new_password" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nueva Contraseña</label>
                                <input type="password" id="confirm_password" name="confirm_password" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                        <button type="button" onclick="closePasswordModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cambiar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Función para previsualizar avatar
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Aquí podrías mostrar una previsualización
                    // Por ahora, simplemente enviamos el formulario
                    document.getElementById('avatarForm').submit();
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Funciones para el modal de contraseña
        function openPasswordModal() {
            document.getElementById('passwordModal').classList.remove('hidden');
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.add('hidden');
            document.getElementById('passwordForm').reset();
        }

        // Validación de contraseñas
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }

            if (newPassword.length < 6) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 6 caracteres');
                return false;
            }
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('passwordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePasswordModal();
            }
        });
    </script>
</body>

</html>