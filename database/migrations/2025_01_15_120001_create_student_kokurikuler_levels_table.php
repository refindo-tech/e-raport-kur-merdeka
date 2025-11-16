<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_kokurikuler_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('tapel_id')->constrained('tapels')->cascadeOnDelete();
            $table->foreignId('dimension_id')->constrained('kokurikuler_dimensions')->cascadeOnDelete();
            $table->enum('level', ['tinggi', 'rendah']);
            $table->timestamps();

            $table->unique(['student_id', 'tapel_id', 'dimension_id'], 'student_dimension_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_kokurikuler_levels');
    }
};

