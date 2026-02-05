<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing requirement loading...\n\n";

// Test loading requirement with norm
$req = \App\Models\NormaRequisito::with('norma')->find(2);

if ($req) {
    echo "Requirement ID: " . $req->nr_id . "\n";
    echo "Numeral: " . $req->nr_numeral . "\n";
    echo "Denominacion: " . substr($req->nr_denominacion ?? 'NULL', 0, 50) . "\n";
    echo "Detalle: " . substr($req->nr_detalle ?? 'NULL', 0, 100) . "...\n";

    if ($req->norma) {
        echo "\nNorma Info:\n";
        echo "  ID: " . $req->norma->id . "\n";
        echo "  Nombre: " . ($req->norma->nombre ?? 'NULL') . "\n";
        echo "  Nombre Norma: " . ($req->norma->nombre_norma ?? 'FIELD DOES NOT EXIST') . "\n";
    } else {
        echo "\nNorma: NULL (relationship failed)\n";
    }
} else {
    echo "Requirement not found\n";
}
