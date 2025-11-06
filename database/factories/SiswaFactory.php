<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Kelas; // Import model Kelas
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Siswa>
 */
class SiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Buat user baru dengan role 'siswa'
        $user = User::factory()->create([
            'role' => 'siswa',
            'password' => Hash::make('password'),
        ]);

        return [
            'user_id' => $user->id,
            'kelas_id' => Kelas::inRandomOrder()->first()->kelas_id, // Ambil kelas secara acak
            'nis' => fake()->unique()->numerify('2025######'),
            'nama_siswa' => $user->name,
            'alamat' => fake()->address(),
        ];
    }
}
