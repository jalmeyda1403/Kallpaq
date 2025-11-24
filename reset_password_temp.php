<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'jalmeyda@contraloria.gob.pe')->first();
if ($user) {
    $user->password = Hash::make('password');
    $user->save();
    echo "Password updated for " . $user->email . "\n";
    
    // Check roles
    $roles = $user->getRoleNames();
    echo "Roles: " . $roles->implode(', ') . "\n";
    
    // Check OUOs
    echo "OUOs: " . $user->ouos->pluck('id')->implode(', ') . "\n";
} else {
    echo "User not found\n";
}
