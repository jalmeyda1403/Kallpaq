<?php
use App\Models\User;
use App\Models\Hallazgo;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'jalmeyda@contraloria.gob.pe')->first();
$userOuos = $user->ouos->pluck('id')->toArray();

echo "Usuario: {$user->email}\n";
echo "OUOs: " . implode(', ', $userOuos) . "\n\n";

// Simular el filtro del controlador
$hallazgosQuery = Hallazgo::with(['procesos.ouos', 'acciones']);

$hallazgosQuery->whereHas('procesos', function ($query) use ($userOuos) {
    $query->whereHas('ouos', function ($q) use ($userOuos) {
        $q->whereIn('ouos.id', $userOuos)
          ->where('procesos_ouo.propietario', 1);
    });
});

$hallazgos = $hallazgosQuery->get();

echo "Total de hallazgos filtrados: " . $hallazgos->count() . "\n\n";

foreach ($hallazgos as $hallazgo) {
    echo "Hallazgo ID: {$hallazgo->id} - {$hallazgo->hallazgo_descripcion}\n";
    echo "Procesos asociados a este hallazgo:\n";
    foreach ($hallazgo->procesos as $proceso) {
        echo "  - {$proceso->proceso_nombre} (ID: {$proceso->id})\n";
        echo "    OUOs de este proceso:\n";
        foreach ($proceso->ouos as $ouo) {
            echo "      * {$ouo->ouo_nombre} (ID: {$ouo->id}) - Propietario: {$ouo->pivot->propietario}\n";
        }
    }
    echo "\n";
}
