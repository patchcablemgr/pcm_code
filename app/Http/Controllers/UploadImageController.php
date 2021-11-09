<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\LocationsModel;
use App\Http\Controllers\PCM;
use Illuminate\Support\Facades\Log;

class UploadImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        // Prepare variables
        $validatorInput = [
            'id' => $id,
            'file' => $request->file
        ];
        $validatorRules = [
            'id' => [
                'integer',
                'exists:location,id'
            ],
            'file' => [
                'mimes:jpg,png,gif',
                'max:512'
            ]
        ];
        $validatorMessages = [];
        Validator::make($validatorInput, $validatorRules, $validatorMessages)->validate();

        // Retrieve location record
        $location = LocationsModel::where('id', $id)->first();

        // Store floorplan image
        $path = $request->file('file')->store('images');

        // Update location floorplan image
        $pathArray = explode('/', $path);
        $location->img = end($pathArray);
            
        // Save location record
        $location->save();

        // Return location record
        return $location->toArray();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
