<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookLocation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function setBookLocationNameAttribute($value)
    {
        $this->attributes['book_location_name'] = ucwords($value);
    }
    public function setBookLocationLabelAttribute($value)
    {
        $this->attributes['book_location_label'] = ucwords($value);
    }
}
