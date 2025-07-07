<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $turkishFirstNames = [
            'Ahmet', 'Mehmet', 'Ali', 'Mustafa', 'Hasan', 'Hüseyin', 'İbrahim', 'Murat', 'Ömer', 'Yusuf',
            'Ayşe', 'Fatma', 'Zeynep', 'Emine', 'Hatice', 'Elif', 'Meryem', 'Esra', 'Büşra', 'Selin',
            'Can', 'Deniz', 'Ege', 'Kaya', 'Mert', 'Burak', 'Emre', 'Serkan', 'Tolga', 'Volkan',
            'Seda', 'Gizem', 'Merve', 'Derya', 'Sevgi', 'Aylin', 'Pınar', 'Gamze', 'Tuğçe', 'Hande'
        ];

        $turkishLastNames = [
            'Yılmaz', 'Demir', 'Çelik', 'Şahin', 'Yıldız', 'Yıldırım', 'Özkan', 'Aydın', 'Özdemir', 'Arslan',
            'Doğan', 'Kılıç', 'Aslan', 'Çetin', 'Keskin', 'Koç', 'Kurt', 'Özkan', 'Şen', 'Erdoğan',
            'Aktaş', 'Özkan', 'Kaya', 'Koç', 'Kurt', 'Özkan', 'Şen', 'Erdoğan', 'Aktaş', 'Özkan',
            'Güneş', 'Yıldız', 'Kaya', 'Koç', 'Kurt', 'Özkan', 'Şen', 'Erdoğan', 'Aktaş', 'Özkan'
        ];

        return [
            'first_name' => fake()->randomElement($turkishFirstNames),
            'last_name' => fake()->randomElement($turkishLastNames),
            'phone' => fake()->unique()->numerify('5#########'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone_verified_at' => fake()->optional(0.7)->dateTime(),
            'password' => static::$password ??= Hash::make('12345678'),
            'role_id' => 3, // Default: user role
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 1, // Admin role
        ]);
    }

    /**
     * Indicate that the user is a writer.
     */
    public function writer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => 2, // Writer role
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
