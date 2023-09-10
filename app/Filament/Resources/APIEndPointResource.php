<?php

namespace App\Filament\Resources;

use App\Filament\Resources\APIEndPointResource\Pages;
use App\Filament\Resources\APIEndPointResource\RelationManagers;
use App\Models\APIEndPoint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\IconColumn;
use Creagia\FilamentCodeField\CodeField;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Actions\Action;
use App\Models\AIEndPoint;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\Grid;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\CheckboxList;
class APIEndPointResource extends Resource
{
    protected static ?string $model = APIEndPoint::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'API';
    protected static ?string $pluralModelLabel = 'APIs';
    protected static ?string $slug = 'apis';
    protected static ?string $navigationLabel = 'APIs';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Select::make('ai_end_points_id')
                ->relationship(name: 'aiendpoint', titleAttribute: 'name')
                ->loadingMessage('Loading...')->live()
                ->afterStateUpdated(function (Set $set, Get $get, string $operation) {
                    if($operation=='create'){
                        $AIEndPoint = AIEndPoint::where(['id'=>$get('ai_end_points_id'), 'isActive'=>true ])->first();

                        $className = '\App\AIEndPoints\\' . $AIEndPoint->className;
                        $REQUEST_SCHEMA = $className::REQUEST_SCHEMA;
                        $LLMS = $className::LLMS;

                        $set('enableHistory', $AIEndPoint->supportHistory);
                        $set('enableCaching', $AIEndPoint->supportCaching);
                        
                        $set('requestSchema', $REQUEST_SCHEMA);

                        $set('AIEndPoint', $AIEndPoint->toArray());

                        $set('AI_LLMs', $LLMS);
                    }
                })->columnSpan('full'),

                TextInput::make('name')->columnSpan('full')->required(),
                Textarea::make('description')->columnSpan('full')->nullable(),
                TextInput::make('apiName')->columnSpan('full')->prefix('https://.../api/')->required(),

                CheckboxList::make('llms')
                ->options(function (Get $get, string $operation) { 
                    $result = [];
                        if(!is_null($get('AI_LLMs'))) {
                            foreach ($get('AI_LLMs') as $item) {
                                $result[$item['modelName']] = $item['name'];
                            }
                        }
                    return $result; 
                })
                ->descriptions(function (Get $get, string $operation) { 
                    $result = [];
                    if(!is_null($get('AI_LLMs'))) {
                        foreach ($get('AI_LLMs') as $item) {
                            $result[$item['modelName']] = $item['description'];
                        }
                    }
                    return $result; 
                })
                ->columns(2)
                ->columnSpan('full')
                ->required(),



                Toggle::make('enableUsage')->default(true),
                Toggle::make('isActive')->default(true),


                Toggle::make('enableCaching')->disabled(function (Get $get, string $operation) { return (($operation=='create') && (isset($get('AIEndPoint')['supportCaching']))) ? (!$get('AIEndPoint')['supportCaching']) : false; }),
                TextInput::make('cachingPeriod')
                ->numeric()
                ->default(1440)->visible(fn(Get $get): bool => $get('enableCaching')),



                Toggle::make('enableHistory')->disabled(function (Get $get, string $operation) { return (($operation=='create') && (isset($get('AIEndPoint')['supportHistory']))) ? (!$get('AIEndPoint')['supportHistory']) : false; }),

                Radio::make('historyMethod')
                ->options( APIEndPoint::HISTORYMETHOD )
                ->inline()
                ->default(0)->visible(fn(Get $get): bool => $get('enableHistory')),



                Repeater::make('requestSchema')
                ->schema([
                    TextInput::make('name')->required(),
                    Select::make('type')
                        ->options([
                            'member' => 'Member',
                            'administrator' => 'Administrator',
                            'owner' => 'Owner',
                        ])
                        ->required(),
                    Textarea::make('desc')->label('Description'),
                    TextInput::make('default')->label('Value'),
                    Toggle::make('is_ApiOption')->label('Is Changeable via API?')->default(true),

                ])->columns(2)->columnSpan('full')
                ->reorderable(false)
                ->reorderableWithDragAndDrop(false)
                ->collapsible()
                // ->cloneable()
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                ->deleteAction(
                    fn (Action $action) => $action->requiresConfirmation(),
                )
                ->addActionLabel('Add API Option'),

            ]);



    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAPIEndPoints::route('/'),
            'create' => Pages\CreateAPIEndPoint::route('/create'),
            'edit' => Pages\EditAPIEndPoint::route('/{record}/edit'),
        ];
    }    
}
