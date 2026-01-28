<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Starting Schema Fix...\n";

if (!Schema::hasTable('subareas_compliance')) {
    echo "ERROR: Table 'subareas_compliance' does not exist. Cannot create foreign key.\n";
    exit(1);
}

if (Schema::hasColumn('obligaciones', 'subarea_compliance_id')) {
    echo "INFO: Column 'subarea_compliance_id' already exists in 'obligaciones'.\n";
    exit(0);
}

echo "Attempting to add column 'subarea_compliance_id' to 'obligaciones'...\n";

try {
    Schema::table('obligaciones', function (Blueprint $table) {
        $table->unsignedBigInteger('subarea_compliance_id')->nullable()->after('area_compliance_id');
        // We add FK in a separate step to isolate errors
    });
    echo "SUCCESS: Column added.\n";

    Schema::table('obligaciones', function (Blueprint $table) {
        $table->foreign('subarea_compliance_id')->references('id')->on('subareas_compliance')->onDelete('set null');
    });
    echo "SUCCESS: Foreign key added.\n";

} catch (\Exception $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
