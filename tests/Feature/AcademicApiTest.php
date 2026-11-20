<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Modulo;
use App\Models\Profesor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AcademicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_professors_and_modules_support_crud_and_validation(): void
    {
        $profesorResponse = $this->postJson('/api/profesores', [
            'nombre' => 'Ana',
            'apellido' => 'Pérez',
            'telefono' => '555123',
            'cedula' => 'CC-100',
        ]);
        $profesorResponse->assertCreated()->assertJsonPath('data.cedula', 'CC-100');

        $profesorId = $profesorResponse->json('data.id');
        $this->getJson('/api/profesor/'.$profesorId)
            ->assertOk()
            ->assertJsonPath('data.id', $profesorId);
        $this->putJson('/api/profesores/'.$profesorId, ['apellido' => 'Gómez'])
            ->assertOk()
            ->assertJsonPath('data.apellido', 'Gómez');
        $this->getJson('/api/profesores')->assertOk()->assertJsonPath('data.0.id', $profesorId);
        $this->deleteJson('/api/profesores/'.$profesorId)->assertNoContent();

        $moduloResponse = $this->postJson('/api/modulos', [
            'codigo' => 'ADSO-01',
            'nombre' => 'Bases de datos',
        ]);
        $moduloResponse->assertCreated()->assertJsonPath('data.codigo', 'ADSO-01');

        $this->postJson('/api/modulos', ['codigo' => 'ADSO-01', 'nombre' => 'Duplicado'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('codigo');
    }

    public function test_student_photo_crud_and_module_delegate_relationships(): void
    {
        Storage::fake('public');

        $profesor = Profesor::create([
            'nombre' => 'Luis',
            'apellido' => 'Díaz',
            'cedula' => 'CC-200',
        ]);
        $modulo = Modulo::create(['codigo' => 'ADSO-02', 'nombre' => 'Programación']);
        $otroModulo = Modulo::create(['codigo' => 'ADSO-03', 'nombre' => 'Pruebas']);

        $studentResponse = $this->post('/api/alumnos', [
            'nombre' => 'Sofía',
            'apellidos' => 'Ruiz',
            'fecha_nacimiento' => '2005-04-20',
            'expediente' => 'EXP-001',
            'foto' => UploadedFile::fake()->create('alumna.jpg', 10, 'image/jpeg'),
        ], ['Accept' => 'application/json']);
        $studentResponse->assertCreated();

        $alumnoId = $studentResponse->json('data.id');
        $fotoAnterior = $studentResponse->json('data.foto');
        $this->assertSame(
            Storage::disk('public')->url($fotoAnterior),
            $studentResponse->json('data.foto_url')
        );
        Storage::disk('public')->assertExists($fotoAnterior);

        $this->put('/api/alumnos/'.$alumnoId, [
            'foto' => UploadedFile::fake()->create('nueva.jpg', 10, 'image/jpeg'),
        ], ['Accept' => 'application/json'])->assertOk();
        Storage::disk('public')->assertMissing($fotoAnterior);

        $otroAlumno = Alumno::create([
            'nombre' => 'Mateo',
            'apellidos' => 'López',
            'fecha_nacimiento' => '2004-01-15',
            'expediente' => 'EXP-002',
        ]);

        $this->putJson('/api/modulos/'.$modulo->id.'/profesores', [
            'profesores' => [$profesor->id],
        ])->assertOk()->assertJsonPath('data.profesores.0.id', $profesor->id);

        $this->putJson('/api/alumnos/'.$alumnoId.'/modulos', [
            'modulos' => [$modulo->id, $otroModulo->id],
        ])->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/alumnos/'.$alumnoId.'/modulos')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/modulos/'.$modulo->id.'/alumnos')->assertOk()->assertJsonCount(1, 'data');

        $this->putJson('/api/alumnos/'.$alumnoId.'/delegado', [
            'delegado_id' => $otroAlumno->id,
        ])->assertOk()->assertJsonPath('data.delegado.id', $otroAlumno->id);
        $this->putJson('/api/alumnos/'.$alumnoId.'/delegado', [
            'delegado_id' => $alumnoId,
        ])->assertUnprocessable();

        $this->deleteJson('/api/alumnos/'.$alumnoId)->assertNoContent();
        $this->assertDatabaseMissing('students', ['id' => $alumnoId]);
    }
}
