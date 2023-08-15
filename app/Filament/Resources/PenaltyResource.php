<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenaltyResource\Pages;
use App\Filament\Resources\PenaltyResource\RelationManagers;
use App\Models\Penalty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenaltyResource extends Resource
{
    protected static ?string $model = Penalty::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationGroup = 'Transaction';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction.bookStock.book.book_name')
                    ->label('Book Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction.member.member_name')
                    ->label('Member\'s Name')
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
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => auth()->user()->id == 1), 
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
