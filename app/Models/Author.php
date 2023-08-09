<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setAuthorNameAttribute($value)
    {
        $this->attributes['author_name'] = ucwords($value);
    }
}
