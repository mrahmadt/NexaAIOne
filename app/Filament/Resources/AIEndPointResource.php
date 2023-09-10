<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AIEndPointResource\Pages;
use App\Filament\Resources\AIEndPointResource\RelationManagers;
use App\Models\AIEndPoint;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\IconColumn;
use Creagia\FilamentCodeField\CodeField;

class AIEndPointResource extends Resource
{
    protected static ?string $model = AIEndPoint::class;

    protected static ?string $modelLabel = 'AI End Point';
    protected static ?string $pluralModelLabel = 'AI End Points';
    protected static ?string $slug = 'AI-End-Point';
    protected static ?string $navigationLabel = 'AI End Points';
    protected static ?string $navigationIcon = 'heroicon-o-server';

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->columnSpan('full')->required(),
                TextInput::make('className')->columnSpan('full')->required(),
                TextInput::make('ApiReference')->columnSpan('full')->nullable(),
                Textarea::make('description')->columnSpan('full')->nullable(),
                Toggle::make('supportHistory')->required(),
                Toggle::make('supportCaching')->required(),
                
                CodeField::make('requestSchema')
                ->afterStateHydrated(function (?array $state, CodeField $component): void {
                    $component->state( ($state ? json_encode($state, JSON_PRETTY_PRINT) : "{\n\n}") );
                })
                ->dehydrated(false)->maxHeight('500px')->setLanguage(CodeField::JSON)->withLineNumbers()->columnSpan('full')->label('API Request Schema'),

                // \InvadersXX\FilamentJsoneditor\Forms\JSONEditor::make('requestSchema')->columnSpan('full')->modes(['code', 'form',  'view', 'preview'])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->weight(FontWeight::Bold)->searchable()->wrap()->description(fn (AIEndPoint $record): string => $record->description),
                ToggleColumn::make('supportHistory'),
                ToggleColumn::make('supportCaching'),
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
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAIEndPoints::route('/'),
            'create' => Pages\CreateAIEndPoint::route('/create'),
            'edit' => Pages\EditAIEndPoint::route('/{record}/edit'),
        ];
    }    
}
