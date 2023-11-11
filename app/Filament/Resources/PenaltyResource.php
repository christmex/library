<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Penalty;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PenaltyResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PenaltyResource\RelationManagers;

class PenaltyResource extends Resource
{
    protected static ?string $model = Penalty::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = 'Transaction';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('penalty_description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('transaction.bookStock.book.book_cover')
                    ->size(80)
                    ->label('Book Cover'),
                Tables\Columns\TextColumn::make('transaction.bookStock.book.book_name')
                    ->label('Book Name')
                    ->searchable()
                    ->wrap()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction.member.member_name')
                    ->label('Member\'s Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction.member.department_name')
                    ->label('Member Department'),
                Tables\Columns\TextColumn::make('transaction.transaction_loaned_at')
                    ->label('Loaned At')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction.transaction_returned_at')
                    ->label('Returned At')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\SelectColumn::make('penalty_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'free' => 'Free',
                    ])
                    ->selectablePlaceholder(false)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penalty_cost')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penalty_description')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => auth()->user()->id == 1), 
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('penalty_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'free' => 'Free',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePenalties::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('penalty_status','unpaid')->count();
    }
}
