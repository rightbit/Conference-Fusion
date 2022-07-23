<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfoConsignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'book1_title',
        'book1_author',
        'book1_isbn',
        'book2_title',
        'book2_author',
        'book2_isbn',
        'book3_title',
        'book3_author',
        'book3_isbn',
    ];
}
