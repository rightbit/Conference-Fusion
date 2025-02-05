<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConferenceSponsorRequest;
use App\Http\Resources\ConferenceSponsorResource;
use App\Models\ConferenceSponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Intervention\Image\Facades\Image;

class SponsorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sponsors = ConferenceSponsor::orderBy('display_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->where('conference_id', $request->conference_id);
        return ConferenceSponsorResource::collection($sponsors->paginate(25));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param \App\Http\Requests\ConferenceSponsorRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConferenceSponsorRequest $request)
    {
        $sponsor = new ConferenceSponsor($request->all());
        $sponsor->save();
        return new ConferenceSponsorResource($sponsor);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @param \App\Http\Requests\ConferenceSponsorRequest $request
     * @param \App\Models\ConferenceSponsor $sponsor
     * @return \Illuminate\Http\Response
     */
    public function update(ConferenceSponsorRequest $request, ConferenceSponsor $sponsor)
    {
        $sponsor->update($request->all());
        return new ConferenceSponsorResource($sponsor);
    }

    /**
     * Remove the specified resource from storage.
     * @param \App\Models\ConferenceSponsor $sponsor
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConferenceSponsor $sponsor)
    {
        if(!Auth::user()->hasPermission('edit_conference')) {
            abort(403, 'Permission denied');
        }

        if($sponsor->delete()) {
            return response()->json(['message' => 'Sponsor deleted successfully']);
        }

        abort(500, 'An error occurred deleting this room');
    }

    /**
     * Update the sponsor image.
     *
     * @param  \App\Models\ConferenceSponsor     $sponsor
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(ConferenceSponsor $sponsor, Request $request)
    {

        if (!Gate::allows('view_admin', Auth::user())){
            abort(403, 'Not authorized');
        }

        $this->validate($request, [
            'file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $folder = intval($sponsor->id / 100); //create a new folder every 100 sponsors
        $file = $request->file('file');
        $image_name = $sponsor->id . substr(sha1($sponsor->id), 0, 10);

        $filePath = public_path('/images/sponsors/' . $folder);
        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $img = Image::make($file->path());
        $img->save($filePath . '/' . $image_name . '.' . $file->extension(), 80);
        $img->resize(300,300, function ($constraint) {
            $constraint->aspectRatio();
        })->save($filePath . '/' . $image_name . '-thumb.' . $file->extension());
        $sponsor->sponsor_image = '/images/sponsors/' . $folder . '/' . $image_name . '-thumb.' . $file->extension();
        try {
            $sponsor->save();
            $sponsor_res = new ConferenceSponsorResource($sponsor);
            return $sponsor_res;

        } catch (\Exception $e) {
            abort(500, 'Something went wrong.');
        }

    }


}
