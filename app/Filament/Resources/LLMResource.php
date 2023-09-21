<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LlmResource\Pages;
use App\Filament\Resources\LlmResource\RelationManagers;
use App\Models\Llm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LlmResource extends Resource
{
    protected static ?string $model = Llm::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?string $navigationLabel = 'LLMs';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $modelLabel = 'LLM';

    protected static ?int $navigationSort = 2;
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
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->icon('heroicon-o-language')
                    ->searchable(),
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
            'index' => Pages\ListLlms::route('/'),
            'create' => Pages\CreateLlm::route('/create'),
            'edit' => Pages\EditLlm::route('/{record}/edit'),
        ];
    }    
}
