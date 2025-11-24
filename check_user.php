<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(1);
if (!$user) {
    echo "User 1 not found.\n";
    exit;
}

echo "User: " . $user->name . "\n";
echo "Roles: " . $user->getRoleNames()->implode(', ') . "\n";

$ouoIds = $user->ouos->pluck('id');
echo "OUOs: " . $user->ouos->pluck('ouo_nombre')->implode(', ') . " (IDs: " . $ouoIds->implode(', ') . ")\n";

$count = App\Models\Hallazgo::whereHas('procesos', function($q) use ($ouoIds) {
    $q->whereHas('ouos', function($q2) use ($ouoIds) {
        $q2->whereIn('ouos.id', $ouoIds);
    });
})->count();

echo "Hallazgos Count for User 1: " . $count . "\n";

$allCount = App\Models\Hallazgo::count();
echo "Total Hallazgos in System: " . $allCount . "\n";
