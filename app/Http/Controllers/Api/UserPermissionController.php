<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPermissionRequest;
use App\Http\Resources\UserPermissionResource;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class UserPermissionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user_id) {
            return UserPermissionResource::collection(
                UserPermission::where('user_id', $request->user_id)
                ->get()
            );
        }

        return UserPermissionResource::collection(UserPermission::orderby('user_id')->paginate(25));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserPermissionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserPermissionRequest $request)
    {
        $user_perm = new UserPermission($request->all());
        $user_perm->save();
        return new UserPermissionResource($user_perm);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserPermission $permission
     * @return \Illuminate\Http\Response
     */
    public function show(UserPermission $permission)
    {
        return new UserPermissionResource($permission);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserPermission  $userPermission
     * @return \Illuminate\Http\Response
     */
    public function edit(UserPermission $userPermission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserPermission  $userPermission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserPermission $userPermission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserPermission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserPermission $permission)
    {
        if($permission->permission === 'admin') {
            $admin_users = UserPermission::where('user_id', '!=', $permission->user_id)
                ->where('permission', 'admin')
                ->get();
            if(!$admin_users->count()) {
                Log::warning("Tried to delete the last admin user");
                abort(409, 'Tried to delete the last admin user');
            }
        }

        $deleted = $permission->delete();
        if($deleted) {
            return response('success', 200);
        }

        abort(500, 'An error occurred deleting this user permission');

    }
}
