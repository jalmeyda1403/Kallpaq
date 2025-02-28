<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AreasComplianceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Insertar los datos de las áreas de compliance
        DB::table('areas_compliance')->insert([
            ['area_compliance_nombre' => 'Regulatorio sectorial', 'area_compliance_descripcion' => 'Normativas o compromisos relacionadas a los sectores de los cuales la CGR realiza control y vigilancia de la gestión fiscal'],
            ['area_compliance_nombre' => 'Laboral', 'area_compliance_descripcion' => 'Normativas o compromisos que se ocupa de los derechos y deberes de los trabajadores'],
            ['area_compliance_nombre' => 'Gobierno digital/Seguridad de la información/ ciberseguridad', 'area_compliance_descripcion' => 'Normativas o compromisos relacionadas al uso de las tecnologías de información y de comunicaciones'],
            ['area_compliance_nombre' => 'Igualdad', 'area_compliance_descripcion' => 'Normativas o compromisos que permiten promover y garantizar la igualdad de oportunidades entre mujeres y hombres'],
            ['area_compliance_nombre' => 'Regulatorio Financiero', 'area_compliance_descripcion' => 'Normativas o compromisos para supervisión de aspectos financieros'],
            ['area_compliance_nombre' => 'Ambiental', 'area_compliance_descripcion' => 'Normativas o compromisos relacionados a aspectos de gestión y cuidado del medio ambiente'],
            ['area_compliance_nombre' => 'Seguridad y Salud Ocupacional', 'area_compliance_descripcion' => 'Normativas o compromisos relacionados a aspectos de gestión y prevención de seguridad y salud en el trabajo'],
            ['area_compliance_nombre' => 'Compromiso Social / Convenios', 'area_compliance_descripcion' => 'Compromisos adquiridos voluntariamente o requisitos establecidos por los grupos de interés que se convierten en un aspecto a "cumplir" por la CGR'],
            ['area_compliance_nombre' => 'Información y Transparencia', 'area_compliance_descripcion' => 'Normativas o compromisos con el objeto de garantizar el derecho de acceso a la información en pos de cualquier autoridad, entidad, órgano o organismo que reciba y ejerza recursos públicos'],
            ['area_compliance_nombre' => 'Fraude Interno', 'area_compliance_descripcion' => 'Normativas o compromisos que rigen control para el posible fraude dentro de la CGR'],
            ['area_compliance_nombre' => 'Organizacional', 'area_compliance_descripcion' => 'Normativas o compromisos relacionados con los procesos transversales o generales de la CGR'],
            ['area_compliance_nombre' => 'Conflicto de Intereses', 'area_compliance_descripcion' => 'Normativas o compromisos relacionados con el control y actuación ante casos de conflicto de interés dentro del marco de la función pública'],
            ['area_compliance_nombre' => 'Anticorrupción', 'area_compliance_descripcion' => 'Normativas o compromisos para establecer un sentido preventivo ante actos los posibles actos de corrupción en la función pública, así como establecer un marco de acción sancionador ante los casos de corrupción detectados.'],
            ['area_compliance_nombre' => 'Control Gubernamental', 'area_compliance_descripcion' => 'Normativas de la CGR como ente de "control", para la supervisión, vigilancia, verificación de los actos y resultados de la gestión pública, en atención al grado de eficiencia, eficacia, transparencia y economía en el uso y destino de los recursos y bienes del Estado'],
            ['area_compliance_nombre' => 'Control Social', 'area_compliance_descripcion' => 'Normativas o lineamientos relacionados al derecho y deber que tienen todos y todos los ciudadanos, individual o colectivamente, a vigilar y fiscalizar la gestión pública con el fin de acompañar el cumplimiento de los fines del Estado'],
        ]);
    }
}
