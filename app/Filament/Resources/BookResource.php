<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Book;
use Filament\Tables;
use App\Models\Author;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BookLocation;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BookResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookResource\RelationManagers;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Book';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('book_name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('book_isbn')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('book_publish_year')
                                ->maxLength(255),
                        ]),
                        Forms\Components\Section::make('Images')
                            ->schema([
                                Forms\Components\FileUpload::make('book_cover')
                                    // ->image()
                                    ->preserveFilenames()
                                    ->directory('book-covers')
                                    ->columnSpanFull(),
                            ])->collapsible(),

                            // dont forget to uncomment the bookStocks() inside Book Model
                        // Forms\Components\Section::make('Book Stock')
                        //     ->schema([
                        //         Forms\Components\Repeater::make('bookStocks')
                        //             ->relationship()
                        //             ->schema([
                        //                 Forms\Components\Select::make('book_id')
                        //                     ->relationship('book', 'book_name')
                        //                     ->required(),
                        //                 Forms\Components\Select::make('book_location_id')
                        //                     ->relationship('bookLocation', 'book_location_name')
                        //                     ->createOptionForm([
                        //                         Forms\Components\TextInput::make('book_location_name')
                        //                             ->required()->unique(ignoreRecord: true),
                        //                     ])
                        //                     ->required(),
                        //                 Forms\Components\TextInput::make('qty')
                        //                     ->required(),
                        //             ])
                        //     ])


                    ])->columnSpan(['lg' => 2]),
                
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('author_id')
                                    ->multiple()
                                    // ->label('Tags')
                                    ->relationship(name: 'authors', titleAttribute: 'author_name')
                                    // ->options(Author::all()->pluck('author_name', 'id'))
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('author_name')
                                            ->required()->unique(ignoreRecord: true),
                                    ])
                                    ->searchable(),
                                Forms\Components\Select::make('book_type_id')
                                    ->multiple()
                                    ->relationship(name: 'bookTypes', titleAttribute: 'book_type_name')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('book_type_name')
                                            ->required()->unique(ignoreRecord: true),
                                    ])
                                    ->searchable(),
                                Forms\Components\Select::make('publisher_id')
                                    ->relationship(name: 'publisher', titleAttribute: 'publisher_name')
                                    ->createOptionForm(self::publishersForm())
                                    ->editOptionForm(self::publishersForm())
                                    ->searchable(),
                            ])
                    ])
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('book_cover')
                    ->size(80)
                    // ->square()
                    ,
                Tables\Columns\TextColumn::make('book_name')
                    ->description(function(Book $record){
                        if($record->book_isbn){
                            return "ISBN ".$record->book_isbn."<br>";
                        }
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('authors.author_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('publisher.publisher_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('bookTypes.book_type_name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('bookStocks.qty')
                    ->description(function(Book $record){
                        if($record->book_isbn){
                            return "All Stock: ".$record->bookStocks->sum('qty');
                        }
                    }, )
                    ->searchable(),
                Tables\Columns\TextColumn::make('bookStocks.bookLocation.book_location_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bookStocks.user.name')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->id == 1), 
                // Tables\Columns\TextColumn::make('book_isbn')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('book_publish_year')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
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
                    ->form([
                            Forms\Components\Select::make('book_location_id')
                                ->label('Book Location')
                                ->options(fn(Model $record) =>BookLocation::whereIn('id',$record->bookStocks->where('qty','>',0)->pluck('book_location_id')->toArray())->pluck('book_location_name', 'id'))
                                ->required(),
                            Forms\Components\TextInput::make('qty')
                                ->numeric()
                                ->required()
                    ])
                    ->action(function(array $data, Model $record){
                        return redirect()->route('book_print_book_label', ['book_location_id'=>$data['book_location_id'],'book_id'=>$record->id,'qty' => $data['qty']]);
                    }),
                Tables\Actions\Action::make('print book card')
                    ->icon('heroicon-o-printer')
                    ->form([
                            Forms\Components\Select::make('book_location_id')
                                ->label('Book Location')
                                ->options(fn(Model $record) =>BookLocation::whereIn('id',$record->bookStocks->where('qty','>',0)->pluck('book_location_id')->toArray())->pluck('book_location_name', 'id'))
                                ->required(),
                            Forms\Components\TextInput::make('qty')
                                ->numeric()
                                ->required()
                    ])
                    ->action(function(array $data, Model $record){
                        return redirect()->route('book_print_book_card', ['book_location_id'=>$data['book_location_id'],'book_id'=>$record->id,'qty' => $data['qty']]);
                    }),

                
                // Tables\Actions\Action::make('addStock')
                //     ->form([
                //         // Forms\Components\Select::make('book_location_id')
                //         //     ->relationship('bookLocation', 'book_location_name')
                //         Forms\Components\Select::make('book_location_id')
                //             ->label('Author')
                //             ->options(User::query()->pluck('name', 'id'))
                //             ->required(),
                //     ])
                //     ->action(function (array $data): void {
                //         dd($this->record);
                //         // $this->record->author()->associate($data['authorId']);
                //         // $this->record->save();
                //     })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            // ->headerActions([
            //     Tables\Actions\Action::make('addStock')
            //         ->form([
            //             // Forms\Components\Select::make('book_location_id')
            //             //     ->relationship('bookLocation', 'book_location_name')
            //             Forms\Components\Select::make('book_id')
            //                 ->options(Book::query()->pluck('book_name', 'id'))
            //                 ->required(),
            //         ])
            //         ->action(function (array $data): void {
            //             dd($data);
            //             // $this->record->author()->associate($data['authorId']);
            //             // $this->record->save();
            //         })
            // ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            // RelationManagers\BookStocksRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         \Filament\Actions\Action::make('addStock')
    //         ->form([
    //             // Forms\Components\Select::make('book_location_id')
    //             //     ->relationship('bookLocation', 'book_location_name')
    //             Forms\Components\Select::make('book_id')
    //                 ->options(Book::query()->pluck('book_name', 'id'))
    //                 ->required(),
    //         ])
    //         ->action(function (array $data): void {
    //             dd($data);
    //             // $this->record->author()->associate($data['authorId']);
    //             // $this->record->save();
    //         }),
    //     ];
    // }


    public static function publishersForm(){
        return [
            Forms\Components\TextInput::make('publisher_name')
                ->required()->unique(ignoreRecord: true),
        ];
    }
}
