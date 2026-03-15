<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTypeFactory extends Factory
{
    protected $model = RoomType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Standard', 'Deluxe', 'Executive']),
            'description' => $this->faker->sentence(),
            'price_per_night' => $this->faker->randomFloat(2, 50, 500),
            'max_guests' => $this->faker->numberBetween(1, 4),
            'is_active' => true,
        ];
    }
}
