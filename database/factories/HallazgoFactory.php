<?php

namespace Database\Factories;

use App\Models\Hallazgo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HallazgoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Hallazgo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fechaIdentificacion = $this->faker->dateTimeBetween('-1 year', 'now');
        $hallazgoEstado = $this->faker->randomElement(['creado', 'asignado', 'desestimado', 'en proceso', 'concluido', 'evaluado', 'cerrado']);

        // Realistic descriptions for quality/anti-bribery findings
        $descripcionHallazgo = $this->faker->randomElement([
            "Se identificó que el procedimiento 'PR-CAL-001 Gestión de No Conformidades' no se aplica de manera consistente en el área de producción, resultando en registros incompletos de las acciones correctivas implementadas.",
            "La capacitación sobre la política antisoborno (POL-ASB-001) no ha sido completada por el 30% del personal de ventas, lo que representa un riesgo de incumplimiento de la normativa interna y externa.",
            "Los equipos de medición utilizados en el control de calidad (EQ-005, EQ-012) no cuentan con certificados de calibración vigentes, lo que pone en duda la fiabilidad de los resultados de inspección de productos terminados.",
            "No se encontraron evidencias de la revisión por la dirección del Sistema de Gestión de Calidad en el último semestre, contraviniendo el requisito de planificación y seguimiento del sistema.",
            "El proceso de selección de proveedores críticos (PRO-COM-003) no incluye una evaluación de riesgos de soborno, lo que podría exponer a la organización a proveedores con prácticas no éticas.",
            "Se detectaron inconsistencias en los registros de control de acceso a información confidencial (INF-SEG-002), lo que podría comprometer la seguridad de los datos de clientes y proyectos."
        ]);

        // ISO 9001 and ISO 37001 criteria
        $criterioNorma = $this->faker->randomElement([
            "ISO 9001:2015, Cláusula 10.2 No conformidad y acción correctiva",
            "ISO 37001:2016, Cláusula 8.2 Debida diligencia",
            "ISO 9001:2015, Cláusula 7.1.5 Recursos de seguimiento y medición",
            "ISO 9001:2015, Cláusula 9.3 Revisión por la dirección",
            "ISO 37001:2016, Cláusula 8.1 Planificación y control operacional",
            "ISO 9001:2015, Cláusula 7.5 Información documentada"
        ]);

        // Evidence related to the finding
        $evidenciaHallazgo = $this->faker->randomElement([
            "Informe de auditoría interna N° 2025-003, sección 4.2. Registros de acciones correctivas incompletos.",
            "Matriz de capacitación de personal de ventas, fecha de corte 31/10/2025. 30% de personal sin completar curso POL-ASB-001.",
            "Listado de equipos de medición y sus certificados de calibración. EQ-005 y EQ-012 con certificados vencidos desde 01/09/2025.",
            "Actas de reunión de revisión por la dirección. No se encontró acta correspondiente al segundo semestre de 2025.",
            "Procedimiento PRO-COM-003 'Selección y Evaluación de Proveedores'. No se menciona evaluación de riesgos de soborno.",
            "Registros de logs del sistema de gestión documental, periodo 01/09/2025 - 31/10/2025. Accesos no autorizados detectados."
        ]);

        return [
            'hallazgo_cod' => 'H-' . $this->faker->unique()->randomNumber(5),
            'informe_id' => $this->faker->optional()->randomNumber(3), // informe_id is nullable
            'especialista_id' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()), // especialista_id is nullable
            'auditor_id' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()), // auditor_id is nullable
            'emisor' => $this->faker->optional()->company, // emisor is nullable
            'facilitador_id' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()), // facilitador_id is nullable
            'hallazgo_resumen' => substr($descripcionHallazgo, 0, 100) . '...', // Summary of description
            'hallazgo_sig' => $this->faker->optional()->randomElements(['SGC', 'SGAS', 'SGCM', 'SGSI', 'SGCO'], $this->faker->numberBetween(1, 3)), // hallazgo_sig is nullable
            'hallazgo_descripcion' => $descripcionHallazgo,
            'hallazgo_criterio' => $criterioNorma,
            'hallazgo_evidencia' => $evidenciaHallazgo,
            'hallazgo_clasificacion' => $this->faker->randomElement(['NCM', 'Ncme', 'Obs', 'Odm']),
            'hallazgo_origen' => $this->faker->randomElement(['RD','IN','EX','SN','GI','GR','SC','OT']),
            'hallazgo_origen_ot' => $this->faker->optional()->word,
            'hallazgo_avance' => $this->faker->optional()->numberBetween(0, 100), // hallazgo_avance is nullable
            'hallazgo_tipo_cierre' => $this->faker->optional()->randomElement(['con eficacia', 'sin eficacia']), // hallazgo_tipo_cierre is nullable
            'hallazgo_estado' => $this->faker->optional()->randomElement(['creado','asignado','desestimado','en proceso','concluido','evaluado','cerrado']), // hallazgo_estado is nullable
            'hallazgo_fecha_identificacion' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'), // hallazgo_fecha_identificacion is nullable
            'hallazgo_fecha_asignacion' => $this->faker->optional()->dateTimeBetween($fechaIdentificacion, 'now'),
            'hallazgo_fecha_desestimacion' => $hallazgoEstado === 'desestimado' ? $this->faker->optional()->dateTimeBetween($fechaIdentificacion, 'now') : null,
            'hallazgo_fecha_conclusion' => $hallazgoEstado === 'concluido' ? $this->faker->optional()->dateTimeBetween($fechaIdentificacion, 'now') : null,
            'hallazgo_fecha_evaluacion' => $hallazgoEstado === 'cerrado' ? $this->faker->optional()->dateTimeBetween($fechaIdentificacion, 'now') : null,
            'hallazgo_fecha_cierre' => $hallazgoEstado === 'cerrado' ? $this->faker->optional()->dateTimeBetween($fechaIdentificacion, 'now') : null,
            'hallazgo_ciclo' => $this->faker->numberBetween(1, 3),
        ];
    }
}
