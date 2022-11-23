<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateUserController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreateUserRequest $request)
    {
        $user =  User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make(time().rand(1000,9999)),
        ]);

        UserInfo::create([
            'user_id' => $user->id,
            'badge_name' => $request->first_name . ' ' . $request->last_name,
            'contact_email' => $request->email,
            'share_email_permission' => 1,
            'agree_to_terms' => 1,
        ]);

        return redirect()->route('user_profile', ['id' => $user->id]);

    }

}
