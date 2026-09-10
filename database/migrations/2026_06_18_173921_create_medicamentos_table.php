<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catalogo de medicamentos disponiveis para prescricao.
     */
    public function up(): void
    {
        Schema::create('medicamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('concentracao'); // ex: "500mg", "10mg/ml"
            // Via de administracao: oral, intramuscular, intravenosa, subcutanea, topica, etc.
            $table->string('via_administracao');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicamentos');
    }
};
