<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Use App\Http\Controllers\DocumentoController;
Use App\Http\Controllers\RequerimientoController;
Use App\Http\Controllers\HallazgoController;
use App\Http\Controllers\UserController; // Added UserController
use App\Http\Controllers\OUOController; // Added OUOController

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
   
    // OUO-User Management Routes
    Route::get('ouos/{ouo}/users', [UserController::class, 'listOuoUsers'])->name('api.ouos.users.list');
    Route::post('ouos/{ouo}/users', [UserController::class, 'attachOuoUser'])->name('api.ouos.users.attach');
    Route::delete('ouos/{ouo}/users/{user}', [UserController::class, 'detachOuoUser'])->name('api.ouos.users.detach');

    // New route for OUOs with details
    Route::get('ouos-with-details', [OUOController::class, 'indexWithDetails'])->name('api.ouos.indexWithDetails');

    // New route for listing all users
    Route::get('users/list', [UserController::class, 'listUsers'])->name('api.users.list');
});
