<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function member(){
        return $this->belongsTo(Member::class,'member_id','id');
    }
    // public function penalty(){
    //     return $this->hasOne(Penalty::class,);
    // }
    public function bookStock(){
        return $this->belongsTo(BookStock::class,'book_stock_id','id');
    }

    // public function book(){
    //     return $this->hasOneThrough
    // }
}
