<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConferenceScheduleRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_schedule_id',
        'uuid',
        'overall_rating',
        'participant_ratings',
        'comments',
    ];
}
