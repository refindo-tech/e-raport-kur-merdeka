<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_kokurikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('tapel_id')->constrained('tapels')->onDelete('cascade');
            $table->foreignId('subdimension_id')->constrained('kokurikuler_subdimensions')->onDelete('cascade');
            $table->enum('level', ['berkembang', 'cakap', 'mahir']);
            $table->text('description');
            $table->timestamps();
            
            $table->unique(['student_id', 'tapel_id', 'subdimension_id'], 'student_tapel_subdimension_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_kokurikuler');
    }
};

