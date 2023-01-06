<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionUserComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'conference_session_id',
        'comment',
    ];

    public function session()
    {
        return $this->hasOne(ConferenceSession::class);
    }

    public function user_info()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'user_id');
    }


}
