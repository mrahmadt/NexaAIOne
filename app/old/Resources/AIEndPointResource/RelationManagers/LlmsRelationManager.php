<?php

namespace App\Filament\Resources\AIEndPointResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TextInput;
use App\Models\AIEndPoint;
use App\Filament\Resources\AIEndPointResource;


class LlmsRelationManager extends RelationManager
{
    protected static string $relationship = 'llms';

    public function isReadOnly(): bool
    {
        return true;
    }
    public function form(Form $form): Form
    {
        return AIEndPointResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->weight(FontWeight::Bold),
                TextColumn::make('description')->wrap(),

            ])
            ->filters([
                //
            ]);
    }
}
