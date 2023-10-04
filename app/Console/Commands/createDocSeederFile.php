<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class createDocSeederFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-doc-seeder-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if(file_exists(database_path('seeders/documentsTableSeederData.json'))){
            //print error message and exit
            $this->error('File already exists ' . database_path('seeders/documentsTableSeederData.json'));
            exit;
        }
        $documents =DB::table('documents')->get();
        $seedData = [];
        foreach ($documents as $key => $document) {
            unset($document->id);
            unset($document->created_at);
            unset($document->updated_at);
            $seedData[] = (array) $document;
        }
        file_put_contents(database_path('seeders/documentsTableSeederData.json'), json_encode($seedData, JSON_PRETTY_PRINT));
    }
}
