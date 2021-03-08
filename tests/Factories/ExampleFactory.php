<?php
namespace Tests\Factories;
use Tests\Models\Example;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ExampleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Example::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => $this->faker->dateTime,
            'description' => $this->faker->sentence,
            'password' => md5(Str::random(30)),
            'remember_token' => Str::random(10),
            'type' => $this->faker->word,
        ];
    }
}