<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Requerimiento;
use App\Models\User;      // <-- ASEGÚRATE DE IMPORTAR USER
use App\Models\Proceso;  // <-- ASEGÚRATE DE IMPORTAR PROCESO
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Schema;

class RequerimientoSeeder extends Seeder
{ /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 0. BORRAR DATOS ANTIGUOS ---
        $this->command->info('Vaciando la tabla de requerimientos...');
        Schema::disableForeignKeyConstraints();
        Requerimiento::truncate();
        Schema::enableForeignKeyConstraints();

        $faker = Faker::create('es_ES');

        // --- 1. DATOS DE MEJOR CALIDAD (TEXTOS REALISTAS) ---
        $asuntos = [
            'Elaboración de nueva Directiva de Gestión Documental',
            'Actualización del Manual de Procedimientos (MAPRO)',
            'Creación de formato de registro para Control de Calidad',
            'Revisión y optimización del Proceso de Logística',
            'Inclusión de nuevo indicador en el Proceso de Atención al Ciudadano',
            'Modificación de la Ficha de Proceso (Caracterización)',
            'Elaboración de Manual de Perfiles de Puesto (MPP)',
        ];

        $descripciones_templates = [
            'Se solicita la elaboración de la Directiva [CODIGO] que regule el "Uso y control de vehículos oficiales", de acuerdo a la nueva normativa [NORMA].',
            'Actualizar el procedimiento [CODIGO] "Gestión de Adquisiciones y Contrataciones" para incluir los nuevos lineamientos de la OSCE.',
            'Crear el formato [CODIGO] "Checklist de Inspección de Seguridad" para el área de Mantenimiento.',
            'Realizar una revisión integral del proceso [CODIGO] para identificar cuellos de botella y proponer mejoras en el flujo de aprobación.',
            'Incluir el indicador "Porcentaje de satisfacción del usuario" en la Ficha de Proceso de [CODIGO], con su respectiva fórmula de cálculo.',
            'Modificar la Ficha de Proceso [CODIGO] para añadir las nuevas actividades de control interno detectadas en la última auditoría.',
            'Elaborar el Manual de Perfiles de Puesto para las 3 nuevas posiciones creadas en el área de Planificación y Presupuesto.',
        ];

        $justificaciones = [
            'Dar cumplimiento a la observación N° 005-2025 de la auditoría interna de la CGR.',
            'Alineamiento con los objetivos estratégicos del Plan Operativo Institucional (POI) 2026.',
            'Necesidad de mejora de la eficiencia operativa y reducción de costos en el área solicitante.',
            'Adaptación a la nueva [NORMA] sobre Transparencia y Acceso a la Información Pública.',
            'Respuesta a las no conformidades (NC-002) detectadas en la última revisión por la dirección.',
            'Requerimiento formal del órgano de control (OCI) para mejorar los puntos de control del proceso.',
        ];


        // --- 2. DEFINICIÓN DE DATOS (IDs) ---
        
        // ¡Importante! Leemos los IDs que SÍ existen, como hicimos antes
        $procesoIds = Proceso::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $especialistaIds = [1, 2, 3, 4, 5, 6, 7, 8];

        // Verificación de seguridad
        if (empty($procesoIds) || empty($userIds)) {
            $this->command->error('¡ERROR! Las tablas "procesos" o "users" están vacías. Ejecuta primero sus seeders.');
            return;
        }

        $this->command->info('Se encontraron ' . count($userIds) . ' usuarios y ' . count($procesoIds) . ' procesos.');

        // Opciones para campos de estado/complejidad
        $estados = ['creado','aprobado','asignado','atendido','desestimado'];
        $complejidades = ['baja','media','alta','muy alta'];
        $prioridades =  ['baja','media','alta','muy alta'];

        $this->command->info('Creando 15 requerimientos de ejemplo realistas...');
        
        $requerimientos = [];

        // --- 3. GENERAR 50 REQUERIMIENTOS ---
        for ($i = 0; $i < 15; $i++) {
            
            $estado = $faker->randomElement($estados);
            $fechaCreacion = $faker->dateTimeBetween('-6 months', 'now');
            
            // --- Lógica de IDs (Nunca nulos, como en tu BD) ---
            $userAsignaId = $faker->randomElement($userIds);
            $especialistaId = $faker->randomElement($especialistaIds);
            $complejidad = $faker->randomElement($complejidades);
            $prioridad = $faker->randomElement($prioridades);
            
            // --- Lógica de fechas condicional ---
            $fechaAsignacion = null;
            $fechaLimite = null;
            $fechaInicio = null;
            $fechaFin = null;
            $rutaDesistimacion = null;

            if (in_array($estado, ['asignado', 'atendido'])) {
                $fechaAsignacion = $faker->dateTimeBetween($fechaCreacion, 'now');
                $fechaLimite = $faker->dateTimeInInterval($fechaAsignacion, '+30 days');
            }
            
            if ($estado === 'atendido') {
                if ($fechaAsignacion === null) {
                   $fechaAsignacion = $faker->dateTimeBetween($fechaCreacion, 'now');
                }
                $fechaInicio = $faker->dateTimeBetween($fechaAsignacion, 'now');
                $fechaFin = $faker->dateTimeBetween($fechaInicio, 'now');
            }

            if ($estado === 'desestimado') {
                $rutaDesistimacion = 'documentos/ejemplo_desistimacion.pdf';
            }

            // --- Rellenar plantillas de texto ---
            $asunto_final = $faker->randomElement($asuntos);
            $justificacion_final = $faker->randomElement($justificaciones);
            $descripcion_template = $faker->randomElement($descripciones_templates);

            // Rellenamos las plantillas con datos falsos pero realistas
            $codigo_ficticio = $faker->randomElement(['PR-', 'DI-', 'FO-', 'MA-']) . $faker->numberBetween(100, 999);
            $normativa_ficticia = $faker->randomElement(['D.S. N° 004-2025-PCM', 'Ley N° 30512', 'Resolución N° 055-2025-CGR']);
            
            $descripcion_final = str_replace(
                ['[CODIGO]', '[NORMA]'],
                [$codigo_ficticio, $normativa_ficticia],
                $descripcion_template
            );
            
            $justificacion_final = str_replace('[NORMA]', $normativa_ficticia, $justificacion_final);


            $requerimientos[] = [
                // Claves Foráneas (Relaciones)
                'proceso_id' => $faker->randomElement($procesoIds),
                'facilitador_id' => $faker->randomElement($userIds),
                'especialista_id' => $especialistaId,
                'user_asigna_id' => $userAsignaId,

                // --- DATOS DE TEXTO DE CALIDAD MEJORADA ---
                'justificacion' => $justificacion_final,
                'asunto' => $asunto_final,
                'descripcion' => $descripcion_final,

                // Estados y Clasificación
                'estado' => $estado,
                'prioridad'=> $prioridad,
                'complejidad' => $complejidad,

                // Rutas y Fechas
                'ruta_archivo_desistimacion' => $rutaDesistimacion,
                'ruta_archivo_requerimiento' => 'documentos/ejemplo_requerimiento.pdf',
                'fecha_limite' => $fechaLimite,
                'fecha_asignacion' => $fechaAsignacion,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'created_at' => $fechaCreacion,
                'updated_at' => $fechaCreacion,
            ];
        }

        // Insertar los datos en la base de datos
        Requerimiento::insert($requerimientos);

        $this->command->info('¡Seeder de Requerimientos con datos realistas completado!');
    }
}