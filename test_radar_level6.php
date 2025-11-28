<?php

require __DIR__ . '/vendor/autoload.php';

use App\Services\NormasElPeruanoService;
use App\Services\AIService;
use App\Services\RadarAnalisisService;

$output = "";

try {
    // 1. Mockear o instanciar servicios
    $scraper = new NormasElPeruanoService();
    $ai = new AIService();
    $radar = new RadarAnalisisService($scraper, $ai);

    $output .= "Iniciando prueba de RadarAnalisisService (Nivel 6)...\n";

    // 2. Ejecutar proceso
    $normasFiltradas = $radar->procesarNormasDelDia();

    $output .= "Proceso completado.\n";
    $output .= "Normas relevantes encontradas: " . count($normasFiltradas) . "\n\n";

    if (count($normasFiltradas) > 0) {
        foreach ($normasFiltradas as $i => $norma) {
            $output .= "Norma #" . ($i + 1) . ":\n";
            $output .= "Título: " . $norma['titulo'] . "\n";
            $output .= "URL: " . ($norma['url_fuente'] ?? 'N/A') . "\n";
            $output .= "Obligación: " . $norma['obligacion_principal'] . "\n";
            $output .= "-----------------------------------\n";
        }
    } else {
        $output .= "No se encontraron normas relevantes después del filtrado.\n";
    }

} catch (\Exception $e) {
    $output .= "Error crítico: " . $e->getMessage() . "\n";
}

file_put_contents('test_level6_output.txt', $output);
echo "Test output written to test_level6_output.txt\n";
