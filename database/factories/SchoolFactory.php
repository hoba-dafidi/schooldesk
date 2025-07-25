<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SchoolFactory extends Factory
{
    public function definition(): array
{
    $demoLogos = [
        'logos/demo1.png',
        'logos/demo2.png',
        'logos/demo3.jpg',
        'logos/demo4.jpg',
        'logos/demo5.jpg',
        'logos/demo6.png',
        'logos/demo7.jpg',
    ];

    $filename = 'logos/' . Str::uuid() . '.jpg';

    return [
        'name'             => $this->faker->company . ' School',
        'email'            => $this->faker->unique()->safeEmail,
        'phone'            => $this->faker->phoneNumber,
        'address'          => $this->faker->address,
        'logo'             => $this->faker->randomElement($demoLogos),
        'type'             => $this->faker->randomElement(['Primary', 'Secondary', 'College']),
        'established_year' => $this->faker->numberBetween(1990, 2022),
        'about'            => $this->faker->sentence(15),
        'city'             => $this->faker->city,
        'state'            => $this->faker->state,
        'country'          => $this->faker->country,
    ];
}
}
