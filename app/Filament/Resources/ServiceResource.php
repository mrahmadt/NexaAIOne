<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $modelLabel = 'Service';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(40),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('className')->label('Class Name')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('reference')
                    ->maxLength(150),
                Forms\Components\Toggle::make('supportMemory')->label('Support Memory')
                    ->required(),
                Forms\Components\Toggle::make('supportCaching')->label('Support Caching')
                    ->required(),
                Forms\Components\Toggle::make('supportCollection')->label('Support Collection')
                    ->required(),
                Forms\Components\Toggle::make('isActive')->label('Active?')
                    ->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->weight(FontWeight::Bold)
                ->description(fn (Service $record): string => (string)$record->description)
                ->icon('heroicon-o-chat-bubble-left-right')
                ->wrap(),
                Tables\Columns\TextColumn::make('className')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\IconColumn::make('supportMemory')->label('Memory')
                    ->boolean(),
                Tables\Columns\IconColumn::make('supportCaching')->label('Caching')
                    ->boolean(),
                Tables\Columns\IconColumn::make('supportCollection')->label('Collection')
                    ->boolean(),
                Tables\Columns\IconColumn::make('isActive')->label('Active')
                    ->boolean(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }    
}
