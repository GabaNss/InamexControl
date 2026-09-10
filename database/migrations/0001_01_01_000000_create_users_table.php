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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // Cargo define o papel do usuario no sistema e é a base das Gates/Policies
            // (admin, medico, enfermeiro, tecnico, diretor). Ver App\Models\User::temCargo().
            // 'pendente' é o estado de quem se auto-cadastrou (ver RegisteredUserController)
            // e ainda não recebeu um cargo de verdade do admin — não tem nenhuma
            // permissão (todas as Policies/Gates exigem um cargo real, nunca 'pendente').
            $table->enum('cargo', ['pendente', 'admin', 'medico', 'enfermeiro', 'tecnico', 'diretor'])
                ->default('pendente');
            // Permite desativar o acesso de um usuario (ex: afastamento) sem excluir o historico de auditoria.
            $table->boolean('ativo')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
