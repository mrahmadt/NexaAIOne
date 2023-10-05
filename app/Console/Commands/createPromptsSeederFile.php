<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class createPromptsSeederFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-prompts-seeder-file';

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
        $seederFile = database_path('seeders/promptsTableSeederData.json');
        $database = 'prompts';
        if(file_exists($seederFile)){
            //print error message and exit
            $this->error('File already exists ' . $seederFile);
            exit;
        }
        $records =DB::table($database)->get();
        $seedData = [];
        foreach ($records as $key => $record) {
            unset($record->id);
            unset($record->created_at);
            unset($record->updated_at);
            $seedData[] = (array) $record;
        }
        file_put_contents($seederFile, json_encode($seedData, JSON_PRETTY_PRINT));
    }
}
