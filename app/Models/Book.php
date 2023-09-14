<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory, Multitenantable;

    protected $guarded = [];

    // protected $casts = [
    //     'book_authors' => 'array',
    //     'book_cover' => 'array',
    // ];

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            if($obj->id && auth()->user()->email == 'super@sekolahbasic.sch.id'){
                // BookStock::where('book_id', $obj->id)->delete();
                // DB::delete('delete author_book where book_id = ?', [$obj->id]);
                DB::table('author_book')->where('book_id', $obj->id)->delete();
                DB::table('book_book_type')->where('book_id', $obj->id)->delete();
                // DB::where('book_id', $obj->id)->delete();
                $obj->bookStocks->each->delete();
            }
        });
    }

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
        // return $this->hasMany(BookStock::class)->where('user_id', auth()->id());
    }
    /**
     * The publishers that belong to the book.
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }
    /**
     * The book source that belong to the book.
     */
    public function bookSource(): BelongsTo
    {
        return $this->belongsTo(BookSource::class);
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

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }
}
