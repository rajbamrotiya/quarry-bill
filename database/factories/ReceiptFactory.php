<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\MaterialType;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Receipt>
 */
class ReceiptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gross = fake()->numberBetween(15000, 50000);
        $tare = fake()->numberBetween(5000, 15000);

        return [
            'client_id' => Client::factory(),
            'vehicle_number' => 'GJ-'.$this->faker->numerify('##-??-####'),
            'material_type_id' => MaterialType::factory(),
            'royalty_number' => $this->faker->optional()->bothify('ROY-####-????'),
            'date' => $this->faker->dateTimeBetween('now', 'now')->format('Y-m-d'),
            'time' => $this->faker->time('H:i'),
            'gross_weight' => $gross,
            'tare_weight' => $tare,
            'net_weight' => $gross - $tare,
            'payment_value' => $this->faker->optional()->randomFloat(2, 500, 5000),
            'payment_type' => $this->faker->randomElement(['cash', 'online']),
            'remarks' => $this->faker->optional()->sentence(),
        ];
    }
}
