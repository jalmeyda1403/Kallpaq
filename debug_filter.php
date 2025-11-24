<?php
use App\Models\User;
use App\Models\Hallazgo;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'jalmeyda@contraloria.gob.pe')->first();
$userOuos = $user->ouos->pluck('id')->toArray();

$hallazgosQuery = Hallazgo::with(['procesos.ouos', 'acciones']);
$hallazgosQuery->whereHas('procesos', function ($query) use ($userOuos) {
    $query->whereHas('ouos', function ($q) use ($userOuos) {
        $q->whereIn('ouos.id', $userOuos)
          ->where('procesos_ouo.propietario', 1);
    });
});

$hallazgos = $hallazgosQuery->get();
$procesos = $hallazgos->pluck('procesos')->flatten()->unique('id');

echo "Procesos ANTES del filtro:\n";
foreach ($procesos as $proceso) {
    echo "- {$proceso->proceso_nombre} (ID: {$proceso->id})\n";
    echo "  OUOs cargadas: " . $proceso->ouos->count() . "\n";
    foreach ($proceso->ouos as $ouo) {
        echo "    * OUO {$ouo->id}: {$ouo->ouo_nombre} - Propietario: " . ($ouo->pivot->propietario ?? 'NULL') . "\n";
    }
}

echo "\n\nAplicando filtro...\n\n";

$procesos = $procesos->filter(function ($proceso) use ($userOuos) {
    $result = $proceso->ouos->contains(function ($ouo) use ($userOuos) {
        return in_array($ouo->id, $userOuos) && $ouo->pivot->propietario == 1;
    });
    echo "Proceso '{$proceso->proceso_nombre}': " . ($result ? 'PASA' : 'NO PASA') . "\n";
    return $result;
});

echo "\n\nProcesos DESPUÉS del filtro:\n";
foreach ($procesos as $proceso) {
    echo "- {$proceso->proceso_nombre} (ID: {$proceso->id})\n";
}
