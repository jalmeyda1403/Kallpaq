<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Export Data
$data = [];
if (Schema::hasTable('riesgo_controles')) {
    $data = DB::table('riesgo_controles')->get()->toArray();
    file_put_contents('legacy_controls.json', json_encode($data, JSON_PRETTY_PRINT));
    echo "Exported " . count($data) . " rows to legacy_controls.json\n";
} else {
    echo "Table risk_controls not found.\n";
}
