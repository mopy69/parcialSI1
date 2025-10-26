<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_assignments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('coordinador_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('docente_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('timeslot_id')->constrained('timeslots')->cascadeOnDelete();
            $table->foreignId('course_offering_id')->constrained('course_offerings')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();

            $table->unique(['coordinador_id','docente_id','timeslot_id','course_offering_id','classroom_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_assignments');
    }
};
