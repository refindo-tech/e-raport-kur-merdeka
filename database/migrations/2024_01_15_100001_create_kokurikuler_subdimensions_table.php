<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kokurikuler_subdimensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimension_id')->constrained('kokurikuler_dimensions')->onDelete('cascade');
            $table->string('name');
            $table->text('berkembang');
            $table->text('cakap');
            $table->text('mahir');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kokurikuler_subdimensions');
    }
};

