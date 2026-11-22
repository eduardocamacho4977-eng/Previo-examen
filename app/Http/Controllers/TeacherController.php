<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;

class TeacherController extends Controller
{
    public function apiIndex()
    {
        $teachers = Teacher::all();

        return response()->json($teachers);
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
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'cedula' => 'required|string|max:255|unique:teachers,cedula',
        ]);

        $teacher = Teacher::create($validated);

        return response()->json($teacher, 201);
    }

    public function apiStore(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function apiShow($id)
    {
        $teacher = Teacher::findOrFail($id);

        return response()->json($teacher);
    }

    public function apiUpdate(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'telefono' => 'sometimes|nullable|string|max:30',
            'cedula' => 'sometimes|required|string|max:255',
        ]);

        $teacher->update($validated);

        return response()->json($teacher->fresh());
    }

    public function apiDestroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();

        return response()->json(['message' => 'Profesor eliminado correctamente.']);
    }
}
