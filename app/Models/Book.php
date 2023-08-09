<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $casts = [
    //     'book_authors' => 'array',
    //     'book_cover' => 'array',
    // ];

    public function setBookNameAttribute($value)
    {
        $this->attributes['book_name'] = ucwords($value);
    }

    /**
     * The authors that belong to the book.
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }
    /**
     * The bookTypes that belong to the book.
     */
    public function bookTypes(): BelongsToMany
    {
        return $this->belongsToMany(BookType::class);
    }
    /**
     * The bookStocks that belong to the book. use this if you use repeater
     */
    public function bookStocks(): HasMany
    {
        return $this->hasMany(BookStock::class);
    }
    /**
     * The publishers that belong to the book.
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    // public function authors(){
    //     return $this->belongsTo(Author::class,'book_authors','id');
    // }
    // public function publishers(){
    //     return $this->belongsTo(Publisher::class,'book_publishers','id');
    // }
    // public function bookTypes(){
    //     return $this->belongsTo(BookType::class,'book_types','id');
    // }

    // public function getAuthorNamesAttribute()
    // {
    //     $authorIds = $this->book_authors;
    //     $authors = Author::whereIn('id', $authorIds)->pluck('author_name')->toArray();
    //     return implode(', ', $authors);
    // }
}
