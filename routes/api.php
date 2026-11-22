<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\TeacherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('profesores', [TeacherController::class, 'apiIndex']);
Route::post('profesores', [TeacherController::class, 'apiStore']);
Route::get('profesores/{id}', [TeacherController::class, 'apiShow'])->name('profesor.show');
Route::put('profesores/{id}', [TeacherController::class, 'apiUpdate'])->name('profesor.update');
Route::delete('profesores/{id}', [TeacherController::class, 'apiDestroy'])->name('profesor.destroy');

Route::get('alumnos', [StudentController::class, 'apiIndex']);
Route::post('alumnos', [StudentController::class, 'apiStore']);
Route::get('alumnos/{id}', [StudentController::class, 'apiShow'])->name('alumno.show');
Route::put('alumnos/{id}', [StudentController::class, 'apiUpdate'])->name('alumno.update');
Route::delete('alumnos/{id}', [StudentController::class, 'apiDestroy'])->name('alumno.destroy');

Route::get('modulos', [ModuloController::class, 'apiIndex']);
Route::post('modulos', [ModuloController::class, 'apiStore']);
Route::get('modulos/{id}', [ModuloController::class, 'apiShow'])->name('modulo.show');
Route::put('modulos/{id}', [ModuloController::class, 'apiUpdate'])->name('modulo.update');
Route::delete('modulos/{id}', [ModuloController::class, 'apiDestroy'])->name('modulo.destroy');

