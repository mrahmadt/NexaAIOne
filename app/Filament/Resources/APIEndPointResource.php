<?php

namespace App\Filament\Resources;

use App\Filament\Resources\APIEndPointResource\Pages;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use App\Models\AIEndPoint;
use App\Models\APIEndPoint;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Str;

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
        
        if($form->getOperation() == 'create'){
            $AIInputField = Select::make('ai_end_points_id')
            ->relationship(name: 'aiendpoint', titleAttribute: 'name')
            ->loadingMessage('Loading...')->live()
            ->afterStateUpdated(function (Set $set, Get $get, string $operation) {
                // if($operation=='create'){
                    $AIEndPoint = AIEndPoint::where(['id'=>$get('ai_end_points_id'), 'isActive'=>true ])->first();
                    $className = '\App\AIEndPoints\\' . $AIEndPoint->className;
                    $requestSchema = $className::getSchema(AIEndPoint: $AIEndPoint);
                    $set('requestSchema', $requestSchema);
                    $set('AIEndPoint', $AIEndPoint->toArray());
                    // $set('AI_LLMs', $LLMS);
                // }
            })->columnSpan('full');
        }else{
            // $AIInputField = TextEntry::make('name');
            $AIInputField = Placeholder::make('')->content(fn (APIEndPoint $record): string => 'AI End Point: ' . $record->aiendpoint()->value('name'));
        }
        return $form
            ->schema([
                $AIInputField,
                        TextInput::make('name')->columnSpan('full')->required()->live(debounce: 500)->afterStateUpdated(function (Set $set, ?string $state, string $operation) {
                            if($operation=='create'){
                                $set('apiName', Str::slug($state));
                            }
                        }),
                        Textarea::make('description')->columnSpan('full')->nullable(),
                        TextInput::make('apiName')->columnSpan('full')->prefix('https://.../api/')->required()->live(debounce: 500)->afterStateUpdated(function (Set $set, ?string $state) {
                                $set('apiName', Str::slug($state));
                        }),
                        Toggle::make('enableUsage')->default(true),
                        Toggle::make('isActive')->default(true),
                                Repeater::make('requestSchema')->schema([
                                    TextInput::make('_noDefaultValue')->hidden()->default(false),
                                    TextInput::make('_mandatory')->hidden()->default(false),
                                    TextInput::make('_allowApiOption')->hidden(true)->default(true),
                                    
                                    TextInput::make('name')->required()->disabled(function (Get $get) { return (!is_null($get('_mandatory')) ) ? ($get('_mandatory')) : false; })->dehydrated(true),
                                    TextInput::make('type')->required()->disabled(function (Get $get) { return (!is_null($get('_mandatory')) ) ? ($get('_mandatory')) : false; })->dehydrated(true),
                                    Textarea::make('desc')->label('Description')->disabled(function (Get $get) { return (!is_null($get('_allowApiOption')) ) ? (!$get('_allowApiOption')) : false; })->dehydrated(true),
                                    TextInput::make('default')->label('Value')->hidden(function (Get $get) { return (!is_null($get('_noDefaultValue')) ) ? ($get('_noDefaultValue')) : false; })->dehydrated(true),
                                    Toggle::make('isApiOption')->label('Enable as API option?')->default(true)->hidden(function (Get $get) { return (!is_null($get('_allowApiOption')) ) ? (!$get('_allowApiOption')) : false; })->disabled(function (Get $get) { return (!is_null($get('_noDefaultValue')) ) ? ($get('_noDefaultValue')) : false; })->dehydrated(true),
                                ])
                                ->columns(2)->columnSpan('full')->reorderable(false)->reorderableWithDragAndDrop(false)->collapsible()->addActionLabel('Add API Option')
                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                // ->collapsed()
                                ->deleteAction(
                                    function (Action $action) {
                                        $action->action(function (array $arguments, Repeater $component): void {
                                            $items = $component->getState();
                                            $activeItem = $items[$arguments['item']];
                            
                                            if ($activeItem['_mandatory']) {
                                                Notification::make()
                                                    ->danger()
                                                    ->title('Error')
                                                    ->body('This option is mandatory')
                                                    ->send();
                                            } else {
                                                unset($items[$arguments['item']]);
                                                $component->state($items);
                                            }
                                        });
                                        return $action;
                                    }
                                )->live(debounce: 500),
            
            ]);
    }

    // public static function getEntitySchema() {
    //     dd(self::getResources());
    //     return TextInput::make('name')->required()->disabled(function (Get $get) { 
    //         return   
    //             (!is_null($get('_mandatory')) ) ? ($get('_mandatory')) : false; 
    //     })->dehydrated(true);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->weight(FontWeight::Bold)->searchable()->wrap()->description(fn (APIEndPoint $record): string => ($record->description ? $record->description: ' ')),

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
