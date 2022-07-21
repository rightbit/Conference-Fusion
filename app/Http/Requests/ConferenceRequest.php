<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ConferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasPermission('admin');
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
            'short_name' => 'required',
            'description' => 'required',
            'time_zone' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'session_start_time' => 'required',
            'session_end_time' => 'required',
            'call_start_date ' => 'date',
            'call_end_date ' => 'date',
        ];
    }
}
