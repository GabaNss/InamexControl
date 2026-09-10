<?php

namespace Database\Factories;

use App\Models\Prescricao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RegistroMedicacao>
 */
class RegistroMedicacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prescricao_id' => Prescricao::factory(),
            'data' => today(),
            'turno' => fake()->randomElement(['manha', 'tarde']),
            'administrado' => false,
            'observacao' => null,
            'usuario_id' => null,
        ];
    }
}
