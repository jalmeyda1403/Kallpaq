<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Control;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jsonFile = 'legacy_controls.json';

if (!file_exists($jsonFile)) {
    echo "No backup file found at $jsonFile\n";
    exit;
}

$data = json_decode(file_get_contents($jsonFile), true);
if (empty($data)) {
    echo "Backup is empty or invalid.\n";
    exit;
}

echo "Restoring " . count($data) . " controls...\n";

foreach ($data as $row) {
    // 1. Create or Find Control (Master)
    // De-duplicate based on description or code? 
    // OLD: control_cod (e.g. "C-01") might be unique.

    // Simplest migration: One Master Control per Legacy Row (1:1), but allows future relinking.
    // Or we could try to deduplicate if desc is identical.

    // Let's create a new Control for each row to be safe/conservative, 
    // but without the riesgod_cod dependency.

    $control = Control::create([
        'nombre' => $row['control_cod'] ?? 'C-LEGACY', // Map old cod to nombre
        'descripcion' => $row['descripcion'] ?? 'Sin descripción',
        'tipo' => $row['tipo'] ?? 'Preventivo',
        'frecuencia' => $row['frecuencia'] ?? 'Eventual',
        'responsable' => $row['responsable'] ?? '',
        'fecha_implementacion' => $row['fecha_implementacion'] ?? null,
        'estado' => 'Activo'
    ]);

    // 2. Link to Risk (Pivot)
    if (!empty($row['riesgo_cod'])) {
        // riesgo_cod is the ID of the risk, confusingly named in old DB.
        DB::table('control_riesgo')->insert([
            'riesgo_id' => $row['riesgo_cod'],
            'control_id' => $control->id,
            'eficacia' => $row['evaluación'] ?? null, // Map accent? 
            'fecha_ultima_evaluacion' => $row['fecha_evaluacion'] ?? null,
            'fecha_revaluacion' => $row['fecha_revaluacion'] ?? null,
            'observaciones' => $row['observaciones'] ?? null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

echo "Restoration Complete.\n";
