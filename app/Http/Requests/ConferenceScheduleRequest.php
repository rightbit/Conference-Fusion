<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ConferenceScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasPermission('view_admin');

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'conference_id' => 'required|integer',
            'date' => 'required',
            'time' => 'nullable',
            'room_id' => 'nullable|integer',
            'track_id' => 'nullable|integer',
            'conference_session_id' => 'nullable|integer',
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'conference_id' => 'conference id required',
            'date' => 'date required',
            'time' => 'time error',
            'room_id' => 'room id error',
            'track_id' => 'track id error',
            'conference_session_id' => 'conference session error',
        ];
    }
}
