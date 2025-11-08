<?php

namespace App\Helpers;

class RequerimientoHelper
{
    public static function getEvaluationOptions()
    {
        return [
            'actividades' => [
                1 => 'El proceso es lineal y sencillo, compuesto por menos de 5 actividades principales.',
                2 => 'El proceso tiene complejidad media, compuesto por 5 a 8 actividades principales, con 1-2 decisiones simples.',
                3 => 'El proceso es complejo, compuesto por 9 a 15 actividades principales, con múltiples puntos de decisión y bifurcaciones.',
                4 => 'El proceso es muy extenso y complejo, compuesto por más de 15 actividades, con bucles o subprocesos anidados.',
            ],
            'areas' => [
                1 => '1 unidad orgánica involucrada (el requerimiento es interno a una sola área).',
                2 => '2 a 3 unidades orgánicas involucradas. La coordinación es manejable.',
                3 => '4 a 5 unidades orgánicas involucradas. Requiere gestión formal de múltiples partes interesadas.',
                4 => 'Más de 5 unidades orgánicas involucradas, o es transversal a toda la organización.',
            ],
            'requisitos' => [
                1 => 'No hay requisitos normativos directos, o solo aplican políticas internas no críticas.',
                2 => 'Afectado por 1-2 normativas o regulaciones conocidas, con requisitos claros y poca ambigüedad.',
                3 => 'Afectado por 3 o más normativas, o por regulaciones de alta rigurosidad (ej. legales, financieras) que requieren revisión constante.',
                4 => 'Afectado por un marco regulatorio complejo, cambiante y/o contradictorio. Requiere validación y auditoría externa.',
            ],
            'documentacion' => [
                1 => 'Solo se requieren adecuar las actividades de un procedimiento o adecuar algún formato (ej. un acta o un email simple).',
                2 => 'Requiere la elaboración o modificación de más de un documento estándar (ej. un manual de usuario o una política simple).',
                3 => 'Requiere la elaboración de un nuevo manual o procedimientos completos, múltiples instructivos, o modificación de documentos críticos.',
                4 => 'Requiere la creación de un paquete documental formal y extenso, incluyendo documentación técnica, legal, de capacitación y aprobación a múltiples niveles.',
            ],
            'impacto' => [
                1 => 'El resultado afecta solo al área que lo ejecuta, o a un grupo muy reducido de usuarios.',
                2 => 'El resultado sirve de entrada o salida para 1-2 procedimientos adyacentes de bajo impacto.',
                3 => 'El resultado afecta un proceso clave de la organización o tiene una influencia directa en el servicio/producto principal.',
                4 => 'La modificación implica un cambio significativo en la forma de operar de la organización (afecta a clientes externos, impacta la misión de la empresa, genera cascadas de cambios).',
            ],
        ];
    }
}
