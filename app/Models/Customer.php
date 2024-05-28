<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'uuid'];
    protected $hidden = ['id'];
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'address'
    ];
}
