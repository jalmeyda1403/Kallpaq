<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcesoController;
use App\Http\Controllers\IndicadorController;
use App\Http\Controllers\PlanificacionSIGController;
use App\Http\Controllers\PlanificacionPEIController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RequerimientoController;
use App\Http\Controllers\RequerimientoNecesidadController;
use App\Http\Controllers\ProgramaAuditoriaController;
use App\Http\Controllers\HallazgoController;
use App\Http\Controllers\AccionController;
use App\Http\Controllers\CausaController;
use App\Http\Controllers\ContextoDeterminacionController;
use App\Http\Controllers\ObligacionController;
use App\Http\Controllers\AreaComplianceController;
use App\Http\Controllers\RiesgoController;
use App\Http\Controllers\OUOController;
use App\Http\Controllers\DiagramaContextoController;
use App\Http\Controllers\SipocController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DocumentoVersionController;




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
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');


Route::resource('procesos', ProcesoController::class);
Route::resource('indicadores', IndicadorController::class);
Route::resource('programa', ProgramaAuditoriaController::class);
Route::resource('smp', HallazgoController::class);
Route::resource('acciones', AccionController::class);
Route::resource('obligaciones', ObligacionController::class);
Route::resource('riesgos', RiesgoController::class);
Route::resource('ouos', OUOController::class);
Route::resource('documentos', DocumentoController::class);
Route::resource('sipoc', SipocController::class);

//Procesos
Route::get('/buscarProcesos/{proceso_id?}', [ProcesoController::class, 'findProcesos'])->name('procesos.buscar');

Route::get('/listarProcesos', [ProcesoController::class, 'listar'])->name('procesos.listar');
Route::get('/procesos-mapa', [ProcesoController::class, 'mapaProcesos'])->name('procesos.mapa');
Route::get('/procesos/{proceso_id}/listarouo', [ProcesoController::class, 'listarOUO'])->name('procesos.listarOUO');
Route::post('/procesos-asociar/{proceso_id}/ouo', [ProcesoController::class, 'asociarOUO'])->name('procesos.asociarOUO');
Route::delete('/procesos-disociar/{proceso_id}/ouo/{ouo_id}', [ProcesoController::class, 'disociarOUO'])->name('procesos.disociarOUO');
Route::get('/procesos', [ProcesoController::class, 'index'])->name('procesos.index');
Route::put('/proceso/update/{id}', [ProcesoController::class, 'update'])->name('proceso.update');
Route::delete('/proceso/delete/{id}', [ProcesoController::class, 'destroy'])->name('proceso.eliminar');
Route::get('/proceso/{proceso_id}/subprocesos', [ProcesoController::class, 'subprocesos'])->name('procesos.subprocesos');
Route::get('//proceso/{proceso_id}/nivel', [ProcesoController::class, 'procesos_nivel'])->name('procesos.nivel');


// Indicadores
Route::get('/indicadores/{proceso_id?}/listar', [IndicadorController::class, 'listar'])->name('indicadores.listar');
Route::get('indicadores/{proceso_id?}/create/', [IndicadorController::class, 'create'])->name('indicadores.create');
Route::get('/indicadores/{id}/historico-datos', [IndicadorController::class, 'showHistorico']);
Route::get('/indicadores/{id}/datos', [IndicadorController::class, 'showDatos']);
Route::get('indicadores/{id}/frecuencia', [IndicadorController::class, 'generarFrecuencia'])->name('indicadores.frecuencia');

//Matriz de Caracterización


Route::resource('caracterizacion', DiagramaContextoController::class);
Route::get('caracterizacion/{proceso_id?}/mcar', [DiagramaContextoController::class, 'index'])->name('procesos.caracterizacion');
Route::get('/salida/{salida_id}/requisitos', [SipocController::class, 'getRequisitos']);

//Objetivos
Route::get('/buscarobjetivosSIG', [PlanificacionSIGController::class, 'findObjetivosSIG'])->name('objetivoSIG.buscar');
Route::get('/buscarobjetivosPEI', [PlanificacionPEIController::class, 'findObjetivosPEI'])->name('objetivoPEI.buscar');
Route::get('/buscarareacompliance', [AreaComplianceController::class, 'findAreaCompliance'])->name('areaCompliance.buscar');
;

//Indicadores
Route::get('/indicadores/{id}/editdatos', [IndicadorController::class, 'editDatos'])->name('indicadores.editdatos');
Route::put('/indicadores_seguimiento/{id}', [IndicadorController::class, 'updateDatos']);
Route::get('/indicadores/formula/{id}', [IndicadorController::class, 'formula'])->name('indicadores.formula');
Route::post('/indicadores/validar-formula', [IndicadorController::class, 'validarFormula'])->name('indicadores.validarFormula');
Route::post('/indicadores/{id}/actualizar-formula', [IndicadorController::class, 'actualizarFormula'])->name('indicadores.actualizarFormula');
Route::post('/programa/{programa}/aprobar', [ProgramaAuditoriaController::class, 'aprobar'])->name('programa.aprobar');
Route::post('/programa/{programa}/reprogramar', [ProgramaAuditoriaController::class, 'reprogramar'])->name('programa.reprogramar');
Route::post('/programa/{programa}/showHistory', [ProgramaAuditoriaController::class, 'showHistory'])->name('programa.showHistory');
Route::get('usuarios/listar-procesos/{id}', [UserController::class, 'listarProcesos'])->name('usuario.listar-procesos');
Route::get('usuarios/especialistas', [UserController::class, 'showEspecialistas'])->name('especialistas.show');
Route::post('/smp/asignar-especialista/{hallazgoId}', [HallazgoController::class, 'asignarEspecialista'])->name('smp.asignarEspecialista');

