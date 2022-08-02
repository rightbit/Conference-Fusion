<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInfoDataCategoryRequest;
use App\Http\Resources\UserInfoDataCategoryResource;
use App\Models\UserInfoDataCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class UserInfoDataCategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->cat)
        {
            return UserInfoDataCategoryResource::collection(UserInfoDataCategory::where('type', $request->cat)->get());
        }

        return UserInfoDataCategoryResource::collection(UserInfoDataCategory::all());
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
     * @param  \App\Http\Requests\UserInfoDataCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserInfoDataCategoryRequest $request)
    {

        $uidc = new UserInfoDataCategory($request->all());
        $uidc->save();
        return $uidc;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UserInfoDataCategory  $user_info_datum
     * @return \Illuminate\Http\Response
     */
    public function show(UserInfoDataCategory $user_info_datum)
    {
        return new UserInfoDataCategoryResource($user_info_datum);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserInfoDataCategory  $user_info_datum
     * @return \Illuminate\Http\Response
     */
    public function edit(UserInfoDataCategory $user_info_datum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserInfoDataCategoryRequest  $request
     * @param  \App\Models\UserInfoDataCategory  $user_info_datum
     * @return \Illuminate\Http\Response
     */
    public function update(UserInfoDataCategoryRequest $request, UserInfoDataCategory $user_info_datum)
    {
        if($request->id == $user_info_datum->id) {
            $user_info_datum->name = $request->name;
            $user_info_datum->label = $request->label;
            $user_info_datum->options = $request->options;
            $user_info_datum->required = (bool) $request->required;
            $user_info_datum->save();
            return $user_info_datum;
        }
        abort(400, "Error in the request body");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserInfoDataCategory  $user_info_datum
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserInfoDataCategory $user_info_datum)
    {
        // Check Permissions
        if (! Gate::allows('admin', Auth::user())) {
            abort(403, 'Not authorized');
        }
        $user_info_datum->delete();
    }
}
