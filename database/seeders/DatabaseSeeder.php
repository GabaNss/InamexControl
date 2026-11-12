<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // As senhas NAO ficam no codigo (repositorio pode ser publico). Sao lidas
        // do ambiente: defina ADMIN_PASSWORD e SEED_DEFAULT_PASSWORD no .env antes
        // de rodar o seeder. Sem elas, o seeder aborta — nunca cria conta com senha
        // padrao fraca/conhecida.
        $adminPassword = config('seeding.admin_password');
        $defaultPassword = config('seeding.default_password');

        if (! $adminPassword || ! $defaultPassword) {
            $this->command?->error(
                'Defina ADMIN_PASSWORD e SEED_DEFAULT_PASSWORD no .env antes de rodar o DatabaseSeeder.'
            );

            return;
        }

        $senha = Hash::make($defaultPassword);

        // Conta administradora do sistema — dispensada do fluxo de primeiro acesso.
        User::updateOrCreate(
            ['email' => 'adm@inamex.com'],
            [
                'name'                 => 'Administrador',
                'password'             => Hash::make($adminPassword),
                'cargo'                => 'admin',
                'ativo'                => true,
                'must_change_password' => false,
                'recovery_email'       => null,
                'turno_inicio'         => null,
                'turno_fim'            => null,
            ]
        );

        // Contas genéricas de gestão (nomes reais serão atualizados pelo painel).
        User::updateOrCreate(
            ['email' => 'diretoria@inamex.com'],
            [
                'name'                 => 'Diretoria',
                'password'             => $senha,
                'cargo'                => 'diretor',
                'ativo'                => true,
                'must_change_password' => true,
                'recovery_email'       => null,
                'turno_inicio'         => null,
                'turno_fim'            => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'administracao@inamex.com'],
            [
                'name'                 => 'Administração',
                'password'             => $senha,
                'cargo'                => 'diretor',
                'ativo'                => true,
                'must_change_password' => true,
                'recovery_email'       => null,
                'turno_inicio'         => null,
                'turno_fim'            => null,
            ]
        );

        // Funcionários do Sistema de Enfermagem
        $funcionarios = [
            // Enfermeiras
            ['name' => 'Ana Carolina de Souza',           'email' => 'ana.carolina@inamex.com',    'cargo' => 'enfermeiro'],
            ['name' => 'Erica Francisco Garcia',          'email' => 'erica.francisco@inamex.com', 'cargo' => 'enfermeiro'],
            ['name' => 'Márcia Roberta Fernandes',        'email' => 'marcia.roberta@inamex.com',  'cargo' => 'enfermeiro'],
            ['name' => 'Regiane Aparecida de Oliveira',   'email' => 'regiane.aparecida@inamex.com','cargo' => 'enfermeiro'],

            // Técnicos de Enfermagem
            ['name' => 'Ana Paula da Cruz Souza',         'email' => 'ana.paula@inamex.com',       'cargo' => 'tecnico'],
            ['name' => 'Claudinéia Maria Ribeiro Rueda',  'email' => 'claudineia.maria@inamex.com','cargo' => 'tecnico'],
            ['name' => 'Eliane Aparecida de Oliveira',    'email' => 'eliane.aparecida@inamex.com','cargo' => 'tecnico'],
            ['name' => 'Elisangela Cristina Santos da Silva','email'=>'elisangela.cristina@inamex.com','cargo'=>'tecnico'],
            ['name' => 'Inajara Elvira Delalata',         'email' => 'inajara.elvira@inamex.com',  'cargo' => 'tecnico'],
            ['name' => 'Karla de Souza Ferreira',         'email' => 'karla.souza@inamex.com',     'cargo' => 'tecnico'],
            ['name' => 'Luciane Gonçalves Rocha',         'email' => 'luciane.goncalves@inamex.com','cargo'=> 'tecnico'],
            ['name' => 'Natália da Silva Fim',            'email' => 'natalia.silva@inamex.com',   'cargo' => 'tecnico'],
            ['name' => 'Samara Cristina da Silva Candida','email' => 'samara.cristina@inamex.com', 'cargo' => 'tecnico'],
            ['name' => 'Sueli Fregonez',                  'email' => 'sueli.fregonez@inamex.com',  'cargo' => 'tecnico'],

            // Médicos
            ['name' => 'Kelin Keiko Kinsui',              'email' => 'kelin.keiko@inamex.com',     'cargo' => 'medico'],
            ['name' => 'Diego Henrique Montoia',          'email' => 'diego.henrique@inamex.com',  'cargo' => 'medico'],

            // Outros profissionais de saúde (acesso médico para visualização de prontuários)
            ['name' => 'Marya Clara Carvalho',            'email' => 'marya.clara@inamex.com',     'cargo' => 'medico'], // Farmacêutica
            ['name' => 'Vanessa Fernandes de Melo G. Ferraz','email'=>'vanessa.fernandes@inamex.com','cargo'=>'medico'], // Nutricionista
            ['name' => 'Larissa Bonutti Trevizoli',       'email' => 'larissa.bonutti@inamex.com', 'cargo' => 'medico'], // Fisioterapeuta
            ['name' => 'Débora Cristina Inácio',          'email' => 'debora.cristina@inamex.com', 'cargo' => 'medico'], // Psicóloga
            ['name' => 'Liliani Angélica Sabino Medeiros','email' => 'liliani.angelica@inamex.com','cargo' => 'medico'], // Assistente Social
        ];

        foreach ($funcionarios as $dados) {
            User::updateOrCreate(
                ['email' => $dados['email']],
                [
                    'name'                 => $dados['name'],
                    'password'             => $senha,
                    'cargo'                => $dados['cargo'],
                    'ativo'                => true,
                    'must_change_password' => true,
                    'recovery_email'       => null,
                    'turno_inicio'         => null,
                    'turno_fim'            => null,
                ]
            );
        }
    }
}
