<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SessionInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_session_id',
        'user_id',
        'interest_level',
        'experience_level',
        'panel_role',
        'notes',
        'will_moderate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conference_session()
    {
        return $this->belongsTo(ConferenceSession::class);
    }

    public static function getUserPresentationInterests($user_id, $conference_id)
    {
        return self::where('user_id', $user_id)
                    ->whereHas('conference_session', function($query) use($conference_id) {
                        $query->where('conference_id', '=', $conference_id);
                    })
                    ->whereHas('conference_session', function($query) {
                        $query->where('type_id', '=', config('site.session_type_id.presentation'));
                    })
                    ->with('user','conference_session')
                    ->get();
    }


}
