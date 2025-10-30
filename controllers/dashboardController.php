<?php
require_once __DIR__ . '/../models/Libro.php';


class dashboardController
{
    private $libroModel;
    


    public function __construct($pdo)
    {
        $this->libroModel = new Libro($pdo);
    }

    public function index(){

        // Llamamos al modelo para obtener el total de diarios
        $stats = $this->libroModel->getTotalLibros();

        // Si la consulta devuelve resultado
        $totalDiarios = $stats['total_diarios'] ?? 0;
        $totalUsuarios = $stats['total_usuarios'] ?? 0;
        $totalFirmasCompletas = $stats['total_firmas_completas'] ?? 0;
        $totalFirmasPendientes = $stats['total_firmas_pendientes'] ?? 0;

        // Obtener libros recientes (últimos 5)
        $librosRecientes = $this->libroModel->obtenerRecientes(5);

        // Obtener actividad reciente (últimos 10 registros)
        $actividadReciente = $this->libroModel->obtenerActividadReciente(10);

        // Pasamos el dato a la vista
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
} 
