<?php
// scripts/check_inventario_schema.php
require_once __DIR__ . '/../vendor/autoload.php'; // Ajustar la ruta si es necesario

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Asumiendo que Laravel está inicializado, esto puede fallar si no se corre con artisan tinker o desde una petición web
try {
    $columns = Schema::getColumnListing('inventarios');
    echo "Columnas en la tabla 'inventarios':\n";
    foreach ($columns as $column) {
        echo "- " . $column . "\n";
    }
} catch (\Exception $e) {
    echo "Error al obtener columnas: " . $e->getMessage() . "\n";
}