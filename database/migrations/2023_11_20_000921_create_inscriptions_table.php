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
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_inscripcion');
            $table->boolean('pago');
            $table->string('status_inscripcion');
            $table->string('tipo_inscripcion');
            $table->string('planilla_ins');
            $table->string('autorizacion_pastoral');
            $table->string('foto');
            $table->string('cedula');
            $table->string('estudios_cursados')->nullable();
            $table->string('estudios_cursados_eclesiasticos')->nullable();
            $table->unsignedBigInteger('person_id');
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade');
            $table->unsignedBigInteger('chair_module_id');
            $table->foreign('chair_module_id')->references('id')->on('chair_module')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
