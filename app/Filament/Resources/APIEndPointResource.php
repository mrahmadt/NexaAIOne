<?php

namespace App\Filament\Resources;

use App\Filament\Resources\APIEndPointResource\Pages;
use App\Filament\Resources\APIEndPointResource\RelationManagers;
use App\Models\APIEndPoint;
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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
class APIEndPointResource extends Resource
{
    protected static ?string $model = APIEndPoint::class;

    protected static ?string $modelLabel = 'API';
    protected static ?string $pluralModelLabel = 'APIs';
    protected static ?string $slug = 'apis';
    protected static ?string $navigationLabel = 'APIs';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-oval-left-ellipsis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->columnSpan('full')->required(),
                TextInput::make('apiName')->columnSpan('full')->required(),
                Textarea::make('description')->columnSpan('full')->nullable(),

                Textarea::make('ai_end_points_id')->columnSpan('full')->nullable(),


                Toggle::make('enableUsage')->required(),
                Toggle::make('enableHistory')->required(),
                
                Textarea::make('historyMethod_id')->columnSpan('full')->nullable(),
                Textarea::make('historyOptions')->columnSpan('full')->nullable(),

                // CodeField::make('requestSchema')
                // ->afterStateHydrated(function (?array $state, CodeField $component): void {
                //     $component->state( ($state ? json_encode($state, JSON_PRETTY_PRINT) : "{\n\n}") );
                // })
                // ->dehydrated(false)->maxHeight('500px')->setLanguage(CodeField::JSON)->withLineNumbers()->columnSpan('full')->label('API Request Schema'),



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListAPIEndPoints::route('/'),
            'create' => Pages\CreateAPIEndPoint::route('/create'),
            'edit' => Pages\EditAPIEndPoint::route('/{record}/edit'),
        ];
    }    
}
