<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_order',
        'color_code',
    ];

    public function track_heads()
    {
        return $this->hasMany(TrackUser::class, 'track_id', 'id');
    }

    public function conference_track_heads() {
        $selected_conference = session('selected_conference', '');
        return $this->track_heads()->where('conference_id', $selected_conference);
    }

    public function conference_sessions() {
        return $this->hasMany(ConferenceSession::class);
    }

}
