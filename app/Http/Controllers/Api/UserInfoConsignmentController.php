<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInfoConsignmentRequest;
use App\Http\Resources\UserInfoConsignmentResource;
use App\Models\UserInfoConsignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserInfoConsignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $info = UserInfoConsignment::where('user_id', Auth::user()->id)
            ->where('conference_id', session('selected_conference'))->first();
        if (empty($info)) {
           $info = new UserInfoConsignment();
           $info->user_id = Auth::user()->id;
           $info->conference_id = session('selected_conference');
           $info->participating = 0;
        }
        return new UserInfoConsignmentResource($info);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $info = UserInfoConsignment::create([
            'user_id' => Auth::user()->id,
            'conference_id' => session('selected_conference'),
            'participating' => $request->participating,
            'book1_title' => $request->book1_title,
            'book1_author' => $request->book1_author,
            'book1_isbn' => $request->book1_isbn,
            'book2_title' => $request->book2_title,
            'book2_author' => $request->book2_author,
            'book2_isbn' => $request->book2_isbn,
            'book3_title' => $request->book3_title,
            'book3_author' => $request->book3_author,
            'book3_isbn' => $request->book3_isbn,
        ]);

        return new UserInfoConsignmentResource($info);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * @param  \App\Models\UserInfoConsignment $user_info_consignment
     * @param  \App\Http\Requests\UserInfoConsignmentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserInfoConsignment $user_info_consignment, UserInfoConsignmentRequest $request)
    {
        if($request->id == $user_info_consignment->id) {
            $user_info_consignment->participating = $request->participating;
            $user_info_consignment->book1_title = $request->book1_title;
            $user_info_consignment->book1_author = $request->book1_author;
            $user_info_consignment->book1_isbn = $request->book1_isbn;
            $user_info_consignment->book2_title = $request->book2_title;
            $user_info_consignment->book2_author = $request->book2_author;
            $user_info_consignment->book2_isbn = $request->book2_isbn;
            $user_info_consignment->book3_title = $request->book3_title;
            $user_info_consignment->book3_author = $request->book3_author;
            $user_info_consignment->book3_isbn = $request->book3_isbn;
            $user_info_consignment->save();
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
}
