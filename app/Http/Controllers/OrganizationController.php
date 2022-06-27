<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrganizationModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organization = OrganizationModel::first();

        return $organization;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $request->validate(['license-key' => 'required|alpha_num|size:10']);

        $organization = OrganizationModel::first();
        $appID = $organization->app_id;

        $response = Http::asForm()
        ->patch('https://pcm.patchcablemgr.com/api/license', [
            'license-key' => $request['license-key'],
            'app-id' => $appID,
        ]);

        if($response->status() !== 200) {
            throw ValidationException::withMessages(['license-key' => $response->body()]);
        } else {
            return $response;
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
