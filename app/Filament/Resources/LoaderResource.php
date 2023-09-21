<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoaderResource\Pages;
use App\Filament\Resources\LoaderResource\RelationManagers;
use App\Models\Loader;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class LoaderResource extends Resource
{
    protected static ?string $model = Loader::class;
    protected static ?string $modelLabel = 'Loader';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 5;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(40),
                Forms\Components\Textarea::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('className')
                    ->required()
                    ->maxLength(100),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->icon('heroicon-o-rectangle-stack')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Loader $record): string => (string)$record->description)
                    ->wrap(),
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
            'index' => Pages\ListLoaders::route('/'),
            'create' => Pages\CreateLoader::route('/create'),
            'edit' => Pages\EditLoader::route('/{record}/edit'),
        ];
    }    
}
