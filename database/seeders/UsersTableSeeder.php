<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 150; $i++) {
            $currentDate = Carbon::now();

            User::factory()->create([
                'name' => 'User ' . ($i + 1),
                'surname' => 'Surname ' . ($i + 1),
                'email' => 'user' . ($i + 1) . '@example.com',
                'phone' => rand(1000000000, 9999999999),
                'birth_date' => $currentDate->subYears(rand(16, 65))->subMonths(rand(0, 11)),
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
