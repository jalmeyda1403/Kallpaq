<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'jalmeyda@contraloria.gob.pe';
$u = \App\Models\User::where('email', $email)->first();
if ($u) {
    $u->syncPermissions([]); // Limpia permisos directos
    echo "Permisos directos eliminados para {$email}\n";
} else {
    echo "Usuario no encontrado.\n";
}
