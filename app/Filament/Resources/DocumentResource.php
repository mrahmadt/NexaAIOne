<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Document;
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
            $introDescription = Placeholder::make('')->content(new HtmlString('Use this form to upload documents to any collection. You can also use the Collection APIs to integrate with other systems.
            <br><br>We will not use Text Splitter for documents created via this page.'))->columnSpanFull();
        }else{
            $introDescription = Placeholder::make('')->content('')->columnSpanFull();
        }

        return $form
            ->schema([
                $introDescription,
                Forms\Components\Select::make('collection_id')
                ->relationship(name: 'collection', titleAttribute: 'name')
                ->preload()
                ->searchable(['name', 'description'])
                ->loadingMessage('Loading...')->live()
                ->required()
                ->hiddenOn(DocumentsRelationManager::class),
                Forms\Components\Textarea::make('content')
                ->label('Document Content')
                ->helperText(new HtmlString('<b>- Make sure you don\'t have any sensitive information in the document.<br>- Consider the LLM token limits that can be processed in a single interaction (System message + Document(s) + History + User Question).</b>'))
                ->required()
                ->rows(8)
                ->columnSpanFull(),

                Forms\Components\KeyValue::make('meta')
                    ->label('Meta Data')
                    ->helperText('Specify any metadata you would like to associate with this document (will be returned in the API response).')
                    ->addActionLabel('Add property'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content')
                ->wrap()
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
