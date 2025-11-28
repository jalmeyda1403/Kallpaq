<?php

use App\Models\User;
use App\Models\OUO;
use App\Models\Proceso;
use App\Models\Obligacion;
use Illuminate\Support\Facades\Auth;

// 1. Pick a user (or create a dummy one)
$user = User::first();
if (!$user) {
    echo "No users found.\n";
    exit;
}

echo "Testing with User: " . $user->name . " (ID: " . $user->id . ")\n";

// 2. Simulate login
Auth::login($user);

// 3. Get User's OUOs
$ouoIds = $user->ouos->pluck('id');
echo "User's OUO IDs: " . $ouoIds->implode(', ') . "\n";

// 4. Get Processes for those OUOs
$procesoIds = OUO::whereIn('id', $ouoIds)
    ->with('procesos')
    ->get()
    ->pluck('procesos')
    ->flatten()
    ->pluck('id')
    ->unique();

echo "Associated Process IDs: " . $procesoIds->implode(', ') . "\n";

// 5. Get Obligations for those Processes
$obligaciones = Obligacion::whereIn('proceso_id', $procesoIds)->get();

echo "Found " . $obligaciones->count() . " obligations.\n";

foreach ($obligaciones as $obs) {
    echo "- Obligacion ID: " . $obs->id . " (Proceso ID: " . $obs->proceso_id . ")\n";
}

echo "\nVerification Complete.\n";
