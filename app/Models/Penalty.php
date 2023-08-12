<?php

namespace App\Models;

use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penalty extends Model
{
    use HasFactory, Multitenantable;

    protected $guarded = [];

    public function getBookNameAttribute()
    {
        return $this->transaction->bookStock->book->book_name;
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }
}
