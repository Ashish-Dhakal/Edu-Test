<?php

namespace Database\Seeders;

use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Question;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'ashish@test.com',
        ]);

        $faker = \Faker\Factory::create();

        // Seed Questions
        $questionCount = 1000; // Define how many questions you want

        for ($k = 0; $k < $questionCount; $k++) {
            $options = [
                'A' => $faker->sentence(5),
                'B' => $faker->sentence(5),
                'C' => $faker->sentence(5),
                'D' => $faker->sentence(5),
            ];
        
            $answer = $faker->randomElement(['A', 'B', 'C', 'D']);
        
            Question::create([
                'question' => $faker->sentence(10),
                'options' => $options, // Store the array directly
                'answer' => $answer,
                'reason' => $faker->paragraph,
            ]);
        }
    }
}
