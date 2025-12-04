<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$output = "";
$tables = ['indicadores', 'indicadores_historico', 'indicadores_seguimiento'];
foreach ($tables as $table) {
    $output .= "TABLE: $table\n";
    $output .= implode(',', Schema::getColumnListing($table)) . "\n\n";
}
file_put_contents('db_columns.txt', $output);
