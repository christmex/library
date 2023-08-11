<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookStock extends Pivot
{
    use HasFactory;

    protected $table = 'book_stocks';
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
}
