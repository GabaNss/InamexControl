<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // E-mail separado do login, usado exclusivamente para recuperação de senha.
            // Null = usuário ainda não fez o primeiro acesso.
            $table->string('recovery_email')->nullable()->unique()->after('email');
            // Forçar troca de senha no próximo login (true para novos usuários criados pelo admin).
            $table->boolean('must_change_password')->default(false)->after('recovery_email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['recovery_email', 'must_change_password']);
        });
    }
};
