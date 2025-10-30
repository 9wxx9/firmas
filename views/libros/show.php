<?php
require_once __DIR__ . '/../../controllers/LibroController.php';
require_once __DIR__ . '/../../config/database.php';



// Al inicio de show.php, después de session_start()
if (isset($_SESSION['debug_data'])) {
    echo "<div style='background: lightgreen; padding: 10px; margin: 10px; border: 2px solid green;'>";
    echo "<h4>✅ DEBUG - Datos Recibidos:</h4>";
    echo "Libro ID: " . $_SESSION['debug_data']['libro_id'] . "<br>";
    echo "Firmantes: " . implode(', ', $_SESSION['debug_data']['firmantes']) . "<br>";
    echo "Hora: " . $_SESSION['debug_data']['timestamp'];
    echo "</div>";

    unset($_SESSION['debug_data']);
}

// Mostrar mensajes de sesión
if (isset($_SESSION['message'])) {
    echo "<div style='background: " . ($_SESSION['message_type'] === 'success' ? 'lightgreen' : 'lightcoral') . "; 
          padding: 10px; margin: 10px; border: 2px solid " . ($_SESSION['message_type'] === 'success' ? 'green' : 'red') . ";'>";
    echo $_SESSION['message'];
    echo "</div>";

    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}




$pageTitle = 'Detalles del Libro';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Sistema de Firmas</title>
    <link href="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/firmas/'; ?>assets/css/main.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <a href="index.php?controller=libro&action=index"
                                class="text-gray-600 hover:text-gray-900 transition duration-200">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <h1 class="text-2xl font-bold text-gray-900">
                                <i class="fas fa-book mr-2 text-blue-600"></i>
                                <?php echo htmlspecialchars($libro['titulo']); ?>
                            </h1>
                        </div>
                        <div class="flex space-x-2">
                            <a href="index.php?controller=libro&action=edit&id=<?php echo $libro['id']; ?>"
                                class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                                <i class="fas fa-edit mr-2"></i>
                                Editar
                            </a>
                            <button onclick="toggleAssignModal()"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                                <i class="fas fa-user-plus mr-2"></i>
                                Asignar Firmantes
                            </button>


                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

                    <!-- Messages -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="mb-4 p-4 rounded-lg <?php echo $_SESSION['message_type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'; ?>">
                            <div class="flex items-center">
                                <i class="fas <?php echo $_SESSION['message_type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2"></i>
                                <?php echo $_SESSION['message']; ?>
                            </div>
                        </div>
                        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <!-- Book Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Número de Referencia</label>
                                <p class="text-lg font-mono text-gray-900"><?php echo htmlspecialchars($libro['numero_referencia']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Mes/Año</label>
                                <p class="text-lg text-gray-900">
                                    <?php
                                    $meses = [
                                        '01' => 'Enero',
                                        '02' => 'Febrero',
                                        '03' => 'Marzo',
                                        '04' => 'Abril',
                                        '05' => 'Mayo',
                                        '06' => 'Junio',
                                        '07' => 'Julio',
                                        '08' => 'Agosto',
                                        '09' => 'Septiembre',
                                        '10' => 'Octubre',
                                        '11' => 'Noviembre',
                                        '12' => 'Diciembre'
                                    ];
                                    $mesNombre = $meses[$libro['mes']] ?? $libro['mes'];
                                    echo $mesNombre . ' ' . $libro['año'];
                                    ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Responsable</label>
                                <p class="text-lg text-gray-900"><?php echo htmlspecialchars($libro['responsable']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Creación</label>
                                <p class="text-lg text-gray-900"><?php echo date('d/m/Y', strtotime($libro['created_at'])); ?></p>
                            </div>
                        </div>

                        <?php if (!empty($libro['descripcion'])): ?>
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-500 mb-2">Descripción</label>
                                <p class="text-gray-900 leading-relaxed"><?php echo nl2br(htmlspecialchars($libro['descripcion'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Signatures Status -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                            Estado de Firmas
                        </h2>

                        <?php if (!empty($firmas)): ?>
                            <?php
                            $totalFirmas = count($firmas);
                            $firmasCompletadas = count(array_filter($firmas, function ($f) {
                                return $f['estado'] === 'firmado';
                            }));
                            $firmasPendientes = $totalFirmas - $firmasCompletadas;
                            $porcentaje = $totalFirmas > 0 ? ($firmasCompletadas / $totalFirmas) * 100 : 0;
                            ?>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-users text-2xl text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-blue-600">Total Firmantes</p>
                                            <p class="text-2xl font-bold text-blue-900"><?php echo $totalFirmas; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-green-600">Firmadas</p>
                                            <p class="text-2xl font-bold text-green-900"><?php echo $firmasCompletadas; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-red-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-clock text-2xl text-red-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-red-600">Pendientes</p>
                                            <p class="text-2xl font-bold text-red-900"><?php echo $firmasPendientes; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Progreso</span>
                                    <span><?php echo number_format($porcentaje, 1); ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-green-600 h-3 rounded-full transition-all duration-300"
                                        style="width: <?php echo $porcentaje; ?>%"></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-user-slash text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-500 text-lg">No hay firmantes asignados</p>
                                <p class="text-gray-400 text-sm mt-2">Asigne firmantes para comenzar el proceso de firmas</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Signatures List -->
                    <?php if (!empty($firmas)): ?>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    <i class="fas fa-list mr-2 text-blue-600"></i>
                                    Lista de Firmantes
                                </h2>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Firmante
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Departamento
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Estado
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Fecha de Firma
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($firmas as $firma): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                                <i class="fas fa-user text-blue-600"></i>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                <?php echo htmlspecialchars($firma['firmante_nombre']); ?>
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                <?php echo htmlspecialchars($firma['firmante_email']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm text-gray-900">
                                                        <?php echo htmlspecialchars($firma['departamento']); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php if ($firma['estado'] === 'firmado'): ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Firmado
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <i class="fas fa-clock mr-1"></i>
                                                            Pendiente
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?php if ($firma['fecha_firma']): ?>
                                                        <?php echo date('d/m/Y H:i', strtotime($firma['fecha_firma'])); ?>
                                                    <?php else: ?>
                                                        <span class="text-gray-400">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <?php if ($firma['estado'] === 'pendiente'): ?>
                                                        <form method="POST" action="index.php?controller=libro&action=updateEstadoFirma" class="inline">
                                                            <input type="hidden" name="firma_id" value="<?php echo $firma['id']; ?>">
                                                            <input type="hidden" name="nuevo_estado" value="firmado">
                                                            <input type="hidden" name="redirect" value="index.php?controller=libro&action=show&id=<?php echo $libro['id']; ?>">
                                                            <button type="submit"
                                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center"
                                                                onclick="return confirm('¿Confirmar la firma de <?php echo htmlspecialchars($firma['firmante_nombre']); ?>?')">
                                                                <i class="fas fa-pen mr-1"></i>
                                                                Firmar
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-green-600 text-sm">
                                                            <i class="fas fa-check mr-1"></i>
                                                            Completado
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Botón de debug FIJO que siempre está visible
<div class="fixed bottom-4 right-4 z-50">
    <button onclick="debugFirmantes()" 
            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow-lg">
        🐛 Debug Firmantes
    </button>
</div> -->

    <!-- Assign Signatories Modal -->
    <div id="assignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Asignar Firmantes</h3>
                </div>

                <form method="POST" action="index.php?controller=libro&action=asignarFirmantes" id="assignForm">
                    <input type="hidden" name="libro_id" id="form_libro_id" value="<?php echo $libro['id']; ?>">

                    <div class="px-6 py-4 max-h-96 overflow-y-auto" id="firmantesContainer">
                        <?php if (!empty($firmantes)): ?>
                            <?php foreach ($firmantes as $firmante): ?>
                                <div class="flex items-center mb-3">
                                    <input type="checkbox"
                                        name="firmantes[]"
                                        value="<?php echo $firmante['id']; ?>"
                                        id="firmante_<?php echo $firmante['id']; ?>"
                                        class="firmante-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="firmante_<?php echo $firmante['id']; ?>" class="ml-3 flex-1">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($firmante['nombre']); ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?php echo htmlspecialchars($firmante['departamento']); ?>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500 text-center py-4">No hay firmantes disponibles</p>
                        <?php endif; ?>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                        <button type="button"
                            onclick="toggleAssignModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition duration-200">
                            Cancelar
                        </button>
                        <button type="submit"
                            id="submitBtn"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition duration-200">
                            Asignar Seleccionados
                        </button>


                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Mejorado -->
    <script>
        // ENVÍO REAL CON VISUALIZACIÓN DE RESPUESTA
        document.addEventListener('DOMContentLoaded', function() {
            const assignForm = document.getElementById('assignForm');

            if (assignForm) {
                assignForm.addEventListener('submit', function(e) {
                    console.log('=== 🚀 ENVIANDO FORMULARIO ===');

                    // MOSTRAR LOADING
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Asignando...';
                        submitBtn.classList.add('opacity-50');
                    }

                    // NO prevenir el envío - queremos ver qué responde el servidor
                    console.log('✅ Formulario enviándose al servidor...');
                });
            }
        });

        // Función de debug
        window.debugFirmantes = function() {
            const form = document.getElementById('assignForm');
            const libroId = document.getElementById('form_libro_id')?.value;
            const checkedBoxes = document.querySelectorAll('input[name="firmantes[]"]:checked');

            console.log('📖 Libro ID:', libroId);
            console.log('✅ Firmantes seleccionados:', checkedBoxes.length);
            console.log('🔢 Valores:', Array.from(checkedBoxes).map(cb => cb.value));

            return false;
        }

        function toggleAssignModal() {
            const modal = document.getElementById('assignModal');
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }
    </script>
</body>

</html>