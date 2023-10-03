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
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class AppResource extends Resource
{
    protected static ?string $model = App::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?int $navigationSort = 1;
    protected static ?string $label = 'App';

    public static function form(Form $form): Form
    {
        if($form->getOperation() == 'create'){
            $introDescription = Placeholder::make('')->content('Create an App and associate APIs to it so you can use Auth Token to access them from your application.');
        }else{
            $introDescription = Placeholder::make('')->content('Edit an App and associate APIs to it so you can use Auth Token to access them from your application.');
        }
        return $form
        ->schema([
            $introDescription,
            Forms\Components\TextInput::make('name')
                ->required()
                ->helperText('Any name to help you identify this app.')
                ->maxLength(40),
            Forms\Components\TextInput::make('description')
                ->maxLength(255),
            Forms\Components\TextInput::make('owner')
                ->maxLength(60),
            Forms\Components\TextInput::make('authToken')
                ->helperText(new HtmlString('Bearer authentication tokens that you can use to access any of the associate APIs. <a href="https://swagger.io/docs/specification/authentication/bearer-authentication/">Bearer Authentication</a>'))
                ->required()
                ->minLength(20)
                ->maxLength(100)
                ->label('Bearer Token')
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
            Forms\Components\Toggle::make('isActive')->label('Is Active')
                ->helperText('Enable/Disable App.')
                ->required()->default(true),
            Forms\Components\Select::make('services')
                ->helperText('List of APIs allowed to be used by this app.')
                ->multiple()
                ->relationship(name: 'apis', titleAttribute: 'name',modifyQueryUsing: function (Builder $query){ 
                    $query
                    ->distinct() // Ensure that results are unique when fetching options.
                    ->select('apis.id','apis.name','apis.description')->orderBy('apis.id')->where('apis.isActive',true);
                })
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
                Tables\Actions\Action::make('Docs')
                ->icon('heroicon-s-code-bracket-square')
                ->url(fn (App $record) => route('api-docs.app', ['appDocToken'=>$record->docToken])),
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
