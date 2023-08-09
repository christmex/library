<?php

namespace App\Filament\Resources\BookResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\BookStock;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class BookStocksRelationManager extends RelationManager
{
    protected static string $relationship = 'bookStocks';
    

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('book_location_id')
                    ->relationship('bookLocation', 'book_location_name')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('book_location_name')
                            ->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('book_location_label')
                    ])
                    ->editOptionForm([
                        Forms\Components\TextInput::make('book_location_name')
                            ->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('book_location_label')
                    ])
                    // ->unique(ignoreRecord: true)
                    ->unique(modifyRuleUsing: function (Unique $rule, ?Model $record, string $operation) {
                        if($operation == 'create'){
                            return $rule->where('book_id', $this->ownerRecord->id);
                        }elseif($operation == 'edit'){
                            return $rule->where('book_id', $this->ownerRecord->id)->ignore($record->id);
                        }
                    })
                    // ->validationAttribute('full name')
                    ->required(),
                    Forms\Components\TextInput::make('qty')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->hiddenOn('edit'),
                    Forms\Components\Textarea::make('description')->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('book_location_name')
            ->columns([
                Tables\Columns\TextColumn::make('bookLocation.book_location_name')->label('Book Location Name'),
                Tables\Columns\TextColumn::make('bookLocation.book_location_label')->label('Book Location Label'),
                Tables\Columns\TextColumn::make('qty'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}
