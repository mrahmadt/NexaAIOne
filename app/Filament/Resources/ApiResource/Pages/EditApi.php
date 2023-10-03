<?php

namespace App\Filament\Resources\ApiResource\Pages;

use App\Filament\Resources\ApiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApi extends EditRecord
{
    protected static string $resource = ApiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $custom = [];
        $keepCustom = [];
        $newCustomOptions = [];

        foreach($data['options']['Messages'] as $option){
            if(isset($option['name']) && in_array($option['name'], ['systemMessage', 'updateSystemMessage'])){
                $message = $option['default'];
                preg_match_all('/\{\{(.*?)\}\}/', $message, $matches);
                foreach($matches[1] as $var){
                    $newCustomOptions[$var] = $var;
                }
            }
        }

        //remove old custom vars
        foreach($data['options']['Custom'] as $index => $customOption){
            if(!in_array($customOption['name'], $newCustomOptions)){
                unset($data['options']['Custom'][$index]);
            }else{
                $keepCustom[$customOption['name']] = $data['options']['Custom'][$index];
            }
        }

        foreach($newCustomOptions as $var => $value) {
            if(!isset($keepCustom[$var])){
                $custom[] = [
                    'name'=> $var,
                    "type" => "text",
                    "required" => true,
                    "desc" => null,
                    "default" => null,
                    "isApiOption" => true,
                    "_group" => 'Custom',
                ];
            }else{
                $custom[] = $keepCustom[$var];
            }
        }
        if($custom) {
            $data['options']['Custom'] = $custom;
        }
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        if ($resource::hasPage('edit') && $resource::canEdit($this->getRecord())) {
            return $resource::getUrl('edit', ['record' => $this->getRecord()]);
        }
        return $this->getResource()::getUrl('index');
    }
}
