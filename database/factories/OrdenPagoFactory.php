<?php

namespace Database\Factories;

use App\Models\OrdenPago;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrdenPago>
 */
class OrdenPagoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_tramite' => fake()->words(3, true),
            'precio_tramite' => fake()->numberBetween(100, 5000),
            'numero_cri' => fake()->unique()->numberBetween(100000, 999999),
            'orden_estatus' => 1,
            'folio_pago' => null,
            'fk_tramite' => null,
        ];
    }
}
