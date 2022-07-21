<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PanelInterestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $request_user_id = $this->request->get('user_id');
        if(!empty($request_user_id)) {
            return (Auth::user()->id == $request_user_id);
        }

        return Auth::user();

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'sometimes|required|integer',
            'user_id' => 'sometimes|required|integer',
            'conference_session_id' => 'required|integer',
            'interest_level' => 'required|integer',
            'experience_level' => 'required|integer',
            'panel_role' => 'required|integer',
            'notes' => 'required',
            'will_moderate' => 'required|integer',
        ];
    }
}
