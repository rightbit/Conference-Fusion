<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiteConfigRequest;
use App\Http\Resources\SiteConfigResource;
use App\Models\SiteConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteConfigController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SiteConfigResource::collection(SiteConfig::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SiteConfig  $configuration
     * @return \Illuminate\Http\Response
     */
    public function show(SiteConfig $configuration)
    {
        return new SiteConfigResource($configuration);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SiteConfig  $siteConfig
     * @return \Illuminate\Http\Response
     */
    public function edit(SiteConfig $siteConfig)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\SiteConfigRequest $request
     * @param  \App\Models\SiteConfig  $configuration
     * @return \Illuminate\Http\Response
     */
    public function update(SiteConfigRequest $request, SiteConfig $configuration)
    {

        if($request->id == $configuration->id) {
            $configuration->value = $request->value;
            $configuration->last_updated_user_id = Auth::id();
            $configuration->saveOrFail();
            return $configuration;
        } else {
            abort(422, 'Could not validate the request');

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SiteConfig  $siteConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy(SiteConfig $siteConfig)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Response
     */
    public function getConfigMessage(string  $key)
    {
        if (preg_match("/^message_/", $key)) {
            $configuration = SiteConfig::where('key', '=', $key)->first();
            return new SiteConfigResource($configuration);
        }

        abort(404);
    }
}
