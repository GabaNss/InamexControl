<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Momento em que o usuario aceitou o termo de uso/confidencialidade.
            // Null = ainda nao aceitou (sera exigido no proximo acesso). Quando a
            // versao do termo mudar (config termos.versao), compara-se esta data
            // com a data de publicacao da versao para reexigir o aceite.
            $table->timestamp('termos_aceitos_em')->nullable()->after('must_change_password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('termos_aceitos_em');
        });
    }
};
