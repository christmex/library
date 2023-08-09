<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookType extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function setBookTypeNameAttribute($value)
    {
        $this->attributes['book_type_name'] = ucwords($value);
    }
}
