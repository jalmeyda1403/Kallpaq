<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalidaNoConformeController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auditoria/especifica')->group(function () {
        // ... existing routes ...
        Route::put('/{id}/agenda/cancelar', [App\Http\Controllers\AuditoriaEspecificaController::class, 'cancelarActividad']);
    });

    // Salidas No Conformes
    Route::prefix('salidas-nc')->name('api.salidas-nc.')->group(function () {
        Route::get('/', [SalidaNoConformeController::class, 'index'])->name('index');
        Route::post('/', [SalidaNoConformeController::class, 'store'])->name('store');
        Route::get('/{id}', [SalidaNoConformeController::class, 'show'])->name('show');
        Route::match(['put', 'patch'], '/{id}', [SalidaNoConformeController::class, 'update'])->name('update');
        Route::delete('/{id}', [SalidaNoConformeController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/validate', [SalidaNoConformeController::class, 'validateSNC'])->name('validate');
    });

    // Usuarios (Admin)
    Route::get('/admin/usuarios', [UserController::class, 'apiIndex'])->name('api.admin.usuarios.index');
});
