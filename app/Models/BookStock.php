<?php

namespace App\Models;

use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookStock extends Pivot
{
    use HasFactory, Multitenantable;

    protected $table = 'book_stocks';

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            if($obj->id && auth()->user()->email == 'super@sekolahbasic.sch.id'){
                Transaction::where('transaction_returned_at','!=',null)->where('book_stock_id', $obj->id)->delete();
            }
        });
    }
    public function bookLocation(): BelongsTo
    {
        return $this->belongsTo(BookLocation::class);
    }
    
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function getBookLocationNameAttribute()
    {
        return $this->bookLocation->book_location_name;
    }
    public function getBookNameAttribute()
    {
        return $this->book->book_name;
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }
}
