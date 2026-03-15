<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Incident;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncidentFactory extends Factory
{
    protected $model = Incident::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'room_id' => Room::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'emergency']),
            'status' => 'open',
            'reported_by' => User::factory(),
        ];
    }
}
