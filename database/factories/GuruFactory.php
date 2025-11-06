<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guru>
 */
class GuruFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Buat user baru dengan role 'guru'
        $user = User::factory()->create([
            'role' => 'guru',
            'password' => Hash::make('password'),
        ]);

        return [
            'user_id' => $user->id,
            'nip' => fake()->unique()->numerify('19##########'),
            'nama_guru' => $user->name,
            'kontak' => fake()->phoneNumber(),
        ];
    }
}
