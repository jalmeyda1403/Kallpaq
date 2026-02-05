<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$renames = [
    "ALTER TABLE obligaciones CHANGE documento_tecnico_normativo obligacion_documento VARCHAR(255)",
    "ALTER TABLE obligaciones CHANGE consecuencia_incumplimiento obligacion_consecuencia TEXT",
    "ALTER TABLE obligaciones CHANGE documento_deroga obligacion_documento_deroga TEXT",
    "ALTER TABLE obligaciones CHANGE estado_obligacion obligacion_estado VARCHAR(255) DEFAULT 'pendiente'",
    "ALTER TABLE obligaciones CHANGE tipo_obligacion obligacion_tipo VARCHAR(255) DEFAULT 'Legal'",
    "ALTER TABLE obligaciones CHANGE frecuencia_revision obligacion_frecuencia INT"
];

foreach ($renames as $sql) {
    try {
        echo "Executing: $sql\n";
        DB::statement($sql);
        echo "Success!\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
