<?php
require_once __DIR__ . '/../../controllers/LibroController.php';
require_once __DIR__ . '/../../config/database.php';

// Verificar si el usuario está autenticado

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

$pageTitle = 'Gestión de Libros';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Sistema de Firmas</title>
    <link href="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/firmas/'; ?>assets/css/main.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- DataTables CSS (DT 2 + Buttons) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.dataTables.min.css">
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
                        <h1 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-book mr-2 text-blue-600"></i>
                            <?php echo $pageTitle; ?>
                        </h1>
                        <a href="index.php?controller=libro&action=create" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Nuevo Libro
                        </a>
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
                    
                    <!-- Filters -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                        <form method="GET" class="flex flex-wrap gap-4 items-end">
                            <input type="hidden" name="controller" value="libro">
                            <input type="hidden" name="action" value="index">
                            
                            <div class="flex-1 min-w-48">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                                <input type="text" name="search" value="<?php echo $_GET['search'] ?? ''; ?>" 
                                       placeholder="Nombre o número de referencia" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
                                <select name="mes" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Todos</option>
                                    <?php 
                                    $meses = [
                                        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                                        '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                                        '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                                    ];
                                    foreach ($meses as $num => $nombre): 
                                    ?>
                                        <option value="<?php echo $num; ?>" <?php echo ($_GET['mes'] ?? '') === $num ? 'selected' : ''; ?>>
                                            <?php echo $nombre; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                                <select name="anio" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Todos</option>
                                    <?php 
                                    $currentYear = date('Y');
                                    for ($year = $currentYear; $year >= $currentYear - 5; $year--): 
                                    ?>
                                        <option value="<?php echo $year; ?>" <?php echo ($_GET['anio'] ?? '') == $year ? 'selected' : ''; ?>>
                                            <?php echo $year; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div>
                                <button id="btn-clear-filters" type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition duration-200">
                                    <i class="fas fa-eraser mr-2"></i>Limpiar
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Books Table -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table id="libros-table" class="min-w-full divide-y divide-gray-200 table-auto">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Libro
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Número
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estado de Firmas
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Responsables
                                        </th>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (!empty($libros)): ?>
                                        <?php foreach ($libros as $libro): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <?php echo htmlspecialchars($libro['titulo']); ?>
                                                        </div>
                                                        <?php if (!empty($libro['descripcion'])): ?>
                                                            <div class="text-sm text-gray-500 mt-1">
                                                                <?php echo htmlspecialchars(substr($libro['descripcion'], 0, 100)); ?>
                                                                <?php echo strlen($libro['descripcion']) > 100 ? '...' : ''; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm font-mono text-gray-900">
                                                        <?php echo htmlspecialchars($libro['numero_referencia']); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        <?php 
                                                        $mesNombre = $meses[$libro['mes']] ?? $libro['mes'];
                                                        echo $mesNombre . ' ' . $libro['año']; 
                                                        ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex flex-col space-y-1">
                                                        <?php if ($libro['total_firmas'] > 0): ?>
                                                            <div class="flex items-center text-sm">
                                                                <span class="text-green-600 font-medium">
                                                                    <?php echo $libro['firmas_completadas']; ?> firmadas
                                                                </span>
                                                            </div>
                                                            <?php if ($libro['firmas_pendientes'] > 0): ?>
                                                                <div class="flex items-center text-sm">
                                                                    <span class="text-red-600 font-medium">
                                                                        <?php echo $libro['firmas_pendientes']; ?> pendientes
                                                                    </span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                                <?php 
                                                                $porcentaje = $libro['total_firmas'] > 0 ? ($libro['firmas_completadas'] / $libro['total_firmas']) * 100 : 0;
                                                                ?>
                                                                <div class="bg-green-600 h-2 rounded-full" 
                                                                     style="width: <?php echo $porcentaje; ?>%"></div>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-gray-500 text-sm">Sin firmantes asignados</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-700">
    <?php if (!empty($libro['firmantes'])): ?>
        <?= htmlspecialchars($libro['firmantes']) ?>
    <?php else: ?>
        <span class="text-gray-400 italic">Sin firmantes</span>
    <?php endif; ?>
