<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use Carbon\Carbon;
use App\Models\Book;
use Filament\Actions;
use App\Models\Member;
use Filament\Forms\Get;
use App\Models\BookStock;
use App\Models\Transaction;
use App\Models\BookLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TransactionResource;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            \Filament\Actions\Action::make('newTransaction')->color('success')
            ->form([
                // \Filament\Forms\Components\Repeater::make('transactions')
                // ->schema([
                    \Awcodes\Shout\Components\Shout::make('should-return-at')
                    ->content(function(){
                        // $loanedDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        // $dueDate = Carbon::createFromFormat('Y-m-d',Carbon::createFromFormat('Y-m-d', date('Y-m-d'))->addDays(env('loanExpDays'))->format('Y-m-d'));

                        $loanedDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        $dueDate = $loanedDate->addDays(env('loanExpDays'))->format('Y-m-d');
                        $Blacklist_days = ['Saturday','Sunday'];



                        $blacklistDueDate = in_array(Carbon::createFromFormat("Y-m-d", $dueDate)->format("l"),$Blacklist_days);
                        // dd($blacklistDueDate);
                        if($blacklistDueDate){
                            // Get the date
                            $dueDate = $loanedDate;

                            // Calculate the days until the next Monday (0 = Sunday, 1 = Monday, 2 = Tuesday, etc.)
                            $daysUntilMonday = 7 - $dueDate->dayOfWeek + 1;

                            // Add the days to the get the next Monday
                            $nextMonday = $dueDate->addDays($daysUntilMonday);

                            // Format the date as a string if needed
                            $dueDate = $nextMonday->format('Y-m-d');
                            // 
                        }
                        
                        return 'If you loan today it should return at '.Carbon::createFromFormat('Y-m-d',Carbon::createFromFormat('Y-m-d', $dueDate)->format('Y-m-d'))->isoFormat('dddd, D MMMM YYYY');


                    })
                    ->type('warning'),
                    \Filament\Forms\Components\Select::make('member_id')
                    // ->options(Member::pluck('member_name','id'))
                        ->relationship(name: 'member', titleAttribute: 'member_name')
                        ->label('Member Name')
                        ->createOptionForm([
                            \Filament\Forms\Components\Select::make('department_id')
                                ->relationship(name: 'department', titleAttribute: 'department_name')
                                ->createOptionForm([
                                    \Filament\Forms\Components\TextInput::make('department_name')
                                        ->required()
                                        ->unique(ignoreRecord: false)
                                        ->maxLength(255),
                                ])
                                ->editOptionForm([
                                    \Filament\Forms\Components\TextInput::make('department_name')
                                        ->required()
                                        ->unique(ignoreRecord: false)
                                        ->maxLength(255),
                                ])
                                ->searchable(),
                            \Filament\Forms\Components\TextInput::make('member_name')
                                ->required()
                                ->maxLength(255),
                            \Filament\Forms\Components\TextInput::make('member_phone_number')
                                ->tel()
                                ->maxLength(255),
                            \Filament\Forms\Components\FileUpload::make('member_profile_picture')
                                ->preserveFilenames()
                                ->directory('member-profile-picture')
                                ->columnSpanFull(),
                        ])
                        ->editOptionForm([
                            \Filament\Forms\Components\Select::make('department_id')
                                ->relationship(name: 'department', titleAttribute: 'department_name')
                                ->createOptionForm([
                                    \Filament\Forms\Components\TextInput::make('department_name')
                                        ->required()
                                        ->unique(ignoreRecord: false)
                                        ->maxLength(255),
                                ])
                                ->editOptionForm([
                                    \Filament\Forms\Components\TextInput::make('department_name')
                                        ->required()
                                        ->unique(ignoreRecord: false)
                                        ->maxLength(255),
                                ])
                                ->searchable(),
                            \Filament\Forms\Components\TextInput::make('member_name')
                                ->required()
                                ->maxLength(255),
                            \Filament\Forms\Components\TextInput::make('member_phone_number')
                                ->tel()
                                ->maxLength(255),
                            \Filament\Forms\Components\FileUpload::make('member_profile_picture')
                                ->preserveFilenames()
                                ->directory('member-profile-picture')
                                ->columnSpanFull(),
                        ])
                        ->searchable()
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->member_name} {$record->department_name}")
                        ->required(),

                    // \Filament\Forms\Components\Select::make('member_id')
                    //     ->options(Member::pluck('member_name','id'))
                    //     ->label('Member Name')
                    //     ->searchable()
                    //     ->required(),

                    \Filament\Forms\Components\Select::make('book_id')
                        ->options(Book::query()->whereHas('bookStocks',fn($q) => $q->where('qty','>',0))->pluck('book_name', 'id'))
                        ->searchable()
                        ->required()
                        ->live()
                        ,
                    \Filament\Forms\Components\Select::make('book_location_id')
                        ->options(function(Get $get){
                            $bookLocationId = BookStock::query()
                                    ->where('book_id', $get('book_id'))
                                    ->where('qty','>',0)
                                    ->get()->pluck('book_location_id')->toArray();
                            
                            return BookLocation::whereIn('id',$bookLocationId)->pluck('book_location_name','id');
                        })
                        ->visible(fn (Get $get): ?bool => $get('book_id'))
                        ->searchable()
                        ->live()
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('qty')
                        ->numeric()
                        // ->default(1) // if we are set this it will show previous field so it wont hide previous field
                        ->minValue(1)
                        ->visible(fn (Get $get): ?bool => $get('book_location_id'))
                        ->live()
                        ->required(),
                    \Filament\Forms\Components\DatePicker::make('transaction_loaned_at')
                        ->label('Loaned At')
                        // ->format('d/m/Y')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->closeOnDateSelection()
                        ->default(now())
                        ->maxDate(now())
                        ->visible(fn (Get $get): ?bool => $get('qty'))
                        ->required(),
                // ])
            ])
            ->action(function(array $data){
                DB::beginTransaction();
                try {
                    $selectBookStock = BookStock::where('book_id',$data['book_id'])->where('book_location_id',$data['book_location_id'])->first();
                    // check if the qty from user bigger than actual stock
                    if($data['qty'] > $selectBookStock->qty){
                        Notification::make()
                            ->danger()
                            ->title('Not enough stock, available in stock:'.$selectBookStock->qty)
                            ->send();
                    }else {
                        $selectBookStock->qty -= $data['qty'];
                        $selectBookStock->save();
                        Transaction::create([
                            'book_stock_id' => $selectBookStock->id,
                            'member_id' => $data['member_id'],
                            'transaction_book_qty' => $data['qty'],
                            'transaction_loaned_at' => $data['transaction_loaned_at'],
                        ]);
                        DB::commit();
                        Notification::make()
                            ->success()
                            ->title('New Transaction Added')
                            ->send();
                    }
                } catch (\Throwable $th) {
                    DB::rollback();
                    Notification::make()
                        ->danger()
                        ->title($th->getMessage())
                        ->send();
                }
            })
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('primary')
        ];
    }

    public function getTabs(): array
    {
        return [
            'unreturn' => \Filament\Resources\Pages\ListRecords\Tab::make('On Going')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('transaction_returned_at', null))
                ->badge(Transaction::query()->where('transaction_returned_at', null)->count()),
            'returned' => \Filament\Resources\Pages\ListRecords\Tab::make('Returned')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('transaction_returned_at', '!=',null)),
            'all' => \Filament\Resources\Pages\ListRecords\Tab::make('All'),
        ];
    }
}
