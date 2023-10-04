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
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\HtmlString;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;
    protected static ?string $label = 'Collection';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'My Data';

    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        if($form->getOperation() == 'create'){
            $introDescription = Placeholder::make('')->content(new HtmlString("A Collection serves as a structured data store for text-based <b>documents</b>. You can populate this Collection either via the Collection API endpoint or through the Admin Portal.
            <br><br>
            The primary function of a Collection is to extend the knowledge base accessible by an AI service. When creating an API, you can specify which Collection the AI should reference for its responses. This allows you to tailor the AI's behavior and the information it draws upon, depending on the context in which it's used.
            <br><br>
            <b>Example Use Case:</b>
            Suppose you have a chatbot aimed at handling HR-related queries (HRChatBot). You can create a Collection named 'HR_Policies' and upload all relevant HR documents into it. When a user asks a question to your 'HR ChatBot' or your 'ERP', the backend can be configured to call the API, which will then consult the 'HR_Policies' Collection to retrieve and generate a response based on the information it contains.
            <br><br>
            <b>Technical Note:</b>
            This mechanism utilizes a method known as Retrieval-Augmented Generation (RAG). RAG empowers the AI to scan the Collection and identify the most relevant information to construct its responses."))->columnSpanFull();
        }else{
            $introDescription = Placeholder::make('')->content('')->columnSpanFull();
        }
        return $form
        ->schema([
            $introDescription,
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(40),
            Forms\Components\TextInput::make('description')
                ->maxLength(255),
            Forms\Components\TextInput::make('authToken')
            ->helperText('Bearer authentication tokens that you can use to access this Collection via the Collection APIs.')
            ->label('Bearer Token')
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
            Forms\Components\Textarea::make('context_prompt')
            ->default("Answer the following Question based on the Context only. Only answer from the Context. When you want to refer to the context provided, call it 'HR Policy' not just 'context'. Try to provide a reference to the HR Policy number. If you don't know the answer mention that you couldn't find the answer in the HR Policy\nCONTEXT: {{context}}\n\nnQuestion:{{UserMessage}}")
                ->rows(4)
                ->maxLength(255),
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
            ->label('Max Documents to share with AI service for each API call?')
            ->required()
            ->default(3),
            Forms\Components\Select::make('loader_id')
                ->label('Documents Loader')
                ->helperText('Document loader provides a "load/download" and "extract text" method for any specified URL or file.')
                ->relationship(name: 'loader', titleAttribute: 'name')
                ->preload()
                ->default(1)
                ->searchable(['name', 'description'])
                ->loadingMessage('Loading...')->live(),
            Forms\Components\Select::make('splitter_id')
                ->label('Text Splitter')
                ->helperText('Often times you want to split large text into smaller chunks to better work with language models. TextSplitters are responsible for splitting up large text into smaller documents.')
                ->relationship(name: 'splitter', titleAttribute: 'name')
                ->preload()
                ->default(1)
                ->searchable(['name', 'description'])
                ->loadingMessage('Loading...')->live(),
            Forms\Components\Select::make('embedder_id')
            ->label('Embedder')
            ->helperText('Embeddings create a vector representation of a document to do things like semantic search where we look for pieces of text that are most similar to an API query.')
                ->relationship(name: 'embedder', titleAttribute: 'name')
                ->preload()
                ->required()
                ->default(1)
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
