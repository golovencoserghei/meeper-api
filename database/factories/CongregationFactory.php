<?php

namespace Database\Factories;

use App\Models\Congregation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CongregationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Congregation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company
        ];
    }
}
