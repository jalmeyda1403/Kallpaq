<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hallazgo = App\Models\Hallazgo::find(55);
if ($hallazgo) {
    $causa = $hallazgo->causa()->first();
    if ($causa) {
        echo "Causa encontrada:\n";
        echo "ID: " . $causa->id . "\n";
        echo "Método: " . $causa->causa_metodo . "\n";
        echo "Resultado: " . ($causa->causa_resultado ?? 'NULL') . "\n";
        echo "\nJSON completo:\n";
        echo json_encode($causa, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "No se encontró causa para el hallazgo 55\n";
    }
} else {
    echo "Hallazgo 55 no encontrado\n";
}
