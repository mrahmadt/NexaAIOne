<?php

namespace App\Filament\Resources\AppResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Api;
use Filament\Support\Enums\FontWeight;
class ApisRelationManager extends RelationManager
{
    protected static string $relationship = 'apis';
    protected static ?string $title = 'APIs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->weight(FontWeight::Bold)
                ->description(fn (Api $record): string => $record->description)
                ->icon('heroicon-o-cloud')
                ->wrap(),
            ])
            ->filters([
                //
            ])
            ;
    }
}
