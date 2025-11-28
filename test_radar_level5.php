<?php

require __DIR__ . '/vendor/autoload.php';

$output = "";
try {
    $service = new \App\Services\NormasElPeruanoService();
    $normas = $service->fetchNormasDelDia();

    $output .= "Normas encontradas: " . count($normas) . "\n";

    if (count($normas) > 0) {
        $output .= "Primera norma:\n";
        $output .= print_r($normas[0], true);
    } else {
        $output .= "No se encontraron normas.\n";
    }

} catch (\Exception $e) {
    $output .= "Error: " . $e->getMessage() . "\n";
}

file_put_contents('test_level5_output.txt', $output);
echo "Test output written to test_level5_output.txt\n";
