<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "Iniciando limpieza masiva de permisos directos...\n";

$users = User::all();
$total = count($users);
$cleaned = 0;

foreach ($users as $index => $u) {
    // syncPermissions([]) elimina todos los permisos asignados directamente al usuario
    // pero mantiene los que vienen a través de sus roles.
    $u->syncPermissions([]);
    $cleaned++;

    if ($cleaned % 50 == 0) {
        echo "Procesados $cleaned de $total usuarios...\n";
    }
}

echo "Limpieza completada con éxito. Se limpiaron $cleaned usuarios.\n";
echo "A partir de ahora, todos los accesos dependen estrictamente de los Roles asignados.\n";
