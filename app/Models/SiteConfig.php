<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'last_updated_user_id',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'last_updated_user_id')->withDefault([
            'id' => '0',
            'name' => 'Unknown',
        ]);
    }
}
