<?php

namespace Database\Factories;

use App\Models\Medicamento;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prescricao>
 */
class PrescricaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'paciente_id' => Paciente::factory(),
            'medicamento_id' => Medicamento::factory(),
            'dose' => fake()->randomElement(['1 comprimido', '5ml', '1 ampola']),
            'horario' => fake()->randomElement(['manha', 'tarde']),
            'ativa' => true,
            'prescrito_por' => null,
        ];
    }
}
