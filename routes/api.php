<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ApiRelationController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\ProfesorController;

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

Route::get('profesores', [ProfesorController::class, 'apiIndex']);
Route::post('profesores', [ProfesorController::class, 'apiStore']);
Route::put('profesores/{id}', [ProfesorController::class, 'apiUpdate']);
Route::patch('profesores/{id}', [ProfesorController::class, 'apiUpdate']);
Route::delete('profesores/{id}', [ProfesorController::class, 'apiDestroy']);
Route::get('profesor/{id}', [ProfesorController::class, 'apiShow'])->name('profesor.show');
Route::put('profesor/{id}', [ProfesorController::class, 'apiUpdate'])->name('profesor.update');
Route::delete('profesor/{id}', [ProfesorController::class, 'apiDestroy'])->name('profesor.destroy');

Route::get('alumnos', [AlumnoController::class, 'apiIndex']);
Route::post('alumnos', [AlumnoController::class, 'apiStore']);
Route::get('alumno/{id}', [AlumnoController::class, 'apiShow'])->name('alumno.show');
Route::put('alumno/{id}', [AlumnoController::class, 'apiUpdate'])->name('alumno.update');
Route::delete('alumno/{id}', [AlumnoController::class, 'apiDestroy'])->name('alumno.destroy');

Route::get('modulos', [ModuloController::class, 'apiIndex']);
Route::post('modulos', [ModuloController::class, 'apiStore']);
Route::put('modulos/{id}', [ModuloController::class, 'apiUpdate']);
Route::patch('modulos/{id}', [ModuloController::class, 'apiUpdate']);
Route::delete('modulos/{id}', [ModuloController::class, 'apiDestroy']);
Route::put('modulos/{id}/profesores', [ModuloController::class, 'apiSyncProfesores']);
Route::get('modulo/{id}', [ModuloController::class, 'apiShow'])->name('modulo.show');
Route::put('modulo/{id}', [ModuloController::class, 'apiUpdate'])->name('modulo.update');
Route::delete('modulo/{id}', [ModuloController::class, 'apiDestroy'])->name('modulo.destroy');
