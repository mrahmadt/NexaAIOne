<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SplitterResource\Pages;
use App\Filament\Resources\SplitterResource\RelationManagers;
use App\Models\Splitter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\KeyValue;

class SplitterResource extends Resource
{
    protected static ?string $model = Splitter::class;
    protected static ?string $modelLabel = 'Splitter';
    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 7;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(40),
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
                ->searchable()
                ->weight(FontWeight::Bold)
                ->description(fn (Splitter $record): string => (string)$record->description)
                ->icon('heroicon-o-scissors')
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
            'index' => Pages\ListSplitters::route('/'),
            'create' => Pages\CreateSplitter::route('/create'),
            'edit' => Pages\EditSplitter::route('/{record}/edit'),
        ];
    }    
}
