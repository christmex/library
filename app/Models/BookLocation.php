<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookLocation extends Model
{
    use HasFactory, Multitenantable;
    protected $guarded = [];

    public function setBookLocationNameAttribute($value)
    {
        $this->attributes['book_location_name'] = ucwords($value);
    }
    public function setBookLocationLabelAttribute($value)
    {
        $this->attributes['book_location_label'] = ucwords($value);
    }

    public function bookStock(){
        return $this->hasMany(BookStock::class);
    }
    
    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    } 
}
