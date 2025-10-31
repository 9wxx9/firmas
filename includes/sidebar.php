<!-- Sidebar -->
<nav id="sidebar" class="sidebar-fixed">
    <div class="sticky top-0 pt-3">
        <!-- Logo/Brand -->
        <div class="sidebar-brand flex items-center justify-center mb-4">
            <div class="sidebar-brand-icon">
                <i class="fas fa-signature text-blue-600"></i>
            </div>
            <div class="sidebar-brand-text mx-2">Control Firmas</div>
        </div>

        <!-- User Info -->
        <div class="user-info mb-4 p-3 bg-white rounded shadow-sm mx-3">
            <div class="flex items-center">
                <div class="user-avatar mr-3">
                    <i class="fas fa-user-circle text-2xl text-gray-600"></i>
                </div>
                <div class="user-details">
                    <h6 class="mb-0 font-semibold"><?php echo htmlspecialchars($_SESSION['nombre']); ?></h6>
                    <small class="text-gray-500"><?php echo htmlspecialchars($_SESSION['rol'] ?? 'usuario'); ?></small>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul class="flex flex-col space-y-1 px-3">
            <!-- Dashboard -->

            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <li>
                <a class="flex items-center px-3 py-2 text-gray-700 bg-blue-100 rounded-md hover:bg-blue-200 transition-colors" href="index.php?controller=dashboard">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Dashboard
                </a>
            </li>
            <?php endif; ?>

            <!-- Libros -->
            <li>
                <button class="w-full flex items-center justify-between px-3 py-2 text-gray-700 hover:bg-gray-200 rounded-md transition-colors" onclick="toggleSubmenu('librosSubmenu')">
                    <div class="flex items-center">
                        <i class="fas fa-book mr-2"></i>
                        Gestión de diarios
                    </div>
                    <i class="fas fa-chevron-down transform transition-transform" id="librosSubmenu-icon"></i>
                </button>
                <div class="hidden ml-6 mt-1 space-y-1" id="librosSubmenu">
                    <a class="block px-3 py-1 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded" href="index.php?controller=libro&action=create">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Registrar diarios
                    </a>
                    <a class="block px-3 py-1 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded" href="index.php?controller=libro&action=index">
                        <i class="fas fa-list mr-2"></i>
                        Mis diarios
                    </a>
                </div>
            </li>

            <!-- Reportes (Solo Admin) -->
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <li>
                    <button class="w-full flex items-center justify-between px-3 py-2 text-gray-700 hover:bg-gray-200 rounded-md transition-colors" onclick="toggleSubmenu('reportesSubmenu')">
                        <div class="flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i>
                            Reportes
                        </div>
                        <i class="fas fa-chevron-down transform transition-transform" id="reportesSubmenu-icon"></i>
                    </button>
                    <div class="hidden ml-6 mt-1 space-y-1" id="reportesSubmenu">
                        <a class="block px-3 py-1 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded" href="index.php?controller=reporte&action=estadisticas">
                            <i class="fas fa-chart-line mr-2"></i>
                            Estadísticas
                        </a>
                        <a class="block px-3 py-1 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded" href="index.php?controller=admin&action=usuarios">
                            <i class="fas fa-users mr-2"></i>
                            Usuarios
                        </a>
                        <a class="block px-3 py-1 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded" href="index.php?controller=admin&action=usuarios">
                            <i class="fas fa-solid fa-file mr-2"></i>
                            generar reporte
                        </a>
                    </div>
                </li>
            <?php endif; ?>

            <!-- Perfil -->
            <li>
                <a class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-200 rounded-md transition-colors" href="index.php?controller=profile&action=show">
                    <i class="fas fa-user mr-2"></i>
                    Mi Perfil
                </a>
            </li>
            <!-- Divider -->
            <hr class="my-3 border-gray-300">

            <!-- Logout -->
            <li>
                <a class="flex items-center px-3 py-2 text-red-600 hover:bg-red-50 rounded-md transition-colors" href="index.php?controller=auth&action=logout">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Sidebar Toggle Button for Mobile -->
<button class="fixed top-4 left-4 z-50 bg-blue-600 text-white p-2 rounded-md md:hidden" type="button" onclick="toggleSidebar()" aria-label="Toggle navigation">
    <i class="fas fa-bars"></i>
</button>

<script>
    function toggleSubmenu(submenuId) {
        const submenu = document.getElementById(submenuId);
        const icon = document.getElementById(submenuId + '-icon');

        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            submenu.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    }
</script>