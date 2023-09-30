<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
use App\Models\Splitter;
use App\Models\loader;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\KeyValue;
use App\Filament\Resources\CollectionResource\RelationManagers\DocumentsRelationManager;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Set;
use Filament\Forms\Get;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Model;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'My Data';
    protected static ?string $modelLabel = 'Document';

    protected static ?int $navigationSort = 2;
    // protected static bool $shouldRegisterNavigation = false;


    public static function form(Form $form): Form
    {
        if($form->getOperation() == 'create'){
            $introDescription = Placeholder::make('')->content(new HtmlString('Use this form to upload documents to any collection. You can also use the Collection APIs to integrate with other systems.'))->columnSpanFull();
            $splitterForm = Forms\Components\Select::make('splitter_id')
                ->label('Splitter')
                ->options(Splitter::all()->pluck('name', 'id'))
                ->placeholder('No Splitter')
                ->helperText('Often times you want to split large text into smaller chunks to better work with language models. TextSplitters are responsible for splitting up large text into smaller documents. you can configure Splitter options from Splitters page and you can create same splitter with different options.')
                ->columnSpanFull();

            $loaderForm = Forms\Components\Select::make('loader_id')
                ->label('Loader')
                ->options(Loader::all()->pluck('name', 'id'))
                ->placeholder('No Loader')
                ->helperText('Document loader provides a "load/download" and "extract text" method for specified file(s).')
                ->columnSpanFull();

            $fileUpload = FileUpload::make('files')
                ->label('OR Upload a File (Text, CSV, pdf, and Microsoft Excel only)')
                ->multiple()
                ->openable()->columns(1)
                ->preserveFilenames()
                ->storeFiles(false);

            $textArea = Forms\Components\Textarea::make('content')
            ->label('Document Content')
            ->rows(8);
        }else{
            $introDescription = Placeholder::make('')->content(new HtmlString('Edit document chunk. <b>We will not use Text Splitter when editing a document chunk.</b>'))->columnSpanFull();
            $splitterForm = Placeholder::make('')->content('')->columnSpanFull();
            $loaderForm = Placeholder::make('')->content('')->columnSpanFull();
            $fileUpload = Placeholder::make('')->content('')->columnSpanFull();
            $textArea = Forms\Components\Textarea::make('content')
            ->label('Document Content')
            ->required()
            ->rows(8)->columnSpanFull();
        }

        return $form
            ->schema([
                $introDescription,
                Forms\Components\Select::make('collection_id')
                ->relationship(name: 'collection', titleAttribute: 'name')
                ->preload()
                ->searchable(['name', 'description'])
                ->loadingMessage('Loading...')
                ->required()
                ->live()
                ->afterStateUpdated(function (string $state, Set $set, Get $get, string $operation) {
                    if(is_numeric($state)){
                        $collection = Collection::find($state);
                        $set('splitter_id', $collection->splitter_id);
                        $set('loader_id', $collection->loader_id);
                    }
                })    
                ->hiddenOn(DocumentsRelationManager::class)->columnSpanFull(),
                $textArea,
                $fileUpload,
                Placeholder::make('')->content(new HtmlString('<b>Make sure you don\'t have any sensitive information in the document and consider the LLM token limits that can be processed in a single interaction (System message + Document(s) + History + User Question).</b>'))->columnSpanFull(),
                $loaderForm,
                $splitterForm,

                Forms\Components\KeyValue::make('meta')
                    ->label('Meta Data')
                    ->helperText('Specify any metadata you would like to associate with this document.')
                    ->addActionLabel('Add property')->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content')
                ->wrap()
                ->words(100)
                ->size('sm')
                ->searchable(),
                Tables\Columns\TextColumn::make('Collection.name')
                ->searchable()
                ->hiddenOn(DocumentsRelationManager::class),
                Tables\Columns\TextColumn::make('meta')
                ->toggleable(isToggledHiddenByDefault: true)
                // ->searchable()
                ->searchable(
                    query: function(Builder $query, string $search): Builder {
                        return $query->where('meta', 'LIKE', "%{$search}%");
                    }
                )
                ,
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
                SelectFilter::make('Collection')->relationship('collection', 'name')->searchable()
                ->hiddenOn(DocumentsRelationManager::class)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->persistFiltersInSession();
            
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }    
}
