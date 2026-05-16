<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Manuel',
            'email' => 'marmocreativo@gmail.com',
            'password' => 'Angeles1#'
        ]);
        $this->call(EstudiosSeeder::class);
        $this->call(CentrosAgendaSeeder::class);
    }
}
