<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionResource\Pages;
use App\Filament\Resources\CollectionResource\RelationManagers;
use App\Models\Collection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\FontWeight;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;
    protected static ?string $label = 'Collection';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'My Data';

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
            Forms\Components\TextInput::make('authToken')
                ->required()
                ->minLength(50)
                ->maxLength(100)
                ->label('Auth Token')
                ->suffixAction(
                    Action::make('RegenerateAuthToken')
                        ->icon('heroicon-s-key')
                        ->action(function (Set $set) {
                            $set('authToken', Collection::newAuthToken());
                        })
                )
                ->default(static function (): string {
                    return Collection::newAuthToken();
                }),
            Forms\Components\Select::make('defaultTotalReturnDocuments')
            ->options([
                1 => 'One',
                2 => '2 Documents',
                3 => '3 Documents',
                4 => '4 Documents',
                5 => '5 Documents',
                6 => '6 Documents',
                7 => '7 Documents',
            ])
            ->label('Max Documents to inject in LLM requests?')
            ->required()
            ->default(3),
            Forms\Components\Select::make('loader_id')
                ->relationship(name: 'loader', titleAttribute: 'name')
                ->preload()
                ->searchable(['name', 'description'])
                ->loadingMessage('Loading...')->live(),
            Forms\Components\Select::make('splitter_id')
                ->relationship(name: 'splitter', titleAttribute: 'name')
                ->preload()
                ->searchable(['name', 'description'])
                ->loadingMessage('Loading...')->live(),
            Forms\Components\Select::make('embedder_id')
                ->relationship(name: 'embedder', titleAttribute: 'name')
                ->preload()
                ->searchable(['name', 'description'])
                ->loadingMessage('Loading...')->live(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->weight(FontWeight::Bold)
                ->icon('heroicon-o-archive-box')
                ->description(fn (Collection $record): string => (string)$record->description)
                ->wrap(),
                Tables\Columns\TextColumn::make('authToken')
                ->state(static function (Collection $record): string {
                    return substr($record->authToken,0,4).str_repeat('*',10);
                })
                ->copyable()
                ->copyableState(fn (Collection $record): string => (string)$record->authToken),
                Tables\Columns\TextColumn::make('defaultTotalReturnChunk')
                ->sortable()
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
            RelationManagers\DocumentsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }    
}
