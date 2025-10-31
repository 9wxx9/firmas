<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Control Firmas</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../assets/css/main.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content flex-1 overflow-y-auto">
            <!-- Header -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
                    <div class="flex space-x-2">
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-6">
                <!-- Welcome Message -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h4 class="text-lg font-semibold text-blue-900 mb-2">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h4>
                    <hr class="border-blue-200 mb-4">
                    <p class="text-blue-700 mb-0"><strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?> | <strong>Rol:</strong> <?php echo htmlspecialchars($_SESSION['rol']); ?></p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                    <!-- Total de Libros -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-book text-blue-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-500 truncate">Total de diarios</p>
                                <p class="text-2xl font-bold text-gray-900"> <?= $totalDiarios; ?></p>
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-arrow-up text-xs"></i> Activo
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Firmas Pendientes -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-500 truncate">Firmas Pendientes</p>
                                <p class="text-2xl font-bold text-gray-900"> <?= $totalFirmasPendientes; ?></p>
                                <p class="text-xs text-yellow-600 mt-1">
                                    <i class="fas fa-exclamation-triangle text-xs"></i> Requiere atención
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Firmas Completadas -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-500 truncate">Firmas Completadas</p>
                                <p class="text-2xl font-bold text-gray-900"><?= $totalFirmasCompletas; ?></p>
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check text-xs"></i> Completado
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Total de Usuarios -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-users text-purple-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-500 truncate">Total de Usuarios</p>
                                <p class="text-2xl font-bold text-gray-900"><?= $totalUsuarios; ?></p>
                                <p class="text-xs text-purple-600 mt-1">
                                    <i class="fas fa-user-plus text-xs"></i> Registrados
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-6 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Acciones Rápidas</h3>
                        <span class="text-sm text-gray-500">Accesos directos</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                        <a href="index.php?controller=libro&action=create"
                            class="group flex items-center p-3 md:p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 transform hover:scale-105">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-plus-circle text-blue-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="font-medium text-gray-900 block truncate">Nuevo Diario</span>
                                <span class="text-xs text-gray-500">Crear registro</span>
                            </div>
                        </a>
                        <a href="index.php?controller=libro&action=index"
                            class="group flex items-center p-3 md:p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-all duration-200 transform hover:scale-105">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-search text-green-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="font-medium text-gray-900 block truncate">Buscar Firmas</span>
                                <span class="text-xs text-gray-500">Consultar estado</span>
                            </div>
                        </a>
                        <a href="index.php?controller=user&action=index"
                            class="group flex items-center p-3 md:p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 transform hover:scale-105">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-users text-purple-600 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="font-medium text-gray-900 block truncate">Gestionar Usuarios</span>
                                <span class="text-xs text-gray-500">Administrar accesos</span>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h6 class="text-lg font-semibold text-gray-900">Actividad Reciente</h6>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($actividadReciente)): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No hay actividad reciente
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($actividadReciente as $actividad): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php 
                                                $fecha = new DateTime($actividad['created_at']);
                                                echo $fecha->format('d/m/Y H:i');
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($actividad['accion']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div>
                                                <div class="font-medium">
                                                    <?php echo htmlspecialchars($actividad['diario_titulo']); ?>
                                                </div>
                                                <div class="text-gray-500 text-xs">
                                                    <?php echo htmlspecialchars($actividad['numero_referencia']); ?>
                                                    <?php if (!empty($actividad['firmante_nombre'])): ?>
                                                        • por <?php echo htmlspecialchars($actividad['firmante_nombre']); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($actividad['estado'] == 'firmado'): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Completado
                                                </span>
                                            <?php elseif ($actividad['estado'] == 'activo'): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Activo
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Pendiente
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h6 class="text-lg font-semibold text-gray-900">Libros Recientes</h6>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php if (empty($librosRecientes)): ?>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h6 class="font-medium text-gray-900 mb-1">No hay libros registrados</h6>
                                <p class="text-sm text-gray-500">Registra tu primer libro para comenzar</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">0</span>
                        </div>
                    <?php else: ?>
                        <?php foreach ($librosRecientes as $libro): ?>
                            <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h6 class="font-medium text-gray-900 truncate">
                                            <?php echo htmlspecialchars($libro['titulo']); ?>
                                        </h6>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <?php echo htmlspecialchars($libro['numero_referencia']); ?>
                                        </p>
                                    </div>
                                    <?php if ($libro['estado'] == 'activo'): ?>
                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200">
                                    <div class="text-xs text-gray-500">
                                        <?php echo $libro['mes']; ?>/<?php echo $libro['anio']; ?>
                                    </div>
                                    <div class="text-xs font-medium text-gray-700">
                                        <?php echo $libro['firmas_completadas']; ?>/<?php echo $libro['total_firmas']; ?> firmas
                                    </div>
                                </div>
                                
                                <?php if ($libro['total_firmas'] > 0): ?>
                                    <div class="mt-2">
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-blue-600 h-1.5 rounded-full" 
                                                 style="width: <?php echo ($libro['firmas_completadas'] / $libro['total_firmas']) * 100; ?>%">
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($librosRecientes) >= 5): ?>
                            <div class="pt-4 border-t border-gray-200">
                                <a href="index.php?controller=libro&action=index" class="block text-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                    Ver todos los libros →
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- JavaScript -->
    <script src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/firmas/'; ?>assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animación de entrada para las tarjetas de estadísticas
            const statCards = document.querySelectorAll('.grid > div');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Actualización automática de estadísticas cada 30 segundos
            let autoRefreshInterval;

            function startAutoRefresh() {
                autoRefreshInterval = setInterval(() => {
                    // Aquí se podría implementar una actualización AJAX
                    console.log('Actualizando estadísticas...');
                }, 30000);
            }

            function stopAutoRefresh() {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }
            }

            // Iniciar actualización automática
            startAutoRefresh();

            // Detener actualización cuando la página no esté visible
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopAutoRefresh();
                } else {
                    startAutoRefresh();
                }
            });

            // Mostrar mensaje de bienvenida
            if (typeof App !== 'undefined' && App.showToast) {
                setTimeout(() => {
                    App.showToast('¡Bienvenid@ de nuevo.', 'success');
                }, 1000);
            }
        });
    </script>
</body>

</html>