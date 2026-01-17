<?php
ob_start();
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all();
foreach ($users as $u) {
    echo "User: {$u->email}\n";
    echo "Roles: " . $u->getRoleNames()->implode(', ') . "\n";
    foreach ($u->roles as $role) {
        echo "  Role [{$role->name}] Perms: " . $role->permissions->pluck('name')->implode(', ') . "\n";
    }
    echo "Direct Perms: " . $u->getDirectPermissions()->pluck('name')->implode(', ') . "\n";
    echo "Consolidated Perms: " . $u->getAllPermissions()->pluck('name')->implode(', ') . "\n";
    echo "-------------------\n";
}
if ($users->isEmpty()) {
    echo "No users with role 'admin' found.\n";
}
$output = ob_get_clean();
echo $output;
file_put_contents('debug_perms_utf8.txt', $output);
