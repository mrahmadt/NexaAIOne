<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\DB;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

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


        $file = public_path('/examples/HR/1.pdf');

        $content = file_get_contents($file);
        $finfo = new \finfo(FILEINFO_MIME);
        $mimeType = $finfo->buffer($content);
        dd($mimeType);

        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($file);
        $text = $pdf->getText();
        dd($text);

        $output = Pdf::getText($file);
        dd($output);
        // $file = public_path('examples/Book1.xlsx');
        // $file = public_path('examples/Book1.txt');

        $content = file_get_contents($file);
        $finfo = new \finfo(FILEINFO_MIME);
        $mimeType = $finfo->buffer($content);
        dd($mimeType);

        print "File: $file\n-------------------\n";
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);

        $sheetCount = $spreadsheet->getSheetCount();
        print $spreadsheet->getSheetCount() . " worksheet" . ($spreadsheet->getSheetCount() == 1 ? '' : 's') . "\n------------------\n";

        for($i = 0; $i< $sheetCount; $i++){
            $dataArray = $spreadsheet->getSheet($i)->toArray(
            NULL,        // Value that should be returned for empty cells
            TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            FALSE,         // Should the array be indexed by cell row and cell column
            FALSE         // Return values for rows/columns even if they are defined as hidden.
            );
            // $cellValue = $spreadsheet->getActiveSheet()->getCell('A1')->getFormattedValue();
            // $cellValue = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, 1)->getCalculatedValue();
            // print_r($dataArray);
            $content = null;
            foreach ($dataArray as $innerArray) {
                $line = implode(' ', $innerArray);
                $content .= $line . PHP_EOL;
            }

            dd($content);
        }

    }
}
