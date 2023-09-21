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

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Collections';
    protected static ?string $modelLabel = 'Document';

    protected static ?int $navigationSort = 8;
    // protected static bool $shouldRegisterNavigation = false;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(200),
                Forms\Components\Select::make('collection_id')
                ->relationship(name: 'collection', titleAttribute: 'name')
                ->preload()
                ->searchable(['name', 'description'])
                ->loadingMessage('Loading...')->live()
                ->required()
                ->hiddenOn(DocumentsRelationManager::class),
                Forms\Components\KeyValue::make('meta')
                    ->addActionLabel('Add property'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('Collection.name')
                ->hiddenOn(DocumentsRelationManager::class),
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }    
}
