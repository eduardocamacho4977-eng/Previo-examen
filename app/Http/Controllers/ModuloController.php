<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modulo;

class ModuloController extends Controller
{
    public function apiIndex()
    {
        $modulos = Modulo::all();

        return response()->json($modulos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
        ]);

        $modulo = Modulo::create($validated);

        return response()->json($modulo, 201);
    }

    public function apiStore(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Modulo  $modulo
     * @return \Illuminate\Http\Response
     */
    public function apiShow($id)
    {
        $modulo = Modulo::findOrFail($id);

        return response()->json($modulo);
    }

    public function apiUpdate(Request $request, $id)
    {
        $modulo = Modulo::findOrFail($id);

        $validated = $request->validate([
            'codigo' => 'sometimes|required|string|max:255',
            'nombre' => 'sometimes|required|string|max:255',
        ]);

        $modulo->update($validated);

        return response()->json($modulo->fresh());
    }

    public function apiSyncProfesores(Request $request, $id)
    {
        $modulo = Modulo::findOrFail($id);

        $validated = $request->validate([
            'profesores' => 'required|array',
            'profesores.*' => 'integer|distinct|exists:teachers,id',
        ]);

        $modulo->teachers()->sync($validated['profesores']);

        return response()->json($modulo->fresh());
    }

    public function apiDestroy($id)
    {
        $modulo = Modulo::findOrFail($id);
        $modulo->delete();

        return response()->json(['message' => 'Módulo eliminado correctamente.']);
    }
}
