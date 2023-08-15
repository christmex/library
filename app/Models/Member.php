<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    use HasFactory, Multitenantable;

    protected $guarded = [];

    public function setMemberNameAttribute($value)
    {
        $this->attributes['member_name'] = ucwords($value);
    }

    public function getDepartmentNameAttribute()
    {
        return !empty($this->department->department_name) ? "- ".$this->department->department_name : "";
        // return $this->department->department_name;
    }

    public function department(){
        return $this->belongsTo(Department::class,'department_id','id');
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function activeTransaction(){
        return $this->transactions()->where('transaction_returned_at',NULL);
    }

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    } 
}
