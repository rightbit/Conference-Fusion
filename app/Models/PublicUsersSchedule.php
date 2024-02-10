<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicUsersSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'conference_schedule_id',
        'email',
    ];
}
