<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppResource\Pages;
use App\Filament\Resources\AppResource\RelationManagers;
use App\Models\App;
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

class AppResource extends Resource
{
    protected static ?string $model = App::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'App';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(40),
            Forms\Components\TextInput::make('description')
                ->maxLength(255),
            Forms\Components\TextInput::make('owner')
                ->maxLength(60),
            Forms\Components\TextInput::make('authToken')
                ->required()
                ->minLength(50)
                ->maxLength(100)
                ->label('Auth Token')
                ->suffixAction(
                    Action::make('RegenerateAuthToken')
                        ->icon('heroicon-s-key')
                        ->action(function (Set $set) {
                            $set('authToken', App::newAuthToken());
                        })
                )
                ->default(static function (): string {
                    return App::newAuthToken();
                }),
            Forms\Components\Toggle::make('isActive')->label('Active?')
                ->required()->default(true),
            Forms\Components\Select::make('services')
                ->multiple()
                ->relationship(name: 'apis', titleAttribute: 'name',modifyQueryUsing: fn (Builder $query) => $query->where('isActive',true))
                ->preload()
                ->searchable(['name', 'description'])
                ->loadingMessage('Loading...')->live()
        ])->columns(1);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->weight(FontWeight::Bold)
                ->icon('heroicon-o-code-bracket')
                ->description(fn (App $record): string => (string)$record->description)
                ->wrap(),
                Tables\Columns\TextColumn::make('authToken')
                ->state(static function (App $record): string {
                    return substr($record->authToken,0,4).str_repeat('*',10);
                })
                ->copyable()
                ->copyableState(fn (App $record): string => (string)$record->authToken),
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
            RelationManagers\ApisRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApps::route('/'),
            'create' => Pages\CreateApp::route('/create'),
            'edit' => Pages\EditApp::route('/{record}/edit'),
        ];
    }    
}
