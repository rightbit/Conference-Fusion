<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ConferenceSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasPermission('edit_sessions');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'conference_id' => 'required',
            'duration_minutes' => 'integer|nullable',
            'session_status_id' => 'integer|nullable',
            'registration_required' => 'boolean|nullable',
            'max_registration' => 'integer|nullable',
            'attendence' => 'integer|nullable',
            'track_id' => 'integer|nullable',
            'type_id' => 'integer|nullable',
        ];
    }
}
