<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Gate::allows('view_admin', Auth::user())){
            abort(403, 'Not authorized');
        }

        if($request->keyword){
            return UserResource::collection(
              User::where('first_name', 'LIKE',"%$request->keyword%")
                  ->orWhere('last_name', 'LIKE',"%$request->keyword%")
                  ->orWhere('email', 'LIKE', "%$request->keyword%")
                  ->orWhereHas('info', function($query) use($request) {
                      $query->where('badge_name', 'LIKE',"%$request->keyword%");
                  })
                  ->orderby('last_name')->paginate(25)
            );
        }

        return UserResource::collection(User::orderby('last_name')->paginate(25));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        abort(403, "Not Authorized");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User     $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if(!Gate::allows('view_admin', Auth::user()) && !Gate::allows('edit_users', $user->id)){
            abort(403, 'Not authorized');
        }
        $userinfo =  new UserResource($user);
        return $userinfo;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\User $user
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(User $user, UserRequest $request)
    {
        if($request->id == $user->id) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->save();

            $user->info->badge_name = $request->info['badge_name'];
            $user->info->contact_email = $request->info['contact_email'];
            $user->info->biography = $request->info['biography'];
            $user->info->notes = $request->info['notes'];
            $user->info->staff_notes = $request->info['staff_notes'];
            $user->info->website = $request->info['website'];
            $user->info->share_email_permission = $request->info['share_email_permission'] ?? 0;
            $user->info->agree_to_terms = $request->info['agree_to_terms'] ?? 0;
            $user->info->personal_data = !empty($request->info['personal_data']) ? json_encode($request->info['personal_data']) : NULL;
            $user->info->social_data = !empty($request->info['social_data']) ? json_encode($request->info['social_data']) : NULL;
            $user->info->participant_data = !empty($request->info['participant_data']) ? json_encode($request->info['participant_data']) : NULL;
            $user->info->save();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Update the user profile image.
     *
     * @param  \App\Models\User     $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(User $user, Request $request)
    {
        Log::debug('user', [$request]);
        if(!Gate::allows('edit_users', $user->id)){
            abort(403, 'Not authorized');
        }

        $this->validate($request, [
            'password' => 'required|string|min:6',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

    }

    /**
     * Update the user profile image.
     *
     * @param  \App\Models\User     $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadProfileImage(User $user, Request $request)
    {

        if(!Gate::allows('view_admin', Auth::user()) && !Gate::allows('edit_users', $user->id)){
            abort(403, 'Not authorized');
        }

        $this->validate($request, [
            'file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $folder = intval($user->id / 100); //create a new folder every 100 users
        $file = $request->file('file');
        $image_name = $user->id . substr(sha1($user->id), 0, 10);

        $filePath = public_path('/images/profile/' . $folder);
        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $img = Image::make($file->path());
        $img->save($filePath . '/' . $image_name . '.' . $file->extension(), 80);
        $img->fit(150)->save($filePath . '/' . $image_name . '-thumb.' . $file->extension());
        $user->info->profile_image = '/images/profile/' . $folder . '/' . $image_name . '-thumb.' . $file->extension();
        try {
            $user->info->save();
            $user_res = new UserResource($user);
            return $user_res;

        } catch (\Exception $e) {
            abort(500, 'Please complete your profile first.');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User     $user
     * @return \Illuminate\Http\Response
     */
    public function createToken(User $user)
    {
        if(!Gate::allows('view_admin', Auth::user()) && !Gate::allows('edit_users', $user->id)){
            abort(403, 'Not authorized');
        }

        $authToken = $user->tokens()->first();

        //TODO - this is to test an app conection and needs a better management system
        if($authToken) {
            return response()->json(['accessToken' => 'A Token was already created and cannot be created again. Contact an administrator.']);
        }
        $authToken = $user->createToken('auth-token')->plainTextToken;

        return response()->json(['accessToken' => $authToken]);

    }

}
