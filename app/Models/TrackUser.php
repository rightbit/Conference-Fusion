<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackUser extends Model
{
    use HasFactory;

    protected $table = 'track_user';

    protected $fillable = [
        'track_id',
        'user_id',
        'conference_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
