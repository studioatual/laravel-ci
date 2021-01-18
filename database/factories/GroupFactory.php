<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => rand(100, 999),
            'name' => $this->faker->company,
            'cnpj' => $this->faker->cnpj(false),
            'type' => rand(0, 3),
            'active' => $this->faker->boolean(rand(0, 100)),
        ];
    }
}
