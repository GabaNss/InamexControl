<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicamento>
 */
class MedicamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->word(),
            'concentracao' => fake()->randomElement(['5mg', '10mg', '500mg', '1g']),
            'via_administracao' => fake()->randomElement(['oral', 'intramuscular', 'intravenosa', 'subcutanea']),
            'ativo' => true,
        ];
    }
}
