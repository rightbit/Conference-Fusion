<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleWarnings extends Model
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

    public static function key_value_pairs()
    {
        return ScheduleWarnings::get()->mapWithKeys(function ($item, $key) {
            return [$item['key'] => $item['value']];
        })->all();
    }
}