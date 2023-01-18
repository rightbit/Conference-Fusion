<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Driver\Session;

class SessionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'conference_session_id',
        'user_id',
        'action_short_code',
        'action',
    ];


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function conference_session()
    {
        return $this->hasOne(ConferenceSession::class, 'id', 'conference_session_id');
    }

    public static function save_history($user_id, $conference_session_id, $action_short_code, $action)
    {
        return SessionHistory::create([
            'conference_id' => session('selected_conference'),
            'conference_session_id' =>  $conference_session_id,
            'user_id' =>  $user_id,
            'action_short_code' =>  $action_short_code,
            'action' =>  $action,
        ]);


    }


}
