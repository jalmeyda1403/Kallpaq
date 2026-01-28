<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Obligacion;

// Simulate the query from apiIndex
$obligacion = Obligacion::with('procesos', 'area_compliance', 'subarea_compliance', 'documento', 'radar')->latest()->first();

if ($obligacion) {
    echo "ID: " . $obligacion->id . "\n";
    echo "Area ID: " . $obligacion->area_compliance_id . "\n";
    echo "SubArea ID: " . $obligacion->subarea_compliance_id . "\n";
    echo "Area Relation: " . ($obligacion->area_compliance ? 'Loaded (' . $obligacion->area_compliance->area_compliance_nombre . ')' : 'NULL') . "\n";
    echo "SubArea Relation: " . ($obligacion->subarea_compliance ? 'Loaded (' . $obligacion->subarea_compliance->subarea_compliance_nombre . ')' : 'NULL') . "\n";

    echo "\nFull JSON:\n";
    echo json_encode($obligacion, JSON_PRETTY_PRINT);
} else {
    echo "No obligaciones found.\n";
}
