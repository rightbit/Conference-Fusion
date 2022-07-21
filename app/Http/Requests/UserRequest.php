<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->route('user');
        return Gate::allows('edit_users', $user->id);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'info.agree_to_terms' => 'required|accepted',
            'info.badge_name' => 'required',
            'info.contact_email' => 'nullable|email',
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
            'first_name' => 'First name is required',
            'last_name' => 'Last name is required',
            'info.agree_to_terms' => 'You must agree to the site terms and code of conduct',
            'info.badge_name' => 'Badge Name is required',
        ];
    }
}
