<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos');
            $table->date('fecha_nacimiento');
            $table->string('expediente')->unique();
            $table->string('imagen_url');
            $table->string('imagen_public_id');

              // llaves foraneas de modulo
            $table->unsignedBigInteger('module_id')->nullable();  
            $table->foreign('module_id')
                ->references('id')
                ->on('modules')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
