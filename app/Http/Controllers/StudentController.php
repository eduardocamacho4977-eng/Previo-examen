<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Cloudinary\Cloudinary;

class StudentController extends Controller
{
    public function apiIndex()
    {
        $students = Student::all();

        return response()->json($students->load(['modulo']));
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
            'apellidos' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'expediente' => 'required|string|max:255|unique:students,expediente',
            'module_id' => 'nullable|exists:modules,id',
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $file = $request->file('imagen');

        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => ['secure' => true],
        ]);

        $resultado = $cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder' => 'students',
                'resource_type' => 'image',
            ]
        );

        $student = Student::create([
            'nombre' => $request->nombre,
            'apellidos' => $request->apellidos,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'expediente' => $request->expediente,
            'module_id' => $request->module_id,
            'imagen_url' => $resultado['secure_url'],
            'imagen_public_id' => $resultado['public_id'],
        ]);

        return response()->json($student->load(['modulo']), 201);
    }

    public function apiStore(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function apiShow($id)
    {
        $student = Student::findOrFail($id);

        return response()->json($student->load(['modulo']));
    }

    public function apiUpdate(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'apellidos' => 'sometimes|required|string|max:255',
            'fecha_nacimiento' => 'sometimes|required|date|before_or_equal:today',
            'expediente' => 'sometimes|required|string|max:255',
            'module_id' => 'sometimes|nullable|exists:modules,id',
        ]);

        $student->update($validated);

        return response()->json($student->fresh()->load(['modulo']));
    }

    public function apiDestroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json(['message' => 'Estudiante eliminado correctamente.']);
    }
}
