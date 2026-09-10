<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Controla o "fechamento" do dia: uma linha por data. Enquanto encerrado = false,
     * os RegistroMedicacao daquela data podem ser editados pela equipe. Apos o
     * encerramento (automatico, via App\Console\Commands\EncerrarDiarioCommand
     * agendado para meia-noite em routes/console.php), os registros daquela data
     * tornam-se IMUTAVEIS — ver App\Policies\RegistroMedicacaoPolicy e
     * App\Services\RegistroMedicacaoService.
     */
    public function up(): void
    {
        Schema::create('diario_status', function (Blueprint $table) {
            $table->id();
            $table->date('data')->unique();
            $table->boolean('encerrado')->default(false);
            $table->timestamp('encerrado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diario_status');
    }
};
