<?php

namespace App\Filament\Resources\CollectionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\KeyValue;
use App\Filament\Resources\DocumentResource;
class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Form $form): Form
    {
        return DocumentResource::form($form);
    }

    public function table(Table $table): Table
    {
        return DocumentResource::table($table)->headerActions([
            Tables\Actions\CreateAction::make(),
        ]);
    }
}
