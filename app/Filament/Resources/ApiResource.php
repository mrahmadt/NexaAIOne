<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiResource\Pages;
use App\Models\Api;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;

use App\Actions\ResetStars;

class ApiResource extends Resource
{
    protected static ?string $model = Api::class;   
    protected static ?string $navigationLabel = 'APIs';
    protected static ?string $label = 'APIs';
    protected static ?string $modelLabel = 'API';
    protected static ?string $pluralLabel = 'APIs';
    protected static ?string $pluralModelLabel = 'APIs';
    protected static ?string $recordTitleAttribute = 'APIs';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';
    public static function form(Form $form): Form
    {
        if($form->getOperation() == 'create'){
            $introDescription = Placeholder::make('')->content('Create an API for an AI service and define the API options & configuration. API creation will help you specify which service configurations can be changeable through an API call (API option) and which configurations are predefined (can not be changed in an API call).')->columnSpanFull();

            $serviceInput = Forms\Components\Select::make('service_id')
            ->relationship(name: 'service', titleAttribute: 'name',modifyQueryUsing: fn (Builder $query) => $query->where('isActive',true))
            ->live()
            ->required()
            ->preload()
            ->columnSpanFull()
            ->helperText('Which Service would you like to create an API for it?')
            ->label('Service')
            ->afterStateUpdated(function (Set $set, Get $get, string $operation) {
                if($get('service_id')){
                    $serviceModel = Service::where(['id'=>$get('service_id'), 'isActive'=>true])->first();
                    $className = '\App\Services\\' . $serviceModel->className;
                    $service = new $className();
                    $options = $service->getOptionSchema($serviceModel);
                    $set('optionGroups', array_keys($options));
                    $set('options', $options);
                    $set('supportCollection', $serviceModel->supportCollection);
                }else{
                    $set('optionGroups', []);
                    $set('options', []);
                    $set('supportCollection', false);

                }
            });
        }else{
            $introDescription = Placeholder::make('')->content('')->columnSpanFull();
            $serviceInput = Placeholder::make('Service')->content(function (API $record, Set $set){ 
                return $record->service()->value('name');
            });

        }
        return $form
            ->schema([
                
            $introDescription,
            Forms\Components\TextInput::make('name')->required()->maxLength(150)->columnSpanFull()->live(debounce: 500)->afterStateUpdated(function (Get $get, Set $set) { $set('endpoint', Str::slug($get('name'))); })->helperText('Any name to help you identify this API.'),
            Forms\Components\TextInput::make('description')->maxLength(255)->columnSpanFull(),
            Forms\Components\TextInput::make('endpoint')->required()->prefix('https://'.request()->getHost().'/api/../')->maxLength(100)->columnSpanFull()->live(onBlur: true)->helperText(
                function(Get $get){
                    $url = '';
                    if($get('endpoint')){
                        $url = '(Example: <b>https://'.request()->getHost().'/api/xx/'.$get('endpoint').'</b>)';
                    }
                    $content = new HtmlString('API end point. '.$url);
                    return $content;
                }
            )->afterStateUpdated(function (Get $get, Set $set) {$set('endpoint', Str::slug($get('endpoint')));}),
            Forms\Components\Toggle::make('enableUsage')->label('Track Usage')->required()->default(true)->helperText('Track API usage in Admin Portal.'),
            Forms\Components\Toggle::make('isActive')->label('Is Active')->required()->default(true)->helperText('Enable/Disable API.'),
            $serviceInput,


            Forms\Components\Select::make('collection_id')->relationship(name: 'collection', titleAttribute: 'name')->disabled(fn (Get $get): bool => !$get('supportCollection') ?? true)->visible(fn (Get $get): bool => $get('supportCollection') ?? false)->columnSpanFull()
            ->helperText(new HtmlString('Should we use specific collections to help AI answer user questions? (this method is called RAG "<b>Retrieval Augmented Generation</b>").<br><b>What is RAG?</b> <a class="underline" href="https://research.ibm.com/blog/retrieval-augmented-generation-RAG" target=_blank>What is retrieval-augmented generation?</a>    <a class="underline" href="https://huggingface.co/docs/transformers/model_doc/rag" target=_blank>RAG Overview</a>' )),

            Grid::make()
                ->schema(
                    function (Get $get, Set $set, string $operation, API $record = null){
                        if($operation != 'create' && !$get('initiated')){
                            $serviceModel = Service::where(['id'=>$record->service_id, 'isActive'=>true])->first();
                            $set('supportCollection', $serviceModel->supportCollection);            
                            $set('optionGroups', array_keys($record->options));
                            $set('options', $record->options);
                            $set('initiated', true);
                        }
                        $groups = $get('optionGroups') ?? [];
                        $sections = [
                            Placeholder::make('')->content('Specify which service configurations can be changeable through an API call (API option) and which configurations are predefined (can not be changed in an API call).')->columnSpanFull()->visible(fn (Get $get): bool => (bool) $get('optionGroups') ?? false),
                        ];
                        foreach($groups as $group){
                            $sections[] =Section::make($group)
                            ->schema(
                                function (Get $get) use($group) {
                                    return [
                                        Repeater::make('options.'.$group)->schema(function (Get $get) {
                                            $form = [];
                                            $form[] = Hidden::make('_allowApiOption')->default(1); // allow to force disable isApiOption by Feature
                                            $form[] = Hidden::make('_group')->default(1);
                                            $form[] = Hidden::make('name');
                                            $form[] = Hidden::make('type')->default('Any');
                                            $form[] = Hidden::make('options'); //hack to save options in API db
                                            $form[] = Hidden::make('desc');
                                            $form[] = Placeholder::make('')->label('Type')->content(fn (Get $get): string => $get('type') ?? '')->dehydrated(true);
                                            $form[] = Toggle::make('isApiOption')->label('Enable as API option?')->default(true)->hidden(function (Get $get) { return (!is_null($get('_allowApiOption')) ) ? (!$get('_allowApiOption')) : false; })->dehydrated(true);
                                            $form[] =  Forms\Components\Textarea::make('desc')->label('Description');
                                            $form[] = Forms\Components\TextInput::make('default')->label('Value');
                                            $form[] = Placeholder::make('')->content(function (Get $get){
                                                $content = $get('desc') ?? ''; 
                                                if($get('options')){
                                                    $content .= "\n\n<br><br><b>Options:</b> " . implode(', ',array_keys($get('options')));
                                                }
                                                return new HtmlString($content);
                                            })->columnSpan(1);
                                            return $form;
                                        })
                                        ->collapsed()->label(false)->addable(false)->columns(2)->columnSpanFull()->deletable(false)->reorderable(false)->reorderableWithDragAndDrop(false)->collapsible()
                                        ->itemLabel(fn (array $state) => new HtmlString('<b>'. ($state['name']??null) .'</b>'))
                                        ->live(debounce: 500),
                                    ];
                                }
                            )->collapsible()->collapsed();
                        }
                        return $sections;
                }),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Api $record): string => (string)$record->description)
                    ->icon('heroicon-o-cloud')
                    ->wrap(),
                Tables\Columns\TextColumn::make('description')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()->wrap(),
                Tables\Columns\TextColumn::make('endpoint')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('service.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('isActive')
                    ->label('Active')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('enableUsage')
                    ->label('Usage')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\ReplicateAction::make()->before(function (\Filament\Tables\Actions\ReplicateAction $action, Api $record) {
                    unset($record->id);
                    $record->name = $record->name . ' (copy)';
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->persistSortInSession();
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
            'index' => Pages\ListApis::route('/'),
            'create' => Pages\CreateApi::route('/create'),
            'edit' => Pages\EditApi::route('/{record}/edit'),
        ];
    }    
}
