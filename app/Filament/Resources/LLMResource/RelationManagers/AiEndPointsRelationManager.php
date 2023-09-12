<?php

namespace App\Filament\Resources\LLMResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ReplicateAction;
use App\Filament\Resources\AIEndPointResource;

class AiEndPointsRelationManager extends RelationManager
{
    protected static string $relationship = 'aiEndPoints';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
    public function isReadOnly(): bool
    {
        return true;
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('New AI End Point')->url(fn (): string =>AIEndPointResource::getUrl('create')),
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
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('New AI End Point')->url(fn (): string =>AIEndPointResource::getUrl('create')),
            ])
            ->emptyStateHeading('No AI End Points')
            ->emptyStateDescription('Create AI end point to get started.');
    }
}
