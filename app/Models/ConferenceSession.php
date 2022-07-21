<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ConferenceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'participant_notes',
        'staff_notes',
        'seed_questions',
        'type_id',
        'duration_minutes',
        'session_status_id',
        'registration_required',
        'max_registration',
        'attendance',
        'conference_id',
        'track_id',
        'proposed_date'
    ];

    public function track() {
        return $this->hasOne(Track::class, 'id', 'track_id')->withDefault([
            'id' => null,
            'name' => '',
        ]);
    }

    public function session_type() {
        return $this->hasOne(SessionType::class, 'id', 'type_id')->withDefault([
            'id' => null,
            'name' => '',
        ]);
    }

    public function status() {
        return $this->hasOne(SessionStatus::class, 'id', 'session_status_id')->withDefault([
            'id' => null,
            'status' => '',
        ]);
    }

    public function session_interest() {
        return $this->hasMany( SessionInterest::class);
    }


    public function user_session_interest() {
        return $this->hasOne( SessionInterest::class);
    }

    public static function getUserPanelInterests($user_id, $conference_id, $request)
    {
        $panels = self::where('conference_id', '=', $conference_id)
                ->whereHas('status', function($query) {
                    $query->where('status', '=', 'Include in Call');
                })
                ->whereHas('session_type', function($query) {
                    $query->where('name', '=', 'Panel');
                })
                ->with(['user_session_interest' => function ($query) use($user_id) {
                    $query->where('user_id', $user_id);
                }]);

        if($request->filter == 'own') {
            $panels->whereHas('session_interest', function($query) use($user_id) {
                $query->where('user_id', '=', $user_id);
            })
            ->with('session_interest');
        }

        if(is_numeric($request->filter)) {
            $panels->where('track_id', '=', $request->filter);
        }

        return $panels->orderBy('proposed_date')->paginate(25);
    }

}
