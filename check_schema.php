<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Checking tables...\n";
echo 'areas_compliance: ' . (Schema::hasTable('areas_compliance') ? 'YES' : 'NO') . PHP_EOL;
echo 'subareas_compliance: ' . (Schema::hasTable('subareas_compliance') ? 'YES' : 'NO') . PHP_EOL;
echo 'obligaciones: ' . (Schema::hasTable('obligaciones') ? 'YES' : 'NO') . PHP_EOL;

if (Schema::hasTable('obligaciones')) {
    echo "Checking columns in obligaciones...\n";
    echo 'subarea_compliance_id: ' . (Schema::hasColumn('obligaciones', 'subarea_compliance_id') ? 'YES' : 'NO') . PHP_EOL;
}
