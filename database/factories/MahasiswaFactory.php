<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MahasiswaFactory extends Factory
{
    public function definition(): array
    {
        $gender = fake()->randomElement(['Laki-laki', 'Perempuan']);
        return [
            'nama' => fake()->name($gender === 'Laki-laki' ? 'male' : 'female'),
            'nim' => fake()->unique()->numerify('########'),
            'jurusan' => fake()->randomElement([
                'Teknik Informatika', 'Sistem Informasi', 'Ilmu Komputer',
                'Teknik Elektro', 'Manajemen Informatika'
            ]),
            'fakultas' => fake()->randomElement([
                'Fakultas Ilmu Komputer', 'Fakultas Teknik'
            ]),
            'email' => fake()->email(),
            'nomor_hp' => '08' . rand(111111111, 999999999),
            'alamat' => fake()->address(),
            'tanggal_lahir' => fake()->date(),
            'jenis_kelamin' => $gender,
        ];
    }
}
