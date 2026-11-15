<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phone = fake()->unique()->numerify('03#########');
        $email = fake()->unique()->safeEmail();

        return [
            'identity_key' => 'phone:'.$phone,
            'customer_name' => fake()->name(),
            'customer_phone' => $phone,
            'phone_normalized' => $phone,
            'customer_email' => $email,
            'email_normalized' => mb_strtolower($email),
        ];
    }
}
