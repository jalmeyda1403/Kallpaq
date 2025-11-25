<?php


use App\Http\Controllers\SubAreaComplianceController;
use App\Http\Controllers\TipoDocumentoController;
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
use App\Http\Controllers\ParteInteresadaController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EspecialistaController;
use App\Http\Controllers\InventarioController;

// ... existing routes ...

// New route for listing users (for FacilitadorForm.vue)
Route::get('users/list', [UserController::class, 'listUsers'])->name('api.users.list');

// ... rest of the existing routes ...



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
//Otros
Route::get('/api/inventarios', [
    InventarioController::class,
    'apiInventarios'
])->name('api.inventarios.index');
Route::get('/procesos/macro', [ProcesoController::class, 'apiMacroProcesos'])->name('api.procesos.macro');
Route::get('/inventario/{id}/procesos-con-ouos', [InventarioController::class, 'apiProcesosConOuos'])->name('api.inventario.procesos');

// Gestion del Inventario (Nuevas Rutas API)
// Rutas CRUD para la gestión de inventarios (listar, crear, actualizar, eliminar)
// Rutas específicas para la gestión de procesos dentro de un inventario y su aprobación

// Grupo para rutas de inventario
Route::prefix('api/inventarios')->name('api.inventarios.')->group(function () {
    // CRUD principal
    Route::get('/', [InventarioController::class, 'indexApi'])->name('index');
    Route::post('/', [InventarioController::class, 'storeApi'])->name('store');
    Route::put('/{inventario}', [InventarioController::class, 'updateApi'])->name('update');
    Route::patch('/{inventario}', [InventarioController::class, 'updateApi'])->name('update.patch');
    Route::delete('/{inventario}', [InventarioController::class, 'destroyApi'])->name('destroy');

    // Rutas específicas para un inventario
    Route::prefix('/{inventario}')->group(function () {
        // Gestión de procesos
        Route::get('/procesos-disponibles', [App\Http\Controllers\InventarioController::class, 'procesosDisponibles'])->name('procesos.disponibles');
        Route::get('/procesos-asociados', [App\Http\Controllers\InventarioController::class, 'procesosAsociados'])->name('procesos.asociados');
        Route::post('/procesos/sync', [App\Http\Controllers\InventarioController::class, 'syncProcesos'])->name('procesos.sync');

        // Aprobación
        Route::post('/aprobar', [App\Http\Controllers\InventarioController::class, 'aprobar'])->name('aprobar');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('hallazgos/mine', [HallazgoController::class, 'apiMyHallazgos'])->name('hallazgos.mine');
    Route::delete('inventarios/{inventario}/procesos/{proceso}', [InventarioController::class, 'disassociateProcess'])->name('api.inventario-proceso.destroy');
    Route::post('inventarios/{inventario}/procesos/add', [InventarioController::class, 'addProcesos'])->name('api.inventarios.procesos.add');
    Route::get('/requerimientos-data', [RequerimientoController::class, 'webApiIndex'])->name('web.requerimientos.data');
    Route::get('/dashboard/resumen-general', [DashboardController::class, 'getResumenGeneral'])->name('dashboard.resumenGeneral');
    Route::get('/dashboard/resumen-grafico', [DashboardController::class, 'getResumenGrafico'])->name('dashboard.resumenGrafico');
    Route::get('/dashboard/alertas', [DashboardController::class, 'getResumenAlertas'])->name('dashboard.alertas');
    Route::get('/dashboard/resumen-especialistas', [DashboardController::class, 'getResumenEspecialistas'])->name('dashboard.resumenEspecialistas');
    Route::get('/dashboard/detalle-especialista', [DashboardController::class, 'getDetalleEspecialista'])->name('dashboard.detalleEspecialista');

    // New route for OUOs for assignment purposes (moved from api.php)
    Route::get('/asignacion-ouos', [OUOController::class, 'indexForAssignment'])->name('api.asignacion-ouos.index');

    // New route for fetching OUOs with nivel_jerarquico 1 or 2 for dropdowns
    Route::get('/ouos-padres-dropdown', [OUOController::class, 'getOuoPadresForDropdown'])->name('api.ouos.padres.dropdown');

    // Requerimientos Edit and Update routes
    Route::put('/requerimientos/{id}', [RequerimientoController::class, 'update'])->name('requerimientos.update');

    // Especialistas
    Route::get('/especialistas', [EspecialistaController::class, 'index'])->name('especialistas.index');

});

Route::resource('procesos', ProcesoController::class);
Route::resource('indicadores', IndicadorController::class);
Route::resource('programa', ProgramaAuditoriaController::class);
Route::resource('smp', HallazgoController::class);
Route::get('/api/hallazgos', [HallazgoController::class, 'apiListar'])->name('api.hallazgos');
Route::resource('acciones', AccionController::class)->except(['destroy', 'update']);
Route::post('/acciones/{accion}/reprogramar', [AccionController::class, 'reprogramar'])->name('acciones.reprogramar');
Route::post('/acciones/{accion}/concluir', [AccionController::class, 'concluir'])->name('acciones.concluir');
Route::get('/acciones/evidencia/{path}', [AccionController::class, 'downloadEvidencia'])->name('acciones.download-evidencia')->where('path', '.*');
Route::post('/acciones/{accion}/upload-evidencia', [AccionController::class, 'uploadEvidencia'])->name('acciones.upload-evidencia');
Route::post('/acciones/{accion}/delete-evidencia', [AccionController::class, 'deleteEvidencia'])->name('acciones.delete-evidencia');

Route::resource('riesgos', RiesgoController::class);
Route::resource('ouos', OUOController::class);

// New routes for OUO-Process assignment
Route::get('/ouos/{ouo}/procesos', [OUOController::class, 'getAssignedProcesses'])->name('ouos.procesos.index');
Route::post('/ouos/{ouo}/procesos', [OUOController::class, 'syncProcesses'])->name('ouos.procesos.sync');
Route::put('/ouos/{ouo}/procesos/{proceso}', [OUOController::class, 'updateProcessPivot'])->name('ouos.procesos.updatePivot');

// New routes for OUO-User assignment
Route::get('/users/gestores', [OUOController::class, 'getGestorUsersForDropdown'])->name('users.gestores');
// Ruta para obtener SMPs basados en la OUO del usuario
Route::get('/smp/ouo', [HallazgoController::class, 'getSmpByUserOuo'])->name('smp.ouo');
Route::get('/ouos/{ouo}/users', [OUOController::class, 'getAssignedUsers'])->name('ouos.users.index');
Route::get('/ouos/{ouo}/users/deleted', [OUOController::class, 'getSoftDeletedUsers'])->name('ouos.users.deleted');
Route::put('/ouos/{ouo}/users/{user}', [OUOController::class, 'updateUserPivot'])->name('ouos.users.updatePivot');
Route::delete('/ouos/{ouo}/users/{user}', [OUOController::class, 'detachUser'])->name('ouos.users.detach');
Route::post('/ouos/{ouo}/users', [OUOController::class, 'attachUser'])->name('ouos.users.attach');

Route::resource('documentos', DocumentoController::class);
Route::resource('sipoc', SipocController::class);

//Partes Interesadas
Route::get('/partes', [ParteInteresadaController::class, 'index'])->name('partes.index');
Route::post('/partes', [ParteInteresadaController::class, 'store'])->name('partes.store');
Route::put('/partes/update/{id}', [ParteInteresadaController::class, 'update'])->name('partes.update');
Route::delete('/partes/{id}', [ParteInteresadaController::class, 'destroy'])->name('partes.destroy');

//Procesos
Route::get('/buscarProcesos', [ProcesoController::class, 'buscar'])->name('procesos.buscar');

Route::get('/proceso/{proceso_id?}/show', [ProcesoController::class, 'show'])->name('procesos.show');
Route::get('/listarProcesos', [ProcesoController::class, 'listar'])->name('procesos.listar');
Route::get('/procesos-mapa', [ProcesoController::class, 'mapaProcesos'])->name('procesos.mapa');

Route::get('/procesos-inventario', [ProcesoController::class, 'inventarioProcesos'])->name('procesos.inventario');
//Procesos-AsociarOUO
Route::get('/procesos/{proceso_id}/listarouo', [ProcesoController::class, 'listarOUO'])->name('procesos.listarOUO');
Route::post('/procesos-asociar/{proceso}/ouo', [ProcesoController::class, 'asociarOUO'])->name('procesos.asociarOUO');
Route::put('/procesos/{proceso}/ouos/{ouo}', [ProcesoController::class, 'updateOUO'])->name('procesos.ouos.update');
Route::delete('/procesos-disociar/{proceso}/ouo/{ouo}', [ProcesoController::class, 'disociarOUO'])->name('procesos.disociarOUO');

//Procesos-AsociarDocumentos
Route::get('procesos/{proceso_id}/listardocumentos', [ProcesoController::class, 'listarDocumentos'])->name('procesos.listarDocumentos');
Route::post('/procesos-asociar/{proceso}/documentos', [ProcesoController::class, 'asociarDocumentos'])->name('procesos.asociarDocumentos');
Route::put('/procesos/{proceso}/documentos/{documentos}', [ProcesoController::class, 'updateOUO'])->name('procesos.documentos.update');
Route::delete('/procesos-disociar/{proceso}/documentos/{documento}', [ProcesoController::class, 'disociarDocumentos'])->name('procesos.disociarDocumentos');

Route::get('/procesos', [ProcesoController::class, 'index'])->name('procesos.index');
Route::put('/proceso/update/{id}', [ProcesoController::class, 'update'])->name('proceso.update');
Route::delete('/proceso/delete/{id}', [ProcesoController::class, 'destroy'])->name('proceso.eliminar');
Route::get('/proceso/{proceso_id}/subprocesos', [ProcesoController::class, 'subprocesos'])->name('procesos.subprocesos');
Route::get('/proceso/{proceso_id}/nivel', [ProcesoController::class, 'procesos_nivel'])->name('procesos.nivel');


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

//Area Compliance
Route::get('/buscarareacompliance', [AreaComplianceController::class, 'findAreaCompliance'])->name('areaCompliance.buscar');
Route::get('/buscarsubareacompliance', [SubAreaComplianceController::class, 'findSubAreaCompliance'])->name('subareaCompliance.buscar');

//Tag
Route::get('/buscartag', [TagController::class, 'findTag'])->name('tag.buscar');

//TipoDocumento
Route::get('/buscartipodocumento', [TipoDocumentoController::class, 'findTipoDocumento'])->name('tipoDocumento.buscar');

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
Route::get('usuarios/especialistas', [UserController::class, 'showEspecialistas'])->name('usuario.especialistas');
Route::get('usuarios/auditores', [UserController::class, 'showAuditores'])->name('usuario.auditores');


//Obligaciones

Route::get('obligaciones', function () {
    return view('app');
})->name('obligaciones.index');
Route::get('obligaciones/{proceso_id?}/listar', [ObligacionController::class, 'listar'])->name('obligaciones.listar');
Route::get('obligaciones/{obligacion_id}/listariesgos', [ObligacionController::class, 'listariesgos'])->name('obligaciones.listariesgos');
//Riesgos
Route::post('riesgos', [RiesgoController::class, 'store'])->name('riesgos.store');
Route::get('riesgos/{riesgo}', [RiesgoController::class, 'show'])->name('riesgos.show');
Route::post('riesgos/update/{riesgo}', [RiesgoController::class, 'update'])->name('riesgos.update');
Route::delete('/riesgos/eliminar/{riesgo}', [RiesgoController::class, 'destroy'])->name('riesgos.destroy');
Route::get('riesgos/{proceso_id?}/listar', [RiesgoController::class, 'listar'])->name('riesgos.listar');

Route::get('/api/hallazgos', [HallazgoController::class, 'apiListar'])->name('api.hallazgos');

// Rutas API para Obligaciones
Route::prefix('api/obligaciones')->name('api.obligaciones.')->group(function () {
    Route::get('/', [ObligacionController::class, 'apiIndex'])->name('index');
    Route::get('/{id}', [ObligacionController::class, 'show'])->name('show');
    Route::post('/', [ObligacionController::class, 'store'])->name('store');
    Route::put('/{id}', [ObligacionController::class, 'update'])->name('update');
    Route::delete('/{id}', [ObligacionController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/riesgos', [ObligacionController::class, 'listariesgos'])->name('listariesgos');
});

// Rutas API para Hallazgos
Route::controller(HallazgoController::class)
    ->prefix('hallazgos')
    ->middleware('auth')
    ->group(function () {
        Route::get('/show/{hallazgo}', 'show')->name('hallazgo.show');
        Route::post('/store', 'store')->name('hallazgo.store');
        Route::put('/update/{hallazgo}', 'update')->name('hallazgo.update');
        Route::post('/{hallazgo}/aprobar', 'aprobar')->name('hallazgo.aprobar');
        Route::post('/{hallazgo}/adjuntos', 'subirAdjunto')->name('hallazgo.adjuntos.store');

        // Rutas para Verificación de Eficacia
        Route::get('/eficacia/listar', 'apiListarEficacia')->name('hallazgo.eficacia.listar');
        Route::post('/{hallazgo}/evaluacion', 'storeEvaluacion')->name('hallazgo.evaluacion.store');
        Route::post('/{hallazgo}/evaluacion/upload', 'uploadEvaluacionEvidencia')->name('hallazgo.evaluacion.upload');
        Route::get('/{hallazgo}/evaluacion', 'getEvaluacion')->name('hallazgo.evaluacion.get');
    });
// Rutas API para Riesgos (con middleware auth)
Route::prefix('api/riesgos')
    ->middleware('auth')
    ->group(function () {
        Route::get('/mis-riesgos', [RiesgoController::class, 'misRiesgos'])->name('api.riesgos.mis-riesgos');
        Route::get('/{riesgo}/completo', [RiesgoController::class, 'getRiesgoCompleto'])->name('api.riesgos.completo');
        Route::put('/{riesgo}/evaluacion', [RiesgoController::class, 'updateEvaluacion'])->name('api.riesgos.update-evaluacion');
        Route::put('/{riesgo}/tratamiento', [RiesgoController::class, 'updateTratamiento'])->name('api.riesgos.update-tratamiento');
        Route::put('/{riesgo}/verificacion', [RiesgoController::class, 'updateVerificacion'])->name('api.riesgos.update-verificacion');

        // Rutas existentes (si las hay, o nuevas CRUD)
        Route::get('/', [RiesgoController::class, 'index'])->name('api.riesgos.index');
        Route::post('/', [RiesgoController::class, 'store'])->name('api.riesgos.store');
        Route::get('/{riesgo}', [RiesgoController::class, 'show'])->name('api.riesgos.show');
        Route::put('/{riesgo}', [RiesgoController::class, 'update'])->name('api.riesgos.update');
        Route::delete('/{riesgo}', [RiesgoController::class, 'destroy'])->name('api.riesgos.destroy');
    });
Route::middleware('auth')->group(function () {
    Route::get('/api/hallazgos/{hallazgo}/acciones', [AccionController::class, 'getAccionesPorHallazgo'])->name('api.acciones.por-hallazgo');
    Route::get('/api/hallazgos/{hallazgo}/planes-accion-completo', [AccionController::class, 'getPlanesAccionCompleto'])->name('api.hallazgos.planes-accion-completo');
});


//Asociar hallazgos con Procesos
Route::controller(HallazgoController::class)
    ->prefix('hallazgos/{hallazgo}/procesos')
    ->name('hallazgo.procesos.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/', 'listarProcesosAsociados')->name('listar');
        Route::post('/', 'asociarProceso')->name('asociar');
        Route::delete('/{proceso}', 'disociarProceso')->name('disociar');
    });
//Asignar especialista
Route::controller(HallazgoController::class)
    ->prefix('hallazgos/{hallazgo}/asignaciones') // Agrupamos bajo el prefijo de asignaciones
    ->name('hallazgo.asignaciones.')           // Nombre base para las rutas del grupo
    ->middleware('auth')                       // Se recomienda proteger estas rutas
    ->group(function () {
        Route::get('/', 'listarAsignaciones')->name('listar');
        Route::post('/', 'asignarEspecialista')->name('asignar');
    });
//Gestionar Acciones
Route::controller(AccionController::class) // <-- CAMBIO
    ->prefix('hallazgos/{hallazgo}/procesos/{proceso}/acciones')
    ->name('hallazgos.acciones.')
    ->middleware('auth')
    ->group(function () {
        // CAMBIO: Se renombra el método a 'listarAcciones'
        Route::get('/', 'listarAcciones')->name('listar');
        Route::post('/', 'storeAccion')->name('store');
    });
Route::controller(AccionController::class) // <-- CAMBIO
    ->prefix('acciones/{accion}')
    ->name('acciones.')
    ->middleware('auth')
    ->group(function () {
        Route::put('/', 'updateAccion')->name('update');
        Route::delete('/', 'destroyAccion')->name('destroy');
    });

// Análisis de Causa Raíz
Route::controller(AccionController::class) // <-- CAMBIO
    ->prefix('hallazgos/{hallazgo}/causas')
    ->name('hallazgos.causas.')
    ->middleware('auth')
    ->group(function () {
        // CAMBIO: Se renombra el método a 'listarCausaRaiz'
        Route::get('/listar', 'listarCausaRaiz')->name('listar');
        Route::post('/', 'storeOrUpdateCausaRaiz')->name('storeOrUpdate');
    });


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
Route::get('/buscarOUO', [OUOController::class, 'buscar'])->name('ouos.buscar');
Route::get('/listarOUO', [OUOController::class, 'listar'])->name('ouos.listar');

//COntexto

Route::get('/contexto/', [ContextoDeterminacionController::class, 'index'])->name('contexto.index');

//Documentos
Route::get('/buscarDocumentos', [DocumentoController::class, 'buscar'])->name('documentos.buscar');
Route::get('/documento/{documento_id?}/show', [DocumentoController::class, 'show'])->name('documentos.show');
Route::put('/documento/{id}/updateInfoGeneral/', [DocumentoController::class, 'updateInfoGeneral'])->name('documento.updateInfoGeneral');
Route::put('/documento/{id}/updateMetadatos', [DocumentoController::class, 'updateMetadatos'])->name('documento.updateMetadatos');

//Asociar Documentos con Procesos
Route::controller(DocumentoController::class)
    ->prefix('documentos/{documento}/procesos')
    ->name('documento.procesos.')
    ->group(function () {
        Route::get('/', 'listarProcesosAsociados')->name('listar');
        Route::post('/{proceso}', 'asociarProceso')->name('asociar');
        Route::delete('/{proceso}', 'disociarProceso')->name('disociar');
    });

// Asociar con Documentos Relacionados
Route::controller(DocumentoController::class)
    ->prefix('documentos/{documento}/relacionados')
    ->name('documento.relacionados.')
    ->group(function () {
        Route::get('/', 'listarRelacionados')->name('listar');
        Route::post('/', 'asociarRelacionado')->name('asociar');
        // Usamos el nombre del parámetro {relacionado} para claridad
        Route::delete('/{relacionado}', 'disociarRelacionado')->name('disociar');
    });

// Ruta Jerarquia de Documentos

Route::controller(DocumentoController::class)
    ->prefix('documentos/{documento}/jerarquia')
    ->name('documento.jerarquia.')
    ->group(function () {
        Route::get('/', 'getJerarquia')->name('get');
        Route::post('/set-padre', 'setPadre')->name('setPadre');
        Route::post('/remove-padre', 'removePadre')->name('removePadre');
        Route::post('/set-hijo', 'setHijo')->name('setHijo');
        Route::post('/remove-hijo/{hijo}', 'removeHijo')->name('removeHijo');
    });
// Ruta para el nuevo flujo de creación de versión "inteligente"
Route::post('documentos/{documento}/versiones/crear-con-dependencias', [DocumentoController::class, 'crearNuevaVersionConDependencias'])
    ->name('documentos.versiones.crearConDependencias');

// Grupo de rutas para gestionar las dependencias de UNA versión específica.
// Usamos {version} para el Route-Model Binding con DocumentoVersion.
Route::controller(DocumentoController::class)
    ->prefix('versiones/{version}/dependencias')
    ->name('versiones.dependencias.')
    ->middleware('auth') // Buena práctica proteger estas rutas
    ->group(function () {
        Route::get('/', 'getDependencias')->name('get');
        Route::post('/', 'setDependencia')->name('set');
        Route::delete('/{hijo}', 'removeDependencia')->name('remove');
    });
// Ruta para el Historial
Route::get('/documentos/{documento}/historial', [DocumentoController::class, 'listarHistorial'])
    ->name('documento.historial.listar');


Route::get('/documento/mostrar/{path}', [DocumentoController::class, 'mostrarArchivo'])
    ->name('documento.mostrar')
    ->where('path', '.*');

Route::get('/documentos', [DocumentoController::class, 'listar'])->name('documento.listar');

Route::put('/documento/update/{id}', [DocumentoController::class, 'update'])->name('documento.update');
Route::delete('/documento/delete/{id}', [DocumentoController::class, 'destroy'])->name('documento.eliminar');
Route::get('/documentos/descargar/{id}', [DocumentoController::class, 'descargarArchivo'])->name('documentos.descargar');

Route::get('/documento/{documento_id}/versiones', [DocumentoController::class, 'versiones'])->name('documento.versiones');
Route::put('/documento/version/update/{id}', [DocumentoVersionController::class, 'update'])->name('documento.version.update');
Route::get('/buscar-documento', [DocumentoController::class, 'findDocumento'])->name('documento.buscar');
Route::get('/documentos/{proceso_id}/validar', [DocumentoController::class, 'validarEnlaces'])->name('documentos.validar.enlaces');

Route::get('/api/documentos', [DocumentoController::class, 'apiListar'])->name('api.documentos');

//Pemisos asignados al Usuario
Route::get('admin/usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
Route::post('admin/usuarios', [UserController::class, 'store'])->name('usuarios.store');
Route::get('admin/usuarios/', [UserController::class, 'index'])->name('usuarios.index');
Route::get('api/admin/usuarios', [UserController::class, 'apiIndex'])->name('api.admin.usuarios.index');

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

Route::get('/requerimientos-data', [RequerimientoController::class, 'webApiIndex'])->name('web.requerimientos.data');

Route::middleware('auth')->group(function () {

    Route::post('/requerimientos', [RequerimientoController::class, 'store'])->name('requerimientos.store');
    Route::get('/requerimiento/{id}/evaluacion', [RequerimientoController::class, 'getEvaluacion'])->name('requerimiento.evaluacion');
    Route::post('/requerimiento/{id}/evaluacion', [RequerimientoController::class, 'storeEvaluacion'])->name('requerimiento.grabarEvaluacion');
    Route::post('/requerimiento/{id}/asignar', [RequerimientoController::class, 'asignarEspecialista'])->name('requerimiento.asignar');
    Route::get('/requerimientos/{id}/avance', [RequerimientoController::class, 'getAvance'])->name('requerimientos.getAvance');
    Route::get('/requerimientos/{id}/etapas', [RequerimientoController::class, 'getEtapas'])->name('requerimientos.etapas');
    Route::post('/requerimientos/{id}/guardar-avance', [RequerimientoController::class, 'guardarAvance'])->name('requerimientos.guardarAvance');
    Route::post('/requerimientos/{id}/desestimar', [RequerimientoController::class, 'desestimar'])->name('requerimientos.desestimar');
    Route::post('/requerimientos/{id}/finalizar', [RequerimientoController::class, 'finalizar'])->name('requerimientos.finalizar');
    Route::post('/requerimientos/{id}/delete-document', [RequerimientoController::class, 'deleteDocument'])->name('requerimientos.deleteDocument');

    Route::prefix('requerimientos/{id}/evidencias')->name('requerimientos.evidencias.')->group(function () {
        Route::get('/', [RequerimientoController::class, 'listarEvidencias'])->name('listar');
        Route::post('/', [RequerimientoController::class, 'subirEvidencia'])->name('subir');
        Route::delete('/', [RequerimientoController::class, 'eliminarEvidencia'])->name('eliminar');
        Route::get('/descargar', [RequerimientoController::class, 'descargarEvidencia'])->name('descargar');
    });


});


Route::get('/requerimientos/creados', [RequerimientoController::class, 'creados'])->name('requerimientos.creados');


Route::get('/requerimientos/asignados/{rol}', [RequerimientoController::class, 'asignados'])->name('requerimientos.asignados');
Route::get('/requerimientos/atendidos/{rol}', [RequerimientoController::class, 'atendidos'])->name('requerimientos.atendidos');

Route::get('/requerimientos/seguimiento', function () {
    return view('app');
})->name('requerimientos.seguimiento');

Route::get('/mis-requerimientos', [RequerimientoController::class, 'index'])->name('requerimientos.mine');

Route::post('/requerimientos', [RequerimientoController::class, 'store'])->name('requerimientos.store');





Route::get('/requerimientos/{id}', [RequerimientoController::class, 'show'])->name('requerimientos.show');




Route::get('/requerimientos/{id}/trazabilidad', [RequerimientoController::class, 'trazabilidad'])->name('requerimientos.trazabilidad');

// Requerimiento Form Wizard related routes
Route::post('/requerimientos/{id}/upload-document', [RequerimientoController::class, 'uploadDocument'])->name('requerimientos.uploadDocument');
Route::post('/requerimientos/{id}/submit-for-evaluation', [RequerimientoController::class, 'submitForEvaluation'])->name('requerimientos.submitForEvaluation');
Route::get('/requerimientos/{id}/print', [RequerimientoController::class, 'printRequerimiento'])->name('requerimientos.print');

// Web API for Requerimientos data
Route::get('/requerimientos-data', [RequerimientoController::class, 'webApiIndex'])->name('web.requerimientos.data');


//Necesidades
Route::post('/necesidades', [RequerimientoNecesidadController::class, 'store'])->name('necesidad.store');

Route::get('/test-styles', function () {
    return view('test');
});

// Ruta para imprimir plan de acción
Route::get('/acciones/imprimir/{hallazgo}', [AccionController::class, 'imprimirPlanAccion'])->name('acciones.imprimir');

// Rutas para Salidas No Conformes
Route::prefix('api/salidas-nc')->middleware('auth')->name('api.salidas-nc.')->group(function () {
    Route::get('/', [App\Http\Controllers\SalidaNoConformeController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\SalidaNoConformeController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\SalidaNoConformeController::class, 'show'])->name('show');
    Route::put('/{id}', [App\Http\Controllers\SalidaNoConformeController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\SalidaNoConformeController::class, 'destroy'])->name('destroy');

    // Rutas para acciones correctivas
    Route::post('/{id}/acciones', [App\Http\Controllers\SalidaNoConformeController::class, 'storeAccion'])->name('acciones.store');
    Route::put('/acciones/{accionId}', [App\Http\Controllers\SalidaNoConformeController::class, 'updateAccion'])->name('acciones.update');
});

// Ruta para el dashboard de mejora
Route::get('/api/dashboard/mejora', [App\Http\Controllers\DashboardMejoraController::class, 'index'])->name('dashboard.mejora.api');

// Rutas para el módulo de Indicadores (Vue)
Route::prefix('api/indicadores-vue')->middleware('auth')->name('api.indicadores-vue.')->group(function () {
    Route::get('/procesos-gestion', [App\Http\Controllers\IndicadoresVueController::class, 'getProcesosGestion'])->name('procesos-gestion');
    Route::get('/procesos/{procesoId}', [App\Http\Controllers\IndicadoresVueController::class, 'getIndicadoresByProceso'])->name('by-proceso');
    Route::post('/', [App\Http\Controllers\IndicadoresVueController::class, 'storeIndicador'])->name('store');
    Route::put('/{id}', [App\Http\Controllers\IndicadoresVueController::class, 'updateIndicador'])->name('update');
    Route::get('/ouo-reporte', [App\Http\Controllers\IndicadoresVueController::class, 'getIndicadoresForOUO'])->name('ouo-reporte');
    Route::post('/{id}/seguimiento', [App\Http\Controllers\IndicadoresVueController::class, 'storeSeguimiento'])->name('seguimiento');
});

// Route to handle Vue SPA pages, prefixed with /vue/
Route::get('/vue/{any}', function () {
    return view('app');
})->where('any', '.*')->name('vue.app');

// New route for OuoUserAssignment component
Route::get('/vue/ouos/user-assignment', function () {
    return view('app');
})->name('vue.ouos.user-assignment');

// Rutas para Consolidado de Sugerencias
Route::prefix('api/sugerencias')->middleware('auth')->name('api.sugerencias.')->group(function () {
    Route::get('/', [App\Http\Controllers\SugerenciaController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\SugerenciaController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\SugerenciaController::class, 'show'])->name('show');
    Route::put('/{id}', [App\Http\Controllers\SugerenciaController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\SugerenciaController::class, 'destroy'])->name('destroy');
});

// Rutas para Encuestas de Satisfacción
Route::prefix('api/encuestas-satisfaccion')->middleware('auth')->name('api.encuestas-satisfaccion.')->group(function () {
    Route::get('/', [App\Http\Controllers\EncuestaSatisfaccionController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\EncuestaSatisfaccionController::class, 'store'])->name('store');
    Route::post('/{id}', [App\Http\Controllers\EncuestaSatisfaccionController::class, 'update'])->name('update'); // Using POST for file upload update
    Route::delete('/{id}', [App\Http\Controllers\EncuestaSatisfaccionController::class, 'destroy'])->name('destroy');
    Route::get('/dashboard', [App\Http\Controllers\EncuestaSatisfaccionController::class, 'dashboard'])->name('dashboard');
});