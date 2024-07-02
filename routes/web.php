<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcesoController;
use App\Http\Controllers\IndicadorController;
use App\Http\Controllers\PlanificacionSIGController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequerimientoController;
use App\Http\Controllers\RequerimientoNecesidadController;
use App\Http\Controllers\ProgramaAuditoriaController;
use App\Http\Controllers\HallazgoController;
use App\Http\Controllers\AccionController;
use App\Http\Controllers\CausaController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});
Auth::routes();
Route::get('home', [HomeController::class, 'index'])->name('home');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');


Route::resource('procesos', ProcesoController::class);
Route::resource('indicadores', IndicadorController::class);
Route::resource('programa', ProgramaAuditoriaController::class);
Route::resource('smp', HallazgoController::class);
Route::resource('acciones', AccionController::class);

Route::get('/indicadores/{proceso_id?}/listar',  [IndicadorController::class, 'listarIndicadores'])->name('indicadores.listar');;
Route::get('/indicadores/{id}/historico-datos',  [IndicadorController::class, 'showHistorico']);
Route::get('/indicadores/{id}/datos',  [IndicadorController::class, 'showDatos']);
Route::get('indicadores/{id}/frecuencia', [IndicadorController::class, 'generarFrecuencia'])->name('indicadores.frecuencia');
Route::get('/buscarprocesos', [ProcesoController::class,'findProcesos']);
Route::get('/listarprocesos', [ProcesoController::class,'listarProcesos'])->name('procesos.listar');;
Route::get('/buscarobjetivos', [PlanificacionSIGController::class, 'findObjetivos']);
Route::get('/indicadores/{id}/editdatos',[IndicadorController::class, 'editDatos'])->name('indicadores.editdatos');
Route::put('/indicadores_seguimiento/{id}', [IndicadorController::class, 'updateDatos']);
Route::get('/indicadores/formula/{id}', [IndicadorController::class, 'formula'])->name('indicadores.formula');
Route::post('/indicadores/validar-formula', [IndicadorController::class, 'validarFormula'])->name('indicadores.validarFormula');
Route::post('/indicadores/{id}/actualizar-formula', [IndicadorController::class, 'actualizarFormula'])->name('indicadores.actualizarFormula');
Route::post('/programa/{programa}/aprobar', [ProgramaAuditoriaController::class,'aprobar'])->name('programa.aprobar');
Route::post('/programa/{programa}/reprogramar', [ProgramaAuditoriaController::class,'reprogramar'])->name('programa.reprogramar');
Route::post('/programa/{programa}/showHistory', [ProgramaAuditoriaController::class,'showHistory'])->name('programa.showHistory');
Route::get('usuarios/listar-procesos/{id}', [UserController::class, 'listarProcesos'])->name('usuario.listar-procesos');
Route::get('usuarios/especialistas', [UserController::class,'showEspecialistas'])->name('especialistas.show');
Route::post('/smp/asignar-especialista/{hallazgoId}', [HallazgoController::class,'asignarEspecialista'])->name('smp.asignarEspecialista');

//hallazgos
Route::get('/smp/class/{clasificacion?}', [HallazgoController::class, 'index'])->name('smp.index');
Route::get('/smp/create/{clasificacion?}', [HallazgoController::class, 'create'])->name('smp.create');
Route::post('/smp/{id}/aprobar', [HallazgoController::class, 'aprobar'])->name('smp.aprobar');
Route::post('/smp/imprimir/{id}', [HallazgoController::class, 'imprimir'])->name('smp.imprimir');
Route::get('/smp/{id}/plan', [HallazgoController::class,'planes'])->name('smp.plan');
Route::get('smp/dashboard/home', [HallazgoController::class, 'dashboard'])->name('smp.dashboard');

//acciones
Route::get('/smp/{hallazgo_id}/acciones/seguimiento', [AccionController::class, 'index'])->name('smp.acciones.seguimiento');
Route::put('/smp/{hallazgo_id}/acciones/seguimiento/{id}', [AccionController::class, 'update_seguimiento'])->name('smp.acciones.update');
Route::get('/smp/{hallazgo_id}/acciones/{id}/archivos', [AccionController::class, 'listarArchivos'])->name('smp.acciones.archivos');
Route::delete('/smp/{hallazgo_id}/acciones/{id}/eliminar-archivo', [AccionController::class, 'eliminarArchivo'])->name('smp.acciones.eliminarArchivo');

//analisis causas
Route::prefix('hallazgos/{hallazgo_id}')->group(function () {
Route::get('analisis', [CausaController::class, 'index'])->name('analisis.index');
Route::get('analisis/create', [CausaController::class, 'create'])->name('analisis.create');
Route::post('analisis', [CausaController::class, 'store'])->name('analisis.store');
Route::get('analisis/{id}/edit', [CausaController::class, 'edit'])->name('analisis.edit');
Route::put('analisis/{id}', [CausaController::class, 'update'])->name('analisis.update');
Route::delete('analisis/{id}', [CausaController::class, 'destroy'])->name('analisis.destroy');
});




Route::group(['namespace' => 'Admin', 'middleware'=> ['auth', 'role:admin']], function () {
    Route::get('admin/usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
    Route::post('admin/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    Route::get('admin/usuarios/', [UserController::class, 'index'])->name('usuarios.index');

   //Pemisos asignados al Usuario

    Route::get('usuarios/asignar-permisos/{id}', [UserController::class, 'asignarPermisos'])->name('usuarios.asignar-permisos');
    Route::post('usuarios/asignar-permisos/{id}', [UserController::class, 'guardarPermisos'])->name('usuarios.guardar-permisos');

   //Roles asignados al Usuario

    Route::get('usuarios/asignar-roles/{id}', [UserController::class, 'asignarRoles'])->name('usuarios.asignar-roles');
    Route::post('usuarios/asignar-roles/{id}', [UserController::class, 'guardarRoles'])->name('usuarios.guardar-roles');

    //Procesos asignados al Usuario
  
    Route::get('usuarios/eliminar-proceso/{id}/{proceso_id}', [UserController::class,'eliminarProceso'])->name('usuarios.eliminar-proceso');
    Route::get('usuarios/resetear-contrasena/{id}', [UserController::class,'resetPassword'])->name('usuarios.reset-password');
    Route::get('usuarios/asignar-procesos/{id}', [UserController::class, 'asignarProcesos'])->name('usuarios.asignar-procesos');
    Route::post('usuarios/asignar-procesos/{id}', [UserController::class,'guardarProcesos'])->name('usuarios.guardar-procesos');
});

Route::group(['namespace' => 'Admin', 'middleware' => ['auth', 'role:facilitador']], function () {
   
    Route::get('usuario/ver-mapa', [UserController::class, 'mapearProcesos'])->name('usuario.mapear-procesos');
    
    //Requerimientos
    Route::get('/requerimientos/listar', [RequerimientoController::class, 'index'])->name('requerimientos.index');
    Route::get('/requerimientos', [RequerimientoController::class, 'create'])->name('requerimientos.create');
    Route::post('/requerimientos', [RequerimientoController::class, 'store'])->name('requerimientos.store');
    Route::get('/requerimientos/{requerimiento}', [RequerimientoController::class, 'show'])->name('requerimientos.show');
    Route::get('/requerimientos/{id}/trazabilidad', [RequerimientoController::class, 'trazabilidad'])->name('requerimientos.trazabilidad');
    //Necesidades
    Route::post('/necesidades', [RequerimientoNecesidadController::class, 'store'])->name('necesidad.store');
});

Route::get('admin', function () {
    return view('admin.admin');
})->middleware('auth'); 

