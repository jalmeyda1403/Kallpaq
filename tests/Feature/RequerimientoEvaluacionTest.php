<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Requerimiento;
use App\Models\User;
use App\Models\Proceso;
use Spatie\Permission\Models\Role;

class RequerimientoEvaluacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Especialista']);

        // Create a user and assign role
        $this->user = User::factory()->create();
        $this->user->assignRole('Admin');

        // Create a process
        $this->proceso = Proceso::factory()->create();

        // Create a requerimiento
        $this->requerimiento = Requerimiento::factory()->create([
            'estado' => 'aprobado',
            'proceso_id' => $this->proceso->id,
        ]);
    }

    /** @test */
    public function it_can_store_an_evaluation_for_a_requerimiento()
    {
        // Acting as the authenticated user
        $this->actingAs($this->user);

        $evaluacionData = [
            'actividades' => 5,
            'areas' => 3,
            'requisitos' => 10,
            'documentacion' => 'alta',
            'impacto' => 'medio',
            'complejidad_valor' => 75,
            'complejidad_nivel' => 'alta',
        ];

        // Send post request to the endpoint
        $response = $this->postJson('/requerimientos/' . $this->requerimiento->id . '/evaluacion', $evaluacionData);

        // Assertions
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Evaluación guardada con éxito.']);

        // Assert that the evaluation was created in the database
        $this->assertDatabaseHas('requerimiento_evaluaciones', [
            'requerimiento_id' => $this->requerimiento->id,
            'num_actividades' => 5,
            'complejidad_nivel' => 'alta',
        ]);

        // Assert that the movimiento was created
        $this->assertDatabaseHas('requerimiento_movimientos', [
            'requerimiento_id' => $this->requerimiento->id,
            'estado' => 'evaluado',
            'user_id' => $this->user->id,
        ]);

        // Assert that the requerimiento complejidad was updated
        $this->assertDatabaseHas('requerimientos', [
            'id' => $this->requerimiento->id,
            'complejidad' => 'alta',
        ]);

        // Assert that the requerimiento estado has NOT changed
        $this->assertDatabaseHas('requerimientos', [
            'id' => $this->requerimiento->id,
            'estado' => 'aprobado',
        ]);
    }
}
