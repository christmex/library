<?php

namespace App\Filament\Resources\BookResource\Pages;

use App\Models\Book;
use Filament\Actions;
use Filament\Forms\Get;
use App\Models\BookStock;
use App\Imports\BookImport;
use App\Models\BookLocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Filament\Resources\BookResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListBooks extends ListRecords
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ActionGroup::make([
                Actions\CreateAction::make()->icon('heroicon-o-plus'),
                \Filament\Actions\Action::make('addStock')->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('book_id')
                        // ->options(Book::query()->whereHas('bookStocks')->pluck('book_name', 'id'))
                        ->options(Book::query()->pluck('book_name', 'id'))
                        ->searchable()
                        ->required()
                        // ->live()
                        ,
                    \Filament\Forms\Components\Select::make('book_location_id')
                        ->options(BookLocation::query()->pluck('book_location_name', 'id'))
                        // ->options(function(Get $get){
                        //     $bookLocationId = BookStock::query()
                        //             ->where('book_id', $get('book_id'))
                        //             ->get()->pluck('book_location_id')->toArray();
                            
                        //     return BookLocation::whereIn('id',$bookLocationId)->pluck('book_location_name','id');
                        // })
                        // ->visible(fn (Get $get): bool => $get('book_id'))
                        // ->createOptionForm([
                        //     \Filament\Forms\Components\TextInput::make('book_location_name')
                        //         ->required()->unique(ignoreRecord: true),
                        //     \Filament\Forms\Components\TextInput::make('book_location_label'),
                        // ])
                        // ->editOptionForm([
                        //     \Filament\Forms\Components\TextInput::make('book_location_name')
                        //         ->required()->unique(ignoreRecord: true),
                        //     \Filament\Forms\Components\TextInput::make('book_location_label'),
                        // ])
                        ->searchable()
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('qty')
                    ->numeric()
                    ->minValue(1)
                    // ->visible(fn (Get $get): bool => $get('book_location_id'))
                    ->required()
                ])
                ->action(function (array $data) {
                    try {
                        $get = BookStock::firstOrCreate(
                            [
                                'book_id' => $data['book_id'],
                                'book_location_id' => $data['book_location_id'],
                            ],
                            [
                                'qty' => $data['qty'],
                            ]
                        );
                        if (!$get->wasRecentlyCreated) {
                            $get->qty += $data['qty'];
                            $get->save();
                            // if(){
                            //     Notification::make()
                            //         ->success()
                            //         ->title('Stock Added')
                            //         ->send();
                            // }
                        }

                        Notification::make()
                            ->success()
                            ->title('Stock Added')
                            ->send();

                        // $get = BookStock::where('book_id',$data['book_id'])->where('book_location_id',$data['book_location_id'])->first();
    
                        // $get->qty += $data['qty'];
                        // if($get->qty < 0){
                        //     Notification::make()
                        //         ->danger()
                        //         ->title('Book stock to small')
                        //         ->send();
                        // }else {
                            // if($get->save()){
                            //     Notification::make()
                            //         ->success()
                            //         ->title('Stock Added')
                            //         ->send();
                            // }
                        // }
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->danger()
                            ->title($th->getMessage())
                            ->send();
                    }
                })
                ->icon('heroicon-o-plus'),
                \Filament\Actions\Action::make('removeStock')
                    ->form([
                        \Filament\Forms\Components\Select::make('book_id')
                        ->options(Book::query()->whereHas('bookStocks')->pluck('book_name', 'id'))
                        ->searchable()
                        ->required()
                        ->live()
                        ,
                        \Filament\Forms\Components\Select::make('book_location_id')
                            ->options(function(Get $get){
                                $bookLocationId = BookStock::query()
                                        ->where('book_id', $get('book_id'))
                                        ->get()->pluck('book_location_id')->toArray();
                                
                                return BookLocation::whereIn('id',$bookLocationId)->pluck('book_location_name','id');
                            })
                            ->searchable()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('qty')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                    ])
                    ->action(function (array $data) {
                        try {
                            $get = BookStock::where('book_id',$data['book_id'])->where('book_location_id',$data['book_location_id'])->first();
                            $get->qty -= $data['qty'];
                            if($get->qty < 0){
                                Notification::make()
                                    ->danger()
                                    ->title('The actuall stock less than your input')
                                    ->send();
                            }else {
                                // nanti disini cek jika buku ini ada di data transaksi dan lagi dipinjam, maka save saja, jadi semisal jadi 0 tetap 0,kecuali sudah tidak ada peminjaman yang aktif maka langsung delete saja
                                if($get->save()){
                                    Notification::make()
                                        ->danger()
                                        ->title('Stock Removed')
                                        ->send();
                                }
                            }
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->danger()
                                ->title($th->getMessage())
                                ->send();
                        }
                        
                    })
                    ->color('danger')
                    ->icon('heroicon-o-minus')
            ])
                ->label('More actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('primary')
                ->button(),
            // \Filament\Actions\Action::make('importBook')->color('success')
            //     ->form([
            //         \Awcodes\Shout\Components\Shout::make('should-return-at')
            //             ->content('Download the template file before upload')
            //             ->type('info'),
            //         \Filament\Forms\Components\FileUpload::make('import_book')
            //             ->storeFiles(false)
            //             ->columnSpanFull(),
            //     ])
            //     ->action(function(array $data){
            //         DB::beginTransaction();
            //         try {
            //             Excel::import(new BookImport, $data['import_book']);
            //             DB::commit();
            //             Notification::make()
            //                 ->success()
            //                 ->title('Book imported')
            //                 ->send();
            //         } catch (\Throwable $th) {
            //             DB::rollback();
            //             Notification::make()
            //                 ->danger()
            //                 ->title($th->getMessage())
            //                 ->send();
            //         }
            // })

        ];
    }
}
