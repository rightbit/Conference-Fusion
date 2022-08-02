<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfoDataCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'label',
        'options',
        'required'
    ];
}
