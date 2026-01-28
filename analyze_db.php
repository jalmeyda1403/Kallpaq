<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function describeTable($tableName)
{
    if (Schema::hasTable($tableName)) {
        echo "\nTable: $tableName\n";
        $columns = Schema::getColumnListing($tableName);
        foreach ($columns as $column) {
            $type = Schema::getColumnType($tableName, $column);
            echo "- $column ($type)\n";
        }
    } else {
        echo "\nTable '$tableName' does not exist.\n";
    }
}

ob_start();

echo "--- Tables related to 'control' ---\n";
$tables = DB::select('SHOW TABLES');
// The key for the table name can vary, let's grab the first value of the object
foreach ($tables as $table) {
    $tableName = array_values((array) $table)[0];
    if (strpos($tableName, 'control') !== false) {
        echo "- $tableName\n";
    }
}

echo "\n--- Structure of 'riesgos' ---\n";
describeTable('riesgos');

echo "\n--- Structure of 'controles' ---\n";
describeTable('controles');

echo "\n--- Structure of 'riesgo_controles' ---\n";
describeTable('riesgo_controles');

echo "\n--- Structure of 'riesgos_controles' ---\n";
describeTable('riesgos_controles');

$output = ob_get_clean();
file_put_contents('db_analysis.txt', $output);
echo "Analysis saved to db_analysis.txt\n";
