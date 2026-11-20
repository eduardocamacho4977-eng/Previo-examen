<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class AlumnoController extends Controller
{
    public function apiIndex(): JsonResponse
    {
        return response()->json(
            Alumno::with(['modulos', 'delegado'])->orderBy('id')->paginate(15)
        );
    }

    public function apiStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'expediente' => ['required', 'string', 'max:255', 'unique:students,expediente'],
            'foto' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'delegado_id' => ['sometimes', 'nullable', 'integer', 'exists:students,id'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->storePhoto($request);
        }

        $alumno = Alumno::create($validated);

        return response()->json([
            'data' => $alumno->load(['modulos', 'delegado']),
        ], 201);
    }

    public function apiShow(int $id): JsonResponse
    {
        $alumno = Alumno::findOrFail($id);

        return response()->json(['data' => $alumno->load(['modulos', 'delegado'])]);
    }

    public function apiUpdate(Request $request, int $id): JsonResponse
    {
        $alumno = Alumno::findOrFail($id);

        $validated = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'apellidos' => ['sometimes', 'required', 'string', 'max:255'],
            'fecha_nacimiento' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'expediente' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('students', 'expediente')->ignore($alumno->id),
            ],
            'foto' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'delegado_id' => ['sometimes', 'nullable', 'integer', 'exists:students,id'],
        ]);

        if (isset($validated['delegado_id']) && (int) $validated['delegado_id'] === $alumno->id) {
            return response()->json([
                'message' => 'Un alumno no puede ser su propio delegado.',
                'errors' => ['delegado_id' => ['Selecciona otro alumno.']],
            ], 422);
        }

        $oldPhoto = $alumno->foto;
        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->storePhoto($request);
        }

        try {
            $alumno->update($validated);
        } catch (Throwable $exception) {
            if (isset($validated['foto']) && $validated['foto'] !== $oldPhoto) {
                Storage::disk('public')->delete($validated['foto']);
            }

            throw $exception;
        }

        if (isset($validated['foto']) && $oldPhoto && ! Storage::disk('public')->delete($oldPhoto)) {
            throw new RuntimeException('No fue posible eliminar la fotografía anterior.');
        }

        return response()->json(['data' => $alumno->load(['modulos', 'delegado'])]);
    }

    public function apiDestroy(int $id): JsonResponse
    {
        $alumno = Alumno::findOrFail($id);
        $photo = $alumno->foto;
        $alumno->delete();

        if ($photo && ! Storage::disk('public')->delete($photo)) {
            throw new RuntimeException('El alumno fue eliminado, pero no fue posible eliminar su fotografía.');
        }

        return response()->json(null, 204);
    }

    private function storePhoto(Request $request): string
    {
        $path = $request->file('foto')->store('alumnos', 'public');

        if (! $path) {
            throw new RuntimeException('No fue posible guardar la fotografía del alumno.');
        }

        return $path;
    }
}
