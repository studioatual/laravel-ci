<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Person::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'group_id' => 1,
            'code' => rand(100, 999),
            'company' => $this->faker->company,
            'name' => $this->faker->name,
            'cpf_cnpj' => (rand(0, 100) > 50) ? $this->faker->cpf(false) : $this->faker->cnpj(false),
        ];
    }
}
