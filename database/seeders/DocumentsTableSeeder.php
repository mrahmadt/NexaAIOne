<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     /*
             $documents =DB::table('documents')->get();
        $seedData = [];
        foreach ($documents as $key => $document) {
            unset($document->id);
            unset($document->created_at);
            unset($document->updated_at);
            $seedData[] = (array) $document;
        }
        file_put_contents(database_path('seeders/documentsTableSeederData.json'), json_encode($seedData, JSON_PRETTY_PRINT));
        exit;
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
