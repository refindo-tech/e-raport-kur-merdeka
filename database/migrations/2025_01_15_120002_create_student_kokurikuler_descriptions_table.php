<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_kokurikuler_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('tapel_id')->constrained('tapels')->cascadeOnDelete();
            $table->text('tinggi_description')->nullable();
            $table->text('rendah_description')->nullable();
            $table->text('final_description')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'tapel_id'], 'student_kokurikuler_description_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_kokurikuler_descriptions');
    }
};

