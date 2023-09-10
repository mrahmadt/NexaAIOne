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
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Actions\Action;
use App\Models\AIEndPoint;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\Grid;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


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
                Select::make('ai_end_points_id')
                ->relationship(name: 'aiendpoint', titleAttribute: 'name')
                ->loadingMessage('Loading...')->live()
                ->afterStateUpdated(function (Set $set, Get $get) {
                    $AIEndPoint = AIEndPoint::where(['id'=>$get('ai_end_points_id'), 'isActive'=>true ])->first();
                    $set('description', $AIEndPoint->requestSchema[0]['desc']);
                    $set('AIEndPoint', $AIEndPoint->toArray());

                    $set('supportHistory', $AIEndPoint->supportHistory);
                    $set('supportCaching', $AIEndPoint->supportCaching);

                    $set('members', $AIEndPoint->requestSchema);
                }),

                TextInput::make('name')->columnSpan('full')->required(),
                Textarea::make('description')->columnSpan('full')->nullable(),
                TextInput::make('apiName')->columnSpan('full')->prefix('https://.../api/')->required(),

                Toggle::make('supportHistory')->disabled(function (Get $get, string $operation) { return (($operation=='create') && (isset($get('AIEndPoint')['supportHistory']))) ? (!$get('AIEndPoint')['supportHistory']) : false; }),
                Toggle::make('supportCaching')->disabled(function (Get $get, string $operation) { return (($operation=='create') && (isset($get('AIEndPoint')['supportCaching']))) ? (!$get('AIEndPoint')['supportCaching']) : false; }),

                TextInput::make('operation')->columnSpan('full')->default(function (string $operation) { return $operation; }),

                Repeater::make('members')
                ->schema([
                    TextInput::make('name')->required(),
                    Select::make('role')
                        ->options([
                            'member' => 'Member',
                            'administrator' => 'Administrator',
                            'owner' => 'Owner',
                        ])
                        ->required(),
                ])->columns(2)->columnSpan('full'),
                // Select::make('ai_end_points_id')
                // ->label('AI End Points')
                // ->options(AIEndPoint::all()->pluck('name', 'id')),


                // Select::make('type')
                // ->options([
                //     'employee' => 'Employee',
                //     'freelancer' => 'Freelancer',
                // ])
                // ->live()
                // ->afterStateUpdated(fn (Select $component) => $component
                //     ->getContainer()
                //     ->getComponent('dynamicTypeFields')
                //     ->getChildComponentContainer()
                //     ->fill()),
                
                //     Grid::make(2)
                //     ->schema(fn (Get $get): array => match ($get('type')) {
                //         'employee' => [
                //             TextInput::make('employee_number')
                //                 ->required(),
                //             FileUpload::make('badge')
                //                 ->image()
                //                 ->required(),
                //         ],
                //         'freelancer' => [
                //             TextInput::make('hourly_rate')
                //                 ->numeric()
                //                 ->required()
                //                 ->prefix('€'),
                //             FileUpload::make('contract')
                //                 ->required(),
                //         ],
                //         default => [],
                //     })
                //     ->key('dynamicTypeFields'),

                // TextInput::make('aiendpoint.apiName')->columnSpan('full')->required()->key('dynamicTypeFields'),

                // Textarea::make('description')->columnSpan('full')->nullable(),
                // Toggle::make('enableUsage')->required(),
                // Toggle::make('enableHistory')->required(),
                // Textarea::make('historyMethod_id')->columnSpan('full')->nullable(),
                // Textarea::make('historyOptions')->columnSpan('full')->nullable(),

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
