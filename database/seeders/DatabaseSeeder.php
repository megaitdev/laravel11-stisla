<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'nama' => 'Abima Nugraha',
            'email' => 'abimanugraha@gmail.com',
            'nomor_wa' => '628989227992',
            'username' => 'tuanputri',
            'password' => Hash::make('root'),
        ]);
    }
}
