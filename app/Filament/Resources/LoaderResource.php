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
use Filament\Support\Enums\FontWeight;

class LoaderResource extends Resource
{
    protected static ?string $model = Loader::class;
    protected static ?string $modelLabel = 'Loader';
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 6;


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
            \InvadersXX\FilamentJsoneditor\Forms\JSONEditor::make('options'),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
            ->searchable()
            ->weight(FontWeight::Bold)
            ->description(fn (Loader $record): string => (string)$record->description)
            ->icon('heroicon-o-inbox-stack')
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
                \Filament\Tables\Actions\ReplicateAction::make()->before(function (\Filament\Tables\Actions\ReplicateAction $action, Loader $record) {
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
            'index' => Pages\ListLoaders::route('/'),
            'create' => Pages\CreateLoader::route('/create'),
            'edit' => Pages\EditLoader::route('/{record}/edit'),
        ];
    }
}
