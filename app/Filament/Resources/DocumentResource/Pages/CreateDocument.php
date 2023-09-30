<?php

namespace App\Filament\Resources\DocumentResource\Pages;
use Filament\Notifications\Notification;
use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Jobs\DocumentLoaderJob;
use App\Models\Document;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;
    // protected static bool $canCreateAnother = false;
    protected function handleRecordCreation(array $data): Model
    {
        if(count($data['files']) == 0 && !isset($data['content'])){
            Notification::make()
            ->warning()
            ->title('No content!')
            ->body('Add content or upload file(s).')
            ->persistent()
            ->send();
            $this->halt();
        }elseif(count($data['files']) && isset($data['content'])){
            Notification::make()
            ->warning()
            ->title('Only one document!')
            ->body('Add content or upload files. Not both!')
            ->persistent()
            ->send();
            $this->halt();
        }elseif(count($data['files']) && (!isset($data['splitter_id']) || !isset($data['loader_id']))){
            Notification::make()
            ->warning()
            ->title('Loader/Splitter not selected!')
            ->body('Must select a Loader and Splitter when uploading files.')
            ->persistent()
            ->send();
            $this->halt();
        }

        if(isset($data['splitter_id']) || count($data['files'])){
            
            $jobID = (string) Str::uuid();
            // $url = $request->url;
            $jobArgs = [
                'jobID' => $jobID,
                'collection_id' => $data['collection_id'],
                'meta' => $data['meta'],
                'splitter_id' => $data['splitter_id'],
                'loader_id' => $data['loader_id'],
            ];


            if(!is_null($data['content'])){
                //write $content to temp .txt file and assign the path to $content
                $tempFile = tempnam(sys_get_temp_dir(), 'docloader_');
                $tempFile .= '.txt';
                file_put_contents($tempFile, $data['content']);
                $jobArgs['file'] = $tempFile;
                DocumentLoaderJob::dispatch($jobArgs);
            }elseif (count($data['files'])) {
                foreach($data['files'] as $fileUpload){
                    if($fileUpload->isValid()){
                        $jobArgs['jobID'] = (string) Str::uuid();
                        $jobArgs['file'] = $fileUpload->path();
                        $jobArgs['meta']['__file'] = $fileUpload->getClientOriginalName();
                        DocumentLoaderJob::dispatch($jobArgs);
                    }
                }
            }else{
                Notification::make()
                ->warning()
                ->title('Unknown error!')
                ->body('Not able to read the file.')
                ->persistent()
                ->send();
                $this->halt();
            }
            
            header('Location: ' . $this->getResource()::getUrl('index'));
            exit;
        }else{
            return static::getModel()::create($data);
        }
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
    return Notification::make()
        ->success()
        ->title('Document Submitted')
        ->body('Document has been submitted successfully.');
    }
}
