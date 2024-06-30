<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcesosTableSeeder extends Seeder
{
   
    public function run()
    {
        //procesos Nivel 0
        /*  $procesos = [
                ['cod_proceso' => 'PE01', 'nombre' => 'Gestión Estratégica', 'tipo_proceso' => 'Estratégico'],   
                ['cod_proceso' => 'PE02', 'nombre' => 'Desarrollo Institucional', 'tipo_proceso' => 'Estratégico'],
                ['cod_proceso' => 'PE03', 'nombre' => 'Comunicación y Relaciones Interinstitucionales', 'tipo_proceso' => 'Estratégico'],
                ['cod_proceso' => 'PM01', 'nombre' => 'Prevención y Detección de la Corrupción', 'tipo_proceso' => 'Misional'],
                ['cod_proceso' => 'PM02', 'nombre' => 'Atención a las Entidades y Partes Interesadas', 'tipo_proceso' => 'Misional'],
                ['cod_proceso' => 'PM03', 'nombre' => 'Realización de los Servicios de Control Simultáneo, de Control Posterior y Relacionados', 'tipo_proceso' => 'Misional'],
                ['cod_proceso' => 'PM04', 'nombre' => 'Gestión de Sanciones y Procesos Judiciales Resultantes de los Servicios de Control', 'tipo_proceso' => 'Misional'],
                ['cod_proceso' => 'PM05', 'nombre' => 'Gestión de los Resultados del Control para la Mejora de las Entidades Públicas', 'tipo_proceso' => 'Misional'],
                ['cod_proceso' => 'PA01', 'nombre' => 'Gestión del Capital Humano', 'tipo_proceso' => 'Apoyo'],
                ['cod_proceso' => 'PA02', 'nombre' => 'Gestión de Activos Documentarios', 'tipo_proceso' => 'Apoyo'],
                ['cod_proceso' => 'PA03', 'nombre' => 'Gestión de Abastecimiento', 'tipo_proceso' => 'Apoyo'],
                ['cod_proceso' => 'PA04', 'nombre' => 'Gestión Financiera', 'tipo_proceso' => 'Apoyo'],
                ['cod_proceso' => 'PA05', 'nombre' => 'Gestión de Tecnologías de la Información y Comunicaciones', 'tipo_proceso' => 'Apoyo'],
                ['cod_proceso' => 'PA06', 'nombre' => 'Gestión Jurídico Legal', 'tipo_proceso' => 'Apoyo'],
                ['cod_proceso' => 'PA07', 'nombre' => 'Gestión de la Seguridad', 'tipo_proceso' => 'Apoyo']
            ];
        */
        //procesos Estratégicos Nivel 1
        /*   $procesos = [
                ['cod_proceso' => 'PE01.01', 'nombre' => 'Planeamiento Estratégico', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE01'], 
                ['cod_proceso' => 'PE01.02', 'nombre' => 'Gestión de Entidades Sujetad a Control', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE01'],
                ['cod_proceso' => 'PE01.03', 'nombre' => 'Planeamiento Operativo', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE01'],
                ['cod_proceso' => 'PE01.04', 'nombre' => 'Control Institucional', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE01'],
                ['cod_proceso' => 'PE02.01', 'nombre' => 'Diseño Organizacional', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE02'],
                ['cod_proceso' => 'PE02.02', 'nombre' => 'Gestión de la Modernización', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE02'],
                ['cod_proceso' => 'PE02.03', 'nombre' => 'Gestión Normativa', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE02'],
                ['cod_proceso' => 'PE02.04', 'nombre' => 'Gestión de la Inversión', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE02'],
                ['cod_proceso' => 'PE02.05', 'nombre' => 'Gestión del Conocimiento', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE02'],
                ['cod_proceso' => 'PE02.06', 'nombre' => 'Gestión de la Continuidad del Negocio', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE02'],
                ['cod_proceso' => 'PE02.07', 'nombre' => 'Gestión de la Integridad Institucional', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre'=>'PE02'],
                ['cod_proceso' => 'PE03.01', 'nombre' => 'Gestión de la Comunicación Institucional', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre' => 'PE03'],
                ['cod_proceso' => 'PE03.02', 'nombre' => 'Gestión de las Relaciones Interinstitucionales', 'tipo_proceso' => 'Estratégico', 'cod_proceso_padre' => 'PE03']
            ];
-       */
        //procesos Misionales Nivel 1
        /*$procesos = [
            ['cod_proceso' => 'PM01.01', 'nombre' => 'Gestión de mecanismos de prevención y detección de la corrupción', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM01'],
            ['cod_proceso' => 'PM01.02', 'nombre' => 'Participación ciudadana', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM01'],
            ['cod_proceso' => 'PM02.01', 'nombre' => 'Atención de la demanda imprevisible de control', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM02'],
            ['cod_proceso' => 'PM02.02', 'nombre' => 'Atención de pedidos de información y solicitudes de opinión', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM02'],
            ['cod_proceso' => 'PM02.03', 'nombre' => 'Atención de quejas y reclamos', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM02'],
            ['cod_proceso' => 'PM03.01', 'nombre' => 'Programación de los servicios de control y de fiscalización', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM03'],
            ['cod_proceso' => 'PM03.02', 'nombre' => 'Realización de los servicios de control simultáneo', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM03'],
            ['cod_proceso' => 'PM03.03', 'nombre' => 'Realización de los servicios de control posterior', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM03'],
            ['cod_proceso' => 'PM03.04', 'nombre' => 'Realización de los servicios relacionados', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM03'],
            ['cod_proceso' => 'PM03.05', 'nombre' => 'Supervisión técnica y revisión de oficio de los servicios de control', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM03'],
            ['cod_proceso' => 'PM04.01', 'nombre' => 'Gestión de sanciones administrativas', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM04'],
            ['cod_proceso' => 'PM04.02', 'nombre' => 'Gestión del procedimiento sancionador por infracción al ejercicio del control gubernamental', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM04'],
            ['cod_proceso' => 'PM04.03', 'nombre' => 'Gestión de los procesos judiciales resultantes de los servicios de control', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM04'],
            ['cod_proceso' => 'PM05.01', 'nombre' => 'Seguimiento y evaluación a la implementación de las recomendaciones, acciones y pronunciamientos, resultados de los servicios de control', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM05'],
            ['cod_proceso' => 'PM05.02', 'nombre' => 'Desarrollo de buenas prácticas y propuestas de mejora para la gestión de las entidades', 'tipo_proceso' => 'Misional', 'cod_proceso_padre' => 'PM05']
         ];
         */
        //procesos Apoyo Nivel 1
        $procesos = [
        //Procesos bajo 'PA01'
        ['cod_proceso' => 'PA01.01', 'nombre' => 'Planificación del capital humano', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA01'],
        ['cod_proceso' => 'PA01.02', 'nombre' => 'Incorporación del capital humano', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA01'],
        ['cod_proceso' => 'PA01.03', 'nombre' => 'Desarrollo del capital humano', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA01'],
        ['cod_proceso' => 'PA01.04', 'nombre' => 'Administración del capital humano', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA01'],
        ['cod_proceso' => 'PA01.05', 'nombre' => 'Gestión del bienestar del capital humano', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA01'],
        ['cod_proceso' => 'PA01.06', 'nombre' => 'Gestión del jefe y personal del OCI', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA01'],
        // Procesos bajo 'PA02'
        ['cod_proceso' => 'PA02.01', 'nombre' => 'Planificación del activo documentario', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA02'],
        ['cod_proceso' => 'PA02.02', 'nombre' => 'Recepción de documentos', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA02'],
        ['cod_proceso' => 'PA02.03', 'nombre' => 'Clasificación, reclasificación y desclasificación de documentos secretos y reservados', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA02'],
        ['cod_proceso' => 'PA02.04', 'nombre' => 'Distribución de documentos y valijas', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA02'],
        ['cod_proceso' => 'PA02.05', 'nombre' => 'Archivo, custodia y conservación de documentos', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA02'],
        ['cod_proceso' => 'PA02.06', 'nombre' => 'Autenticación de firmas y certificación de documentos', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA02'],
        // Procesos bajo 'PA03'
        ['cod_proceso' => 'PA03.01', 'nombre' => 'Elaboración del plan anual de contrataciones', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA03'],
        ['cod_proceso' => 'PA03.02', 'nombre' => 'Contratación de bienes y servicios', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA03'],
        ['cod_proceso' => 'PA03.03', 'nombre' => 'Gestión de bienes patrimoniales', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA03'],
        ['cod_proceso' => 'PA03.04', 'nombre' => 'Gestión de almacén', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA03'],
        ['cod_proceso' => 'PA03.05', 'nombre' => 'Administración de servicios generales', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA03'],
        ['cod_proceso' => 'PA03.06', 'nombre' => 'Gestión de sociedades de auditoria', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA03'],
         // Procesos bajo 'PA04'
        ['cod_proceso' => 'PA04.01', 'nombre' => 'Programación multianual, formulación y aprobación del presupuesto', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA04'],
        ['cod_proceso' => 'PA04.02', 'nombre' => 'Ejecución presupuestal', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA04'],
        ['cod_proceso' => 'PA04.03', 'nombre' => 'Evaluación presupuestal', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA04'],
        ['cod_proceso' => 'PA04.04', 'nombre' => 'Gestión contable', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA04'],
         // Procesos bajo 'PA05'
        ['cod_proceso' => 'PA05.01', 'nombre' => 'Planificación de tecnologías de la información y comunicaciones', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA05'],
        ['cod_proceso' => 'PA05.02', 'nombre' => 'Implementación de tecnologías de la información y comunicaciones', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA05'],
        ['cod_proceso' => 'PA05.03', 'nombre' => 'Operación de tecnologías de la información y comunicaciones', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA05'],
        // Procesos bajo 'PA06'
        ['cod_proceso' => 'PA06.01', 'nombre' => 'Gestión y difusión de productos de interés legal', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA06'],
        ['cod_proceso' => 'PA06.02', 'nombre' => 'Gestión de los procesos judiciales de la CGR', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA06'],
        ['cod_proceso' => 'PA06.03', 'nombre' => 'Gestión de los procesos arbitrales de la CGR', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA06'],
        ['cod_proceso' => 'PA06.04', 'nombre' => 'Defensa legal de los colaboradores y ex colaboradores', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA06'],
        ['cod_proceso' => 'PA06.05', 'nombre' => 'Absolución de consultas internas de carácter jurídico', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA06'],
        // Procesos bajo 'PA07'
        ['cod_proceso' => 'PA07.01', 'nombre' => 'Gestión de prevención de riesgos de desastres', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA07'],
        ['cod_proceso' => 'PA07.02', 'nombre' => 'Operación de la gestión de la seguridad', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA07'],
        ['cod_proceso' => 'PA07.03', 'nombre' => 'Fomento de una cultura de seguridad', 'tipo_proceso' => 'Apoyo', 'cod_proceso_padre' => 'PA07'],
    ];
            DB::table('procesos')->insert($procesos);
    }
}