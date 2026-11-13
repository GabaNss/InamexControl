<?php

return [
    // Credenciais usadas apenas pelo DatabaseSeeder. Lidas do ambiente para nunca
    // ficarem no codigo-fonte (o repositorio pode ser publico). Usar config() em
    // vez de env() direto no seeder garante funcionamento mesmo com config cacheada
    // em producao (env() retorna null quando a config esta em cache).
    'admin_password' => env('ADMIN_PASSWORD'),
    'default_password' => env('SEED_DEFAULT_PASSWORD'),
];
