<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Kdabrow\SeederOnce\SeederOnce;
class PromptsTableSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
         $records = json_decode(file_get_contents(database_path('seeders/promptsTableSeederData.json')));
        foreach ($records as $key => $record) {
            $record->created_at = now();
            $record->updated_at = now();
            DB::table('prompts')->insert((array) $record);
        }
    }
}
