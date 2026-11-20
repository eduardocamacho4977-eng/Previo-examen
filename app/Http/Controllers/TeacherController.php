<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function apiIndex(): JsonResponse
    {
        return response()->json(Teacher::with('modulo')->orderBy('id')->paginate(15));
    }

    public function apiStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'cedula' => ['required', 'string', 'max:255', 'unique:teachers,cedula'],
            'module_id' => ['sometimes', 'nullable', 'integer', 'exists:modules,id'],
        ]);

        $teacher = Teacher::create($validated);

        return response()->json(['data' => $teacher->load('modulo')], 201);
    }

    public function apiShow(int $id): JsonResponse
    {
        $teacher = Teacher::with('modulo')->findOrFail($id);

        return response()->json(['data' => $teacher]);
    }

    public function apiUpdate(Request $request, int $id): JsonResponse
    {
        $teacher = Teacher::findOrFail($id);

        $validated = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'apellido' => ['sometimes', 'required', 'string', 'max:255'],
            'telefono' => ['sometimes', 'nullable', 'string', 'max:30'],
            'module_id' => ['sometimes', 'nullable', 'integer', 'exists:modules,id'],
            'cedula' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('teachers', 'cedula')->ignore($teacher->id),
            ],
        ]);

        $teacher->update($validated);

        return response()->json(['data' => $teacher->load('modulo')]);
    }

    public function apiDestroy(int $id): JsonResponse
    {
        Teacher::findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}
