<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $list_locale = ['en_US', 'fr_FR', 'es_ES', 'de_DE', 'it_IT', 'bo_CN'];
        $locale = $list_locale[array_rand($list_locale)];

        return [
            'first_name' => fake($locale)->firstName(),
            'last_name' => fake($locale)->lastName(),
            'email' => fake($locale)->unique()->safeEmail(),
            'phone' => fake($locale)->phoneNumber(),
            'user_id' => User::factory(),
        ];
    }
}
