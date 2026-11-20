<?php

namespace App\Http\Controllers;

use App\Models\Modulo;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ModuloController extends Controller
{
    public function apiIndex(): JsonResponse
    {
        return response()->json(Modulo::with('profesores')->orderBy('id')->paginate(15));
    }

    public function apiStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:255', 'unique:modules,codigo'],
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $modulo = Modulo::create($validated);

        return response()->json(['data' => $modulo->load('profesores')], 201);
    }

    public function apiShow(int $id): JsonResponse
    {
        $modulo = Modulo::findOrFail($id);

        return response()->json(['data' => $modulo->load('profesores')]);
    }

    public function apiUpdate(Request $request, int $id): JsonResponse
    {
        $modulo = Modulo::findOrFail($id);

        $validated = $request->validate([
            'codigo' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('modules', 'codigo')->ignore($modulo->id),
            ],
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        $modulo->update($validated);

        return response()->json(['data' => $modulo->load('profesores')]);
    }

    public function apiSyncProfesores(Request $request, int $id): JsonResponse
    {
        $modulo = Modulo::findOrFail($id);

        $validated = $request->validate([
            'profesores' => ['required', 'array'],
            'profesores.*' => ['integer', 'distinct', 'exists:teachers,id'],
        ]);

        DB::transaction(function () use ($modulo, $validated, $id): void {
            $profesores = $validated['profesores'];

            $modulo->profesores()
                ->when($profesores !== [], fn ($query) => $query->whereNotIn('id', $profesores))
                ->update(['module_id' => null]);

            if ($profesores !== []) {
                Teacher::whereKey($profesores)->update(['module_id' => $id]);
            }
        });

        return response()->json(['data' => $modulo->load('profesores')]);
    }

    public function apiDestroy(int $id): JsonResponse
    {
        $modulo = Modulo::findOrFail($id);
        $modulo->delete();

        return response()->json(null, 204);
    }
}
