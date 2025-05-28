<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'gender',
        'class_id',
        'birth_date',
        'address',
        'phone_number',
        'email',
        'status'
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
