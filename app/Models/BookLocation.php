<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
