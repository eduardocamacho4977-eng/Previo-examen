<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_teacher', function (Blueprint $table) {
            $table->id();
                
            // llaves fonraneas de courses y teachers
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('teacher_id');

            // Referencia modulos
            $table->foreign('module_id')
                ->references('id')
                ->on('modules')->onDelete('cascade');

            // Referencia teachers
            $table->foreign('teacher_id')
                ->references('id')
                ->on('teachers')->onDelete('cascade');

                 $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_teacher');
    }
};
