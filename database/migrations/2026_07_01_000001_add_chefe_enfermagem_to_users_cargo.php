<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_cargo_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_cargo_check CHECK (cargo IN ('pendente','admin','medico','enfermeiro','chefe_enfermagem','tecnico','diretor'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_cargo_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_cargo_check CHECK (cargo IN ('pendente','admin','medico','enfermeiro','tecnico','diretor'))");
        }
    }
};
