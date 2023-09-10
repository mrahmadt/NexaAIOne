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
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
class LLMResource extends Resource
{
    protected static ?string $modelLabel = 'LLM';
    protected static ?string $pluralModelLabel = 'LLMs';
    protected static ?string $slug = 'LLM';
    protected static ?string $navigationLabel = 'LLMs';
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $model = LLM::class;


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('modelName')->required(),
            Forms\Components\TextInput::make('ownedBy')->nullable(),
            Forms\Components\TextInput::make('maxTokens')->numeric()->nullable(),
            Forms\Components\Textarea::make('description')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->weight(FontWeight::Bold)->searchable()->wrap()->description(fn (LLM $record): string => $record->description),
                TextColumn::make('ownedBy')->sortable()->searchable(),
                TextColumn::make('modelName')->searchable(),
                TextColumn::make('maxTokens')->sortable(),
            ])
            ->filters([
                // Tables\Filters\SelectFilter::make('ownedBy')
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
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLLMs::route('/'),
            'create' => Pages\CreateLLM::route('/create'),
            'edit' => Pages\EditLLM::route('/{record}/edit'),
        ];
    }    
}
