<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\MaterialType;
use App\Models\BuyReceipt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BuyReceipt>
 */
class BuyReceiptFactory extends Factory
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
            'supplier_id' => Supplier::factory(),
            'vehicle_number' => 'GJ-'.$this->faker->numerify('##-??-####'),
            'material_type_id' => MaterialType::factory(),
            'royalty_number' => $this->faker->optional()->bothify('ROY-####-????'),
            'date' => $this->faker->dateTimeBetween('now', 'now')->format('Y-m-d'),
            'time' => $this->faker->time('H:i'),
            'gross_weight' => $gross,
            'tare_weight' => $tare,
            'net_weight' => $gross - $tare,
            'remarks' => $this->faker->optional()->sentence(),
        ];
    }
}
