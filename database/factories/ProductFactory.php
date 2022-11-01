<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->unique()->text($maxNbChars = 10),
            'price' => fake()->randomFloat($nbMaxDecimals = 2, $min = 10, $max = 1000000),
            'expiration' => fake()->dateTimeBetween('+1 year', '+2 years')->format('Y-m-d'),
            'user_id' => User::factory(),
        ];
    }
}
