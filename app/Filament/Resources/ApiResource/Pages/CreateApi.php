<?php

namespace App\Filament\Resources\ApiResource\Pages;

use App\Filament\Resources\ApiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApi extends CreateRecord
{
    protected static string $resource = ApiResource::class;
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $custom['Custom'] = [];
        $newCustomOptions = [];
        foreach($data['options']['Messages'] as $option){
            if(isset($option['name']) && in_array($option['name'], ['systemMessage', 'updateSystemMessage'])){
                $message = $option['default'];
                preg_match_all('/\{\{(.*?)\}\}/', $message, $matches);
                foreach($matches[1] as $var){
                    $newCustomOptions[$var] = true;
                }
            }
        }
        foreach($newCustomOptions as $var => $value) {
            $custom['Custom'][] = [
                'name'=> $var,
                "type" => "text",
                "required" => true,
                "desc" => null,
                "default" => null,
                "isApiOption" => true,
                "_group" => 'Custom',
            ];
        }
        if($custom['Custom']) {
            $data['options'] = array_merge($custom,$data['options']);
        }
        return $data;
    }
}


