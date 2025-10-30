<?php
require_once __DIR__ . '/../../config/database.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?controller=auth&action=login');
    exit;
}

$pageTitle = 'Editar Libro';
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
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="index.php?controller=libro&action=index" class="text-gray-600 hover:text-gray-800 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Volver a Libros
                        </a>
                        <h1 class="text-2xl font-bold text-gray-800"><?php echo $pageTitle; ?></h1>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

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

                    <!-- Edit Form -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-edit mr-2 text-blue-600"></i>
                                Editar Información del Libro
                            </h2>
                        </div>

                        <form method="POST" action="index.php?controller=libro&action=update" class="p-6">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($libro['id']); ?>">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Número de Referencia -->
                                <div>
                                    <label for="numero_referencia" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-hashtag mr-1 text-gray-500"></i>
                                        Número de Referencia *
                                    </label>
                                    <input type="text"
                                        id="numero_referencia"
                                        name="numero_referencia"
                                        value="<?php echo htmlspecialchars($libro['numero_referencia']); ?>"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Ej: REF001">
                                </div>

                                <!-- Título -->
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-book mr-1 text-gray-500"></i>
                                        Título del Libro *
                                    </label>
                                    <input type="text"
                                        id="nombre"
                                        name="nombre"
                                        value="<?php echo htmlspecialchars($libro['titulo']); ?>"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Nombre del libro">
                                </div>

                                <!-- Mes -->
                                <div>
                                    <label for="mes" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-calendar-alt mr-1 text-gray-500"></i>
                                        Mes *
                                    </label>
                                    <select id="mes"
                                        name="mes"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Seleccionar mes</option>
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
                                        foreach ($meses as $num => $nombre):
                                        ?>
                                            <option value="<?php echo $num; ?>" <?php echo $libro['mes'] === $num ? 'selected' : ''; ?>>
                                                <?php echo $nombre; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Año -->
                                <div>
                                    <label for="anio" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-calendar mr-1 text-gray-500"></i>
                                        Año *
                                    </label>
                                    <select id="anio"
                                        name="anio"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Seleccionar año</option>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($year = $currentYear; $year >= $currentYear - 10; $year--):
                                        ?>
                                            <option value="<?php echo $year; ?>" <?php echo $libro['año'] == $year ? 'selected' : ''; ?>>
                                                <?php echo $year; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="mt-6">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-align-left mr-1 text-gray-500"></i>
                                    Descripción
                                </label>
                                <textarea id="descripcion"
                                    name="descripcion"
                                    rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Descripción opcional del libro"><?php echo htmlspecialchars($libro['descripcion'] ?? ''); ?></textarea>
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                                <a href="index.php?controller=libro&action=show&id=<?php echo $libro['id']; ?>"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200">
                                    <i class="fas fa-times mr-2"></i>Cancelar
                                </a>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/firmas/'; ?>assets/js/app.js"></script>
</body>

</html>