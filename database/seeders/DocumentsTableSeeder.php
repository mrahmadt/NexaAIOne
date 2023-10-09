<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Kdabrow\SeederOnce\SeederOnce;
class DocumentsTableSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
       // read documentsTableSeederData.json and insert into database
         $documents = json_decode(file_get_contents(database_path('seeders/documentsTableSeederData.json')));
        foreach ($documents as $key => $document) {
            $document->created_at = now();
            $document->updated_at = now();
            DB::table('documents')->insert((array) $document);
        }
    }
}
