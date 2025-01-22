<?php

namespace App\Models;

use App\Http\Requests\ConferenceAnnouncementRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class ConferenceAnnouncement extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
        'message',
        'pinned',
        'display_date',
        'conference_id',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = Auth::user()->id;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user()->id;
        });
    }

    public function delete()
    {
        $this->deleted_by = Auth::user()->id;
        $this->save();

        return parent::delete();
    }

}
