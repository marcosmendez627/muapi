<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->text(),
            'image' => $this->faker->imageUrl(),
            'brand' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 100, 2000),
            'price_sale' => $this->faker->randomFloat(2, 100, 2000),
            'category' => $this->faker->word(),
            'stock' => $this->faker->numberBetween(0, 1000)
        ];
    }
}
