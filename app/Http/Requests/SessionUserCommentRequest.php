<?php

namespace App\Http\Requests;

use App\Models\SessionInterest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SessionUserCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Gate::allows('view_admin', Auth::user())) {
            return true;
        }

        $in_session = SessionInterest::where('user_id', Auth::user()->id)
                        ->where('conference_session_id', $this->request->get('conference_session_id'))
                        ->where('is_participant', 1)
                        ->first();

        if ($in_session) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'conference_session_id' => 'required|integer',
            'comment' => 'required',
        ];
    }
}
