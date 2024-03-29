<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Book;
use Filament\Tables;
use App\Models\Member;
use App\Models\Penalty;
use Filament\Forms\Form;
use App\Models\BookStock;
use Filament\Tables\Table;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-s-hashtag';

    protected static ?string $navigationGroup = 'Transaction';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('book_stock_id')
                    ->relationship(
                        name: 'bookStock.book',
                        titleAttribute: 'book.book_name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('qty','>',0),
                    )
                    // ->options(Book::query()->whereHas('bookStocks',fn ($q) => $q->where('qty','>',0))->pluck('book_name', 'id'))
                    // ->options(function(){
                    //     return Book::query()->whereHas('bookStocks',function($q){
                    //         $q->where('qty','>',0);
                    //     })->pluck('book_name', 'id');
                    // })
                    ->multiple()
                    ->label('Book Name')
                    ->required(),
                Forms\Components\Select::make('member_id')
                    // ->options(Member::pluck('member_name','id'))
                    ->relationship(name: 'member', titleAttribute: 'member_name')
                    ->label('Member Name')
                    ->createOptionForm([
                        Forms\Components\Select::make('department_id')
                            ->relationship(name: 'department', titleAttribute: 'department_name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('department_name')
                                    ->required()
                                    ->unique(ignoreRecord: false)
                                    ->maxLength(255),
                            ])
                            ->editOptionForm([
                                Forms\Components\TextInput::make('department_name')
                                    ->required()
                                    ->unique(ignoreRecord: false)
                                    ->maxLength(255),
                            ])
                            ->searchable(),
                        Forms\Components\TextInput::make('member_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('member_phone_number')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('member_profile_picture')
                            ->preserveFilenames()
                            ->directory('member-profile-picture')
                            ->columnSpanFull(),
                    ])
                    ->editOptionForm([
                        Forms\Components\Select::make('department_id')
                            ->relationship(name: 'department', titleAttribute: 'department_name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('department_name')
                                    ->required()
                                    ->unique(ignoreRecord: false)
                                    ->maxLength(255),
                            ])
                            ->editOptionForm([
                                Forms\Components\TextInput::make('department_name')
                                    ->required()
                                    ->unique(ignoreRecord: false)
                                    ->maxLength(255),
                            ])
                            ->searchable(),
                        Forms\Components\TextInput::make('member_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('member_phone_number')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('member_profile_picture')
                            ->preserveFilenames()
                            ->directory('member-profile-picture')
                            ->columnSpanFull(),
                    ])
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('transaction_book_qty')
                    ->label('Qty')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('transaction_loaned_at')
                    ->label('Loaned At')
                    // ->format('d/m/Y')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection()
                    ->default(now())
                    ->maxDate(now())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('bookStock.book.book_cover')
                    ->size(80)
                    ->label('Book Cover'),
                Tables\Columns\TextColumn::make('bookStock.book.book_name')
                    ->label('Book Name')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bookStock.bookLocation.book_location_name')
                    ->label('Book Location')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.member_name')
                    ->label('Member name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('member.department.department_name')
                    ->label('Member Department')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_book_qty')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => auth()->user()->id == 1), 
                Tables\Columns\TextColumn::make('transaction_loaned_at')
                    ->label('Loaned At')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('should_return_at')
                    ->date(),
                Tables\Columns\TextColumn::make('transaction_returned_at')
                    ->label('Returned At')
                    ->date()
                    ->sortable(),
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
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('returnBook')
                        ->requiresConfirmation()
                        ->icon('heroicon-m-arrows-right-left')
                        ->color('warning')
                        ->action(function(Collection $records){
                            DB::beginTransaction();
                            try {
                                foreach ($records as $value) {
                                    // Cek aja jika returned_at udah ada, maka ngg usah diapaapain
                                    if(empty($value->returned_at)){
                                        // Set returned at in transaction table
                                        $value->update(['transaction_returned_at' => date('Y-m-d')]);
                                        $value->save();
                                        // increase the book stock in book_stocks table
                                        BookStock::find($value->book_stock_id)->increment('qty',$value->transaction_book_qty);

                                        // Check if this transaction have pelanty
                                        // Check if in env there is loanExpDays
                                        if(!empty(env('loanExpDays'))){
                                            $loanedDate = Carbon::createFromFormat('Y-m-d', $value->transaction_loaned_at);
                                            $dueDate = $loanedDate->addDays(env('loanExpDays'))->format('Y-m-d');
                                            $now = date('Y-m-d');
                                            // $now = "2023-09-19";

                                            $Blacklist_days = ['Saturday','Sunday'];
                                            $blacklistDueDate = in_array(Carbon::createFromFormat("Y-m-d", $dueDate)->format("l"),$Blacklist_days);
                                            if($blacklistDueDate){
                                            //    // Get the date
                                            //     $dueDate = $loanedDate;

                                            //     // Calculate the days until the next Monday (0 = Sunday, 1 = Monday, 2 = Tuesday, etc.)
                                            //     $daysUntilMonday = 7 - $dueDate->dayOfWeek + 1;

                                            //     // Add the days to the get the next Monday
                                            //     $nextMonday = $dueDate->addDays($daysUntilMonday);

                                                // $specificDate = Carbon::createFromDate(date('Y'), date('m'), date('d')); // Ganti dengan tanggal yang Anda inginkan
                                                $nextMonday = $loanedDate->next('Monday');

                                                // Set ulang tanggalnya ke hari senin jika masa peminjaman berakhir di sabtu minggu
                                                $dueDate = $nextMonday->format('Y-m-d');
                                            }

                                            if($now > $dueDate){
                                                // Calculate the difference between the two dates.
                                                $diff = Carbon::createFromFormat('Y-m-d', $dueDate)->diff($now);
                                                // i add + 1 supaya jika 3 hari setelah peminjaman adalah 17 dan hari ini 18, maka akan dihitung telat 1 hari, karna carbon tidak menganggap 17 dan 18 itu selisih 1 haari, kecuali di hari berikutnya 17 dan 19 ituadalah selisih 1 hari
                                                $penaltyCost = ($diff->days + 1) * env('penaltyCost');
                                                $isTitlePenaltySet = true;
                                                Penalty::create([
                                                    'transaction_id' => $value->id,
                                                    'penalty_status' => 'unpaid',
                                                    'penalty_cost' => $penaltyCost,
                                                ]);
                                                Notification::make()
                                                ->success()
                                                ->title('Please check the penalty page')
                                                ->actions([
                                                    \Filament\Notifications\Actions\Action::make('view')
                                                        ->button()
                                                        ->url(route('filament.admin.resources.penalties.index'))
                                                ])
                                                ->send();
                                            }
                                        }

                                        // $titleMsg = $isTitlePenaltySet ? 'Successfuly returned the book, please see the' :'Successfuly returned the book';
                                        $titleMsg = 'Successfuly returned the book';
                                        
                                        DB::commit();
                                        Notification::make()
                                            ->success()
                                            ->title($titleMsg)
                                            // ->actions([
                                            //     \Filament\Notifications\Actions\Action::make('view')
                                            //         ->button()
                                            //         ->url(route('penalties.index'))
                                            // ])
                                            ->send();
                                    }
                                }
                            } catch (\Throwable $th) {
                                DB::rollback();
                                Notification::make()
                                    ->danger()
                                    ->title($th->getMessage())
                                    ->send();
                            }
                            // dd($records);
                            
                        })
                        ->deselectRecordsAfterCompletion()
                    ,
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => $record->transaction_returned_at === null,
            )
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            // 'create' => Pages\CreateTransaction::route('/create'),
            // 'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('transaction_returned_at',null)->count();
    }
}
