<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'description',
        'location',
        'url',
        'time_zone',
        'start_date',
        'end_date',
        'session_start_time',
        'session_end_time',
        'call_start_date',
        'call_end_date',
    ];
}
