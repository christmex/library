<?php

namespace App\Models;

use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, Multitenantable;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            if($obj->id && auth()->user()->email == 'super@sekolahbasic.sch.id'){
                Penalty::where('transaction_id', $obj->id)->where('penalty_status', '!=','unpaid')->delete();
            }
        });
    }

    public function member(){
        return $this->belongsTo(Member::class,'member_id','id');
    }
    // public function penalty(){
    //     return $this->hasOne(Penalty::class,);
    // }
    public function bookStock(){
        return $this->belongsTo(BookStock::class,'book_stock_id','id');
    }

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    // public function book(){
    //     return $this->hasOneThrough
    // }
}
