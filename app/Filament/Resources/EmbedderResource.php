<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmbedderResource\Pages;
use App\Filament\Resources\EmbedderResource\RelationManagers;
use App\Models\Embedder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\KeyValue;

class EmbedderResource extends Resource
{
    protected static ?string $model = Embedder::class;
    protected static ?string $modelLabel = 'Embedder';

    protected static ?string $navigationIcon = 'heroicon-o-variable';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 8;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('className')
                    ->required()
                    ->maxLength(100),
                Forms\Components\KeyValue::make('options'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->icon('heroicon-o-variable')
                ->searchable()
                ->weight(FontWeight::Bold)
                ->description(fn (Embedder $record): string => (string)$record->description)
                ->wrap(),
                Tables\Columns\TextColumn::make('description')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('className')
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
                \Filament\Tables\Actions\ReplicateAction::make()->before(function (\Filament\Tables\Actions\ReplicateAction $action, Embedder $record) {
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
            'index' => Pages\ListEmbedders::route('/'),
            'create' => Pages\CreateEmbedder::route('/create'),
            'edit' => Pages\EditEmbedder::route('/{record}/edit'),
        ];
    }    
}
