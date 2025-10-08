<?php
require_once __DIR__ . '/../../controllers/LibroController.php';
require_once __DIR__ . '/../../config/database.php';

// Verificar si el usuario está autenticado

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

$pageTitle = 'Crear Nuevo Libro';
$errors = $_SESSION['errors'] ?? [];
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['errors'], $_SESSION['form_data']);
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
                                <i class="fas fa-plus mr-2 text-blue-600"></i>
                                <?php echo $pageTitle; ?>
                            </h1>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    
                    <!-- Error Messages -->
                    <?php if (!empty($errors)): ?>
                        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 border border-red-300">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <strong>Se encontraron los siguientes errores:</strong>
                            </div>
                            <ul class="list-disc list-inside ml-4">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Form -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-book mr-2 text-blue-600"></i>
                                Información del Libro
                            </h2>
                        </div>
                        
                        <form method="POST" action="index.php?controller=libro&action=store" class="p-6" id="create-libro-form">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Número de Referencia -->
                                <div>
                                    <label for="numero_referencia" class="block text-sm font-medium text-gray-700 mb-2">
                                        Número de Referencia *
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="numero_referencia" 
                                               name="numero_referencia" 
                                               value="<?php echo htmlspecialchars($formData['numero_referencia'] ?? ''); ?>"
                                               required
                                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Ej: LIB-2024-001">
                                        <button type="button" id="generate-ref" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-blue-600 hover:text-blue-800 transition-colors" title="Generar automáticamente">
                                            <i class="fas fa-magic"></i>
                                        </button>
                                    </div>
                                    <small class="text-gray-500 mt-1 block">Se generará automáticamente si se deja vacío</small>
                                </div>
                                
                                <!-- Título del Libro -->
                                <div>
                                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                                        Título del Libro *
                                    </label>
                                    <input type="text" 
                                           id="titulo" 
                                           name="titulo" 
                                           value="<?php echo htmlspecialchars($formData['titulo'] ?? ''); ?>"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                           placeholder="Título descriptivo del libro">
                                    <div class="invalid-feedback text-red-600 text-sm mt-1 hidden">El título es requerido</div>
                                </div>
                                
                                <!-- Mes -->
                                <div>
                                    <label for="mes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Mes *
                                    </label>
                                    <select id="mes" 
                                            name="mes" 
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Seleccionar mes</option>
                                        <?php 
                                        $meses = [
                                            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                                            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                                            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                                        ];
                                        foreach ($meses as $num => $nombre): 
                                        ?>
                                            <option value="<?php echo $num; ?>" <?php echo ($formData['mes'] ?? '') === $num ? 'selected' : ''; ?>>
                                                <?php echo $nombre; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <!-- Año -->
                                <div>
                                    <label for="año" class="block text-sm font-medium text-gray-700 mb-2">
                                        Año
                                    </label>
                                    <select id="año" 
                                            name="año" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <?php 
                                        $currentYear = date('Y');
                                        $selectedYear = $formData['año'] ?? $currentYear;
                                        for ($year = $currentYear + 1; $year >= $currentYear - 5; $year--): 
                                        ?>
                                            <option value="<?php echo $year; ?>" <?php echo $selectedYear == $year ? 'selected' : ''; ?>>
                                                <?php echo $year; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <!-- Día -->
                                <div>
                                    <label for="dia" class="block text-sm font-medium text-gray-700 mb-2">
                                        Día
                                    </label>
                                    <select id="dia" 
                                            name="dia" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                         <?php 
                                         // Día actual
                                         $currentDay = date('j'); 
                                         // Día seleccionado si viene en $formData
                                         $selectedDay = $formData['dia'] ?? $currentDay;

                                        for ($day = 1; $day <= 31; $day++): 
                                        ?>
                                            <option value="<?php echo $day; ?>" <?php echo $selectedDay == $day ? 'selected' : ''; ?>>
                                                 <?php echo $day; ?>
                                             </option>
                                         <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <!-- Responsable -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-users mr-2 text-blue-600"></i>
                                        Asignar Firmantes
                                    </label>
                                    <div class="border border-gray-300 rounded-md bg-gray-50">
                                        <div class="px-4 py-3 border-b border-gray-200 bg-white">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-medium text-gray-900">Seleccionar firmantes</h4>
                                                <span class="text-xs text-gray-500" id="firmantes-seleccionados">0 seleccionados</span>
                                            </div>
                                        </div>
                                        <div class="px-4 py-3 max-h-48 overflow-y-auto" id="firmantesContainer">
                                            <?php if (!empty($firmantes)): ?>
                                                <?php foreach ($firmantes as $firmante): ?>
                                                    <div class="flex items-center mb-3">
                                                        <input type="checkbox" 
                                                               name="firmantes[]" 
                                                               value="<?php echo $firmante['id']; ?>"
                                                               id="firmante_<?php echo $firmante['id']; ?>"
                                                               class="firmante-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                        <label for="firmante_<?php echo $firmante['id']; ?>" class="ml-3 flex-1 cursor-pointer">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                <?php echo htmlspecialchars($firmante['nombre']); ?>
                                                            </div>
                                                            <div class="text-xs text-gray-500">
                                                                <?php echo htmlspecialchars($firmante['departamento'] ?? 'Sin departamento'); ?>
                                                                <?php if (!empty($firmante['cargo'])): ?>
                                                                    - <?php echo htmlspecialchars($firmante['cargo']); ?>
                                                                <?php endif; ?>
                                                            </div>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-gray-500 text-center py-4">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    No hay firmantes disponibles
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <small class="text-gray-500 mt-1 block">Selecciona los firmantes que participarán en este libro</small>
                                </div>
                            </div>
                            
                            <!-- Descripción -->
                            <div class="mt-6">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                    Descripción
                                </label>
                                <textarea id="descripcion" 
                                          name="descripcion" 
                                          rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-vertical"
                                          placeholder="Descripción detallada del libro (opcional)"><?php echo htmlspecialchars($formData['descripcion'] ?? ''); ?></textarea>
                                <div class="text-sm text-gray-500 mt-1">
                                    <span id="char-count">0</span> caracteres
                                </div>
                            </div>
                            
                            <!-- Buttons -->
                            <div class="mt-8 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-4">
                                <a href="index.php?controller=libro&action=index" 
                                   class="w-full sm:w-auto px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition duration-200 text-center">
                                    Cancelar
                                </a>
                                <button type="submit" id="submit-btn"
                                        class="w-full sm:w-auto px-6 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span class="btn-text">
                                        <i class="fas fa-save mr-2"></i>
                                        Crear Libro
                                    </span>
                                    <span class="btn-loading hidden">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Creando...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Help Section -->
                    <div class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                            <div>
                                <h3 class="text-sm font-medium text-blue-900 mb-2">Información importante</h3>
                                <ul class="text-sm text-blue-800 space-y-1">
                                    <li>• El número de referencia debe ser único en el sistema</li>
                                    <li>• Puede asignar firmantes directamente al crear el libro</li>
                                    <li>• Los firmantes seleccionados se crearán automáticamente en el sistema</li>
                                    <li>• Los campos marcados con (*) son obligatorios</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/firmas/'; ?>assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('create-libro-form');
            const generateRefBtn = document.getElementById('generate-ref');
            const numeroRefInput = document.getElementById('numero_referencia');
            const tituloInput = document.getElementById('titulo');
            const descripcionTextarea = document.getElementById('descripcion');
            const charCount = document.getElementById('char-count');
            const submitBtn = document.getElementById('submit-btn');
            const firmantesSeleccionados = document.getElementById('firmantes-seleccionados');
            const firmanteCheckboxes = document.querySelectorAll('.firmante-checkbox');
            
            // Auto-focus en el primer campo
            numeroRefInput.focus();
            
            // Función para actualizar contador de firmantes seleccionados
            function actualizarContadorFirmantes() {
                if (firmantesSeleccionados) {
                    const seleccionados = document.querySelectorAll('.firmante-checkbox:checked').length;
                    firmantesSeleccionados.textContent = seleccionados + ' seleccionados';
                }
            }
            
            // Agregar event listeners a los checkboxes de firmantes
            firmanteCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', actualizarContadorFirmantes);
            });
            
            // Inicializar contador
            actualizarContadorFirmantes();
            
            // Generar número de referencia automático
            if (generateRefBtn) {
                generateRefBtn.addEventListener('click', function() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const time = String(now.getHours()).padStart(2, '0') + String(now.getMinutes()).padStart(2, '0');
                    const ref = `LIB-${year}-${month}${day}-${time}`;
                    numeroRefInput.value = ref;
                    if (typeof App !== 'undefined' && App.showToast) {
                        App.showToast('Número de referencia generado: ' + ref, 'success');
                    }
                });
            }
            
            // Contador de caracteres para descripción
            if (descripcionTextarea && charCount) {
                // Inicializar contador
                charCount.textContent = descripcionTextarea.value.length;
                
                descripcionTextarea.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
            }
            
            // Validación en tiempo real del número de referencia
            numeroRefInput.addEventListener('input', function(e) {
                const value = e.target.value;
                const regex = /^[A-Z0-9-]+$/;
                
                if (value && !regex.test(value)) {
                    e.target.setCustomValidity('Solo se permiten letras mayúsculas, números y guiones');
                } else {
                    e.target.setCustomValidity('');
                }
            });
            
            // Validación en tiempo real para título
            if (tituloInput) {
                tituloInput.addEventListener('blur', function() {
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (!this.value.trim()) {
                        this.classList.add('border-red-500');
                        if (feedback) feedback.classList.remove('hidden');
                    } else {
                        this.classList.remove('border-red-500');
                        if (feedback) feedback.classList.add('hidden');
                    }
                });
            }
            
            // Manejo del formulario
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Validar campos requeridos
                    let isValid = true;
                    
                    if (tituloInput && !tituloInput.value.trim()) {
                        tituloInput.classList.add('border-red-500');
                        const feedback = tituloInput.parentNode.querySelector('.invalid-feedback');
                        if (feedback) feedback.classList.remove('hidden');
                        isValid = false;
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                        if (typeof App !== 'undefined' && App.showToast) {
                            App.showToast('Por favor, corrige los errores en el formulario', 'error');
                        }
                        return;
                    }
                    
                    // Mostrar estado de carga
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        const btnText = submitBtn.querySelector('.btn-text');
                        const btnLoading = submitBtn.querySelector('.btn-loading');
                        if (btnText) btnText.classList.add('hidden');
                        if (btnLoading) btnLoading.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
</body>
</html>