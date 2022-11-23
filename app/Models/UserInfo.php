<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'badge_name',
        'contact_email',
        'notes',
        'staff_notes',
        'share_email_permission',
        'agree_to_terms',
    ];

    public function user()
    {
       return $this->belongsTo(User::class);
    }
}
