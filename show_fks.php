<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$table = 'riesgo_controles';
$dbName = DB::connection()->getDatabaseName();

$fks = DB::select("
    SELECT Constraint_Name AS FK_Name 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = ? 
    AND TABLE_NAME = ? 
    AND REFERENCED_TABLE_NAME IS NOT NULL
", [$dbName, $table]);

$output = "Foreign Keys on table '$table':\n";
foreach ($fks as $fk) {
    $output .= "- " . $fk->FK_Name . "\n";
}
file_put_contents('fks.txt', $output);
echo "Saved to fks.txt";
