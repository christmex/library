<?php

use App\Models\BookLocation;
use App\Models\BookStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/book-locations/print-book-label/{record}', function (BookLocation $record){
    $entry = $record;
    return view('print_book_label',compact('entry'));
})->name('book_location_print_book_label');

Route::get('/book-locations/print-book-card/{record}', function (BookLocation $record){
    $entry = $record;
    return view('print_book_card',compact('entry'));
})->name('book_location_print_book_card');

Route::get('/books/print-book-label/{book_location_id}/{book_id}/{qty}', function (string $book_location_id,string $book_id, string $qty){
    $entry = BookStock::with('bookLocation','book')->where('book_location_id',$book_location_id)->where('book_id',$book_id)->first();
    return view('print_book_label_single',compact('entry','qty'));
})->name('book_print_book_label');
Route::get('/books/print-book-card/{book_location_id}/{book_id}/{qty}', function (string $book_location_id,string $book_id, string $qty){
    $entry = BookStock::with('bookLocation','book')->where('book_location_id',$book_location_id)->where('book_id',$book_id)->first();
    return view('print_book_card_single',compact('entry','qty'));
})->name('book_print_book_card');