//Obligaciones

Route::get('obligaciones', [ObligacionController::class, 'index'])->name('obligaciones.index');
Route::get('obligaciones/{proceso_id?}/listar', [ObligacionController::class, 'listar'])->name('obligaciones.listar');
Route::get('obligaciones/{obligacion_id}/listariesgos', [ObligacionController::class, 'listariesgos'])->name('obligaciones.listariesgos');
//Riesgos
Route::post('riesgos', [RiesgoController::class, 'store'])->name('riesgos.store');
Route::get('riesgos/{riesgo}', [RiesgoController::class, 'show'])->name('riesgos.show');
Route::post('riesgos/update/{riesgo}', [RiesgoController::class, 'update'])->name('riesgos.update');
Route::delete('/riesgos/eliminar/{riesgo}', [RiesgoController::class, 'destroy'])->name('riesgos.destroy');
Route::get('riesgos/{proceso_id?}/listar', [RiesgoController::class, 'listar'])->name('riesgos.listar');

//hallazgos
Route::get('/smp/class/{clasificacion?}', [HallazgoController::class, 'index'])->name('smp.index');
Route::get('/smp/create/{clasificacion?}', [HallazgoController::class, 'create'])->name('smp.create');
Route::post('/smp/{id}/aprobar', [HallazgoController::class, 'aprobar'])->name('smp.aprobar');
Route::post('/smp/imprimir/{id}', [HallazgoController::class, 'imprimir'])->name('smp.imprimir');
Route::get('/smp/{id}/plan', [HallazgoController::class, 'planes'])->name('smp.plan');
Route::get('/smp/{id}/clasificacion/{clasificacion}', [HallazgoController::class, 'porProceso'])->name('proceso.hallazgos');
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

//OUO
Route::get('/listarOUO', [OUOController::class, 'listar'])->name('ouos.listar');

//COntexto

Route::get('/contexto/', [ContextoDeterminacionController::class, 'index'])->name('contexto.index');

//Documentos
Route::put('/documento/update/{id}', [DocumentoController::class, 'update'])->name('documento.update');
Route::delete('/documento/delete/{id}', [DocumentoController::class, 'destroy'])->name('documento.eliminar');
Route::get('/documentos/descargar/{id}', [DocumentoController::class, 'descargarArchivo'])->name('documentos.descargar');
Route::get('/documentos/mostrar/{path}', [DocumentoController::class, 'mostrarArchivo'])
->where('path', '.*') 
->name('documentos.mostrar');
Route::put('/documento/version/update/{id}', [DocumentoVersionController::class, 'update'])->name('documento.version.update');
Route::get('/buscar-documento', [DocumentoController::class, 'buscar'])->name('documento.buscar');
Route::get('/documentos/{proceso_id}/validar', [DocumentoController::class, 'validarEnlaces'])->name('documentos.validar.enlaces');

//Pemisos asignados al Usuario
Route::get('admin/usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
Route::post('admin/usuarios', [UserController::class, 'store'])->name('usuarios.store');
Route::get('admin/usuarios/', [UserController::class, 'index'])->name('usuarios.index');

Route::get('usuarios/asignar-permisos/{id}', [UserController::class, 'asignarPermisos'])->name('usuarios.asignar-permisos');
Route::post('usuarios/asignar-permisos/{id}', [UserController::class, 'guardarPermisos'])->name('usuarios.guardar-permisos');

//Roles asignados al Usuario

Route::get('usuarios/asignar-roles/{id}', [UserController::class, 'asignarRoles'])->name('usuarios.asignar-roles');
Route::post('usuarios/asignar-roles/{id}', [UserController::class, 'guardarRoles'])->name('usuarios.guardar-roles');

//Procesos asignados al Usuario

Route::get('usuarios/eliminar-proceso/{id}/{proceso_id}', [UserController::class, 'eliminarProceso'])->name('usuarios.eliminar-proceso');
Route::get('usuarios/resetear-contrasena/{id}', [UserController::class, 'resetPassword'])->name('usuarios.reset-password');
Route::get('usuarios/asignar-procesos/{id}', [UserController::class, 'asignarProcesos'])->name('usuarios.asignar-procesos');
Route::post('usuarios/asignar-procesos/{id}', [UserController::class, 'guardarProcesos'])->name('usuarios.guardar-procesos');



//Requerimientos
Route::get('/requerimientos/listar', [RequerimientoController::class, 'index'])->name('requerimientos.index');
Route::get('/requerimientos', [RequerimientoController::class, 'create'])->name('requerimientos.create');
Route::post('/requerimientos', [RequerimientoController::class, 'store'])->name('requerimientos.store');
Route::get('/requerimientos/{requerimiento}', [RequerimientoController::class, 'show'])->name('requerimientos.show');
Route::get('/requerimientos/{id}/trazabilidad', [RequerimientoController::class, 'trazabilidad'])->name('requerimientos.trazabilidad');

//Necesidades
Route::post('/necesidades', [RequerimientoNecesidadController::class, 'store'])->name('necesidad.store');


