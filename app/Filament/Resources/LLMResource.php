<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LLMResource\Pages;
use App\Filament\Resources\LLMResource\RelationManagers;
use App\Models\LLM;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;

class LLMResource extends Resource
{
    protected static ?string $model = LLM::class;
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'LLM';
    protected static ?string $pluralModelLabel = 'LLMs';
    protected static ?string $slug = 'llms';
    protected static ?string $navigationLabel = 'LLMs';

    protected static ?string $navigationIcon = 'heroicon-o-language';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('modelName')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ownedBy')
                    ->maxLength(255),
                Forms\Components\TextInput::make('maxTokens')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->description(fn (LLM $record): string => $record->description)
                    ->wrap()
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Medium)
                    ,
                Tables\Columns\TextColumn::make('modelName')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ownedBy')
                    ->searchable(),
                Tables\Columns\TextColumn::make('maxTokens')
                    ->numeric()
                    ->sortable(),
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\AiEndPointsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLLMS::route('/'),
            'create' => Pages\CreateLLM::route('/create'),
            'edit' => Pages\EditLLM::route('/{record}/edit'),
        ];
    }    
}
