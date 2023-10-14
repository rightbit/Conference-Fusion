<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        'proposed_date',
        'special_equipment',
        'ignore_errors',
    ];

    public function conference() {
        return $this->hasOne( Conference::class, 'id', 'conference_id');
    }

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

    public function session_participants() {
        return $this->session_interest()->where('is_participant', '=', 1)
            ->with('user', 'user_info')
            ->orderByDesc('is_moderator');
    }

    public function user_session_interest() {
        return $this->hasOne( SessionInterest::class);
    }

    public function conference_schedule() {
        return $this->hasMany( ConferenceSchedule::class, 'conference_session_id', 'id');
    }

    public function session_user_comments() {
        return $this->hasMany( SessionUserComment::class, 'conference_session_id', 'id');
    }

    public static function getUserPanelInterests($user_id, $conference_id, $request)
    {
        $panels = self::where('conference_id', '=', $conference_id)
                ->whereIn('session_status_id', [2,4,5]) // Ready for call and Scheduled
                ->where('type_id', '=', 1) // Panel
                ->with(['user_session_interest' => function ($query) use($user_id) {
                    $query->where('user_id', $user_id);
                }]);

        if($request->call_included) {
            $panels->whereHas('track', function($query) {
                $query->where('show_on_call', 1);
            });
        }

        if($request->filter == 'own') {
            $panels->whereHas('session_interest', function($query) use($user_id) {
                $query->where('user_id', '=', $user_id);
            })
            ->with('session_interest');
        }

        if($request->single_session && is_numeric($request->single_session)) {
            $panels->where('id', '=', $request->single_session);
        }

        if(is_numeric($request->filter)) {
            $panels->where('track_id', '=', $request->filter);
        }

        return $panels->orderByRaw('-proposed_date DESC')->paginate(25);
    }

}