</td>

                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex space-x-2">
                                                        <a href="index.php?controller=libro&action=show&id=<?php echo $libro['id']; ?>" class="inline-flex items-center justify-center w-8 h-8 md:w-auto md:px-3 md:py-1 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-md transition-colors mr-1" title="Ver detalles" aria-label="Ver detalles del libro">
                                            <i class="fas fa-eye"></i>
                                            <span class="hidden md:inline ml-1">Ver</span>
                                        </a>
                                        <a href="index.php?controller=libro&action=edit&id=<?php echo $libro['id']; ?>" class="inline-flex items-center justify-center w-8 h-8 md:w-auto md:px-3 md:py-1 text-yellow-600 hover:text-yellow-900 hover:bg-yellow-50 rounded-md transition-colors mr-1" title="Editar" aria-label="Editar libro">
                                            <i class="fas fa-edit"></i>
                                            <span class="hidden md:inline ml-1">Editar</span>
                                        </a>
                                        <a href="index.php?controller=libro&action=delete&id=<?php echo $libro['id']; ?>" class="inline-flex items-center justify-center w-8 h-8 md:w-auto md:px-3 md:py-1 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-md transition-colors" title="Eliminar" aria-label="Eliminar libro" onclick="return confirm('¿Está seguro de que desea eliminar este libro?')">
                                            <i class="fas fa-trash"></i>
                                            <span class="hidden md:inline ml-1">Eliminar</span>
                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="text-gray-500">
                                                    <i class="fas fa-book text-4xl mb-4"></i>
                                                    <p class="text-lg">No hay libros registrados</p>
                                                    <p class="text-sm mt-2">Comience creando su primer libro</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- JavaScript -->
    <!-- jQuery + DataTables (jQuery integration) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/firmas/'; ?>assets/js/app.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const tableEl = document.querySelector('#libros-table');
    if (!tableEl) return;

    console.log('Inicializando DataTables sobre #libros-table');
    const dt = $('#libros-table').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/2.0.7/i18n/es-ES.json' },
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        // Ocultamos el buscador nativo de DataTables y usamos el existente
        dom: 'Btip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel mr-2"></i>Exportar Excel',
                titleAttr: 'Exportar a Excel',
                exportOptions: { columns: [0, 1, 2, 3] }
            }
        ]
    });

    // Estilizar botón con Tailwind tras render
    const styleButtons = () => {
        document.querySelectorAll('.dt-button, .buttons-html5').forEach(btn => {
            btn.classList.add('bg-green-600', 'hover:bg-green-700', 'text-white', 'px-3', 'py-2', 'rounded-md', 'transition-colors', 'text-sm');
        });
        const wrapper = document.querySelector('.dataTables_wrapper');
        if (wrapper) {
            // Info y paginación estilizadas
            const info = wrapper.querySelector('.dataTables_info');
            const paginate = wrapper.querySelector('.dataTables_paginate');
            if (info) info.classList.add('text-sm', 'text-gray-600', 'py-3');
            if (paginate) paginate.classList.add('flex', 'items-center', 'gap-1', 'py-2');
        }
    };
    styleButtons();
    dt.on('draw', styleButtons);

    // Integrar buscador existente con DataTables
    const form = document.querySelector('form[action=""][method="GET"], .bg-white form');
    const searchInput = document.querySelector('input[name="search"]');
    const mesSelect = document.querySelector('select[name="mes"]');
    const anioSelect = document.querySelector('select[name="anio"]');

    const monthMap = { '01':'Enero','02':'Febrero','03':'Marzo','04':'Abril','05':'Mayo','06':'Junio','07':'Julio','08':'Agosto','09':'Septiembre','10':'Octubre','11':'Noviembre','12':'Diciembre' };

    function applyFechaFilter() {
        const mesVal = mesSelect ? mesSelect.value : '';
        const anioVal = anioSelect ? anioSelect.value : '';
        if (!mesVal && !anioVal) {
            dt.column(2).search('', true, false); // limpiar
            return;
        }
        const parts = [];
        if (mesVal && monthMap[mesVal]) parts.push(monthMap[mesVal]);
        if (anioVal) parts.push(anioVal);
        const regex = parts.length ? parts.map(p => `(?=.*${p})`).join('') + '.*' : '';
        dt.column(2).search(regex, true, false); // regex, smart=false
    }

    if (searchInput) {
        const handler = () => { dt.search(searchInput.value).draw(); };
        searchInput.addEventListener('input', handler);
        // Aplicar valor inicial si viene por GET
        if (searchInput.value) dt.search(searchInput.value);
    }
    if (mesSelect) mesSelect.addEventListener('change', () => { applyFechaFilter(); dt.draw(); });
    if (anioSelect) anioSelect.addEventListener('change', () => { applyFechaFilter(); dt.draw(); });

    // Evitar submit del formulario para filtrar en cliente
    const filtersForm = document.querySelector('.bg-white.rounded-lg form');
    if (filtersForm) {
        filtersForm.addEventListener('submit', function(e) {
            e.preventDefault();
            applyFechaFilter();
            if (searchInput) dt.search(searchInput.value);
            dt.draw();
        });
    }

    // Botón limpiar filtros
    const clearBtn = document.getElementById('btn-clear-filters');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                dt.search('');
            }
            if (mesSelect) mesSelect.value = '';
            if (anioSelect) anioSelect.value = '';
            dt.columns().every(function(idx) {
                dt.column(idx).search('');
            });
            applyFechaFilter(); // limpiará columna fecha
            dt.draw();
        });
    }

    // Aplicar filtros iniciales de selects (si los hay por GET)
    applyFechaFilter();
    dt.draw();
});
</script>

</body>
</html>