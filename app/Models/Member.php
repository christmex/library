<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setMemberNameAttribute($value)
    {
        $this->attributes['member_name'] = ucwords($value);
    }

    public function getDepartmentNameAttribute()
    {
        return $this->department->department_name;
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
}
