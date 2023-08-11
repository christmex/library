<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BookLocation;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookLocationResource\Pages;
use App\Filament\Resources\BookLocationResource\RelationManagers;

class BookLocationResource extends Resource
{
    protected static ?string $model = BookLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Book';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('book_location_name')
                    ->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('book_location_label'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('book_location_name')
                ->searchable(),
                Tables\Columns\TextColumn::make('book_location_label')
                ->searchable(),
                Tables\Columns\TextColumn::make('bookStock.book.book_name')
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
                    Tables\Actions\Action::make('print book label')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Model $record): string => route('book_location_print_book_label', $record))
                    ->openUrlInNewTab(),
                    Tables\Actions\Action::make('print book card')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Model $record): string => route('book_location_print_book_card', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\BulkAction::make('printBookLocation')
                    // ->url(fn (Collection $records): string => route('print_book_label', ['records' => $records]))
                    // ->action(function(Collection $records){
                    //     // dd($records);
                    //     // return view('welcome');
                    //     // return view('print_book_label',['data' => $records]);
                    //     // return redirect(route('book_location_print_book_label',['id' => $records->id]));
                    // })
                    // ->deselectRecordsAfterCompletion()
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBookLocations::route('/'),
        ];
    }    
}
