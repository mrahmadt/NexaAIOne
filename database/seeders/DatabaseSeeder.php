<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LLMsTableSeeder::class,
            ServiceTableSeeder::class,
            ServicesLLMsTableSeeder::class,
            EmbeddersTableSeeder::class,
            LoadersTableSeeder::class,
            SplittersTableSeeder::class,
            CollectionsTableSeeder::class,
            DocumentsTableSeeder::class,
            APIsTableSeeder::class,
            AppsTableSeeder::class,
            PromptsTableSeeder::class,
        ]);
    }
}
